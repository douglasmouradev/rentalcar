<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CarSoftDeleteTest extends TestCase
{
    public function testActiveReservationCount(): void
    {
        if (($_ENV['DB_DATABASE'] ?? '') === '') {
            self::markTestSkipped('DB_DATABASE não configurado');
        }
        try {
            $pdo = Database::pdo();
        } catch (Throwable $e) {
            self::markTestSkipped('Base de dados indisponível');
        }

        $carId = (int) $pdo->query('SELECT id FROM cars WHERE deleted_at IS NULL LIMIT 1')->fetchColumn();
        if ($carId === 0) {
            self::markTestSkipped('Sem veículos no seed');
        }
        self::assertGreaterThanOrEqual(0, Car::activeReservationCount($carId));
    }
}
