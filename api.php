<?php
require_once 'config.php';

header('Content-Type: application/json');

function apiResponse($statusCode, $payload) {
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    apiResponse(405, [
        'success' => false,
        'message' => 'Metodo nao permitido',
        'errors' => ['Use POST para enviar leads.'],
    ]);
}

$nome = trim($_POST['nome'] ?? '');
$empresa = trim($_POST['empresa'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$instagram = normalizeInstagram($_POST['instagram'] ?? '');
$faturamento = $_POST['faturamento'] ?? '';
$desafio = $_POST['desafio'] ?? '';

$errors = [];

if ($nome === '') {
    $errors[] = 'Nome completo e obrigatorio';
} elseif (mb_strlen($nome) < 2) {
    $errors[] = 'Nome completo deve ter ao menos 2 caracteres';
} elseif (mb_strlen($nome) > 120) {
    $errors[] = 'Nome completo muito longo';
}

if (empty($empresa)) {
    $errors[] = 'Nome da empresa e obrigatorio';
} elseif (mb_strlen($empresa) < 2) {
    $errors[] = 'Nome da empresa deve ter ao menos 2 caracteres';
} elseif (mb_strlen($empresa) > 120) {
    $errors[] = 'Nome da empresa muito longo';
}

if (empty($whatsapp)) {
    $errors[] = 'WhatsApp e obrigatorio';
} else {
    $whatsapp_clean = formatWhatsApp($whatsapp);
    if (strlen($whatsapp_clean) < 10 || strlen($whatsapp_clean) > 11) {
        $errors[] = 'WhatsApp invalido';
    }
}

$faturamentos_validos = ['ate_10k', '10k_20k', '20k_50k', '50k_100k', 'acima_100k'];
if ($faturamento === '' || !in_array($faturamento, $faturamentos_validos, true)) {
    $errors[] = 'Selecione uma faixa de faturamento valida';
}

$desafios_validos = array_keys(getDesafioLabels());
if ($desafio === '' || !in_array($desafio, $desafios_validos, true)) {
    $errors[] = 'Selecione um desafio valido';
}

if ($instagram !== '' && !preg_match('/^[A-Za-z0-9._]{1,30}$/', $instagram)) {
    $errors[] = 'Instagram invalido';
}

if (!empty($errors)) {
    apiResponse(422, [
        'success' => false,
        'message' => 'Formulario invalido',
        'errors' => $errors,
    ]);
}

$whatsapp_clean = formatWhatsApp($whatsapp);
$faturamento_valor = getFaturamentoValor($faturamento);

try {
    $db = getDB();

    $stmt = $db->prepare('
        INSERT INTO leads (
            nome,
            empresa,
            whatsapp,
            instagram,
            faturamento,
            faturamento_valor,
            desafio,
            status,
            created_at,
            updated_at
        )
        VALUES (
            :nome,
            :empresa,
            :whatsapp,
            :instagram,
            :faturamento,
            :faturamento_valor,
            :desafio,
            :status,
            datetime("now", "localtime"),
            datetime("now", "localtime")
        )
    ');

    $stmt->bindValue(':nome', $nome, SQLITE3_TEXT);
    $stmt->bindValue(':empresa', $empresa, SQLITE3_TEXT);
    $stmt->bindValue(':whatsapp', $whatsapp_clean, SQLITE3_TEXT);
    $stmt->bindValue(':instagram', $instagram, SQLITE3_TEXT);
    $stmt->bindValue(':faturamento', $faturamento, SQLITE3_TEXT);
    $stmt->bindValue(':faturamento_valor', $faturamento_valor, SQLITE3_INTEGER);
    $stmt->bindValue(':desafio', $desafio, SQLITE3_TEXT);
    $stmt->bindValue(':status', 'novo', SQLITE3_TEXT);

    if ($stmt->execute()) {
        apiResponse(201, [
            'success' => true,
            'message' => 'Lead cadastrado com sucesso',
            'id' => $db->lastInsertRowID(),
        ]);
    }

    throw new Exception('Falha ao inserir lead');
} catch (Exception $e) {
    apiResponse(500, [
        'success' => false,
        'message' => 'Erro interno do servidor',
        'errors' => ['Nao foi possivel salvar o lead agora.'],
    ]);
}
?>
