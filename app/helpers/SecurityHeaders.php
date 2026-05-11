<?php

declare(strict_types=1);

final class SecurityHeaders
{
    public static function send(): void
    {
        if (headers_sent()) {
            return;
        }

        header_remove('X-Powered-By');

        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');

        $app = file_exists(BASE_PATH . '/config/app.php')
            ? require BASE_PATH . '/config/app.php'
            : [];
        $isProd = ($app['env'] ?? 'production') === 'production' && !($app['debug'] ?? false);
        if ($isProd) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        $csp = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "object-src 'none'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' https://fonts.googleapis.com 'unsafe-inline'",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https://images.unsplash.com https:",
            "connect-src 'self'",
        ];
        if ($isProd) {
            $csp[] = 'upgrade-insecure-requests';
        }
        header('Content-Security-Policy: ' . implode('; ', $csp));
    }
}
