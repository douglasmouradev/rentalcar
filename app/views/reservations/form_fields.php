<?php
declare(strict_types=1);
/** @var array<int,array<string,mixed>> $cars */
/** @var array<int,array<string,mixed>> $locations */
/** @var array<int,array<string,mixed>> $customers */
/** @var array<string,mixed>|null $r */
$rv = $r ?? [];
$slots = TimeHelper::slots30();
$today = date('Y-m-d');
$defaultRate = $rv['daily_rate'] ?? ($cars[0]['daily_rate'] ?? 0);
?>
<div class="grid two">
    <div>
        <label class="label"><?= Lang::e('customer.search') ?></label>
        <input class="input" type="search" id="custSearch" placeholder="…" autocomplete="off">
        <div id="custSuggest" class="suggest"></div>
        <label class="label"><?= Lang::e('reservation.customer') ?></label>
        <select class="input" name="customer_id" id="customer_id" required>
            <?php foreach ($customers as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= ((int) ($rv['customer_id'] ?? 0) === (int) $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['full_name'] . ' — ' . $c['document'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" class="btn btn-ghost btn-sm" id="openQuickCust"><?= Lang::e('reservation.new_customer') ?></button>
    </div>
    <div>
        <label class="label"><?= Lang::e('reservation.car') ?></label>
        <select class="input" name="car_id" id="car_id" required>
            <?php foreach ($cars as $car): ?>
                <option value="<?= (int) $car['id'] ?>"
                    data-rate="<?= htmlspecialchars((string) $car['daily_rate'], ENT_QUOTES, 'UTF-8') ?>"
                    data-label="<?= htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' (' . $car['license_plate'] . ')', ENT_QUOTES, 'UTF-8') ?>"
                    <?= ((int) ($rv['car_id'] ?? 0) === (int) $car['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' — ' . $car['license_plate'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="carPreview" class="car-preview muted"></div>
    </div>
</div>

<div class="grid two">
    <div>
        <label class="label"><?= Lang::e('reservation.pickup') ?> (<?= Lang::e('reservation.schedule') ?>)</label>
        <input class="input" type="date" name="pickup_date" id="pickup_date" required min="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>"
               value="<?= htmlspecialchars((string) ($rv['pickup_date'] ?? $today), ENT_QUOTES, 'UTF-8') ?>">
        <select class="input" name="pickup_time" id="pickup_time">
            <?php foreach ($slots as $val => $label): ?>
                <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= ((string) ($rv['pickup_time'] ?? '09:00:00') === $val) ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="label"><?= Lang::e('reservation.return') ?></label>
        <input class="input" type="date" name="return_date" id="return_date" required
               value="<?= htmlspecialchars((string) ($rv['return_date'] ?? $today), ENT_QUOTES, 'UTF-8') ?>">
        <select class="input" name="return_time" id="return_time">
            <?php foreach ($slots as $val => $label): ?>
                <option value="<?= htmlspecialchars($val, ENT_QUOTES, 'UTF-8') ?>" <?= ((string) ($rv['return_time'] ?? '18:00:00') === $val) ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="grid two">
    <div>
        <label class="label"><?= Lang::e('reservation.pickup') ?> — <?= Lang::e('reservation.location') ?></label>
        <select class="input" name="pickup_location_id" required>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= (int) $loc['id'] ?>" <?= ((int) ($rv['pickup_location_id'] ?? 0) === (int) $loc['id']) ? 'selected' : '' ?>><?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="label"><?= Lang::e('reservation.return') ?> — <?= Lang::e('reservation.location') ?></label>
        <select class="input" name="return_location_id" required>
            <?php foreach ($locations as $loc): ?>
                <option value="<?= (int) $loc['id'] ?>" <?= ((int) ($rv['return_location_id'] ?? 0) === (int) $loc['id']) ? 'selected' : '' ?>><?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="grid three">
    <div>
        <label class="label"><?= Lang::e('car.daily_rate') ?></label>
        <input class="input mono" name="daily_rate" id="daily_rate" type="number" step="0.01" required
               value="<?= htmlspecialchars((string) $defaultRate, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <?php if (Auth::isOwner()): ?>
        <div>
            <label class="label"><?= Lang::e('reservation.discount') ?></label>
            <input class="input mono" name="discount" id="discount" type="number" step="0.01" value="<?= htmlspecialchars((string) ($rv['discount'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>">
        </div>
    <?php else: ?>
        <input type="hidden" name="discount" id="discount" value="0">
    <?php endif; ?>
    <div>
        <label class="label"><?= Lang::e('reservation.total') ?></label>
        <div id="total_preview" class="kpi-inline mono">R$ 0,00</div>
        <div id="conflict_msg" class="toast toast-error hidden"></div>
    </div>
</div>

<div class="grid two">
    <div>
        <label class="label"><?= Lang::e('reservation.status') ?></label>
        <select class="input" name="status">
            <?php foreach (['pending','confirmed','active','completed'] as $s): ?>
                <option value="<?= $s ?>" <?= ((string) ($rv['status'] ?? 'pending') === $s) ? 'selected' : '' ?>><?= Lang::e('status.' . $s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="label"><?= Lang::e('reservation.payment') ?></label>
        <select class="input" name="payment_status">
            <?php foreach (['unpaid','partial','paid'] as $p): ?>
                <option value="<?= $p ?>" <?= ((string) ($rv['payment_status'] ?? 'unpaid') === $p) ? 'selected' : '' ?>><?= Lang::e('payment.' . $p) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="input" name="payment_method">
            <option value="">—</option>
            <?php foreach (['cash','credit_card','debit_card','pix','transfer'] as $pm): ?>
                <option value="<?= $pm ?>" <?= ((string) ($rv['payment_method'] ?? '') === $pm) ? 'selected' : '' ?>><?= Lang::e('pay.' . $pm) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<label class="label"><?= Lang::e('reservation.notes') ?></label>
<textarea class="input" name="notes" rows="2"><?= htmlspecialchars((string) ($rv['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>

<div id="quickCustModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-card">
        <h3><?= Lang::e('customer.new_quick') ?></h3>
        <div class="form-stack">
            <input class="input" id="qc_name" placeholder="<?= Lang::e('customer.name') ?>">
            <input class="input" id="qc_doc" placeholder="<?= Lang::e('customer.document') ?>">
            <input class="input" id="qc_phone" placeholder="<?= Lang::e('customer.phone') ?>">
            <input class="input" id="qc_email" placeholder="<?= Lang::e('customer.email') ?>">
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="qc_close"><?= Lang::e('actions.cancel') ?></button>
                <button type="button" class="btn btn-primary" id="qc_save"><?= Lang::e('actions.save') ?></button>
            </div>
        </div>
    </div>
</div>
