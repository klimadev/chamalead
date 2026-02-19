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
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

$error = '';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Check if session timed out
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $error = "Sessão expirada por inatividade. Faça login novamente.";
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
            $error = "Sessão inválida. Por favor, recarregue a página.";
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
                } else {
                    record_login_attempt($ip_address, $username, false);
                    $error = "Usuário ou senha inválidos.";
                }
            } else {
                $error = "Ação inválida.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Painel de controle premium - Login">
    <title>Painel Premium - Login</title>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Local Styles -->
    <link rel="stylesheet" href="styles.css">
    
    <style>
        /* Login-specific styles */
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-md);
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: linear-gradient(145deg, rgba(19, 19, 31, 0.98) 0%, rgba(10, 10, 18, 0.98) 100%);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-2xl);
            padding: var(--space-2xl);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 60px -20px rgba(124, 58, 237, 0.2);
            animation: scaleIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: var(--space-xl);
        }
        
        .login-logo {
            width: 64px;
            height: 64px;
            background: var(--gradient-primary);
            border-radius: var(--radius-xl);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-lg);
            box-shadow: 0 10px 30px -10px rgba(124, 58, 237, 0.5);
            animation: glow 2s ease-in-out infinite;
        }
        
        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: var(--space-xs);
        }
        
        .login-subtitle {
            color: var(--color-text-tertiary);
            font-size: 0.875rem;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: var(--space-lg);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: var(--space-sm);
        }
        
        .form-label {
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-tertiary);
        }
        
        .form-input {
            width: 100%;
            padding: var(--space-md) var(--space-lg);
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            color: var(--color-text-primary);
            font-size: 0.875rem;
            transition: all var(--transition-base);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--color-border-focus);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        
        .form-input::placeholder {
            color: var(--color-text-muted);
        }
        
        .login-actions {
            display: flex;
            flex-direction: column;
            gap: var(--space-md);
            margin-top: var(--space-md);
        }
        
        .btn-login {
            width: 100%;
            padding: var(--space-md) var(--space-xl);
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-base);
            box-shadow: 0 4px 15px -2px rgba(124, 58, 237, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -4px rgba(124, 58, 237, 0.5);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-register {
            width: 100%;
            padding: var(--space-md) var(--space-xl);
            background: transparent;
            color: var(--color-text-secondary);
            font-weight: 500;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-base);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-sm);
        }
        
        .btn-register:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--color-border-hover);
            color: var(--color-text-primary);
        }
        
        .alert {
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-lg);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            animation: slideInRight 0.3s ease-out;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--color-error);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--color-success);
        }
        
        .login-footer {
            text-align: center;
            margin-top: var(--space-xl);
            padding-top: var(--space-xl);
            border-top: 1px solid var(--color-border);
        }
        
        .login-footer-text {
            font-size: 0.75rem;
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-xs);
        }
        
        .security-icon {
            width: 14px;
            height: 14px;
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="grid-overlay" aria-hidden="true"></div>

    <!-- Login Page -->
    <div class="login-page">
        <div class="login-container">
            <div class="login-card">
                <!-- Header -->
                <div class="login-header">
                    <div class="login-logo">
                        <i data-lucide="zap" style="width: 32px; height: 32px; color: white;"></i>
                    </div>
                    <h1 class="login-title">
                        <span class="text-gradient">Chamalead</span>
                    </h1>
                    <p class="login-subtitle">Gerencie suas instâncias com elegância</p>
                </div>

                <!-- Alerts -->
                <?php if ($error): ?>
                    <div class="alert alert-error" role="alert">
                        <i data-lucide="alert-circle" style="width: 18px; height: 18px; flex-shrink: 0;"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" class="login-form" aria-label="Formulário de login">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Usuário</label>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            required 
                            minlength="3"
                            maxlength="50"
                            autocomplete="username"
                            class="form-input"
                            placeholder="Digite seu usuário"
                            aria-required="true"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            required 
                            minlength="6"
                            autocomplete="current-password"
                            class="form-input"
                            placeholder="Digite sua senha"
                            aria-required="true"
                        >
                    </div>

                    <div class="login-actions">
                        <button type="submit" name="action" value="login" class="btn-login">
                            <i data-lucide="log-in" style="width: 18px; height: 18px;"></i>
                            Entrar
                        </button>
                    </div>
                </form>
                
                <!-- Footer -->
                <div class="login-footer">
                    <p class="login-footer-text">
                        <i data-lucide="shield-check" class="security-icon"></i>
                        Protegido por criptografia e limitação de tentativas
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>
</html>
