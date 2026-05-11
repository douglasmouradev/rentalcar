<?php declare(strict_types=1); /** @var array<int,array<string,mixed>> $users */ ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.users') ?></h1>
    <a class="btn btn-primary" href="<?= Router::url('/users/create') ?>"><?= Lang::e('user.create') ?></a>
</div>
<div class="table-wrap card">
    <table class="table">
        <thead><tr><th><?= Lang::e('customer.name') ?></th><th><?= Lang::e('auth.email') ?></th><th><?= Lang::e('user.role') ?></th><th><?= Lang::e('location.active') ?></th><th></th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="mono"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?php
                    $role = (string) ($u['role'] ?? 'operator');
                    if ($role === 'owner') {
                        echo Lang::e('user.owner');
                    } elseif ($role === 'partner') {
                        echo Lang::e('user.partner');
                    } else {
                        echo Lang::e('user.operator');
                    }
                ?></td>
                <td><?= (int) $u['is_active'] ? '✓' : '—' ?></td>
                <td><a class="btn btn-sm btn-secondary" href="<?= Router::url('/users/' . (int) $u['id'] . '/edit') ?>"><?= Lang::e('actions.edit') ?></a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($users === []): ?><tr><td colspan="5" class="muted"><?= Lang::e('table.empty') ?></td></tr><?php endif; ?>
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
