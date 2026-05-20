<?php

declare(strict_types=1);

final class HealthController
{
    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $ok = true;
        $checks = ['app' => true, 'database' => false];
        try {
            Database::pdo()->query('SELECT 1');
            $checks['database'] = true;
        } catch (Throwable) {
            $ok = false;
        }
        http_response_code($ok ? 200 : 503);
        echo json_encode(['ok' => $ok, 'checks' => $checks], JSON_THROW_ON_ERROR);
    }
}
