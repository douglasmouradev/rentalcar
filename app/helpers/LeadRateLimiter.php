<?php

declare(strict_types=1);

/** Limita envios do formulário público de lead (landing). */
final class LeadRateLimiter
{
    private const MAX = 12;
    private const WINDOW = 3600;

    private static function path(): string
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ip = preg_replace('/[^a-fA-F0-9.:]/', '_', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return $dir . '/lead_' . $ip . '.json';
    }

    public static function tooMany(): bool
    {
        $p = self::path();
        if (!is_readable($p)) {
            return false;
        }
        $raw = file_get_contents($p);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data) || !isset($data['count'], $data['first_at'])) {
            return false;
        }
        $now = time();
        if ($now - (int) $data['first_at'] > self::WINDOW) {
            return false;
        }
        return (int) $data['count'] >= self::MAX;
    }

    public static function hit(): void
    {
        $p = self::path();
        $now = time();
        $count = 1;
        $first = $now;
        if (is_readable($p)) {
            $raw = file_get_contents($p);
            $data = $raw ? json_decode($raw, true) : null;
            if (is_array($data) && isset($data['count'], $data['first_at'])) {
                if ($now - (int) $data['first_at'] <= self::WINDOW) {
                    $count = (int) $data['count'] + 1;
                    $first = (int) $data['first_at'];
                }
            }
        }
        file_put_contents($p, json_encode(['count' => $count, 'first_at' => $first]), LOCK_EX);
    }
}
