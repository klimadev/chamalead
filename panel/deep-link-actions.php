<?php

require_once 'Config.php';
require_once 'EvolutionApiService.php';
require_once 'DeepLinkService.php';
require_once 'Logger.php';

Config::load();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
if ($action !== 'syncQrDeepLink') {
    echo json_encode([
        'success' => false,
        'message' => 'Acao invalida',
        'errorCode' => 'INVALID_ACTION'
    ]);
    exit;
}

$instanceName = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['instance'] ?? '');
$expiresAt = (int)($_POST['exp'] ?? 0);
$signature = (string)($_POST['sig'] ?? '');

if (!DeepLinkService::validate($instanceName, $expiresAt, $signature)) {
    echo json_encode([
        'success' => false,
        'message' => 'Link invalido ou expirado',
        'errorCode' => 'LINK_INVALID'
    ]);
    exit;
}

$api = new EvolutionApiService();

$ensureResult = $api->ensureInstanceExistsForQr($instanceName);
if (!$ensureResult['success']) {
    Logger::error('Deep link automatic creation failed', [
        'instance' => $instanceName,
        'message' => $ensureResult['message'] ?? 'unknown'
    ]);

    echo json_encode([
        'success' => false,
        'message' => $ensureResult['message'] ?? 'Erro ao preparar instancia',
        'errorCode' => 'INSTANCE_PREPARE_FAILED'
    ]);
    exit;
}

$status = $api->getInstanceStatus($instanceName);
if ($status !== null && (($status['state'] ?? '') === 'open' || ($status['status'] ?? '') === 'open')) {
    echo json_encode([
        'success' => false,
        'message' => 'Instancia ja conectada',
        'errorCode' => 'CONNECTED'
    ]);
    exit;
}

$qrResult = $api->getQrCode($instanceName);
if (!$qrResult['success']) {
    $isPending = !empty($qrResult['pending']);
    echo json_encode([
        'success' => false,
        'message' => $qrResult['message'] ?? ($isPending ? 'Aguardando QR code' : 'Falha ao obter QR code'),
        'errorCode' => $isPending ? 'QR_PENDING' : 'QR_FETCH_FAILED'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => [
        'qrCode' => $qrResult['data']['qrCode'] ?? null,
        'created' => (bool)($ensureResult['created'] ?? false)
    ]
]);
exit;
