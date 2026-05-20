<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class LeadRateLimiterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once BASE_PATH . '/app/helpers/LeadRateLimiter.php';
    }

    public function testTooManyWhenFileMissingIsFalse(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        self::assertFalse(LeadRateLimiter::tooMany());
    }
}
