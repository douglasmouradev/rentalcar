<?php

declare(strict_types=1);

final class RobotsController
{
    public function index(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Content-Type: text/plain; charset=UTF-8');
        $cfg = Config::app();
        $origin = rtrim($cfg['url'] . ($cfg['base'] ?? ''), '/');
        $sitemap = $origin . '/sitemap.xml';
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /dashboard\n";
        echo "Disallow: /cars\n";
        echo "Disallow: /customers\n";
        echo "Disallow: /reservations\n";
        echo "Disallow: /locations\n";
        echo "Disallow: /users\n";
        echo "Disallow: /reports\n";
        echo "Disallow: /audit\n";
        echo "Disallow: /leads\n";
        echo "Disallow: /api/\n";
        echo "\n";
        echo 'Sitemap: ' . $sitemap . "\n";
    }
}
