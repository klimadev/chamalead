<?php

require_once 'Config.php';
require_once 'DeepLinkService.php';

Config::load();

$instanceName = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['instance'] ?? '');
$expiresAt = (int)($_GET['exp'] ?? 0);
$signature = (string)($_GET['sig'] ?? '');

$isValid = DeepLinkService::validate($instanceName, $expiresAt, $signature);

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");

if (!$isValid) {
    http_response_code(403);
}

?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectar WhatsApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes qr-refresh-pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(34, 211, 238, 0.18);
            }
            50% {
                transform: scale(1.02);
                box-shadow: 0 0 0 8px rgba(34, 211, 238, 0);
            }
        }

        .qr-refresh-pulse {
            animation: qr-refresh-pulse 1.8s ease-in-out infinite;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_15%_10%,rgba(56,189,248,0.2),transparent_34%),radial-gradient(circle_at_84%_8%,rgba(249,115,22,0.24),transparent_36%),radial-gradient(circle_at_50%_120%,rgba(15,23,42,0.52),transparent_50%)]"></div>
    <main class="mx-auto flex h-full w-full max-w-lg items-center px-4">
        <section class="w-full rounded-2xl border border-slate-700 bg-slate-900/85 p-5 shadow-2xl shadow-slate-950/50 backdrop-blur-xl sm:p-6">
            <h1 class="text-xl font-bold">Conectar WhatsApp</h1>
            <p class="mt-1 text-xs text-slate-400">Escaneie o QR code no WhatsApp. Se ele expirar, toque em Renovar QR para gerar outro na hora.</p>

            <?php if ($isValid): ?>
                <p class="mt-3 inline-flex rounded-full border border-slate-700 bg-slate-950/60 px-3 py-1 text-[11px] font-medium text-slate-300">Instancia: <?= htmlspecialchars($instanceName, ENT_QUOTES, 'UTF-8') ?></p>
                <div class="mt-3 rounded-xl border border-orange-400/35 bg-orange-500/10 p-3" id="expiryBox" role="status" aria-live="polite">
                    <div class="flex items-center justify-between gap-2 text-xs">
                        <span class="font-bold text-orange-300" id="expiryLabel">Expira em --:--</span>
                        <span class="text-slate-300" id="expiryAt">--/--/---- --:--</span>
                    </div>
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/20" aria-hidden="true">
                        <div class="h-full w-full rounded-full bg-gradient-to-r from-orange-400 via-orange-500 to-red-600 transition-[width] duration-1000" id="expiryProgress"></div>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-slate-800 bg-slate-950/40 px-3 py-2">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Status do QR</p>
                        <p class="mt-1 text-sm font-medium text-slate-200" id="qrStateLabel">Preparando conexao segura...</p>
                        <p class="mt-1 text-xs text-slate-400" id="qrUpdatedAt">Ultima atualizacao: aguardando primeiro QR...</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex rounded-full border border-slate-700 bg-slate-900 px-2.5 py-1 text-[11px] font-medium text-slate-300" id="qrFreshnessBadge">Aguardando</span>
                        <button type="button" id="refreshQrButton" class="inline-flex items-center justify-center rounded-lg border border-cyan-400/40 bg-cyan-500/10 px-3 py-2 text-xs font-semibold text-cyan-200 transition hover:border-cyan-300 hover:bg-cyan-500/20 disabled:cursor-not-allowed disabled:opacity-50">
                            Renovar QR
                        </button>
                    </div>
                </div>
                <section class="mt-3 flex min-h-[220px] flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-slate-600 bg-slate-950/40 p-3 text-center transition-colors" id="qrWrap">
                    <p id="qrPlaceholder" class="text-sm text-slate-400">Preparando o QR code...</p>
                </section>
                <p class="status mt-3 text-center text-xs text-slate-400" id="statusText">Aguardando o primeiro QR code...</p>
                <ol class="mt-3 list-decimal space-y-1 pl-5 text-xs text-slate-300">
                    <li>Abra o WhatsApp no celular.</li>
                    <li>Toque em Dispositivos conectados.</li>
                    <li>Escaneie o QR code exibido nesta tela.</li>
                </ol>
            <?php else: ?>
                <section class="mt-4 flex min-h-[180px] items-center justify-center rounded-xl border border-red-400/35 bg-red-500/10 p-4 text-center">
                    <p class="text-sm text-red-200">Este link expirou ou e invalido.</p>
                </section>
                <p class="mt-3 text-center text-xs text-red-300">Solicite um novo link ao administrador.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php if ($isValid): ?>
        <script>
            const deepLinkPayload = {
                instance: <?= json_encode($instanceName) ?>,
                exp: <?= json_encode($expiresAt) ?>,
                sig: <?= json_encode($signature) ?>
            };

            let pollTimer = null;
            let lastQrHash = '';
            let inFlight = false;
            let expiryTimer = null;
            let qrState = 'loading';
            let qrLastUpdatedAt = 0;

            const qrWrap = document.getElementById('qrWrap');
            const statusText = document.getElementById('statusText');
            const expiryBox = document.getElementById('expiryBox');
            const expiryLabel = document.getElementById('expiryLabel');
            const expiryAt = document.getElementById('expiryAt');
            const expiryProgress = document.getElementById('expiryProgress');
            const qrStateLabel = document.getElementById('qrStateLabel');
            const qrUpdatedAt = document.getElementById('qrUpdatedAt');
            const qrFreshnessBadge = document.getElementById('qrFreshnessBadge');
            const refreshQrButton = document.getElementById('refreshQrButton');
            const expiryInitialSeconds = Math.max(1, Math.ceil(deepLinkPayload.exp - Date.now() / 1000));
            const qrStaleAfterMs = 30000;

            async function sha256(text) {
                const data = new TextEncoder().encode(text);
                const digest = await crypto.subtle.digest('SHA-256', data);
                return Array.from(new Uint8Array(digest)).map((byte) => byte.toString(16).padStart(2, '0')).join('');
            }

            function formatRemaining(seconds) {
                const safe = Math.max(0, Math.floor(seconds));
                const days = Math.floor(safe / 86400);
                const hours = Math.floor((safe % 86400) / 3600);
                const mins = Math.floor((safe % 3600) / 60);
                const secs = safe % 60;

                if (days > 0) {
                    return `${days}d ${String(hours).padStart(2, '0')}h ${String(mins).padStart(2, '0')}m`;
                }

                if (hours > 0) {
                    return `${String(hours).padStart(2, '0')}h ${String(mins).padStart(2, '0')}m`;
                }

                return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            function stopAllTimers() {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
                if (expiryTimer) {
                    clearInterval(expiryTimer);
                    expiryTimer = null;
                }
            }

            function setExpiryVisualExpired(expired) {
                if (expired) {
                    expiryBox.classList.remove('border-orange-400/35', 'bg-orange-500/10');
                    expiryBox.classList.add('border-red-400/45', 'bg-red-500/12');
                    expiryLabel.classList.remove('text-orange-300');
                    expiryLabel.classList.add('text-red-200');
                    return;
                }

                expiryBox.classList.add('border-orange-400/35', 'bg-orange-500/10');
                expiryBox.classList.remove('border-red-400/45', 'bg-red-500/12');
                expiryLabel.classList.add('text-orange-300');
                expiryLabel.classList.remove('text-red-200');
            }

            function updateExpiryState() {
                const nowMs = Date.now();
                const remainingSeconds = Math.max(0, Math.ceil((deepLinkPayload.exp * 1000 - nowMs) / 1000));
                const progressPercent = Math.max(0, Math.min(100, (remainingSeconds / expiryInitialSeconds) * 100));

                expiryAt.textContent = new Date(deepLinkPayload.exp * 1000).toLocaleString('pt-BR');
                expiryProgress.style.width = `${progressPercent}%`;

                if (remainingSeconds <= 0) {
                    setExpiryVisualExpired(true);
                    expiryLabel.textContent = 'Deep link expirado';
                    expiryProgress.style.width = '0%';
                    setStatus('Este deep link expirou. Solicite um novo link.', 'error');
                    qrWrap.innerHTML = '<p class="text-sm text-red-200">Este deep link expirou.</p>';
                    stopAllTimers();
                    return false;
                }

                setExpiryVisualExpired(false);
                expiryLabel.textContent = `Expira em ${formatRemaining(remainingSeconds)}`;
                return true;
            }

            function setStatus(text, type) {
                statusText.textContent = text;
                statusText.className = 'status mt-3 text-center text-xs';
                if (type === 'ok') {
                    statusText.classList.add('text-emerald-300');
                } else if (type === 'warn') {
                    statusText.classList.add('text-amber-300');
                } else if (type === 'error') {
                    statusText.classList.add('text-red-300');
                } else {
                    statusText.classList.add('text-slate-400');
                }
            }

            function formatElapsedSinceLastQr() {
                if (!qrLastUpdatedAt) {
                    return 'Ultima atualizacao: aguardando primeiro QR...';
                }

                const elapsedSeconds = Math.max(0, Math.floor((Date.now() - qrLastUpdatedAt) / 1000));

                if (elapsedSeconds < 5) {
                    return 'Ultima atualizacao: agora mesmo';
                }

                if (elapsedSeconds < 60) {
                    return `Ultima atualizacao: ha ${elapsedSeconds}s`;
                }

                const minutes = Math.floor(elapsedSeconds / 60);
                const seconds = elapsedSeconds % 60;
                return `Ultima atualizacao: ha ${minutes}m ${String(seconds).padStart(2, '0')}s`;
            }

            function updateQrUpdatedAtLabel() {
                qrUpdatedAt.classList.remove('text-slate-400', 'text-amber-300', 'text-red-300');
                qrUpdatedAt.textContent = formatElapsedSinceLastQr();

                if (!qrLastUpdatedAt) {
                    qrUpdatedAt.classList.add('text-slate-400');
                    return;
                }

                const elapsedMs = Date.now() - qrLastUpdatedAt;
                if (elapsedMs >= qrStaleAfterMs) {
                    qrUpdatedAt.classList.add('text-red-300');
                    return;
                }

                if (elapsedMs >= qrStaleAfterMs * 0.66) {
                    qrUpdatedAt.classList.add('text-amber-300');
                    return;
                }

                qrUpdatedAt.classList.add('text-slate-400');
            }

            function setQrState(state, options = {}) {
                qrState = state;

                qrWrap.classList.remove('border-dashed', 'border-slate-600', 'border-emerald-400/50', 'border-amber-400/50', 'border-cyan-400/50', 'border-red-400/45');
                qrWrap.classList.remove('bg-slate-950/40', 'bg-emerald-500/10', 'bg-amber-500/10', 'bg-cyan-500/10', 'bg-red-500/10');
                refreshQrButton.classList.remove('qr-refresh-pulse');
                qrFreshnessBadge.className = 'inline-flex rounded-full border px-2.5 py-1 text-[11px] font-medium';

                if (state === 'connected') {
                    qrWrap.classList.add('border-emerald-400/50', 'bg-emerald-500/10');
                    qrStateLabel.textContent = 'WhatsApp conectado';
                    updateQrUpdatedAtLabel();
                    qrFreshnessBadge.classList.add('border-emerald-400/40', 'bg-emerald-500/15', 'text-emerald-200');
                    qrFreshnessBadge.textContent = 'Conectado';
                    refreshQrButton.disabled = true;
                    return;
                }

                if (state === 'refreshing') {
                    qrWrap.classList.add('border-cyan-400/50', 'bg-cyan-500/10');
                    qrStateLabel.textContent = 'Renovando QR code';
                    updateQrUpdatedAtLabel();
                    qrFreshnessBadge.classList.add('border-cyan-400/40', 'bg-cyan-500/15', 'text-cyan-200');
                    qrFreshnessBadge.textContent = 'Atualizando';
                    refreshQrButton.disabled = true;
                    return;
                }

                if (state === 'stale') {
                    qrWrap.classList.add('border-amber-400/50', 'bg-amber-500/10');
                    qrStateLabel.textContent = 'QR provavelmente expirado';
                    updateQrUpdatedAtLabel();
                    refreshQrButton.classList.add('qr-refresh-pulse');
                    qrFreshnessBadge.classList.add('border-amber-400/40', 'bg-amber-500/15', 'text-amber-200');
                    qrFreshnessBadge.textContent = 'Expirado';
                    refreshQrButton.disabled = false;
                    return;
                }

                if (state === 'error') {
                    qrWrap.classList.add('border-red-400/45', 'bg-red-500/10');
                    qrStateLabel.textContent = options.label || 'Falha ao carregar QR';
                    updateQrUpdatedAtLabel();
                    qrFreshnessBadge.classList.add('border-red-400/40', 'bg-red-500/15', 'text-red-200');
                    qrFreshnessBadge.textContent = 'Erro';
                    refreshQrButton.disabled = false;
                    return;
                }

                if (state === 'active') {
                    qrWrap.classList.add('border-slate-600', 'bg-slate-950/40');
                    qrStateLabel.textContent = 'QR pronto para escanear';
                    updateQrUpdatedAtLabel();
                    qrFreshnessBadge.classList.add('border-emerald-400/40', 'bg-emerald-500/15', 'text-emerald-200');
                    qrFreshnessBadge.textContent = 'Recente';
                    refreshQrButton.disabled = false;
                    return;
                }

                qrWrap.classList.add('border-dashed', 'border-slate-600', 'bg-slate-950/40');
                    qrStateLabel.textContent = options.label || 'Preparando conexao segura...';
                updateQrUpdatedAtLabel();
                qrFreshnessBadge.classList.add('border-slate-700', 'bg-slate-900', 'text-slate-300');
                qrFreshnessBadge.textContent = 'Aguardando';
                refreshQrButton.disabled = true;
            }

            function updateQrFreshness() {
                if (!qrLastUpdatedAt || qrState === 'connected' || qrState === 'refreshing' || qrState === 'error') {
                    return;
                }

                if (Date.now() - qrLastUpdatedAt >= qrStaleAfterMs) {
                    setQrState('stale');
                    setStatus('Este QR provavelmente expirou. Toque em "Renovar QR" para gerar um novo agora.', 'warn');
                }
            }

            async function renderQr(dataUrl) {
                const hash = await sha256(dataUrl);
                if (hash === lastQrHash) {
                    updateQrFreshness();
                    return;
                }

                lastQrHash = hash;
                qrLastUpdatedAt = Date.now();
                qrWrap.innerHTML = '';

                const image = document.createElement('img');
                image.alt = 'QR code para conectar WhatsApp';
                image.src = dataUrl;
                image.className = 'h-auto w-full max-w-[220px] rounded-lg bg-white p-2';
                qrWrap.appendChild(image);
                setQrState('active');
            }

            async function syncConnection(action = 'syncQrDeepLink') {
                if (!updateExpiryState()) {
                    return;
                }

                if (inFlight) {
                    return;
                }

                inFlight = true;
                try {
                    if (action === 'refreshQrDeepLink') {
                        setQrState('refreshing');
                        setStatus('Solicitando um novo QR code para voce...', 'warn');
                    }

                    const body = new URLSearchParams({
                        action,
                        instance: deepLinkPayload.instance,
                        exp: String(deepLinkPayload.exp),
                        sig: deepLinkPayload.sig
                    });

                    const response = await fetch('deep-link-actions.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                        body: body.toString()
                    });

                    const result = await response.json();
                    if (!result.success) {
                        if (result.errorCode === 'LINK_INVALID') {
                            setStatus(result.message || 'Este link nao e mais valido.', 'error');
                            setQrState('error', { label: 'Link invalido' });
                            stopAllTimers();
                            return;
                        }

                        if (result.errorCode === 'CONNECTED') {
                            qrWrap.innerHTML = '<p class="text-sm text-emerald-300">Conexao concluida com sucesso.</p>';
                            setStatus('WhatsApp conectado com sucesso.', 'ok');
                            setQrState('connected');
                            stopAllTimers();
                            return;
                        }

                        if (result.errorCode === 'QR_REFRESH_FAILED') {
                            setQrState('error', { label: 'Falha ao renovar QR' });
                        } else if (lastQrHash) {
                            updateQrFreshness();
                        } else {
                            setQrState('loading', { label: 'Aguardando QR code...' });
                        }

                        setStatus(result.message || 'Aguardando QR code...', 'warn');
                        return;
                    }

                    if (result.data && result.data.qrCode) {
                        await renderQr(result.data.qrCode);
                        const statusMessage = result.data.refreshed
                            ? 'Novo QR code gerado. Escaneie antes que ele expire.'
                            : (result.data.created
                            ? 'Instancia preparada automaticamente. Escaneie o QR code para conectar.'
                            : 'QR code pronto. Escaneie para conectar seu WhatsApp.');
                        setStatus(statusMessage, 'ok');
                    } else {
                        setQrState('loading', { label: 'Aguardando QR code...' });
                        setStatus('Aguardando QR code...', 'warn');
                    }
                } catch (_error) {
                    if (!lastQrHash) {
                        setQrState('error', { label: 'Falha ao atualizar QR' });
                    }
                    setStatus('Falha temporaria ao atualizar o QR code. Tentando novamente...', 'warn');
                } finally {
                    inFlight = false;
                }
            }

            refreshQrButton.addEventListener('click', () => {
                syncConnection('refreshQrDeepLink');
            });

            setQrState('loading', { label: 'Preparando conexao segura...' });
            updateQrUpdatedAtLabel();
            updateExpiryState();
            expiryTimer = setInterval(() => {
                updateExpiryState();
                updateQrUpdatedAtLabel();
                updateQrFreshness();
            }, 1000);
            syncConnection();
            pollTimer = setInterval(syncConnection, 5000);
        </script>
    <?php endif; ?>
</body>
</html>
