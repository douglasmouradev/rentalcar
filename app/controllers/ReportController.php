<?php

declare(strict_types=1);

final class ReportController
{
    public function index(): void
    {
        $pdo = Database::pdo();
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-t');
        $stmt = $pdo->prepare(
            "SELECT DATE_FORMAT(r.pickup_date, '%Y-%m') AS ym, SUM(r.final_amount) AS total, COUNT(*) AS cnt
             FROM reservations r
             WHERE r.status IN ('confirmed','active','completed') AND r.pickup_date BETWEEN ? AND ?
             GROUP BY ym ORDER BY ym"
        );
        $stmt->execute([$from, $to]);
        $monthly = $stmt->fetchAll();

        $fleet = $pdo->query(
            "SELECT status, COUNT(*) AS c FROM cars WHERE deleted_at IS NULL GROUP BY status"
        )->fetchAll();

        View::render('reports/index', [
            'title' => Lang::get('nav.reports'),
            'monthly' => $monthly,
            'fleet' => $fleet,
            'from' => $from,
            'to' => $to,
        ], 'main');
    }

    public function exportCsv(): void
    {
        $pdo = Database::pdo();
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-t');
        if (!is_string($from) || !is_string($to) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            http_response_code(400);
            echo 'Invalid range';
            return;
        }
        $stmt = $pdo->prepare(
            "SELECT DATE_FORMAT(r.pickup_date, '%Y-%m') AS ym, SUM(r.final_amount) AS total, COUNT(*) AS cnt
             FROM reservations r
             WHERE r.status IN ('confirmed','active','completed') AND r.pickup_date BETWEEN ? AND ?
             GROUP BY ym ORDER BY ym"
        );
        $stmt->execute([$from, $to]);
        $rows = $stmt->fetchAll();

        $filename = 'relatorio-reservas-' . $from . '-' . $to . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if ($out === false) {
            return;
        }
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['mes', 'reservas', 'total_brl'], ';');
        foreach ($rows as $row) {
            fputcsv($out, [
                (string) $row['ym'],
                (string) $row['cnt'],
                number_format((float) $row['total'], 2, ',', ''),
            ], ';');
        }
        fclose($out);
    }
}
