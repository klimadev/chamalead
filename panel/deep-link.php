<?php

require_once 'Config.php';
require_once 'DeepLinkService.php';

Config::load();

$instanceName = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['instance'] ?? '');
$expiresAt = (int)($_GET['exp'] ?? 0);
$signature = (string)($_GET['sig'] ?? '');

$isValid = DeepLinkService::validate($instanceName, $expiresAt, $signature);

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");

if (!$isValid) {
    http_response_code(403);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectar WhatsApp</title>
    <style>
        :root {
            color-scheme: light;
            --bg-1: #0b1220;
            --bg-2: #0f172a;
            --card: rgba(15, 23, 42, 0.86);
            --text: #e5e7eb;
            --muted: #94a3b8;
            --ok: #22c55e;
            --warn: #f59e0b;
            --error: #ef4444;
            --border: rgba(148, 163, 184, 0.2);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(circle at top, #1e293b 0%, #020617 60%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: min(100%, 540px);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 32px rgba(2, 6, 23, 0.5);
        }

        .title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .subtitle {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .instance {
            margin-top: 16px;
            display: inline-flex;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: #cbd5e1;
            background: rgba(15, 23, 42, 0.65);
            font-size: 0.85rem;
        }

        .qr-wrap {
            margin-top: 24px;
            min-height: 300px;
            border-radius: 14px;
            border: 1px dashed var(--border);
            background: rgba(2, 6, 23, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
            text-align: center;
            gap: 12px;
        }

        .qr-wrap img {
            width: min(100%, 290px);
            height: auto;
            border-radius: 10px;
            background: #fff;
            padding: 10px;
        }

        .status {
            margin-top: 16px;
            color: var(--muted);
            font-size: 0.9rem;
            text-align: center;
        }

        .status.ok { color: var(--ok); }
        .status.warn { color: var(--warn); }
        .status.error { color: var(--error); }

        .steps {
            margin: 16px 0 0;
            color: #cbd5e1;
            font-size: 0.9rem;
            line-height: 1.5;
            padding-left: 18px;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1 class="title">Conectar WhatsApp</h1>
        <p class="subtitle">Escaneie o QR code no app do WhatsApp. O codigo atualiza automaticamente.</p>
        <?php if ($isValid): ?>
            <p class="instance">Instancia: <?= htmlspecialchars($instanceName, ENT_QUOTES, 'UTF-8') ?></p>
            <section class="qr-wrap" id="qrWrap">
                <p id="qrPlaceholder">Preparando conexao...</p>
            </section>
            <p class="status" id="statusText">Aguardando QR code...</p>
            <ol class="steps">
                <li>Abra o WhatsApp no celular.</li>
                <li>Toque em Dispositivos conectados.</li>
                <li>Escaneie o QR code exibido nesta tela.</li>
            </ol>
        <?php else: ?>
            <section class="qr-wrap">
                <p>Este link expirou ou e invalido.</p>
            </section>
            <p class="status error">Solicite um novo link ao administrador.</p>
        <?php endif; ?>
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

            const qrWrap = document.getElementById('qrWrap');
            const statusText = document.getElementById('statusText');

            function setStatus(text, type) {
                statusText.textContent = text;
                statusText.className = 'status';
                if (type) {
                    statusText.classList.add(type);
                }
            }

            function renderQr(dataUrl) {
                const hash = dataUrl.slice(0, 80);
                if (hash === lastQrHash) {
                    return;
                }

                lastQrHash = hash;
                qrWrap.innerHTML = '';

                const image = document.createElement('img');
                image.alt = 'QR code para conectar WhatsApp';
                image.src = dataUrl;
                qrWrap.appendChild(image);
            }

            async function syncConnection() {
                if (inFlight) {
                    return;
                }

                inFlight = true;
                try {
                    const body = new URLSearchParams({
                        action: 'syncQrDeepLink',
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
                            setStatus(result.message || 'Link invalido.', 'error');
                            clearInterval(pollTimer);
                            return;
                        }

                        if (result.errorCode === 'CONNECTED') {
                            qrWrap.innerHTML = '<p>Conexao concluida com sucesso.</p>';
                            setStatus('WhatsApp conectado.', 'ok');
                            clearInterval(pollTimer);
                            return;
                        }

                        setStatus(result.message || 'Aguardando QR code...', 'warn');
                        return;
                    }

                    if (result.data && result.data.qrCode) {
                        renderQr(result.data.qrCode);
                        const statusMessage = result.data.created
                            ? 'Instancia criada automaticamente. Escaneie o QR code.'
                            : 'QR code atualizado. Escaneie para conectar.';
                        setStatus(statusMessage, 'ok');
                    } else {
                        setStatus('Aguardando QR code...', 'warn');
                    }
                } catch (error) {
                    setStatus('Falha temporaria ao atualizar QR code. Tentando novamente...', 'warn');
                } finally {
                    inFlight = false;
                }
            }

            syncConnection();
            pollTimer = setInterval(syncConnection, 5000);
        </script>
    <?php endif; ?>
</body>
</html>
