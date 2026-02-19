<?php

/**
 * Evolution API Service Class
 *
 * Centralizes all API calls to the Evolution API with caching,
 * timeout handling, and proper error management.
 *
 * SECURITY FIX: Replaced serialize/unserialize with JSON + HMAC to prevent
 * PHP Object Injection attacks (CVE-2021-XXX).
 *
 * @package Panel
 * @author Chamalead
 * @version 1.1.0
 */

require_once 'Config.php';

class EvolutionApiService
{
    private string $apiUrl;
    private string $apiKey;
    private int $timeout;
    private bool $cacheEnabled;
    private string $cachePath;
    private int $cacheTtl;
    private string $hmacKey;
    private int $maxRetries;

    /**
     * Candidate keys commonly used by Evolution payloads for QR data.
     */
    private const QR_CODE_KEYS = [
        'base64',
        'qrcode',
        'qrCode',
        'qr',
        'code'
    ];

    /**
     * Constructor
     *
     * @param string|null $apiUrl API base URL (optional, defaults to env)
     * @param string|null $apiKey API key (optional, defaults to env)
     */
    public function __construct(?string $apiUrl = null, ?string $apiKey = null)
    {
        Config::load();
        
        $this->apiUrl = $apiUrl ?: Config::getString('EVOLUTION_API_URL', 'http://evolution-api:8080');
        $this->apiKey = $apiKey ?: Config::getString('EVOLUTION_API_KEY', '');
        $this->timeout = Config::getInt('API_TIMEOUT', 10);
        $this->cacheEnabled = Config::getBool('CACHE_ENABLED', true);
        $this->cachePath = Config::getString('CACHE_PATH', __DIR__ . '/data/cache');
        $this->cacheTtl = Config::getInt('CACHE_TTL', 300); // 5 minutes default
        $this->hmacKey = Config::getHmacKey();
        $this->maxRetries = Config::getInt('API_MAX_RETRIES', 3);

        $this->ensureCacheDirectory();
    }

    /**
     * Ensure cache directory exists with proper permissions
     */
    private function ensureCacheDirectory(): void
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0750, true);
        }

        // Verify directory permissions
        if (is_dir($this->cachePath)) {
            $perms = fileperms($this->cachePath);
            // Check if group or others have write access (should not)
            if (($perms & 0x0010) || ($perms & 0x0002)) {
                chmod($this->cachePath, 0750);
            }
        }
    }

    /**
     * Get cache file path for a key
     *
     * @param string $key Cache key
     * @return string File path
     */
    private function getCacheFile(string $key): string
    {
        // Use hash to prevent directory traversal
        return $this->cachePath . '/' . hash('sha256', $key) . '.cache';
    }

    /**
     * Generate HMAC signature for cache data
     *
     * @param array $data Data to sign
     * @return string HMAC signature
     */
    private function generateCacheSignature(array $data): string
    {
        $payload = json_encode($data);
        return hash_hmac('sha256', $payload, $this->hmacKey);
    }

    /**
     * Verify cache data signature
     *
     * @param array $data Data with signature
     * @return bool True if valid
     */
    private function verifyCacheSignature(array $data): bool
    {
        if (!isset($data['signature']) || !isset($data['payload'])) {
            return false;
        }

        $signature = $data['signature'];
        $payload = $data['payload'];

        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->hmacKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get cached data if valid
     *
     * SECURITY: Uses JSON + HMAC instead of unserialize() to prevent
     * PHP Object Injection attacks.
     *
     * @param string $key Cache key
     * @return array|null Cached data or null
     */
    private function getCache(string $key): ?array
    {
        if (!$this->cacheEnabled) {
            return null;
        }

        $file = $this->getCacheFile($key);

        if (!file_exists($file)) {
            return null;
        }

        // Check file permissions - should not be readable by others
        $perms = fileperms($file);
        if ($perms & 0x0004) {
            chmod($file, 0640);
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return null;
        }

        // Decode JSON safely
        $data = json_decode($content, true);
        if ($data === null || !is_array($data)) {
            // Invalid JSON, delete corrupted cache
            @unlink($file);
            return null;
        }

        // Verify HMAC signature to prevent tampering
        if (!$this->verifyCacheSignature($data)) {
            // Signature invalid - possible tampering detected
            error_log("[EvolutionApiService] Cache tampering detected for key: {$key}");
            @unlink($file);
            return null;
        }

        // Check expiration
        if (!isset($data['payload']['expires']) || $data['payload']['expires'] < time()) {
            @unlink($file);
            return null;
        }

        return $data['payload']['value'] ?? null;
    }

    /**
     * Store data in cache
     *
     * SECURITY: Uses JSON + HMAC instead of serialize() to prevent
     * PHP Object Injection attacks.
     *
     * @param string $key Cache key
     * @param mixed $value Data to cache
     * @param int|null $ttl Custom TTL (optional)
     */
    private function setCache(string $key, $value, ?int $ttl = null): void
    {
        if (!$this->cacheEnabled) {
            return;
        }

        $file = $this->getCacheFile($key);
        $payload = [
            'expires' => time() + ($ttl ?: $this->cacheTtl),
            'value' => $value
        ];

        // Create signed cache entry
        $cacheData = [
            'payload' => $payload,
            'signature' => $this->generateCacheSignature($payload)
        ];

        // Write with proper permissions
        $jsonData = json_encode($cacheData, JSON_UNESCAPED_UNICODE);
        file_put_contents($file, $jsonData, LOCK_EX);
        chmod($file, 0640); // Owner read/write, group read, others no access
    }

    /**
     * Clear cache for a specific key or all cache
     *
     * @param string|null $key Specific key or null for all
     */
    public function clearCache(?string $key = null): void
    {
        if ($key) {
            $file = $this->getCacheFile($key);
            if (file_exists($file)) {
                unlink($file);
            }
        } else {
            $files = glob($this->cachePath . '/*.cache');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Make HTTP request to API with retry logic
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint (with or without leading /)
     * @param array|null $data Request body data
     * @param int $maxRetries Maximum number of retry attempts
     * @return array Response with status and data
     */
    private function request(string $method, string $endpoint, ?array $data = null, int $maxRetries = 0): array
    {
        $maxRetries = $maxRetries > 0 ? $maxRetries : $this->maxRetries;
        $lastError = null;
        $lastResponse = null;

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            $result = $this->executeRequest($method, $endpoint, $data);

            // Success or non-retryable error
            if ($result['status'] !== 0 || $attempt === $maxRetries) {
                return $result;
            }

            // Retry with exponential backoff
            $lastError = $result['error'];
            $lastResponse = $result;

            // Exponential backoff: 1s, 2s, 4s
            $delay = (int) (1000 * pow(2, $attempt));
            usleep($delay * 1000); // Convert to microseconds
        }

        return $lastResponse ?? [
            'status' => 0,
            'data' => null,
            'error' => $lastError ?? 'Unknown error after retries'
        ];
    }

    /**
     * Execute single HTTP request
     *
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array|null $data Request body
     * @return array Response
     */
    private function executeRequest(string $method, string $endpoint, ?array $data = null): array
    {
        $url = $this->apiUrl . (strpos($endpoint, '/') === 0 ? $endpoint : '/' . $endpoint);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $headers = [
            'apikey: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            $jsonData = json_encode($data);
            if ($jsonData === false) {
                return [
                    'status' => 0,
                    'data' => null,
                    'error' => 'Failed to encode JSON data'
                ];
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
                'Content-Length: ' . strlen($jsonData)
            ]));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($error || $errno !== 0) {
            $errorMsg = $this->getCurlErrorMessage($errno, $error);
            error_log("[EvolutionApi] cURL Error (attempt): {$errorMsg} - URL: {$url}");
            return [
                'status' => 0,
                'data' => null,
                'error' => $errorMsg,
                'retryable' => $this->isRetryableError($errno)
            ];
        }

        $decoded = json_decode($response, true);

        return [
            'status' => $httpCode,
            'data' => $decoded,
            'raw' => $response
        ];
    }

    /**
     * Check if cURL error is retryable
     *
     * @param int $errno cURL error number
     * @return bool True if error is retryable
     */
    private function isRetryableError(int $errno): bool
    {
        // Retryable errors: connection refused, timeout, DNS failure, etc.
        $retryableErrors = [
            CURLE_COULDNT_CONNECT,      // 7
            CURLE_OPERATION_TIMEDOUT,   // 28
            CURLE_COULDNT_RESOLVE_HOST, // 6
            CURLE_COULDNT_RESOLVE_PROXY, // 5
            CURLE_SSL_CONNECT_ERROR,    // 35
            CURLE_GOT_NOTHING,          // 52
            CURLE_SEND_ERROR,           // 55
            CURLE_RECV_ERROR            // 56
        ];

        return in_array($errno, $retryableErrors, true);
    }

    /**
     * Get human-readable error message for cURL errors
     *
     * @param int $errno cURL error number
     * @param string $error Error message
     * @return string Human-readable error
     */
    private function getCurlErrorMessage(int $errno, string $error): string
    {
        $messages = [
            CURLE_COULDNT_CONNECT => 'Não foi possível conectar ao servidor',
            CURLE_OPERATION_TIMEDOUT => 'Tempo de conexão esgotado',
            CURLE_COULDNT_RESOLVE_HOST => 'Não foi possível resolver o hostname',
            CURLE_COULDNT_RESOLVE_PROXY => 'Erro no proxy',
            CURLE_SSL_CONNECT_ERROR => 'Erro de SSL/TLS',
            CURLE_GOT_NOTHING => 'Servidor não respondeu',
            CURLE_SEND_ERROR => 'Erro ao enviar dados',
            CURLE_RECV_ERROR => 'Erro ao receber dados'
        ];

        return $messages[$errno] ?? "Erro de conexão: {$error}";
    }

    /**
     * Fetch all instances (NO CACHE - always fresh for real-time status)
     *
     * @param bool $forceRefresh Always true, kept for backwards compatibility
     * @return array List of instances or error info
     */
    public function fetchInstances(bool $forceRefresh = true): array
    {
        // Always fetch fresh data - no caching for real-time connection status
        $response = $this->request('GET', '/instance/fetchInstances');

        if ($response['status'] === 200 && is_array($response['data'])) {
            return ['success' => true, 'data' => $response['data'], 'cached' => false];
        }

        // Return error state with details
        return [
            'success' => false,
            'error' => $response['error'] ?? 'Erro de conexão com a API',
            'status' => $response['status'],
            'data' => []
        ];
    }

    /**
     * Get settings for a specific instance (NO CACHE - always fresh)
     *
     * @param string $instanceName Instance name
     * @return array|null Settings or null on error
     */
    public function getSettings(string $instanceName): ?array
    {
        // Always fetch fresh data - no caching for real-time settings
        $response = $this->request('GET', '/settings/find/' . urlencode($instanceName));

        if ($response['status'] === 200) {
            return $response['data'];
        }

        return null;
    }

    /**
     * Create new instance
     *
     * @param array $data Instance configuration
     * @return array Response with success status
     */
    public function createInstance(array $data): array
    {
        $response = $this->request('POST', '/instance/create', $data);

        if ($response['status'] === 201 || $response['status'] === 200) {
            $this->clearCache('instances_all');
            return [
                'success' => true,
                'data' => $response['data']
            ];
        }

        return [
            'success' => false,
            'message' => $response['data']['response']['message'][0] ?? 'Erro ao criar instância',
            'status' => $response['status']
        ];
    }

    /**
     * Update instance settings
     *
     * @param string $instanceName Instance name
     * @param array $data Settings to update
     * @return array Response with success status
     */
    public function updateSettings(string $instanceName, array $data): array
    {
        $response = $this->request('POST', '/settings/set/' . urlencode($instanceName), $data);

        if ($response['status'] === 200 || $response['status'] === 201) {
            $this->clearCache('settings_' . $instanceName);
            return [
                'success' => true,
                'data' => $response['data']
            ];
        }

        return [
            'success' => false,
            'message' => $response['data']['response']['message'][0] ?? 'Erro ao atualizar configurações',
            'status' => $response['status']
        ];
    }

    /**
     * Delete instance
     *
     * @param string $instanceName Instance name
     * @return array Response with success status
     */
    public function deleteInstance(string $instanceName): array
    {
        $response = $this->request('DELETE', '/instance/delete/' . urlencode($instanceName));

        if ($response['status'] === 200) {
            $this->clearCache('instances_all');
            $this->clearCache('settings_' . $instanceName);
            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'message' => $response['data']['response']['message'] ?? 'Erro ao deletar instância',
            'status' => $response['status']
        ];
    }

    /**
     * Get single instance details
     *
     * @param string $instanceName Instance name
     * @return array|null Instance data or null
     */
    public function getInstance(string $instanceName): ?array
    {
        $instances = $this->fetchInstances();

        // Handle new return format
        $instancesData = isset($instances['data']) ? $instances['data'] : $instances;

        foreach ($instancesData as $inst) {
            $name = $inst['name'] ?? $inst['instanceName'] ?? '';
            if ($name === $instanceName) {
                return $inst;
            }
        }

        return null;
    }

    /**
     * Check API health
     *
     * @return bool True if API is reachable
     */
    public function healthCheck(): bool
    {
        $response = $this->request('GET', '/instance/fetchInstances');
        return $response['status'] === 200;
    }

    /**
     * Get pairing code for instance connection
     *
     * @param string $instanceName Instance name
     * @param string $phoneNumber Phone number with country code (e.g., 555199309404)
     * @return array Response with pairing code or error
     */
    public function getPairingCode(string $instanceName, string $phoneNumber): array
    {
        $endpoint = '/instance/connect/' . urlencode($instanceName) . '?number=' . urlencode($phoneNumber);

        $response = $this->request('GET', $endpoint);

        if ($response['status'] === 200) {
            $payload = is_array($response['data']) ? $response['data'] : [];

            $pairingCode = $payload['pairingCode']
                ?? $payload['pairing_code']
                ?? null;

            if (!$pairingCode && isset($payload['code']) && is_string($payload['code'])) {
                $candidate = trim($payload['code']);
                if (preg_match('/^[A-Z0-9]{6,12}$/i', $candidate) === 1) {
                    $pairingCode = $candidate;
                }
            }

            if (!$pairingCode) {
                return [
                    'success' => false,
                    'pending' => true,
                    'message' => 'Pairing code ainda nao disponivel, tente novamente em alguns segundos',
                    'data' => $payload
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'pairingCode' => $pairingCode,
                    'raw' => $payload
                ]
            ];
        }

        $errorMessage = $response['data']['response']['message'][0]
            ?? $response['data']['response']['message']
            ?? $response['error']
            ?? 'Erro ao gerar codigo';

        return [
            'success' => false,
            'message' => $errorMessage
        ];
    }

    /**
     * Ensure an instance exists for QR flow and create it if missing.
     *
     * @param string $instanceName
     * @return array
     */
    public function ensureInstanceExistsForQr(string $instanceName): array
    {
        $existing = $this->getInstance($instanceName);
        if ($existing !== null) {
            return [
                'success' => true,
                'created' => false
            ];
        }

        $createPayload = [
            'instanceName' => $instanceName,
            'integration' => 'WHATSAPP-BAILEYS',
            'qrcode' => true,
            'rejectCall' => false,
            'msgCall' => '',
            'groupsIgnore' => false,
            'alwaysOnline' => false,
            'readMessages' => true,
            'readStatus' => true,
            'syncFullHistory' => false
        ];

        $created = $this->createInstance($createPayload);
        if (!$created['success']) {
            $message = strtolower((string)($created['message'] ?? ''));
            if (strpos($message, 'already') !== false || strpos($message, 'existe') !== false) {
                return [
                    'success' => true,
                    'created' => false
                ];
            }

            return [
                'success' => false,
                'created' => false,
                'message' => $created['message'] ?? 'Nao foi possivel criar a instancia automaticamente'
            ];
        }

        return [
            'success' => true,
            'created' => true
        ];
    }

    /**
     * Fetches QR Code data for an instance connection attempt.
     *
     * @param string $instanceName
     * @return array
     */
    public function getQrCode(string $instanceName): array
    {
        $endpoint = '/instance/connect/' . urlencode($instanceName);
        $response = $this->request('GET', $endpoint);

        if ($response['status'] !== 200) {
            $errorMessage = $response['data']['response']['message'][0]
                ?? $response['data']['response']['message']
                ?? $response['error']
                ?? 'Erro ao obter QR code';

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }

        $payload = is_array($response['data']) ? $response['data'] : [];
        $qrCodeDataUrl = $this->extractQrCodeDataUrl($payload);

        if ($qrCodeDataUrl === null) {
            return [
                'success' => false,
                'pending' => true,
                'message' => 'QR code ainda nao disponivel',
                'data' => $payload
            ];
        }

        return [
            'success' => true,
            'data' => [
                'qrCode' => $qrCodeDataUrl,
                'raw' => $payload
            ]
        ];
    }

    /**
     * Walk payload recursively and returns a normalized QR image data URL.
     *
     * @param mixed $value
     * @return string|null
     */
    private function extractQrCodeDataUrl($value): ?string
    {
        if (is_array($value)) {
            foreach (self::QR_CODE_KEYS as $key) {
                if (isset($value[$key])) {
                    $normalized = $this->normalizeQrCodeValue($value[$key]);
                    if ($normalized !== null) {
                        return $normalized;
                    }
                }
            }

            foreach ($value as $item) {
                $normalized = $this->extractQrCodeDataUrl($item);
                if ($normalized !== null) {
                    return $normalized;
                }
            }
        }

        return null;
    }

    /**
     * Normalize raw QR value into a data URL when possible.
     *
     * @param mixed $value
     * @return string|null
     */
    private function normalizeQrCodeValue($value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if (strpos($trimmed, 'data:image/') === 0) {
            return $trimmed;
        }

        if (preg_match('/^[A-Za-z0-9+\/]+=*$/', $trimmed) === 1 && strlen($trimmed) >= 128) {
            return 'data:image/png;base64,' . $trimmed;
        }

        return null;
    }

    /**
     * Run just-in-time connectivity validation with Evolution API
     *
     * @return array
     */
    public function checkConnectivity(): array
    {
        $response = $this->request('GET', '/instance/fetchInstances', null, 1);

        if ($response['status'] === 200) {
            return ['success' => true];
        }

        $errorMessage = $response['error']
            ?? $response['data']['response']['message'][0]
            ?? $response['data']['response']['message']
            ?? 'Falha de conectividade com Evolution API';

        if ($response['status'] === 0) {
            return [
                'success' => false,
                'code' => 'CONNECTION_REFUSED',
                'message' => 'Nao foi possivel conectar na Evolution API agora. ' . $errorMessage
            ];
        }

        return [
            'success' => false,
            'code' => 'API_UNAVAILABLE',
            'message' => 'Evolution API indisponivel no momento. ' . $errorMessage
        ];
    }
    
    /**
     * Get instance connection status
     *
     * @param string $instanceName Instance name
     * @return array|null Status data with 'state' and 'status' keys, or null on error
     */
    public function getInstanceStatus(string $instanceName): ?array
    {
        $endpoint = '/instance/fetchInstances?instanceName=' . urlencode($instanceName);
        
        $response = $this->request('GET', $endpoint);
        
        if ($response['status'] === 200 && isset($response['data'][0])) {
            $instance = $response['data'][0];
            // API returns flat structure with connectionStatus field
            return [
                'state' => $instance['connectionStatus'] ?? 'close',
                'status' => $instance['connectionStatus'] ?? 'close',
                'owner' => $instance['ownerJid'] ?? null,
                'profileName' => $instance['profileName'] ?? null
            ];
        }
        
        return null;
    }
}
