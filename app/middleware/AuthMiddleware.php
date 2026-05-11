<?php

declare(strict_types=1);

final class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . Router::url('/login'));
            exit;
        }
    }
}
