<?php

declare(strict_types=1);

final class CustomerController
{
    public function index(): void
    {
        PartnerForbiddenMiddleware::handle();
        $q = trim((string) ($_GET['q'] ?? ''));
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $p = Customer::paginated($page, $perPage, $q !== '' ? $q : null);
        $listQuery = $q !== '' ? ['q' => $q] : [];
        View::render('customers/index', [
            'title' => Lang::get('nav.customers'),
            'customers' => $p['rows'],
            'search' => $q,
            'pagination' => $p,
            'paginationBase' => Router::url('/customers'),
            'listQuery' => $listQuery,
        ], 'main');
    }

    public function createForm(): void
    {
        PartnerForbiddenMiddleware::handle();
        View::render('customers/create', ['title' => Lang::get('customer.create'), 'customer' => null], 'main');
    }

    public function create(): void
    {
        PartnerForbiddenMiddleware::handle();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/customers/create'));
            exit;
        }
        $d = $this->sanitize($_POST);
        $d['created_by'] = Auth::id();
        $attachment = $this->handleAttachmentUpload($_FILES['attachment'] ?? null, null);
        if ($attachment === false) {
            header('Location: ' . Router::url('/customers/create'));
            exit;
        }
        $d['attachment_path'] = $attachment;
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
        PartnerForbiddenMiddleware::handle();
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
        PartnerForbiddenMiddleware::handle();
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
        $attachment = $this->handleAttachmentUpload($_FILES['attachment'] ?? null, $old);
        if ($attachment === false) {
            header('Location: ' . Router::url('/customers/' . $id . '/edit'));
            exit;
        }
        $d['attachment_path'] = $attachment;
        Customer::update((int) $id, $d);
        Audit::log(Auth::id(), 'update', 'customer', (int) $id, $old, $d);
        Flash::success(Lang::get('flash.saved'));
        header('Location: ' . Router::url('/customers'));
        exit;
    }

    public function attachment(string $id): void
    {
        PartnerForbiddenMiddleware::handle();
        $c = Customer::find((int) $id);
        if (!$c || empty($c['attachment_path'])) {
            http_response_code(404);
            return;
        }
        $path = CustomerAttachment::filesystemPath((string) $c['attachment_path']);
        if ($path === null) {
            http_response_code(404);
            return;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path) ?: 'application/octet-stream';
        $name = basename($path);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . rawurlencode($name) . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        readfile($path);
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

    /**
     * @param array<string,mixed>|null $file
     * @param array<string,mixed>|null $existing
     * @return string|null false em falha de validação/upload
     */
    private function handleAttachmentUpload(?array $file, ?array $existing): string|null|false
    {
        if (empty($file) || empty($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            return $existing['attachment_path'] ?? null;
        }

        $app = Config::app();
        $max = (int) ($app['max_upload'] ?? 5242880);
        if (($file['size'] ?? 0) > $max) {
            Flash::error(Lang::get('upload.too_large'));
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);
        $allowed = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];
        if (!isset($allowed[$mime])) {
            Flash::error(Lang::get('upload.invalid_type'));
            return false;
        }

        $dir = BASE_PATH . '/storage/customers';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = 'cust_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file((string) $file['tmp_name'], $dest)) {
            Flash::error(Lang::get('upload.failed'));
            return false;
        }

        if (!empty($existing['attachment_path'])) {
            CustomerAttachment::deleteFile((string) $existing['attachment_path']);
        }

        return CustomerAttachment::storeRelative($name);
    }
}
