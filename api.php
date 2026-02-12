<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

$empresa = trim($_POST['empresa'] ?? '');
$whatsapp = trim($_POST['whatsapp'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');
$faturamento = $_POST['faturamento'] ?? '';

// Validações
$errors = [];

if (empty($empresa)) {
    $errors[] = 'Nome da empresa é obrigatório';
} elseif (strlen($empresa) < 2) {
    $errors[] = 'Nome da empresa deve ter pelo menos 2 caracteres';
}

if (empty($whatsapp)) {
    $errors[] = 'WhatsApp é obrigatório';
} else {
    $whatsapp_clean = preg_replace('/[^0-9]/', '', $whatsapp);
    if (strlen($whatsapp_clean) < 10 || strlen($whatsapp_clean) > 11) {
        $errors[] = 'WhatsApp inválido';
    }
}

$faturamentos_validos = ['ate_10k', '10k_20k', '20k_50k', '50k_100k', 'acima_100k'];
if (empty($faturamento) || !in_array($faturamento, $faturamentos_validos)) {
    $errors[] = 'Selecione uma faixa de faturamento válida';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => implode(', ', $errors)]);
    exit;
}

// Formatação
$whatsapp_clean = formatWhatsApp($whatsapp);
$faturamento_valor = getFaturamentoValor($faturamento);

// Salvar no banco
try {
    $db = getDB();
    
    $stmt = $db->prepare('
        INSERT INTO leads (empresa, whatsapp, instagram, faturamento, faturamento_valor, created_at)
        VALUES (:empresa, :whatsapp, :instagram, :faturamento, :faturamento_valor, datetime("now", "localtime"))
    ');
    
    $stmt->bindValue(':empresa', $empresa, SQLITE3_TEXT);
    $stmt->bindValue(':whatsapp', $whatsapp_clean, SQLITE3_TEXT);
    $stmt->bindValue(':instagram', $instagram ? ltrim($instagram, '@') : '', SQLITE3_TEXT);
    $stmt->bindValue(':faturamento', $faturamento, SQLITE3_TEXT);
    $stmt->bindValue(':faturamento_valor', $faturamento_valor, SQLITE3_INTEGER);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Lead cadastrado com sucesso',
            'id' => $db->lastInsertRowID()
        ]);
    } else {
        throw new Exception('Erro ao inserir no banco');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno do servidor']);
}
?>