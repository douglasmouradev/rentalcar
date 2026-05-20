<?php declare(strict_types=1); /** @var array<string,mixed> $r */ ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('reservation.edit') ?></h1>
    <a class="btn btn-secondary" href="<?= Router::url('/reservations/' . (int) $r['id']) ?>"><?= Lang::e('actions.back') ?></a>
</div>
<form class="card form-stack" method="post" action="<?= Router::url('/reservations/' . (int) $r['id'] . '/update') ?>" id="resForm">
    <?= Csrf::field() ?>
    <?php View::partial('reservations/form_fields', ['cars' => $cars, 'locations' => $locations, 'selectedCustomer' => $selectedCustomer ?? null, 'r' => $r]); ?>
    <button class="btn btn-primary" type="submit"><?= Lang::e('actions.save') ?></button>
</form>
<script src="<?= htmlspecialchars(Router::url('/js/reservation-form.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<script<?= CspNonce::attr() ?>>
window.__RES_FORM = { excludeId: <?= (int) $r['id'] ?>, conflictText: <?= json_encode(Lang::get('reservation.conflict'), JSON_THROW_ON_ERROR) ?>, conflictUrl: <?= json_encode(Router::url('/api/reservations/conflict'), JSON_THROW_ON_ERROR) ?>, searchUrl: <?= json_encode(Router::url('/api/customers/search'), JSON_THROW_ON_ERROR) ?>, quickUrl: <?= json_encode(Router::url('/api/customers/quick'), JSON_THROW_ON_ERROR) ?> };
</script>
