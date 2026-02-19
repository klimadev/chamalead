<?php

/**
 * Centralized Configuration Manager Class
 *
 * Implements singleton pattern with support for nested keys, type casting,
 * and secure value handling. Loads .env file with proper security.
 *
 * @package Panel
 * @author Chamalead
 * @version 2.0.0
 */
class Config
{
    private static ?array $config = null;
    private static string $envFilePath;

    /**
     * Sensitive keys that should not be logged or exposed
     */
    private const SENSITIVE_KEYS = [
        'password',
        'secret',
        'key',
        'token',
        'api_key',
        'apikey',
        'auth',
        'credential',
        'private',
        'pass',
        'hmac_key'
    ];

    /**
     * Load configuration from .env file
     *
     * @param string|null $envPath Path to .env file (optional)
     */
    public static function load(?string $envPath = null): void
    {
        if (self::$config !== null) {
            return;
        }

        self::$envFilePath = $envPath ?? __DIR__ . '/.env';
        self::$config = [];

        // Define default values
        self::$config = [
            'EVOLUTION_API_URL' => 'http://localhost:8080',
            'EVOLUTION_API_KEY' => '',
            'PANEL_ADMIN_USER' => 'admin',
            'PANEL_ADMIN_PASSWORD' => 'ChangeMeNow123',
            'API_TIMEOUT' => '10',
            'PAIRING_CODE_TTL_SECONDS' => '45',
            'DEEP_LINK_DEFAULT_TTL_SECONDS' => '604800',
            'CACHE_ENABLED' => 'true',
            'CACHE_PATH' => __DIR__ . '/data/cache',
            'CACHE_TTL' => '300',
            'LOG_PATH' => __DIR__ . '/data/logs',
            'LOG_LEVEL' => 'info',
            'SESSION_TIMEOUT' => '1800',
            'BACKUP_INTERVAL' => '86400',
            'PANEL_PUBLIC_BASE_URL' => '',
            'DEEP_LINK_SECRET' => '',
            'CACHE_HMAC_KEY' => self::generateHmacKey(),
            'APP_ENV' => 'production',
        ];

        // Load .env file if it exists
        if (file_exists(self::$envFilePath) && is_readable(self::$envFilePath)) {
            self::parseEnvFile();
        }

        // Also check for environment variables (useful for Docker)
        foreach (self::$config as $key => $defaultValue) {
            $envValue = getenv($key);
            if ($envValue !== false) {
                self::$config[$key] = $envValue;
            }
        }
    }

    /**
     * Parse .env file manually (more secure than parse_ini_file)
     */
    private static function parseEnvFile(): void
    {
        $lines = file(self::$envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Skip comments and empty lines
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }

                // Only set if key is in our allowed list
                if (array_key_exists($key, self::$config)) {
                    self::$config[$key] = $value;
                }
            }
        }
    }

    /**
     * Generate a secure HMAC key if not provided
     *
     * @return string
     */
    private static function generateHmacKey(): string
    {
        $keyFile = __DIR__ . '/data/.hmac_key';
        
        // Check if key already exists
        if (file_exists($keyFile)) {
            $key = file_get_contents($keyFile);
            if ($key !== false && strlen($key) >= 32) {
                return trim($key);
            }
        }

        // Generate new key
        $key = bin2hex(random_bytes(32));
        
        // Store in secure location
        $keyDir = dirname($keyFile);
        if (!is_dir($keyDir)) {
            mkdir($keyDir, 0750, true);
        }
        
        file_put_contents($keyFile, $key, LOCK_EX);
        chmod($keyFile, 0600); // Only owner can read/write

        return $key;
    }

    /**
     * Get configuration value with support for nested keys
     *
     * Examples:
     * - Config::get('database.host') - returns nested value
     * - Config::get('EVOLUTION_API_KEY') - returns simple value
     * - Config::get('nonexistent', 'default') - returns default
     *
     * @param string $key Configuration key (supports dot notation)
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public static function get(string $key, $default = null)
    {
        if (self::$config === null) {
            self::load();
        }

        // Handle nested keys with dot notation
        if (strpos($key, '.') !== false) {
            return self::getNested($key, $default);
        }

        return self::$config[$key] ?? $default;
    }

    /**
     * Check if configuration key exists
     *
     * @param string $key Configuration key (supports dot notation)
     * @return bool True if key exists
     */
    public static function has(string $key): bool
    {
        if (self::$config === null) {
            self::load();
        }

        if (strpos($key, '.') !== false) {
            return self::getNested($key, null) !== null;
        }

        return isset(self::$config[$key]) && self::$config[$key] !== '';
    }

    /**
     * Get nested value using dot notation
     *
     * @param string $key Dot-notation key (e.g., 'database.host')
     * @param mixed $default Default value
     * @return mixed
     */
    private static function getNested(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Get all configuration values (excluding sensitive data)
     *
     * @param bool $includeSensitive Whether to include sensitive keys
     * @return array
     */
    public static function all(bool $includeSensitive = false): array
    {
        if (self::$config === null) {
            self::load();
        }

        if ($includeSensitive) {
            return self::$config;
        }

        return array_filter(
            self::$config,
            function ($key) {
                return !self::isSensitiveKey($key);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Set configuration value (runtime only, not persisted)
     *
     * @param string $key Configuration key
     * @param mixed $value Value to set
     * @return void
     */
    public static function set(string $key, $value): void
    {
        if (self::$config === null) {
            self::load();
        }

        self::$config[$key] = $value;
    }

    /**
     * Check if a key contains sensitive information
     *
     * @param string $key Key to check
     * @return bool True if key is sensitive
     */
    private static function isSensitiveKey(string $key): bool
    {
        $keyLower = strtolower($key);

        foreach (self::SENSITIVE_KEYS as $sensitive) {
            if (strpos($keyLower, $sensitive) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get safe representation of value for logging
     *
     * @param string $key Key to get
     * @return string Safe string representation
     */
    public static function getSafeValue(string $key): string
    {
        if (!self::has($key)) {
            return '[NOT SET]';
        }

        if (self::isSensitiveKey($key)) {
            $value = self::get($key);
            if (empty($value)) {
                return '[EMPTY]';
            }
            return '[REDACTED]';
        }

        return (string) self::get($key);
    }

    /**
     * Get configuration value as string
     *
     * @param string $key Configuration key
     * @param string $default Default value
     * @return string Configuration value
     */
    public static function getString(string $key, string $default = ''): string
    {
        $value = self::get($key, $default);
        return (string) $value;
    }

    /**
     * Get configuration value as integer
     *
     * @param string $key Configuration key
     * @param int $default Default value
     * @return int Configuration value
     */
    public static function getInt(string $key, int $default = 0): int
    {
        $value = self::get($key, $default);
        return intval($value);
    }

    /**
     * Get configuration value as boolean
     *
     * @param string $key Configuration key
     * @param bool $default Default value
     * @return bool Configuration value
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get HMAC key for cache validation
     *
     * @return string
     */
    public static function getHmacKey(): string
    {
        return self::getString('CACHE_HMAC_KEY');
    }

    /**
     * Check if running in production
     *
     * @return bool
     */
    public static function isProduction(): bool
    {
        return self::getString('APP_ENV') === 'production';
    }
}
