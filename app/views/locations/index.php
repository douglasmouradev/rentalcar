<?php declare(strict_types=1); /** @var array<int,array<string,mixed>> $locations */ ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.locations') ?></h1>
    <a class="btn btn-primary" href="<?= Router::url('/locations/create') ?>"><?= Lang::e('location.create') ?></a>
</div>
<div class="table-wrap card">
    <table class="table">
        <thead><tr><th><?= Lang::e('location.name') ?></th><th><?= Lang::e('location.city') ?></th><th><?= Lang::e('location.active') ?></th><th></th></tr></thead>
        <tbody>
        <?php foreach ($locations as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($l['city'] . '/' . $l['state'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $l['is_active'] ? '✓' : '—' ?></td>
                <td><a class="btn btn-sm btn-secondary" href="<?= Router::url('/locations/' . (int) $l['id'] . '/edit') ?>"><?= Lang::e('actions.edit') ?></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
