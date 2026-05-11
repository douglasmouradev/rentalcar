<?php

declare(strict_types=1);

final class AppError
{
    public static function log(Throwable $e): void
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $line = date('c') . ' ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n";
        @file_put_contents($dir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function render(Throwable $e): void
    {
        $app = require BASE_PATH . '/config/app.php';
        $debug = (bool) ($app['debug'] ?? false);
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
        if ($debug) {
            echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Erro</title></head><body><pre>';
            echo htmlspecialchars($e->getMessage() . "\n\n" . $e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
            echo '</pre></body></html>';
            return;
        }
        echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Indisponível</title>';
        echo '<style>body{font-family:system-ui,sans-serif;background:#f1f5f9;color:#0f172a;display:grid;min-height:100vh;place-items:center;margin:0;padding:1rem;text-align:center;}div{max-width:420px;background:#fff;padding:2rem;border-radius:12px;border:1px solid #e2e8f0;}</style></head><body><div>';
        echo '<h1 style="margin:0 0 .5rem;font-size:1.25rem;">Serviço temporariamente indisponível</h1>';
        echo '<p style="margin:0;color:#64748b;font-size:.95rem;">Tente novamente em instantes. Se o problema persistir, contacte o suporte.</p>';
        echo '</div></body></html>';
    }
}
