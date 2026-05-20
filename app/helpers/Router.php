<?php

declare(strict_types=1);

final class Router
{
    /** @param array<string, array{0: string, 1: string, auth?: bool, role?: string}> $routes */
    public static function dispatch(string $method, string $uri, array $routes): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = Router::basePath();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base)) ?: '/';
        }
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/') ?: '/';
        }

        foreach ($routes as $pattern => $def) {
            $parts = explode(':', $pattern, 2);
            $m = $parts[0] ?? '';
            $p = $parts[1] ?? '';
            if (strtoupper($m) !== strtoupper($method)) {
                continue;
            }
            $regex = self::patternToRegex($p);
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }
            $auth = $def['auth'] ?? false;
            $role = $def['role'] ?? null;
            if ($auth) {
                AuthMiddleware::handle();
            }
            if ($role !== null) {
                RoleMiddleware::handle($role);
            }
            $controller = $def[0];
            $action = $def[1];
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $class = $controller;
            if (!class_exists($class)) {
                http_response_code(500);
                echo 'Controller not found';
                return;
            }
            $c = new $class();
            if (!method_exists($c, $action)) {
                http_response_code(500);
                echo 'Action not found';
                return;
            }
            $c->$action(...array_values($params));
            return;
        }
        http_response_code(404);
        View::render('errors/404', ['title' => Lang::get('error.404_title')], 'main');
    }

    public static function basePath(): string
    {
        $app = Config::app();
        return $app['base'] ?? '';
    }

    public static function url(string $path = ''): string
    {
        $app = Config::app();
        $base = $app['base'] ?? '';
        $path = $path === '' ? '/' : (str_starts_with($path, '/') ? $path : '/' . $path);
        return $app['url'] . $base . ($path === '/' ? '' : $path);
    }

    private static function patternToRegex(string $pattern): string
    {
        $parts = preg_split('#(\{[a-zA-Z_]+\})#', $pattern, -1, PREG_SPLIT_DELIM_CAPTURE);
        $regex = '';
        foreach ($parts as $part) {
            if (preg_match('#^\{([a-zA-Z_]+)\}$#', $part, $m)) {
                $regex .= '(?P<' . $m[1] . '>[^/]+)';
            } else {
                $regex .= preg_quote($part, '#');
            }
        }
        return '#^' . $regex . '$#';
    }
}
