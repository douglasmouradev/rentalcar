<?php
declare(strict_types=1);
/** @var array<string,mixed>|null $car */
/** @var array<int,array<string,mixed>> $locations */
$c = $car ?? [];
?>
<div class="grid two">
    <label class="label"><?= Lang::e('car.plate') ?></label>
    <input class="input" name="license_plate" required value="<?= htmlspecialchars((string) ($c['license_plate'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <label class="label"><?= Lang::e('car.brand') ?></label>
    <input class="input" name="brand" required value="<?= htmlspecialchars((string) ($c['brand'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <label class="label"><?= Lang::e('car.model') ?></label>
    <input class="input" name="model" required value="<?= htmlspecialchars((string) ($c['model'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <label class="label"><?= Lang::e('car.year') ?></label>
    <input class="input" name="year" type="number" required value="<?= (int) ($c['year'] ?? date('Y')) ?>">

    <label class="label"><?= Lang::e('car.color') ?></label>
    <input class="input" name="color" required value="<?= htmlspecialchars((string) ($c['color'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

    <label class="label"><?= Lang::e('car.color_hex') ?></label>
    <input class="input" name="color_hex" type="text" value="<?= htmlspecialchars((string) ($c['color_hex'] ?? '#CCCCCC'), ENT_QUOTES, 'UTF-8') ?>">

    <label class="label"><?= Lang::e('car.category') ?></label>
    <select class="input" name="category">
        <?php foreach (['economy','standard','suv','luxury','van','truck'] as $cat): ?>
            <option value="<?= $cat ?>" <?= (($c['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label class="label"><?= Lang::e('car.seats') ?></label>
    <input class="input" name="seats" type="number" min="1" value="<?= (int) ($c['seats'] ?? 5) ?>">

    <label class="label"><?= Lang::e('car.transmission') ?></label>
    <select class="input" name="transmission">
        <?php foreach (['manual','automatic'] as $t): ?>
            <option value="<?= $t ?>" <?= (($c['transmission'] ?? '') === $t) ? 'selected' : '' ?>><?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label class="label"><?= Lang::e('car.fuel') ?></label>
    <select class="input" name="fuel">
        <?php foreach (['flex','gasoline','diesel','electric','hybrid'] as $f): ?>
            <option value="<?= $f ?>" <?= (($c['fuel'] ?? '') === $f) ? 'selected' : '' ?>><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label class="label"><?= Lang::e('car.daily_rate') ?></label>
    <input class="input" name="daily_rate" type="number" step="0.01" required value="<?= htmlspecialchars((string) ($c['daily_rate'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">

    <label class="label"><?= Lang::e('car.status') ?></label>
    <select class="input" name="status">
        <?php foreach (['available','rented','maintenance','inactive'] as $s): ?>
            <option value="<?= $s ?>" <?= (($c['status'] ?? '') === $s) ? 'selected' : '' ?>><?= htmlspecialchars($s, ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label class="label"><?= Lang::e('car.location') ?></label>
    <select class="input" name="location_id">
        <option value="0">—</option>
        <?php foreach ($locations as $loc): ?>
            <option value="<?= (int) $loc['id'] ?>" <?= ((int) ($c['location_id'] ?? 0) === (int) $loc['id']) ? 'selected' : '' ?>><?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select>

    <label class="label"><?= Lang::e('car.mileage') ?></label>
    <input class="input" name="mileage" type="number" value="<?= (int) ($c['mileage'] ?? 0) ?>">
</div>

<div class="monthly-costs-block">
    <h3 class="form-section-title"><?= Lang::e('car.monthly_expenses') ?></h3>
    <p class="muted form-section-hint"><?= Lang::e('car.monthly_expenses_hint') ?></p>
    <div class="grid three">
        <div>
            <label class="label"><?= Lang::e('car.monthly_fuel') ?></label>
            <input class="input mono" name="monthly_fuel" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) ($c['monthly_fuel'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div>
            <label class="label"><?= Lang::e('car.monthly_toll') ?></label>
            <input class="input mono" name="monthly_toll" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) ($c['monthly_toll'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div>
            <label class="label"><?= Lang::e('car.monthly_wash') ?></label>
            <input class="input mono" name="monthly_wash" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) ($c['monthly_wash'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div>
            <label class="label"><?= Lang::e('car.monthly_maintenance') ?></label>
            <input class="input mono" name="monthly_maintenance" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) ($c['monthly_maintenance'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div>
            <label class="label"><?= Lang::e('car.monthly_extra') ?></label>
            <input class="input mono" name="monthly_extra" type="number" step="0.01" min="0" value="<?= htmlspecialchars((string) ($c['monthly_extra'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>
    <div class="monthly-total-row" aria-live="polite" aria-atomic="true">
        <span class="label monthly-total-label"><?= Lang::e('car.monthly_total_live') ?></span>
        <div id="monthlyTotalLive" class="monthly-total-live mono">R$&nbsp;0,00</div>
    </div>
</div>
<script src="<?= htmlspecialchars(Router::url('/js/car-monthly-total.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>

<div class="grid two">
    <label class="label"><?= Lang::e('car.image') ?></label>
    <input class="input" type="file" name="image" accept="image/jpeg,image/png,image/webp">

    <label class="label"><?= Lang::e('car.notes') ?></label>
    <textarea class="input" name="notes" rows="3"><?= htmlspecialchars((string) ($c['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
</div>
