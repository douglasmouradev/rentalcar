<?php

declare(strict_types=1);

final class LoginRateLimiter
{
    private const MAX = 5;
    private const WINDOW = 900;

    private static function bucket(): string
    {
        return DbRateLimiter::clientBucket('login');
    }

    public static function tooManyAttempts(): bool
    {
        return DbRateLimiter::tooMany(self::bucket(), self::MAX, self::WINDOW);
    }

    public static function hit(): void
    {
        DbRateLimiter::hit(self::bucket(), self::WINDOW);
    }

    public static function clear(): void
    {
        DbRateLimiter::clear(self::bucket());
    }
}
