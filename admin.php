<?php
date_default_timezone_set('America/Sao_Paulo');
require_once 'config.php';

startAdminSession();

if (empty($_SESSION['admin_authenticated'])) {
    header('Location: admin-login.php');
    exit;
}

$db = getDB();
$csrfToken = ensureAdminCsrfToken();

$statusOptions = ['novo', 'contatado', 'arquivado'];
$flash = $_SESSION['admin_flash'] ?? ['type' => '', 'message' => ''];
unset($_SESSION['admin_flash']);

function redirectToSelf() {
    $target = $_SERVER['REQUEST_URI'] ?? 'admin.php';
    header('Location: ' . $target);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verifyAdminCsrfToken($csrf)) {
        $_SESSION['admin_flash'] = ['type' => 'error', 'message' => 'Token CSRF invalido. Recarregue a pagina e tente novamente.'];
        redirectToSelf();
    } else {
        try {
            if ($action === 'logout') {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
                session_destroy();
                header('Location: admin-login.php');
                exit;
            }

            if ($action === 'update_status') {
                $id = (int) ($_POST['id'] ?? 0);
                $newStatus = $_POST['status'] ?? '';

                if ($id < 1 || !in_array($newStatus, $statusOptions, true)) {
                    throw new RuntimeException('Dados invalidos para atualizacao de status.');
                }

                $stmt = $db->prepare('UPDATE leads SET status = :status, updated_at = datetime("now", "localtime") WHERE id = :id');
                $stmt->bindValue(':status', $newStatus, SQLITE3_TEXT);
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $stmt->execute();

                $_SESSION['admin_flash'] = ['type' => 'success', 'message' => 'Status atualizado com sucesso.'];
                redirectToSelf();
            }

            if ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);

                if ($id < 1) {
                    throw new RuntimeException('Lead invalido para exclusao.');
                }

                $stmt = $db->prepare('DELETE FROM leads WHERE id = :id');
                $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
                $stmt->execute();

                $_SESSION['admin_flash'] = ['type' => 'success', 'message' => 'Lead excluido com sucesso.'];
                redirectToSelf();
            }
        } catch (Throwable $exception) {
            $_SESSION['admin_flash'] = ['type' => 'error', 'message' => 'Nao foi possivel concluir a acao.'];
            redirectToSelf();
        }
    }
}

$filterStatus = $_GET['status'] ?? 'todos';
$filterSearch = trim($_GET['search'] ?? '');
$sortBy = $_GET['sort'] ?? 'newest';

$validFilterStatus = ['todos', 'novo', 'contatado', 'arquivado'];
if (!in_array($filterStatus, $validFilterStatus, true)) {
    $filterStatus = 'todos';
}

$validSorts = ['newest', 'oldest', 'faturamento_high', 'faturamento_low'];
if (!in_array($sortBy, $validSorts, true)) {
    $sortBy = 'newest';
}

$sql = 'SELECT * FROM leads WHERE 1=1';
$params = [];

if ($filterStatus !== 'todos') {
    $sql .= ' AND status = :status';
    $params[':status'] = $filterStatus;
}

if ($filterSearch !== '') {
    $sql .= ' AND (nome LIKE :search OR empresa LIKE :search OR whatsapp LIKE :search OR instagram LIKE :search OR desafio LIKE :search)';
    $params[':search'] = '%' . $filterSearch . '%';
}

switch ($sortBy) {
    case 'oldest':
        $sql .= ' ORDER BY created_at ASC';
        break;
    case 'faturamento_high':
        $sql .= ' ORDER BY faturamento_valor DESC, created_at DESC';
        break;
    case 'faturamento_low':
        $sql .= ' ORDER BY faturamento_valor ASC, created_at DESC';
        break;
    default:
        $sql .= ' ORDER BY created_at DESC';
        break;
}

$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, SQLITE3_TEXT);
}
$result = $stmt->execute();

$leads = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $leads[] = $row;
}

$total = (int) $db->querySingle('SELECT COUNT(*) FROM leads');
$novos = (int) $db->querySingle("SELECT COUNT(*) FROM leads WHERE status = 'novo'");
$contatados = (int) $db->querySingle("SELECT COUNT(*) FROM leads WHERE status = 'contatado'");
$hotLeads = (int) $db->querySingle("SELECT COUNT(*) FROM leads WHERE faturamento IN ('20k_50k', '50k_100k', 'acima_100k')");

$faturamentoLabels = getFaturamentoLabels();
$desafioLabels = getDesafioLabels();

function timeAgo($datetime) {
    if (!$datetime) {
        return 'Sem data';
    }

    $time = strtotime($datetime);
    if ($time === false) {
        return 'Data invalida';
    }

    $diff = time() - $time;

    if ($diff < 60) {
        return 'Agora';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' min atras';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . 'h atras';
    }
    if ($diff < 604800) {
        return floor($diff / 86400) . ' dias atras';
    }

    return date('d/m/Y', $time);
}

function leadDisplayName($lead) {
    $nome = trim((string) ($lead['nome'] ?? ''));
    if ($nome !== '') {
        return $nome;
    }

    $empresa = trim((string) ($lead['empresa'] ?? ''));
    if ($empresa !== '') {
        return $empresa;
    }

    return 'Lead sem identificacao';
}

function leadCompanyLine($lead) {
    $nome = trim((string) ($lead['nome'] ?? ''));
    $empresa = trim((string) ($lead['empresa'] ?? ''));

    if ($empresa === '') {
        return 'Empresa nao informada';
    }

    if ($nome === '' || mb_strtolower($nome) === mb_strtolower($empresa)) {
        return 'Sem nome no cadastro';
    }

    return $empresa;
}

function formatWhatsAppDisplay($number) {
    $digits = preg_replace('/[^0-9]/', '', (string) $number);

    if (strlen($digits) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $digits);
    }

    if (strlen($digits) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $digits);
    }

    return $digits !== '' ? $digits : 'Nao informado';
}

function formatInstagramDisplay($handle) {
    $normalized = ltrim(trim((string) $handle), '@');
    if ($normalized === '') {
        return 'Nao informado';
    }

    return '@' . $normalized;
}

function formatDesafioDisplay($value, $labels) {
    $desafio = trim((string) $value);
    if ($desafio === '') {
        return 'Nao preenchido (lead antigo)';
    }

    return $labels[$desafio] ?? $desafio;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChamaLead Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                    colors: {
                        flame: '#f97316',
                        ember: '#ea580c',
                        coal: '#09090b',
                    },
                },
            },
        };
    </script>
    <style>
        :root {
            --focus-ring: rgba(249, 115, 22, 0.42);
        }

        body {
            background:
                radial-gradient(circle at 15% 20%, rgba(249, 115, 22, 0.16), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(234, 88, 12, 0.18), transparent 42%),
                #09090b;
        }

        .glass {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(18px);
        }

        .lead-card {
            background: linear-gradient(140deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: transform .2s ease, border-color .2s ease;
        }

        .lead-card:hover {
            transform: translateY(-2px);
            border-color: rgba(249, 115, 22, 0.45);
        }

        .focus-ring:focus-visible {
            outline: 2px solid transparent;
            box-shadow: 0 0 0 4px var(--focus-ring);
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen text-zinc-100 antialiased">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <header class="glass rounded-3xl p-6 sm:p-8 mb-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-orange-300/90">ChamaLead</p>
                    <h1 class="font-display text-3xl sm:text-4xl font-bold mt-2">Painel de Triagem</h1>
                    <p class="text-zinc-400 mt-2">Fila viva de leads com acoes rapidas para contato e qualificacao.</p>
                </div>
                <form method="POST" class="flex items-center gap-3">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="focus-ring inline-flex items-center gap-2 rounded-xl border border-white/20 px-4 py-2 text-sm font-semibold text-zinc-200 hover:border-orange-400 hover:text-white">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Sair
                    </button>
                </form>
            </div>
        </header>

        <?php if ($flash['message'] !== ''): ?>
            <div class="mb-6 rounded-2xl border px-4 py-3 text-sm <?php echo $flash['type'] === 'success' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200' : 'border-red-500/30 bg-red-500/10 text-red-200'; ?>" role="status" aria-live="polite">
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
            <article class="glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wider text-zinc-400">Total</p>
                <p class="text-3xl font-display font-bold mt-2"><?php echo $total; ?></p>
            </article>
            <article class="glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wider text-zinc-400">Novos</p>
                <p class="text-3xl font-display font-bold mt-2 text-emerald-300"><?php echo $novos; ?></p>
            </article>
            <article class="glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wider text-zinc-400">Contatados</p>
                <p class="text-3xl font-display font-bold mt-2 text-sky-300"><?php echo $contatados; ?></p>
            </article>
            <article class="glass rounded-2xl p-5">
                <p class="text-xs uppercase tracking-wider text-zinc-400">Hot Leads</p>
                <p class="text-3xl font-display font-bold mt-2 text-orange-300"><?php echo $hotLeads; ?></p>
            </article>
        </section>

        <section class="glass rounded-2xl p-5 mb-8">
            <form method="GET" class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end" aria-label="Filtros de leads">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-xs uppercase tracking-wider text-zinc-400 mb-2">Busca</label>
                    <input id="search" name="search" type="text" value="<?php echo htmlspecialchars($filterSearch); ?>" placeholder="Nome, empresa, whatsapp ou desafio" class="focus-ring w-full rounded-xl border border-white/15 bg-black/30 px-4 py-3 text-sm placeholder-zinc-500">
                </div>
                <div>
                    <label for="status" class="block text-xs uppercase tracking-wider text-zinc-400 mb-2">Status</label>
                    <select id="status" name="status" class="focus-ring w-full rounded-xl border border-white/15 bg-black/30 px-4 py-3 text-sm">
                        <option value="todos" <?php echo $filterStatus === 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="novo" <?php echo $filterStatus === 'novo' ? 'selected' : ''; ?>>Novos</option>
                        <option value="contatado" <?php echo $filterStatus === 'contatado' ? 'selected' : ''; ?>>Contatados</option>
                        <option value="arquivado" <?php echo $filterStatus === 'arquivado' ? 'selected' : ''; ?>>Arquivados</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-xs uppercase tracking-wider text-zinc-400 mb-2">Ordenar</label>
                    <select id="sort" name="sort" class="focus-ring w-full rounded-xl border border-white/15 bg-black/30 px-4 py-3 text-sm">
                        <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Mais recentes</option>
                        <option value="oldest" <?php echo $sortBy === 'oldest' ? 'selected' : ''; ?>>Mais antigos</option>
                        <option value="faturamento_high" <?php echo $sortBy === 'faturamento_high' ? 'selected' : ''; ?>>Maior faturamento</option>
                        <option value="faturamento_low" <?php echo $sortBy === 'faturamento_low' ? 'selected' : ''; ?>>Menor faturamento</option>
                    </select>
                </div>
                <button type="submit" class="focus-ring rounded-xl bg-gradient-to-r from-flame to-ember px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-900/30 hover:translate-y-[-1px]">
                    Aplicar filtros
                </button>
            </form>
        </section>

        <section aria-label="Lista de leads">
            <?php if (empty($leads)): ?>
                <div class="glass rounded-3xl p-12 text-center">
                    <p class="text-xl font-semibold">Nenhum lead encontrado</p>
                    <p class="text-zinc-400 mt-2">Ajuste os filtros ou aguarde novos envios da landing page.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    <?php foreach ($leads as $lead):
                        $isHot = isHotLead((string) ($lead['faturamento'] ?? ''));
                        $whatsappDigits = preg_replace('/[^0-9]/', '', (string) ($lead['whatsapp'] ?? ''));
                        $whatsappDisplay = formatWhatsAppDisplay((string) ($lead['whatsapp'] ?? ''));
                        $instagramDisplay = formatInstagramDisplay((string) ($lead['instagram'] ?? ''));
                        $desafioDisplay = formatDesafioDisplay((string) ($lead['desafio'] ?? ''), $desafioLabels);
                        $leadName = leadDisplayName($lead);
                        $companyLine = leadCompanyLine($lead);
                        $status = $lead['status'] ?? 'novo';
                    ?>
                        <article class="lead-card rounded-2xl p-5">
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="min-w-0">
                                    <h2 class="text-lg font-semibold truncate"><?php echo htmlspecialchars($leadName); ?></h2>
                                    <p class="text-zinc-300 truncate"><?php echo htmlspecialchars($companyLine); ?></p>
                                    <p class="text-xs text-zinc-500 mt-1"><?php echo timeAgo($lead['created_at']); ?></p>
                                </div>
                                <div class="flex flex-col gap-2 items-end">
                                    <?php if ($isHot): ?>
                                        <span class="rounded-full border border-orange-400/30 bg-orange-500/15 px-2.5 py-1 text-[11px] font-semibold text-orange-200">Hot</span>
                                    <?php endif; ?>
                                    <span class="rounded-full border px-2.5 py-1 text-[11px] font-semibold <?php echo $status === 'novo' ? 'border-emerald-500/30 bg-emerald-500/15 text-emerald-200' : ($status === 'contatado' ? 'border-sky-500/30 bg-sky-500/15 text-sky-200' : 'border-zinc-500/30 bg-zinc-500/15 text-zinc-300'); ?>">
                                        <?php echo htmlspecialchars(ucfirst($status)); ?>
                                    </span>
                                </div>
                            </div>

                            <dl class="space-y-2 text-sm mb-5">
                                <div class="flex justify-between gap-4">
                                    <dt class="text-zinc-500">WhatsApp</dt>
                                    <dd class="text-zinc-200 font-medium"><?php echo htmlspecialchars($whatsappDisplay); ?></dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-zinc-500">Instagram</dt>
                                    <dd class="text-zinc-200"><?php echo htmlspecialchars($instagramDisplay); ?></dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-zinc-500">Faturamento</dt>
                                    <dd class="text-zinc-200"><?php echo htmlspecialchars($faturamentoLabels[$lead['faturamento']] ?? $lead['faturamento']); ?></dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-zinc-500">Desafio</dt>
                                    <dd class="text-zinc-200 text-right max-w-[60%]"><?php echo htmlspecialchars($desafioDisplay); ?></dd>
                                </div>
                            </dl>

                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <a href="https://wa.me/55<?php echo htmlspecialchars($whatsappDigits); ?>?text=<?php echo urlencode('Oi, aqui e da ChamaLead! Vi seu cadastro e vou te ajudar com a automacao.'); ?>" target="_blank" rel="noopener noreferrer" class="focus-ring inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-200 hover:bg-emerald-500/20">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                    WhatsApp
                                </a>
                                <button type="button" class="focus-ring copy-btn inline-flex items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/5 px-3 py-2 text-xs font-semibold hover:bg-white/10" data-copy="<?php echo htmlspecialchars($leadName . ' | ' . $companyLine . ' | ' . $whatsappDisplay . ' | ' . $desafioDisplay); ?>">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                    Copiar
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id" value="<?php echo (int) $lead['id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $status === 'novo' ? 'contatado' : 'novo'; ?>">
                                    <button type="submit" class="focus-ring w-full rounded-xl border border-sky-400/30 bg-sky-500/10 px-3 py-2 text-xs font-semibold text-sky-200 hover:bg-sky-500/20">
                                        <?php echo $status === 'novo' ? 'Marcar contatado' : 'Reabrir lead'; ?>
                                    </button>
                                </form>

                                <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este lead?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int) $lead['id']; ?>">
                                    <button type="submit" class="focus-ring w-full rounded-xl border border-red-400/30 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-200 hover:bg-red-500/20">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        lucide.createIcons();

        document.querySelectorAll('.copy-btn').forEach((button) => {
            button.addEventListener('click', async () => {
                const text = button.dataset.copy || '';
                try {
                    await navigator.clipboard.writeText(text);
                    button.textContent = 'Copiado!';
                    setTimeout(() => {
                        button.innerHTML = '<i data-lucide="copy" class="w-4 h-4"></i>Copiar';
                        lucide.createIcons();
                    }, 900);
                } catch (error) {
                    button.textContent = 'Falhou ao copiar';
                }
            });
        });
    </script>
</body>
</html>
