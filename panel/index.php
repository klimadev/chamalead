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
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none';");
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
foreach($instances as $inst) {
    if (isset($inst['connectionStatus']) && $inst['connectionStatus'] === 'open') $online++;
}
$offline = $total - $online;

// Get CSRF token for JavaScript
$csrfToken = csrf_token();

// Log page access
Logger::info('Dashboard accessed', ['user' => $_SESSION['username'] ?? 'unknown', 'instances_count' => $total]);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
    <meta name="description" content="Painel de controle premium - Gerencie suas instâncias WhatsApp">
    <title>Painel Premium - Dashboard</title>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Local Styles -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Background Effects -->
    <div class="grid-overlay" aria-hidden="true"></div>

    <!-- Navigation -->
    <nav class="nav" role="navigation" aria-label="Navegaçao principal">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="nav-brand-icon">
                    <i data-lucide="zap" style="color: white;"></i>
                </div>
                <div>
                    <h1 style="font-size: 1.25rem; font-weight: 700;">
                        <span class="text-gradient">Chamalead</span>
                    </h1>
                    <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Olá, <?= htmlspecialchars($_SESSION['username'] ?? 'Usuário') ?></p>
                </div>
            </div>
            <div class="nav-actions">
                <button id="openDeepLinkModalBtn" class="btn btn-primary" type="button" onclick="openDeepLinkModal()">
                    <i data-lucide="qr-code"></i>
                    Deep Link QR
                </button>
                <button id="refreshBtn" class="btn btn-ghost btn-icon" title="Atualizar" aria-label="Atualizar dados">
                    <i data-lucide="refresh-cw"></i>
                </button>
                <button id="themeToggle" class="btn btn-ghost btn-icon" title="Alternar tema" aria-label="Alternar tema">
                    <i data-lucide="sun" id="sunIcon" style="display: none;"></i>
                    <i data-lucide="moon" id="moonIcon"></i>
                </button>
                <a href="logout.php" class="btn btn-danger" style="text-decoration: none;">
                    <i data-lucide="log-out"></i>
                    Sair
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container" role="main">
        <!-- Statistics Section -->
        <section class="section" aria-label="Estatísticas">
            <div class="stats-grid">
                <article class="stat-card" style="animation: fadeInUp 0.5s ease-out 0.1s both;">
                    <p class="stat-label">Total de Instancias</p>
                    <p class="stat-value counter" data-target="<?= $total ?>">0</p>
                </article>
                <article class="stat-card stat-card-success" style="animation: fadeInUp 0.5s ease-out 0.2s both;">
                    <p class="stat-label">Online</p>
                    <p class="stat-value stat-value-success counter" data-target="<?= $online ?>">0</p>
                </article>
                <article class="stat-card stat-card-error" style="animation: fadeInUp 0.5s ease-out 0.3s both;">
                    <p class="stat-label">Offline</p>
                    <p class="stat-value stat-value-error counter" data-target="<?= $offline ?>">0</p>
                </article>
            </div>
        </section>

        <!-- Instances Section -->
        <section class="section" aria-label="Lista de instâncias">
            <div class="section-header">
                <h2 class="section-title">
                    <i data-lucide="layout-grid" class="section-title-icon"></i>
                    Suas Instancias
                </h2>
                <div style="display: flex; gap: var(--space-md); align-items: center; flex-wrap: wrap;">
                    <div class="search-container" style="width: 280px;">
                        <i data-lucide="search" class="search-icon" style="width: 18px; height: 18px;"></i>
                        <input 
                            type="text" 
                            id="searchInput" 
                            class="search-input" 
                            placeholder="Buscar instâncias..."
                            aria-label="Buscar instâncias"
                        >
                    </div>
                    <select id="statusFilter" class="filter-select" aria-label="Filtrar por status">
                        <option value="all">Todos os status</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
            </div>

            <!-- Instances Grid -->
            <div class="instances-grid" id="instancesGrid" role="list">
                <?php if ($apiError): ?>
                    <!-- API Error State -->
                    <div class="empty-state" style="grid-column: 1 / -1; animation: fadeIn 0.5s ease-out;">
                        <div class="empty-state-icon" style="border-color: rgba(239, 68, 68, 0.3); color: var(--color-error);">
                            <i data-lucide="wifi-off" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h3 class="empty-state-title">Erro de conexao</h3>
                        <p class="empty-state-description">Nao foi possível conectar ao servidor. Verifique sua conexao ou tente novamente.</p>
                        <button onclick="location.reload()" class="btn btn-primary">
                            <i data-lucide="refresh-cw"></i>
                            Tentar novamente
                        </button>
                    </div>
                <?php elseif (is_array($instances) && !empty($instances)): ?>
                    <?php foreach($instances as $index => $inst): ?>
                        <?php 
                            $instName = htmlspecialchars((string)($inst['name'] ?? $inst['instanceName'] ?? 'Sem Nome'));
                            $instId = htmlspecialchars((string)($inst['id'] ?? ''));
                            $status = $inst['connectionStatus'] ?? 'closed';
                            $isOnline = $status === 'open';
                            $statusClass = $isOnline ? 'instance-status-online' : 'instance-status-offline';
                            $statusText = $isOnline ? 'Conectado' : 'Desconectado';
                            $animationDelay = $index * 0.05;
                        ?>
                        <article 
                            class="instance-card" 
                            style="animation: fadeInUp 0.5s ease-out <?= $animationDelay ?>s both;"
                            role="listitem"
                            data-instance-name="<?= $instName ?>"
                        >
                            <div class="instance-card-header">
                                <div class="instance-icon">
                                    <i data-lucide="smartphone" style="width: 24px; height: 24px;"></i>
                                </div>
                                <span class="instance-status <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </div>
                            
                            <div style="flex: 1;">
                                <h3 class="instance-name" title="<?= $instName ?>">
                                    <?= $instName ?>
                                </h3>
                                <p class="instance-number">
                                    <i data-lucide="hash" style="width: 12px; height: 12px;"></i>
                                    <?= htmlspecialchars((string)($inst['ownerJid'] ?? $inst['owner'] ?? 'Sem numero')) ?>
                                </p>
                            </div>

                            <div class="instance-actions">
                                <?php if (!$isOnline): ?>
                                    <button
                                        onclick="openConnectModal('<?= $instName ?>')"
                                        class="instance-action-btn connect"
                                        title="Conectar"
                                        aria-label="Conectar <?= $instName ?>"
                                    >
                                        <i data-lucide="link" style="width: 16px; height: 16px; color: var(--color-success);"></i>
                                    </button>
                                <?php endif; ?>
                                <button 
                                    onclick="quickGenerateDeepLink('<?= $instName ?>')"
                                    class="instance-action-btn"
                                    title="Gerar deep link"
                                    aria-label="Gerar deep link para <?= $instName ?>"
                                >
                                    <i data-lucide="qr-code" style="width: 16px; height: 16px;"></i>
                                </button>
                                <button 
                                    onclick="openViewModal('<?= $instName ?>')" 
                                    class="instance-action-btn"
                                    title="Ver detalhes"
                                    aria-label="Ver detalhes de <?= $instName ?>"
                                >
                                    <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                </button>
                                <button 
                                    onclick="openEditModal('<?= $instName ?>')" 
                                    class="instance-action-btn"
                                    title="Editar"
                                    aria-label="Editar <?= $instName ?>"
                                >
                                    <i data-lucide="edit-2" style="width: 16px; height: 16px;"></i>
                                </button>
                                <button 
                                    onclick="openDeleteModal('<?= $instName ?>')" 
                                    class="instance-action-btn delete"
                                    title="Deletar"
                                    aria-label="Deletar <?= $instName ?>"
                                >
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state" style="grid-column: 1 / -1; animation: fadeIn 0.5s ease-out;">
                        <div class="empty-state-icon">
                            <i data-lucide="inbox" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h3 class="empty-state-title">Nenhuma instância</h3>
                        <p class="empty-state-description">Você ainda nao possui instâncias configuradas. Crie sua primeira instância para começar.</p>
                        <button onclick="openCreateModal()" class="btn btn-primary btn-lg">
                            <i data-lucide="plus"></i>
                            Criar instância
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total > 0): ?>
                <nav class="pagination" id="pagination" aria-label="Paginaçao">
                    <button 
                        onclick="changePage(-1)" 
                        id="prevPage" 
                        class="pagination-btn" 
                        aria-label="Página anterior"
                    >
                        <i data-lucide="chevron-left" style="width: 20px; height: 20px;"></i>
                    </button>
                    <span class="pagination-info">
                        Página <strong id="currentPage">1</strong> de <strong id="totalPages"><?= ceil($total / 12) ?></strong>
                    </span>
                    <button 
                        onclick="changePage(1)" 
                        id="nextPage" 
                        class="pagination-btn" 
                        aria-label="Próxima página"
                    >
                        <i data-lucide="chevron-right" style="width: 20px; height: 20px;"></i>
                    </button>
                </nav>
            <?php endif; ?>
        </section>
    </main>

    <!-- Floating Action Button -->
    <?php if ($total > 0): ?>
        <button onclick="openCreateModal()" class="fab" title="Criar nova instância" aria-label="Criar nova instância">
            <i data-lucide="plus"></i>
        </button>
    <?php endif; ?>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container" role="alert" aria-live="polite"></div>

    <!-- Create Modal -->
    <div id="createModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="createModalTitle">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title">
                    <div class="modal-title-icon modal-title-icon-primary">
                        <i data-lucide="plus" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 id="createModalTitle" style="font-size: 1.125rem; font-weight: 600;">Criar Instancia</h3>
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Configure sua nova instância WhatsApp</p>
                    </div>
                </div>
                <button onclick="closeModal('createModal')" class="modal-close" aria-label="Fechar modal">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="createForm" onsubmit="handleCreate(event)" style="display: flex; flex-direction: column; gap: var(--space-lg);">
                    <!-- Instance Name -->
                    <div>
                        <label for="createInstanceName" style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">
                            Nome da Instancia *
                        </label>
                        <input 
                            type="text" 
                            id="createInstanceName"
                            name="instanceName" 
                            required 
                            minlength="3"
                            maxlength="50"
                            class="input" 
                            placeholder="Ex: minha-instancia"
                            aria-required="true"
                        >
                        <p class="text-muted" style="font-size: 0.75rem; margin-top: var(--space-xs);">Mínimo 3 caracteres. Apenas letras, numeros, hífen e underscore.</p>
                    </div>
                    
                    <!-- Configurations -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-lg);">
                        <h4 style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-md); display: flex; align-items: center; gap: var(--space-sm);">
                            <i data-lucide="settings-2" style="width: 14px; height: 14px;"></i>
                            Configurações
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-md);">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="readMessages" checked>
                                <span style="color: var(--color-text-secondary);">Ler Mensagens</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="readStatus" checked>
                                <span style="color: var(--color-text-secondary);">Ver Status</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="syncFullHistory" checked>
                                <span style="color: var(--color-text-secondary);">Sincronizar Histórico</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="alwaysOnline" id="createAlwaysOnline">
                                <span style="color: var(--color-text-secondary);">Sempre Online</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="groupsIgnore">
                                <span style="color: var(--color-text-secondary);">Ignorar Grupos</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="rejectCall" id="createRejectCall" onchange="toggleCreateMsgCall()">
                                <span style="color: var(--color-text-secondary);">Recusar Chamadas</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Reject Call Message -->
                    <div id="createMsgCallContainer" class="hidden">
                        <label for="createMsgCall" style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">
                            Mensagem de Recusa
                        </label>
                        <input 
                            type="text" 
                            id="createMsgCall"
                            name="msgCall" 
                            class="input" 
                            placeholder="Ex: Estou ocupado no momento, ligo mais tarde."
                            maxlength="200"
                        >
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('createModal')" class="btn btn-secondary flex-1">
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    form="createForm"
                    id="createSubmitBtn"
                    class="btn btn-primary flex-1"
                    disabled
                >
                    <span id="createBtnText">Criar Instancia</span>
                    <div id="createSpinner" class="hidden" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title">
                    <div class="modal-title-icon" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: var(--color-info);">
                        <i data-lucide="edit-2" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 id="editModalTitle" style="font-size: 1.125rem; font-weight: 600;">Editar Instancia</h3>
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Modifique as configurações</p>
                    </div>
                </div>
                <button onclick="closeModal('editModal')" class="modal-close" aria-label="Fechar modal">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Skeleton Loading -->
                <div id="editSkeleton" class="space-y-4">
                    <div class="skeleton" style="height: 60px;"></div>
                    <div class="skeleton" style="height: 200px;"></div>
                </div>
                
                <!-- Edit Form -->
                <form id="editForm" onsubmit="handleEdit(event)" class="hidden" style="display: flex; flex-direction: column; gap: var(--space-lg);">
                    <input type="hidden" name="instanceName" id="editInstanceName">
                    
                    <!-- Instance Name Display -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-md); display: flex; align-items: center; gap: var(--space-md);">
                        <div style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.1); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--color-info);">
                            <i data-lucide="smartphone" style="width: 20px; height: 20px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Nome da Instancia</p>
                            <p id="editInstanceNameDisplay" style="font-weight: 600; color: var(--color-text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></p>
                        </div>
                    </div>
                    
                    <!-- Configurations -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-lg);">
                        <h4 style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-md); display: flex; align-items: center; gap: var(--space-sm);">
                            <i data-lucide="settings-2" style="width: 14px; height: 14px;"></i>
                            Configurações
                        </h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-md);">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="readMessages" id="editReadMessages">
                                <span style="color: var(--color-text-secondary);">Ler Mensagens</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="readStatus" id="editReadStatus">
                                <span style="color: var(--color-text-secondary);">Ver Status</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="syncFullHistory" id="editSyncFullHistory">
                                <span style="color: var(--color-text-secondary);">Sincronizar Histórico</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="alwaysOnline" id="editAlwaysOnline">
                                <span style="color: var(--color-text-secondary);">Sempre Online</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="groupsIgnore" id="editGroupsIgnore">
                                <span style="color: var(--color-text-secondary);">Ignorar Grupos</span>
                            </label>
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="rejectCall" id="editRejectCall" onchange="toggleEditMsgCall()">
                                <span style="color: var(--color-text-secondary);">Recusar Chamadas</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Reject Call Message -->
                    <div id="editMsgCallContainer" class="hidden">
                        <label for="editMsgCall" style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">
                            Mensagem de Recusa
                        </label>
                        <input 
                            type="text" 
                            name="msgCall" 
                            id="editMsgCall"
                            class="input" 
                            placeholder="Ex: Estou ocupado no momento, ligo mais tarde."
                        >
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary flex-1">
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    form="editForm"
                    class="btn btn-primary flex-1"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                >
                    <span id="editBtnText">Salvar Alterações</span>
                    <div id="editSpinner" class="hidden" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                </button>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle">
        <div class="modal-container" style="max-width: 700px;">
            <div class="modal-header">
                <div class="modal-title">
                    <div class="modal-title-icon modal-title-icon-success">
                        <i data-lucide="eye" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 id="viewModalTitle" style="font-size: 1.125rem; font-weight: 600;">Detalhes da Instancia</h3>
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Informações completas</p>
                    </div>
                </div>
                <button onclick="closeModal('viewModal')" class="modal-close" aria-label="Fechar modal">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            <div id="viewContent" class="modal-body">
                <!-- Skeleton Loading -->
                <div class="space-y-4">
                    <div style="display: flex; justify-content: center;">
                        <div class="skeleton" style="width: 120px; height: 32px; border-radius: var(--radius-full);"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-md);">
                        <div class="skeleton" style="height: 80px; border-radius: var(--radius-lg);"></div>
                        <div class="skeleton" style="height: 80px; border-radius: var(--radius-lg);"></div>
                        <div class="skeleton" style="height: 80px; border-radius: var(--radius-lg);"></div>
                    </div>
                    <div class="skeleton" style="height: 150px; border-radius: var(--radius-lg);"></div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: flex-end;">
                <button onclick="closeModal('viewModal')" class="btn btn-secondary">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header" style="border-bottom-color: rgba(239, 68, 68, 0.2);">
                <div class="modal-title">
                    <div class="modal-title-icon modal-title-icon-danger">
                        <i data-lucide="alert-triangle" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 id="deleteModalTitle" style="font-size: 1.125rem; font-weight: 600;">Deletar Instancia</h3>
                        <p style="font-size: 0.75rem; color: var(--color-error);">Esta açao nao pode ser desfeita</p>
                    </div>
                </div>
                <button onclick="closeModal('deleteModal')" class="modal-close" aria-label="Fechar modal">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            <div class="modal-body">
                <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-lg); padding: var(--space-lg); text-align: center;">
                    <p style="color: var(--color-text-tertiary); font-size: 0.875rem; margin-bottom: var(--space-sm);">Tem certeza que deseja deletar:</p>
                    <p id="deleteInstanceName" style="font-size: 1.25rem; font-weight: 600; color: var(--color-text-primary);"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('deleteModal')" class="btn btn-secondary flex-1">
                    Cancelar
                </button>
                <button 
                    onclick="handleDelete()"
                    class="btn btn-danger flex-1"
                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white;"
                >
                    <span id="deleteBtnText">Deletar</span>
                    <div id="deleteSpinner" class="hidden" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                </button>
            </div>
        </div>
    </div>

    <!-- Connect Modal -->
    <div id="connectModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="connectModalTitle">
        <div class="modal-container" style="max-width: 480px;">
            <div class="modal-header">
                <div class="modal-title">
                    <div class="modal-title-icon" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: var(--color-success);">
                        <i data-lucide="link" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 id="connectModalTitle" style="font-size: 1.125rem; font-weight: 600;">Conectar WhatsApp</h3>
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Gere o codigo de pareamento</p>
                    </div>
                </div>
                <button onclick="closeModal('connectModal')" class="modal-close" aria-label="Fechar modal">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <form id="connectForm" onsubmit="handleConnect(event)" style="display: flex; flex-direction: column; gap: var(--space-lg);">
                    <input type="hidden" name="instanceName" id="connectInstanceName">
                    
                    <!-- Instancia -->
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">
                            Instancia
                        </label>
                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-md); display: flex; align-items: center; gap: var(--space-md);">
                            <i data-lucide="smartphone" style="color: var(--color-success);"></i>
                            <span id="connectInstanceDisplay" style="font-weight: 600;"></span>
                        </div>
                    </div>
                    
                    <!-- Telefone -->
                    <div>
                        <label for="connectPhoneNumber" style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">
                            Numero de Telefone *
                        </label>
                        <input 
                            type="text" 
                            id="connectPhoneNumber"
                            name="phoneNumber" 
                            required 
                            class="input" 
                            placeholder="Ex: 555199309404"
                            pattern="[0-9]{10,15}"
                            autocomplete="tel"
                        >
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-top: var(--space-xs);">
                            Com DDD, sem o +55
                        </p>
                    </div>
                    
                    <!-- Pairing Code (escondido ate gerar) -->
                    <div id="pairingCodeContainer" class="hidden" style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: var(--radius-lg); padding: var(--space-lg); text-align: center;">
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">CODIGO DE PAREAMENTO</p>
                        <p id="pairingCodeDisplay" style="font-size: 2rem; font-weight: 700; color: var(--color-success); letter-spacing: 0.15em; font-family: monospace;"></p>
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-top: var(--space-md); line-height: 1.5;">
                            1. WhatsApp -> Configuracoes -> Dispositivos conectados<br>
                            2. Conectar dispositivo -> Conectar com numero<br>
                            3. Digite o codigo acima
                        </p>
                    </div>
                    
                    <!-- Timer (escondido ate gerar) -->
                    <div id="timerContainer" class="hidden" style="text-align: center;">
                        <p id="timerText" style="font-size: 0.875rem; color: var(--color-text-tertiary);">
                            Pode pedir outro codigo em <span id="countdown" style="color: var(--color-warning); font-weight: 600;">2:00</span>
                        </p>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="closeModal('connectModal')" class="btn btn-secondary flex-1">
                    Fechar
                </button>
                <button type="submit" form="connectForm" id="connectSubmitBtn" class="btn btn-success flex-1" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                    <span id="connectBtnText">Gerar Codigo</span>
                    <div id="connectSpinner" class="hidden" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                </button>
                <button type="button" id="retryBtn" onclick="retryPairingCode()" class="btn btn-secondary hidden flex-1">
                    <i data-lucide="refresh-cw" style="width: 16px; height: 16px; margin-right: var(--space-xs);"></i>
                    Pedir Outro
                </button>
            </div>
        </div>
    </div>

    <!-- Deep Link Modal -->
    <div id="deepLinkModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="deepLinkModalTitle">
        <div class="modal-container" style="max-width: 560px;">
            <div class="modal-header">
                <div class="modal-title">
                    <div class="modal-title-icon" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.25); color: var(--color-info);">
                        <i data-lucide="qr-code" style="width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <h3 id="deepLinkModalTitle" style="font-size: 1.125rem; font-weight: 600;">Gerar Deep Link (QR)</h3>
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary);">Compartilhe com o cliente para conectar sozinho</p>
                    </div>
                </div>
                <button onclick="closeModal('deepLinkModal')" class="modal-close" aria-label="Fechar modal">
                    <i data-lucide="x" style="width: 18px; height: 18px;"></i>
                </button>
            </div>

            <div class="modal-body" style="display: flex; flex-direction: column; gap: var(--space-lg);">
                <form id="deepLinkForm" onsubmit="handleGenerateDeepLink(event)" style="display: flex; flex-direction: column; gap: var(--space-md);">
                    <div>
                        <label for="deepLinkInstanceName" style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-sm);">
                            Nome da Instancia *
                        </label>
                        <input
                            type="text"
                            id="deepLinkInstanceName"
                            name="instanceName"
                            required
                            minlength="3"
                            maxlength="50"
                            class="input"
                            placeholder="Ex: cliente-acme"
                        >
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-top: var(--space-xs);">
                            Se a instancia nao existir, ela sera criada automaticamente quando o cliente abrir o link.
                        </p>
                    </div>

                    <button type="submit" id="deepLinkSubmitBtn" class="btn btn-primary">
                        <span id="deepLinkBtnText">Gerar Link</span>
                        <div id="deepLinkSpinner" class="hidden" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                    </button>
                </form>

                <div id="deepLinkResult" class="hidden" style="display: flex; flex-direction: column; gap: var(--space-sm);">
                    <label for="deepLinkUrl" style="display: block; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary);">
                        URL para compartilhar
                    </label>
                    <textarea id="deepLinkUrl" class="input" readonly rows="3" style="resize: vertical;"></textarea>
                    <div style="display: flex; gap: var(--space-sm); flex-wrap: wrap;">
                        <button type="button" class="btn btn-secondary" onclick="copyGeneratedDeepLink()">
                            <i data-lucide="copy" style="width: 16px; height: 16px;"></i>
                            Copiar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="openGeneratedDeepLink()">
                            <i data-lucide="external-link" style="width: 16px; height: 16px;"></i>
                            Abrir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="panel.js?v=<?php echo file_exists(__DIR__ . '/panel.js') ? filemtime(__DIR__ . '/panel.js') : time(); ?>"></script>
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
