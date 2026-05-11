<?php declare(strict_types=1); ?>
<div class="empty-state card">
    <h1 class="page-title"><?= Lang::e('error.404_title') ?></h1>
    <p class="muted"><?= Lang::e('error.404_lead') ?></p>
    <p style="margin-top:1.25rem"><a class="btn btn-primary" href="<?= Router::url('/dashboard') ?>"><?= Lang::e('nav.dashboard') ?></a></p>
</div>
