<?php

declare(strict_types=1);

$url = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');
// Em desenvolvimento, usa o host/porta do pedido atual (ex.: :8000 vs :8888).
if (($_ENV['APP_ENV'] ?? 'production') === 'development' && !empty($_SERVER['HTTP_HOST'])) {
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $url = ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
}

return [
    'name' => $_ENV['APP_NAME'] ?? 'Titanium Rental Car',
    'url' => $url,
    'base' => rtrim($_ENV['APP_BASE'] ?? '', '/'),
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'default_lang' => $_ENV['APP_DEFAULT_LANG'] ?? 'pt-BR',
    'session_lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 480),
    'session_secure' => filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'max_upload' => (int) ($_ENV['MAX_UPLOAD_SIZE'] ?? 5242880),
    'upload_path' => $_ENV['UPLOAD_PATH'] ?? 'public/assets/uploads',
    'per_page' => (int) ($_ENV['APP_PER_PAGE'] ?? 15),
    'privacy' => [
        'controller_name' => trim((string) ($_ENV['PRIVACY_CONTROLLER_NAME'] ?? '')),
        'controller_cnpj' => trim((string) ($_ENV['PRIVACY_CONTROLLER_CNPJ'] ?? '')),
        'address' => trim((string) ($_ENV['PRIVACY_ADDRESS'] ?? '')),
        'dpo_email' => trim((string) ($_ENV['PRIVACY_DPO_EMAIL'] ?? '')),
        'dpo_phone' => trim((string) ($_ENV['PRIVACY_DPO_PHONE'] ?? '')),
    ],
];
