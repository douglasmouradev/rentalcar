<?php declare(strict_types=1); /** @var array<string,mixed>|null $location */ $l = $location ?? []; $isEdit = $l !== []; ?>
<div class="page-head">
    <h1 class="page-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <a class="btn btn-secondary" href="<?= Router::url('/locations') ?>"><?= Lang::e('actions.back') ?></a>
</div>
<form class="card form-stack" method="post" action="<?= $isEdit ? Router::url('/locations/' . (int) $l['id'] . '/update') : Router::url('/locations') ?>">
    <?= Csrf::field() ?>
    <label class="label"><?= Lang::e('location.name') ?></label>
    <input class="input" name="name" required value="<?= htmlspecialchars((string) ($l['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <label class="label"><?= Lang::e('location.address') ?></label>
    <input class="input" name="address" required value="<?= htmlspecialchars((string) ($l['address'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <div class="grid two">
        <div>
            <label class="label"><?= Lang::e('location.city') ?></label>
            <input class="input" name="city" required value="<?= htmlspecialchars((string) ($l['city'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div>
            <label class="label"><?= Lang::e('location.state') ?></label>
            <input class="input" name="state" required value="<?= htmlspecialchars((string) ($l['state'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>
    <label class="label"><?= Lang::e('location.zip') ?></label>
    <input class="input" name="zip_code" value="<?= htmlspecialchars((string) ($l['zip_code'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <label class="label"><?= Lang::e('customer.phone') ?></label>
    <input class="input" name="phone" value="<?= htmlspecialchars((string) ($l['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <label class="checkbox"><input type="checkbox" name="is_active" value="1" <?= (($l['is_active'] ?? 1) == 1) ? 'checked' : '' ?>> <?= Lang::e('location.active') ?></label>
    <button class="btn btn-primary" type="submit"><?= Lang::e('actions.save') ?></button>
</form>
