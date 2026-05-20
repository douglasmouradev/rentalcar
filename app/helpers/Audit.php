<?php

declare(strict_types=1);

final class Audit
{
    public static function log(
        ?int $userId,
        string $action,
        string $entity,
        ?int $entityId = null,
        ?array $oldData = null,
        ?array $newData = null
    ): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = Database::pdo()->prepare(
            'INSERT INTO audit_logs (user_id, action, entity, entity_id, old_data, new_data, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            $action,
            $entity,
            $entityId,
            $oldData === null ? null : json_encode($oldData, JSON_THROW_ON_ERROR),
            $newData === null ? null : json_encode($newData, JSON_THROW_ON_ERROR),
            $ip,
        ]);
    }
}
