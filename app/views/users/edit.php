<?php
declare(strict_types=1);
/** @var array<string,mixed> $user */
/** @var array<int,array<string,mixed>> $allCars */
/** @var array<int,int> $assignedCarIds */
?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('user.edit') ?></h1>
    <a class="btn btn-secondary" href="<?= Router::url('/users') ?>"><?= Lang::e('actions.back') ?></a>
</div>
<form class="card form-stack" method="post" action="<?= Router::url('/users/' . (int) $user['id'] . '/update') ?>">
    <?= Csrf::field() ?>
    <label class="label"><?= Lang::e('customer.name') ?></label>
    <input class="input" name="name" required value="<?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8') ?>">
    <label class="label"><?= Lang::e('auth.email') ?></label>
    <input class="input" type="email" name="email" required value="<?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?>">
    <label class="label"><?= Lang::e('user.password_optional') ?></label>
    <input class="input" type="password" name="password" minlength="8" autocomplete="new-password" placeholder="<?= Lang::e('user.password_keep') ?>">
    <label class="label"><?= Lang::e('user.role') ?></label>
    <select class="input" name="role" id="user-role-select">
        <option value="operator" <?= ($user['role'] ?? '') === 'operator' ? 'selected' : '' ?>><?= Lang::e('user.operator') ?></option>
        <option value="owner" <?= ($user['role'] ?? '') === 'owner' ? 'selected' : '' ?>><?= Lang::e('user.owner') ?></option>
        <option value="partner" <?= ($user['role'] ?? '') === 'partner' ? 'selected' : '' ?>><?= Lang::e('user.partner') ?></option>
    </select>
    <label class="label"><?= Lang::e('customer.phone') ?></label>
    <input class="input" name="phone" value="<?= htmlspecialchars((string) ($user['phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <label class="label">Lang</label>
    <select class="input" name="lang_pref">
        <option value="pt-BR" <?= ($user['lang_pref'] ?? '') === 'pt-BR' ? 'selected' : '' ?>>pt-BR</option>
        <option value="en-US" <?= ($user['lang_pref'] ?? '') === 'en-US' ? 'selected' : '' ?>>en-US</option>
    </select>
    <label class="checkbox"><input type="checkbox" name="is_active" value="1" <?= (int) ($user['is_active'] ?? 0) ? 'checked' : '' ?>> <?= Lang::e('location.active') ?></label>
    <div id="partner-cars-wrap" class="field-group" hidden>
        <label class="label" for="user-car-ids"><?= Lang::e('user.assigned_cars') ?></label>
        <select class="input" name="car_ids[]" id="user-car-ids" multiple size="8">
            <?php foreach ($allCars as $c): ?>
                <?php $sel = in_array((int) $c['id'], $assignedCarIds, true) ? ' selected' : ''; ?>
                <option value="<?= (int) $c['id'] ?>"<?= $sel ?>><?= htmlspecialchars((string) $c['license_plate'] . ' — ' . $c['brand'] . ' ' . $c['model'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <p class="help-text"><?= Lang::e('user.assigned_cars_hint') ?></p>
    </div>
    <button class="btn btn-primary" type="submit"><?= Lang::e('actions.save') ?></button>
</form>
<script<?= CspNonce::attr() ?>>
(function () {
  var role = document.getElementById('user-role-select');
  var wrap = document.getElementById('partner-cars-wrap');
  function sync() {
    if (!role || !wrap) return;
    wrap.hidden = role.value !== 'partner';
  }
  if (role) { role.addEventListener('change', sync); sync(); }
})();
</script>
