<?php
declare(strict_types=1);

require_once __DIR__ . '/../panel/PhoneParser.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Vary: Accept-Encoding');

function apiResponse(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonInput(): array
{
    $payload = json_decode(file_get_contents('php://input'), true);

    return is_array($payload) ? $payload : [];
}

function requireQuizConfig(): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    require_once __DIR__ . '/../config.php';
    $loaded = true;
}

function requireMetaConversionsService(): void
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    require_once __DIR__ . '/../MetaConversionsApiService.php';
    $loaded = true;
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

function valueOrNull(string $value): ?string
{
    return $value === '' ? null : $value;
}

function getQuizSnapshotContext(array $input): array
{
    return [
        'landing_url' => trim((string) ($input['landing_url'] ?? ($_SERVER['HTTP_REFERER'] ?? ''))),
        'referer' => trim((string) ($input['referer'] ?? ($_SERVER['HTTP_REFERER'] ?? ''))),
        'gclid' => trim((string) ($input['gclid'] ?? '')),
        'fbclid' => trim((string) ($input['fbclid'] ?? '')),
        'ttclid' => trim((string) ($input['ttclid'] ?? '')),
        'wbraid' => trim((string) ($input['wbraid'] ?? '')),
        'gbraid' => trim((string) ($input['gbraid'] ?? '')),
        'fbp' => trim((string) ($input['fbp'] ?? '')),
        'fbc' => trim((string) ($input['fbc'] ?? '')),
    ];
}

function getQuizEventContext(array $input): array
{
    $snapshot = getQuizSnapshotContext($input);

    return $snapshot + [
        'client_ip' => getClientIpAddress(),
        'user_agent' => trim((string) ($input['client_user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? ''))),
    ];
}

function saveQuizEvent(SQLite3 $db, string $sessionId, string $eventType, array $data, array $context): void
{
    $stmt = $db->prepare(' 
        INSERT INTO quiz_events (
            session_id, event_type, step_key, step_index, field_name, field_value, page_url, referer,
            utm_source, utm_medium, utm_campaign, utm_content, utm_term, gclid, fbclid, ttclid,
            wbraid, gbraid, fbp, fbc, client_ip, user_agent
        ) VALUES (
            :session_id, :event_type, :step_key, :step_index, :field_name, :field_value, :page_url, :referer,
            :utm_source, :utm_medium, :utm_campaign, :utm_content, :utm_term, :gclid, :fbclid, :ttclid,
            :wbraid, :gbraid, :fbp, :fbc, :client_ip, :user_agent
        )
    ');

    $stmt->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
    $stmt->bindValue(':event_type', $eventType, SQLITE3_TEXT);
    $stmt->bindValue(':step_key', (string) ($data['step_key'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':step_index', (int) ($data['step_index'] ?? 0), SQLITE3_INTEGER);
    $stmt->bindValue(':field_name', (string) ($data['field_name'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':field_value', (string) ($data['field_value'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':page_url', (string) ($context['landing_url'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':referer', (string) ($context['referer'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':utm_source', (string) ($data['utm_source'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':utm_medium', (string) ($data['utm_medium'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':utm_campaign', (string) ($data['utm_campaign'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':utm_content', (string) ($data['utm_content'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':utm_term', (string) ($data['utm_term'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':gclid', (string) ($context['gclid'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':fbclid', (string) ($context['fbclid'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':ttclid', (string) ($context['ttclid'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':wbraid', (string) ($context['wbraid'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':gbraid', (string) ($context['gbraid'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':fbp', (string) ($context['fbp'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':fbc', (string) ($context['fbc'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':client_ip', (string) ($context['client_ip'] ?? ''), SQLITE3_TEXT);
    $stmt->bindValue(':user_agent', (string) ($context['user_agent'] ?? ''), SQLITE3_TEXT);

    $stmt->execute();
}

function saveQuizLeadSnapshot(SQLite3 $db, array $lead): void
{
    $existing = $db->prepare('SELECT created_at, first_seen_at, current_step, status, webhook_sent_at, webhook_response FROM quiz_leads WHERE session_id = :session_id');
    $existing->bindValue(':session_id', $lead['session_id'], SQLITE3_TEXT);
    $row = $existing->execute()->fetchArray(SQLITE3_ASSOC) ?: [];

    $lead['created_at'] = $row['created_at'] ?? $lead['created_at'];
    $lead['first_seen_at'] = $row['first_seen_at'] ?? $lead['first_seen_at'];
    $lead['current_step'] = max((int) ($row['current_step'] ?? 0), (int) $lead['current_step']);
    $lead['status'] = $row['status'] === 'completed' || $lead['status'] === 'completed' ? 'completed' : $lead['status'];
    $lead['webhook_sent_at'] = $row['webhook_sent_at'] ?? $lead['webhook_sent_at'];
    $lead['webhook_response'] = $row['webhook_response'] ?? $lead['webhook_response'];

    $db->exec('DELETE FROM quiz_leads WHERE session_id = ' . $db->escapeString((string) $lead['session_id']));

    $stmt = $db->prepare('
        INSERT INTO quiz_leads (
            session_id, nome, whatsapp, cargo, faturamento, faturamento_valor, canal, volume_leads,
            dor_principal, dor_detalhe, timing, score, classificacao, trilha, utm_source, utm_medium,
            utm_campaign, utm_content, utm_term, landing_url, referer, gclid, fbclid, ttclid, wbraid,
            gbraid, fbp, fbc, first_seen_at, completed_at, last_event_at, status, current_step,
            created_at, updated_at, webhook_sent_at, webhook_response
        ) VALUES (
            :session_id, :nome, :whatsapp, :cargo, :faturamento, :faturamento_valor, :canal, :volume_leads,
            :dor_principal, :dor_detalhe, :timing, :score, :classificacao, :trilha, :utm_source, :utm_medium,
            :utm_campaign, :utm_content, :utm_term, :landing_url, :referer, :gclid, :fbclid, :ttclid, :wbraid,
            :gbraid, :fbp, :fbc, :first_seen_at, :completed_at, :last_event_at, :status, :current_step,
            :created_at, :updated_at, :webhook_sent_at, :webhook_response
        )
    ');

    foreach ($lead as $key => $value) {
        $stmt->bindValue(':' . $key, $value === null ? null : $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
    }

    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(405, [
        'success' => false,
        'message' => 'Metodo nao permitido',
    ]);
}

$input = getJsonInput();

$sessionId = trim((string) ($input['session_id'] ?? ''));

if ($sessionId === '') {
    apiResponse(400, [
        'success' => false,
        'message' => 'Dados invalidos',
    ]);
}

$action = $input['action'] ?? 'submit';
$context = getQuizEventContext($input);

if ($action === 'validate-phone') {
    $phone = trim((string) ($input['phone'] ?? ''));

    if (empty($phone)) {
        apiResponse(400, [
            'success' => false,
            'message' => 'Telefone não fornecido',
        ]);
    }

    // Rate limit REMOVIDO por causa de loop infinito

    $parsed = Panel\PhoneParser::parse($phone);

    if (!$parsed['is_valid']) {
        $response = [
            'success' => true,
            'valid' => false,
            'error' => $parsed['reason'] ?? 'Telefone inválido',
        ];

        if ($parsed['ddd'] !== null) {
            $response['ddd'] = $parsed['ddd'];
            $response['state'] = Panel\PhoneParser::getStateFromDDD($parsed['ddd']);
            $response['state_name'] = Panel\PhoneParser::getStateNameFromDDD($parsed['ddd']);
        }

        apiResponse(200, $response);
    }

    $response = [
        'success' => true,
        'valid' => true,
        'normalized' => $parsed['normalized'],
        'ddd' => $parsed['ddd'],
        'carrier' => $parsed['carrier_inferred_from_prefix'] ?? 'Desconhecida',
        'carrier_is_guaranteed' => $parsed['carrier_is_guaranteed'],
        'line_type' => $parsed['type'],
        'state' => Panel\PhoneParser::getStateFromDDD($parsed['ddd']),
        'state_name' => Panel\PhoneParser::getStateNameFromDDD($parsed['ddd']),
    ];

    apiResponse(200, $response);
}

if ($action === 'track-event') {
    requireQuizConfig();
    $eventType = trim((string) ($input['event_type'] ?? ''));
    if ($eventType === '') {
        apiResponse(400, ['success' => false, 'message' => 'Evento inválido']);
    }

    try {
        $db = getDB();
        saveQuizEvent($db, $sessionId, $eventType, $input, $context);
        apiResponse(200, ['success' => true]);
    } catch (Exception $e) {
        error_log("[Quiz API] Event error: {$e->getMessage()}");
        apiResponse(500, ['success' => false, 'message' => 'Erro interno do servidor']);
    }
}

if ($action === 'track-progress') {
    requireQuizConfig();

    $currentStep = (int) ($input['current_step'] ?? 0);
    $stepKey = trim((string) ($input['step_key'] ?? ''));
    $nome = trim((string) ($input['nome'] ?? ''));
    $whatsapp = trim((string) ($input['whatsapp'] ?? ''));
    $cargo = trim((string) ($input['cargo'] ?? ''));
    $faturamento = trim((string) ($input['faturamento'] ?? ''));
    $canal = trim((string) ($input['canal'] ?? ''));
    $volumeLeads = trim((string) ($input['volume_leads'] ?? ''));
    $dorPrincipal = trim((string) ($input['dor_principal'] ?? ''));
    $timing = trim((string) ($input['timing'] ?? ''));

    $utmSource = trim((string) ($input['utm_source'] ?? ''));
    $utmMedium = trim((string) ($input['utm_medium'] ?? ''));
    $utmCampaign = trim((string) ($input['utm_campaign'] ?? ''));
    $utmContent = trim((string) ($input['utm_content'] ?? ''));
    $utmTerm = trim((string) ($input['utm_term'] ?? ''));

    $snapshotContext = getQuizSnapshotContext($input);
    $status = $currentStep >= 10 ? 'completed' : 'in_progress';

    try {
        $db = getDB();

        saveQuizEvent($db, $sessionId, 'step_progress', [
            'step_key' => $stepKey,
            'step_index' => $currentStep,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_content' => $utmContent,
            'utm_term' => $utmTerm,
        ], $context);

        $existing = $db->prepare('SELECT created_at, first_seen_at, current_step, status, webhook_sent_at, webhook_response FROM quiz_leads WHERE session_id = :session_id');
        $existing->bindValue(':session_id', $sessionId, SQLITE3_TEXT);
        $row = $existing->execute()->fetchArray(SQLITE3_ASSOC) ?: [];

        $lead = [
            'session_id' => $sessionId,
            'nome' => $nome,
            'whatsapp' => $whatsapp,
            'cargo' => $cargo,
            'faturamento' => $faturamento,
            'canal' => $canal,
            'volume_leads' => $volumeLeads,
            'dor_principal' => $dorPrincipal,
            'timing' => $timing,
            'utm_source' => $utmSource,
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_content' => $utmContent,
            'utm_term' => $utmTerm,
            'landing_url' => $snapshotContext['landing_url'],
            'referer' => $snapshotContext['referer'],
            'gclid' => $snapshotContext['gclid'],
            'fbclid' => $snapshotContext['fbclid'],
            'ttclid' => $snapshotContext['ttclid'],
            'wbraid' => $snapshotContext['wbraid'],
            'gbraid' => $snapshotContext['gbraid'],
            'fbp' => $snapshotContext['fbp'],
            'fbc' => $snapshotContext['fbc'],
            'first_seen_at' => $row['first_seen_at'] ?? date('Y-m-d H:i:s'),
            'completed_at' => $status === 'completed' ? date('Y-m-d H:i:s') : null,
            'last_event_at' => date('Y-m-d H:i:s'),
            'status' => $status,
            'current_step' => max((int) ($row['current_step'] ?? 0), $currentStep),
            'step_key' => $stepKey,
            'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'webhook_sent_at' => $row['webhook_sent_at'] ?? null,
            'webhook_response' => $row['webhook_response'] ?? null,
        ];

        $db->exec('DELETE FROM quiz_leads WHERE session_id = ' . $db->escapeString((string) $sessionId));

        $stmt = $db->prepare('
            INSERT INTO quiz_leads (
                session_id, nome, whatsapp, cargo, faturamento, canal, volume_leads,
                dor_principal, timing, utm_source, utm_medium, utm_campaign, utm_content, utm_term,
                landing_url, referer, gclid, fbclid, ttclid, wbraid, gbraid, fbp, fbc,
                first_seen_at, completed_at, last_event_at, status, current_step, step_key,
                created_at, updated_at, webhook_sent_at, webhook_response
            ) VALUES (
                :session_id, :nome, :whatsapp, :cargo, :faturamento, :canal, :volume_leads,
                :dor_principal, :timing, :utm_source, :utm_medium, :utm_campaign, :utm_content, :utm_term,
                :landing_url, :referer, :gclid, :fbclid, :ttclid, :wbraid, :gbraid, :fbp, :fbc,
                :first_seen_at, :completed_at, :last_event_at, :status, :current_step, :step_key,
                :created_at, :updated_at, :webhook_sent_at, :webhook_response
            )
        ');

        foreach ($lead as $key => $value) {
            $stmt->bindValue(':' . $key, $value === null ? null : $value, is_int($value) ? SQLITE3_INTEGER : SQLITE3_TEXT);
        }

        $stmt->execute();

        apiResponse(200, ['success' => true, 'step' => $currentStep, 'step_key' => $stepKey]);
    } catch (Exception $e) {
        error_log("[Quiz API] Track progress error: {$e->getMessage()}");
        apiResponse(500, ['success' => false, 'message' => 'Erro interno do servidor']);
    }
}

requireQuizConfig();

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

$whatsappParsed = Panel\PhoneParser::parse($whatsapp);
if (!$whatsappParsed['is_valid']) {
    $errors[] = $whatsappParsed['reason'] ?? 'Telefone inválido';
}

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
$status = $currentStep > 0 && $status !== 'completed' ? 'in_progress' : $status;
$snapshotContext = getQuizSnapshotContext($input);

try {
    $db = getDB();

    saveQuizEvent($db, $sessionId, $status === 'completed' ? 'quiz_completed' : 'step_completed', [
        'step_key' => (string) ($input['step_key'] ?? ''),
        'step_index' => $currentStep,
        'utm_source' => $utmSource,
        'utm_medium' => $utmMedium,
        'utm_campaign' => $utmCampaign,
        'utm_content' => $utmContent,
        'utm_term' => $utmTerm,
    ], $context);

    saveQuizLeadSnapshot($db, [
        'session_id' => $sessionId,
        'nome' => $nome,
        'whatsapp' => $whatsappClean,
        'cargo' => $cargo,
        'faturamento' => $faturamento,
        'faturamento_valor' => $faturamentoValor,
        'canal' => $canal,
        'volume_leads' => $volumeLeads,
        'dor_principal' => $dorPrincipal,
        'dor_detalhe' => $dorDetalhe,
        'timing' => $timing,
        'score' => $scoring['score'],
        'classificacao' => $scoring['classificacao'],
        'trilha' => $trilha,
        'utm_source' => $utmSource,
        'utm_medium' => $utmMedium,
        'utm_campaign' => $utmCampaign,
        'utm_content' => $utmContent,
        'utm_term' => $utmTerm,
        'landing_url' => $snapshotContext['landing_url'],
        'referer' => $snapshotContext['referer'],
        'gclid' => $snapshotContext['gclid'],
        'fbclid' => $snapshotContext['fbclid'],
        'ttclid' => $snapshotContext['ttclid'],
        'wbraid' => $snapshotContext['wbraid'],
        'gbraid' => $snapshotContext['gbraid'],
        'fbp' => $snapshotContext['fbp'],
        'fbc' => $snapshotContext['fbc'],
        'first_seen_at' => date('Y-m-d H:i:s'),
        'completed_at' => $status === 'completed' ? date('Y-m-d H:i:s') : null,
        'last_event_at' => date('Y-m-d H:i:s'),
        'status' => $status,
        'current_step' => $currentStep,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'webhook_sent_at' => null,
        'webhook_response' => null,
    ]);

    if ($status === 'completed') {
        $clientIp = getClientIpAddress();
        $clientUserAgent = $input['client_user_agent'] ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $createdAt = (new DateTime())->format('Y-m-d H:i:s');

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
            'utm_medium' => $utmMedium,
            'utm_campaign' => $utmCampaign,
            'utm_content' => $utmContent,
            'utm_term' => $utmTerm,
            'client_ip_address' => $clientIp,
            'client_user_agent' => $clientUserAgent,
            'referer' => $referer,
            'created_at' => $createdAt,
        ]);

        requireMetaConversionsService();
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
