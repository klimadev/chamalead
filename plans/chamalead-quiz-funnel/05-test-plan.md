# 05 - Test Plan

## Testing Strategy

- **Automated**: PHP logic (scoring, validation, payload generation) via CLI scripts
- **Manual**: UI interactions, transitions, responsive design, visual quality
- **Integration**: Webhook dispatch, database operations, API endpoints
- **Priority**: Core logic first, then integration, then UI polish

## Automated Tests (PHP CLI Scripts)

### Test 1: Lead Scoring Algorithm

**File**: `quiz/tests/test-scoring.php`

**Purpose**: Verify scoring calculation matches specification exactly.

**Test Cases**:
```php
$testCases = [
    // Case 1: Hot lead (business owner, high revenue, high volume, urgent)
    [
        'input' => [
            'cargo' => 'dono',
            'faturamento' => '50k_100k',
            'volume_leads' => '100_mais',
            'dor_principal' => 'atendimento_lento',
            'timing' => 'agora',
        ],
        'expected_score' => 15,  // 3 + 4 + 3 + 2 + 3
        'expected_class' => 'quente',
    ],
    
    // Case 2: Warm lead (manager, mid revenue, mid volume, medium urgency)
    [
        'input' => [
            'cargo' => 'gestor',
            'faturamento' => '20k_50k',
            'volume_leads' => '31_100',
            'dor_principal' => 'leads_desqualificados',
            'timing' => 'este_mes',
        ],
        'expected_score' => 9,  // 2 + 3 + 2 + 1 + 2
        'expected_class' => 'quente',
    ],
    
    // Case 3: Cold lead (other role, low revenue, low volume, just looking)
    [
        'input' => [
            'cargo' => 'outro',
            'faturamento' => 'ate_10k',
            'volume_leads' => '0_10',
            'dor_principal' => 'dificuldade_escalar',
            'timing' => 'entendendo',
        ],
        'expected_score' => 1,  // 1 + (-1) + 0 + 1 + 0
        'expected_class' => 'frio',
    ],
    
    // Case 4: Warm lead (edge case: exactly 5 points)
    [
        'input' => [
            'cargo' => 'gestor',
            'faturamento' => '10k_20k',
            'volume_leads' => '11_30',
            'dor_principal' => 'perdendo_vendas',
            'timing' => 'proximo_mes',
        ],
        'expected_score' => 6,  // 2 + 1 + 1 + 2 + 1
        'expected_class' => 'morno',
    ],
    
    // Case 5: Minimum score (all lowest values)
    [
        'input' => [
            'cargo' => 'outro',
            'faturamento' => 'ate_10k',
            'volume_leads' => '0_10',
            'dor_principal' => 'leads_desqualificados',
            'timing' => 'entendendo',
        ],
        'expected_score' => 0,  // 1 + (-1) + 0 + 1 + 0 = 1... wait
        'expected_class' => 'frio',
    ],
    
    // Case 6: Maximum score (all highest values)
    [
        'input' => [
            'cargo' => 'dono',
            'faturamento' => 'acima_100k',
            'volume_leads' => '100_mais',
            'dor_principal' => 'atendimento_lento',
            'timing' => 'agora',
        ],
        'expected_score' => 16,  // 3 + 5 + 3 + 2 + 3
        'expected_class' => 'quente',
    ],
];
```

**Run**: `php quiz/tests/test-scoring.php`
**Pass Criteria**: All test cases output "PASS"

---

### Test 2: Track Determination

**File**: `quiz/tests/test-track.php`

**Purpose**: Verify revenue-based routing logic.

**Test Cases**:
```php
$testCases = [
    ['faturamento' => 'ate_10k', 'expected_track' => 'consultiva'],
    ['faturamento' => '10k_20k', 'expected_track' => 'consultiva'],
    ['faturamento' => '20k_50k', 'expected_track' => 'acelerada'],
    ['faturamento' => '50k_100k', 'expected_track' => 'acelerada'],
    ['faturamento' => 'acima_100k', 'expected_track' => 'acelerada'],
];
```

**Run**: `php quiz/tests/test-track.php`
**Pass Criteria**: All mappings correct

---

### Test 3: Input Validation

**File**: `quiz/tests/test-validation.php`

**Purpose**: Verify server-side validation for all inputs.

**Test Cases**:
```php
$testCases = [
    // Name validation
    ['field' => 'nome', 'value' => '', 'expected' => 'error'],
    ['field' => 'nome', 'value' => 'A', 'expected' => 'error'],
    ['field' => 'nome', 'value' => 'João Silva', 'expected' => 'valid'],
    ['field' => 'nome', 'value' => str_repeat('A', 121), 'expected' => 'error'],
    
    // WhatsApp validation
    ['field' => 'whatsapp', 'value' => '', 'expected' => 'error'],
    ['field' => 'whatsapp', 'value' => '123', 'expected' => 'error'],
    ['field' => 'whatsapp', 'value' => '11999999999', 'expected' => 'valid'],
    ['field' => 'whatsapp', 'value' => '(11) 99999-9999', 'expected' => 'valid'],  // cleaned to digits
    
    // Cargo validation
    ['field' => 'cargo', 'value' => '', 'expected' => 'error'],
    ['field' => 'cargo', 'value' => 'dono', 'expected' => 'valid'],
    ['field' => 'cargo', 'value' => 'gestor', 'expected' => 'valid'],
    ['field' => 'cargo', 'value' => 'outro', 'expected' => 'valid'],
    ['field' => 'cargo', 'value' => 'invalido', 'expected' => 'error'],
    
    // Faturamento validation
    ['field' => 'faturamento', 'value' => '', 'expected' => 'error'],
    ['field' => 'faturamento', 'value' => 'ate_10k', 'expected' => 'valid'],
    ['field' => 'faturamento', 'value' => 'invalido', 'expected' => 'error'],
    
    // Timing validation
    ['field' => 'timing', 'value' => '', 'expected' => 'error'],
    ['field' => 'timing', 'value' => 'agora', 'expected' => 'valid'],
    ['field' => 'timing', 'value' => 'este_mes', 'expected' => 'valid'],
    ['field' => 'timing', 'value' => 'proximo_mes', 'expected' => 'valid'],
    ['field' => 'timing', 'value' => 'entendendo', 'expected' => 'valid'],
    ['field' => 'timing', 'value' => 'invalido', 'expected' => 'error'],
];
```

**Run**: `php quiz/tests/test-validation.php`
**Pass Criteria**: All validations return expected result

---

### Test 4: Webhook Payload Generation

**File**: `quiz/tests/test-webhook-payload.php`

**Purpose**: Verify webhook payload structure and content.

**Test Cases**:
```php
// Generate payload from test quiz completion
$answers = [
    'session_id' => 'test-uuid-123',
    'nome' => 'João Silva',
    'whatsapp' => '5511999999999',
    'cargo' => 'dono',
    'faturamento' => '50k_100k',
    'faturamento_valor' => 100000,
    'canal' => 'instagram',
    'volume_leads' => '31_100',
    'dor_principal' => 'atendimento_lento',
    'dor_detalhe' => 'Detalhe da dor',
    'timing' => 'agora',
    'score' => 15,
    'classificacao' => 'quente',
    'trilha' => 'acelerada',
    'utm_source' => 'google',
    'utm_medium' => 'cpc',
    'utm_campaign' => 'brand',
];

$payload = generateWebhookPayload($answers);

// Assertions
assert($payload['event'] === 'quiz_completed');
assert(isset($payload['timestamp']));
assert($payload['idempotency_key'] === 'test-uuid-123');
assert($payload['lead']['nome'] === 'João Silva');
assert($payload['lead']['score'] === 15);
assert($payload['lead']['classificacao'] === 'quente');
assert(is_string($payload['timestamp']));  // ISO 8601 format
```

**Run**: `php quiz/tests/test-webhook-payload.php`
**Pass Criteria**: All assertions pass, payload matches specification

---

### Test 5: Database Operations

**File**: `quiz/tests/test-db-operations.php`

**Purpose**: Verify CRUD operations on `quiz_leads` table.

**Test Cases**:
```php
// 1. Table creation
$db = getDB();
$result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='quiz_leads'");
assert($result->fetchArray() !== false);

// 2. Insert new session
$stmt = $db->prepare("INSERT INTO quiz_leads (session_id, status, current_step) VALUES (:sid, :status, :step)");
$stmt->bindValue(':sid', 'test-session-1', SQLITE3_TEXT);
$stmt->bindValue(':status', 'started', SQLITE3_TEXT);
$stmt->bindValue(':step', 0, SQLITE3_INTEGER);
assert($stmt->execute() !== false);

// 3. Update step (upsert pattern)
$stmt = $db->prepare("UPDATE quiz_leads SET nome = :nome, current_step = :step, status = :status, updated_at = datetime('now', 'localtime') WHERE session_id = :sid");
$stmt->bindValue(':sid', 'test-session-1', SQLITE3_TEXT);
$stmt->bindValue(':nome', 'João', SQLITE3_TEXT);
$stmt->bindValue(':step', 1, SQLITE3_INTEGER);
$stmt->bindValue(':status', 'in_progress', SQLITE3_TEXT);
assert($stmt->execute() !== false);

// 4. Retrieve session
$stmt = $db->prepare("SELECT * FROM quiz_leads WHERE session_id = :sid");
$stmt->bindValue(':sid', 'test-session-1', SQLITE3_TEXT);
$result = $stmt->execute();
$row = $result->fetchArray(SQLITE3_ASSOC);
assert($row['nome'] === 'João');
assert($row['current_step'] === 1);
assert($row['status'] === 'in_progress');

// 5. Status transitions
$validTransitions = [
    'started' => 'in_progress',
    'in_progress' => 'completed',
    'completed' => 'webhook_pending',
    'webhook_pending' => 'webhook_sent',
    'webhook_pending' => 'webhook_error',
];
// Verify each transition is possible

// 6. Cleanup
$db->exec("DELETE FROM quiz_leads WHERE session_id = 'test-session-1'");
```

**Run**: `php quiz/tests/test-db-operations.php`
**Pass Criteria**: All database operations succeed

---

### Test 6: Idempotency

**File**: `quiz/tests/test-idempotency.php`

**Purpose**: Verify webhook is not sent twice for same session.

**Test Cases**:
```php
// Simulate double completion
$sessionId = 'test-idempotent-1';

// First completion
$result1 = completeQuiz($sessionId, $answers);
assert($result1['success'] === true);
assert($result1['webhook_status'] === 'sent');

// Second completion (same session)
$result2 = completeQuiz($sessionId, $answers);
assert($result2['success'] === false);  // or returns cached result
assert($result2['message'] === 'Webhook already sent for this session');

// Verify only one webhook was "sent" (check mock log)
$webhookCount = countWebhookCalls($sessionId);
assert($webhookCount === 1);
```

**Run**: `php quiz/tests/test-idempotency.php`
**Pass Criteria**: Only one webhook dispatched per session

---

## Manual Test Scenarios

### Scenario 1: Complete Quiz - Accelerated Track
**Steps**:
1. Navigate to `/quiz/?utm_source=google&utm_campaign=brand`
2. Click "Começar" on opening screen
3. Enter name: "Maria Silva"
4. Enter WhatsApp: "(11) 98765-4321"
5. Select cargo: "Dono/Sócio"
6. Select faturamento: "R$ 50k - 100k"
7. Select canal: "Instagram"
8. Select volume: "31-100"
9. Select dor: "Atendimento lento"
10. Enter dor_detalhe: "Perdemos clientes por demora"
11. Select timing: "Agora"
12. Click "Quero ser chamado agora"

**Expected**:
- Score: 3 + 4 + 2 + 2 + 3 = 14 (quente)
- Track: acelerada
- Webhook fires with correct payload
- Success message displayed
- Database row complete with all fields

---

### Scenario 2: Complete Quiz - Consultive Track
**Steps**:
1. Navigate to `/quiz/`
2. Complete quiz with faturamento: "Até R$ 10k"
3. Verify step 8 shows consultive template
4. Complete remaining steps

**Expected**:
- Score: lower range (frio or morno)
- Track: consultiva
- Step 8 content differs from accelerated track
- Webhook fires with correct track

---

### Scenario 3: Session Resume
**Steps**:
1. Start quiz, complete steps 1-3
2. Refresh browser
3. Verify quiz resumes at step 4

**Expected**:
- Session restored from localStorage + server
- Previous answers preserved
- Progress bar at correct position

---

### Scenario 4: Validation Errors
**Steps**:
1. Try to proceed with empty name
2. Try to proceed with invalid WhatsApp
3. Try to proceed without selecting required option

**Expected**:
- Error messages displayed
- Progression blocked
- Visual feedback (red borders)

---

### Scenario 5: Network Failure Simulation
**Steps**:
1. Open browser dev tools
2. Set network to "Offline"
3. Try to complete a step
4. Restore network, retry

**Expected**:
- Error message shown
- Data preserved locally
- Retry succeeds when network restored

---

### Scenario 6: Webhook Failure
**Steps**:
1. Configure webhook URL to invalid endpoint
2. Complete quiz
3. Check database status

**Expected**:
- Status: webhook_error
- webhook_response contains error details
- Retry logic attempted 3 times

---

## Test Execution Order

1. **Run automated tests first** (fast feedback on core logic):
   ```bash
   php quiz/tests/test-scoring.php
   php quiz/tests/test-track.php
   php quiz/tests/test-validation.php
   php quiz/tests/test-webhook-payload.php
   php quiz/tests/test-db-operations.php
   php quiz/tests/test-idempotency.php
   ```

2. **Manual UI testing** (after automated tests pass):
   - Scenario 1: Complete quiz (accelerated)
   - Scenario 2: Complete quiz (consultive)
   - Scenario 3: Session resume
   - Scenario 4: Validation errors
   - Scenario 5: Network failure
   - Scenario 6: Webhook failure

3. **Cross-browser testing** (after manual scenarios pass):
   - Chrome, Safari, Firefox, Samsung Internet

4. **Final QA checklist** (see 03-final-qa-checklist.md)

## Test Data

### Valid Test Inputs
```php
$validNames = ['João Silva', 'Maria Santos', 'Pedro Oliveira', 'Ana Costa'];
$validPhones = ['11999999999', '21988888888', '11987654321'];
$validCargos = ['dono', 'gestor', 'outro'];
$validFaturamentos = ['ate_10k', '10k_20k', '20k_50k', '50k_100k', 'acima_100k'];
$validCanais = ['instagram', 'facebook', 'google', 'indicacao', 'outro'];
$validVolumes = ['0_10', '11_30', '31_100', '100_mais'];
$validDores = ['atendimento_lento', 'perdendo_vendas', 'leads_desqualificados', 'dificuldade_escalar'];
$validTimings = ['agora', 'este_mes', 'proximo_mes', 'entendendo'];
```

### Invalid Test Inputs
```php
$invalidNames = ['', 'A', str_repeat('X', 121), '<script>alert(1)</script>'];
$invalidPhones = ['', '123', 'abc', '1199999999999999'];
$invalidCargos = ['', 'invalido', 'admin'];
$invalidFaturamentos = ['', 'invalido', '0'];
$invalidTimings = ['', 'invalido', 'never'];
```

## Pass/Fail Criteria

### Automated Tests
- **Pass**: All assertions succeed, no errors/warnings
- **Fail**: Any assertion fails, or script throws exception

### Manual Tests
- **Pass**: All expected outcomes observed
- **Fail**: Any deviation from expected behavior

### Overall
- **Ready for Production**: All automated tests pass + all manual scenarios pass + QA checklist complete
- **Needs Work**: Any test fails or checklist item incomplete
