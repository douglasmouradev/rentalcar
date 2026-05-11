<?php

declare(strict_types=1);

$privacyUrl = Router::url('/privacidade') . '#cookies';
?>
<div id="cookie-notice" class="cookie-notice" role="dialog" aria-modal="false" aria-labelledby="cookie-notice-title" aria-describedby="cookie-notice-desc" hidden>
    <div class="cookie-notice-inner">
        <p id="cookie-notice-title" class="cookie-notice-title"><?= Lang::e('legal.cookie_title') ?></p>
        <p id="cookie-notice-desc" class="cookie-notice-text">
            <?= Lang::e('legal.cookie_body') ?>
            <a href="<?= htmlspecialchars($privacyUrl, ENT_QUOTES, 'UTF-8') ?>"><?= Lang::e('legal.cookie_link') ?></a>.
        </p>
        <button type="button" class="cookie-notice-btn" data-cookie-accept><?= Lang::e('legal.cookie_accept') ?></button>
    </div>
</div>
