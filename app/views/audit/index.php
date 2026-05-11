<?php declare(strict_types=1); /** @var array<int,array<string,mixed>> $logs */ ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.audit') ?></h1>
</div>
<div class="table-wrap card">
    <table class="table">
        <thead><tr><th>ID</th><th><?= Lang::e('reservation.operator') ?></th><th>Action</th><th>Entity</th><th>IP</th><th>When</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td class="mono"><?= (int) $log['id'] ?></td>
                <td><?= htmlspecialchars((string) ($log['user_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="mono"><?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($log['entity'], ENT_QUOTES, 'UTF-8') ?> #<?= htmlspecialchars((string) ($log['entity_id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="mono"><?= htmlspecialchars((string) ($log['ip_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="mono"><?= htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($logs === []): ?><tr><td colspan="6" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
        </tbody>
    </table>
    <?php if (!empty($pagination)): View::partial('partials/pagination', [
        'paginationBase' => $paginationBase,
        'listQuery' => $listQuery ?? [],
        'page' => (int) $pagination['page'],
        'totalPages' => (int) $pagination['totalPages'],
        'total' => (int) $pagination['total'],
        'perPage' => (int) $pagination['perPage'],
    ]); endif; ?>
</div>
