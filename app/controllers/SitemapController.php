<?php

declare(strict_types=1);

final class SitemapController
{
    public function index(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Content-Type: application/xml; charset=UTF-8');
        $cfg = Config::app();
        $origin = rtrim($cfg['url'] . ($cfg['base'] ?? ''), '/');
        $urls = ['/', '/privacidade', '/termos'];
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $path) {
            $loc = htmlspecialchars($origin . ($path === '/' ? '' : $path), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            echo "  <url><loc>{$loc}</loc></url>\n";
        }
        echo '</urlset>';
    }
}
