<?php
/**
 * Premium Login Page
 *
 * Modern authentication interface with security features.
 *
 * @package Panel
 * @author Chamalead
 * @version 3.0.0
 */

require_once 'auth.php';

// Security Headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

$error = '';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Check if session timed out
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $error = "Sessao expirada por inatividade. Faca login novamente.";
}

// Check rate limit immediately
$can_attempt = check_rate_limit($ip_address);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check rate limiting first
    if (!$can_attempt) {
        $error = "Muitas tentativas de login. Aguarde 15 minutos.";
        record_login_attempt($ip_address, $_POST['username'] ?? '', false);
    } else {
        // Validate CSRF token
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf_token($csrf_token)) {
            $error = "Sessao invalida. Por favor, recarregue a pagina.";
            record_login_attempt($ip_address, $_POST['username'] ?? '', false);
        } else {
            $action = sanitize_string($_POST['action'] ?? '');
            $username = sanitize_alphanumeric($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($action === 'login') {
                if (login($username, $password)) {
                    record_login_attempt($ip_address, $username, true);
                    header("Location: index.php");
                    exit;
                }

                record_login_attempt($ip_address, $username, false);
                $error = "Usuario ou senha invalidos.";
            } else {
                $error = "Acao invalida.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Painel de controle premium - Login">
    <title>Painel Premium - Login</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    boxShadow: {
                        softfire: '0 24px 48px -20px rgba(249, 115, 22, 0.42)'
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full overflow-hidden bg-slate-950 text-slate-100 antialiased">
    <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_12%_16%,rgba(249,115,22,0.22),transparent_42%),radial-gradient(circle_at_82%_2%,rgba(239,68,68,0.16),transparent_34%),radial-gradient(circle_at_50%_120%,rgba(14,116,144,0.26),transparent_50%)]"></div>
    <div class="fixed inset-0 -z-10 bg-[linear-gradient(rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.08)_1px,transparent_1px)] bg-[size:36px_36px] opacity-[0.08]"></div>

    <main class="mx-auto flex h-full w-full max-w-md items-center justify-center px-5">
        <section class="w-full rounded-2xl border border-slate-800/90 bg-slate-900/80 p-6 shadow-softfire backdrop-blur-xl sm:p-8" aria-label="Area de login">
            <header class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-700/40">
                    <i data-lucide="zap" class="h-8 w-8 text-white"></i>
                </div>
                <h1 class="bg-gradient-to-r from-orange-400 to-red-500 bg-clip-text text-2xl font-bold text-transparent">Chamalead</h1>
                <p class="mt-1 text-xs text-slate-400">Gerencie suas instancias com elegancia</p>
            </header>

            <?php if ($error): ?>
                <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-red-500/35 bg-red-500/10 px-3 py-2.5 text-sm text-red-300" role="alert">
                    <i data-lucide="alert-circle" class="mt-0.5 h-4 w-4 shrink-0"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4" aria-label="Formulario de login">
                <?= csrf_field() ?>

                <div>
                    <label for="username" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-400">Usuario</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        minlength="3"
                        maxlength="50"
                        autocomplete="username"
                        class="block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30"
                        placeholder="Digite seu usuario"
                        aria-required="true"
                    >
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.08em] text-slate-400">Senha</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        minlength="6"
                        autocomplete="current-password"
                        class="block w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2.5 text-sm text-slate-100 placeholder:text-slate-500 focus:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500/30"
                        placeholder="Digite sua senha"
                        aria-required="true"
                    >
                </div>

                <button type="submit" name="action" value="login" class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 px-3 py-2.5 text-sm font-semibold text-white shadow-lg shadow-orange-700/40 transition hover:-translate-y-0.5 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-orange-500/40">
                    <i data-lucide="log-in" class="h-4 w-4"></i>
                    Entrar
                </button>
            </form>

            <footer class="mt-6 border-t border-slate-800 pt-4">
                <p class="flex items-center justify-center gap-1.5 text-[11px] text-slate-500">
                    <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                    Protegido por criptografia e limitacao de tentativas
                </p>
            </footer>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
