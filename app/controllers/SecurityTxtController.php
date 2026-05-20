<?php

declare(strict_types=1);

final class SecurityTxtController
{
    public function index(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Content-Type: text/plain; charset=UTF-8');
        $email = trim((string) ($_ENV['SECURITY_CONTACT_EMAIL'] ?? ''));
        if ($email === '') {
            $email = trim((string) ($_ENV['PRIVACY_DPO_EMAIL'] ?? ''));
        }
        if ($email === '') {
            $email = 'security@example.com';
        }
        echo "Contact: mailto:{$email}\n";
        echo "Expires: 2027-12-31T23:59:59.000Z\n";
        echo "Preferred-Languages: pt, en\n";
    }
}
