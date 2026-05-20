<?php

declare(strict_types=1);

/**
 * Limita chamadas à API autenticada por IP.
 */
final class ApiRateLimiter
{
    private const MAX = 240;
    private const WINDOW = 300;

    private static function bucket(): string
    {
        return DbRateLimiter::clientBucket('api');
    }

    /**
     * Retorna true se estiver dentro do limite e atualiza o contador.
     */
    public static function hit(): bool
    {
        return DbRateLimiter::hit(self::bucket(), self::WINDOW) <= self::MAX;
    }

    /**
     * Garante que o IP não está acima do limite; responde 429 e termina se estiver.
     */
    public static function guardJson(): void
    {
        if (self::hit()) {
            return;
        }
        http_response_code(429);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'rate_limited'], JSON_THROW_ON_ERROR);
        exit;
    }
}
