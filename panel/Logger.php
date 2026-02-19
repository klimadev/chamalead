<?php

/**
 * Logger Class
 *
 * Provides structured logging with multiple levels and rotation.
 *
 * @package Panel
 * @author Chamalead
 * @version 1.1.0
 */

require_once 'Config.php';

class Logger
{
    private static ?self $instance = null;
    private string $logPath;
    private string $level;
    private array $levels = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
        'critical' => 4
    ];

    private function __construct()
    {
        Config::load();
        $this->logPath = Config::getString('LOG_PATH', __DIR__ . '/data/logs');
        $this->level = Config::getString('LOG_LEVEL', 'info');

        $this->ensureLogDirectory();
    }

    /**
     * Get singleton instance
     * 
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ensure log directory exists
     */
    private function ensureLogDirectory(): void
    {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0750, true);
        }
    }

    /**
     * Get log file path for today
     * 
     * @return string
     */
    private function getLogFile(): string
    {
        return $this->logPath . '/panel_' . date('Y-m-d') . '.log';
    }

    /**
     * Write log entry
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context
     */
    private function write(string $level, string $message, array $context = []): void
    {
        // Check if this level should be logged
        if ($this->levels[$level] < $this->levels[$this->level]) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user = $_SESSION['username'] ?? 'anonymous';
        
        $entry = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'user' => $user,
            'ip' => $ip,
            'message' => $message,
            'context' => $context
        ];

        $logLine = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        
        file_put_contents($this->getLogFile(), $logLine, FILE_APPEND | LOCK_EX);
        
        // Rotate logs if file is too large (> 10MB)
        $this->rotateIfNeeded();
    }

    /**
     * Rotate log file if it exceeds 10MB
     */
    private function rotateIfNeeded(): void
    {
        $file = $this->getLogFile();
        if (file_exists($file) && filesize($file) > 10 * 1024 * 1024) {
            rename($file, $file . '.old');
        }
    }

    /**
     * Log debug message
     * 
     * @param string $message
     * @param array $context
     */
    public static function debug(string $message, array $context = []): void
    {
        self::getInstance()->write('debug', $message, $context);
    }

    /**
     * Log info message
     * 
     * @param string $message
     * @param array $context
     */
    public static function info(string $message, array $context = []): void
    {
        self::getInstance()->write('info', $message, $context);
    }

    /**
     * Log warning message
     * 
     * @param string $message
     * @param array $context
     */
    public static function warning(string $message, array $context = []): void
    {
        self::getInstance()->write('warning', $message, $context);
    }

    /**
     * Log error message
     * 
     * @param string $message
     * @param array $context
     */
    public static function error(string $message, array $context = []): void
    {
        self::getInstance()->write('error', $message, $context);
    }

    /**
     * Log critical message
     * 
     * @param string $message
     * @param array $context
     */
    public static function critical(string $message, array $context = []): void
    {
        self::getInstance()->write('critical', $message, $context);
    }

    /**
     * Get recent log entries
     * 
     * @param int $lines Number of lines to retrieve
     * @param string|null $level Filter by level
     * @return array
     */
    public static function getRecent(int $lines = 100, ?string $level = null): array
    {
        $instance = self::getInstance();
        $file = $instance->getLogFile();
        
        if (!file_exists($file)) {
            return [];
        }

        $entries = [];
        $handle = fopen($file, 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false && count($entries) < $lines) {
                $entry = json_decode(trim($line), true);
                if ($entry && ($level === null || $entry['level'] === strtoupper($level))) {
                    $entries[] = $entry;
                }
            }
            fclose($handle);
        }

        return array_reverse($entries);
    }
}
