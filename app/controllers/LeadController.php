<?php

declare(strict_types=1);

final class LeadController
{
    public function submit(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            header('Location: ' . Router::url('/') . '?lead=erro');
            exit;
        }
        if (LeadRateLimiter::tooMany()) {
            header('Location: ' . Router::url('/') . '?lead=limite');
            exit;
        }

        $local = trim((string) ($_POST['local'] ?? ''));
        $inicio = trim((string) ($_POST['inicio'] ?? ''));
        $fim = trim((string) ($_POST['fim'] ?? ''));
        $mesmo = isset($_POST['mesmo_local']) ? '1' : '0';
        $localDevolucao = trim((string) ($_POST['local_devolucao'] ?? ''));

        if ($local === '' || strlen($local) > 240 || !self::validDate($inicio) || !self::validDate($fim)) {
            header('Location: ' . Router::url('/') . '?lead=erro');
            exit;
        }
        if ($mesmo === '0') {
            if ($localDevolucao === '' || strlen($localDevolucao) > 240) {
                header('Location: ' . Router::url('/') . '?lead=erro');
                exit;
            }
        } else {
            // Para leads antigos ou quando não é necessário outro local, normaliza com o mesmo local
            $localDevolucao = $local;
        }
        if (strcmp($inicio, $fim) > 0) {
            header('Location: ' . Router::url('/') . '?lead=erro');
            exit;
        }

        $dir = BASE_PATH . '/storage/leads';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $line = json_encode([
            'at' => gmdate('c'),
            'local' => $local,
            'inicio' => $inicio,
            'fim' => $fim,
            'mesmo_local' => $mesmo,
            'local_devolucao' => $localDevolucao,
            'ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '')),
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
        file_put_contents($dir . '/leads.jsonl', $line, FILE_APPEND | LOCK_EX);

        LeadRateLimiter::hit();

        header('Location: ' . Router::url('/') . '?lead=1#frota');
        exit;
    }

    private static function validDate(string $d): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return false;
        }
        $t = strtotime($d . ' UTC');
        return $t !== false;
    }
}
