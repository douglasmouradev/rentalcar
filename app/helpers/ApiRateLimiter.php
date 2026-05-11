<?php

declare(strict_types=1);

/**
 * Limita chamadas à API autenticada por IP.
 *
 * Objectivo: evitar scraping agressivo ou abuso acidental de endpoints AJAX.
 */
final class ApiRateLimiter
{
    private const MAX = 240;      // 240 pedidos
    private const WINDOW = 300;   // em 5 minutos (~48/min)

    private static function path(): string
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ip = preg_replace('/[^a-fA-F0-9.:]/', '_', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return $dir . '/api_' . $ip . '.json';
    }

    private static function snapshot(): array
    {
        $p = self::path();
        if (!is_readable($p)) {
            return ['count' => 0, 'first_at' => time()];
        }
        $raw = file_get_contents($p);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data) || !isset($data['count'], $data['first_at'])) {
            return ['count' => 0, 'first_at' => time()];
        }
        return [
            'count' => (int) $data['count'],
            'first_at' => (int) $data['first_at'],
        ];
    }

    /**
     * Retorna true se estiver dentro do limite e atualiza o contador.
     */
    public static function hit(): bool
    {
        $p = self::path();
        $now = time();
        $snap = self::snapshot();

        if ($now - $snap['first_at'] > self::WINDOW) {
            $snap = ['count' => 0, 'first_at' => $now];
        }

        $snap['count']++;

        file_put_contents($p, json_encode($snap), LOCK_EX);

        return $snap['count'] <= self::MAX;
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

