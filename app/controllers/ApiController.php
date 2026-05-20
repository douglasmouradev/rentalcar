<?php

declare(strict_types=1);

final class ApiController
{
    public function customersSearch(): void
    {
        AuthMiddleware::handle();
        PartnerForbiddenMiddleware::handleJson();
        ApiRateLimiter::guardJson();
        header('Content-Type: application/json; charset=utf-8');
        $q = (string) ($_GET['q'] ?? '');
        $rows = Customer::searchAutocomplete($q);
        echo json_encode(['ok' => true, 'data' => $rows], JSON_THROW_ON_ERROR);
    }

    public function reservationConflict(): void
    {
        AuthMiddleware::handle();
        PartnerForbiddenMiddleware::handleJson();
        ApiRateLimiter::guardJson();
        header('Content-Type: application/json; charset=utf-8');
        $carId = (int) ($_GET['car_id'] ?? 0);
        $pickupDate = (string) ($_GET['pickup_date'] ?? '');
        $pickupTime = (string) ($_GET['pickup_time'] ?? '09:00:00');
        $returnDate = (string) ($_GET['return_date'] ?? '');
        $returnTime = (string) ($_GET['return_time'] ?? '18:00:00');
        if (strlen($pickupTime) === 5) {
            $pickupTime .= ':00';
        }
        if (strlen($returnTime) === 5) {
            $returnTime .= ':00';
        }
        $exclude = isset($_GET['exclude_id']) ? (int) $_GET['exclude_id'] : null;
        if ($carId <= 0 || $pickupDate === '' || $returnDate === '') {
            echo json_encode(['ok' => false, 'conflict' => false, 'error' => 'invalid'], JSON_THROW_ON_ERROR);
            return;
        }
        $conflict = Reservation::hasConflict($carId, $pickupDate, $pickupTime, $returnDate, $returnTime, $exclude);
        echo json_encode(['ok' => true, 'conflict' => $conflict], JSON_THROW_ON_ERROR);
    }

    public function calendarEvents(): void
    {
        AuthMiddleware::handle();
        PartnerForbiddenMiddleware::handleJson();
        ApiRateLimiter::guardJson();
        header('Content-Type: application/json; charset=utf-8');
        $start = (string) ($_GET['start'] ?? date('Y-m-01'));
        $end = (string) ($_GET['end'] ?? date('Y-m-t'));
        $carId = isset($_GET['car_id']) ? (int) $_GET['car_id'] : 0;
        $operatorId = isset($_GET['operator_id']) ? (int) $_GET['operator_id'] : 0;
        $status = (string) ($_GET['status'] ?? '');
        if (!Auth::isOwner() && $operatorId > 0 && $operatorId !== Auth::id()) {
            $operatorId = 0;
        }
        $events = Reservation::eventsBetween(
            $start,
            $end,
            $carId > 0 ? $carId : null,
            $operatorId > 0 ? $operatorId : null,
            $status !== '' ? $status : null
        );
        echo json_encode(['ok' => true, 'data' => $events], JSON_THROW_ON_ERROR);
    }

    public function customersQuickCreate(): void
    {
        AuthMiddleware::handle();
        PartnerForbiddenMiddleware::handleJson();
        ApiRateLimiter::guardJson();
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false]);
            return;
        }
        $raw = file_get_contents('php://input') ?: '';
        $json = json_decode($raw, true);
        if (!is_array($json)) {
            $json = $_POST;
        }
        if (!Csrf::validate($json['_csrf'] ?? null)) {
            http_response_code(403);
            echo json_encode(['ok' => false, 'error' => 'csrf']);
            return;
        }
        $d = [
            'type' => (string) ($json['type'] ?? 'individual'),
            'full_name' => trim((string) ($json['full_name'] ?? '')),
            'document' => preg_replace('/\D/', '', (string) ($json['document'] ?? '')),
            'email' => trim((string) ($json['email'] ?? '')),
            'phone' => trim((string) ($json['phone'] ?? '')),
            'address' => null,
            'city' => null,
            'state' => null,
            'zip_code' => null,
            'notes' => null,
            'created_by' => Auth::id(),
        ];
        if ($d['full_name'] === '' || $d['document'] === '' || $d['phone'] === '') {
            echo json_encode(['ok' => false, 'error' => 'validation']);
            return;
        }
        try {
            $id = Customer::create($d);
            $row = Customer::find($id);
            echo json_encode(['ok' => true, 'customer' => $row], JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'db']);
        }
    }
}
