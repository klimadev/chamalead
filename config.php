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
define('QUIZ_WEBHOOK_URL', getenv('QUIZ_WEBHOOK_URL') ?: 'https://webhook.site/your-unique-url');
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

    $parts[] = '=== ORIGEM: Quiz Diagnostico Comercial - ChamaLead ===';
    $parts[] = 'Este lead veio de um funil quiz interativo de diagnostico comercial.';
    $parts[] = 'Ele respondeu perguntas sobre cargo, faturamento, canal de aquisicao, volume de leads, dor principal e urgencia.';
    $parts[] = 'Use essas informacoes para personalizar a abordagem e mostrar que voce ja entende o contexto dele.';
    $parts[] = '';

    $parts[] = '--- DADOS DO LEAD ---';
    $parts[] = 'Nome: ' . ($leadData['nome'] ?? 'Nao informado');
    $parts[] = 'Cargo: ' . ($cargoLabels[$leadData['cargo'] ?? ''] ?? $leadData['cargo'] ?? 'Nao informado');
    $parts[] = 'Faturamento mensal: ' . ($fatLabels[$leadData['faturamento'] ?? ''] ?? $leadData['faturamento'] ?? 'Nao informado');
    $parts[] = 'Canal principal de aquisicao: ' . ($canalLabels[$leadData['canal'] ?? ''] ?? $leadData['canal'] ?? 'Nao informado');
    $parts[] = 'Leads novos por semana: ' . ($volumeLabels[$leadData['volume_leads'] ?? ''] ?? $leadData['volume_leads'] ?? 'Nao informado');
    $parts[] = 'Dor principal: ' . ($dorLabels[$leadData['dor_principal'] ?? ''] ?? $leadData['dor_principal'] ?? 'Nao informada');
    if (!empty($leadData['dor_detalhe'])) {
        $parts[] = 'Detalhe da dor: ' . $leadData['dor_detalhe'];
    }
    $parts[] = 'Urgencia: ' . ($timingLabels[$leadData['timing'] ?? ''] ?? $leadData['timing'] ?? 'Nao informado');

    $parts[] = '';
    $parts[] = '--- PONTUACAO E CLASSIFICACAO ---';
    $score = (int) ($leadData['score'] ?? 0);
    $classification = $leadData['classificacao'] ?? 'frio';
    $trilha = $leadData['trilha'] ?? 'nao definida';

    $scoreInterpretation = match (true) {
        $score >= 9 => 'LEAD QUENTE - Alta prioridade. Esse lead tem autoridade decisoria, bom faturamento, dor clara e urgencia. Aborde de forma direta e proativa, oferecendo uma conversa com especialista ou agendamento imediato.',
        $score >= 5 => 'LEAD MORNO - Oportunidade identificada. Tem bom potencial mas pode precisar de mais educacao ou nurturing. Mostre casos de sucesso e beneficios concretos da automacao.',
        default => 'LEAD FRIO - Em fase de entendimento. Nao seja agressivo. Foque em educar, mostrar valor e manter o relacionamento para o momento certo.',
    };

    $parts[] = 'Score: ' . $score . '/20';
    $parts[] = 'Classificacao: ' . strtoupper($classification);
    $parts[] = 'Trilha: ' . $trilha;
    $parts[] = 'Como abordar: ' . $scoreInterpretation;

    $parts[] = '';
    $parts[] = '--- INSTRUCOES PARA A IA ---';
    $parts[] = '1. Comece mencionando que ele fez o diagnostico comercial e que voce tem o resultado pra ele.';
    $parts[] = '2. Use a dor principal dele como ponto de entrada - mostre empatia e entendimento do problema.';
    $parts[] = '3. Se o faturamento for acima de R$ 20k, ele esta na trilha acelerada - foque em eficiencia e escala.';
    $parts[] = '4. Se o faturamento for ate R$ 20k, ele esta na trilha consultiva - foque em educacao e fundamentos.';
    $parts[] = '5. Se a urgencia for "agora" ou "este mes", priorize agendamento ou call com especialista.';
    $parts[] = '6. Se a dor for atendimento lento ou fora de horario, destaque a resposta instantanea 24/7.';
    $parts[] = '7. Se a dor for falta de follow-up, destaque a automacao de acompanhamento inteligente.';
    $parts[] = '8. Se a dor for prospeccao inconsistente, destaque a prospeccao automatica e continua.';
    $parts[] = '9. Nao seja generico - use os dados especificos que ele compartilhou no quiz.';
    $parts[] = '10. O objetivo e agendar uma conversa ou demonstracao, nao vender de imediato.';

    if (!empty($leadData['utm_source'])) {
        $parts[] = '';
        $parts[] = 'UTM Source: ' . $leadData['utm_source'];
    }
    if (!empty($leadData['utm_campaign'])) {
        $parts[] = 'UTM Campaign: ' . $leadData['utm_campaign'];
    }

    $context = implode("\n", $parts);

    $phone = preg_replace('/[^0-9]/', '', (string) ($leadData['whatsapp'] ?? ''));
    if (strlen($phone) === 10) {
        $phone = '55' . $phone;
    } elseif (strlen($phone) === 11 && !str_starts_with($phone, '55')) {
        $phone = '55' . $phone;
    }

    $payload = json_encode([
        'phone' => $phone,
        'context' => $context,
        'instance' => $instance,
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
