/**
 * Premium Panel JavaScript Module
 * 
 * Enhanced UI interactions, modal management, API calls with modern
 * animations and sophisticated micro-interactions.
 * 
 * @version 3.0.0
 */

// Initialize Lucide icons
lucide.createIcons();

// CSRF Token for AJAX requests
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Global state
let instanceToDelete = null;
let currentPageNum = 1;
const ITEMS_PER_PAGE = 12;

// ==================== THEME MANAGEMENT ====================

function loadTheme() {
    const savedTheme = localStorage.getItem('panel-theme');
    if (savedTheme === 'light') {
        document.body.classList.add('light-theme');
        updateThemeIcons(true);
    }
}

function toggleTheme() {
    const isLight = document.body.classList.toggle('light-theme');
    localStorage.setItem('panel-theme', isLight ? 'light' : 'dark');
    updateThemeIcons(isLight);
}

function updateThemeIcons(isLight) {
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
    if (sunIcon && moonIcon) {
        sunIcon.style.display = isLight ? 'block' : 'none';
        moonIcon.style.display = isLight ? 'none' : 'block';
    }
}

// Initialize theme
loadTheme();
document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);

// ==================== MODAL MANAGEMENT ====================

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus trap for accessibility
    const focusableElements = modal.querySelectorAll('button, input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusableElements.length) {
        focusableElements[0].focus();
    }
    
    lucide.createIcons();
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.classList.remove('active');
    document.body.style.overflow = '';
    
    // Stop polling if closing connect modal
    if (modalId === 'connectModal') {
        stopPollingStatus();
        stopPairingSyncPolling();
        stopCountdown();
        // Reset stored values
        currentPairingCode = null;
        currentPhoneNumber = null;
        currentConnectInstance = null;
    }
}

// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            closeModal(modal.id);
        });
    }
});

// ==================== TOAST NOTIFICATIONS ====================

function showToast(message, type = 'success', duration = 4000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const icons = {
        success: 'check-circle',
        error: 'alert-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-icon toast-icon-${type}">
            <i data-lucide="${icons[type]}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${type === 'success' ? 'Sucesso' : type === 'error' ? 'Erro' : type === 'warning' ? 'Atenção' : 'Info'}</div>
            <div class="toast-message">${message}</div>
        </div>
    `;
    
    container.appendChild(toast);
    lucide.createIcons();
    
    // Auto-remove
    setTimeout(() => {
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

// ==================== API CALLS ====================

const API_CONFIG = {
    maxRetries: 3,
    baseDelay: 1000,
    timeout: 30000
};

async function fetchWithTimeout(url, options = {}, timeout = API_CONFIG.timeout) {
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), timeout);
    
    try {
        const response = await fetch(url, {
            ...options,
            signal: controller.signal
        });
        clearTimeout(id);
        return response;
    } catch (error) {
        clearTimeout(id);
        throw error;
    }
}

async function apiCall(action, data = {}, maxRetries = API_CONFIG.maxRetries) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('csrf_token', CSRF_TOKEN);
    
    Object.keys(data).forEach(key => {
        formData.append(key, data[key]);
    });
    
    let lastError = null;
    
    for (let attempt = 0; attempt <= maxRetries; attempt++) {
        try {
            const response = await fetchWithTimeout('instance-actions.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            return await response.json();
            
        } catch (error) {
            lastError = error;
            if (attempt === maxRetries) break;
            
            await new Promise(resolve => setTimeout(resolve, API_CONFIG.baseDelay * Math.pow(2, attempt)));
        }
    }
    
    return { 
        success: false, 
        message: 'Erro de conexão. Tente novamente.'
    };
}

// ==================== CREATE INSTANCE ====================

function toggleCreateMsgCall() {
    const container = document.getElementById('createMsgCallContainer');
    const checkbox = document.getElementById('createRejectCall');
    if (container && checkbox) {
        container.classList.toggle('hidden', !checkbox.checked);
    }
}

function openCreateModal() {
    const form = document.getElementById('createForm');
    if (form) {
        form.reset();
        // Pre-check defaults
        document.querySelectorAll('#createModal input[name="readMessages"], #createModal input[name="readStatus"], #createModal input[name="syncFullHistory"]').forEach(cb => {
            cb.checked = true;
        });
    }
    
    document.getElementById('createMsgCallContainer')?.classList.add('hidden');
    document.getElementById('createSubmitBtn')?.setAttribute('disabled', 'true');
    
    openModal('createModal');
}

function validateInstanceName(name) {
    if (!name || name.length === 0) return 'Nome é obrigatório';
    if (name.length < 3) return 'Mínimo 3 caracteres';
    if (name.length > 50) return 'Máximo 50 caracteres';
    if (!/^[a-zA-Z0-9_-]+$/.test(name)) return 'Apenas letras, números, hífen e underscore';
    return null;
}

// Real-time validation
document.getElementById('createInstanceName')?.addEventListener('input', function() {
    const error = validateInstanceName(this.value);
    const submitBtn = document.getElementById('createSubmitBtn');
    
    if (error) {
        this.classList.add('input-error');
        this.classList.remove('input-success');
        submitBtn?.setAttribute('disabled', 'true');
    } else {
        this.classList.remove('input-error');
        this.classList.add('input-success');
        submitBtn?.removeAttribute('disabled');
    }
});

async function handleCreate(e) {
    e.preventDefault();
    
    const btnText = document.getElementById('createBtnText');
    const spinner = document.getElementById('createSpinner');
    const submitBtn = e.target.querySelector('button[type="submit"]');
    
    // Loading state
    submitBtn?.setAttribute('disabled', 'true');
    btnText.style.display = 'none';
    spinner?.classList.remove('hidden');
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    const result = await apiCall('create', data);
    
    if (result.success) {
        showToast(result.message, 'success');
        closeModal('createModal');
        setTimeout(() => location.reload(), 1000);
    } else {
        showToast(result.message, 'error');
        submitBtn?.removeAttribute('disabled');
        btnText.style.display = 'block';
        spinner?.classList.add('hidden');
    }
}

// ==================== EDIT INSTANCE ====================

function toggleEditMsgCall() {
    const container = document.getElementById('editMsgCallContainer');
    const checkbox = document.getElementById('editRejectCall');
    if (container && checkbox) {
        container.classList.toggle('hidden', !checkbox.checked);
    }
}

async function openEditModal(instanceName) {
    openModal('editModal');
    
    // Show skeleton, hide form
    document.getElementById('editSkeleton')?.classList.remove('hidden');
    document.getElementById('editForm')?.classList.add('hidden');
    
    // Set instance name
    document.getElementById('editInstanceName').value = instanceName;
    document.getElementById('editInstanceNameDisplay').textContent = instanceName;
    
    // Load settings
    const result = await apiCall('getSettings', { instanceName });
    
    // Hide skeleton, show form
    document.getElementById('editSkeleton')?.classList.add('hidden');
    document.getElementById('editForm')?.classList.remove('hidden');
    
    if (result.success) {
        const settings = result.data;
        
        document.getElementById('editMsgCall').value = settings.msgCall || '';
        document.getElementById('editRejectCall').checked = settings.rejectCall || false;
        document.getElementById('editGroupsIgnore').checked = settings.groupsIgnore || false;
        document.getElementById('editAlwaysOnline').checked = settings.alwaysOnline || false;
        document.getElementById('editReadMessages').checked = settings.readMessages || false;
        document.getElementById('editReadStatus').checked = settings.readStatus || false;
        document.getElementById('editSyncFullHistory').checked = settings.syncFullHistory || false;
        
        toggleEditMsgCall();
        lucide.createIcons();
    } else {
        showToast('Erro ao carregar configurações', 'error');
    }
}

async function handleEdit(e) {
    e.preventDefault();
    
    const btnText = document.getElementById('editBtnText');
    const spinner = document.getElementById('editSpinner');
    const submitBtn = e.target.querySelector('button[type="submit"]');
    
    // Loading state
    submitBtn?.setAttribute('disabled', 'true');
    btnText.style.display = 'none';
    spinner?.classList.remove('hidden');
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    const result = await apiCall('edit', data);
    
    if (result.success) {
        showToast(result.message, 'success');
        closeModal('editModal');
        setTimeout(() => location.reload(), 1000);
    } else {
        showToast(result.message, 'error');
        submitBtn?.removeAttribute('disabled');
        btnText.style.display = 'block';
        spinner?.classList.add('hidden');
    }
}

// ==================== VIEW INSTANCE ====================

async function openViewModal(instanceName) {
    openModal('viewModal');
    
    const container = document.getElementById('viewContent');
    if (!container) return;
    
    const result = await apiCall('getInstanceDetails', { instanceName });
    
    if (result.success) {
        const inst = result.data;
        const status = inst.connectionStatus || 'closed';
        const isOnline = status === 'open';
        
        container.innerHTML = `
            <div style="animation: fadeIn 0.3s ease-out;">
                <div style="display: flex; justify-content: center; margin-bottom: var(--space-lg);">
                    <span class="instance-status ${isOnline ? 'instance-status-online' : 'instance-status-offline'}">
                        ${isOnline ? 'Conectado' : 'Desconectado'}
                    </span>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-md); margin-bottom: var(--space-lg);">
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-md); text-align: center;">
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-bottom: var(--space-xs);">Nome</p>
                        <p style="font-weight: 600; color: var(--color-text-primary); font-size: 0.875rem;">${inst.name || inst.instanceName || 'N/A'}</p>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-md); text-align: center;">
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-bottom: var(--space-xs);">Número</p>
                        <p style="font-weight: 600; color: var(--color-text-primary); font-size: 0.875rem;">${inst.ownerJid || inst.owner || 'N/A'}</p>
                    </div>
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-md); text-align: center;">
                        <p style="font-size: 0.75rem; color: var(--color-text-tertiary); margin-bottom: var(--space-xs);">ID</p>
                        <p style="font-weight: 600; color: var(--color-text-secondary); font-size: 0.75rem;">${inst.id || inst.instanceId || 'N/A'}</p>
                    </div>
                </div>
                
                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: var(--space-lg);">
                    <h4 style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-tertiary); margin-bottom: var(--space-md); display: flex; align-items: center; gap: var(--space-sm);">
                        <i data-lucide="settings-2" style="width: 14px; height: 14px;"></i>
                        Configurações
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--space-md);">
                        ${renderSettingItem('Recusar Chamadas', inst.Setting?.rejectCall)}
                        ${renderSettingItem('Ignorar Grupos', inst.Setting?.groupsIgnore)}
                        ${renderSettingItem('Sempre Online', inst.Setting?.alwaysOnline)}
                        ${renderSettingItem('Ler Mensagens', inst.Setting?.readMessages)}
                        ${renderSettingItem('Ver Status', inst.Setting?.readStatus)}
                        ${renderSettingItem('Sinc. Histórico', inst.Setting?.syncFullHistory)}
                    </div>
                </div>
            </div>
        `;
        lucide.createIcons();
    } else {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon" style="border-color: rgba(239, 68, 68, 0.3); color: var(--color-error);">
                    <i data-lucide="alert-circle" style="width: 32px; height: 32px;"></i>
                </div>
                <h3 class="empty-state-title">Erro ao carregar</h3>
                <p class="empty-state-description">${result.message || 'Não foi possível carregar os detalhes da instância.'}</p>
            </div>
        `;
        lucide.createIcons();
    }
}

function renderSettingItem(label, value) {
    const isEnabled = value === true || value === 'true' || value === 1;
    const icon = isEnabled ? 'check-circle' : 'x-circle';
    const color = isEnabled ? 'var(--color-success)' : 'var(--color-text-muted)';
    
    return `
        <div style="display: flex; align-items: center; gap: var(--space-sm); padding: var(--space-sm); background: rgba(255, 255, 255, 0.03); border-radius: var(--radius-md);">
            <i data-lucide="${icon}" style="width: 16px; height: 16px; color: ${color};"></i>
            <span style="font-size: 0.875rem; color: var(--color-text-secondary);">${label}</span>
        </div>
    `;
}

// ==================== DELETE INSTANCE ====================

function openDeleteModal(instanceName) {
    instanceToDelete = instanceName;
    document.getElementById('deleteInstanceName').textContent = instanceName;
    openModal('deleteModal');
}

async function handleDelete() {
    if (!instanceToDelete) return;
    
    const btnText = document.getElementById('deleteBtnText');
    const spinner = document.getElementById('deleteSpinner');
    const deleteBtn = document.querySelector('#deleteModal button[onclick*="handleDelete"]');
    
    // Loading state
    deleteBtn?.setAttribute('disabled', 'true');
    btnText.style.display = 'none';
    spinner?.classList.remove('hidden');
    
    const result = await apiCall('delete', { instanceName: instanceToDelete });
    
    if (result.success) {
        showToast(result.message, 'success');
        closeModal('deleteModal');
        setTimeout(() => location.reload(), 1000);
    } else {
        showToast(result.message, 'error');
        deleteBtn?.removeAttribute('disabled');
        btnText.style.display = 'block';
        spinner?.classList.add('hidden');
    }
}

// ==================== DEEP LINK (QR) ====================

function openDeepLinkModal(prefilledInstance = '') {
    const form = document.getElementById('deepLinkForm');
    const input = document.getElementById('deepLinkInstanceName');
    const resultBox = document.getElementById('deepLinkResult');
    const resultUrl = document.getElementById('deepLinkUrl');

    if (form) {
        form.reset();
    }

    if (input) {
        input.value = prefilledInstance || '';
    }

    if (resultBox) {
        resultBox.classList.add('hidden');
    }

    if (resultUrl) {
        resultUrl.value = '';
    }

    const submitBtn = document.getElementById('deepLinkSubmitBtn');
    const btnText = document.getElementById('deepLinkBtnText');
    const spinner = document.getElementById('deepLinkSpinner');
    if (submitBtn) submitBtn.disabled = false;
    if (btnText) btnText.style.display = 'block';
    if (spinner) spinner.classList.add('hidden');

    openModal('deepLinkModal');
}

async function handleGenerateDeepLink(e) {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    const submitBtn = document.getElementById('deepLinkSubmitBtn');
    const btnText = document.getElementById('deepLinkBtnText');
    const spinner = document.getElementById('deepLinkSpinner');
    const resultBox = document.getElementById('deepLinkResult');
    const resultUrl = document.getElementById('deepLinkUrl');

    submitBtn.disabled = true;
    btnText.style.display = 'none';
    spinner.classList.remove('hidden');

    const result = await apiCall('generateDeepLink', data);

    submitBtn.disabled = false;
    btnText.style.display = 'block';
    spinner.classList.add('hidden');

    if (!result.success || !result.data?.url) {
        showToast(result.message || 'Falha ao gerar deep link', 'error');
        return;
    }

    resultUrl.value = result.data.url;
    resultBox.classList.remove('hidden');
    showToast('Deep link gerado com sucesso', 'success');
}

async function quickGenerateDeepLink(instanceName) {
    const result = await apiCall('generateDeepLink', { instanceName: instanceName });

    if (!result.success || !result.data?.url) {
        showToast(result.message || 'Falha ao gerar deep link', 'error');
        return;
    }

    try {
        await navigator.clipboard.writeText(result.data.url);
        showToast('Deep link copiado para a area de transferencia', 'success');
    } catch (error) {
        openDeepLinkModal(instanceName);
        const resultBox = document.getElementById('deepLinkResult');
        const resultUrl = document.getElementById('deepLinkUrl');
        resultUrl.value = result.data.url;
        resultBox.classList.remove('hidden');
        showToast('Nao foi possivel copiar automaticamente. Copie manualmente.', 'warning');
    }
}

async function copyGeneratedDeepLink() {
    const deepLinkUrl = document.getElementById('deepLinkUrl')?.value || '';
    if (!deepLinkUrl) {
        showToast('Gere um link antes de copiar', 'warning');
        return;
    }

    try {
        await navigator.clipboard.writeText(deepLinkUrl);
        showToast('Link copiado com sucesso', 'success');
    } catch (error) {
        showToast('Falha ao copiar link. Copie manualmente.', 'warning');
    }
}

function openGeneratedDeepLink() {
    const deepLinkUrl = document.getElementById('deepLinkUrl')?.value || '';
    if (!deepLinkUrl) {
        showToast('Gere um link antes de abrir', 'warning');
        return;
    }

    window.open(deepLinkUrl, '_blank', 'noopener,noreferrer');
}

// ==================== SEARCH & FILTER ====================

const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const instancesGrid = document.getElementById('instancesGrid');

function filterInstances() {
    const searchTerm = searchInput?.value.toLowerCase().trim() || '';
    const statusValue = statusFilter?.value || 'all';
    const cards = instancesGrid?.querySelectorAll('.instance-card');
    
    if (!cards) return;
    
    let visibleCount = 0;
    
    cards.forEach(card => {
        const nameEl = card.querySelector('.instance-name');
        const statusEl = card.querySelector('.instance-status');
        
        const name = nameEl?.textContent.toLowerCase() || '';
        const status = statusEl?.textContent.toLowerCase() || '';
        
        const matchesSearch = !searchTerm || name.includes(searchTerm);
        const matchesStatus = statusValue === 'all' || 
            (statusValue === 'online' && status.includes('conectado')) ||
            (statusValue === 'offline' && status.includes('desconectado'));
        
        if (matchesSearch && matchesStatus) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateNoResultsMessage(visibleCount);
    updatePagination();
}

function updateNoResultsMessage(visibleCount) {
    let noResultsMsg = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'noResultsMessage';
            noResultsMsg.className = 'empty-state';
            noResultsMsg.style.gridColumn = '1 / -1';
            noResultsMsg.innerHTML = `
                <div class="empty-state-icon">
                    <i data-lucide="search-x" style="width: 40px; height: 40px;"></i>
                </div>
                <h3 class="empty-state-title">Nenhuma instância encontrada</h3>
                <p class="empty-state-description">Tente ajustar seus filtros de busca.</p>
            `;
            instancesGrid.appendChild(noResultsMsg);
            lucide.createIcons();
        }
        noResultsMsg.style.display = 'block';
    } else if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
    }
}

searchInput?.addEventListener('input', filterInstances);
statusFilter?.addEventListener('change', filterInstances);

// ==================== PAGINATION ====================

function updatePagination() {
    const cards = Array.from(instancesGrid?.querySelectorAll('.instance-card') || []);
    const visibleCards = cards.filter(card => card.style.display !== 'none');
    
    const totalPages = Math.ceil(visibleCards.length / ITEMS_PER_PAGE) || 1;
    
    if (currentPageNum > totalPages) {
        currentPageNum = totalPages;
    }
    
    visibleCards.forEach((card, index) => {
        const pageIndex = Math.floor(index / ITEMS_PER_PAGE) + 1;
        card.style.display = pageIndex === currentPageNum ? 'flex' : 'none';
    });
    
    document.getElementById('currentPage').textContent = currentPageNum;
    document.getElementById('totalPages').textContent = totalPages;
    document.getElementById('prevPage').disabled = currentPageNum <= 1;
    document.getElementById('nextPage').disabled = currentPageNum >= totalPages;
}

function changePage(direction) {
    currentPageNum += direction;
    updatePagination();
    instancesGrid?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Initialize pagination
document.addEventListener('DOMContentLoaded', updatePagination);

// ==================== COUNTER ANIMATION ====================

function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 1500;
        const start = performance.now();
        
        function updateCounter(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = Math.floor(easeOut * target);
            
            counter.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        }
        
        requestAnimationFrame(updateCounter);
    });
}

window.addEventListener('load', animateCounters);

// ==================== REFRESH BUTTON ====================

document.getElementById('refreshBtn')?.addEventListener('click', function() {
    this.style.animation = 'spin 0.5s ease-out';
    setTimeout(() => {
        this.style.animation = '';
        location.reload();
    }, 500);
});

// ==================== INTERSECTION OBSERVER ====================

const observerOptions = { threshold: 0.1 };
const cardObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.instance-card').forEach(card => {
    cardObserver.observe(card);
});

// ==================== BUTTON RIPPLE EFFECT ====================

document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const ripple = document.createElement('span');
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: scale(0);
            animation: rippleEffect 0.6s ease-out;
            pointer-events: none;
        `;
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
});

// Add ripple keyframes
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes rippleEffect {
        to { transform: scale(2); opacity: 0; }
    }
`;
document.head.appendChild(rippleStyle);

// ==================== INSTANCE CARD HOVER EFFECTS ====================

document.querySelectorAll('.instance-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-6px) scale(1.01)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = '';
    });
});

// ==================== KEYBOARD NAVIGATION ====================

document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
    
    // N for new instance
    if (e.key === 'n' && !e.ctrlKey && !e.metaKey && !e.altKey) {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (!activeModal && document.activeElement?.tagName !== 'INPUT') {
            e.preventDefault();
            openCreateModal();
        }
    }
});

// ==================== ACCESSIBILITY ====================

// Enhanced focus indicators
document.querySelectorAll('button, input, select').forEach(el => {
    el.addEventListener('focus', function() {
        this.style.outline = '2px solid var(--color-accent-primary)';
        this.style.outlineOffset = '2px';
    });
    
    el.addEventListener('blur', function() {
        this.style.outline = '';
        this.style.outlineOffset = '';
    });
});

// ==================== CONNECT INSTANCE ====================

let currentConnectInstance = null;
let countdownInterval = null;
let pollingInterval = null;
let pairingSyncInterval = null;
let currentPairingCode = null;
let currentPhoneNumber = null;
let pairingSyncInFlight = false;
let pairingSyncErrorShownAt = 0;
let lastExpirySyncMarker = 0;

const PAIRING_SYNC_INTERVAL_MS = 45000;
const STATUS_POLLING_INTERVAL_MS = 3000;
const SYNC_ERROR_TOAST_COOLDOWN_MS = 20000;

function isConnectModalOpen() {
    return document.getElementById('connectModal')?.classList.contains('active') === true;
}

function stopCountdown() {
    if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
    }
}

function resetPairingCountdownUI() {
    const timerContainer = document.getElementById('timerContainer');
    const retryBtn = document.getElementById('retryBtn');

    timerContainer?.classList.add('hidden');
    if (retryBtn) {
        retryBtn.disabled = true;
        retryBtn.innerHTML = '<i data-lucide="refresh-cw" style="width: 16px; height: 16px; margin-right: var(--space-xs);"></i>Pedir Outro';
        lucide.createIcons();
    }
}

function openConnectModal(instanceName) {
    currentConnectInstance = instanceName;
    stopPollingStatus();
    stopPairingSyncPolling();
    stopCountdown();
    currentPairingCode = null;
    currentPhoneNumber = null;
    pairingSyncErrorShownAt = 0;

    document.getElementById('connectInstanceName').value = instanceName;
    document.getElementById('connectInstanceDisplay').textContent = instanceName;
    document.getElementById('connectPhoneNumber').value = '';
    document.getElementById('connectPhoneNumber').disabled = false;

    // Reset UI states
    document.getElementById('pairingCodeContainer').classList.add('hidden');
    document.getElementById('timerContainer').classList.add('hidden');
    document.getElementById('retryBtn').classList.add('hidden');
    document.getElementById('connectSubmitBtn').classList.remove('hidden');
    document.getElementById('connectSubmitBtn').disabled = false;
    document.getElementById('connectBtnText').style.display = 'block';
    document.getElementById('connectSpinner').classList.add('hidden');
    document.getElementById('pairingCodeDisplay').textContent = '';
    resetPairingCountdownUI();

    openModal('connectModal');

    // Focus phone input
    setTimeout(() => {
        document.getElementById('connectPhoneNumber')?.focus();
    }, 100);
}

async function handleConnect(e) {
    e.preventDefault();

    const btnText = document.getElementById('connectBtnText');
    const spinner = document.getElementById('connectSpinner');
    const submitBtn = document.getElementById('connectSubmitBtn');
    const phoneInput = document.getElementById('connectPhoneNumber');
    const phoneNumber = phoneInput.value.replace(/[^0-9]/g, '');

    // Validation
    if (phoneNumber.length < 10) {
        showToast('Numero deve ter pelo menos 10 digitos', 'warning');
        return;
    }

    // Loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    spinner.classList.remove('hidden');

    const result = await apiCall('connect', {
        instanceName: currentConnectInstance,
        phoneNumber: phoneNumber
    });

    if (result.success && result.data?.pairingCode) {
        currentPhoneNumber = phoneNumber;
        applyPairingData(result.data);

        // Disable phone input after success
        phoneInput.disabled = true;

        // Hide submit, show retry with timer
        submitBtn.classList.add('hidden');
        document.getElementById('retryBtn').classList.remove('hidden');
        document.getElementById('retryBtn').disabled = true;

        startPollingStatus();
        startPairingSyncPolling();
        showToast(result.message, 'success');
    } else {
        const isConnectivityError = result.errorCode === 'CONNECTION_REFUSED' || result.errorCode === 'API_UNAVAILABLE';
        const toastType = isConnectivityError ? 'warning' : 'error';
        showToast(result.message || 'Erro ao gerar codigo', toastType);

        submitBtn.disabled = false;
        btnText.style.display = 'block';
        spinner.classList.add('hidden');
    }
}

function applyPairingData(pairingData) {
    const pairingCode = pairingData.pairingCode;
    const expiresAt = Number(pairingData.expiresAt || 0);

    currentPairingCode = pairingCode;
    lastExpirySyncMarker = 0;

    document.getElementById('pairingCodeDisplay').textContent = pairingCode;
    document.getElementById('pairingCodeContainer').classList.remove('hidden');

    if (expiresAt > 0) {
        startCountdown(expiresAt);
    }
}

function startCountdown(expiresAtSeconds) {
    const countdownEl = document.getElementById('countdown');
    const timerContainer = document.getElementById('timerContainer');
    const retryBtn = document.getElementById('retryBtn');
    const expiryMs = Number(expiresAtSeconds) * 1000;

    stopCountdown();

    timerContainer.classList.remove('hidden');
    retryBtn.disabled = true;

    function updateTimer() {
        const remainingSeconds = Math.max(0, Math.ceil((expiryMs - Date.now()) / 1000));
        const mins = Math.floor(remainingSeconds / 60);
        const secs = remainingSeconds % 60;
        countdownEl.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;

        if (remainingSeconds <= 0) {
            stopCountdown();
            timerContainer.classList.add('hidden');
            retryBtn.disabled = false;
            retryBtn.innerHTML = '<i data-lucide="refresh-cw" style="width: 16px; height: 16px; margin-right: var(--space-xs);"></i>Pedir Outro Código';
            lucide.createIcons();
            return;
        }

        if (remainingSeconds <= 5 && lastExpirySyncMarker !== expiryMs) {
            lastExpirySyncMarker = expiryMs;
            syncPairingCode();
        }
    }

    updateTimer();
    countdownInterval = setInterval(updateTimer, 1000);
}

async function retryPairingCode() {
    stopPollingStatus();
    stopPairingSyncPolling();
    stopCountdown();

    // Re-enable phone input
    document.getElementById('connectPhoneNumber').disabled = false;
    document.getElementById('connectPhoneNumber').value = '';

    // Hide results
    document.getElementById('pairingCodeContainer').classList.add('hidden');
    document.getElementById('timerContainer').classList.add('hidden');

    // Show submit button again
    document.getElementById('retryBtn').classList.add('hidden');
    document.getElementById('connectSubmitBtn').classList.remove('hidden');
    document.getElementById('connectSubmitBtn').disabled = false;
    
    // Reset stored values
    currentPairingCode = null;
    currentPhoneNumber = null;
    pairingSyncErrorShownAt = 0;

    // Focus phone input
    setTimeout(() => {
        document.getElementById('connectPhoneNumber')?.focus();
    }, 100);
}

// ==================== POLLING STATUS ====================

function startPollingStatus() {
    stopPollingStatus();

    // Poll every 3 seconds
    pollingInterval = setInterval(async () => {
        await checkInstanceStatus();
    }, STATUS_POLLING_INTERVAL_MS);

    checkInstanceStatus();
}

function stopPollingStatus() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

function startPairingSyncPolling() {
    if (!currentConnectInstance || !currentPhoneNumber) {
        return;
    }

    stopPairingSyncPolling();
    pairingSyncInterval = setInterval(async () => {
        await syncPairingCode();
    }, PAIRING_SYNC_INTERVAL_MS);

    syncPairingCode();
}

function stopPairingSyncPolling() {
    if (pairingSyncInterval) {
        clearInterval(pairingSyncInterval);
        pairingSyncInterval = null;
    }
}

async function syncPairingCode() {
    if (pairingSyncInFlight || !currentConnectInstance || !currentPhoneNumber || !isConnectModalOpen()) {
        return;
    }

    pairingSyncInFlight = true;

    try {
        const result = await apiCall('syncPairing', {
            instanceName: currentConnectInstance,
            phoneNumber: currentPhoneNumber,
            lastPairingCode: currentPairingCode || ''
        }, 1);

        if (result.success && result.data?.pairingCode) {
            const previousCode = currentPairingCode;
            applyPairingData(result.data);

            showToast('Codigo verificado na Evolution API.', 'info', 1600);

            if (previousCode && result.data.changed) {
                showToast('Codigo atualizado automaticamente.', 'info', 2200);
            }
            return;
        }

        const now = Date.now();
        const shouldNotify = now - pairingSyncErrorShownAt > SYNC_ERROR_TOAST_COOLDOWN_MS;

        if (result.errorCode !== 'PAIRING_PENDING' && shouldNotify) {
            pairingSyncErrorShownAt = now;
            const toastType = result.errorCode === 'CONNECTION_REFUSED' ? 'warning' : 'info';
            showToast(result.message || 'Falha temporaria ao sincronizar codigo', toastType, 2200);
        }
    } finally {
        pairingSyncInFlight = false;
    }
}

async function checkInstanceStatus() {
    if (!currentConnectInstance) return;
    
    // Polling ONLY checks connection status, does NOT generate new pairing codes
    const result = await apiCall('checkStatus', {
        instanceName: currentConnectInstance
    });
    
    if (!result.success) return;
    
    // Check if connected
    if (result.data?.state === 'open' || result.data?.connected === true) {
        // Connected! Stop polling and show success
        stopPollingStatus();
        stopPairingSyncPolling();
        stopCountdown();
        showToast('WhatsApp conectado com sucesso!', 'success');
        
        // Update modal to show connected status
        document.getElementById('pairingCodeContainer').innerHTML = `
            <div style="color: var(--color-success);">
                <i data-lucide="check-circle" style="width: 48px; height: 48px; margin-bottom: var(--space-md);"></i>
                <p style="font-size: 1.125rem; font-weight: 600; margin-bottom: var(--space-sm);">Conectado!</p>
                <p style="font-size: 0.875rem; color: var(--color-text-secondary);">Seu WhatsApp foi conectado com sucesso.</p>
            </div>
        `;
        lucide.createIcons();
        
        // Hide timer and retry button
        document.getElementById('timerContainer').classList.add('hidden');
        document.getElementById('retryBtn').classList.add('hidden');
        
        // Reload page after 2 seconds to show updated status
        setTimeout(() => {
            location.reload();
        }, 2000);
        return;
    }
}

// Announce page updates to screen readers
function announceToScreenReader(message) {
    const announcement = document.createElement('div');
    announcement.setAttribute('role', 'status');
    announcement.setAttribute('aria-live', 'polite');
    announcement.className = 'sr-only';
    announcement.textContent = message;
    document.body.appendChild(announcement);
    setTimeout(() => announcement.remove(), 1000);
}
