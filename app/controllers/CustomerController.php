<?php

declare(strict_types=1);

final class CustomerController
{
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $p = Customer::paginated($page, $perPage);
        View::render('customers/index', [
            'title' => Lang::get('nav.customers'),
            'customers' => $p['rows'],
            'pagination' => $p,
            'paginationBase' => Router::url('/customers'),
            'listQuery' => [],
        ], 'main');
    }

    public function createForm(): void
    {
        View::render('customers/create', ['title' => Lang::get('customer.create'), 'customer' => null], 'main');
    }

    public function create(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/customers/create'));
            exit;
        }
        $d = $this->sanitize($_POST);
        $d['created_by'] = Auth::id();
        try {
            $id = Customer::create($d);
            Audit::log(Auth::id(), 'create', 'customer', $id, null, $d);
            Flash::success(Lang::get('flash.saved'));
            header('Location: ' . Router::url('/customers'));
            exit;
        } catch (Throwable $e) {
            Flash::error(Lang::get('flash.error'));
            header('Location: ' . Router::url('/customers/create'));
            exit;
        }
    }

    public function editForm(string $id): void
    {
        $c = Customer::find((int) $id);
        if (!$c) {
            http_response_code(404);
            View::render('errors/404', ['title' => Lang::get('error.404_title')], 'main');
            return;
        }
        View::render('customers/edit', ['title' => Lang::get('customer.edit'), 'customer' => $c], 'main');
    }

    public function update(string $id): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/customers/' . $id . '/edit'));
            exit;
        }
        $old = Customer::find((int) $id);
        if (!$old) {
            http_response_code(404);
            return;
        }
        $d = $this->sanitize($_POST);
        Customer::update((int) $id, $d);
        Audit::log(Auth::id(), 'update', 'customer', (int) $id, $old, $d);
        Flash::success(Lang::get('flash.saved'));
        header('Location: ' . Router::url('/customers'));
        exit;
    }

    /** @param array<string, mixed> $post */
    private function sanitize(array $post): array
    {
        return [
            'type' => (string) ($post['type'] ?? 'individual'),
            'full_name' => trim((string) ($post['full_name'] ?? '')),
            'document' => preg_replace('/\D/', '', (string) ($post['document'] ?? '')),
            'email' => trim((string) ($post['email'] ?? '')),
            'phone' => trim((string) ($post['phone'] ?? '')),
            'address' => trim((string) ($post['address'] ?? '')) ?: null,
            'city' => trim((string) ($post['city'] ?? '')) ?: null,
            'state' => trim((string) ($post['state'] ?? '')) ?: null,
            'zip_code' => trim((string) ($post['zip_code'] ?? '')) ?: null,
            'notes' => trim((string) ($post['notes'] ?? '')) ?: null,
        ];
    }
}
