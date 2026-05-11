<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require BASE_PATH . '/app/helpers/Env.php';
Env::load(BASE_PATH . '/.env');

$appCfg = file_exists(BASE_PATH . '/config/app.php')
    ? require BASE_PATH . '/config/app.php'
    : [];

$lifetime = (int) ($appCfg['session_lifetime'] ?? 480) * 60;
$secure = (bool) ($appCfg['session_secure'] ?? false);
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

spl_autoload_register(static function (string $class): void {
    foreach (['helpers', 'middleware', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

SecurityHeaders::send();

if (isset($_GET['lang']) && in_array($_GET['lang'], ['pt-BR', 'en-US'], true)) {
    Lang::setLocale($_GET['lang']);
    if (Auth::check()) {
        $uid = Auth::id();
        if ($uid !== null) {
            $stmt = Database::pdo()->prepare('UPDATE users SET lang_pref = ? WHERE id = ?');
            $stmt->execute([$_GET['lang'], $uid]);
            Auth::refreshUserFromDb();
        }
    }
    $back = Auth::check() ? Router::url('/dashboard') : Router::url('/');
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $host = parse_url($appCfg['url'] ?? '', PHP_URL_HOST);
    if ($ref !== '' && $host && str_contains($ref, $host)) {
        $back = $ref;
    }
    header('Location: ' . $back);
    exit;
}

$routes = require BASE_PATH . '/config/routes.php';
try {
    Router::dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/', $routes);
} catch (Throwable $e) {
    AppError::log($e);
    AppError::render($e);
}
