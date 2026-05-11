<?php

declare(strict_types=1);

final class UserCar
{
    /** @return array<int, int> */
    public static function carIdsForUser(int $userId): array
    {
        $stmt = Database::pdo()->prepare('SELECT car_id FROM user_cars WHERE user_id = ? ORDER BY car_id');
        $stmt->execute([$userId]);
        $out = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $id) {
            $out[] = (int) $id;
        }
        return $out;
    }

    /**
     * @param array<int, int|string> $carIds
     */
    public static function syncForUser(int $userId, array $carIds): void
    {
        $pdo = Database::pdo();
        $pdo->prepare('DELETE FROM user_cars WHERE user_id = ?')->execute([$userId]);
        $ids = array_values(array_unique(array_filter(array_map(static fn ($v) => (int) $v, $carIds), static fn ($id) => $id > 0)));
        if ($ids === []) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $chk = $pdo->prepare('SELECT id FROM cars WHERE id IN (' . $placeholders . ')');
        $chk->execute($ids);
        $valid = array_map(static fn ($row) => (int) $row['id'], $chk->fetchAll());
        if ($valid === []) {
            return;
        }
        $ins = $pdo->prepare('INSERT INTO user_cars (user_id, car_id) VALUES (?, ?)');
        foreach ($valid as $cid) {
            $ins->execute([$userId, $cid]);
        }
    }

    public static function deleteForUser(int $userId): void
    {
        Database::pdo()->prepare('DELETE FROM user_cars WHERE user_id = ?')->execute([$userId]);
    }
}
