<?php

declare(strict_types=1);

final class LegalController
{
    public function privacy(): void
    {
        $cfg = Config::app();
        View::render('legal.privacy', [
            'title' => Lang::get('legal.privacy_title'),
            'privacy' => $cfg['privacy'] ?? [],
        ], 'main');
    }

    public function terms(): void
    {
        View::render('legal.terms', [
            'title' => Lang::get('legal.terms_title'),
        ], 'main');
    }
}
