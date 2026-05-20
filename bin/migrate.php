#!/usr/bin/env php
<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

spl_autoload_register(static function (string $class): void {
    foreach (['helpers', 'middleware', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

require BASE_PATH . '/app/helpers/Env.php';
Env::load(BASE_PATH . '/.env');

$pending = Migrate::pending();
if ($pending === []) {
    echo "Nenhuma migration pendente.\n";
    exit(0);
}

echo "Aplicando " . count($pending) . " migration(s)...\n";
foreach (Migrate::run() as $version) {
    echo "  ✓ {$version}\n";
}
echo "Concluído.\n";
