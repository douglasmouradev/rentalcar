<?php

declare(strict_types=1);

final class Customer
{
    /** @return array<int, array<string, mixed>> */
    public static function searchAutocomplete(string $q, int $limit = 20): array
    {
        $q = trim($q);
        if ($q === '') {
            return [];
        }
        $like = '%' . $q . '%';
        $stmt = Database::pdo()->prepare(
            'SELECT id, type, full_name, document, email, phone FROM customers
             WHERE full_name LIKE ? OR document LIKE ? OR email LIKE ?
             ORDER BY full_name LIMIT ' . (int) $limit
        );
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM customers WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function all(): array
    {
        return Database::pdo()->query('SELECT * FROM customers ORDER BY full_name')->fetchAll();
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, total: int, page: int, perPage: int, totalPages: int}
     */
    public static function paginated(int $page, int $perPage, ?string $q = null): array
    {
        $where = '';
        $params = [];
        if ($q !== null && trim($q) !== '') {
            $like = '%' . trim($q) . '%';
            $where = ' WHERE full_name LIKE ? OR document LIKE ? OR email LIKE ? OR phone LIKE ?';
            $params = [$like, $like, $like, $like];
        }
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM customers' . $where);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();
        $meta = Pagination::meta($total, $page, $perPage);
        $stmt = Database::pdo()->prepare(
            'SELECT * FROM customers' . $where . ' ORDER BY full_name LIMIT '
            . (int) $meta['perPage'] . ' OFFSET ' . (int) $meta['offset']
        );
        $stmt->execute($params);
        return [
            'rows' => $stmt->fetchAll(),
            'total' => $meta['total'],
            'page' => $meta['page'],
            'perPage' => $meta['perPage'],
            'totalPages' => $meta['totalPages'],
        ];
    }

    public static function reservationCount(int $id): int
    {
        $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM reservations WHERE customer_id = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

    public static function create(array $d): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO customers (type, full_name, document, email, phone, address, city, state, zip_code, notes, attachment_path, created_by)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['type'], $d['full_name'], $d['document'], $d['email'] ?: null, $d['phone'],
            $d['address'] ?? null, $d['city'] ?? null, $d['state'] ?? null, $d['zip_code'] ?? null,
            $d['notes'] ?? null, $d['attachment_path'] ?? null, $d['created_by'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE customers SET type=?, full_name=?, document=?, email=?, phone=?, address=?, city=?, state=?, zip_code=?, notes=?, attachment_path=? WHERE id=?'
        );
        $stmt->execute([
            $d['type'], $d['full_name'], $d['document'], $d['email'] ?: null, $d['phone'],
            $d['address'] ?? null, $d['city'] ?? null, $d['state'] ?? null, $d['zip_code'] ?? null,
            $d['notes'] ?? null, $d['attachment_path'] ?? null, $id,
        ]);
    }
}
