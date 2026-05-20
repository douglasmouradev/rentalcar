<?php

declare(strict_types=1);

final class CustomerAttachment
{
    private const STORAGE_PREFIX = 'storage/customers/';

    public static function storeRelative(string $filename): string
    {
        return self::STORAGE_PREFIX . $filename;
    }

    public static function filesystemPath(?string $stored): ?string
    {
        if ($stored === null || $stored === '') {
            return null;
        }
        if (str_starts_with($stored, self::STORAGE_PREFIX)) {
            $path = BASE_PATH . '/' . $stored;
            return is_file($path) ? $path : null;
        }
        // Legado: URL pública /storage/customers/...
        if (str_contains($stored, '/storage/customers/')) {
            $pos = strpos($stored, '/storage/customers/');
            if ($pos !== false) {
                $rel = ltrim(substr($stored, $pos), '/');
                $path = BASE_PATH . '/' . $rel;
                return is_file($path) ? $path : null;
            }
        }
        return null;
    }

    public static function downloadUrl(int $customerId): string
    {
        return Router::url('/customers/' . $customerId . '/attachment');
    }

    public static function deleteFile(?string $stored): void
    {
        $path = self::filesystemPath($stored);
        if ($path !== null && is_file($path)) {
            @unlink($path);
        }
    }
}
