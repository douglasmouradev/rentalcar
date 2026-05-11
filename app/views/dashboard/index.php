<?php declare(strict_types=1);
/** @var bool $isOwner */
/** @var float $revenueMonth */
/** @var int $fleet */
/** @var int $activeRes */
/** @var int $occupancy */
/** @var int $unpaid */
/** @var array<string,int> $chartDays */
/** @var array<int,array<string,mixed>> $revenueByCategory */
/** @var array<int,array<string,mixed>> $returns */
/** @var array<int,array<string,mixed>> $maintenance */
/** @var array<int,array<string,mixed>> $myToday */
/** @var int $myTodayCount */
$fmt = static fn (float $v) => 'R$ ' . number_format($v, 2, ',', '.');
?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.dashboard') ?></h1>
</div>

<?php if (!$isOwner): ?>
    <div class="grid kpis">
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.operator_today') ?></div><div class="kpi-value"><?= (int) $myTodayCount ?></div></div>
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.operator_upcoming') ?></div><div class="kpi-value"><?= count($myToday) ?></div></div>
    </div>
    <div class="card mt">
        <h2 class="card-title"><?= Lang::e('dashboard.operator_upcoming') ?></h2>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th><?= Lang::e('reservation.code') ?></th><th><?= Lang::e('reservation.customer') ?></th><th><?= Lang::e('reservation.car') ?></th><th><?= Lang::e('reservation.pickup') ?></th><th></th></tr></thead>
                <tbody>
                <?php foreach ($myToday as $row): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['brand'] . ' ' . $row['model'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['pickup_date'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><a class="btn btn-sm btn-secondary" href="<?= Router::url('/reservations/' . (int) $row['id']) ?>"><?= Lang::e('actions.view') ?></a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($myToday === []): ?><tr><td colspan="5" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="grid kpis">
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.revenue_month') ?></div><div class="kpi-value"><?= $fmt($revenueMonth) ?></div></div>
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.fleet') ?></div><div class="kpi-value"><?= (int) $fleet ?></div></div>
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.active_res') ?></div><div class="kpi-value"><?= (int) $activeRes ?></div></div>
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.occupancy') ?></div><div class="kpi-value"><?= (int) $occupancy ?>%</div></div>
        <div class="card kpi"><div class="kpi-label"><?= Lang::e('dashboard.unpaid') ?></div><div class="kpi-value"><?= (int) $unpaid ?></div></div>
    </div>

    <div class="grid two mt">
        <div class="card">
            <h2 class="card-title"><?= Lang::e('dashboard.chart_reservations') ?></h2>
            <div class="bar-chart">
                <?php
                $max = max(1, ...array_values($chartDays));
                foreach ($chartDays as $day => $c):
                    $h = (int) round(($c / $max) * 100);
                    ?>
                    <div class="bar" title="<?= htmlspecialchars($day . ': ' . $c, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="bar-fill" style="height:<?= $h ?>%"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card">
            <h2 class="card-title"><?= Lang::e('dashboard.chart_category') ?></h2>
            <ul class="list-plain">
                <?php foreach ($revenueByCategory as $cat): ?>
                    <li><span class="mono"><?= htmlspecialchars((string) $cat['category'], ENT_QUOTES, 'UTF-8') ?></span> — <?= $fmt((float) $cat['total']) ?></li>
                <?php endforeach; ?>
                <?php if ($revenueByCategory === []): ?><li class="muted"><?= Lang::e('table.empty') ?></li><?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="grid two mt">
        <div class="card">
            <h2 class="card-title"><?= Lang::e('dashboard.returns') ?></h2>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th><?= Lang::e('reservation.code') ?></th><th><?= Lang::e('reservation.customer') ?></th><th><?= Lang::e('car.plate') ?></th><th><?= Lang::e('reservation.return') ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($returns as $row): ?>
                        <tr>
                            <td class="mono"><?= htmlspecialchars($row['code'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="mono"><?= htmlspecialchars($row['license_plate'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['return_date'] . ' ' . substr((string) $row['return_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($returns === []): ?><tr><td colspan="4" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <h2 class="card-title"><?= Lang::e('dashboard.maintenance') ?></h2>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th><?= Lang::e('car.model') ?></th><th><?= Lang::e('car.plate') ?></th><th><?= Lang::e('car.color') ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($maintenance as $car): ?>
                        <tr>
                            <td><?= htmlspecialchars($car['brand'] . ' ' . $car['model'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="mono"><?= htmlspecialchars($car['license_plate'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="swatch" style="background:<?= htmlspecialchars($car['color_hex'], ENT_QUOTES, 'UTF-8') ?>"></span> <?= htmlspecialchars($car['color'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($maintenance === []): ?><tr><td colspan="3" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
