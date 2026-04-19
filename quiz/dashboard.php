<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/dashboard-data.php';

startAdminSession();

if (empty($_SESSION['admin_authenticated'])) {
    header('Location: /admin-login.php');
    exit;
}

$filters = quizDashboardFilters($_GET);
$data = quizDashboardData(getDB(), $filters);

?><!doctype html>
<html lang="pt-BR">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quiz Analytics</title><style>body{font-family:Arial,sans-serif;background:#0b0f19;color:#e8eefc;margin:0;padding:24px}.wrap{max-width:1240px;margin:0 auto}.grid{display:grid;gap:16px}.cards{grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}.card,.panel{background:#12192a;border:1px solid #23304d;border-radius:16px;padding:16px}.muted{color:#8ea0c6}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:10px;border-bottom:1px solid #23304d;text-align:left;font-size:14px}.bar{height:10px;background:#23304d;border-radius:999px;overflow:hidden}.bar > span{display:block;height:100%;background:linear-gradient(90deg,#7dd3fc,#60a5fa)}.insights{display:grid;gap:10px}.pill{display:inline-block;padding:6px 10px;border-radius:999px;background:#1b2740;color:#cbd7f5;margin-right:8px;margin-bottom:8px;font-size:12px}</style></head>
<body>
<div class="wrap grid">
    <h1>Quiz Analytics</h1>
    <div class="grid cards">
        <div class="card"><div class="muted">Sessões</div><strong><?= (int) ($data['totals']['total'] ?? 0) ?></strong></div>
        <div class="card"><div class="muted">Concluídos</div><strong><?= (int) ($data['totals']['completed'] ?? 0) ?></strong></div>
        <div class="card"><div class="muted">Nome</div><strong><?= (int) ($data['totals']['named'] ?? 0) ?></strong></div>
        <div class="card"><div class="muted">WhatsApp</div><strong><?= (int) ($data['totals']['whatsapp_valid'] ?? 0) ?></strong></div>
    </div>

    <div class="panel">
        <h2>Insights</h2>
        <div class="insights"><?php foreach ($data['insights'] as $insight) { ?><div><?= htmlspecialchars($insight, ENT_QUOTES, 'UTF-8') ?></div><?php } ?></div>
    </div>

    <div class="panel">
        <h2>Funil por etapa</h2>
        <table class="table">
            <thead><tr><th>Etapa</th><th>Atual</th><th>Base anterior</th><th>Conversão</th><th>Abandono</th></tr></thead>
            <tbody>
            <?php foreach ($data['rows'] as $row) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $row['count'] ?></td>
                    <td><?= (int) $row['previous'] ?></td>
                    <td>
                        <div class="bar"><span style="width:<?= max(0, min(100, (float) $row['rate'])) ?>%"></span></div>
                        <span class="muted"><?= htmlspecialchars((string) $row['rate'], ENT_QUOTES, 'UTF-8') ?>%</span>
                    </td>
                    <td><?= (int) $row['drop'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Marco-chave</h2>
        <div><?php foreach ($data['milestones'] as $milestone) { ?><span class="pill"><?= htmlspecialchars($milestone['label'], ENT_QUOTES, 'UTF-8') ?>: <?= (int) $milestone['count'] ?></span><?php } ?></div>
    </div>

    <div class="panel">
        <h2>Eventos de etapa</h2>
        <table class="table">
            <thead><tr><th>Passo</th><th>Ocorrências</th></tr></thead>
            <tbody><?php while ($row = $data['step_events']->fetchArray(SQLITE3_ASSOC)) { ?><tr><td><?= htmlspecialchars((string) $row['step_key'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int) $row['total'] ?></td></tr><?php } ?></tbody>
        </table>
    </div>
</div>
</body>
</html>
