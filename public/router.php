<?php

declare(strict_types=1);

/**
 * Router para o servidor embutido do PHP (desenvolvimento).
 *
 *   php -S localhost:8888 -t public public/router.php
 */
$uri = urldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

require __DIR__ . '/index.php';
