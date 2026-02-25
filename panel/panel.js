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
let deepLinkExpiresAt = 0;
let deepLinkInitialTtlSeconds = 0;
let deepLinkExpiryInterval = null;
let deepLinkExpiredNotified = false;

// ==================== THEME MANAGEMENT ====================

function loadTheme() {
    const savedTheme = localStorage.getItem('panel-theme');
    const root = document.documentElement;
    const isLight = savedTheme === 'light';
    root.classList.toggle('dark', !isLight);
    updateThemeIcons(isLight);
}

function toggleTheme() {
    const root = document.documentElement;
    const isLight = root.classList.contains('dark');
    root.classList.toggle('dark', !isLight);
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

    modal.classList.remove('hidden');
    modal.classList.add('flex');
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
    modal.classList.remove('flex');
    modal.classList.add('hidden');
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

    if (modalId === 'deepLinkModal') {
        stopDeepLinkExpiryTimer();
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
    if (typeof window.Toastify === 'function') {
        const toastStyles = {
            success: 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)',
            error: 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)',
            warning: 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
            info: 'linear-gradient(135deg, #f97316 0%, #dc2626 100%)'
        };

        window.Toastify({
            text: message,
            duration: duration,
            gravity: 'top',
            position: 'right',
            close: true,
            stopOnFocus: true,
            className: `toast-${type}`,
            style: {
                background: toastStyles[type] || toastStyles.info,
                color: '#fff',
                borderRadius: '10px',
                boxShadow: '0 14px 32px rgba(0, 0, 0, 0.35)',
                border: '1px solid rgba(255, 255, 255, 0.2)',
                fontWeight: '600',
                fontSize: '13px'
            }
        }).showToast();
        return;
    }

    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const icons = {
        success: 'check-circle',
        error: 'alert-circle',
        warning: 'alert-triangle',
        info: 'info'
    };
    
    const toneClass = {
        success: 'border-emerald-400/40 bg-emerald-500/15 text-emerald-100',
        error: 'border-red-400/40 bg-red-500/15 text-red-100',
        warning: 'border-amber-400/40 bg-amber-500/15 text-amber-100',
        info: 'border-sky-400/40 bg-sky-500/15 text-sky-100'
    };

    const toast = document.createElement('div');
    toast.className = `toast rounded-xl border px-3 py-2 shadow-xl backdrop-blur ${toneClass[type] || toneClass.info}`;
    toast.innerHTML = `
        <div class="inline-flex items-start gap-2.5">
            <div class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md border border-white/30 bg-white/10">
                <i data-lucide="${icons[type]}" class="h-4 w-4"></i>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-bold uppercase tracking-[0.08em]">${type === 'success' ? 'Sucesso' : type === 'error' ? 'Erro' : type === 'warning' ? 'Atencao' : 'Info'}</div>
                <div class="text-sm">${message}</div>
            </div>
        </div>
    `;
    
    container.appendChild(toast);
    lucide.createIcons();
    
    // Auto-remove
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-3', 'transition');
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
        this.classList.add('border-red-400', 'ring-2', 'ring-red-500/30');
        this.classList.remove('border-emerald-400', 'ring-emerald-500/30');
        submitBtn?.setAttribute('disabled', 'true');
    } else {
        this.classList.remove('border-red-400', 'ring-red-500/30');
        this.classList.add('border-emerald-400', 'ring-2', 'ring-emerald-500/30');
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
            <div class="animate-riseIn">
                <div class="mb-5 flex justify-center">
                    <span class="instance-status ${isOnline ? 'instance-status-online text-emerald-700 bg-emerald-500/15 border-emerald-400/40 dark:text-emerald-300' : 'instance-status-offline text-red-700 bg-red-500/15 border-red-400/40 dark:text-red-300'} inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.06em]">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        ${isOnline ? 'Conectado' : 'Desconectado'}
                    </span>
                </div>

                <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center dark:border-slate-700 dark:bg-slate-950/70">
                        <p class="mb-1 text-xs text-slate-500">Nome</p>
                        <p class="text-sm font-semibold">${inst.name || inst.instanceName || 'N/A'}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center dark:border-slate-700 dark:bg-slate-950/70">
                        <p class="mb-1 text-xs text-slate-500">Numero</p>
                        <p class="text-sm font-semibold">${inst.ownerJid || inst.owner || 'N/A'}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center dark:border-slate-700 dark:bg-slate-950/70">
                        <p class="mb-1 text-xs text-slate-500">ID</p>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">${inst.id || inst.instanceId || 'N/A'}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/70">
                    <h4 class="mb-3 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">
                        <i data-lucide="settings-2" class="h-3.5 w-3.5"></i>
                        Configurações
                    </h4>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
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
            <div class="empty-state rounded-xl border border-red-400/35 bg-red-500/10 p-6 text-center">
                <div class="empty-state-icon mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full border border-red-400/45 text-red-500 dark:text-red-300">
                    <i data-lucide="alert-circle" class="h-8 w-8"></i>
                </div>
                <h3 class="empty-state-title text-lg font-semibold">Erro ao carregar</h3>
                <p class="empty-state-description mt-1 text-sm text-slate-600 dark:text-slate-300">${result.message || 'Nao foi possivel carregar os detalhes da instancia.'}</p>
            </div>
        `;
        lucide.createIcons();
    }
}

function renderSettingItem(label, value) {
    const isEnabled = value === true || value === 'true' || value === 1;
    const icon = isEnabled ? 'check-circle' : 'x-circle';
    const color = isEnabled ? '#34d399' : '#94a3b8';
    
    return `
        <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
            <i data-lucide="${icon}" class="h-4 w-4" style="color: ${color};"></i>
            <span class="text-sm text-slate-600 dark:text-slate-300">${label}</span>
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

function stopDeepLinkExpiryTimer() {
    if (deepLinkExpiryInterval) {
        clearInterval(deepLinkExpiryInterval);
        deepLinkExpiryInterval = null;
    }
}

function formatDeepLinkRemaining(totalSeconds) {
    const safeSeconds = Math.max(0, Math.floor(totalSeconds));
    const days = Math.floor(safeSeconds / 86400);
    const hours = Math.floor((safeSeconds % 86400) / 3600);
    const minutes = Math.floor((safeSeconds % 3600) / 60);
    const seconds = safeSeconds % 60;

    if (days > 0) {
        return `${days}d ${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m`;
    }

    if (hours > 0) {
        return `${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m`;
    }

    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

function parseDeepLinkExpiryFromUrl(deepLinkUrl) {
    if (!deepLinkUrl) return 0;

    try {
        const parsedUrl = new URL(deepLinkUrl);
        return Number(parsedUrl.searchParams.get('exp') || 0);
    } catch (_error) {
        return 0;
    }
}

function isGeneratedDeepLinkExpired() {
    return deepLinkExpiresAt > 0 && Date.now() >= deepLinkExpiresAt * 1000;
}

function updateDeepLinkExpiryUI() {
    const expiryBox = document.getElementById('deepLinkExpiryBox');
    const expiryLabel = document.getElementById('deepLinkExpiryLabel');
    const expiryAt = document.getElementById('deepLinkExpiryAt');
    const expiryProgress = document.getElementById('deepLinkExpiryProgress');

    if (!expiryBox || !expiryLabel || !expiryAt || !expiryProgress) {
        return;
    }

    if (!deepLinkExpiresAt) {
        expiryBox.classList.remove('border-red-400/45', 'bg-red-500/12');
        expiryBox.classList.add('border-orange-400/35', 'bg-orange-500/10');
        expiryLabel.classList.remove('text-red-200');
        expiryLabel.classList.add('text-orange-700', 'dark:text-orange-300');
        expiryLabel.textContent = 'Expira em --:--';
        expiryAt.textContent = '--/--/---- --:--';
        expiryProgress.style.width = '100%';
        return;
    }

    const remainingSeconds = Math.max(0, Math.ceil((deepLinkExpiresAt * 1000 - Date.now()) / 1000));
    const progressPercent = deepLinkInitialTtlSeconds > 0
        ? Math.max(0, Math.min(100, (remainingSeconds / deepLinkInitialTtlSeconds) * 100))
        : 0;

    expiryAt.textContent = new Date(deepLinkExpiresAt * 1000).toLocaleString('pt-BR');
    expiryProgress.style.width = `${progressPercent}%`;

    if (remainingSeconds <= 0) {
        expiryBox.classList.remove('border-orange-400/35', 'bg-orange-500/10');
        expiryBox.classList.add('border-red-400/45', 'bg-red-500/12');
        expiryLabel.classList.remove('text-orange-700', 'dark:text-orange-300');
        expiryLabel.classList.add('text-red-200');
        expiryLabel.textContent = 'Deep link expirado';
        expiryProgress.style.width = '0%';

        if (!deepLinkExpiredNotified) {
            deepLinkExpiredNotified = true;
            showToast('Este deep link expirou. Gere um novo para continuar.', 'warning', 5000);
        }

        stopDeepLinkExpiryTimer();
        return;
    }

    expiryBox.classList.remove('border-red-400/45', 'bg-red-500/12');
    expiryBox.classList.add('border-orange-400/35', 'bg-orange-500/10');
    expiryLabel.classList.remove('text-red-200');
    expiryLabel.classList.add('text-orange-700', 'dark:text-orange-300');
    expiryLabel.textContent = `Expira em ${formatDeepLinkRemaining(remainingSeconds)}`;
}

function setDeepLinkExpiry(expiresAt, ttlSeconds) {
    const parsedExpiresAt = Number(expiresAt || 0);
    deepLinkExpiresAt = parsedExpiresAt > 0 ? parsedExpiresAt : 0;

    if (deepLinkExpiresAt > 0) {
        const normalizedTtl = Number(ttlSeconds || 0);
        deepLinkInitialTtlSeconds = normalizedTtl > 0
            ? normalizedTtl
            : Math.max(1, Math.ceil(deepLinkExpiresAt - Date.now() / 1000));
    } else {
        deepLinkInitialTtlSeconds = 0;
    }

    deepLinkExpiredNotified = false;
    stopDeepLinkExpiryTimer();
    updateDeepLinkExpiryUI();

    if (deepLinkExpiresAt > 0 && !isGeneratedDeepLinkExpired()) {
        deepLinkExpiryInterval = setInterval(updateDeepLinkExpiryUI, 1000);
    }
}

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

    setDeepLinkExpiry(0, 0);

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
    setDeepLinkExpiry(result.data.expiresAt || parseDeepLinkExpiryFromUrl(result.data.url), result.data.ttlSeconds || 0);
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
        const expiresAt = Number(result.data.expiresAt || parseDeepLinkExpiryFromUrl(result.data.url));
        if (expiresAt > 0) {
            const remaining = Math.max(0, Math.ceil(expiresAt - Date.now() / 1000));
            showToast(`Deep link copiado. Expira em ${formatDeepLinkRemaining(remaining)}.`, 'success');
        } else {
            showToast('Deep link copiado para a area de transferencia', 'success');
        }
    } catch (error) {
        openDeepLinkModal(instanceName);
        const resultBox = document.getElementById('deepLinkResult');
        const resultUrl = document.getElementById('deepLinkUrl');
        resultUrl.value = result.data.url;
        resultBox.classList.remove('hidden');
        setDeepLinkExpiry(result.data.expiresAt || parseDeepLinkExpiryFromUrl(result.data.url), result.data.ttlSeconds || 0);
        showToast('Nao foi possivel copiar automaticamente. Copie manualmente.', 'warning');
    }
}

async function copyGeneratedDeepLink() {
    const deepLinkUrl = document.getElementById('deepLinkUrl')?.value || '';
    if (!deepLinkUrl) {
        showToast('Gere um link antes de copiar', 'warning');
        return;
    }

    if (isGeneratedDeepLinkExpired()) {
        showToast('Este deep link ja expirou. Gere um novo antes de copiar.', 'warning');
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

    if (isGeneratedDeepLinkExpired()) {
        showToast('Este deep link expirou. Gere um novo antes de abrir.', 'warning');
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
            card.style.display = 'block';
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
            noResultsMsg.className = 'empty-state col-span-full rounded-xl border border-slate-300 bg-white/90 px-6 py-10 text-center dark:border-slate-800 dark:bg-slate-900/70';
            noResultsMsg.style.gridColumn = '1 / -1';
            noResultsMsg.innerHTML = `
                <div class="empty-state-icon mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-slate-300 bg-slate-100 text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                    <i data-lucide="search-x" class="h-10 w-10"></i>
                </div>
                <h3 class="empty-state-title text-xl font-semibold">Nenhuma instancia encontrada</h3>
                <p class="empty-state-description mt-2 text-sm text-slate-600 dark:text-slate-300">Tente ajustar seus filtros de busca.</p>
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
        card.style.display = pageIndex === currentPageNum ? 'block' : 'none';
    });

    const currentPage = document.getElementById('currentPage');
    const totalPagesEl = document.getElementById('totalPages');
    const prevPage = document.getElementById('prevPage');
    const nextPage = document.getElementById('nextPage');

    if (!currentPage || !totalPagesEl || !prevPage || !nextPage) {
        return;
    }

    currentPage.textContent = currentPageNum;
    totalPagesEl.textContent = totalPages;
    prevPage.disabled = currentPageNum <= 1;
    nextPage.disabled = currentPageNum >= totalPages;
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
        this.style.outline = '2px solid #f97316';
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
        retryBtn.innerHTML = '<i data-lucide="refresh-cw" class="mr-1 h-4 w-4"></i>Pedir Outro';
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
            retryBtn.innerHTML = '<i data-lucide="refresh-cw" class="mr-1 h-4 w-4"></i>Pedir Outro Codigo';
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
            <div class="text-emerald-600 dark:text-emerald-300">
                <i data-lucide="check-circle" class="mx-auto mb-3 h-12 w-12"></i>
                <p class="mb-1 text-lg font-semibold">Conectado!</p>
                <p class="text-sm text-slate-600 dark:text-slate-300">Seu WhatsApp foi conectado com sucesso.</p>
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
