<?php

declare(strict_types=1);

final class Reservation
{
    private const BLOCKING = "('pending','confirmed','active')";

    public static function find(int $id): ?array
    {
        $sql = 'SELECT r.*, c.full_name AS customer_name, c.document AS customer_document,
                car.brand, car.model, car.license_plate, car.color_hex,
                u.name AS operator_name,
                pl.name AS pickup_location_name, rl.name AS return_location_name
                FROM reservations r
                JOIN customers c ON c.id = r.customer_id
                JOIN cars car ON car.id = r.car_id
                JOIN users u ON u.id = r.operator_id
                JOIN locations pl ON pl.id = r.pickup_location_id
                JOIN locations rl ON rl.id = r.return_location_id
                WHERE r.id = ?';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public static function forOperator(?int $operatorId): array
    {
        $sql = 'SELECT r.*, c.full_name AS customer_name, car.brand, car.model, car.license_plate, car.color_hex
                FROM reservations r
                JOIN customers c ON c.id = r.customer_id
                JOIN cars car ON car.id = r.car_id';
        $params = [];
        if ($operatorId !== null) {
            $sql .= ' WHERE r.operator_id = ?';
            $params[] = $operatorId;
        }
        $sql .= ' ORDER BY r.pickup_date DESC, r.id DESC';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, total: int, page: int, perPage: int, totalPages: int}
     */
    public static function forOperatorPaginated(?int $operatorId, int $page, int $perPage): array
    {
        $base = ' FROM reservations r
                JOIN customers c ON c.id = r.customer_id
                JOIN cars car ON car.id = r.car_id';
        $where = '';
        $params = [];
        if ($operatorId !== null) {
            $where = ' WHERE r.operator_id = ?';
            $params[] = $operatorId;
        }
        $stmt = Database::pdo()->prepare('SELECT COUNT(*)' . $base . $where);
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();
        $meta = Pagination::meta($total, $page, $perPage);
        $sql = 'SELECT r.*, c.full_name AS customer_name, car.brand, car.model, car.license_plate, car.color_hex'
            . $base . $where . ' ORDER BY r.pickup_date DESC, r.id DESC LIMIT ' . (int) $meta['perPage'] . ' OFFSET ' . (int) $meta['offset'];
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return [
            'rows' => $stmt->fetchAll(),
            'total' => $meta['total'],
            'page' => $meta['page'],
            'perPage' => $meta['perPage'],
            'totalPages' => $meta['totalPages'],
        ];
    }

    public static function nextCode(): string
    {
        $year = (int) date('Y');
        $stmt = Database::pdo()->prepare(
            "SELECT code FROM reservations WHERE code LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $prefix = 'TRC-' . $year . '-';
        $stmt->execute([$prefix . '%']);
        $last = $stmt->fetchColumn();
        $n = 1;
        if (is_string($last) && preg_match('/TRC-\d+-(\d+)/', $last, $m)) {
            $n = (int) $m[1] + 1;
        }
        return $prefix . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param array<string, mixed> $pick Return assoc with pickup/return datetimes as strings Y-m-d H:i:s
     */
    public static function hasConflict(
        int $carId,
        string $pickupDate,
        string $pickupTime,
        string $returnDate,
        string $returnTime,
        ?int $excludeReservationId = null
    ): bool {
        $pickup = $pickupDate . ' ' . $pickupTime;
        $ret = $returnDate . ' ' . $returnTime;
        $sql = "SELECT COUNT(*) FROM reservations
                WHERE car_id = ? AND status IN " . self::BLOCKING . "
                AND CONCAT(pickup_date, ' ', pickup_time) < ?
                AND CONCAT(return_date, ' ', return_time) > ?";
        $params = [$carId, $ret, $pickup];
        if ($excludeReservationId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeReservationId;
        }
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function create(array $d): int
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO reservations (code, customer_id, car_id, operator_id, pickup_location_id, return_location_id,
             pickup_date, pickup_time, return_date, return_time, daily_rate, total_days, total_amount, discount, final_amount,
             status, payment_status, payment_method, notes)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $d['code'], $d['customer_id'], $d['car_id'], $d['operator_id'], $d['pickup_location_id'], $d['return_location_id'],
            $d['pickup_date'], $d['pickup_time'], $d['return_date'], $d['return_time'],
            $d['daily_rate'], $d['total_days'], $d['total_amount'], $d['discount'], $d['final_amount'],
            $d['status'], $d['payment_status'], $d['payment_method'] ?? null, $d['notes'] ?? null,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function update(int $id, array $d): void
    {
        $stmt = Database::pdo()->prepare(
            'UPDATE reservations SET customer_id=?, car_id=?, pickup_location_id=?, return_location_id=?,
             pickup_date=?, pickup_time=?, return_date=?, return_time=?, daily_rate=?, total_days=?, total_amount=?, discount=?, final_amount=?,
             status=?, payment_status=?, payment_method=?, notes=? WHERE id=?'
        );
        $stmt->execute([
            $d['customer_id'], $d['car_id'], $d['pickup_location_id'], $d['return_location_id'],
            $d['pickup_date'], $d['pickup_time'], $d['return_date'], $d['return_time'],
            $d['daily_rate'], $d['total_days'], $d['total_amount'], $d['discount'], $d['final_amount'],
            $d['status'], $d['payment_status'], $d['payment_method'] ?? null, $d['notes'] ?? null, $id,
        ]);
    }

    public static function setStatus(int $id, string $status): void
    {
        $stmt = Database::pdo()->prepare('UPDATE reservations SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    /** Calendar events in range */
    public static function eventsBetween(string $start, string $end, ?int $carId, ?int $operatorId, ?string $status): array
    {
        $sql = 'SELECT r.*, c.full_name AS customer_name, car.brand, car.model, car.license_plate, car.color_hex,
                u.name AS operator_name,
                pl.name AS pickup_location_name, rl.name AS return_location_name
                FROM reservations r
                JOIN customers c ON c.id = r.customer_id
                JOIN cars car ON car.id = r.car_id
                JOIN users u ON u.id = r.operator_id
                JOIN locations pl ON pl.id = r.pickup_location_id
                JOIN locations rl ON rl.id = r.return_location_id
                WHERE r.return_date >= ? AND r.pickup_date <= ?';
        $params = [$start, $end];
        if ($carId) {
            $sql .= ' AND r.car_id = ?';
            $params[] = $carId;
        }
        if ($operatorId) {
            $sql .= ' AND r.operator_id = ?';
            $params[] = $operatorId;
        }
        if ($status && $status !== '') {
            $sql .= ' AND r.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY r.pickup_date, r.pickup_time';
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
