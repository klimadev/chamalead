<?php
/**
 * Authentication Module
 *
 * Handles user authentication, session management, CSRF protection,
 * rate limiting, and audit logging for the Panel system.
 *
 * @package Panel
 * @author Chamalead
 * @version 2.0.0
 */

session_start();
require_once 'db.php';
require_once 'Logger.php';
require_once 'Config.php';

Config::load();

/**
 * Handle errors with logging
 *
 * @param string $user_message Message to display to user
 * @param string $internal_details Internal error details for logging
 * @return string User-facing error message
 */
function handle_error(string $user_message, string $internal_details = ''): string {
    if (!empty($internal_details)) {
        error_log("[Panel Error] " . $internal_details);
        Logger::error($internal_details);
    }
    return $user_message;
}

/**
 * Generate CSRF token if not exists
 *
 * @return string CSRF token
 */
function generate_csrf_token(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 *
 * @param string $token Token to validate
 * @return bool True if valid
 */
function validate_csrf_token(string $token): bool {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    // Token expires after 1 hour
    if (time() - ($_SESSION['csrf_token_time'] ?? 0) > 3600) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token field for forms
 *
 * @return string HTML input field
 */
function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Get CSRF token for AJAX
 *
 * @return string CSRF token
 */
function csrf_token(): string {
    return generate_csrf_token();
}

/**
 * Sanitize input data
 *
 * @param string $data Raw input
 * @return string Sanitized data
 */
function sanitize_input(string $data): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitize string for general use
 *
 * @param string $data Raw input
 * @return string Sanitized string
 */
function sanitize_string(string $data): string {
    return filter_var(sanitize_input($data), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Sanitize email address
 *
 * @param string $data Raw email
 * @return string Sanitized email
 */
function sanitize_email(string $data): string {
    return filter_var(sanitize_input($data), FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize alphanumeric string
 *
 * @param string $data Raw input
 * @return string Sanitized alphanumeric string
 */
function sanitize_alphanumeric(string $data): string {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $data);
}

/**
 * Check rate limit for login attempts
 * TEMPORARILY DISABLED - Always returns true
 *
 * @param string $ip_address IP address to check
 * @return bool True if within limit
 */
function check_rate_limit(string $ip_address): bool {
    // RATE LIMITING TEMPORARILY DISABLED
    // Uncomment the code below to re-enable
    return true;

    /* ORIGINAL CODE:
    global $pdo;

    // Clean old attempts (older than 15 minutes)
    try {
        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE attempted_at < datetime('now', '-15 minutes')");
        $stmt->execute();

        // Count recent attempts from this IP
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM login_attempts WHERE ip_address = ? AND attempted_at > datetime('now', '-15 minutes')");
        $stmt->execute([$ip_address]);
        $result = $stmt->fetch();

        // Limit: 5 attempts per 15 minutes
        $allowed = ($result['count'] ?? 0) < 5;

        if (!$allowed) {
            Logger::warning('Rate limit exceeded', ['ip' => $ip_address]);
        }

        return $allowed;
    } catch (PDOException $e) {
        Logger::error('Rate limit check failed: ' . $e->getMessage());
        return true; // Allow on error to prevent lockouts
    }
    */
}

/**
 * Record login attempt
 *
 * @param string $ip_address Client IP
 * @param string $username Attempted username
 * @param bool $success Whether login succeeded
 */
function record_login_attempt(string $ip_address, string $username, bool $success): void {
    global $pdo;

    try {
        // Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            username TEXT,
            success INTEGER DEFAULT 0,
            attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, username, success) VALUES (?, ?, ?)");
        $stmt->execute([$ip_address, $username, $success ? 1 : 0]);

        Logger::info('Login attempt recorded', [
            'ip' => $ip_address,
            'username' => $username,
            'success' => $success
        ]);
    } catch (PDOException $e) {
        Logger::error('Failed to record login attempt: ' . $e->getMessage());
    }
}

/**
 * Register new user
 *
 * @param string $username Username
 * @param string $password Plain text password
 * @return bool True on success
 */
function register(string $username, string $password): bool {
    Logger::warning('Account registration attempt blocked', ['username' => $username]);
    return false;
}

/**
 * Authenticate user
 *
 * @param string $username Username
 * @param string $password Plain text password
 * @return bool True on successful authentication
 */
function login(string $username, string $password): bool {
    $adminUser = Config::getString('PANEL_ADMIN_USER', 'admin');
    $adminPassword = Config::getString('PANEL_ADMIN_PASSWORD', '');

    if ($adminPassword === '') {
        Logger::error('PANEL_ADMIN_PASSWORD is not configured');
        return false;
    }

    $isValidUser = hash_equals($adminUser, $username);
    $isValidPassword = hash_equals($adminPassword, $password);

    if ($isValidUser && $isValidPassword) {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $adminUser;
        $_SESSION['last_activity'] = time();

        log_action('login', "Admin logged in: {$adminUser}");
        Logger::info('Admin logged in', ['username' => $adminUser, 'user_id' => 1]);

        session_regenerate_id(true);
        return true;
    }

    Logger::warning('Failed admin login attempt', ['username' => $username]);
    return false;
}

/**
 * Check if user is authenticated
 *
 * @return bool True if authenticated
 */
function is_authenticated(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect if not authenticated
 * Also handles session timeout
 */
function redirect_if_not_auth(): void {
    if (!is_authenticated()) {
        header("Location: login.php");
        exit;
    }

    // Check session timeout (30 minutes of inactivity)
    $timeout = Config::getInt('SESSION_TIMEOUT', 1800);
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        Logger::info('Session timeout', ['username' => $_SESSION['username'] ?? 'unknown']);
        logout();
        header("Location: login.php?timeout=1");
        exit;
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
}

/**
 * Log out user and destroy session
 */
function logout(): void {
    if (isset($_SESSION['user_id'])) {
        Logger::info('User logged out', ['username' => $_SESSION['username'] ?? 'unknown']);
        log_action('logout', 'User logged out');
    }

    // Clear all session data
    $_SESSION = [];

    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }

    // Destroy session
    session_destroy();
}

/**
 * Log action to audit log
 *
 * @param string $action Action type
 * @param string $details Action details
 */
function log_action(string $action, string $details = ''): void {
    global $pdo;

    try {
        // Create audit_logs table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            username TEXT,
            action TEXT NOT NULL,
            details TEXT,
            ip_address TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $user_id = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'anonymous';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, username, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $username, $action, $details, $ip_address, $user_agent]);
    } catch (PDOException $e) {
        Logger::error('Failed to write audit log: ' . $e->getMessage());
    }
}

/**
 * Get recent audit logs
 *
 * @param int $limit Number of records to retrieve
 * @param int $offset Offset for pagination
 * @return array Array of log entries
 */
function get_audit_logs(int $limit = 50, int $offset = 0): array {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        Logger::error('Failed to retrieve audit logs: ' . $e->getMessage());
        return [];
    }
}

/**
 * Create database backup
 *
 * @return string|false Path to backup file or false on failure
 */
function backup_database() {
    $db_path = __DIR__ . '/data/panel.db';
    $backup_dir = __DIR__ . '/data/backups';

    // Create backup directory if not exists
    if (!is_dir($backup_dir)) {
        if (!mkdir($backup_dir, 0750, true)) {
            Logger::error('Failed to create backup directory');
            return false;
        }
    }

    // Generate backup filename with timestamp
    $backup_file = $backup_dir . '/panel_' . date('Y-m-d_H-i-s') . '.db';

    // Copy database file
    if (copy($db_path, $backup_file)) {
        // Keep only last 10 backups
        $backups = glob($backup_dir . '/panel_*.db');
        if (count($backups) > 10) {
            // Sort by modification time
            usort($backups, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // Delete oldest backups
            $to_delete = array_slice($backups, 0, count($backups) - 10);
            foreach ($to_delete as $file) {
                unlink($file);
            }
        }

        log_action('database_backup', "Backup created: {$backup_file}");
        Logger::info('Database backup created', ['file' => $backup_file]);
        return $backup_file;
    }

    Logger::error('Database backup failed');
    return false;
}

/**
 * Auto backup if needed (every 24 hours)
 *
 * @return string|null Path to backup file or null if not needed
 */
function auto_backup_if_needed(): ?string {
    $backup_dir = __DIR__ . '/data/backups';
    $last_backup_file = $backup_dir . '/.last_backup';

    // Check if backup is needed (every 24 hours)
    $interval = Config::getInt('BACKUP_INTERVAL', 86400);

    if (!file_exists($last_backup_file) || (time() - filemtime($last_backup_file)) > $interval) {
        $backup = backup_database();
        if ($backup) {
            touch($last_backup_file);
        }
        return $backup;
    }

    return null;
}
