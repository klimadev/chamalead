<?php
define('DB_PATH', __DIR__ . '/leads.db');
define('ADMIN_SECRET', 'painel2025');

function getDB() {
    static $db = null;

    if ($db instanceof SQLite3) {
        return $db;
    }

    $db = new SQLite3(DB_PATH);
    $db->busyTimeout(3000);

    $db->exec(
        'CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT,
            empresa TEXT NOT NULL,
            whatsapp TEXT NOT NULL,
            instagram TEXT,
            faturamento TEXT NOT NULL,
            faturamento_valor INTEGER DEFAULT 0,
            desafio TEXT,
            status TEXT DEFAULT "novo",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME
        )'
    );

    ensureLeadsSchema($db);
    ensureQuizSchema($db);

    return $db;
}

function ensureLeadsSchema(SQLite3 $db) {
    if (!canWriteDatabase()) {
        return;
    }

    $columns = [];
    $result = $db->query('PRAGMA table_info(leads)');

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $columns[$row['name']] = true;
    }

    if (!isset($columns['nome'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN nome TEXT');
    }

    if (!isset($columns['desafio'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN desafio TEXT');
    }

    if (!isset($columns['status'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN status TEXT DEFAULT "novo"');
    }

    if (!isset($columns['updated_at'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN updated_at DATETIME');
    }

    if (!isset($columns['faturamento_valor'])) {
        @ $db->exec('ALTER TABLE leads ADD COLUMN faturamento_valor INTEGER DEFAULT 0');
    }

    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_leads_status_created_at ON leads(status, created_at DESC)');
    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_leads_faturamento_valor ON leads(faturamento_valor DESC)');
}

function canWriteDatabase() {
    if (file_exists(DB_PATH)) {
        return is_writable(DB_PATH);
    }

    return is_writable(__DIR__);
}

function formatWhatsApp($number) {
    return preg_replace('/[^0-9]/', '', (string) $number);
}

function normalizeInstagram($instagram) {
    return ltrim(trim((string) $instagram), '@');
}

function getFaturamentoValor($faturamento) {
    $map = [
        'ate_10k' => 10000,
        '10k_20k' => 20000,
        '20k_50k' => 50000,
        '50k_100k' => 100000,
        'acima_100k' => 150000,
    ];

    return $map[$faturamento] ?? 0;
}

function getFaturamentoLabels() {
    return [
        'ate_10k' => 'Ate R$ 10k',
        '10k_20k' => 'R$ 10k - R$ 20k',
        '20k_50k' => 'R$ 20k - R$ 50k',
        '50k_100k' => 'R$ 50k - R$ 100k',
        'acima_100k' => 'Acima de R$ 100k',
    ];
}

function getDesafioLabels() {
    return [
        'atendimento_lento' => 'Demora no atendimento',
        'perda_vendas' => 'Perdendo vendas fora do horario',
        'agendamento' => 'Gestao de agendamentos',
        'qualificacao' => 'Leads desqualificados',
        'escalar' => 'Dificuldade para escalar',
        'outro' => 'Outro',
    ];
}

function isHotLead($faturamento) {
    return in_array($faturamento, ['20k_50k', '50k_100k', 'acima_100k'], true);
}

// Quiz configuration
define('QUIZ_WEBHOOK_URL', getenv('QUIZ_WEBHOOK_URL') ?: 'https://chamalead.chamalead.com/prospeccao/enfileirar');
define('QUIZ_WEBHOOK_TIMEOUT', 10);
define('QUIZ_WEBHOOK_RETRIES', 3);

function ensureQuizSchema(SQLite3 $db) {
    if (!canWriteDatabase()) {
        return;
    }

    $db->exec(
        'CREATE TABLE IF NOT EXISTS quiz_leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id TEXT UNIQUE NOT NULL,
            nome TEXT,
            whatsapp TEXT,
            cargo TEXT,
            faturamento TEXT,
            faturamento_valor INTEGER DEFAULT 0,
            canal TEXT,
            volume_leads TEXT,
            dor_principal TEXT,
            dor_detalhe TEXT,
            timing TEXT,
            score INTEGER DEFAULT 0,
            classificacao TEXT DEFAULT \'frio\',
            trilha TEXT,
            utm_source TEXT,
            utm_medium TEXT,
            utm_campaign TEXT,
            utm_content TEXT,
            utm_term TEXT,
            status TEXT DEFAULT \'started\',
            current_step INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            webhook_sent_at DATETIME,
            webhook_response TEXT
        )'
    );

    $columns = [];
    $result = $db->query('PRAGMA table_info(quiz_leads)');
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $columns[$row['name']] = true;
    }

    $migrations = [
        'nome' => 'ALTER TABLE quiz_leads ADD COLUMN nome TEXT',
        'whatsapp' => 'ALTER TABLE quiz_leads ADD COLUMN whatsapp TEXT',
        'cargo' => 'ALTER TABLE quiz_leads ADD COLUMN cargo TEXT',
        'faturamento' => 'ALTER TABLE quiz_leads ADD COLUMN faturamento TEXT',
        'faturamento_valor' => 'ALTER TABLE quiz_leads ADD COLUMN faturamento_valor INTEGER DEFAULT 0',
        'canal' => 'ALTER TABLE quiz_leads ADD COLUMN canal TEXT',
        'volume_leads' => 'ALTER TABLE quiz_leads ADD COLUMN volume_leads TEXT',
        'dor_principal' => 'ALTER TABLE quiz_leads ADD COLUMN dor_principal TEXT',
        'dor_detalhe' => 'ALTER TABLE quiz_leads ADD COLUMN dor_detalhe TEXT',
        'timing' => 'ALTER TABLE quiz_leads ADD COLUMN timing TEXT',
        'score' => 'ALTER TABLE quiz_leads ADD COLUMN score INTEGER DEFAULT 0',
        'classificacao' => "ALTER TABLE quiz_leads ADD COLUMN classificacao TEXT DEFAULT 'frio'",
        'trilha' => 'ALTER TABLE quiz_leads ADD COLUMN trilha TEXT',
        'utm_source' => 'ALTER TABLE quiz_leads ADD COLUMN utm_source TEXT',
        'utm_medium' => 'ALTER TABLE quiz_leads ADD COLUMN utm_medium TEXT',
        'utm_campaign' => 'ALTER TABLE quiz_leads ADD COLUMN utm_campaign TEXT',
        'utm_content' => 'ALTER TABLE quiz_leads ADD COLUMN utm_content TEXT',
        'utm_term' => 'ALTER TABLE quiz_leads ADD COLUMN utm_term TEXT',
        'updated_at' => 'ALTER TABLE quiz_leads ADD COLUMN updated_at DATETIME',
        'created_at' => 'ALTER TABLE quiz_leads ADD COLUMN created_at DATETIME',
        'webhook_sent_at' => 'ALTER TABLE quiz_leads ADD COLUMN webhook_sent_at DATETIME',
        'webhook_response' => 'ALTER TABLE quiz_leads ADD COLUMN webhook_response TEXT',
    ];

    foreach ($migrations as $col => $sql) {
        if (!isset($columns[$col])) {
            @ $db->exec($sql);
        }
    }

    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_quiz_session ON quiz_leads(session_id)');
    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_quiz_status ON quiz_leads(status)');
    @ $db->exec('CREATE INDEX IF NOT EXISTS idx_quiz_score ON quiz_leads(score DESC)');
}

function calculateQuizScore($answers): array {
    $score = 0;

    $authorityScores = [
        'dono' => 3,
        'gestor' => 2,
        'time' => 1,
        'outro' => 1,
    ];
    $score += $authorityScores[$answers['cargo'] ?? ''] ?? 0;

    $revenueScores = [
        'ate_10k' => -1,
        '10k_20k' => 1,
        '20k_50k' => 3,
        '50k_100k' => 4,
        'acima_100k' => 5,
    ];
    $score += $revenueScores[$answers['faturamento'] ?? ''] ?? 0;

    $volumeScores = [
        '0_10' => 0,
        '11_30' => 1,
        '31_100' => 2,
        '100_mais' => 3,
    ];
    $score += $volumeScores[$answers['volume_leads'] ?? ''] ?? 0;

    $painScores = [
        'atendimento_lento' => 2,
        'fora_horario' => 2,
        'falta_followup' => 2,
        'prospeccao_inconsistente' => 2,
        'converte_mal' => 1,
        'organizacao_baguncada' => 1,
        'outro' => 0,
    ];
    $score += $painScores[$answers['dor_principal'] ?? ''] ?? 0;

    $timingScores = [
        'agora' => 3,
        'este_mes' => 2,
        'proximo_mes' => 1,
        'entendendo' => 0,
    ];
    $score += $timingScores[$answers['timing'] ?? ''] ?? 0;

    $classification = match (true) {
        $score >= 9 => 'quente',
        $score >= 5 => 'morno',
        default => 'frio',
    };

    return [
        'score' => $score,
        'classificacao' => $classification,
    ];
}

function determineTrack($faturamento): string {
    $revenueMap = [
        'ate_10k' => 10000,
        '10k_20k' => 20000,
        '20k_50k' => 50000,
        '50k_100k' => 100000,
        'acima_100k' => 150000,
    ];

    $value = $revenueMap[$faturamento] ?? 0;
    return $value <= 20000 ? 'consultiva' : 'acelerada';
}

function generateWebhookPayload($leadData): array {
    return [
        'event' => 'quiz_completed',
        'timestamp' => date('c'),
        'idempotency_key' => $leadData['session_id'],
        'lead' => [
            'session_id' => $leadData['session_id'],
            'nome' => $leadData['nome'] ?? '',
            'whatsapp' => $leadData['whatsapp'] ?? '',
            'cargo' => $leadData['cargo'] ?? '',
            'faturamento' => $leadData['faturamento'] ?? '',
            'faturamento_valor' => (int) ($leadData['faturamento_valor'] ?? 0),
            'canal' => $leadData['canal'] ?? '',
            'volume_leads' => $leadData['volume_leads'] ?? '',
            'dor_principal' => $leadData['dor_principal'] ?? '',
            'dor_detalhe' => $leadData['dor_detalhe'] ?? '',
            'timing' => $leadData['timing'] ?? '',
            'score' => (int) ($leadData['score'] ?? 0),
            'classificacao' => $leadData['classificacao'] ?? 'frio',
            'trilha' => $leadData['trilha'] ?? '',
            'utm_source' => $leadData['utm_source'] ?? '',
            'utm_medium' => $leadData['utm_medium'] ?? '',
            'utm_campaign' => $leadData['utm_campaign'] ?? '',
            'utm_content' => $leadData['utm_content'] ?? '',
            'utm_term' => $leadData['utm_term'] ?? '',
        ],
    ];
}

function sendWebhookWithRetry($payload): array {
    $url = QUIZ_WEBHOOK_URL;
    $maxRetries = QUIZ_WEBHOOK_RETRIES;
    $timeout = QUIZ_WEBHOOK_TIMEOUT;
    $response = null;
    $lastError = null;

    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        if ($attempt > 0) {
            usleep(pow(2, $attempt - 1) * 1000000);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Idempotency-Key: ' . $payload['idempotency_key'],
            ],
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $lastError = $curlError;
            continue;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'http_code' => $httpCode,
                'response' => $response,
                'attempts' => $attempt + 1,
            ];
        }

        $lastError = "HTTP {$httpCode}: {$response}";
    }

    return [
        'success' => false,
        'error' => $lastError,
        'attempts' => $maxRetries,
    ];
}

function validateQuizField($field, $value): array {
    $value = trim((string) $value);

    switch ($field) {
        case 'nome':
            if (strlen($value) < 2) {
                return ['valid' => false, 'error' => 'Nome deve ter pelo menos 2 caracteres'];
            }
            if (strlen($value) > 120) {
                return ['valid' => false, 'error' => 'Nome muito longo'];
            }
            return ['valid' => true];

        case 'whatsapp':
            $digits = preg_replace('/[^0-9]/', '', $value);
            if (strlen($digits) < 10 || strlen($digits) > 11) {
                return ['valid' => false, 'error' => 'Número de WhatsApp inválido'];
            }
            return ['valid' => true, 'clean' => $digits];

        case 'cargo':
            $valid = ['dono', 'gestor', 'time', 'outro'];
            if (!in_array($value, $valid, true)) {
                return ['valid' => false, 'error' => 'Cargo inválido'];
            }
            return ['valid' => true];

        case 'faturamento':
            $valid = ['ate_10k', '10k_20k', '20k_50k', '50k_100k', 'acima_100k'];
            if (!in_array($value, $valid, true)) {
                return ['valid' => false, 'error' => 'Faixa de faturamento inválida'];
            }
            return ['valid' => true];

        case 'canal':
            $valid = ['whatsapp_direto', 'instagram_whatsapp', 'trafego_pago', 'indicacao', 'prospeccao_ativa', 'varios_canais'];
            if (!in_array($value, $valid, true)) {
                return ['valid' => false, 'error' => 'Canal inválido'];
            }
            return ['valid' => true];

        case 'volume_leads':
            $valid = ['0_10', '11_30', '31_100', '100_mais'];
            if (!in_array($value, $valid, true)) {
                return ['valid' => false, 'error' => 'Volume inválido'];
            }
            return ['valid' => true];

        case 'dor_principal':
            $valid = [
                'atendimento_lento',
                'fora_horario',
                'falta_followup',
                'prospeccao_inconsistente',
                'converte_mal',
                'organizacao_baguncada',
                'outro',
            ];
            if (!in_array($value, $valid, true)) {
                return ['valid' => false, 'error' => 'Dor principal inválida'];
            }
            return ['valid' => true];

        case 'dor_detalhe':
            if (strlen($value) > 500) {
                return ['valid' => false, 'error' => 'Detalhe muito longo'];
            }
            return ['valid' => true];

        case 'timing':
            $valid = ['agora', 'este_mes', 'proximo_mes', 'entendendo'];
            if (!in_array($value, $valid, true)) {
                return ['valid' => false, 'error' => 'Timing inválido'];
            }
            return ['valid' => true];

        default:
            return ['valid' => true];
    }
}

function getStateFromDDD(string $ddd): ?string
{
    $dddToState = [
        '11' => 'SP', '12' => 'SP', '13' => 'SP', '14' => 'SP', '15' => 'SP',
        '16' => 'SP', '17' => 'SP', '18' => 'SP', '19' => 'SP',
        '21' => 'RJ', '22' => 'RJ', '24' => 'RJ',
        '31' => 'MG', '32' => 'MG', '33' => 'MG', '34' => 'MG', '35' => 'MG',
        '36' => 'MG', '37' => 'MG', '38' => 'MG',
        '41' => 'PR', '42' => 'PR', '43' => 'PR', '44' => 'PR', '45' => 'PR', '46' => 'PR',
        '47' => 'SC', '48' => 'SC', '49' => 'SC',
        '51' => 'RS', '52' => 'RS', '53' => 'RS', '54' => 'RS', '55' => 'RS',
        '61' => 'DF', '62' => 'GO', '63' => 'GO', '64' => 'GO',
        '65' => 'MT', '66' => 'MT', '67' => 'MT', '68' => 'MT',
        '69' => 'RO',
        '71' => 'BA', '72' => 'BA', '73' => 'BA', '74' => 'BA', '75' => 'BA', '77' => 'BA', '78' => 'BA',
        '79' => 'PI',
        '81' => 'PE', '82' => 'PB', '83' => 'PE', '84' => 'RN', '85' => 'CE', '86' => 'CE', '87' => 'CE', '88' => 'CE', '89' => 'PI',
        '91' => 'PA', '92' => 'AM', '93' => 'AM', '94' => 'PA', '95' => 'PA', '96' => 'PA', '97' => 'AM', '98' => 'AM', '99' => 'AM',
    ];

    return $dddToState[$ddd] ?? null;
}

function getStateNameFromDDD(string $ddd): ?string
{
    $dddToStateName = [
        '11' => 'São Paulo', '12' => 'São Paulo', '13' => 'São Paulo', '14' => 'São Paulo', '15' => 'São Paulo',
        '16' => 'São Paulo', '17' => 'São Paulo', '18' => 'São Paulo', '19' => 'São Paulo',
        '21' => 'Rio de Janeiro', '22' => 'Rio de Janeiro', '24' => 'Rio de Janeiro',
        '31' => 'Minas Gerais', '32' => 'Minas Gerais', '33' => 'Minas Gerais', '34' => 'Minas Gerais', '35' => 'Minas Gerais',
        '36' => 'Minas Gerais', '37' => 'Minas Gerais', '38' => 'Minas Gerais',
        '41' => 'Paraná', '42' => 'Paraná', '43' => 'Paraná', '44' => 'Paraná', '45' => 'Paraná', '46' => 'Paraná',
        '47' => 'Santa Catarina', '48' => 'Santa Catarina', '49' => 'Santa Catarina',
        '51' => 'Rio Grande do Sul', '52' => 'Rio Grande do Sul', '53' => 'Rio Grande do Sul', '54' => 'Rio Grande do Sul', '55' => 'Rio Grande do Sul',
        '61' => 'Distrito Federal', '62' => 'Goiás', '63' => 'Goiás', '64' => 'Goiás',
        '65' => 'Mato Grosso', '66' => 'Mato Grosso', '67' => 'Mato Grosso', '68' => 'Mato Grosso',
        '69' => 'Rondônia',
        '71' => 'Bahia', '72' => 'Bahia', '73' => 'Bahia', '74' => 'Bahia', '75' => 'Bahia', '77' => 'Bahia', '78' => 'Bahia',
        '79' => 'Piauí',
        '81' => 'Pernambuco', '82' => 'Paraíba', '83' => 'Pernambuco', '84' => 'Rio Grande do Norte', '85' => 'Ceará', '86' => 'Ceará', '87' => 'Ceará', '88' => 'Ceará', '89' => 'Piauí',
        '91' => 'Pará', '92' => 'Amazonas', '93' => 'Amazonas', '94' => 'Pará', '95' => 'Pará', '96' => 'Pará', '97' => 'Amazonas', '98' => 'Amazonas', '99' => 'Amazonas',
    ];

    return $dddToStateName[$ddd] ?? null;
}

function getCarrierFromDigits(string $digits): string
{
    if (strlen($digits) < 4) {
        return 'Desconhecida';
    }

    $prefix = substr($digits, 0, 4);
    $ddd = substr($digits, 0, 2);

    $carrierPatterns = [
        'Vivo' => ['9191', '9192', '9193', '9194', '9195', '9196', '9197', '9198', '9199', '2191', '2192', '2193', '2194', '2195', '2196', '2197', '2198', '2199', '4191', '4192', '4193', '4194', '4195', '4196', '4197', '4198', '4199', '5191', '5192', '5193', '5194', '5195', '5196', '5197', '5198', '5199', '6191', '6192', '6193', '6194', '6195', '6196', '6197', '6198', '6199', '7191', '7192', '7193', '7194', '7195', '7196', '7197', '7198', '7199', '8191', '8192', '8193', '8194', '8195', '8196', '8197', '8198', '8199', '9190', '9191', '9192', '9193', '9194', '9195', '9196', '9197', '9198', '9199'],
        'Claro' => ['2191', '2192', '2193', '2194', '2195', '2196', '2197', '2198', '2199', '4191', '4192', '4193', '4194', '4195', '4196', '4197', '4198', '4199', '5191', '5192', '5193', '5194', '5195', '5196', '5197', '5198', '5199', '6191', '6192', '6193', '6194', '6195', '6196', '6197', '6198', '6199', '7191', '7192', '7193', '7194', '7195', '7196', '7197', '7198', '7199', '8191', '8192', '8193', '8194', '8195', '8196', '8197', '8198', '8199', '9196', '9197', '9198', '9199'],
        'TIM' => ['2191', '2192', '2193', '2194', '2195', '4191', '4192', '4193', '4194', '4195', '4196', '5191', '5192', '5193', '5194', '6191', '6192', '6193', '6194', '6195', '6196', '7191', '7192', '8191', '8192', '8193', '8194', '8195', '9194', '9195', '9196', '9197'],
        'Oi' => ['3191', '3192', '3193', '3194', '3195', '3196', '3197', '3198', '3199', '2191', '2192', '2193', '2194', '2195', '2196', '2197', '2198', '2199', '5191', '5192', '5193', '5194', '5195', '5196', '5197', '5198', '5199', '6191', '6192', '6193', '6194', '6195', '6196', '7191', '7192', '7193', '7194', '7195', '7196', '7197', '7198', '7199', '8191', '8192', '8193', '8194', '8195', '8196', '8197', '8198', '8199', '9191', '9192', '9193', '9194', '9195'],
    ];

    foreach ($carrierPatterns as $carrier => $patterns) {
        if (in_array($prefix, $patterns, true)) {
            return $carrier;
        }
    }

    return 'Desconhecida';
}

function validatePhoneOffline(string $phone): array
{
    $digits = preg_replace('/[^0-9]/', '', $phone);

    if (strlen($digits) === 0) {
        return [
            'valid' => false,
            'error' => 'Telefone vazio',
        ];
    }

    if (strlen($digits) < 10) {
        return [
            'valid' => false,
            'error' => 'Número muito curto',
        ];
    }

    if (strlen($digits) > 11) {
        return [
            'valid' => false,
            'error' => 'Número muito longo',
        ];
    }

    $ddd = substr($digits, 0, 2);

    if ($ddd < '11' || $ddd > '99') {
        return [
            'valid' => false,
            'error' => 'DDD inválido',
        ];
    }

    $state = getStateFromDDD($ddd);
    $stateName = getStateNameFromDDD($ddd);
    $carrier = getCarrierFromDigits($digits);

    if (strlen($digits) === 10) {
        return [
            'valid' => true,
            'carrier' => $carrier,
            'line_type' => 'fixed',
            'state' => $state,
            'state_name' => $stateName,
        ];
    }

    if (strlen($digits) === 11) {
        $firstAfterDdd = $digits[2];

        if ($firstAfterDdd !== '9') {
            return [
                'valid' => false,
                'error' => 'Número de celular deve começar com 9',
                'line_type' => 'invalid',
                'state' => $state,
                'state_name' => $stateName,
            ];
        }

        return [
            'valid' => true,
            'carrier' => $carrier,
            'line_type' => 'mobile',
            'state' => $state,
            'state_name' => $stateName,
        ];
    }

    return [
        'valid' => false,
        'error' => 'Formato inválido',
    ];
}

function sendQuizLeadToEvolution(array $leadData): array {
    $configFile = __DIR__ . '/IA/config.env';
    if (!file_exists($configFile)) {
        return ['success' => false, 'error' => 'Configuracao de integracao nao encontrada'];
    }

    $config = [];
    foreach (explode("\n", file_get_contents($configFile)) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        $pos = strpos($line, '=');
        if ($pos !== false) {
            $config[trim(substr($line, 0, $pos))] = trim(substr($line, $pos + 1));
        }
    }

    $baseUrl = $config['BASE_URL'] ?? '';
    $webhookPath = $config['WEBHOOK_PATH'] ?? '';
    $instancesRaw = $config['INSTANCES'] ?? '';

    if ($baseUrl === '' || $webhookPath === '' || $instancesRaw === '') {
        return ['success' => false, 'error' => 'Configuracao de webhook incompleta'];
    }

    $instances = array_map('trim', explode(',', $instancesRaw));
    $instances = array_filter($instances, fn($v) => $v !== '');

    if (empty($instances)) {
        return ['success' => false, 'error' => 'Nenhuma instancia configurada'];
    }

    $instance = $instances[array_rand($instances)];

    $cargoLabels = [
        'dono' => 'Dono(a) / Socio(a)',
        'gestor' => 'Gestor(a) comercial',
        'time' => 'Parte do time',
        'outro' => 'Outro',
    ];
    $fatLabels = [
        'ate_10k' => 'Ate R$ 10k',
        '10k_20k' => 'R$ 10k - R$ 20k',
        '20k_50k' => 'R$ 20k - R$ 50k',
        '50k_100k' => 'R$ 50k - R$ 100k',
        'acima_100k' => 'Acima de R$ 100k',
    ];
    $canalLabels = [
        'whatsapp_direto' => 'WhatsApp direto',
        'instagram_whatsapp' => 'Instagram -> WhatsApp',
        'trafego_pago' => 'Traffego pago',
        'indicacao' => 'Indicacao',
        'prospeccao_ativa' => 'Prospeccao ativa',
        'varios_canais' => 'Varios canais misturados',
    ];
    $volumeLabels = [
        '0_10' => '0 a 10',
        '11_30' => '11 a 30',
        '31_100' => '31 a 100',
        '100_mais' => '100+',
    ];
    $dorLabels = [
        'atendimento_lento' => 'Demora no primeiro atendimento',
        'fora_horario' => 'Leads chegam fora do horario e ninguem responde',
        'falta_followup' => 'Falta de follow-up',
        'prospeccao_inconsistente' => 'Prospeccao nao acontece de forma consistente',
        'converte_mal' => 'O comercial conversa, mas converte mal',
        'organizacao_baguncada' => 'Agendamento / repasse / organizacao baguncados',
        'outro' => 'Outro',
    ];
    $timingLabels = [
        'agora' => 'Quero resolver agora',
        'este_mes' => 'Ainda neste mes',
        'proximo_mes' => 'Talvez no proximo mes',
        'entendendo' => 'So entendendo por enquanto',
    ];

    $parts = [];

    $parts[] = '=== QUIZ DIAGNOSTICO COMERCIAL - ChamaLead ===';
    $parts[] = 'LEAD QUALIFICADO via Quiz Interativo';
    $parts[] = 'Data/Horario: ' . ($leadData['created_at'] ?? date('Y-m-d H:i:s')) . ' (America/Sao Paulo)';
    $parts[] = 'Este lead completou o diagnostico comercial e demonstrou interesse real em resolver suas dores.';
    $parts[] = 'Use TODAS as informacoes abaixo para abordagem personalizada e eficiente.';
    $parts[] = '';

    $parts[] = '--- CONTATO DO LEAD ---';
    $parts[] = 'Nome: ' . ($leadData['nome'] ?? 'Nao informado');
    $parts[] = 'WhatsApp: ' . ($leadData['whatsapp'] ?? 'Nao informado');
    $parts[] = '';

    $parts[] = '--- TODAS RESPOSTAS DO QUIZ ---';
    $parts[] = '1. Cargo/Funcao: ' . ($cargoLabels[$leadData['cargo'] ?? ''] ?? $leadData['cargo'] ?? 'Nao informado');
    $parts[] = '2. Faturamento mensal: ' . ($fatLabels[$leadData['faturamento'] ?? ''] ?? $leadData['faturamento'] ?? 'Nao informado');
    $parts[] = '3. Canal de aquisicao: ' . ($canalLabels[$leadData['canal'] ?? ''] ?? $leadData['canal'] ?? 'Nao informado');
    $parts[] = '4. Volume de leads/semana: ' . ($volumeLabels[$leadData['volume_leads'] ?? ''] ?? $leadData['volume_leads'] ?? 'Nao informado');
    $parts[] = '5. DOR PRINCIPAL (maior dor): ' . ($dorLabels[$leadData['dor_principal'] ?? ''] ?? $leadData['dor_principal'] ?? 'Nao informada');
    if (!empty($leadData['dor_detalhe'])) {
        $parts[] = '   Detalhe adicional: ' . $leadData['dor_detalhe'];
    }
    $parts[] = '6. Urgencia/Timing: ' . ($timingLabels[$leadData['timing'] ?? ''] ?? $leadData['timing'] ?? 'Nao informado');
    $parts[] = '';

    $parts[] = '--- ORIGEM E FONTE (ONDE VEIO) ---';
    if (!empty($leadData['utm_source'])) {
        $parts[] = 'UTM Source (fonte): ' . $leadData['utm_source'];
    }
    if (!empty($leadData['utm_medium'])) {
        $parts[] = 'UTM Medium (medio): ' . $leadData['utm_medium'];
    }
    if (!empty($leadData['utm_campaign'])) {
        $parts[] = 'UTM Campaign (campanha): ' . $leadData['utm_campaign'];
    }
    if (!empty($leadData['utm_content'])) {
        $parts[] = 'UTM Content (conteudo): ' . $leadData['utm_content'];
    }
    if (!empty($leadData['utm_term'])) {
        $parts[] = 'UTM Term (termo): ' . $leadData['utm_term'];
    }
    if (!empty($leadData['referer'])) {
        $parts[] = 'Referer (pagina de origem): ' . $leadData['referer'];
    }
    if (empty($leadData['utm_source']) && empty($leadData['referer'])) {
        $parts[] = 'Origem: Acesso direto (digito a URL)';
    }
    $parts[] = '';

    $parts[] = '--- DADOS TECNICOS ---';
    $parts[] = 'IP: ' . ($leadData['client_ip_address'] ?? 'Nao identificado');
    $parts[] = 'User Agent: ' . (mb_strlen($leadData['client_user_agent'] ?? '') > 100 ? mb_substr($leadData['client_user_agent'], 0, 100) . '...' : ($leadData['client_user_agent'] ?? 'Nao identificado'));
    $parts[] = '';

    $parts[] = '--- PONTUACAO E CLASSIFICACAO ---';
    $score = (int) ($leadData['score'] ?? 0);
    $classification = $leadData['classificacao'] ?? 'frio';
    $trilha = $leadData['trilha'] ?? 'nao definida';

    $scoreInterpretation = match (true) {
        $score >= 15 => 'QUENTE - Alta prioridade! Gostou do diagnostico, tem faturamento, dor clara e urgencia. Aborde RAPIDO com proposta ou agendamento.',
        $score >= 10 => 'MORADO - Boa oportunidade. Tem potencial, mas precisa de nurturing. Mostre casos de sucesso e agende follow-up.',
        $score >= 5 => 'TIEPIDO - Interesse moderado. Continue educando com valor. Sem pressa, mantenha contato.',
        default => 'FRIO - Apenas iniziou. Eduque, mostre valor, seja paciente ate ele aquecer.',
    };

    $classificationLabel = $classification === 'quente' ? 'ALTA PRIORIDADE' : ($classification === 'morno' ? 'OPORTUNIDADE IDENTIFICADA' : 'EM ANALISE');

    $parts[] = 'Score: ' . $score . '/20';
    $parts[] = 'Classificacao: ' . strtoupper($classificationLabel);
    $parts[] = 'Trilha: ' . ucfirst($trilha);
    $parts[] = 'Score detalhado: ' . $score . '/20 = ' . $scoreInterpretation;

    $context = implode("\n", $parts);

    $phone = preg_replace('/[^0-9]/', '', (string) ($leadData['whatsapp'] ?? ''));
    if (strlen($phone) === 10) {
        $phone = '55' . $phone;
    } elseif (strlen($phone) === 11 && !str_starts_with($phone, '55')) {
        $phone = '55' . $phone;
    }

    $payload = json_encode([
        'telefone' => $phone,
        'contexto' => $context,
        'nome' => $leadData['nome'] ?? '',
    ], JSON_UNESCAPED_UNICODE);

    $webhookUrl = rtrim($baseUrl, '/') . $webhookPath;
    $maxRetries = 3;
    $lastError = null;

    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
        if ($attempt > 0) {
            usleep(pow(2, $attempt - 1) * 1000000);
        }

        $ch = curl_init($webhookUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            $lastError = $curlError;
            continue;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'http_code' => $httpCode,
                'instance' => $instance,
                'response' => $response,
                'attempts' => $attempt + 1,
            ];
        }

        $lastError = "HTTP {$httpCode}: {$response}";
    }

    return [
        'success' => false,
        'error' => $lastError,
        'instance' => $instance,
        'attempts' => $maxRetries,
    ];
}

function startAdminSession() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('chamalead_admin');
        session_start();
    }
}

function ensureAdminCsrfToken() {
    startAdminSession();
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['admin_csrf_token'];
}

function verifyAdminCsrfToken($token) {
    startAdminSession();
    if (empty($_SESSION['admin_csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['admin_csrf_token'], (string) $token);
}
?>
