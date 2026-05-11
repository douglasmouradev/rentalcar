<?php

declare(strict_types=1);

final class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return Database::pdo()->query('SELECT id, name, email, role, phone, is_active, lang_pref, created_at FROM users ORDER BY name')->fetchAll();
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, total: int, page: int, perPage: int, totalPages: int}
     */
    public static function paginated(int $page, int $perPage): array
    {
        $total = (int) Database::pdo()->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $meta = Pagination::meta($total, $page, $perPage);
        $stmt = Database::pdo()->prepare(
            'SELECT id, name, email, role, phone, is_active, lang_pref, created_at FROM users ORDER BY name LIMIT '
            . (int) $meta['perPage'] . ' OFFSET ' . (int) $meta['offset']
        );
        $stmt->execute();
        return [
            'rows' => $stmt->fetchAll(),
            'total' => $meta['total'],
            'page' => $meta['page'],
            'perPage' => $meta['perPage'],
            'totalPages' => $meta['totalPages'],
        ];
    }

    public static function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (name, email, password_hash, role, phone, is_active, lang_pref) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            $hash,
            $data['role'],
            $data['phone'] ?? null,
            (int) ($data['is_active'] ?? 1),
            $data['lang_pref'] ?? 'pt-BR',
        ]);
        return (int) Database::pdo()->lastInsertId();
    }
}
