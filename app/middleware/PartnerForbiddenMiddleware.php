<?php

declare(strict_types=1);

final class PartnerForbiddenMiddleware
{
    /** Bloqueia cotistas em rotas operacionais (reservas, clientes, API). */
    public static function handle(): void
    {
        if (!Auth::isPartner()) {
            return;
        }
        http_response_code(403);
        View::render('errors/403', ['title' => Lang::get('error.403_title')], 'main');
        exit;
    }

    /** Resposta JSON para rotas /api. */
    public static function handleJson(): void
    {
        if (!Auth::isPartner()) {
            return;
        }
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_THROW_ON_ERROR);
        exit;
    }
}
