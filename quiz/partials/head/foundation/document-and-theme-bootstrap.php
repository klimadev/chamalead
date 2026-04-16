<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChamaLead | Diagnóstico Comercial</title>
    <meta name="description" content="Descubra em 2 minutos se sua operação comercial está perdendo leads todos os dias.">

    <meta property="og:title" content="ChamaLead | Diagnóstico Comercial">
    <meta property="og:description" content="Responda algumas perguntas e veja se já faz sentido automatizar sua operação.">
    <meta property="og:type" content="website">

    <script>
        window.addEventListener('load', function () {
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '1485156178775034');
            fbq('track', 'PageView');
            fbq('track', 'ViewContent', {content_name: 'Quiz Comercial'});
        }, { once: true });
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1485156178775034&ev=PageView&noscript=1" />
    </noscript>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    </noscript>

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
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --bg-main: #030303;
            --flame-500: #f97316;
            --flame-600: #ea580c;
            --ember-600: #dc2626;
            --ember-500: #ef4444;
            --bg-energy: 0.58;
            --bg-speed: 18s;
            --bg-angle: 132deg;
            --bg-warm-alpha: 0.18;
            --bg-alert-alpha: 0.11;
            --bg-focus-x: 50%;
            --bg-focus-y: 30%;
            --bg-grid-opacity: 0.18;
            --ff-body: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --ff-display: 'Space Grotesk', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --type-meta-size: 11px;
            --type-meta-lh: 1.25;
            --type-meta-track: 0.1em;
            --type-progress-size: 12px;
            --type-progress-stage-size: 11px;
            --type-headline-size: clamp(24px, 5vw, 36px);
            --type-headline-lh: 1.28;
            --type-headline-track: -0.02em;
            --type-body-md-size: 15px;
            --type-body-md-lh: 1.6;
            --type-body-sm-size: 13px;
            --type-body-sm-lh: 1.5;
            --type-ui-size: 15px;
            --type-ui-lh: 1.45;
            --type-input-size: 16px;
            --type-cta-size: 16px;
            --type-score-size: 28px;
            --type-metric-size: 14px;
            --spring-soft: cubic-bezier(0.22, 1, 0.36, 1);
            --spring-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
            --motion-flame-enter: 0.46s var(--spring-soft);
            --motion-flame-pop: 0.3s var(--spring-bounce);
            --motion-flame-settle: 0.26s var(--spring-soft);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: var(--ff-body);
            background: var(--bg-main);
            color: #ffffff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .quiz-container {
            height: 100dvh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .quiz-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            background: var(--bg-main);
            transition: filter 0.7s ease, transform 0.7s var(--spring-soft);
        }

        .quiz-bg-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            transition: opacity 0.65s var(--spring-soft), transform 0.8s var(--spring-soft), filter 0.65s ease;
        }

        .quiz-bg-layer--mesh {
            background:
                radial-gradient(circle at 12% 88%, rgba(249, 115, 22, calc(0.08 + var(--bg-warm-alpha))) 0%, transparent 42%),
                radial-gradient(circle at 88% 12%, rgba(220, 38, 38, calc(0.06 + var(--bg-alert-alpha))) 0%, transparent 45%),
                radial-gradient(circle at var(--bg-focus-x) var(--bg-focus-y), rgba(249, 115, 22, 0.1) 0%, transparent 44%),
                linear-gradient(var(--bg-angle), rgba(249, 115, 22, 0.06) 0%, rgba(220, 38, 38, 0.03) 42%, transparent 75%);
            transform-origin: 50% 50%;
        }

        .quiz-bg-layer--rings {
            inset: -20%;
            background:
                radial-gradient(circle at 82% 16%, transparent 0 18%, rgba(249, 115, 22, 0.16) 19% 19.6%, transparent 20%),
                radial-gradient(circle at 76% 22%, transparent 0 24%, rgba(220, 38, 38, 0.12) 24.8% 25.3%, transparent 26%);
            opacity: 0.62;
        }

        .quiz-bg-layer--grid {
            background-image: radial-gradient(circle, rgba(249, 115, 22, 0.18) 1px, transparent 1px);
            background-size: 38px 38px;
            opacity: var(--bg-grid-opacity);
            mask-image: radial-gradient(circle at 50% 30%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.35) 56%, transparent 100%);
            -webkit-mask-image: radial-gradient(circle at 50% 30%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.35) 56%, transparent 100%);
        }

        .quiz-container[data-animated='1'] .quiz-bg-layer--mesh {
            animation: meshPulse var(--bg-speed) ease-in-out infinite;
        }

        .quiz-container[data-animated='1'] .quiz-bg-layer--rings {
            animation: orbitalBreath 11s ease-in-out infinite;
        }

        .quiz-container[data-animated='1'] .quiz-bg-layer--grid {
            animation: gridDrift 24s linear infinite;
        }

        .quiz-bg-layer--noise {
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.03;
            mix-blend-mode: soft-light;
        }

        .quiz-bg-spotlight {
            position: absolute;
            width: 520px;
            height: 520px;
            left: var(--bg-focus-x);
            top: var(--bg-focus-y);
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.16) 0%, rgba(220, 38, 38, 0.07) 34%, transparent 72%);
            opacity: calc(0.28 + (var(--bg-energy) * 0.3));
            filter: blur(6px);
            pointer-events: none;
            transition: left 0.75s var(--spring-soft), top 0.75s var(--spring-soft), opacity 0.55s ease;
        }

        .quiz-bg[data-mood='welcome'] .quiz-bg-layer--mesh {
            filter: saturate(1.06) brightness(1.02);
        }

        .quiz-bg[data-mood='contact'] .quiz-bg-layer--grid {
            opacity: 0.14;
        }

        .quiz-bg[data-mood='diagnosis'] .quiz-bg-layer--mesh {
            filter: saturate(1.1);
        }

        .quiz-bg[data-mood='pain'] {
            filter: contrast(1.05);
        }

        .quiz-bg[data-mood='pain'] .quiz-bg-layer--rings {
            opacity: 0.76;
            transform: scale(1.05);
        }

        .quiz-bg[data-mood='urgency'] .quiz-bg-layer--mesh {
            animation-duration: 12s;
        }

        .quiz-bg[data-mood='result'] .quiz-bg-layer--rings {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .quiz-bg[data-mood='result'] .quiz-bg-layer--grid {
            opacity: 0.22;
        }

        @keyframes orbitalBreath {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.28; }
            50% { transform: translate(-10px, 8px) scale(1.08); opacity: 0.5; }
        }

        @keyframes meshPulse {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(0, -2%, 0) scale(calc(1 + (var(--bg-energy) * 0.08))); }
        }

        @keyframes gridDrift {
            from { transform: translate3d(0, 0, 0); }
            to { transform: translate3d(38px, -24px, 0); }
        }

        .corner-line {
            position: absolute;
            z-index: 0;
        }

        .corner-line--tl {
            top: 60px;
            left: 0;
            width: 92px;
            height: 1px;
            background: linear-gradient(90deg, rgba(249, 115, 22, 0.16), transparent);
        }

        .corner-line--br {
            bottom: 80px;
            right: 0;
            width: 160px;
            height: 1px;
            background: linear-gradient(270deg, rgba(220, 38, 38, 0.18), transparent);
        }

        .corner-line--bl {
            bottom: 0;
            left: 40px;
            width: 1px;
            height: 80px;
            background: linear-gradient(0deg, rgba(249, 115, 22, 0.18), transparent);
        }

        .quiz-header {
            position: relative;
            z-index: 10;
            padding: 20px 24px 0;
            flex-shrink: 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .logo img {
            height: 24px;
            width: auto;
            opacity: 0.85;
        }

        .logo-text {
            font-family: var(--ff-display);
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.15em;
            color: rgba(255, 255, 255, 0.7);
        }

        .progress-track {
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 2px;
            overflow: hidden;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.28s ease, transform 0.28s var(--spring-soft);
        }

        .progress-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            gap: 10px;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.3s ease, transform 0.32s var(--spring-soft);
        }

        .progress-track.is-hidden,
        .progress-meta.is-hidden {
            opacity: 0;
            transform: translateY(-8px);
            pointer-events: none;
        }

        .progress-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .progress-step-label,
        .progress-percent-label {
            font-size: var(--type-progress-size);
            line-height: var(--type-meta-lh);
            letter-spacing: 0.07em;
