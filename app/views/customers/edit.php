<?php declare(strict_types=1); /** @var array<string,mixed> $customer */ ?>
<div class="page-head">
    <h1 class="page-title"><?= Lang::e('customer.edit') ?></h1>
    <a class="btn btn-secondary" href="<?= Router::url('/customers') ?>"><?= Lang::e('actions.back') ?></a>
</div>
<form class="card form-stack" method="post" action="<?= Router::url('/customers/' . (int) $customer['id'] . '/update') ?>" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <?php View::partial('customers/form_fields', ['customer' => $customer]); ?>
    <button class="btn btn-primary" type="submit"><?= Lang::e('actions.save') ?></button>
</form>
