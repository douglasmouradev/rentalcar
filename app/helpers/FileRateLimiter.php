<?php

declare(strict_types=1);

/** Fallback em ficheiro quando a tabela rate_limits ainda não existe. */
final class FileRateLimiter
{
    private static function path(string $bucket): string
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . '/rl_' . hash('sha256', $bucket) . '.json';
    }

    public static function tooMany(string $bucket, int $max, int $windowSeconds): bool
    {
        $p = self::path($bucket);
        if (!is_readable($p)) {
            return false;
        }
        $data = json_decode((string) file_get_contents($p), true);
        if (!is_array($data) || !isset($data['hits'], $data['window_start'])) {
            return false;
        }
        if (time() - (int) $data['window_start'] > $windowSeconds) {
            return false;
        }
        return (int) $data['hits'] >= $max;
    }

    public static function hit(string $bucket, int $windowSeconds): int
    {
        $p = self::path($bucket);
        $now = time();
        $hits = 1;
        $first = $now;
        if (is_readable($p)) {
            $data = json_decode((string) file_get_contents($p), true);
            if (is_array($data) && isset($data['hits'], $data['window_start'])) {
                if ($now - (int) $data['window_start'] <= $windowSeconds) {
                    $hits = (int) $data['hits'] + 1;
                    $first = (int) $data['window_start'];
                }
            }
        }
        file_put_contents($p, json_encode(['hits' => $hits, 'window_start' => $first]), LOCK_EX);
        return $hits;
    }

    public static function clear(string $bucket): void
    {
        $p = self::path($bucket);
        if (is_file($p)) {
            unlink($p);
        }
    }
}
