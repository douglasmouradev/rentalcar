<?php

declare(strict_types=1);

final class LoginRateLimiter
{
    private const MAX = 5;
    private const WINDOW = 900;

    private static function path(): string
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ip = preg_replace('/[^a-fA-F0-9.:]/', '_', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
        return $dir . '/login_' . $ip . '.json';
    }

    public static function tooManyAttempts(): bool
    {
        $p = self::path();
        if (!is_readable($p)) {
            return false;
        }
        $raw = file_get_contents($p);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data) || !isset($data['attempts'], $data['first_at'])) {
            return false;
        }
        $now = time();
        if ($now - (int) $data['first_at'] > self::WINDOW) {
            return false;
        }
        return (int) $data['attempts'] >= self::MAX;
    }

    public static function hit(): void
    {
        $p = self::path();
        $now = time();
        $attempts = 1;
        $first = $now;
        if (is_readable($p)) {
            $raw = file_get_contents($p);
            $data = $raw ? json_decode($raw, true) : null;
            if (is_array($data) && isset($data['attempts'], $data['first_at'])) {
                if ($now - (int) $data['first_at'] <= self::WINDOW) {
                    $attempts = (int) $data['attempts'] + 1;
                    $first = (int) $data['first_at'];
                }
            }
        }
        file_put_contents($p, json_encode(['attempts' => $attempts, 'first_at' => $first]), LOCK_EX);
    }

    public static function clear(): void
    {
        $p = self::path();
        if (is_file($p)) {
            unlink($p);
        }
    }
}
