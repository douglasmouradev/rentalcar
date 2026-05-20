<?php

declare(strict_types=1);

final class CspNonce
{
    private static ?string $nonce = null;

    public static function get(): string
    {
        if (self::$nonce === null) {
            self::$nonce = base64_encode(random_bytes(16));
        }
        return self::$nonce;
    }

    public static function attr(): string
    {
        return ' nonce="' . htmlspecialchars(self::get(), ENT_QUOTES, 'UTF-8') . '"';
    }
}
