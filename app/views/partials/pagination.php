<?php
declare(strict_types=1);
/** @var string $paginationBase */
/** @var array<string, scalar|null> $listQuery */
/** @var int $page */
/** @var int $totalPages */
/** @var int $total */
/** @var int $perPage */
$from = $total > 0 ? (int) (($page - 1) * $perPage + 1) : 0;
$to = (int) min($page * $perPage, $total);
$sep = str_contains($paginationBase, '?') ? '&' : '?';
?>
<nav class="pagination-bar" aria-label="<?= Lang::e('pagination.label') ?>">
    <span class="pagination-meta muted"><?= Lang::e('pagination.showing', ['from' => $from, 'to' => $to, 'total' => $total]) ?></span>
    <?php if ($totalPages > 1): ?>
    <div class="pagination-actions">
        <?php if ($page > 1): ?>
            <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($paginationBase . $sep . Pagination::mergeQuery($listQuery, $page - 1), ENT_QUOTES, 'UTF-8') ?>"><?= Lang::e('pagination.prev') ?></a>
        <?php else: ?>
            <span class="btn btn-sm btn-secondary" aria-disabled="true"><?= Lang::e('pagination.prev') ?></span>
        <?php endif; ?>
        <span class="pagination-page mono"><?= (int) $page ?> / <?= (int) $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
            <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($paginationBase . $sep . Pagination::mergeQuery($listQuery, $page + 1), ENT_QUOTES, 'UTF-8') ?>"><?= Lang::e('pagination.next') ?></a>
        <?php else: ?>
            <span class="btn btn-sm btn-secondary" aria-disabled="true"><?= Lang::e('pagination.next') ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</nav>
