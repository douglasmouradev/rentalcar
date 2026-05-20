<?php declare(strict_types=1);
/** @var array<int,array<string,mixed>> $customers */
/** @var string $search */
?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.customers') ?></h1>
    <a class="btn btn-primary" href="<?= Router::url('/customers/create') ?>"><?= Lang::e('customer.create') ?></a>
</div>
<form class="filters card" method="get" action="<?= Router::url('/customers') ?>">
    <label class="label"><?= Lang::e('customer.search') ?></label>
    <div class="filter-row">
        <input class="input" type="search" name="q" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="…">
        <button type="submit" class="btn btn-secondary"><?= Lang::e('actions.filter') ?></button>
    </div>
</form>
<div class="table-wrap card">
    <table class="table">
        <thead><tr><th><?= Lang::e('customer.name') ?></th><th><?= Lang::e('customer.document') ?></th><th><?= Lang::e('customer.phone') ?></th><th></th></tr></thead>
        <tbody>
        <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="mono"><?= htmlspecialchars($c['document'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($c['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><a class="btn btn-sm btn-secondary" href="<?= Router::url('/customers/' . (int) $c['id'] . '/edit') ?>"><?= Lang::e('actions.edit') ?></a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($customers === []): ?><tr><td colspan="4" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
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
