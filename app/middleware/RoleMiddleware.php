<?php

declare(strict_types=1);

final class RoleMiddleware
{
    public static function handle(string $requiredRole): void
    {
        if (!Auth::check()) {
            header('Location: ' . Router::url('/login'));
            exit;
        }
        if ($requiredRole === 'owner' && !Auth::isOwner()) {
            http_response_code(403);
            View::render('errors/403', ['title' => Lang::get('error.403_title')], 'main');
            exit;
        }
    }
}
