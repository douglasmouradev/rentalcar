<?php declare(strict_types=1); ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('car.create') ?></h1>
    <a class="btn btn-secondary" href="<?= Router::url('/cars') ?>"><?= Lang::e('actions.back') ?></a>
</div>
<form class="card form-stack" method="post" action="<?= Router::url('/cars') ?>" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <?php View::partial('cars/form_fields', ['car' => null, 'locations' => $locations]); ?>
    <button class="btn btn-primary" type="submit"><?= Lang::e('actions.save') ?></button>
</form>
