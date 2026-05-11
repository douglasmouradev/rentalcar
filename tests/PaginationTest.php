<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once BASE_PATH . '/app/helpers/Pagination.php';
    }

    public function testMetaComputesPages(): void
    {
        $m = Pagination::meta(25, 2, 10);
        self::assertSame(25, $m['total']);
        self::assertSame(3, $m['totalPages']);
        self::assertSame(2, $m['page']);
        self::assertSame(10, $m['perPage']);
        self::assertSame(10, $m['offset']);
    }

    public function testMetaClampsPageToTotalPages(): void
    {
        $m = Pagination::meta(5, 99, 10);
        self::assertSame(1, $m['totalPages']);
        self::assertSame(1, $m['page']);
    }

    public function testMergeQuery(): void
    {
        $q = Pagination::mergeQuery(['status' => 'available', 'q' => 'fiat'], 3);
        self::assertStringContainsString('page=3', $q);
        self::assertStringContainsString('status=available', $q);
    }
}
