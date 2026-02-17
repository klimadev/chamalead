<?php
date_default_timezone_set('America/Sao_Paulo');
require_once 'config.php';

startAdminSession();

if (!empty($_SESSION['admin_authenticated'])) {
    header('Location: admin.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');

    if (hash_equals(ADMIN_SECRET, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        ensureAdminCsrfToken();

        header('Location: admin.php');
        exit;
    }

    $error_message = 'Senha invalida.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | ChamaLead</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        flame: '#f97316',
                        ember: '#ea580c',
                    },
                },
            },
        };
    </script>
    <style>
        body {
            background:
                radial-gradient(circle at 15% 20%, rgba(249, 115, 22, 0.15), transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(234, 88, 12, 0.16), transparent 45%),
                #09090b;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 text-zinc-100">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="mx-auto mb-4 h-14 w-14 rounded-2xl bg-gradient-to-br from-flame to-ember flex items-center justify-center text-white text-2xl font-black">C</div>
            <h1 class="text-3xl font-extrabold tracking-tight">ChamaLead Admin</h1>
            <p class="text-zinc-400 mt-2">Acesse o painel com sua senha de administrador.</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label for="password" class="block text-sm text-zinc-300 font-medium mb-2">Senha</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-xl border border-white/15 bg-black/30 px-4 py-3 text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-flame/60"
                    placeholder="Digite sua senha"
                >
            </div>

            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-flame to-ember px-4 py-3 font-semibold text-white shadow-lg shadow-orange-900/30 transition hover:translate-y-[-1px]">
                Entrar no painel
            </button>
        </form>
    </div>
</body>
</html>
