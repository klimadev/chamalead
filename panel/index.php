<?php
/**
 * Premium Panel Dashboard
 *
 * Modern dashboard interface for managing Evolution API instances.
 *
 * @package Panel
 * @author Chamalead
 * @version 3.0.0
 */

require_once 'auth.php';
require_once 'EvolutionApiService.php';
require_once 'Logger.php';
require_once 'Modal.php';

// Security Headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

redirect_if_not_auth();

// Auto backup database if needed
auto_backup_if_needed();

// Initialize API service with caching
$api = new EvolutionApiService();

// Fetch instances with caching
$fetchResult = $api->fetchInstances();
$apiError = false;
$instances = [];

if (isset($fetchResult['success'])) {
    $apiError = !$fetchResult['success'];
    $instances = $fetchResult['data'] ?? [];
} else {
    $instances = is_array($fetchResult) ? $fetchResult : [];
}

// Calculate statistics
$total = count($instances);
$online = 0;
foreach ($instances as $inst) {
    if (isset($inst['connectionStatus']) && $inst['connectionStatus'] === 'open') {
        $online++;
    }
}
$offline = $total - $online;

// Get CSRF token for JavaScript
$csrfToken = csrf_token();

// Log page access
Logger::info('Dashboard accessed', ['user' => $_SESSION['username'] ?? 'unknown', 'instances_count' => $total]);

?>
<!DOCTYPE html>
<html lang="pt-br" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
    <meta name="description" content="Painel de controle premium - Gerencie suas instancias WhatsApp">
    <title>Painel Premium - Dashboard</title>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    keyframes: {
                        riseIn: {
                            '0%': { opacity: '0', transform: 'translateY(12px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    },
                    animation: {
                        riseIn: 'riseIn 0.4s ease-out forwards'
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_10%_12%,rgba(249,115,22,0.2),transparent_38%),radial-gradient(circle_at_90%_4%,rgba(239,68,68,0.18),transparent_32%),radial-gradient(circle_at_50%_120%,rgba(15,23,42,0.5),transparent_50%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[linear-gradient(rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.08)_1px,transparent_1px)] bg-[size:34px_34px] opacity-[0.06]"></div>

    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/80">
        <div class="mx-auto flex w-full max-w-[1300px] items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-700/40">
                    <i data-lucide="zap" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h1 class="bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-xl font-bold text-transparent">Chamalead</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ola, <?= htmlspecialchars($_SESSION['username'] ?? 'Usuario') ?></p>
                </div>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <button id="openDeepLinkModalBtn" class="btn inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-3 py-2 text-sm font-semibold text-white shadow-lg shadow-orange-700/40 transition hover:-translate-y-0.5 hover:brightness-105" type="button" onclick="openDeepLinkModal()">
                    <i data-lucide="qr-code" class="h-4 w-4"></i>
                    Deep Link QR
                </button>
                <button id="refreshBtn" class="btn inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-slate-400 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:text-slate-100" title="Atualizar" aria-label="Atualizar dados">
                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                </button>
                <button id="themeToggle" class="btn inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-slate-400 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-slate-500 dark:hover:text-slate-100" title="Alternar tema" aria-label="Alternar tema">
                    <i data-lucide="sun" id="sunIcon" class="hidden h-4 w-4"></i>
                    <i data-lucide="moon" id="moonIcon" class="h-4 w-4"></i>
                </button>
                <a href="logout.php" class="btn inline-flex items-center gap-2 rounded-lg border border-red-400/50 bg-red-500/10 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-500/20 dark:text-red-300">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                    Sair
                </a>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-[1300px] px-4 py-5 sm:px-6">
        <section aria-label="Estatisticas" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-3">
            <article class="rounded-xl border border-slate-300 bg-white/90 p-4 shadow-sm shadow-slate-300/40 dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-none" style="animation-delay:0.08s">
                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Total de Instancias</p>
                <p class="counter mt-1 text-3xl font-bold" data-target="<?= $total ?>">0</p>
            </article>
            <article class="rounded-xl border border-emerald-400/40 bg-emerald-500/10 p-4 shadow-sm shadow-emerald-600/10 dark:bg-emerald-500/8" style="animation-delay:0.16s">
                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-emerald-700 dark:text-emerald-300">Online</p>
                <p class="counter mt-1 text-3xl font-bold text-emerald-700 dark:text-emerald-300" data-target="<?= $online ?>">0</p>
            </article>
            <article class="rounded-xl border border-red-400/40 bg-red-500/10 p-4 shadow-sm shadow-red-600/10 dark:bg-red-500/8" style="animation-delay:0.24s">
                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-red-700 dark:text-red-300">Offline</p>
                <p class="counter mt-1 text-3xl font-bold text-red-700 dark:text-red-300" data-target="<?= $offline ?>">0</p>
            </article>
        </section>

        <section aria-label="Lista de instancias">
            <div class="mb-4 flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <h2 class="inline-flex items-center gap-2 text-base font-semibold">
                    <i data-lucide="layout-grid" class="h-4 w-4 text-orange-500"></i>
                    Suas Instancias
                </h2>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative w-full min-w-[220px] max-w-[300px]">
                        <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-sm text-slate-800 placeholder:text-slate-400 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Buscar instancias..." aria-label="Buscar instancias">
                    </div>
                    <select id="statusFilter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200" aria-label="Filtrar por status">
                        <option value="all">Todos os status</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                    <span class="inline-flex items-center gap-1 rounded-full border border-orange-400/40 bg-orange-500/10 px-2.5 py-1 text-xs font-semibold text-orange-700 dark:text-orange-300" title="Atalho de teclado">
                        <i data-lucide="command" class="h-3.5 w-3.5"></i>
                        <span>Ctrl/Cmd + K</span>
                    </span>
                </div>
            </div>

            <div class="instances-grid grid grid-cols-1 gap-3 md:grid-cols-2 2xl:grid-cols-3" id="instancesGrid" role="list">
                <?php if ($apiError): ?>
                    <div class="empty-state col-span-full rounded-xl border border-red-400/40 bg-red-500/10 px-6 py-10 text-center" style="animation: riseIn .35s ease-out both;">
                        <div class="empty-state-icon mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-red-400/45 bg-red-500/10 text-red-500 dark:text-red-300">
                            <i data-lucide="wifi-off" class="h-10 w-10"></i>
                        </div>
                        <h3 class="empty-state-title text-xl font-semibold">Erro de conexao</h3>
                        <p class="empty-state-description mx-auto mt-2 max-w-md text-sm text-slate-600 dark:text-slate-300">Nao foi possivel conectar ao servidor. Verifique sua conexao ou tente novamente.</p>
                        <button onclick="location.reload()" class="btn mt-5 inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-orange-700/35">
                            <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                            Tentar novamente
                        </button>
                    </div>
                <?php elseif (is_array($instances) && !empty($instances)): ?>
                    <?php foreach ($instances as $index => $inst): ?>
                        <?php
                            $instName = htmlspecialchars((string)($inst['name'] ?? $inst['instanceName'] ?? 'Sem Nome'));
                            $status = $inst['connectionStatus'] ?? 'closed';
                            $isOnline = $status === 'open';
                            $statusClass = $isOnline ? 'instance-status-online' : 'instance-status-offline';
                            $statusColor = $isOnline
                                ? 'text-emerald-700 bg-emerald-500/15 border-emerald-400/40 dark:text-emerald-300'
                                : 'text-red-700 bg-red-500/15 border-red-400/40 dark:text-red-300';
                            $statusText = $isOnline ? 'Conectado' : 'Desconectado';
                            $animationDelay = $index * 0.05;
                        ?>
                        <article class="instance-card animate-riseIn rounded-xl border border-slate-300 bg-white/90 p-4 shadow-sm shadow-slate-300/40 transition hover:-translate-y-0.5 hover:border-orange-400/60 dark:border-slate-800 dark:bg-slate-900/70 dark:shadow-none" style="animation-delay: <?= $animationDelay ?>s;" role="listitem" data-instance-name="<?= $instName ?>">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-orange-400/30 bg-orange-500/10 text-orange-500">
                                    <i data-lucide="smartphone" class="h-5 w-5"></i>
                                </div>
                                <span class="instance-status <?= $statusClass ?> inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.06em] <?= $statusColor ?>">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    <?= $statusText ?>
                                </span>
                            </div>

                            <div class="mb-4 min-w-0">
                                <h3 class="instance-name truncate text-sm font-semibold" title="<?= $instName ?>"><?= $instName ?></h3>
                                <p class="instance-number mt-1 inline-flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                                    <i data-lucide="hash" class="h-3 w-3"></i>
                                    <?= htmlspecialchars((string)($inst['ownerJid'] ?? $inst['owner'] ?? 'Sem numero')) ?>
                                </p>
                            </div>

                            <div class="instance-actions mt-auto flex flex-wrap gap-1.5 border-t border-slate-200 pt-3 dark:border-slate-800">
                                <?php if (!$isOnline): ?>
                                    <button onclick="openConnectModal('<?= $instName ?>')" class="instance-action-btn connect inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-400/35 bg-emerald-500/10 text-emerald-600 transition hover:bg-emerald-500/20 dark:text-emerald-300" title="Conectar" aria-label="Conectar <?= $instName ?>">
                                        <i data-lucide="link" class="h-4 w-4"></i>
                                    </button>
                                <?php endif; ?>
                                <button onclick="quickGenerateDeepLink('<?= $instName ?>')" class="instance-action-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-orange-400 hover:text-orange-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300" title="Gerar deep link" aria-label="Gerar deep link para <?= $instName ?>">
                                    <i data-lucide="qr-code" class="h-4 w-4"></i>
                                </button>
                                <button onclick="openViewModal('<?= $instName ?>')" class="instance-action-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-sky-400 hover:text-sky-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300" title="Ver detalhes" aria-label="Ver detalhes de <?= $instName ?>">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </button>
                                <button onclick="openEditModal('<?= $instName ?>')" class="instance-action-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-indigo-400 hover:text-indigo-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300" title="Editar" aria-label="Editar <?= $instName ?>">
                                    <i data-lucide="edit-2" class="h-4 w-4"></i>
                                </button>
                                <button onclick="openDeleteModal('<?= $instName ?>')" class="instance-action-btn delete inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-400/35 bg-red-500/10 text-red-600 transition hover:bg-red-500/20 dark:text-red-300" title="Deletar" aria-label="Deletar <?= $instName ?>">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state col-span-full rounded-xl border border-slate-300 bg-white/90 px-6 py-10 text-center dark:border-slate-800 dark:bg-slate-900/70" style="animation: riseIn .35s ease-out both;">
                        <div class="empty-state-icon mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-slate-300 bg-slate-100 text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                            <i data-lucide="inbox" class="h-10 w-10"></i>
                        </div>
                        <h3 class="empty-state-title text-xl font-semibold">Nenhuma instancia</h3>
                        <p class="empty-state-description mx-auto mt-2 max-w-md text-sm text-slate-600 dark:text-slate-300">Voce ainda nao possui instancias configuradas. Crie sua primeira instancia para comecar.</p>
                        <button onclick="openCreateModal()" class="btn mt-5 inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-orange-700/35">
                            <i data-lucide="plus" class="h-4 w-4"></i>
                            Criar instancia
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($total > 0): ?>
                <nav class="pagination mt-4 flex items-center justify-center gap-2" id="pagination" aria-label="Paginacao">
                    <button onclick="changePage(-1)" id="prevPage" class="pagination-btn inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-orange-400 hover:text-orange-500 disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300" aria-label="Pagina anterior">
                        <i data-lucide="chevron-left" class="h-4 w-4"></i>
                    </button>
                    <span class="pagination-info text-sm text-slate-600 dark:text-slate-300">
                        Pagina <strong id="currentPage">1</strong> de <strong id="totalPages"><?= ceil($total / 12) ?></strong>
                    </span>
                    <button onclick="changePage(1)" id="nextPage" class="pagination-btn inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 transition hover:border-orange-400 hover:text-orange-500 disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300" aria-label="Proxima pagina">
                        <i data-lucide="chevron-right" class="h-4 w-4"></i>
                    </button>
                </nav>
            <?php endif; ?>
        </section>
    </main>

    <?php if ($total > 0): ?>
        <button onclick="openCreateModal()" class="fab btn fixed bottom-5 right-5 z-30 inline-flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-xl shadow-orange-700/40 transition hover:scale-105" title="Criar nova instancia" aria-label="Criar nova instancia">
            <i data-lucide="plus" class="h-5 w-5"></i>
        </button>
    <?php endif; ?>

    <div id="toastContainer" class="toast-container fixed right-4 top-4 z-[70] flex max-w-sm flex-col gap-3" role="alert" aria-live="polite"></div>

    <div id="createModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="createModalTitle">
        <div class="modal-container max-h-[88vh] w-full max-w-[560px] overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl dark:bg-slate-900">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="modal-title flex items-center gap-3">
                    <div class="modal-title-icon modal-title-icon-primary flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
                        <i data-lucide="plus" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h3 id="createModalTitle" class="text-lg font-semibold">Criar Instancia</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Configure sua nova instancia WhatsApp</p>
                    </div>
                </div>
                <button onclick="closeModal('createModal')" class="modal-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-500 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300" aria-label="Fechar modal">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <div class="modal-body max-h-[66vh] overflow-y-auto px-5 py-4">
                <form id="createForm" onsubmit="handleCreate(event)" class="space-y-5">
                    <div>
                        <label for="createInstanceName" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Nome da Instancia *</label>
                        <input type="text" id="createInstanceName" name="instanceName" required minlength="3" maxlength="50" class="input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-950" placeholder="Ex: minha-instancia" aria-required="true">
                        <p class="mt-1 text-xs text-slate-500">Minimo 3 caracteres. Apenas letras, numeros, hifen e underscore.</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/70">
                        <h4 class="mb-3 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"><i data-lucide="settings-2" class="h-3.5 w-3.5"></i>Configuracoes</h4>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="readMessages" checked><span>Ler Mensagens</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="readStatus" checked><span>Ver Status</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="syncFullHistory" checked><span>Sincronizar Historico</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="alwaysOnline" id="createAlwaysOnline"><span>Sempre Online</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="groupsIgnore"><span>Ignorar Grupos</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="rejectCall" id="createRejectCall" onchange="toggleCreateMsgCall()"><span>Recusar Chamadas</span></label>
                        </div>
                    </div>

                    <div id="createMsgCallContainer" class="hidden">
                        <label for="createMsgCall" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Mensagem de Recusa</label>
                        <input type="text" id="createMsgCall" name="msgCall" class="input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-950" placeholder="Ex: Estou ocupado no momento, ligo mais tarde." maxlength="200">
                    </div>
                </form>
            </div>
            <div class="modal-footer flex gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/70">
                <button type="button" onclick="closeModal('createModal')" class="btn flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700">Cancelar</button>
                <button type="submit" form="createForm" id="createSubmitBtn" class="btn flex-1 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-3 py-2 text-sm font-semibold text-white" disabled>
                    <span id="createBtnText">Criar Instancia</span>
                    <i id="createSpinner" data-lucide="loader-circle" class="hidden h-4 w-4 animate-spin"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
        <div class="modal-container max-h-[88vh] w-full max-w-[560px] overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl dark:bg-slate-900">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="modal-title flex items-center gap-3">
                    <div class="modal-title-icon flex h-10 w-10 items-center justify-center rounded-lg border border-sky-400/35 bg-sky-500/10 text-sky-600 dark:text-sky-300"><i data-lucide="edit-2" class="h-5 w-5"></i></div>
                    <div>
                        <h3 id="editModalTitle" class="text-lg font-semibold">Editar Instancia</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Modifique as configuracoes</p>
                    </div>
                </div>
                <button onclick="closeModal('editModal')" class="modal-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-500 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300" aria-label="Fechar modal"><i data-lucide="x" class="h-4 w-4"></i></button>
            </div>
            <div class="modal-body max-h-[66vh] overflow-y-auto px-5 py-4">
                <div id="editSkeleton" class="space-y-4">
                    <div class="h-16 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                    <div class="h-48 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                </div>

                <form id="editForm" onsubmit="handleEdit(event)" class="hidden space-y-5">
                    <input type="hidden" name="instanceName" id="editInstanceName">
                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/70">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-sky-400/35 bg-sky-500/10 text-sky-600 dark:text-sky-300"><i data-lucide="smartphone" class="h-5 w-5"></i></div>
                        <div class="min-w-0">
                            <p class="text-xs text-slate-500">Nome da Instancia</p>
                            <p id="editInstanceNameDisplay" class="truncate text-sm font-semibold"></p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/70">
                        <h4 class="mb-3 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400"><i data-lucide="settings-2" class="h-3.5 w-3.5"></i>Configuracoes</h4>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="readMessages" id="editReadMessages"><span>Ler Mensagens</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="readStatus" id="editReadStatus"><span>Ver Status</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="syncFullHistory" id="editSyncFullHistory"><span>Sincronizar Historico</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="alwaysOnline" id="editAlwaysOnline"><span>Sempre Online</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="groupsIgnore" id="editGroupsIgnore"><span>Ignorar Grupos</span></label>
                            <label class="checkbox-wrapper inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><input type="checkbox" name="rejectCall" id="editRejectCall" onchange="toggleEditMsgCall()"><span>Recusar Chamadas</span></label>
                        </div>
                    </div>

                    <div id="editMsgCallContainer" class="hidden">
                        <label for="editMsgCall" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Mensagem de Recusa</label>
                        <input type="text" name="msgCall" id="editMsgCall" class="input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-950" placeholder="Ex: Estou ocupado no momento, ligo mais tarde.">
                    </div>
                </form>
            </div>
            <div class="modal-footer flex gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/70">
                <button type="button" onclick="closeModal('editModal')" class="btn flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700">Cancelar</button>
                <button type="submit" form="editForm" class="btn flex-1 rounded-lg bg-gradient-to-r from-sky-500 to-blue-600 px-3 py-2 text-sm font-semibold text-white">
                    <span id="editBtnText">Salvar Alteracoes</span>
                    <i id="editSpinner" data-lucide="loader-circle" class="hidden h-4 w-4 animate-spin"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="viewModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle">
        <div class="modal-container max-h-[88vh] w-full max-w-[760px] overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl dark:bg-slate-900">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="modal-title flex items-center gap-3">
                    <div class="modal-title-icon modal-title-icon-success flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-400/35 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300"><i data-lucide="eye" class="h-5 w-5"></i></div>
                    <div>
                        <h3 id="viewModalTitle" class="text-lg font-semibold">Detalhes da Instancia</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Informacoes completas</p>
                    </div>
                </div>
                <button onclick="closeModal('viewModal')" class="modal-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-500 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300" aria-label="Fechar modal"><i data-lucide="x" class="h-4 w-4"></i></button>
            </div>
            <div id="viewContent" class="modal-body max-h-[66vh] overflow-y-auto px-5 py-4">
                <div class="space-y-4">
                    <div class="mx-auto h-8 w-28 animate-pulse rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="h-20 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                        <div class="h-20 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                        <div class="h-20 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                    </div>
                    <div class="h-40 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"></div>
                </div>
            </div>
            <div class="modal-footer flex justify-end border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/70">
                <button onclick="closeModal('viewModal')" class="btn rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700">Fechar</button>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="modal-container w-full max-w-[470px] overflow-hidden rounded-2xl border border-red-400/35 bg-white shadow-xl dark:bg-slate-900">
            <div class="modal-header flex items-center justify-between border-b border-red-400/35 px-5 py-4">
                <div class="modal-title flex items-center gap-3">
                    <div class="modal-title-icon modal-title-icon-danger flex h-10 w-10 items-center justify-center rounded-lg border border-red-400/35 bg-red-500/10 text-red-600 dark:text-red-300"><i data-lucide="alert-triangle" class="h-5 w-5"></i></div>
                    <div>
                        <h3 id="deleteModalTitle" class="text-lg font-semibold">Deletar Instancia</h3>
                        <p class="text-xs text-red-500 dark:text-red-300">Esta acao nao pode ser desfeita</p>
                    </div>
                </div>
                <button onclick="closeModal('deleteModal')" class="modal-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-500 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300" aria-label="Fechar modal"><i data-lucide="x" class="h-4 w-4"></i></button>
            </div>
            <div class="modal-body px-5 py-4">
                <div class="rounded-xl border border-red-400/35 bg-red-500/10 p-4 text-center">
                    <p class="mb-1 text-sm text-slate-600 dark:text-slate-300">Tem certeza que deseja deletar:</p>
                    <p id="deleteInstanceName" class="text-xl font-semibold"></p>
                </div>
            </div>
            <div class="modal-footer flex gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/70">
                <button onclick="closeModal('deleteModal')" class="btn flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700">Cancelar</button>
                <button onclick="handleDelete()" class="btn flex-1 rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-3 py-2 text-sm font-semibold text-white">
                    <span id="deleteBtnText">Deletar</span>
                    <i id="deleteSpinner" data-lucide="loader-circle" class="hidden h-4 w-4 animate-spin"></i>
                </button>
            </div>
        </div>
    </div>

    <div id="connectModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="connectModalTitle">
        <div class="modal-container max-h-[88vh] w-full max-w-[520px] overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl dark:bg-slate-900">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="modal-title flex items-center gap-3">
                    <div class="modal-title-icon flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-400/35 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300"><i data-lucide="link" class="h-5 w-5"></i></div>
                    <div>
                        <h3 id="connectModalTitle" class="text-lg font-semibold">Conectar WhatsApp</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Gere o codigo de pareamento</p>
                    </div>
                </div>
                <button onclick="closeModal('connectModal')" class="modal-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-500 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300" aria-label="Fechar modal"><i data-lucide="x" class="h-4 w-4"></i></button>
            </div>

            <div class="modal-body max-h-[66vh] overflow-y-auto px-5 py-4">
                <form id="connectForm" onsubmit="handleConnect(event)" class="space-y-5">
                    <input type="hidden" name="instanceName" id="connectInstanceName">

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Instancia</label>
                        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-700 dark:bg-slate-950/70">
                            <i data-lucide="smartphone" class="h-4 w-4 text-emerald-600 dark:text-emerald-300"></i>
                            <span id="connectInstanceDisplay" class="text-sm font-semibold"></span>
                        </div>
                    </div>

                    <div>
                        <label for="connectPhoneNumber" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Numero de Telefone *</label>
                        <input type="text" id="connectPhoneNumber" name="phoneNumber" required class="input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-950" placeholder="Ex: 555199309404" pattern="[0-9]{10,15}" autocomplete="tel">
                        <p class="mt-1 text-xs text-slate-500">Com DDD, sem o +55</p>
                    </div>

                    <div id="pairingCodeContainer" class="hidden rounded-xl border border-emerald-400/35 bg-emerald-500/10 p-4 text-center">
                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500">CODIGO DE PAREAMENTO</p>
                        <p id="pairingCodeDisplay" class="mt-2 font-mono text-3xl font-bold tracking-[0.15em] text-emerald-700 dark:text-emerald-300"></p>
                        <p class="mt-3 text-xs leading-5 text-slate-600 dark:text-slate-300">
                            1. WhatsApp -> Configuracoes -> Dispositivos conectados<br>
                            2. Conectar dispositivo -> Conectar com numero<br>
                            3. Digite o codigo acima
                        </p>
                    </div>

                    <div id="timerContainer" class="hidden text-center">
                        <p id="timerText" class="text-sm text-slate-600 dark:text-slate-300">
                            Pode pedir outro codigo em <span id="countdown" class="font-semibold text-amber-600 dark:text-amber-300">2:00</span>
                        </p>
                    </div>
                </form>
            </div>

            <div class="modal-footer flex flex-wrap gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/70">
                <button type="button" onclick="closeModal('connectModal')" class="btn flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700">Fechar</button>
                <button type="submit" form="connectForm" id="connectSubmitBtn" class="btn flex-1 rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 px-3 py-2 text-sm font-semibold text-white">
                    <span id="connectBtnText">Gerar Codigo</span>
                    <i id="connectSpinner" data-lucide="loader-circle" class="hidden h-4 w-4 animate-spin"></i>
                </button>
                <button type="button" id="retryBtn" onclick="retryPairingCode()" class="btn hidden flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700">
                    <i data-lucide="refresh-cw" class="mr-1 h-4 w-4"></i>
                    Pedir Outro
                </button>
            </div>
        </div>
    </div>

    <div id="deepLinkModal" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="deepLinkModalTitle">
        <div class="modal-container max-h-[88vh] w-full max-w-[580px] overflow-hidden rounded-2xl border border-slate-700 bg-white shadow-xl dark:bg-slate-900">
            <div class="modal-header flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800">
                <div class="modal-title flex items-center gap-3">
                    <div class="modal-title-icon flex h-10 w-10 items-center justify-center rounded-lg border border-sky-400/35 bg-sky-500/10 text-sky-600 dark:text-sky-300"><i data-lucide="qr-code" class="h-5 w-5"></i></div>
                    <div>
                        <h3 id="deepLinkModalTitle" class="text-lg font-semibold">Gerar Deep Link (QR)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Compartilhe com o cliente para conectar sozinho</p>
                    </div>
                </div>
                <button onclick="closeModal('deepLinkModal')" class="modal-close inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-500 hover:text-slate-900 dark:border-slate-700 dark:text-slate-300" aria-label="Fechar modal"><i data-lucide="x" class="h-4 w-4"></i></button>
            </div>

            <div class="modal-body max-h-[66vh] space-y-5 overflow-y-auto px-5 py-4">
                <form id="deepLinkForm" onsubmit="handleGenerateDeepLink(event)" class="space-y-3">
                    <div>
                        <label for="deepLinkInstanceName" class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">Nome da Instancia *</label>
                        <input type="text" id="deepLinkInstanceName" name="instanceName" required minlength="3" maxlength="50" class="input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30 dark:border-slate-700 dark:bg-slate-950" placeholder="Ex: cliente-acme">
                        <p class="mt-1 text-xs text-slate-500">Se a instancia nao existir, ela sera criada automaticamente quando o cliente abrir o link.</p>
                    </div>

                    <button type="submit" id="deepLinkSubmitBtn" class="btn inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-4 py-2 text-sm font-semibold text-white">
                        <span id="deepLinkBtnText">Gerar Link</span>
                        <i id="deepLinkSpinner" data-lucide="loader-circle" class="hidden h-4 w-4 animate-spin"></i>
                    </button>
                </form>

                <div id="deepLinkResult" class="hidden space-y-2">
                    <label for="deepLinkUrl" class="block text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">URL para compartilhar</label>
                    <textarea id="deepLinkUrl" class="input w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none dark:border-slate-700 dark:bg-slate-950" readonly rows="3" style="resize: vertical;"></textarea>
                    <div id="deepLinkExpiryBox" class="rounded-lg border border-orange-400/35 bg-orange-500/10 p-3" role="status" aria-live="polite">
                        <div class="mb-2 flex items-center justify-between gap-2 text-xs">
                            <span id="deepLinkExpiryLabel" class="font-semibold text-orange-700 dark:text-orange-300">Expira em --:--</span>
                            <span id="deepLinkExpiryAt" class="text-slate-600 dark:text-slate-300">--/--/---- --:--</span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-300/70 dark:bg-slate-700">
                            <div id="deepLinkExpiryProgress" class="h-full w-full bg-gradient-to-r from-orange-400 via-orange-500 to-red-600 transition-[width] duration-1000"></div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="btn rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700" onclick="copyGeneratedDeepLink()">
                            <i data-lucide="copy" class="h-4 w-4"></i>
                            Copiar
                        </button>
                        <button type="button" class="btn rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold dark:border-slate-700" onclick="openGeneratedDeepLink()">
                            <i data-lucide="external-link" class="h-4 w-4"></i>
                            Abrir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/toastify-js"></script>
    <script src="panel.js?v=<?php echo file_exists(__DIR__ . '/panel.js') ? filemtime(__DIR__ . '/panel.js') : time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
