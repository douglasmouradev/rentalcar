<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DbRateLimiterTest extends TestCase
{
    public function testHitIncrementsWithinWindow(): void
    {
        if (($_ENV['DB_DATABASE'] ?? '') === '') {
            self::markTestSkipped('DB_DATABASE não configurado');
        }
        try {
            Database::pdo();
        } catch (Throwable $e) {
            self::markTestSkipped('Base de dados indisponível');
        }

        $bucket = 'test:' . bin2hex(random_bytes(4));
        DbRateLimiter::clear($bucket);
        self::assertSame(1, DbRateLimiter::hit($bucket, 60));
        self::assertSame(2, DbRateLimiter::hit($bucket, 60));
        self::assertFalse(DbRateLimiter::tooMany($bucket, 5, 60));
        DbRateLimiter::clear($bucket);
    }
}
