<?php declare(strict_types=1);
/** @var array<int,array<string,mixed>> $monthly */
/** @var array<int,array<string,mixed>> $fleet */
/** @var string $from */
/** @var string $to */
?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.reports') ?></h1>
    <div class="page-actions">
        <a class="btn btn-secondary" href="<?= htmlspecialchars(Router::url('/reports/export') . '?' . http_build_query(['from' => $from, 'to' => $to]), ENT_QUOTES, 'UTF-8') ?>"><?= Lang::e('reports.export_csv') ?></a>
    </div>
</div>
<form class="filters card" method="get">
    <div class="filters-row">
        <input class="input" type="date" name="from" value="<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?>">
        <input class="input" type="date" name="to" value="<?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn btn-secondary" type="submit"><?= Lang::e('actions.filter') ?></button>
    </div>
</form>
<div class="grid two mt">
    <div class="card">
        <h2 class="card-title"><?= Lang::e('nav.reports') ?> (<?= htmlspecialchars($from, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($to, ENT_QUOTES, 'UTF-8') ?>)</h2>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Mês</th><th>Reservas</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($monthly as $row): ?>
                    <tr>
                        <td class="mono"><?= htmlspecialchars((string) $row['ym'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $row['cnt'] ?></td>
                        <td class="mono">R$ <?= number_format((float) $row['total'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($monthly === []): ?><tr><td colspan="3" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <h2 class="card-title"><?= Lang::e('nav.cars') ?> (<?= Lang::e('car.status') ?>)</h2>
        <ul class="list-plain">
            <?php foreach ($fleet as $row): ?>
                <li><span class="mono"><?= htmlspecialchars((string) $row['status'], ENT_QUOTES, 'UTF-8') ?></span> — <?= (int) $row['c'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
