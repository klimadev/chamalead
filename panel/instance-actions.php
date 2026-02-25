<?php
/**
 * Instance Actions API Endpoint
 * 
 * Handles all AJAX requests for instance management using EvolutionApiService.
 * 
 * @package Panel
 * @author Chamalead
 * @version 2.0.0
 */

require_once 'auth.php';
require_once 'EvolutionApiService.php';
require_once 'DeepLinkService.php';

redirect_if_not_auth();

header('Content-Type: application/json');

// Disable error display to prevent information leakage
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// Validate CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!validate_csrf_token($csrf_token)) {
    Logger::warning('Invalid CSRF token attempt', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    echo json_encode(['success' => false, 'message' => 'Token de segurança inválido ou expirado']);
    exit;
}

// Initialize API service
$api = new EvolutionApiService();

$action = sanitize_string($_POST['action'] ?? '');
$result = ['success' => false, 'message' => 'Ação inválida'];
$pairingCodeTtlSeconds = max(20, Config::getInt('PAIRING_CODE_TTL_SECONDS', 45));

/**
 * Builds normalized pairing payload with timer metadata.
 */
function build_pairing_payload(string $pairingCode, ?string $lastPairingCode, int $ttlSeconds): array
{
    $receivedAt = time();
    $expiresAt = $receivedAt + $ttlSeconds;

    return [
        'pairingCode' => $pairingCode,
        'receivedAt' => $receivedAt,
        'expiresAt' => $expiresAt,
        'ttlSeconds' => $ttlSeconds,
        'changed' => $lastPairingCode === null ? true : $pairingCode !== $lastPairingCode
    ];
}

/**
 * Masks pairing code for logs.
 */
function mask_pairing_code(string $pairingCode): string
{
    $length = strlen($pairingCode);

    if ($length <= 4) {
        return str_repeat('*', $length);
    }

    return substr($pairingCode, 0, 2) . str_repeat('*', $length - 4) . substr($pairingCode, -2);
}

switch($action) {
    case 'create':
        $instanceName = sanitize_alphanumeric(trim($_POST['instanceName'] ?? ''));
        
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instância é obrigatório'];
            break;
        }
        
        if (strlen($instanceName) < 3 || strlen($instanceName) > 50) {
            $result = ['success' => false, 'message' => 'Nome deve ter entre 3 e 50 caracteres'];
            break;
        }
        
        $data = [
            'instanceName' => $instanceName,
            'integration' => 'WHATSAPP-BAILEYS',
            'qrcode' => false,
            'rejectCall' => filter_var($_POST['rejectCall'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'msgCall' => sanitize_string($_POST['msgCall'] ?? ''),
            'groupsIgnore' => filter_var($_POST['groupsIgnore'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'alwaysOnline' => filter_var($_POST['alwaysOnline'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'readMessages' => filter_var($_POST['readMessages'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'readStatus' => filter_var($_POST['readStatus'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'syncFullHistory' => filter_var($_POST['syncFullHistory'] ?? false, FILTER_VALIDATE_BOOLEAN)
        ];
        
        $response = $api->createInstance($data);
        
        if ($response['success']) {
            log_action('instance_create', "Created instance: {$instanceName}");
            Logger::info("Instance created: {$instanceName}");
            $result = ['success' => true, 'message' => 'Instância criada com sucesso!'];
        } else {
            log_action('instance_create_failed', "Failed to create instance: {$instanceName}. Error: {$response['message']}");
            Logger::error("Failed to create instance: {$instanceName}", ['error' => $response['message']]);
            $result = ['success' => false, 'message' => $response['message']];
        }
        break;
        
    case 'getSettings':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instância não fornecido'];
            break;
        }
        
        $settings = $api->getSettings($instanceName);
        
        if ($settings !== null) {
            $result = ['success' => true, 'data' => $settings];
        } else {
            Logger::warning("Failed to get settings for instance: {$instanceName}");
            $result = ['success' => false, 'message' => 'Erro ao buscar configurações'];
        }
        break;
        
    case 'edit':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instância não fornecido'];
            break;
        }
        
        $data = [
            'rejectCall' => filter_var($_POST['rejectCall'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'msgCall' => sanitize_string($_POST['msgCall'] ?? ''),
            'groupsIgnore' => filter_var($_POST['groupsIgnore'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'alwaysOnline' => filter_var($_POST['alwaysOnline'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'readMessages' => filter_var($_POST['readMessages'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'readStatus' => filter_var($_POST['readStatus'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'syncFullHistory' => filter_var($_POST['syncFullHistory'] ?? false, FILTER_VALIDATE_BOOLEAN)
        ];
        
        $response = $api->updateSettings($instanceName, $data);
        
        if ($response['success']) {
            log_action('instance_edit', "Updated settings for instance: {$instanceName}");
            Logger::info("Instance settings updated: {$instanceName}");
            $result = ['success' => true, 'message' => 'Configurações atualizadas com sucesso!'];
        } else {
            log_action('instance_edit_failed', "Failed to update instance: {$instanceName}. Error: {$response['message']}");
            Logger::error("Failed to update instance: {$instanceName}", ['error' => $response['message']]);
            $result = ['success' => false, 'message' => $response['message']];
        }
        break;
        
    case 'delete':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instância não fornecido'];
            break;
        }
        
        $response = $api->deleteInstance($instanceName);
        
        if ($response['success']) {
            log_action('instance_delete', "Deleted instance: {$instanceName}");
            Logger::info("Instance deleted: {$instanceName}");
            $result = ['success' => true, 'message' => 'Instância deletada com sucesso!'];
        } else {
            log_action('instance_delete_failed', "Failed to delete instance: {$instanceName}. Error: {$response['message']}");
            Logger::error("Failed to delete instance: {$instanceName}", ['error' => $response['message']]);
            $result = ['success' => false, 'message' => $response['message']];
        }
        break;
        
    case 'getInstanceDetails':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instância não fornecido'];
            break;
        }
        
        $instance = $api->getInstance($instanceName);
        
        if ($instance !== null) {
            log_action('instance_view', "Viewed details for instance: {$instanceName}");
            $result = ['success' => true, 'data' => $instance];
        } else {
            log_action('instance_view_failed', "Failed to view instance: {$instanceName} - not found");
            Logger::warning("Instance not found: {$instanceName}");
            $result = ['success' => false, 'message' => 'Instância não encontrada'];
        }
        break;
        
    case 'connect':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        $phoneNumber = preg_replace('/[^0-9]/', '', $_POST['phoneNumber'] ?? '');
        
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instância é obrigatório'];
            break;
        }
        
        if (empty($phoneNumber) || strlen($phoneNumber) < 10) {
            $result = ['success' => false, 'message' => 'Número de telefone inválido (mínimo 10 dígitos)'];
            break;
        }

        $connectivity = $api->checkConnectivity();
        if (!$connectivity['success']) {
            Logger::warning('Evolution connectivity failed before pairing generation', [
                'instance' => $instanceName,
                'phoneSuffix' => substr($phoneNumber, -4),
                'code' => $connectivity['code'] ?? 'UNKNOWN'
            ]);

            $result = [
                'success' => false,
                'message' => $connectivity['message'] ?? 'Falha de conectividade com Evolution API',
                'errorCode' => $connectivity['code'] ?? 'CONNECTION_FAILED'
            ];
            break;
        }

        $response = $api->getPairingCode($instanceName, $phoneNumber);

        if ($response['success']) {
            $pairingCode = $response['data']['pairingCode'] ?? null;
            if (!is_string($pairingCode) || $pairingCode === '') {
                $result = [
                    'success' => false,
                    'message' => 'Pairing code invalido retornado pela Evolution API',
                    'errorCode' => 'PAIRING_INVALID_RESPONSE'
                ];
                break;
            }

            $pairingPayload = build_pairing_payload($pairingCode, null, $pairingCodeTtlSeconds);

            log_action('instance_connect', "Pairing code generated: {$instanceName}");
            Logger::info('Pairing generated', [
                'instance' => $instanceName,
                'ttlSeconds' => $pairingCodeTtlSeconds,
                'codeMasked' => mask_pairing_code($pairingCode)
            ]);

            $result = [
                'success' => true,
                'message' => 'Codigo gerado com sucesso!',
                'data' => $pairingPayload
            ];
        } elseif (!empty($response['pending'])) {
            $result = [
                'success' => false,
                'message' => $response['message'] ?? 'Pairing code ainda nao disponivel',
                'errorCode' => 'PAIRING_PENDING'
            ];
        } else {
            log_action('instance_connect_failed', "{$instanceName}: {$response['message']}");
            $result = [
                'success' => false,
                'message' => $response['message'],
                'errorCode' => 'PAIRING_CONNECT_FAILED'
            ];
        }
        break;

    case 'syncPairing':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        $phoneNumber = preg_replace('/[^0-9]/', '', $_POST['phoneNumber'] ?? '');
        $lastPairingCodeRaw = sanitize_string($_POST['lastPairingCode'] ?? '');
        $lastPairingCode = $lastPairingCodeRaw !== '' ? $lastPairingCodeRaw : null;

        if (empty($instanceName) || empty($phoneNumber)) {
            $result = ['success' => false, 'message' => 'Dados para sincronizacao do pairing estao incompletos'];
            break;
        }

        $response = $api->getPairingCode($instanceName, $phoneNumber);

        if ($response['success']) {
            $pairingCode = $response['data']['pairingCode'] ?? null;
            if (!is_string($pairingCode) || $pairingCode === '') {
                $result = [
                    'success' => false,
                    'message' => 'Pairing code invalido retornado pela Evolution API',
                    'errorCode' => 'PAIRING_INVALID_RESPONSE'
                ];
                break;
            }

            $pairingPayload = build_pairing_payload($pairingCode, $lastPairingCode, $pairingCodeTtlSeconds);

            Logger::info('Pairing sync cycle', [
                'instance' => $instanceName,
                'changed' => $pairingPayload['changed'],
                'ttlSeconds' => $pairingCodeTtlSeconds,
                'codeMasked' => mask_pairing_code($pairingCode)
            ]);

            $result = [
                'success' => true,
                'data' => $pairingPayload
            ];
        } elseif (!empty($response['pending'])) {
            $result = [
                'success' => false,
                'message' => $response['message'] ?? 'Pairing code ainda nao disponivel',
                'errorCode' => 'PAIRING_PENDING'
            ];
        } else {
            $errorMessage = $response['message'] ?? 'Erro ao sincronizar codigo';
            $isConnectivityFailure = stripos($errorMessage, 'conectar') !== false
                || stripos($errorMessage, 'connection') !== false
                || stripos($errorMessage, 'servidor') !== false;

            $result = [
                'success' => false,
                'message' => $errorMessage,
                'errorCode' => $isConnectivityFailure ? 'CONNECTION_REFUSED' : 'PAIRING_SYNC_FAILED'
            ];
        }
        break;
        
    case 'checkStatus':
        $instanceName = sanitize_alphanumeric($_POST['instanceName'] ?? '');
        
        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instancia e obrigatorio'];
            break;
        }
        
        // Check instance status via fetchInstances ONLY (no connect call)
        // This prevents generating new pairing codes during polling
        $status = $api->getInstanceStatus($instanceName);
        
        if ($status !== null) {
            // Check if connected
            $isConnected = ($status['state'] === 'open' || $status['status'] === 'open');
            
            $result = [
                'success' => true,
                'data' => [
                    'state' => $isConnected ? 'open' : 'close',
                    'connected' => $isConnected
                ]
            ];
        } else {
            $result = ['success' => false, 'message' => 'Erro ao verificar status'];
        }
        break;

    case 'generateDeepLink':
        $instanceName = sanitize_alphanumeric(trim($_POST['instanceName'] ?? ''));

        if (empty($instanceName)) {
            $result = ['success' => false, 'message' => 'Nome da instancia e obrigatorio'];
            break;
        }

        if (strlen($instanceName) < 3 || strlen($instanceName) > 50) {
            $result = ['success' => false, 'message' => 'Nome deve ter entre 3 e 50 caracteres'];
            break;
        }

        $url = DeepLinkService::buildSignedUrl($instanceName);
        $expiresAt = 0;

        $queryString = (string)parse_url($url, PHP_URL_QUERY);
        if ($queryString !== '') {
            parse_str($queryString, $queryParams);
            $expiresAt = (int)($queryParams['exp'] ?? 0);
        }

        if ($expiresAt <= 0) {
            $expiresAt = time() + max(300, Config::getInt('DEEP_LINK_DEFAULT_TTL_SECONDS', 604800));
        }

        $ttlSeconds = max(0, $expiresAt - time());
        log_action('deep_link_generated', "Deep link generated for instance: {$instanceName}");

        $result = [
            'success' => true,
            'message' => 'Deep link gerado com sucesso',
            'data' => [
                'instanceName' => $instanceName,
                'url' => $url,
                'expiresAt' => $expiresAt,
                'ttlSeconds' => $ttlSeconds
            ]
        ];
        break;
}

echo json_encode($result);
exit;
