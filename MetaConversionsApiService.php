<?php
declare(strict_types=1);

final class MetaConversionsApiService
{
    private string $pixelId;
    private string $accessToken;
    private string $apiVersion;

    public function __construct(?string $pixelId = null, ?string $accessToken = null, ?string $apiVersion = null)
    {
        $this->pixelId = $pixelId ?: (getenv('META_CAPI_PIXEL_ID') ?: '1386130056894015');
        $this->accessToken = $accessToken ?: (getenv('META_CAPI_ACCESS_TOKEN') ?: '');
        $this->apiVersion = $apiVersion ?: (getenv('META_CAPI_API_VERSION') ?: 'v25.0');
    }

    public function isConfigured(): bool
    {
        return $this->pixelId !== '' && $this->accessToken !== '';
    }

    public function sendLead(array $leadData): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Meta CAPI nao configurada',
            ];
        }

        $eventTime = (int) ($leadData['event_time'] ?? time());
        $eventId = (string) ($leadData['event_id'] ?? ('quiz_' . ($leadData['session_id'] ?? uniqid('event_', true))));
        $userData = $this->buildUserData($leadData);

        $payload = [
            'data' => [[
                'event_name' => 'Lead',
                'event_time' => $eventTime,
                'event_id' => $eventId,
                'action_source' => 'website',
                'event_source_url' => (string) ($leadData['event_source_url'] ?? ''),
                'user_data' => $userData,
                'custom_data' => $this->buildCustomData($leadData),
            ]],
        ];

        if (!empty($leadData['test_event_code'])) {
            $payload['test_event_code'] = (string) $leadData['test_event_code'];
        }

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/events?access_token=%s',
            $this->apiVersion,
            $this->pixelId,
            rawurlencode($this->accessToken)
        );

        $ch = curl_init($url);
        if ($ch === false) {
            return ['success' => false, 'message' => 'Falha ao inicializar cURL'];
        }

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return ['success' => false, 'message' => $error ?: 'Falha desconhecida na CAPI'];
        }

        $decoded = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $decoded ?? $response,
        ];
    }

    private function buildUserData(array $leadData): array
    {
        $userData = [];

        if (!empty($leadData['fbp'])) {
            $userData['fbp'] = (string) $leadData['fbp'];
        }

        if (!empty($leadData['fbc'])) {
            $userData['fbc'] = (string) $leadData['fbc'];
        }

        if (!empty($leadData['client_ip_address'])) {
            $userData['client_ip_address'] = (string) $leadData['client_ip_address'];
        }

        if (!empty($leadData['client_user_agent'])) {
            $userData['client_user_agent'] = (string) $leadData['client_user_agent'];
        }

        $phone = preg_replace('/\D+/', '', (string) ($leadData['whatsapp'] ?? ''));
        if ($phone !== '') {
            $userData['ph'] = hash('sha256', $phone);
        }

        $email = trim((string) ($leadData['email'] ?? ''));
        if ($email !== '') {
            $userData['em'] = hash('sha256', strtolower($email));
        }

        return $userData;
    }

    private function buildCustomData(array $leadData): array
    {
        return [
            'content_name' => 'Quiz Comercial',
            'content_category' => 'lead_generation',
            'status' => (string) ($leadData['classificacao'] ?? ''),
            'score' => (int) ($leadData['score'] ?? 0),
            'faturamento' => (string) ($leadData['faturamento'] ?? ''),
            'trilha' => (string) ($leadData['trilha'] ?? ''),
        ];
    }
}
