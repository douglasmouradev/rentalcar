<?php
declare(strict_types=1);
/** @var array<int,array<string,mixed>> $allCars */
?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('user.create') ?></h1>
    <a class="btn btn-secondary" href="<?= Router::url('/users') ?>"><?= Lang::e('actions.back') ?></a>
</div>
<form class="card form-stack" method="post" action="<?= Router::url('/users') ?>">
    <?= Csrf::field() ?>
    <label class="label"><?= Lang::e('customer.name') ?></label>
    <input class="input" name="name" required>
    <label class="label"><?= Lang::e('auth.email') ?></label>
    <input class="input" type="email" name="email" required>
    <label class="label"><?= Lang::e('auth.password') ?></label>
    <input class="input" type="password" name="password" required minlength="8">
    <label class="label"><?= Lang::e('user.role') ?></label>
    <select class="input" name="role" id="user-role-select">
        <option value="operator"><?= Lang::e('user.operator') ?></option>
        <option value="owner"><?= Lang::e('user.owner') ?></option>
        <option value="partner"><?= Lang::e('user.partner') ?></option>
    </select>
    <label class="label"><?= Lang::e('customer.phone') ?></label>
    <input class="input" name="phone">
    <label class="label">Lang</label>
    <select class="input" name="lang_pref">
        <option value="pt-BR">pt-BR</option>
        <option value="en-US">en-US</option>
    </select>
    <label class="checkbox"><input type="checkbox" name="is_active" value="1" checked> <?= Lang::e('location.active') ?></label>
    <div id="partner-cars-wrap" class="field-group" hidden>
        <label class="label" for="user-car-ids"><?= Lang::e('user.assigned_cars') ?></label>
        <select class="input" name="car_ids[]" id="user-car-ids" multiple size="8">
            <?php foreach ($allCars as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars((string) $c['license_plate'] . ' — ' . $c['brand'] . ' ' . $c['model'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <p class="help-text"><?= Lang::e('user.assigned_cars_hint') ?></p>
    </div>
    <button class="btn btn-primary" type="submit"><?= Lang::e('actions.save') ?></button>
</form>
<script>
(function () {
  var role = document.getElementById('user-role-select');
  var wrap = document.getElementById('partner-cars-wrap');
  function sync() {
    if (!role || !wrap) return;
    var show = role.value === 'partner';
    wrap.hidden = !show;
  }
  if (role) { role.addEventListener('change', sync); sync(); }
})();
</script>
