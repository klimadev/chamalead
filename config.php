<?php
define('DB_PATH', __DIR__ . '/leads.db');
define('ADMIN_SECRET', 'painel2025'); // Altere para uma senha segura

function getDB() {
    $db = new SQLite3(DB_PATH);
    $db->exec('
        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa TEXT NOT NULL,
            whatsapp TEXT NOT NULL,
            instagram TEXT,
            faturamento TEXT NOT NULL,
            faturamento_valor INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            status TEXT DEFAULT "novo"
        )
    ');
    return $db;
}

function formatWhatsApp($number) {
    $clean = preg_replace('/[^0-9]/', '', $number);
    if (strlen($clean) === 11) {
        return $clean;
    }
    return $clean;
}

function getFaturamentoValor($faturamento) {
    $map = [
        'ate_10k' => 10000,
        '10k_20k' => 20000,
        '20k_50k' => 50000,
        '50k_100k' => 100000,
        'acima_100k' => 150000
    ];
    return $map[$faturamento] ?? 0;
}

function isHotLead($faturamento) {
    return in_array($faturamento, ['20k_50k', '50k_100k', 'acima_100k']);
}
?>