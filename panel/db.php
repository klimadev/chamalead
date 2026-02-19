<?php
/**
 * Database Connection Module
 *
 * Establishes SQLite database connection with proper error handling,
 * security settings, and automatic schema initialization.
 *
 * @package Panel
 * @author Chamalead
 * @version 2.1.0
 */

require_once 'Config.php';

// Database configuration using centralized Config
$db_path = Config::getString('DB_PATH', __DIR__ . '/data/panel.db');
$cache_path = Config::getString('CACHE_PATH', __DIR__ . '/data/cache');
$logs_path = Config::getString('LOG_PATH', __DIR__ . '/data/logs');

// Ensure data directory exists with proper permissions
$data_dir = dirname($db_path);
if (!is_dir($data_dir)) {
    if (!mkdir($data_dir, 0750, true)) {
        error_log("[Panel] Failed to create data directory: {$data_dir}");
        die("Erro interno do sistema. Contate o administrador.");
    }
}

// Ensure cache directory exists
if (!is_dir($cache_path)) {
    mkdir($cache_path, 0750, true);
}

// Ensure logs directory exists
if (!is_dir($logs_path)) {
    mkdir($logs_path, 0750, true);
}

// Ensure backups directory exists
$backups_path = $data_dir . '/backups';
if (!is_dir($backups_path)) {
    mkdir($backups_path, 0750, true);
}

try {
    // Create PDO connection with security settings
    $pdo = new PDO("sqlite:{$db_path}");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Set busy timeout to prevent locking issues
    $pdo->exec('PRAGMA busy_timeout = 5000');
    
    // Create users table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create index on username for faster lookups
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)");
    
} catch (PDOException $e) {
    // Log detailed error internally
    error_log("[Panel] Database connection failed: " . $e->getMessage());
    
    // Show generic message to user
    die("Erro interno do sistema. Contate o administrador.");
}
