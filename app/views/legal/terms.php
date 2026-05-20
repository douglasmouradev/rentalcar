<?php

declare(strict_types=1);

/** @var string $title */
?>
<div class="legal-wrap">
    <h1 class="legal-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="legal-updated"><?= Lang::e('legal.last_updated') ?></p>
    <?php
    $locale = Lang::locale();
    $partial = APP_PATH . '/views/partials/legal/terms_' . ($locale === 'en-US' ? 'en' : 'pt') . '.php';
    include is_readable($partial) ? $partial : APP_PATH . '/views/partials/legal/terms_pt.php';
    ?>
</div>
