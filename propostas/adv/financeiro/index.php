<?php
declare(strict_types=1);

$modules = [
    'hero.php',
    'breakdown.php',
    'costs.php',
    'highlight.php',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light" />
  <title>Sistema Jurídico Próprio - Breakdown Financeiro</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#fbecec',
              100: '#f5dada',
              200: '#ebb5b5',
              300: '#e19090',
              400: '#d76b6b',
              500: '#cd4646',
              600: '#8c2424',
              700: '#6b1919',
              800: '#4a0e0e',
              900: '#2d0808',
            },
            accent: {
              300: '#e2c98a',
              400: '#d4b87a',
              500: '#c6a76a',
            },
            surface: {
              pale: '#f9f7f2',
              alt: '#f2eee7',
              warm: '#ece4d6',
            },
            neutral: {
              100: '#f6f3ee',
              200: '#ddd3ca',
              500: '#7f6f72',
              700: '#4f4346',
              900: '#1a1718',
            },
          },
          fontFamily: {
            sans: ['Geist', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
          },
          boxShadow: {
            panel: '0 24px 80px rgba(45, 8, 8, 0.12)',
            float: '0 28px 90px rgba(45, 8, 8, 0.16)',
          },
        },
      },
    };
  </script>
  <link rel="stylesheet" href="../styles/redesign.css" />
</head>
<body class="min-h-screen bg-surface-pale text-neutral-900 antialiased">
  <main class="relative overflow-hidden pb-16 lg:pb-24">
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[34rem] bg-[radial-gradient(circle_at_top,rgba(198,167,106,0.18),transparent_56%)]"></div>
    <?php foreach ($modules as $module) : ?>
      <?php include __DIR__ . '/modules/' . $module; ?>
    <?php endforeach; ?>
    <footer class="max-w-6xl mx-auto px-6 text-xs font-semibold uppercase tracking-[0.22em] text-primary-900/45">
      Breakdown financeiro
    </footer>
  </main>
  <script src="../assets/js/redesign.js" defer></script>
</body>
</html>
