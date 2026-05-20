<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ReservationValidatorTest extends TestCase
{
    public function testRejectsInvalidDateRange(): void
    {
        if (($_ENV['DB_DATABASE'] ?? '') === '') {
            self::markTestSkipped('DB não configurado');
        }
        try {
            Database::pdo();
        } catch (Throwable) {
            self::markTestSkipped('BD indisponível');
        }

        $result = ReservationValidator::validate([
            'pickup_date' => '2030-06-10',
            'return_date' => '2030-06-01',
            'pickup_time' => '09:00',
            'return_time' => '18:00',
            'customer_id' => '1',
            'car_id' => '1',
            'pickup_location_id' => '1',
            'return_location_id' => '1',
            'daily_rate' => '100',
        ], true);

        self::assertFalse($result['ok']);
        self::assertSame('reservation.invalid_range', $result['error']);
    }
}
