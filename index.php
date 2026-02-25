<?php
$modulesDir = __DIR__ . '/modules';
$modules = [];

if (is_dir($modulesDir)) {
    $files = scandir($modulesDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
            $modules[] = $file;
        }
    }
    sort($modules);
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ChamaLead - Automação Inteligente com IA para WhatsApp. Transforme seu atendimento em uma máquina de vendas 24/7. Entrega em 48h, sem taxas ocultas.">
    <title>ChamaLead | Sua Máquina de Vendas no WhatsApp</title>
    
    <!-- SEO & Social -->
    <meta property="og:title" content="ChamaLead | Transforme seu WhatsApp em Vendas">
    <meta property="og:description" content="IA humanizada que atende, qualifica e agenda 24/7. Sem custos por mensagem. Entrega em 48h.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://placehold.co/1200x630/ea580c/ffffff?text=ChamaLead">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'flame': {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            950: '#431407',
                        },
                        'ember': {
                            500: '#ef4444',
                            600: '#dc2626',
                        },
                        'dark': {
                            DEFAULT: '#0a0a0a',
                            900: '#111111',
                            800: '#1a1a1a',
                            700: '#262626',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'float-slow': 'float 8s ease-in-out infinite',
                        'float-reverse': 'floatReverse 7s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                        'shimmer': 'shimmer 2s linear infinite',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                        'scale-in': 'scaleIn 0.4s ease-out forwards',
                        'mesh-1': 'mesh1 20s ease-in-out infinite',
                        'mesh-2': 'mesh2 25s ease-in-out infinite',
                        'mesh-3': 'mesh3 22s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(-20px) rotate(2deg)' },
                        },
                        floatReverse: {
                            '0%, 100%': { transform: 'translateY(0) rotate(0deg)' },
                            '50%': { transform: 'translateY(-15px) rotate(-2deg)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(249, 115, 22, 0.3)' },
                            '100%': { boxShadow: '0 0 40px rgba(249, 115, 22, 0.6), 0 0 60px rgba(220, 38, 38, 0.3)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        mesh1: {
                            '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                            '33%': { transform: 'translate(30px, -30px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.95)' },
                        },
                        mesh2: {
                            '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                            '33%': { transform: 'translate(-40px, 20px) scale(1.15)' },
                            '66%': { transform: 'translate(30px, -10px) scale(0.9)' },
                        },
                        mesh3: {
                            '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                            '33%': { transform: 'translate(20px, 30px) scale(1.05)' },
                            '66%': { transform: 'translate(-30px, -20px) scale(1.1)' },
                        },
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="selection:bg-flame-500 selection:text-white antialiased">

<?php foreach ($modules as $module): ?>
<?php include $modulesDir . '/' . $module; ?>
<?php endforeach; ?>

<script src="assets/js/app.js"></script>

</body>
</html>
