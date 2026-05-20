<?php declare(strict_types=1);
/** @var array<int,array<string,mixed>> $cars */
/** @var array<int,array<string,mixed>> $operators */
?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('nav.calendar') ?></h1>
</div>
<div class="card calendar-toolbar">
    <div class="tabs" id="calTabs">
        <button type="button" class="tab active" data-view="month"><?= Lang::e('calendar.view_month') ?></button>
        <button type="button" class="tab" data-view="week"><?= Lang::e('calendar.view_week') ?></button>
        <button type="button" class="tab" data-view="day"><?= Lang::e('calendar.view_day') ?></button>
        <button type="button" class="tab" data-view="vehicle"><?= Lang::e('calendar.view_vehicle') ?></button>
    </div>
    <div class="filters-row mt">
        <input class="input" type="month" id="calMonth" value="<?= htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8') ?>">
        <select class="input" id="fCar"><option value=""><?= Lang::e('reservation.car') ?></option>
            <?php foreach ($cars as $car): ?>
                <option value="<?= (int) $car['id'] ?>"><?= htmlspecialchars($car['brand'] . ' ' . $car['model'] . ' — ' . $car['license_plate'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (Auth::isOwner()): ?>
            <select class="input" id="fOp"><option value=""><?= Lang::e('reservation.operator') ?></option>
                <?php foreach ($operators as $op): ?>
                    <option value="<?= (int) $op['id'] ?>"><?= htmlspecialchars($op['name'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <select class="input" id="fStatus">
            <option value=""><?= Lang::e('reservation.status') ?></option>
            <?php foreach (['pending','confirmed','active','completed','cancelled'] as $s): ?>
                <option value="<?= $s ?>"><?= Lang::e('status.' . $s) ?></option>
            <?php endforeach; ?>
        </select>
        <span class="muted" id="calLoading"><?= Lang::e('calendar.loading') ?></span>
    </div>
</div>
<div id="calendarRoot" class="card mt calendar-root"></div>
<script<?= CspNonce::attr() ?>>
window.__CAL = { eventsUrl: <?= json_encode(Router::url('/api/calendar/events'), JSON_THROW_ON_ERROR) ?> };
</script>
<script src="<?= htmlspecialchars(Router::url('/js/calendar.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
