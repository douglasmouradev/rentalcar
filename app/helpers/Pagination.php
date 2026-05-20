<?php

declare(strict_types=1);

final class Pagination
{
    public static function currentPage(): int
    {
        $p = (int) ($_GET['page'] ?? 1);
        return max(1, $p);
    }

    public static function perPage(): int
    {
        $app = Config::app();
        $n = (int) ($app['per_page'] ?? 20);
        return max(5, min(100, $n));
    }

    /**
     * @return array{total: int, page: int, perPage: int, totalPages: int, offset: int}
     */
    public static function meta(int $total, int $page, int $perPage): array
    {
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $totalPages);
        $offset = ($page - 1) * $perPage;
        return [
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'offset' => $offset,
        ];
    }

    /** @param array<string, scalar|null> $query */
    public static function mergeQuery(array $query, int $page): string
    {
        $query['page'] = max(1, $page);
        return http_build_query($query);
    }
}
