<?php
define('DB_PATH', __DIR__ . '/leads.db');
define('ADMIN_SECRET', 'painel2025');

function getDB() {
    static $db = null;

    if ($db instanceof SQLite3) {
        return $db;
    }

    $db = new SQLite3(DB_PATH);
    $db->busyTimeout(3000);

    $db->exec(
        'CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT,
            empresa TEXT NOT NULL,
            whatsapp TEXT NOT NULL,
            instagram TEXT,
            faturamento TEXT NOT NULL,
            faturamento_valor INTEGER DEFAULT 0,
            desafio TEXT,
            status TEXT DEFAULT "novo",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME
        )'
    );

    ensureLeadsSchema($db);

    return $db;
}

function ensureLeadsSchema(SQLite3 $db) {
    if (!canWriteDatabase()) {
        return;
    }

    $columns = [];
    $result = $db->query('PRAGMA table_info(leads)');

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $columns[$row['name']] = true;
    }

    if (!isset($columns['nome'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN nome TEXT');
    }

    if (!isset($columns['desafio'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN desafio TEXT');
    }

    if (!isset($columns['status'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN status TEXT DEFAULT "novo"');
    }

    if (!isset($columns['updated_at'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN updated_at DATETIME');
    }

    if (!isset($columns['faturamento_valor'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN faturamento_valor INTEGER DEFAULT 0');
    }

    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_leads_status_created_at ON leads(status, created_at DESC)');
    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_leads_faturamento_valor ON leads(faturamento_valor DESC)');
}

function canWriteDatabase() {
    if (file_exists(DB_PATH)) {
        return is_writable(DB_PATH);
    }

    return is_writable(__DIR__);
}

function formatWhatsApp($number) {
    return preg_replace('/[^0-9]/', '', (string) $number);
}

function normalizeInstagram($instagram) {
    return ltrim(trim((string) $instagram), '@');
}

function getFaturamentoValor($faturamento) {
    $map = [
        'ate_10k' => 10000,
        '10k_20k' => 20000,
        '20k_50k' => 50000,
        '50k_100k' => 100000,
        'acima_100k' => 150000,
    ];

    return $map[$faturamento] ?? 0;
}

function getFaturamentoLabels() {
    return [
        'ate_10k' => 'Ate R$ 10k',
        '10k_20k' => 'R$ 10k - R$ 20k',
        '20k_50k' => 'R$ 20k - R$ 50k',
        '50k_100k' => 'R$ 50k - R$ 100k',
        'acima_100k' => 'Acima de R$ 100k',
    ];
}

function getDesafioLabels() {
    return [
        'atendimento_lento' => 'Demora no atendimento',
        'perda_vendas' => 'Perdendo vendas fora do horario',
        'agendamento' => 'Gestao de agendamentos',
        'qualificacao' => 'Leads desqualificados',
        'escalar' => 'Dificuldade para escalar',
        'outro' => 'Outro',
    ];
}

function isHotLead($faturamento) {
    return in_array($faturamento, ['20k_50k', '50k_100k', 'acima_100k'], true);
}

function startAdminSession() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('chamalead_admin');
        session_start();
    }
}

function ensureAdminCsrfToken() {
    startAdminSession();
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['admin_csrf_token'];
}

function verifyAdminCsrfToken($token) {
    startAdminSession();
    if (empty($_SESSION['admin_csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['admin_csrf_token'], (string) $token);
}
?>
