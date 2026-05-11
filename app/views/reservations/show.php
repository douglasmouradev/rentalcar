<?php declare(strict_types=1); /** @var array<string,mixed> $r */ ?>
<div class="page-head">
    <h1 class="page-title mono"><?= htmlspecialchars($r['code'], ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= Router::url('/reservations') ?>"><?= Lang::e('actions.back') ?></a>
        <a class="btn btn-primary" href="<?= Router::url('/reservations/' . (int) $r['id'] . '/edit') ?>"><?= Lang::e('actions.edit') ?></a>
        <?php if ($r['status'] !== 'cancelled' && $r['status'] !== 'completed'): ?>
            <form method="post" action="<?= Router::url('/reservations/' . (int) $r['id'] . '/cancel') ?>" class="inline-form" onsubmit="return confirm('OK?');">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-danger"><?= Lang::e('reservation.cancel_btn') ?></button>
            </form>
        <?php endif; ?>
    </div>
</div>
<div class="grid two">
    <div class="card">
        <h2 class="card-title"><?= Lang::e('reservation.customer') ?></h2>
        <p><?= htmlspecialchars($r['customer_name'], ENT_QUOTES, 'UTF-8') ?> <span class="mono muted"><?= htmlspecialchars($r['customer_document'], ENT_QUOTES, 'UTF-8') ?></span></p>
        <h2 class="card-title"><?= Lang::e('reservation.car') ?></h2>
        <p><span class="swatch" style="background:<?= htmlspecialchars($r['color_hex'], ENT_QUOTES, 'UTF-8') ?>"></span>
            <?= htmlspecialchars($r['brand'] . ' ' . $r['model'], ENT_QUOTES, 'UTF-8') ?> — <span class="mono"><?= htmlspecialchars($r['license_plate'], ENT_QUOTES, 'UTF-8') ?></span></p>
        <h2 class="card-title"><?= Lang::e('reservation.operator') ?></h2>
        <p><?= htmlspecialchars($r['operator_name'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card">
        <dl class="dl">
            <dt><?= Lang::e('reservation.pickup') ?></dt>
            <dd><?= htmlspecialchars($r['pickup_date'] . ' ' . substr((string) $r['pickup_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($r['pickup_location_name'], ENT_QUOTES, 'UTF-8') ?></dd>
            <dt><?= Lang::e('reservation.return') ?></dt>
            <dd><?= htmlspecialchars($r['return_date'] . ' ' . substr((string) $r['return_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($r['return_location_name'], ENT_QUOTES, 'UTF-8') ?></dd>
            <dt><?= Lang::e('reservation.status') ?></dt>
            <dd><span class="badge st-<?= htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8') ?>"><?= Lang::e('status.' . $r['status']) ?></span></dd>
            <dt><?= Lang::e('reservation.payment') ?></dt>
            <dd><?= Lang::e('payment.' . $r['payment_status']) ?><?php if (!empty($r['payment_method'])): ?> / <?= Lang::e('pay.' . $r['payment_method']) ?><?php endif; ?></dd>
            <dt><?= Lang::e('reservation.days') ?></dt>
            <dd><?= (int) $r['total_days'] ?></dd>
            <dt><?= Lang::e('reservation.total') ?></dt>
            <dd class="mono">R$ <?= number_format((float) $r['final_amount'], 2, ',', '.') ?></dd>
        </dl>
        <p class="muted"><?= nl2br(htmlspecialchars((string) ($r['notes'] ?? ''), ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
</div>
