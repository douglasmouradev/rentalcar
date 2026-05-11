<?php

declare(strict_types=1);

final class ReservationController
{
    public function index(): void
    {
        PartnerForbiddenMiddleware::handle();
        $op = Auth::isOwner() ? null : Auth::id();
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $p = Reservation::forOperatorPaginated($op, $page, $perPage);
        View::render('reservations/index', [
            'title' => Lang::get('nav.reservations'),
            'reservations' => $p['rows'],
            'pagination' => $p,
            'paginationBase' => Router::url('/reservations'),
            'listQuery' => [],
        ], 'main');
    }

    public function calendar(): void
    {
        PartnerForbiddenMiddleware::handle();
        $cars = Car::search([]);
        $operators = Database::pdo()->query("SELECT id, name FROM users WHERE role = 'operator' ORDER BY name")->fetchAll();
        View::render('reservations/calendar', [
            'title' => Lang::get('nav.calendar'),
            'cars' => $cars,
            'operators' => $operators,
        ], 'main');
    }

    public function createForm(): void
    {
        PartnerForbiddenMiddleware::handle();
        $cars = Auth::isOwner()
            ? Car::search([])
            : Car::search(['status' => 'available']);
        View::render('reservations/create', [
            'title' => Lang::get('reservation.create'),
            'cars' => $cars,
            'locations' => Location::allActive(),
            'customers' => Customer::all(),
            'reservation' => null,
        ], 'main');
    }

    public function create(): void
    {
        PartnerForbiddenMiddleware::handle();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/reservations/create'));
            exit;
        }
        $d = $this->normalize($_POST);
        if (Reservation::hasConflict(
            $d['car_id'],
            $d['pickup_date'],
            $d['pickup_time'],
            $d['return_date'],
            $d['return_time'],
            null
        )) {
            Flash::error(Lang::get('reservation.conflict'));
            header('Location: ' . Router::url('/reservations/create'));
            exit;
        }
        $d['code'] = Reservation::nextCode();
        $d['operator_id'] = Auth::id();
        $id = Reservation::create($d);
        Audit::log(Auth::id(), 'create', 'reservation', $id, null, $d);
        Flash::success(Lang::get('flash.saved'));
        header('Location: ' . Router::url('/reservations/' . $id));
        exit;
    }

    public function show(string $id): void
    {
        PartnerForbiddenMiddleware::handle();
        $r = Reservation::find((int) $id);
        if (!$r || (!$this->canAccessReservation($r))) {
            http_response_code(404);
            View::render('errors/404', ['title' => Lang::get('error.404_title')], 'main');
            return;
        }
        View::render('reservations/show', ['title' => $r['code'], 'r' => $r], 'main');
    }

    public function editForm(string $id): void
    {
        PartnerForbiddenMiddleware::handle();
        $r = Reservation::find((int) $id);
        if (!$r || !$this->canAccessReservation($r)) {
            http_response_code(404);
            View::render('errors/404', ['title' => Lang::get('error.404_title')], 'main');
            return;
        }
        $cars = Auth::isOwner()
            ? Car::search([])
            : Car::search(['status' => 'available']);
        View::render('reservations/edit', [
            'title' => Lang::get('reservation.edit'),
            'r' => $r,
            'cars' => $cars,
            'locations' => Location::allActive(),
            'customers' => Customer::all(),
        ], 'main');
    }

    public function update(string $id): void
    {
        PartnerForbiddenMiddleware::handle();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/reservations/' . $id . '/edit'));
            exit;
        }
        $old = Reservation::find((int) $id);
        if (!$old || !$this->canAccessReservation($old)) {
            http_response_code(404);
            return;
        }
        $d = $this->normalize($_POST, (int) $id);
        if (Reservation::hasConflict(
            $d['car_id'],
            $d['pickup_date'],
            $d['pickup_time'],
            $d['return_date'],
            $d['return_time'],
            (int) $id
        )) {
            Flash::error(Lang::get('reservation.conflict'));
            header('Location: ' . Router::url('/reservations/' . $id . '/edit'));
            exit;
        }
        Reservation::update((int) $id, $d);
        Audit::log(Auth::id(), 'update', 'reservation', (int) $id, $old, $d);
        Flash::success(Lang::get('flash.saved'));
        header('Location: ' . Router::url('/reservations/' . $id));
        exit;
    }

    public function cancel(string $id): void
    {
        PartnerForbiddenMiddleware::handle();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/reservations/' . $id));
            exit;
        }
        $old = Reservation::find((int) $id);
        if (!$old || !$this->canAccessReservation($old)) {
            http_response_code(404);
            return;
        }
        Reservation::setStatus((int) $id, 'cancelled');
        Audit::log(Auth::id(), 'cancel', 'reservation', (int) $id, $old, ['status' => 'cancelled']);
        Flash::success(Lang::get('flash.saved'));
        header('Location: ' . Router::url('/reservations'));
        exit;
    }

    /** @param array<string, mixed> $post */
    private function normalize(array $post, ?int $excludeId = null): array
    {
        $pickupDate = (string) ($post['pickup_date'] ?? '');
        $returnDate = (string) ($post['return_date'] ?? '');
        $pickupTime = (string) ($post['pickup_time'] ?? '09:00');
        $returnTime = (string) ($post['return_time'] ?? '18:00');
        if (strlen($pickupTime) === 5) {
            $pickupTime .= ':00';
        }
        if (strlen($returnTime) === 5) {
            $returnTime .= ':00';
        }
        $d1 = new DateTimeImmutable($pickupDate);
        $d2 = new DateTimeImmutable($returnDate);
        $totalDays = max(1, (int) $d1->diff($d2)->format('%a') + 1);
        $daily = (float) ($post['daily_rate'] ?? 0);
        $discount = Auth::isOwner() ? (float) ($post['discount'] ?? 0) : 0.0;
        $totalAmount = round($daily * $totalDays, 2);
        $final = max(0, round($totalAmount - $discount, 2));

        return [
            'customer_id' => (int) ($post['customer_id'] ?? 0),
            'car_id' => (int) ($post['car_id'] ?? 0),
            'pickup_location_id' => (int) ($post['pickup_location_id'] ?? 0),
            'return_location_id' => (int) ($post['return_location_id'] ?? 0),
            'pickup_date' => $pickupDate,
            'pickup_time' => $pickupTime,
            'return_date' => $returnDate,
            'return_time' => $returnTime,
            'daily_rate' => $daily,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'discount' => $discount,
            'final_amount' => $final,
            'status' => (string) ($post['status'] ?? 'pending'),
            'payment_status' => (string) ($post['payment_status'] ?? 'unpaid'),
            'payment_method' => ($post['payment_method'] ?? '') !== '' ? (string) $post['payment_method'] : null,
            'notes' => trim((string) ($post['notes'] ?? '')) ?: null,
        ];
    }

    private function canAccessReservation(array $r): bool
    {
        if (Auth::isOwner()) {
            return true;
        }
        return (int) $r['operator_id'] === (int) Auth::id();
    }
}
