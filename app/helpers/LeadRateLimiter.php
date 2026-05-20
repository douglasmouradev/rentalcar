<?php

declare(strict_types=1);

/** Limita envios do formulário público de lead (landing). */
final class LeadRateLimiter
{
    private const MAX = 12;
    private const WINDOW = 3600;

    private static function bucket(): string
    {
        return DbRateLimiter::clientBucket('lead');
    }

    public static function tooMany(): bool
    {
        return DbRateLimiter::tooMany(self::bucket(), self::MAX, self::WINDOW);
    }

    public static function hit(): void
    {
        DbRateLimiter::hit(self::bucket(), self::WINDOW);
    }
}
