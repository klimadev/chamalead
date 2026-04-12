<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../MetaConversionsApiService.php';

header('Content-Type: application/json');

function apiResponse(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function getClientIpAddress(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $value = (string) $_SERVER[$key];
            if ($key === 'HTTP_X_FORWARDED_FOR') {
                return trim(explode(',', $value)[0]);
            }

            return trim($value);
        }
    }

    return 'unknown';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(405, [
        'success' => false,
        'message' => 'Metodo nao permitido',
    ]);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['session_id'])) {
    apiResponse(400, [
        'success' => false,
        'message' => 'Dados invalidos',
    ]);
}

$sessionId = trim((string) $input['session_id']);
$nome = trim((string) ($input['nome'] ?? ''));
$whatsapp = trim((string) ($input['whatsapp'] ?? ''));
$cargo = trim((string) ($input['cargo'] ?? ''));
$faturamento = trim((string) ($input['faturamento'] ?? ''));
$canal = trim((string) ($input['canal'] ?? ''));
$volumeLeads = trim((string) ($input['volume_leads'] ?? ''));
$dorPrincipal = trim((string) ($input['dor_principal'] ?? ''));
$dorDetalhe = trim((string) ($input['dor_detalhe'] ?? ''));
$timing = trim((string) ($input['timing'] ?? ''));
$currentStep = (int) ($input['current_step'] ?? 0);

$utmSource = trim((string) ($input['utm_source'] ?? ''));
$utmMedium = trim((string) ($input['utm_medium'] ?? ''));
$utmCampaign = trim((string) ($input['utm_campaign'] ?? ''));
$utmContent = trim((string) ($input['utm_content'] ?? ''));
$utmTerm = trim((string) ($input['utm_term'] ?? ''));

$errors = [];

$nomeValidation = validateQuizField('nome', $nome);
if (!$nomeValidation['valid']) {
    $errors[] = $nomeValidation['error'];
}

$whatsappValidation = validateQuizField('whatsapp', $whatsapp);
if (!$whatsappValidation['valid']) {
    $errors[] = $whatsappValidation['error'];
}
$whatsappClean = $whatsappValidation['clean'] ?? preg_replace('/[^0-9]/', '', $whatsapp);

if ($cargo !== '') {
    $cargoValidation = validateQuizField('cargo', $cargo);
    if (!$cargoValidation['valid']) {
        $errors[] = $cargoValidation['error'];
    }
}

if ($faturamento !== '') {
    $fatValidation = validateQuizField('faturamento', $faturamento);
    if (!$fatValidation['valid']) {
        $errors[] = $fatValidation['error'];
    }
}

if ($canal !== '') {
    $canalValidation = validateQuizField('canal', $canal);
    if (!$canalValidation['valid']) {
        $errors[] = $canalValidation['error'];
    }
}

if ($volumeLeads !== '') {
    $volValidation = validateQuizField('volume_leads', $volumeLeads);
    if (!$volValidation['valid']) {
        $errors[] = $volValidation['error'];
    }
}

if ($dorPrincipal !== '') {
    $dorValidation = validateQuizField('dor_principal', $dorPrincipal);
    if (!$dorValidation['valid']) {
        $errors[] = $dorValidation['error'];
    }
}

if ($timing !== '') {
    $timingValidation = validateQuizField('timing', $timing);
    if (!$timingValidation['valid']) {
        $errors[] = $timingValidation['error'];
    }
}

if (!empty($errors)) {
    apiResponse(422, [
        'success' => false,
        'message' => 'Dados invalidos',
        'errors' => $errors,
    ]);
}

$faturamentoValor = getFaturamentoValor($faturamento);

$answers = [
    'cargo' => $cargo,
    'faturamento' => $faturamento,
    'volume_leads' => $volumeLeads,
    'dor_principal' => $dorPrincipal,
    'timing' => $timing,
];

$scoring = calculateQuizScore($answers);
$trilha = determineTrack($faturamento);

$status = $currentStep >= 10 ? 'completed' : 'in_progress';

try {
    $db = getDB();

    $stmt = $db->prepare('
        INSERT OR REPLACE INTO quiz_leads (
            session_id,
            nome,
            whatsapp,
            cargo,
            faturamento,
            faturamento_valor,
            canal,
            volume_leads,
            dor_principal,
            dor_detalhe,
            timing,
            score,
            classificacao,
            trilha,
            utm_source,
            utm_medium,
            utm_campaign,
            utm_content,
            utm_term,
            status,
            current_step,
            updated_at
        ) VALUES (
            :session_id,
            :nome,
            :whatsapp,
            :cargo,
            :faturamento,
            :faturamento_valor,
            :canal,
            :volume_leads,
            :dor_principal,
            :dor_detalhe,
            :timing,
            :score,
            :classificacao,
            :trilha,
            :utm_source,
            :utm_medium,
            :utm_campaign,
            :utm_content,
            :utm_term,
            :status,
            :current_step,
            datetime("now", "localtime")
        )
    ');

    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':whatsapp', $whatsappClean, SQLITE3_TEXT);
    $stmt->bindValue(':cargo', $cargo, SQLITE3_TEXT);
    $stmt->bindValue(':faturamento', $faturamento, SQLITE3_TEXT);
    $stmt->bindValue(':faturamento_valor', $faturamentoValor, SQLITE3_INTEGER);
    $stmt->bindValue(':canal', $canal, SQLITE3_TEXT);
    $stmt->bindValue(':volume_leads', $volumeLeads, SQLITE3_TEXT);
    $stmt->bindValue(':dor_principal', $dorPrincipal, SQLITE3_TEXT);
    $stmt->bindValue(':dor_detalhe', $dorDetalhe, SQLITE3_TEXT);
    $stmt->bindValue(':timing', $timing, SQLITE3_TEXT);
    $stmt->bindValue(':score', $scoring['score'], SQLITE3_INTEGER);
    $stmt->bindValue(':classificacao', $scoring['classificacao'], SQLITE3_TEXT);
    $stmt->bindValue(':trilha', $trilha, SQLITE3_TEXT);
    $stmt->bindValue(':utm_source', $utmSource, SQLITE3_TEXT);
    $stmt->bindValue(':utm_medium', $utmMedium, SQLITE3_TEXT);
    $stmt->bindValue(':utm_campaign', $utmCampaign, SQLITE3_TEXT);
    $stmt->bindValue(':utm_content', $utmContent, SQLITE3_TEXT);
    $stmt->bindValue(':utm_term', $utmTerm, SQLITE3_TEXT);
    $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    $stmt->bindValue(':current_step', $currentStep, SQLITE3_INTEGER);

    if (!$stmt->execute()) {
        throw new Exception('Falha ao salvar dados do quiz');
    }

    if ($status === 'completed') {
        $evolutionResult = sendQuizLeadToEvolution([
            'nome' => $nome,
            'whatsapp' => $whatsappClean,
            'cargo' => $cargo,
            'faturamento' => $faturamento,
            'canal' => $canal,
            'volume_leads' => $volumeLeads,
            'dor_principal' => $dorPrincipal,
            'dor_detalhe' => $dorDetalhe,
            'timing' => $timing,
            'score' => $scoring['score'],
            'classificacao' => $scoring['classificacao'],
            'trilha' => $trilha,
            'utm_source' => $utmSource,
            'utm_campaign' => $utmCampaign,
        ]);

        $metaService = new MetaConversionsApiService();
        $metaResult = $metaService->sendLead([
            'session_id' => $sessionId,
            'whatsapp' => $whatsappClean,
            'email' => $input['email'] ?? '',
            'fbp' => $input['fbp'] ?? '',
            'fbc' => $input['fbc'] ?? '',
            'client_ip_address' => $input['client_ip_address'] ?? getClientIpAddress(),
            'client_user_agent' => $input['client_user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? ''),
            'score' => $scoring['score'],
            'classificacao' => $scoring['classificacao'],
            'faturamento' => $faturamento,
            'trilha' => $trilha,
            'event_id' => 'quiz_' . $sessionId,
            'event_time' => time(),
            'event_source_url' => $_SERVER['HTTP_REFERER'] ?? '',
            'test_event_code' => getenv('META_CAPI_TEST_EVENT_CODE') ?: '',
        ]);

        $webhookResponse = json_encode([
            'evolution' => $evolutionResult,
            'meta' => $metaResult,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $update = $db->prepare('UPDATE quiz_leads SET webhook_sent_at = datetime("now", "localtime"), webhook_response = :webhook_response WHERE session_id = :session_id');
        $update->bindValue(':webhook_response', $webhookResponse, SQLITE3_TEXT);
        $update->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
        $update->execute();
    }

    apiResponse(200, [
        'success' => true,
        'message' => $status === 'completed' ? 'Quiz finalizado com sucesso' : 'Progresso salvo',
        'score' => $scoring['score'],
        'classificacao' => $scoring['classificacao'],
        'trilha' => $trilha,
        'status' => $status,
    ]);
} catch (Exception $e) {
    error_log("[Quiz API] Error: {$e->getMessage()}");
    apiResponse(500, [
        'success' => false,
        'message' => 'Erro interno do servidor',
    ]);
}
