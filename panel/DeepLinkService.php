<?php

require_once 'Config.php';

class DeepLinkService
{
    /**
     * Builds a signed deep-link URL for end-user QR connection flow.
     *
     * @param string $instanceName
     * @param int|null $expiresAt Unix timestamp
     * @return string
     */
    public static function buildSignedUrl(string $instanceName, ?int $expiresAt = null): string
    {
        Config::load();

        $instanceName = preg_replace('/[^a-zA-Z0-9_-]/', '', $instanceName);
        $ttlSeconds = max(300, Config::getInt('DEEP_LINK_DEFAULT_TTL_SECONDS', 604800));
        $expiresAt = $expiresAt ?? (time() + $ttlSeconds);
        $signature = self::sign($instanceName, $expiresAt);

        $query = http_build_query([
            'instance' => $instanceName,
            'exp' => $expiresAt,
            'sig' => $signature
        ]);

        return rtrim(self::baseUrl(), '/') . '/deep-link.php?' . $query;
    }

    /**
     * Validates signed request payload from query/form parameters.
     *
     * @param string $instanceName
     * @param int $expiresAt
     * @param string $signature
     * @return bool
     */
    public static function validate(string $instanceName, int $expiresAt, string $signature): bool
    {
        if ($instanceName === '' || $signature === '') {
            return false;
        }

        if ($expiresAt < time()) {
            return false;
        }

        $expected = self::sign($instanceName, $expiresAt);
        return hash_equals($expected, $signature);
    }

    /**
     * Returns secure signature for deep link payload.
     *
     * @param string $instanceName
     * @param int $expiresAt
     * @return string
     */
    public static function sign(string $instanceName, int $expiresAt): string
    {
        Config::load();
        $secret = Config::getString('DEEP_LINK_SECRET', Config::getHmacKey());
        $payload = $instanceName . '|' . $expiresAt;
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Public base URL for deep-link generation.
     *
     * @return string
     */
    public static function baseUrl(): string
    {
        $configuredBaseUrl = trim(Config::getString('PANEL_PUBLIC_BASE_URL', ''));
        if ($configuredBaseUrl !== '') {
            return $configuredBaseUrl;
        }

        $forwardedHost = trim((string)($_SERVER['HTTP_X_FORWARDED_HOST'] ?? ''));
        if ($forwardedHost !== '') {
            $hostCandidates = explode(',', $forwardedHost);
            $host = trim($hostCandidates[0]);
        } else {
            $host = trim((string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
        }

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        );

        $scheme = $isHttps ? 'https' : 'http';

        if (strpos($host, ':') === false) {
            $forwardedPort = (string)($_SERVER['HTTP_X_FORWARDED_PORT'] ?? '');
            $serverPort = (string)($_SERVER['SERVER_PORT'] ?? '');
            $port = $forwardedPort !== '' ? $forwardedPort : $serverPort;

            if ($port !== '' && $port !== '80' && $port !== '443') {
                $host .= ':' . $port;
            }
        }

        $basePath = self::detectBasePath();

        return $scheme . '://' . $host . $basePath;
    }

    /**
     * Detect current application base path from executing script.
     *
     * Example: /panel/index.php => /panel
     *
     * @return string
     */
    private static function detectBasePath(): string
    {
        $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName === '') {
            return '';
        }

        $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($dir === '' || $dir === '.') {
            return '';
        }

        return $dir;
    }
}
