<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    public function testTokenValidation(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        $token = Csrf::token();
        self::assertNotSame('', $token);
        self::assertTrue(Csrf::validate($token));
        self::assertFalse(Csrf::validate('invalid'));
    }
}
