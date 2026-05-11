<?php

declare(strict_types=1);

final class AuditController
{
    public function index(): void
    {
        $page = Pagination::currentPage();
        $perPage = Pagination::perPage();
        $p = AuditLog::paginated($page, $perPage);
        View::render('audit/index', [
            'title' => Lang::get('nav.audit'),
            'logs' => $p['rows'],
            'pagination' => $p,
            'paginationBase' => Router::url('/audit'),
            'listQuery' => [],
        ], 'main');
    }
}
