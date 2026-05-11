<?php declare(strict_types=1); /** @var array<int,array<string,mixed>> $reservations */ ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.reservations') ?></h1>
    <a class="btn btn-primary" href="<?= Router::url('/reservations/create') ?>"><?= Lang::e('reservation.create') ?></a>
</div>
<div class="table-wrap card">
    <table class="table">
        <thead>
        <tr>
            <th><?= Lang::e('reservation.code') ?></th>
            <th><?= Lang::e('reservation.customer') ?></th>
            <th><?= Lang::e('reservation.car') ?></th>
            <th><?= Lang::e('reservation.pickup') ?></th>
            <th><?= Lang::e('reservation.return') ?></th>
            <th><?= Lang::e('reservation.status') ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr>
                <td class="mono"><?= htmlspecialchars($r['code'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['customer_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="swatch" style="background:<?= htmlspecialchars($r['color_hex'], ENT_QUOTES, 'UTF-8') ?>"></span>
                    <?= htmlspecialchars($r['brand'] . ' ' . $r['model'], ENT_QUOTES, 'UTF-8') ?>
                    <span class="mono muted"><?= htmlspecialchars($r['license_plate'], ENT_QUOTES, 'UTF-8') ?></span>
                </td>
                <td><?= htmlspecialchars($r['pickup_date'] . ' ' . substr((string) $r['pickup_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['return_date'] . ' ' . substr((string) $r['return_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="badge st-<?= htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8') ?>"><?= Lang::e('status.' . $r['status']) ?></span></td>
                <td><a class="btn btn-sm btn-secondary" href="<?= Router::url('/reservations/' . (int) $r['id']) ?>"><?= Lang::e('actions.view') ?></a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($reservations === []): ?><tr><td colspan="7" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
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
