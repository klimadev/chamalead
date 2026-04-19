<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

function quizDashboardFilters(array $get): array
{
    return [
        'from' => trim((string) ($get['from'] ?? date('Y-m-01'))),
        'to' => trim((string) ($get['to'] ?? date('Y-m-d'))),
        'utm_source' => trim((string) ($get['utm_source'] ?? '')),
        'utm_campaign' => trim((string) ($get['utm_campaign'] ?? '')),
    ];
}

function quizDashboardWhere(array $filters): array
{
    $where = ['date(created_at) BETWEEN :from AND :to'];
    $params = [':from' => $filters['from'], ':to' => $filters['to']];

    if ($filters['utm_source'] !== '') {
        $where[] = 'utm_source = :utm_source';
        $params[':utm_source'] = $filters['utm_source'];
    }

    if ($filters['utm_campaign'] !== '') {
        $where[] = 'utm_campaign = :utm_campaign';
        $params[':utm_campaign'] = $filters['utm_campaign'];
    }

    return [$where, $params];
}

function quizDashboardData(SQLite3 $db, array $filters): array
{
    [$where, $params] = quizDashboardWhere($filters);
    $whereSql = implode(' AND ', $where);

    $query = function (string $sql) use ($db, $params): SQLite3Result {
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, SQLITE3_TEXT);
        }

        return $stmt->execute();
    };

    $totals = $query("SELECT COUNT(*) AS total, SUM(status = 'completed') AS completed, SUM(nome IS NOT NULL AND nome != '') AS named, SUM(whatsapp IS NOT NULL AND whatsapp != '') AS whatsapp_valid FROM quiz_leads WHERE {$whereSql}")->fetchArray(SQLITE3_ASSOC) ?: [];
    $stepTotals = $query("SELECT current_step, COUNT(*) AS total FROM quiz_leads WHERE {$whereSql} GROUP BY current_step ORDER BY current_step ASC");
    $stepEvents = $query("SELECT step_key, COUNT(*) AS total FROM quiz_events WHERE {$whereSql} AND event_type IN ('step_view', 'step_completed', 'quiz_completed') GROUP BY step_key ORDER BY total DESC");

    $stepLabels = [
        0 => 'Entrada',
        1 => 'Nome',
        2 => 'WhatsApp',
        3 => 'Cargo',
        4 => 'Faturamento',
        5 => 'Canal',
        6 => 'Volume',
        7 => 'Dor',
        8 => 'Aprofundamento',
        9 => 'Resultado',
        10 => 'Finalizado',
    ];

    $rows = [];
    $currentStepRows = [];
    while ($row = $stepTotals->fetchArray(SQLITE3_ASSOC)) {
        $currentStepRows[] = $row;
    }

    $leadByStep = [];
    foreach ($currentStepRows as $row) {
        $step = (int) $row['current_step'];
        $leadByStep[$step] = (int) $row['total'];
    }

    $totalLeads = (int) ($totals['total'] ?? 0);
    $completed = (int) ($totals['completed'] ?? 0);
    $named = (int) ($totals['named'] ?? 0);
    $whatsappValid = (int) ($totals['whatsapp_valid'] ?? 0);

    $milestones = [
        ['step' => 1, 'label' => 'Nome', 'count' => $named],
        ['step' => 2, 'label' => 'WhatsApp', 'count' => $whatsappValid],
        ['step' => 10, 'label' => 'Finalização', 'count' => $completed],
    ];

    $rows = [];
    foreach ($stepLabels as $step => $label) {
        $count = $leadByStep[$step] ?? 0;
        $prevCount = $step === 0 ? $totalLeads : ($leadByStep[$step - 1] ?? $totalLeads);
        $drop = $prevCount > 0 ? max(0, $prevCount - $count) : 0;
        $rate = $prevCount > 0 ? round(($count / $prevCount) * 100, 1) : 0.0;
        $rows[] = [
            'step' => $step,
            'label' => $label,
            'count' => $count,
            'previous' => $prevCount,
            'drop' => $drop,
            'rate' => $rate,
        ];
    }

    $insights = [];
    if ($totalLeads > 0) {
        $completionRate = round(($completed / $totalLeads) * 100, 1);
        $whatsappRate = round(($whatsappValid / $totalLeads) * 100, 1);
        $nameRate = round(($named / $totalLeads) * 100, 1);

        $insights[] = 'Conclusão geral: ' . $completionRate . '% (' . $completed . '/' . $totalLeads . ').';
        $insights[] = 'Nome capturado em ' . $nameRate . '% da base.';
        $insights[] = 'WhatsApp capturado em ' . $whatsappRate . '% da base.';
    }

    return [
        'totals' => $totals,
        'rows' => $rows,
        'milestones' => $milestones,
        'insights' => $insights,
        'step_events' => $stepEvents,
    ];
}
