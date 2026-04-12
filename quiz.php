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
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=1485156178775034&ev=PageView&noscript=1" />
    </noscript>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

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
            --ff-body: 'Inter', sans-serif;
            --ff-display: 'Space Grotesk', sans-serif;
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
            animation: meshPulse var(--bg-speed) ease-in-out infinite;
            transform-origin: 50% 50%;
        }

        .quiz-bg-layer--rings {
            inset: -20%;
            background:
                radial-gradient(circle at 82% 16%, transparent 0 18%, rgba(249, 115, 22, 0.16) 19% 19.6%, transparent 20%),
                radial-gradient(circle at 76% 22%, transparent 0 24%, rgba(220, 38, 38, 0.12) 24.8% 25.3%, transparent 26%);
            opacity: 0.62;
            animation: orbitalBreath 11s ease-in-out infinite;
        }

        .quiz-bg-layer--grid {
            background-image: radial-gradient(circle, rgba(249, 115, 22, 0.18) 1px, transparent 1px);
            background-size: 38px 38px;
            opacity: var(--bg-grid-opacity);
            mask-image: radial-gradient(circle at 50% 30%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.35) 56%, transparent 100%);
            -webkit-mask-image: radial-gradient(circle at 50% 30%, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.35) 56%, transparent 100%);
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
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
            font-family: var(--ff-display);
            font-weight: 600;
        }

        .progress-percent-label {
            color: rgba(249, 115, 22, 0.85);
        }

        .progress-stage-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 138px;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(249, 115, 22, 0.24);
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.14), rgba(220, 38, 38, 0.08));
            color: rgba(255, 255, 255, 0.82);
            font-size: var(--type-progress-stage-size);
            line-height: 1.2;
            letter-spacing: var(--type-meta-track);
            text-transform: uppercase;
            font-family: var(--ff-display);
            font-weight: 700;
            transition: all 0.28s var(--spring-soft);
        }

        .progress-stage-icon {
            width: 12px;
            height: 12px;
            margin-right: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: rgba(249, 115, 22, 0.9);
            opacity: 0.88;
            transform: scale(0.92);
            transition: color 0.24s ease, opacity 0.24s ease, transform 0.3s var(--spring-soft);
        }

        .progress-stage-label.stage-enter .progress-stage-icon {
            opacity: 1;
            transform: scale(1);
        }

        .progress-stage-label.stage-discovery .progress-stage-icon {
            color: rgba(251, 191, 36, 0.9);
        }

        .progress-stage-label.stage-diagnosis .progress-stage-icon {
            color: rgba(249, 115, 22, 0.92);
        }

        .progress-stage-label.stage-result .progress-stage-icon {
            color: rgba(220, 38, 38, 0.92);
        }

        .progress-stage-icon svg {
            width: 12px;
            height: 12px;
            stroke: currentColor;
        }

        .progress-stage-text {
            line-height: 1;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--flame-600), var(--ember-600));
            border-radius: 2px;
            transition: width 0.5s var(--spring-soft);
            width: 0%;
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.45);
        }

        .progress-fill.surge {
            animation: progressSurge 0.5s var(--spring-soft);
        }

        @keyframes progressSurge {
            0% { filter: brightness(1); }
            45% { filter: brightness(1.3); }
            100% { filter: brightness(1); }
        }

        .quiz-content {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 24px;
            overflow: hidden;
            min-height: 0;
        }

        .step {
            display: none;
            animation: stepIn var(--motion-flame-enter) forwards;
        }

        .step.active {
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-height: 0;
            height: 100%;
        }

        .step.active > div {
            flex-shrink: 0;
        }

        .step.exiting {
            animation: stepOut 0.24s ease-in forwards;
        }

        @keyframes stepIn {
            0% { opacity: 0; transform: translateY(22px) scale(0.98); }
            70% { opacity: 1; transform: translateY(-2px) scale(1.005); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes stepOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-12px); }
        }

        .step-number {
            font-family: var(--ff-display);
            font-size: var(--type-meta-size);
            font-weight: 500;
            letter-spacing: 0.16em;
            line-height: var(--type-meta-lh);
            text-transform: uppercase;
            color: rgba(249, 115, 22, 0.75);
            margin-bottom: 14px;
        }

        .step-headline {
            font-family: var(--ff-display);
            font-size: var(--type-headline-size);
            font-weight: 700;
            line-height: var(--type-headline-lh);
            letter-spacing: var(--type-headline-track);
            color: #ffffff;
            text-wrap: balance;
        }

        .step-headline .accent {
            position: relative;
            background: linear-gradient(135deg, var(--flame-500), var(--ember-600));
            background-size: 180% 180%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: flameGradientFlow 5.2s ease-in-out infinite, flameGlowPulse 2.8s ease-in-out infinite;
        }

        @keyframes flameGradientFlow {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes flameGlowPulse {
            0%, 100% { filter: drop-shadow(0 0 0 rgba(249, 115, 22, 0)); }
            50% { filter: drop-shadow(0 0 12px rgba(249, 115, 22, 0.45)); }
        }

        .step[data-step="welcome"] .step-headline .accent {
            animation-duration: 4.2s, 2.3s;
        }

        .step[data-step="welcome"] .step-headline .accent.flame-live {
            display: inline-block;
            isolation: isolate;
            text-shadow: 0 0 0 rgba(249, 115, 22, 0);
            animation: flameGradientFlow 4.2s ease-in-out infinite, flameGlowPulse 2.3s ease-in-out infinite, flameFlicker 1.7s ease-in-out infinite;
        }

        .step[data-step="welcome"] .step-headline .accent.flame-live::after {
            content: attr(data-text);
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(251, 146, 60, 0.22), rgba(239, 68, 68, 0.1) 45%, rgba(251, 146, 60, 0.2));
            background-size: 100% 190%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: blur(0.5px);
            opacity: 0.55;
            transform-origin: 50% 100%;
            pointer-events: none;
            mix-blend-mode: screen;
            animation: flameHeat 2.1s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes flameFlicker {
            0%, 100% { text-shadow: 0 0 0 rgba(249, 115, 22, 0); }
            25% { text-shadow: 0 0 12px rgba(249, 115, 22, 0.24); }
            55% { text-shadow: 0 0 16px rgba(239, 68, 68, 0.26); }
            78% { text-shadow: 0 0 8px rgba(249, 115, 22, 0.2); }
        }

        @keyframes flameHeat {
            0%, 100% {
                transform: translateY(0) skewX(0deg) scaleY(1);
                background-position: 50% 0%;
            }
            35% {
                transform: translateY(-0.5px) skewX(1.1deg) scaleY(1.02);
                background-position: 50% 40%;
            }
            65% {
                transform: translateY(0.4px) skewX(-0.9deg) scaleY(0.99);
                background-position: 50% 75%;
            }
        }

        .step-sub {
            font-size: var(--type-body-md-size);
            line-height: var(--type-body-md-lh);
            color: rgba(255, 255, 255, 0.62);
            max-width: 480px;
            margin-top: 22px;
            text-wrap: pretty;
        }

        .step-guidance {
            display: block;
            margin-top: 18px;
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid rgba(249, 115, 22, 0.38);
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.2), rgba(220, 38, 38, 0.1));
            color: rgba(255, 255, 255, 0.9);
            font-size: var(--type-body-sm-size);
            line-height: var(--type-ui-lh);
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.28s ease, transform 0.28s var(--spring-soft);
            visibility: hidden;
            pointer-events: none;
        }

        .step-guidance .guidance-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            margin-right: 8px;
            color: rgba(249, 115, 22, 0.95);
            vertical-align: text-top;
        }

        .step-guidance .guidance-text {
            display: inline;
        }

        .step-guidance.active {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
            pointer-events: auto;
        }

        .options-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .step.active .options-grid {
            min-height: 0;
        }

        .option-btn {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.85);
            font-size: var(--type-ui-size);
            font-weight: 400;
            line-height: var(--type-ui-lh);
            text-align: left;
            cursor: pointer;
            transition: transform 0.25s var(--spring-soft), border-color 0.22s ease, background-color 0.22s ease, box-shadow 0.28s var(--spring-soft);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .option-btn:focus-visible {
            outline: none;
            border-color: rgba(249, 115, 22, 0.56);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.24);
        }

        .option-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.14), rgba(220, 38, 38, 0.08));
            opacity: 0;
            transition: opacity 0.22s ease;
        }

        .option-btn::after {
            content: '';
            position: absolute;
            top: -70%;
            left: -30%;
            width: 44%;
            height: 240%;
            background: linear-gradient(110deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.16) 45%, rgba(255, 255, 255, 0) 100%);
            transform: translateX(-180%) rotate(10deg);
            transition: transform 0.58s var(--spring-soft);
            pointer-events: none;
        }

        .option-btn:hover {
            border-color: rgba(249, 115, 22, 0.58);
            background: rgba(249, 115, 22, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(249, 115, 22, 0.15), inset 0 0 0 1px rgba(249, 115, 22, 0.12);
        }

        .option-btn:hover::before {
            opacity: 1;
        }

        .option-btn:hover::after {
            transform: translateX(360%) rotate(10deg);
        }

        .option-btn:hover .option-icon {
            border-color: rgba(249, 115, 22, 0.5);
            transform: scale(1.06);
        }

        .option-btn:active {
            transform: scale(0.98);
        }

        .option-btn.selected {
            border-color: rgba(249, 115, 22, 0.55);
            background: rgba(249, 115, 22, 0.12);
            color: #ffffff;
            animation: optionSelectPop 0.35s var(--spring-bounce);
        }

        @keyframes optionSelectPop {
            0% { transform: scale(0.985); }
            60% { transform: scale(1.015); }
            100% { transform: scale(1); }
        }

        .option-btn.selected::before {
            opacity: 1;
            animation: optionSelectPulse var(--motion-flame-pop);
        }

        @keyframes optionSelectPulse {
            0% { transform: translate(-50%, -50%) scale(0.25); opacity: 0.4; }
            70% { transform: translate(-50%, -50%) scale(1.1); opacity: 0.2; }
            100% { transform: translate(-50%, -50%) scale(1.26); opacity: 0; }
        }

        .option-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s var(--spring-soft), border-color 0.2s ease, background-color 0.2s ease;
            position: relative;
            z-index: 1;
        }

        .option-btn.selected .option-icon {
            border-color: var(--flame-500);
            background: var(--flame-500);
        }

        .option-btn.selected .option-icon::after {
            content: '';
            width: 6px;
            height: 6px;
            background: #fff;
            border-radius: 50%;
        }

        .option-label {
            position: relative;
            z-index: 1;
            display: block;
            letter-spacing: 0.005em;
            text-wrap: pretty;
            overflow-wrap: anywhere;
        }

        .input-field {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            font-family: var(--ff-body);
            font-size: var(--type-input-size);
            line-height: 1.4;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-wrap {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-label {
            font-family: var(--ff-display);
            font-size: var(--type-meta-size);
            line-height: var(--type-meta-lh);
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.58);
            font-weight: 600;
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .input-field:focus {
            border-color: rgba(249, 115, 22, 0.5);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.14);
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 40px;
            background: linear-gradient(135deg, var(--flame-600), var(--ember-600));
            color: #ffffff;
            font-family: var(--ff-display);
            font-size: var(--type-cta-size);
            font-weight: 600;
            letter-spacing: 0.01em;
            line-height: 1.2;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            transition: transform 0.24s var(--spring-soft), box-shadow 0.28s var(--spring-soft), filter 0.24s ease;
            position: relative;
            overflow: hidden;
            min-width: 200px;
            isolation: isolate;
            box-shadow: 0 8px 22px rgba(249, 115, 22, 0.22);
            background-size: 160% 160%;
            animation: ctaFlameFlow 4.8s ease-in-out infinite;
        }

        @keyframes ctaFlameFlow {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .cta-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.24), 0 10px 34px rgba(249, 115, 22, 0.26);
        }

        .cta-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--flame-500), var(--ember-600));
            opacity: 0;
            transition: opacity 0.25s;
        }

        .cta-btn::after {
            content: '';
            position: absolute;
            top: -70%;
            left: -28%;
            width: 40%;
            height: 240%;
            background: linear-gradient(110deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.28) 48%, rgba(255, 255, 255, 0) 100%);
            transform: translateX(-210%) rotate(8deg);
            transition: transform 0.62s var(--spring-soft);
            z-index: 0;
            pointer-events: none;
        }

        .cta-btn:hover::after {
            transform: translateX(380%) rotate(8deg);
        }

        .cta-btn:hover::before {
            opacity: 1;
        }

        .cta-btn:hover {
            transform: translateY(-3px) scale(1.01);
            filter: saturate(1.08) brightness(1.04);
            box-shadow: 0 16px 40px rgba(249, 115, 22, 0.34), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        }

        .cta-btn:active {
            transform: scale(0.97);
        }

        .cta-btn span {
            position: relative;
            z-index: 1;
        }

        .cta-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .cta-btn:disabled::before {
            display: none;
        }

        .cta-btn .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            position: relative;
            z-index: 1;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .footer-cta {
            position: relative;
            z-index: 10;
            padding: 20px 24px 32px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            background: transparent;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.28s ease, transform 0.3s var(--spring-soft);
        }

        .footer-cta--hidden {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
        }

        .quiz-container[data-density='tight'] {
            --type-headline-size: clamp(22px, 4.2vw, 32px);
            --type-body-md-size: 14px;
            --type-body-md-lh: 1.5;
            --type-ui-size: 14px;
            --type-cta-size: 15px;
        }

        .quiz-container[data-density='tight'] .quiz-header {
            padding-top: 16px;
        }

        .quiz-container[data-density='tight'] .quiz-content {
            padding: 18px 22px;
        }

        .quiz-container[data-density='tight'] .step.active {
            gap: 16px;
        }

        .quiz-container[data-density='tight'] .option-btn {
            padding: 12px 14px;
            gap: 10px;
        }

        .quiz-container[data-density='tight'] .step-sub {
            margin-top: 14px;
        }

        .quiz-container[data-density='tight'] .step-guidance {
            margin-top: 10px;
            margin-bottom: 12px;
            padding: 10px 12px;
        }

        .quiz-container[data-density='tight'] .footer-cta {
            padding: 14px 22px 20px;
        }

        .quiz-container[data-density='compact'] {
            --type-headline-size: clamp(20px, 3.8vw, 28px);
            --type-body-md-size: 13px;
            --type-body-md-lh: 1.4;
            --type-ui-size: 13px;
            --type-cta-size: 14px;
            --type-progress-size: 10px;
            --type-progress-stage-size: 9px;
        }

        .quiz-container[data-density='compact'] .logo {
            margin-bottom: 10px;
        }

        .quiz-container[data-density='compact'] .logo img {
            height: 20px;
        }

        .quiz-container[data-density='compact'] .logo-text {
            font-size: 12px;
            letter-spacing: 0.12em;
        }

        .quiz-container[data-density='compact'] .quiz-header {
            padding-top: 12px;
            padding-left: 18px;
            padding-right: 18px;
        }

        .quiz-container[data-density='compact'] .quiz-content {
            padding: 14px 16px;
        }

        .quiz-container[data-density='compact'] .step.active {
            gap: 12px;
        }

        .quiz-container[data-density='compact'] .step-sub {
            margin-top: 8px;
            max-width: 100%;
        }

        .quiz-container[data-density='compact'] .step-guidance {
            display: none;
        }

        .quiz-container[data-density='compact'] .option-btn {
            padding: 10px 12px;
            border-radius: 10px;
        }

        .quiz-container[data-density='compact'] .option-label {
            line-height: 1.35;
        }

        .quiz-container[data-density='compact'] .input-wrap {
            gap: 6px;
        }

        .quiz-container[data-density='compact'] .input-field {
            padding: 12px 14px;
        }

        .quiz-container[data-density='compact'] .footer-cta {
            padding: 10px 16px 14px;
        }

        .quiz-container[data-density='compact'] .cta-btn {
            padding: 13px 18px;
            min-width: 0;
            width: 100%;
        }

        .quiz-container[data-density='compact'] .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .quiz-container[data-density='compact'] .options-grid--single {
            grid-template-columns: 1fr;
        }

        .quiz-container[data-overflow-risk='1'] .step-guidance {
            display: none;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 22px;
            margin-top: 0;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .step[data-step='resultado'].active {
            gap: 12px;
        }

        .result-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--flame-500), var(--ember-600), var(--flame-500));
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .result-score-ring {
            width: 92px;
            height: 92px;
            margin: 0 auto 14px;
            position: relative;
        }

        .result-score-ring svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .result-score-ring-bg {
            fill: none;
            stroke: rgba(255, 255, 255, 0.06);
            stroke-width: 6;
        }

        .result-score-ring-fill {
            fill: none;
            stroke: url(#scoreGradient);
            stroke-width: 6;
            stroke-linecap: round;
            stroke-dasharray: 264;
            stroke-dashoffset: 264;
            transition: stroke-dashoffset 1.2s cubic-bezier(0.22, 1, 0.36, 1);
            filter: drop-shadow(0 0 0 rgba(249, 115, 22, 0));
        }

        .result-score-ring-fill.animate {
            stroke-dashoffset: var(--score-offset);
            filter: drop-shadow(0 0 8px rgba(249, 115, 22, 0.34));
        }

        .result-score-ring.is-live {
            animation: ringPulseIn 1.1s var(--spring-bounce) both;
        }

        @keyframes ringPulseIn {
            0% {
                transform: scale(0.92);
                opacity: 0.55;
            }
            65% {
                transform: scale(1.04);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .result-score-value {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--ff-display);
            font-size: var(--type-score-size);
            font-weight: 700;
            line-height: 1;
            color: #ffffff;
            text-shadow: 0 0 0 rgba(249, 115, 22, 0);
        }

        .result-score-value.is-live {
            animation: scoreValuePop 0.85s var(--spring-bounce) both;
        }

        .result-score-value.is-counting {
            animation: scoreValuePop 0.85s var(--spring-bounce) both, scoreValueBreath 0.9s ease-in-out infinite;
        }

        .result-score-value.tick {
            transform: translateY(-1px) scale(1.02);
            transition: transform 0.14s ease;
        }

        @keyframes scoreValueBreath {
            0%, 100% {
                filter: brightness(1);
            }
            50% {
                filter: brightness(1.08);
            }
        }

        @keyframes scoreValuePop {
            0% {
                transform: scale(0.85);
                opacity: 0;
                text-shadow: 0 0 0 rgba(249, 115, 22, 0);
            }
            60% {
                transform: scale(1.08);
                opacity: 1;
                text-shadow: 0 0 16px rgba(249, 115, 22, 0.3);
            }
            100% {
                transform: scale(1);
                opacity: 1;
                text-shadow: 0 0 6px rgba(249, 115, 22, 0.16);
            }
        }

        .result-score-label {
            font-size: 10px;
            font-weight: 500;
            letter-spacing: var(--type-meta-track);
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            text-align: center;
            margin-top: -4px;
            margin-bottom: 10px;
        }

        .result-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 12px;
            margin-bottom: 10px;
        }

        .result-metric {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            transition: transform 0.25s var(--spring-soft), border-color 0.2s ease, background-color 0.2s ease;
        }

        .result-metric:hover {
            transform: translateY(-2px);
            border-color: rgba(249, 115, 22, 0.22);
            background: rgba(249, 115, 22, 0.08);
        }

        .result-metric-icon {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .result-metric-value {
            font-family: var(--ff-display);
            font-size: var(--type-metric-size);
            font-weight: 600;
            line-height: 1.2;
            color: #ffffff;
            margin-bottom: 1px;
        }

        .result-metric-label {
            font-size: var(--type-meta-size);
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.3;
        }

        .result-insight {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.12), rgba(220, 38, 38, 0.06));
            border: 1px solid rgba(249, 115, 22, 0.2);
            border-radius: 12px;
            padding: 12px 14px;
            margin-top: 8px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .result-phase {
            opacity: 0;
            transform: translateY(10px) scale(0.985);
            filter: blur(2px);
            transition: opacity 0.36s ease, transform 0.42s var(--spring-soft), filter 0.36s ease;
            will-change: opacity, transform, filter;
        }

        .result-phase.is-visible {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }

        .result-card.result-enter {
            opacity: 0;
            transform: translateY(18px) scale(0.975);
            filter: blur(5px);
            animation: resultCardEnter 0.72s var(--spring-bounce) forwards;
            transform-origin: 50% 100%;
        }

        @keyframes resultCardEnter {
            0% {
                opacity: 0;
                transform: translateY(18px) scale(0.975);
                filter: blur(5px);
            }
            72% {
                opacity: 1;
                transform: translateY(-2px) scale(1.01);
                filter: blur(0);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        .result-insight-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1px;
        }

        .result-insight-text {
            font-size: var(--type-body-sm-size);
            line-height: 1.45;
            color: rgba(255, 255, 255, 0.7);
        }

        .quiz-container[data-density='tight'] .step[data-step='resultado'] .result-card {
            padding: 16px;
            border-radius: 16px;
        }

        .quiz-container[data-density='tight'] .step[data-step='resultado'] .result-score-ring {
            width: 84px;
            height: 84px;
            margin-bottom: 10px;
        }

        .quiz-container[data-density='tight'] .step[data-step='resultado'] .result-score-value {
            font-size: 24px;
        }

        .quiz-container[data-density='tight'] .step[data-step='resultado'] .result-metrics {
            gap: 6px;
        }

        .quiz-container[data-density='tight'] .step[data-step='resultado'] .result-metric {
            padding: 9px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] {
            gap: 8px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .step-number {
            margin-bottom: 8px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-card {
            padding: 12px;
            border-radius: 14px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-score-ring {
            width: 74px;
            height: 74px;
            margin-bottom: 8px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-score-value {
            font-size: 21px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-score-label {
            margin-bottom: 8px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-metrics {
            gap: 6px;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-metric {
            padding: 8px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-metric-icon {
            display: none;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-metric-value {
            font-size: 12px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-metric-label {
            font-size: 10px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-insight {
            padding: 10px 11px;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-insight-icon {
            display: none;
        }

        .quiz-container[data-density='compact'] .step[data-step='resultado'] .result-insight-text {
            font-size: 12px;
            line-height: 1.35;
        }

        .result-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-refazer {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 100px;
            color: rgba(255, 255, 255, 0.6);
            font-family: var(--ff-body);
            font-size: 14px;
            font-weight: 500;
            line-height: 1.35;
            cursor: pointer;
            transition: color 0.2s ease, border-color 0.2s ease, background-color 0.2s ease, transform 0.24s var(--spring-soft);
        }

        .btn-refazer:hover {
            border-color: rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.03);
        }

        .btn-refazer svg {
            width: 16px;
            height: 16px;
            transition: transform 0.3s;
        }

        .btn-refazer:hover svg {
            transform: rotate(-90deg);
        }

        .result-enter {
            animation: resultEnter 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes resultEnter {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .result-metric-enter {
            animation: metricEnter 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }

        .result-metric-enter:nth-child(1) { animation-delay: 0.2s; }
        .result-metric-enter:nth-child(2) { animation-delay: 0.3s; }
        .result-metric-enter:nth-child(3) { animation-delay: 0.4s; }
        .result-metric-enter:nth-child(4) { animation-delay: 0.5s; }

        @keyframes metricEnter {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            line-height: 1.2;
            font-family: var(--ff-display);
        }

        .result-badge--hot {
            background: rgba(249, 115, 22, 0.16);
            color: var(--flame-500);
            border: 1px solid rgba(249, 115, 22, 0.28);
        }

        .result-badge--warm {
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.2);
        }

        .result-badge--cold {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .result-text {
            font-size: var(--type-body-md-size);
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 12px;
            text-wrap: pretty;
        }

        .micro-text {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
            text-align: center;
            margin-top: 12px;
        }

        .welcome-features {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 22px;
        }

        .welcome-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: var(--type-body-sm-size);
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.58);
        }

        .welcome-feature-dot {
            width: 4px;
            height: 4px;
            background: rgba(249, 115, 22, 0.55);
            border-radius: 50%;
            flex-shrink: 0;
            box-shadow: 0 0 0 rgba(249, 115, 22, 0);
            animation: emberPulse 2.6s ease-in-out infinite;
        }

        @keyframes emberPulse {
            0%, 100% {
                background: rgba(249, 115, 22, 0.55);
                box-shadow: 0 0 0 rgba(249, 115, 22, 0);
            }
            50% {
                background: rgba(239, 68, 68, 0.82);
                box-shadow: 0 0 10px rgba(249, 115, 22, 0.45);
            }
        }

        .input-error {
            border-color: rgba(249, 115, 22, 0.6) !important;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.16) !important;
        }

        .error-msg {
            font-size: 12px;
            line-height: 1.35;
            color: var(--flame-500);
            margin-top: 0;
            min-height: 18px;
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity 0.2s ease, transform var(--motion-flame-settle);
            pointer-events: none;
        }

        .error-msg.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (min-width: 640px) {
            .quiz-content {
                max-width: 560px;
                margin: 0 auto;
                width: 100%;
            }

            .quiz-header {
                max-width: 560px;
                margin: 0 auto;
                width: 100%;
            }

            .footer-cta {
                max-width: 560px;
                margin: 0 auto;
                width: 100%;
            }

            .options-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .options-grid--single {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 1024px) {
            .quiz-content {
                max-width: 640px;
            }

            .quiz-header {
                max-width: 640px;
            }

            .footer-cta {
                max-width: 640px;
            }
        }

        @media (max-width: 420px) {
            :root {
                --type-headline-size: clamp(22px, 7.2vw, 30px);
                --type-body-md-size: 14px;
                --type-body-md-lh: 1.55;
                --type-ui-size: 14px;
                --type-cta-size: 15px;
                --type-progress-size: 11px;
                --type-progress-stage-size: 10px;
            }

            .quiz-content {
                padding: 18px;
                justify-content: flex-start;
            }

            .step.active {
                gap: 18px;
            }

            .option-btn {
                padding: 14px 16px;
                gap: 12px;
            }

            .footer-cta {
                padding: 16px 18px 26px;
            }

            .cta-btn {
                min-width: 100%;
                padding: 15px 20px;
            }

            .result-card {
                padding: 22px;
                border-radius: 16px;
            }

            .result-metrics {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-bg" id="quizBg" data-mood="welcome" data-bias="clarity">
            <div class="quiz-bg-layer quiz-bg-layer--mesh"></div>
            <div class="quiz-bg-layer quiz-bg-layer--rings"></div>
            <div class="quiz-bg-layer quiz-bg-layer--grid"></div>
            <div class="quiz-bg-layer quiz-bg-layer--noise"></div>
            <div class="quiz-bg-spotlight" id="quizBgSpotlight"></div>
        </div>

        <div class="corner-line corner-line--tl"></div>
        <div class="corner-line corner-line--br"></div>
        <div class="corner-line corner-line--bl"></div>

        <header class="quiz-header">
            <div class="logo">
                <img src="logo_new.png" alt="ChamaLead">
                <span class="logo-text">CHAMALEAD</span>
            </div>
            <div class="progress-track is-hidden" id="progressTrack">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-meta is-hidden" id="progressMeta">
                <span class="progress-step-label" id="progressStepLabel">Etapa 0 de 10</span>
                <span class="progress-center">
                    <span class="progress-stage-label" id="progressStageLabel">
                        <span class="progress-stage-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M9 12l2 2 4-4"></path>
                            </svg>
                        </span>
                        <span class="progress-stage-text">Descoberta</span>
                    </span>
                </span>
                <span class="progress-percent-label" id="progressPercentLabel">0%</span>
            </div>
        </header>

        <main class="quiz-content" id="quizContent">

            <p class="step-guidance" id="stepGuidance"></p>

            <div class="step active" data-step="welcome">
                <div>
                    <p class="step-number">Diagnóstico rápido</p>
                    <h1 class="step-headline">
                        Descubra em 2 minutos se sua operação está <span class="accent flame-live" data-text="perdendo leads">perdendo leads</span> todos os dias
                    </h1>
                    <p class="step-sub">
                        Responda algumas perguntas e veja se já faz sentido automatizar sua prospecção, atendimento e follow-up no WhatsApp.
                    </p>
                    <div class="welcome-features">
                        <div class="welcome-feature">
                            <span class="welcome-feature-dot"></span>
                            Sem enrolação
                        </div>
                        <div class="welcome-feature">
                            <span class="welcome-feature-dot"></span>
                            Continuação no WhatsApp
                        </div>
                    </div>
                </div>
            </div>

            <div class="step" data-step="nome">
                <div>
                    <p class="step-number">Etapa 1 de 10</p>
                    <h2 class="step-headline">Como posso te chamar?</h2>
                </div>
                <div class="input-wrap">
                    <label class="input-label" for="inputNome">Seu nome</label>
                    <input type="text" class="input-field" id="inputNome" placeholder="Digite seu nome" autocomplete="given-name" maxlength="120" aria-describedby="errorNome">
                    <p class="error-msg" id="errorNome">Por favor, informe seu nome</p>
                </div>
            </div>

            <div class="step" data-step="whatsapp">
                <div>
                    <p class="step-number">Etapa 2 de 10</p>
                    <h2 class="step-headline">Qual é o seu melhor WhatsApp <span class="accent">com DDD</span>?</h2>
                </div>
                <div class="input-wrap">
                    <label class="input-label" for="inputWhatsapp">WhatsApp com DDD</label>
                    <input type="tel" class="input-field" id="inputWhatsapp" placeholder="(11) 99999-9999" autocomplete="tel" maxlength="15" aria-describedby="errorWhatsapp">
                    <p class="error-msg" id="errorWhatsapp">Digite um WhatsApp valido com DDD. Ex.: (11) 99999-9999</p>
                </div>
            </div>

            <div class="step" data-step="cargo">
                <div>
                    <p class="step-number">Etapa 3 de 10</p>
                    <h2 class="step-headline">Qual dessas opções melhor te descreve <span class="accent">hoje</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="dono" data-field="cargo">
                        <span class="option-icon"></span>
                        <span class="option-label">Sou dono(a) / sócio(a)</span>
                    </button>
                    <button class="option-btn" data-value="gestor" data-field="cargo">
                        <span class="option-icon"></span>
                        <span class="option-label">Sou gestor(a) comercial / atendimento</span>
                    </button>
                    <button class="option-btn" data-value="time" data-field="cargo">
                        <span class="option-icon"></span>
                        <span class="option-label">Sou parte do time</span>
                    </button>
                    <button class="option-btn" data-value="outro" data-field="cargo">
                        <span class="option-icon"></span>
                        <span class="option-label">Outro</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="faturamento">
                <div>
                    <p class="step-number">Etapa 4 de 10</p>
                    <h2 class="step-headline">Em média, quanto sua empresa <span class="accent">fatura por mês</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="ate_10k" data-field="faturamento">
                        <span class="option-icon"></span>
                        <span class="option-label">Até R$ 10 mil</span>
                    </button>
                    <button class="option-btn" data-value="10k_20k" data-field="faturamento">
                        <span class="option-icon"></span>
                        <span class="option-label">R$ 10 mil a R$ 20 mil</span>
                    </button>
                    <button class="option-btn" data-value="20k_50k" data-field="faturamento">
                        <span class="option-icon"></span>
                        <span class="option-label">R$ 20 mil a R$ 50 mil</span>
                    </button>
                    <button class="option-btn" data-value="50k_100k" data-field="faturamento">
                        <span class="option-icon"></span>
                        <span class="option-label">R$ 50 mil a R$ 100 mil</span>
                    </button>
                    <button class="option-btn" data-value="acima_100k" data-field="faturamento">
                        <span class="option-icon"></span>
                        <span class="option-label">Acima de R$ 100 mil</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="canal">
                <div>
                    <p class="step-number">Etapa 5 de 10</p>
                    <h2 class="step-headline">Hoje, por onde entram mais <span class="accent">oportunidades</span> no seu comercial?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="whatsapp_direto" data-field="canal">
                        <span class="option-icon"></span>
                        <span class="option-label">WhatsApp direto</span>
                    </button>
                    <button class="option-btn" data-value="instagram_whatsapp" data-field="canal">
                        <span class="option-icon"></span>
                        <span class="option-label">Instagram → WhatsApp</span>
                    </button>
                    <button class="option-btn" data-value="trafego_pago" data-field="canal">
                        <span class="option-icon"></span>
                        <span class="option-label">Tráfego pago</span>
                    </button>
                    <button class="option-btn" data-value="indicacao" data-field="canal">
                        <span class="option-icon"></span>
                        <span class="option-label">Indicação</span>
                    </button>
                    <button class="option-btn" data-value="prospeccao_ativa" data-field="canal">
                        <span class="option-icon"></span>
                        <span class="option-label">Prospecção ativa</span>
                    </button>
                    <button class="option-btn" data-value="varios_canais" data-field="canal">
                        <span class="option-icon"></span>
                        <span class="option-label">Vários canais misturados</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="volume">
                <div>
                    <p class="step-number">Etapa 6 de 10</p>
                    <h2 class="step-headline">Quantos novos leads ou conversas comerciais vocês recebem <span class="accent">por semana</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="0_10" data-field="volume_leads">
                        <span class="option-icon"></span>
                        <span class="option-label">0 a 10</span>
                    </button>
                    <button class="option-btn" data-value="11_30" data-field="volume_leads">
                        <span class="option-icon"></span>
                        <span class="option-label">11 a 30</span>
                    </button>
                    <button class="option-btn" data-value="31_100" data-field="volume_leads">
                        <span class="option-icon"></span>
                        <span class="option-label">31 a 100</span>
                    </button>
                    <button class="option-btn" data-value="100_mais" data-field="volume_leads">
                        <span class="option-icon"></span>
                        <span class="option-label">100+</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor">
                <div>
                    <p class="step-number">Etapa 7 de 10</p>
                    <h2 class="step-headline">Onde você sente que mais <span class="accent">perde oportunidades</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="atendimento_lento" data-field="dor_principal">
                        <span class="option-icon"></span>
                        <span class="option-label">Demora no primeiro atendimento</span>
                    </button>
                    <button class="option-btn" data-value="fora_horario" data-field="dor_principal">
                        <span class="option-icon"></span>
                        <span class="option-label">Leads chegam fora do horário e ninguém responde</span>
                    </button>
                    <button class="option-btn" data-value="falta_followup" data-field="dor_principal">
                        <span class="option-icon"></span>
                        <span class="option-label">Falta de follow-up</span>
                    </button>
                    <button class="option-btn" data-value="prospeccao_inconsistente" data-field="dor_principal">
                        <span class="option-icon"></span>
                        <span class="option-label">A prospecção não acontece de forma consistente</span>
                    </button>
                    <button class="option-btn" data-value="converte_mal" data-field="dor_principal">
                        <span class="option-icon"></span>
                        <span class="option-label">O comercial conversa, mas converte mal</span>
                    </button>
                    <button class="option-btn" data-value="organizacao_baguncada" data-field="dor_principal">
                        <span class="option-icon"></span>
                        <span class="option-label">Agendamento / repasse / organização são bagunçados</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor_atendimento_lento">
                <div>
                    <p class="step-number">Etapa 8 de 10</p>
                    <h2 class="step-headline">Quanto tempo um lead costuma esperar pelo <span class="accent">primeiro retorno</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="menos_5min" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Menos de 5 min</span>
                    </button>
                    <button class="option-btn" data-value="5_30min" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">5 a 30 min</span>
                    </button>
                    <button class="option-btn" data-value="mais_30min" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Mais de 30 min</span>
                    </button>
                    <button class="option-btn" data-value="so_horario_comercial" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Só no horário comercial</span>
                    </button>
                    <button class="option-btn" data-value="nao_sei" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Não sei / varia muito</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor_fora_horario">
                <div>
                    <p class="step-number">Etapa 8 de 10</p>
                    <h2 class="step-headline">O que acontece quando alguém chama <span class="accent">à noite ou no fim de semana</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="ninguem_responde" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Ninguém responde</span>
                    </button>
                    <button class="option-btn" data-value="responde_dia_seguinte" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Responde no dia seguinte</span>
                    </button>
                    <button class="option-btn" data-value="as vezes_cobre" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Às vezes alguém cobre</span>
                    </button>
                    <button class="option-btn" data-value="tem_plantao" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Já temos plantão</span>
                    </button>
                    <button class="option-btn" data-value="sem_volume" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Não temos volume nesse horário</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor_falta_followup">
                <div>
                    <p class="step-number">Etapa 8 de 10</p>
                    <h2 class="step-headline">Quando o lead some, existe um processo para <span class="accent">retomar a conversa</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="nao_existe" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Não existe</span>
                    </button>
                    <button class="option-btn" data-value="manual" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Existe, mas é manual</span>
                    </button>
                    <button class="option-btn" data-value="parcial" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Existe parcialmente</span>
                    </button>
                    <button class="option-btn" data-value="estruturado" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Sim, é bem estruturado</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor_prospeccao">
                <div>
                    <p class="step-number">Etapa 8 de 10</p>
                    <h2 class="step-headline">Sua prospecção acontece todos os dias ou <span class="accent">depende do time lembrar</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="todo_dia" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Acontece todo dia</span>
                    </button>
                    <button class="option-btn" data-value="alguns_dias" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Acontece alguns dias</span>
                    </button>
                    <button class="option-btn" data-value="irregular" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">É irregular</span>
                    </button>
                    <button class="option-btn" data-value="quase_nao" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Quase não acontece</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor_converte">
                <div>
                    <p class="step-number">Etapa 8 de 10</p>
                    <h2 class="step-headline">O que mais <span class="accent">trava o fechamento</span> hoje?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="lead_desqualificado" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Lead desqualificado</span>
                    </button>
                    <button class="option-btn" data-value="resposta_lenta" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Resposta lenta</span>
                    </button>
                    <button class="option-btn" data-value="falta_followup" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Falta de follow-up</span>
                    </button>
                    <button class="option-btn" data-value="objecoes_preco" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Objeções / preço</span>
                    </button>
                    <button class="option-btn" data-value="sem_processo" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Falta de processo comercial</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="dor_organizacao">
                <div>
                    <p class="step-number">Etapa 8 de 10</p>
                    <h2 class="step-headline">Hoje o lead consegue avançar sem depender de alguém do seu time estar <span class="accent">online</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="nao" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Não</span>
                    </button>
                    <button class="option-btn" data-value="poucos_casos" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Em poucos casos</span>
                    </button>
                    <button class="option-btn" data-value="maioria" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Na maioria dos casos</span>
                    </button>
                    <button class="option-btn" data-value="sim" data-field="dor_detalhe">
                        <span class="option-icon"></span>
                        <span class="option-label">Sim</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="urgencia">
                <div>
                    <p class="step-number">Etapa 9 de 10</p>
                    <h2 class="step-headline">Se a automação começasse a rodar nos próximos dias, qual cenário <span class="accent">faz mais sentido</span>?</h2>
                </div>
                <div class="options-grid">
                    <button class="option-btn" data-value="agora" data-field="timing">
                        <span class="option-icon"></span>
                        <span class="option-label">Quero resolver isso agora</span>
                    </button>
                    <button class="option-btn" data-value="este_mes" data-field="timing">
                        <span class="option-icon"></span>
                        <span class="option-label">Ainda neste mês</span>
                    </button>
                    <button class="option-btn" data-value="proximo_mes" data-field="timing">
                        <span class="option-icon"></span>
                        <span class="option-label">Talvez no próximo mês</span>
                    </button>
                    <button class="option-btn" data-value="entendendo" data-field="timing">
                        <span class="option-icon"></span>
                        <span class="option-label">Só estou entendendo melhor por enquanto</span>
                    </button>
                </div>
            </div>

            <div class="step" data-step="resultado">
                <div>
                    <p class="step-number">Diagnóstico concluído</p>
                    <h2 class="step-headline">Seu diagnóstico inicial <span class="accent">está pronto</span></h2>
                    <div class="result-card result-enter" id="resultCard">
                        <div class="result-score-ring">
                            <svg viewBox="0 0 96 96">
                                <defs>
                                    <linearGradient id="scoreGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#f97316" />
                                        <stop offset="100%" stop-color="#dc2626" />
                                    </linearGradient>
                                </defs>
                                <circle class="result-score-ring-bg" cx="48" cy="48" r="42" />
                                <circle class="result-score-ring-fill" id="scoreRing" cx="48" cy="48" r="42" />
                            </svg>
                            <div class="result-score-value" id="scoreValue">0</div>
                        </div>
                        <p class="result-score-label">Potencial de automação</p>
                        <div id="resultBadge" class="result-phase"></div>
                        <div class="result-metrics result-phase" id="resultMetrics"></div>
                        <div class="result-insight result-phase" id="resultInsight">
                            <div class="result-insight-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <p class="result-insight-text" id="resultInsightText"></p>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <footer class="footer-cta" id="footerCta">
            <button class="cta-btn" id="ctaBtn">
                <span>Começar diagnóstico</span>
            </button>
        </footer>
    </div>

    <script>
        (function () {
            'use strict';

            const STEPS = [
                'welcome',
                'nome',
                'whatsapp',
                'cargo',
                'faturamento',
                'canal',
                'volume',
                'dor',
                'urgencia',
                'resultado',
            ];

            const CONDITIONAL_STEPS = {
                atendimento_lento: 'dor_atendimento_lento',
                fora_horario: 'dor_fora_horario',
                falta_followup: 'dor_falta_followup',
                prospeccao_inconsistente: 'dor_prospeccao',
                converte_mal: 'dor_converte',
                organizacao_baguncada: 'dor_organizacao',
            };

            const TOTAL_STEPS = 10;

            let currentStepIndex = 0;
            let conditionalStep = null;
            let answers = {};
            let sessionId = '';
            const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            let fitRaf = null;

            function motionDelay(ms) {
                return prefersReducedMotion ? 0 : ms;
            }

            function onPhaseTransitionEnd(element, callback) {
                if (!element || typeof callback !== 'function') {
                    return;
                }

                if (prefersReducedMotion) {
                    callback();
                    return;
                }

                let done = false;
                function finish() {
                    if (done) return;
                    done = true;
                    element.removeEventListener('transitionend', handle);
                    callback();
                }

                function handle(event) {
                    if (event.target !== element) return;
                    if (event.propertyName !== 'opacity') return;
                    finish();
                }

                element.addEventListener('transitionend', handle);
                setTimeout(finish, motionDelay(520));
            }

            function onAnimationEnd(element, callback, fallbackMs) {
                if (!element || typeof callback !== 'function') {
                    return;
                }

                if (prefersReducedMotion) {
                    callback();
                    return;
                }

                let done = false;
                function finish() {
                    if (done) return;
                    done = true;
                    element.removeEventListener('animationend', handle);
                    callback();
                }

                function handle(event) {
                    if (event.target !== element) return;
                    finish();
                }

                element.addEventListener('animationend', handle);
                setTimeout(finish, motionDelay(fallbackMs || 900));
            }

            function revealResultPhases(phases) {
                if (!Array.isArray(phases) || !phases.length) {
                    return;
                }

                let index = 0;
                function revealNext() {
                    const phase = phases[index];
                    if (!phase) return;

                    phase.classList.add('is-visible');
                    index += 1;

                    if (index < phases.length) {
                        onPhaseTransitionEnd(phase, revealNext);
                    }
                }

                revealNext();
            }

            function generateSessionId() {
                return crypto.randomUUID ? crypto.randomUUID() : [Date.now().toString(36), Math.random().toString(36).slice(2)].join('-');
            }

            function getUTMParams() {
                const params = new URLSearchParams(window.location.search);
                return {
                    utm_source: params.get('utm_source') || '',
                    utm_medium: params.get('utm_medium') || '',
                    utm_campaign: params.get('utm_campaign') || '',
                    utm_content: params.get('utm_content') || '',
                    utm_term: params.get('utm_term') || '',
                };
            }

            function loadState() {
                try {
                    const saved = localStorage.getItem('chamalead_quiz');
                    if (saved) {
                        const data = JSON.parse(saved);
                        if (data.sessionId && data.answers) {
                            sessionId = data.sessionId;
                            answers = data.answers;
                            currentStepIndex = data.currentStepIndex || 0;
                            conditionalStep = data.conditionalStep || null;
                            return true;
                        }
                    }
                } catch (e) {
                }
                return false;
            }

            function saveState() {
                try {
                    localStorage.setItem('chamalead_quiz', JSON.stringify({
                        sessionId,
                        answers,
                        currentStepIndex,
                        conditionalStep,
                    }));
                } catch (e) {
                }
            }

            function init() {
                const restored = loadState();
                if (!restored) {
                    sessionId = generateSessionId();
                    answers = {};
                    currentStepIndex = 0;
                    conditionalStep = null;
                }
                saveState();
                bindBackgroundInteractivity();
                renderStep();
                bindEvents();
            }

            function getBackgroundPreset(stepKey) {
                if (stepKey === 'welcome') {
                    return {
                        mood: 'welcome',
                        bias: 'desire',
                        energy: 0.86,
                        speed: '14s',
                        angle: '128deg',
                        warm: 0.22,
                        alert: 0.09,
                        focusX: '44%',
                        focusY: '28%',
                        grid: 0.2,
                    };
                }

                if (stepKey === 'nome' || stepKey === 'whatsapp') {
                    return {
                        mood: 'contact',
                        bias: 'clarity',
                        energy: 0.48,
                        speed: '20s',
                        angle: '140deg',
                        warm: 0.13,
                        alert: 0.07,
                        focusX: '48%',
                        focusY: '30%',
                        grid: 0.14,
                    };
                }

                if (stepKey === 'dor' || (stepKey && stepKey.indexOf('dor_') === 0)) {
                    return {
                        mood: 'pain',
                        bias: 'risk',
                        energy: 0.78,
                        speed: '12s',
                        angle: '118deg',
                        warm: 0.14,
                        alert: 0.2,
                        focusX: '58%',
                        focusY: '26%',
                        grid: 0.18,
                    };
                }

                if (stepKey === 'urgencia') {
                    return {
                        mood: 'urgency',
                        bias: 'action',
                        energy: 0.92,
                        speed: '10.5s',
                        angle: '106deg',
                        warm: 0.2,
                        alert: 0.2,
                        focusX: '62%',
                        focusY: '32%',
                        grid: 0.16,
                    };
                }

                if (stepKey === 'resultado') {
                    return {
                        mood: 'result',
                        bias: 'reward',
                        energy: 0.62,
                        speed: '17s',
                        angle: '134deg',
                        warm: 0.2,
                        alert: 0.08,
                        focusX: '50%',
                        focusY: '24%',
                        grid: 0.22,
                    };
                }

                return {
                    mood: 'diagnosis',
                    bias: 'focus',
                    energy: 0.66,
                    speed: '16s',
                    angle: '130deg',
                    warm: 0.17,
                    alert: 0.1,
                    focusX: '52%',
                    focusY: '29%',
                    grid: 0.18,
                };
            }

            function applyBackgroundMood(stepKey) {
                const quizBg = document.getElementById('quizBg');
                if (!quizBg) return;

                const preset = getBackgroundPreset(stepKey || 'welcome');
                quizBg.dataset.mood = preset.mood;
                quizBg.dataset.bias = preset.bias;

                const style = document.documentElement.style;
                style.setProperty('--bg-energy', String(preset.energy));
                style.setProperty('--bg-speed', preset.speed);
                style.setProperty('--bg-angle', preset.angle);
                style.setProperty('--bg-warm-alpha', String(preset.warm));
                style.setProperty('--bg-alert-alpha', String(preset.alert));
                style.setProperty('--bg-focus-x', preset.focusX);
                style.setProperty('--bg-focus-y', preset.focusY);
                style.setProperty('--bg-grid-opacity', String(preset.grid));
            }

            function bindBackgroundInteractivity() {
                const quizBg = document.getElementById('quizBg');
                const spotlight = document.getElementById('quizBgSpotlight');
                if (!quizBg || !spotlight || prefersReducedMotion) return;

                let rafId = null;
                let pointerX = window.innerWidth * 0.5;
                let pointerY = window.innerHeight * 0.35;
                let targetX = pointerX;
                let targetY = pointerY;

                function updateSpotlight() {
                    pointerX += (targetX - pointerX) * 0.08;
                    pointerY += (targetY - pointerY) * 0.08;
                    spotlight.style.left = pointerX + 'px';
                    spotlight.style.top = pointerY + 'px';
                    rafId = window.requestAnimationFrame(updateSpotlight);
                }

                function setTarget(clientX, clientY) {
                    const clampedY = Math.min(window.innerHeight * 0.72, Math.max(0, clientY));
                    targetX = clientX;
                    targetY = clampedY;
                }

                window.addEventListener('mousemove', function (event) {
                    setTarget(event.clientX, event.clientY);
                });

                window.addEventListener('touchmove', function (event) {
                    if (!event.touches || !event.touches[0]) return;
                    setTarget(event.touches[0].clientX, event.touches[0].clientY);
                }, { passive: true });

                window.addEventListener('resize', function () {
                    targetX = Math.min(targetX, window.innerWidth);
                    targetY = Math.min(targetY, window.innerHeight * 0.72);
                });

                if (!rafId) {
                    rafId = window.requestAnimationFrame(updateSpotlight);
                }
            }

            function getStepKey(index) {
                if (index >= STEPS.length) return null;
                const base = STEPS[index];
                if (base === 'urgencia' && conditionalStep) {
                    return conditionalStep;
                }
                return base;
            }

            function getEffectiveTotal() {
                return conditionalStep ? TOTAL_STEPS : TOTAL_STEPS - 1;
            }

            function getProgress() {
                if (currentStepIndex === 0) return 0;
                const effectiveTotal = getEffectiveTotal();
                const stepNum = currentStepIndex;
                return Math.min((stepNum / effectiveTotal) * 100, 100);
            }

            function getProgressLabels() {
                const effectiveTotal = getEffectiveTotal();
                const stepNum = Math.max(0, Math.min(currentStepIndex, effectiveTotal));
                const percent = Math.round(getProgress());
                return {
                    stepNum,
                    effectiveTotal,
                    percent,
                };
            }

            function getProgressStage(stepNum, total) {
                if (!stepNum || stepNum <= 2) {
                    return {
                        label: 'Descoberta',
                        tone: 'stage-discovery',
                        icon: '<circle cx="12" cy="12" r="9"></circle><path d="M9 12l2 2 4-4"></path>',
                    };
                }

                if (stepNum < total) {
                    return {
                        label: 'Diagnóstico',
                        tone: 'stage-diagnosis',
                        icon: '<path d="M12 3v18"></path><path d="M3 12h18"></path><circle cx="12" cy="12" r="9"></circle>',
                    };
                }

                return {
                    label: 'Resultado',
                    tone: 'stage-result',
                    icon: '<path d="M12 3l2.6 5.2L20 9l-4 3.9.9 5.5L12 15.8 7.1 18.4 8 12.9 4 9l5.4-.8L12 3z"></path>',
                };
            }

            function renderProgressStage(stage) {
                const icon = stage.icon || '';
                const label = stage.label || '';
                return '<span class="progress-stage-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + icon + '</svg></span><span class="progress-stage-text">' + label + '</span>';
            }

            function getResultCtaByScore(score) {
                if (score >= 70) {
                    return 'Alta prioridade: quero acelerar minha automação';
                }
                if (score >= 40) {
                    return 'Quero ver o plano ideal para meu cenário';
                }
                return 'Quero mapear meus próximos passos';
            }

            function getResultCtaByClassificacao(classificacao) {
                if (classificacao === 'quente') {
                    return 'Perfeito. Vamos acelerar sua implementação';
                }
                if (classificacao === 'morno') {
                    return 'Ótimo. Vamos desenhar seu plano de automação';
                }
                return 'Fechado. Vamos organizar seus próximos ganhos';
            }

            function updateProgressUI() {
                const progressTrack = document.getElementById('progressTrack');
                const progressFill = document.getElementById('progressFill');
                const progressMeta = document.getElementById('progressMeta');
                const progressStepLabel = document.getElementById('progressStepLabel');
                const progressStageLabel = document.getElementById('progressStageLabel');
                const progressPercentLabel = document.getElementById('progressPercentLabel');

                if (currentStepIndex === 0) {
                    progressTrack.classList.add('is-hidden');
                    progressMeta.classList.add('is-hidden');
                    return;
                }

                const info = getProgressLabels();
                progressTrack.classList.remove('is-hidden');
                progressMeta.classList.remove('is-hidden');
                progressFill.style.width = info.percent + '%';
                progressFill.classList.remove('surge');
                requestAnimationFrame(function () {
                    progressFill.classList.add('surge');
                });
                progressStepLabel.textContent = 'Etapa ' + info.stepNum + ' de ' + info.effectiveTotal;
                const stage = getProgressStage(info.stepNum, info.effectiveTotal);
                progressStageLabel.className = 'progress-stage-label ' + (stage.tone || '');
                progressStageLabel.innerHTML = renderProgressStage(stage);
                progressStageLabel.classList.remove('stage-enter');
                requestAnimationFrame(function () {
                    progressStageLabel.classList.add('stage-enter');
                });
                progressPercentLabel.textContent = info.percent + '%';
            }

            function getGuidanceText(stepKey) {
                const byDor = {
                    atendimento_lento: 'Resposta rápida costuma ser o divisor entre orçamento e fechamento.',
                    fora_horario: 'Ganhos fora do horário são onde a automação costuma pagar sozinha.',
                    falta_followup: 'Follow-up consistente aumenta conversão sem aumentar equipe.',
                    prospeccao_inconsistente: 'Cadência previsível reduz meses fracos no comercial.',
                    converte_mal: 'Ajustes no processo aumentam taxa de fechamento com o mesmo tráfego.',
                    organizacao_baguncada: 'Padronização de etapas reduz perda de oportunidades no meio do funil.',
                };

                const staticMap = {
                    welcome: 'Sem pegadinha: no final você recebe um diagnóstico prático para o seu cenário.',
                    nome: 'Vamos personalizar o diagnóstico para você.',
                    whatsapp: 'Usamos esse contato para continuar o diagnóstico com recomendações práticas.',
                    cargo: 'Seu papel muda o tipo de automação mais eficiente.',
                    faturamento: 'Essa resposta ajuda a calibrar o nível de prioridade e implementação.',
                    canal: 'Canal principal define onde focar resposta e qualificação automática.',
                    volume: 'Volume indica se o gargalo é velocidade, processo ou capacidade.',
                    dor: 'Escolha o maior gargalo hoje para gerar recomendações mais assertivas.',
                    dor_atendimento_lento: byDor.atendimento_lento,
                    dor_fora_horario: byDor.fora_horario,
                    dor_falta_followup: byDor.falta_followup,
                    dor_prospeccao: byDor.prospeccao_inconsistente,
                    dor_converte: byDor.converte_mal,
                    dor_organizacao: byDor.organizacao_baguncada,
                    urgencia: 'Quanto mais cedo começar, mais rápido você recupera oportunidades perdidas.',
                    resultado: 'Seu potencial foi calculado com base no seu contexto atual.',
                };

                if (stepKey === 'urgencia' && answers.dor_principal && byDor[answers.dor_principal]) {
                    return byDor[answers.dor_principal];
                }

                return staticMap[stepKey] || '';
            }

            function updateGuidance(stepKey) {
                const guidance = document.getElementById('stepGuidance');
                if (!guidance) return;

                const text = getGuidanceText(stepKey);
                if (!text) {
                    guidance.classList.remove('active');
                    guidance.innerHTML = '';
                    return;
                }

                const iconMap = {
                    welcome: '<path d="M12 3l2.6 5.2L20 9l-4 3.9.9 5.5L12 15.8 7.1 18.4 8 12.9 4 9l5.4-.8L12 3z"></path>',
                    nome: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
                    whatsapp: '<path d="M21 11.5a8.38 8.38 0 0 1-1.9 5.4A8.5 8.5 0 0 1 6.4 18.7L3 20l1.3-3.4A8.5 8.5 0 1 1 21 11.5z"></path>',
                    resultado: '<path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-6"></path>',
                };
                const icon = iconMap[stepKey] || '<circle cx="12" cy="12" r="9"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path>';

                guidance.classList.remove('active');
                guidance.innerHTML = '<span class="guidance-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + icon + '</svg></span><span class="guidance-text">' + text + '</span>';
                requestAnimationFrame(function () {
                    guidance.classList.add('active');
                });
            }

            function renderStep() {
                const stepKey = getStepKey(currentStepIndex);
                if (!stepKey) return;

                document.querySelectorAll('.step').forEach(function (el) {
                    el.classList.remove('active', 'exiting');
                    el.style.display = 'none';
                });

                const target = document.querySelector('[data-step="' + stepKey + '"]');
                if (!target) return;

                target.style.display = '';
                target.classList.add('active');

                applyBackgroundMood(stepKey);
                updateProgressUI();
                updateGuidance(stepKey);

                updateFooter();
                restoreInputValues();

                scheduleLayoutFit();
            }

            function checkContentFits() {
                const content = document.getElementById('quizContent');
                if (!content) return true;
                return content.scrollHeight <= content.clientHeight + 1;
            }

            function applyDensity(density) {
                const container = document.querySelector('.quiz-container');
                if (!container) return;

                container.dataset.density = density;
            }

            function fitNoScrollLayout() {
                const container = document.querySelector('.quiz-container');
                const stepKey = getStepKey(currentStepIndex);
                if (!container || !stepKey) return;

                container.dataset.overflowRisk = '0';
                applyDensity('normal');
                if (checkContentFits()) return;

                applyDensity('tight');
                if (checkContentFits()) return;

                applyDensity('compact');
                if (checkContentFits()) return;

                container.dataset.overflowRisk = '1';
            }

            function scheduleLayoutFit() {
                if (fitRaf) {
                    cancelAnimationFrame(fitRaf);
                }

                fitRaf = requestAnimationFrame(function () {
                    fitRaf = null;
                    fitNoScrollLayout();
                });
            }

            function restoreInputValues() {
                const stepKey = getStepKey(currentStepIndex);
                if (stepKey === 'nome' && answers.nome) {
                    document.getElementById('inputNome').value = answers.nome;
                }
                if (stepKey === 'whatsapp' && answers.whatsapp) {
                    document.getElementById('inputWhatsapp').value = formatPhoneDisplay(answers.whatsapp);
                }

                document.querySelectorAll('.option-btn').forEach(function (btn) {
                    btn.classList.remove('selected');
                });

                const currentStepEl = document.querySelector('.step.active');
                if (currentStepEl) {
                    const field = currentStepEl.querySelector('.option-btn')?.dataset.field;
                    if (field && answers[field]) {
                        const selected = currentStepEl.querySelector('[data-value="' + answers[field] + '"]');
                        if (selected) selected.classList.add('selected');
                    }
                }
            }

            function updateFooter() {
                const footer = document.getElementById('footerCta');
                const btn = document.getElementById('ctaBtn');
                const stepKey = getStepKey(currentStepIndex);

                footer.classList.remove('footer-cta--hidden');

                if (stepKey === 'welcome') {
                    btn.innerHTML = '<span>Começar diagnóstico</span>';
                    btn.disabled = false;
                    btn.onclick = function () { goToNext(); };
                } else if (stepKey === 'nome') {
                    btn.innerHTML = '<span>Continuar</span>';
                    btn.disabled = false;
                    btn.onclick = function () { validateAndNext('nome'); };
                } else if (stepKey === 'whatsapp') {
                    btn.innerHTML = '<span>Continuar</span>';
                    btn.disabled = false;
                    btn.onclick = function () { validateAndNext('whatsapp'); };
                } else if (stepKey === 'resultado') {
                    renderResultPreview();
                    btn.innerHTML = '<span>' + getResultCtaByScore(calculateScore()) + '</span>';
                    btn.disabled = false;
                    btn.onclick = function () { submitQuiz(); };
                } else if (stepKey && CONDITIONAL_STEPS[answers.dor_principal] === stepKey) {
                    btn.innerHTML = '<span>Continuar</span>';
                    btn.disabled = false;
                    btn.onclick = function () { goToNext(); };
                } else if (stepKey === 'urgencia') {
                    btn.innerHTML = '<span>Continuar</span>';
                    btn.disabled = false;
                    btn.onclick = function () { goToNext(); };
                } else {
                    footer.classList.add('footer-cta--hidden');
                }
            }

            function formatPhoneDisplay(value) {
                const digits = value.replace(/\D/g, '');
                if (digits.length <= 2) return digits.length ? '(' + digits : '';
                if (digits.length <= 7) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
                return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7, 11);
            }

            function bindEvents() {
                document.querySelectorAll('.option-btn').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const parent = this.closest('.step');
                        parent.querySelectorAll('.option-btn').forEach(function (b) { b.classList.remove('selected'); });
                        this.classList.add('selected');

                        const field = this.dataset.field;
                        const value = this.dataset.value;
                        answers[field] = value;

                        if (field === 'dor_principal' && CONDITIONAL_STEPS[value]) {
                            conditionalStep = CONDITIONAL_STEPS[value];
                        }

                        saveState();

                        setTimeout(function () {
                            goToNext();
                        }, motionDelay(360));
                    });
                });

                document.getElementById('inputNome').addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        validateAndNext('nome');
                    }
                });

                document.getElementById('inputWhatsapp').addEventListener('input', function () {
                    const pos = this.selectionStart;
                    const oldLen = this.value.length;
                    this.value = formatPhoneDisplay(this.value);
                    const newLen = this.value.length;
                    const newPos = pos + (newLen - oldLen);
                    this.setSelectionRange(newPos, newPos);
                    clearError('whatsapp');
                });

                document.getElementById('inputWhatsapp').addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        validateAndNext('whatsapp');
                    }
                });

                document.getElementById('inputNome').addEventListener('input', function () {
                    clearError('nome');
                });

                window.addEventListener('resize', scheduleLayoutFit);

                if (window.visualViewport) {
                    window.visualViewport.addEventListener('resize', scheduleLayoutFit);
                }
            }

            function clearError(field) {
                const errorEl = document.getElementById('error' + field.charAt(0).toUpperCase() + field.slice(1));
                const inputEl = document.getElementById('input' + field.charAt(0).toUpperCase() + field.slice(1));
                if (errorEl) errorEl.classList.remove('visible');
                if (inputEl) {
                    inputEl.classList.remove('input-error');
                    inputEl.setAttribute('aria-invalid', 'false');
                }
            }

            function showError(field) {
                const errorEl = document.getElementById('error' + field.charAt(0).toUpperCase() + field.slice(1));
                const inputEl = document.getElementById('input' + field.charAt(0).toUpperCase() + field.slice(1));
                if (errorEl) errorEl.classList.add('visible');
                if (inputEl) {
                    inputEl.classList.add('input-error');
                    inputEl.setAttribute('aria-invalid', 'true');
                }
            }

            function validateAndNext(field) {
                if (field === 'nome') {
                    const val = document.getElementById('inputNome').value.trim();
                    if (val.length < 2) {
                        showError('nome');
                        return;
                    }
                    answers.nome = val;
                    clearError('nome');
                } else if (field === 'whatsapp') {
                    const val = document.getElementById('inputWhatsapp').value.replace(/\D/g, '');
                    if (val.length < 10 || val.length > 11) {
                        showError('whatsapp');
                        return;
                    }
                    answers.whatsapp = val;
                    clearError('whatsapp');
                }
                saveState();
                goToNext();
            }

            function goToNext() {
                const currentEl = document.querySelector('.step.active');
                if (currentEl) {
                    currentEl.classList.add('exiting');
                    setTimeout(function () {
                        currentStepIndex++;
                        saveState();
                        renderStep();
                    }, motionDelay(250));
                } else {
                    currentStepIndex++;
                    saveState();
                    renderStep();
                }
            }

            function submitQuiz() {
                const btn = document.getElementById('ctaBtn');
                btn.disabled = true;
                btn.innerHTML = '<div class="spinner"></div><span>' + getFinalLoadingCopy() + '</span>';

                const payload = Object.assign({
                    session_id: sessionId,
                    nome: answers.nome || '',
                    whatsapp: answers.whatsapp || '',
                    cargo: answers.cargo || '',
                    faturamento: answers.faturamento || '',
                    canal: answers.canal || '',
                    volume_leads: answers.volume_leads || '',
                    dor_principal: answers.dor_principal || '',
                    dor_detalhe: answers.dor_detalhe || '',
                    timing: answers.timing || '',
                    current_step: TOTAL_STEPS,
                    client_user_agent: navigator.userAgent,
                    client_ip_address: '',
                    fbp: getCookie('_fbp'),
                    fbc: getCookie('_fbc'),
                }, getUTMParams());

                fetch('quiz-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            if (typeof fbq === 'function') {
                                fbq('track', 'Lead', {
                                    content_name: 'Quiz Comercial',
                                    score: data.score,
                                    classification: data.classificacao,
                                }, {
                                    eventID: 'quiz_' + sessionId,
                                });
                            }
                            renderResult(data);
                        } else {
                            btn.disabled = false;
                            btn.innerHTML = '<span>Tentar novamente</span>';
                            btn.onclick = function () { submitQuiz(); };
                        }
                    })
                    .catch(function () {
                        btn.disabled = false;
                        btn.innerHTML = '<span>Tentar novamente</span>';
                        btn.onclick = function () { submitQuiz(); };
                    });
            }

            function getCookie(name) {
                const value = '; ' + document.cookie;
                const parts = value.split('; ' + name + '=');
                if (parts.length === 2) {
                    return parts.pop().split(';').shift();
                }

                return '';
            }

            function renderResultPreview() {
                const badge = document.getElementById('resultBadge');
                const metrics = document.getElementById('resultMetrics');
                const insight = document.getElementById('resultInsight');
                const insightText = document.getElementById('resultInsightText');
                const scoreRing = document.getElementById('scoreRing');
                const scoreValue = document.getElementById('scoreValue');
                const scoreRingWrap = document.querySelector('.result-score-ring');
                const resultCard = document.getElementById('resultCard');

                const score = calculateScore();
                const scoreOffset = 264 - (264 * score / 100);

                badge.classList.remove('is-visible');
                metrics.classList.remove('is-visible');
                insight.classList.remove('is-visible');
                scoreRing.classList.remove('animate');
                if (scoreRingWrap) scoreRingWrap.classList.remove('is-live');
                scoreValue.classList.remove('is-live');
                scoreValue.classList.remove('is-counting', 'tick');
                if (resultCard) {
                    resultCard.classList.remove('result-enter');
                }

                scoreRing.style.setProperty('--score-offset', scoreOffset + 'px');
                scoreRing.style.strokeDashoffset = '264';
                scoreValue.textContent = '0';

                requestAnimationFrame(function () {
                    if (resultCard) {
                        resultCard.classList.add('result-enter');
                    }
                    if (scoreRingWrap) scoreRingWrap.classList.add('is-live');
                    scoreValue.classList.add('is-live');
                    scoreRing.classList.add('animate');
                    animateScoreRingAndValue(scoreRing, scoreValue, score, motionDelay(1200));
                });

                const isHot = score >= 70;
                const isWarm = score >= 40 && score < 70;

                let badgeClass = 'result-badge--cold';
                let badgeText = 'Em análise';
                if (isHot) {
                    badgeClass = 'result-badge--hot';
                    badgeText = 'Alta prioridade';
                } else if (isWarm) {
                    badgeClass = 'result-badge--warm';
                    badgeText = 'Oportunidade identificada';
                }

                badge.innerHTML = '<span class="result-badge ' + badgeClass + '">' + badgeText + '</span>';

                const canalLabels = {
                    'whatsapp_direto': 'WhatsApp',
                    'instagram_whatsapp': 'Instagram',
                    'trafego_pago': 'Tráfego pago',
                    'indicacao': 'Indicação',
                    'prospeccao_ativa': 'Prospecção',
                    'varios_canais': 'Multicanal',
                };

                const dorLabels = {
                    'atendimento_lento': 'Atendimento lento',
                    'fora_horario': 'Fora do horário',
                    'falta_followup': 'Sem follow-up',
                    'prospeccao_inconsistente': 'Prospecção fraca',
                    'converte_mal': 'Baixa conversão',
                    'organizacao_baguncada': 'Desorganização',
                };

                metrics.innerHTML =
                    '<div class="result-metric result-metric-enter">' +
                        '<div class="result-metric-icon">' + getVolumeIcon() + '</div>' +
                        '<div class="result-metric-value">' + getVolumeDisplay() + '</div>' +
                        '<div class="result-metric-label">Leads/semana</div>' +
                    '</div>' +
                    '<div class="result-metric result-metric-enter">' +
                        '<div class="result-metric-icon">' + getCanalIcon() + '</div>' +
                        '<div class="result-metric-value">' + (canalLabels[answers.canal] || '—') + '</div>' +
                        '<div class="result-metric-label">Canal principal</div>' +
                    '</div>' +
                    '<div class="result-metric result-metric-enter">' +
                        '<div class="result-metric-icon">' + getFatIcon() + '</div>' +
                        '<div class="result-metric-value">' + getFatDisplay() + '</div>' +
                        '<div class="result-metric-label">Faturamento</div>' +
                    '</div>' +
                    '<div class="result-metric result-metric-enter">' +
                        '<div class="result-metric-icon">' + getDorIcon() + '</div>' +
                        '<div class="result-metric-value">' + (dorLabels[answers.dor_principal] || '—') + '</div>' +
                        '<div class="result-metric-label">Ponto de atenção</div>' +
                    '</div>';

                revealResultPhases([badge, metrics, insight]);

                if (score >= 70) {
                    insightText.textContent = 'Seu cenário tem alto potencial de ganho com automação. No WhatsApp, vamos te mostrar o plano ideal para acelerar resultados.';
                } else if (score >= 40) {
                    insightText.textContent = 'Você já tem sinais claros de oportunidade. No WhatsApp, vamos indicar o melhor próximo passo para o seu momento.';
                } else {
                    insightText.textContent = 'Seu cenário pede ajustes pontuais. No WhatsApp, mostramos prioridades práticas para gerar ganho sem complexidade.';
                }

                const footer = document.getElementById('footerCta');
                const btn = document.getElementById('ctaBtn');
                btn.innerHTML = '<span>' + getResultCtaByScore(score) + '</span>';
                btn.disabled = false;
                btn.onclick = function () { submitQuiz(); };

                const existingRefazer = footer.querySelector('.btn-refazer');
                if (!existingRefazer) {
                    const refazerBtn = document.createElement('button');
                    refazerBtn.className = 'btn-refazer';
                    refazerBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg> Refazer diagnóstico';
                    refazerBtn.onclick = function () { resetQuiz(); };
                    footer.appendChild(refazerBtn);
                }

                scheduleLayoutFit();
            }

            function renderResult(data) {
                const badge = document.getElementById('resultBadge');

                const isHot = data.classificacao === 'quente';
                const isWarm = data.classificacao === 'morno';

                let badgeClass = 'result-badge--cold';
                let badgeText = 'Em análise';
                if (isHot) {
                    badgeClass = 'result-badge--hot';
                    badgeText = 'Alta prioridade';
                } else if (isWarm) {
                    badgeClass = 'result-badge--warm';
                    badgeText = 'Oportunidade identificada';
                }

                badge.innerHTML = '<span class="result-badge ' + badgeClass + '">' + badgeText + '</span>';

                const btn = document.getElementById('ctaBtn');
                btn.innerHTML = '<div class="spinner"></div>';
                btn.disabled = true;

                const spinner = btn.querySelector('.spinner');
                onAnimationEnd(spinner, function () {
                    btn.innerHTML = '<span>' + getResultCtaByClassificacao(data.classificacao) + '</span>';
                    btn.disabled = false;
                    scheduleLayoutFit();
                }, 1500);
            }

            function getFinalLoadingCopy() {
                const byDor = {
                    atendimento_lento: 'Calculando tempo de resposta ideal...',
                    fora_horario: 'Analisando oportunidades fora do horario...',
                    falta_followup: 'Projetando cadencia de follow-up...',
                    prospeccao_inconsistente: 'Avaliando previsibilidade de prospeccao...',
                    converte_mal: 'Mapeando gargalos de conversao...',
                    organizacao_baguncada: 'Organizando trilha ideal de atendimento...',
                };

                if (answers.dor_principal && byDor[answers.dor_principal]) {
                    return byDor[answers.dor_principal];
                }

                return 'Analisando seu cenario...';
            }

            function calculateScore() {
                let score = 20;

                const fatMap = { 'ate_10k': 10, '10k_20k': 20, '20k_50k': 30, '50k_100k': 40, 'acima_100k': 50 };
                score += fatMap[answers.faturamento] || 10;

                const volumeMap = { '0_10': 5, '11_30': 15, '31_100': 25, '100_mais': 35 };
                score += volumeMap[answers.volume_leads] || 5;

                const dorImpact = ['atendimento_lento', 'fora_horario', 'falta_followup'].includes(answers.dor_principal) ? 15 : 10;
                score += dorImpact;

                const timingMap = { 'agora': 15, 'este_mes': 10, 'proximo_mes': 5, 'entendendo': 0 };
                score += timingMap[answers.timing] || 5;

                return Math.min(Math.max(score, 15), 98);
            }

            function animateScore(from, to, duration) {
                const el = document.getElementById('scoreValue');
                if (!el) return;

                if (prefersReducedMotion || duration <= 0) {
                    el.textContent = String(Math.round(to));
                    return;
                }

                const start = performance.now();
                function tick(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.round(from + (to - from) * eased);
                    el.textContent = current;
                    if (progress < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
            }

            function animateScoreRingAndValue(ringEl, valueEl, score, duration) {
                if (!ringEl || !valueEl) return;

                const startOffset = 264;
                const targetOffset = 264 - (264 * score / 100);
                const finalScore = Math.max(0, Math.min(100, Math.round(score)));

                if (prefersReducedMotion || duration <= 0) {
                    ringEl.style.strokeDashoffset = String(targetOffset);
                    valueEl.textContent = String(finalScore);
                    return;
                }

                valueEl.textContent = '0';
                valueEl.classList.add('is-counting');

                const start = performance.now();
                let lastTickValue = -1;
                function springEase(t) {
                    const p = Math.min(Math.max(t, 0), 1);
                    return 1 - Math.pow(1 - p, 3) + (Math.sin(p * Math.PI * 2.2) * (1 - p) * 0.06);
                }

                function valueEase(t) {
                    const p = Math.min(Math.max(t, 0), 1);
                    return 1 - Math.pow(1 - p, 2.2);
                }

                function tick(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const ringProgress = springEase(progress);
                    const countProgress = valueEase(progress);
                    const currentOffset = startOffset + (targetOffset - startOffset) * ringProgress;
                    const currentValue = Math.max(0, Math.min(100, Math.floor(finalScore * countProgress)));

                    ringEl.style.strokeDashoffset = String(currentOffset);
                    valueEl.textContent = String(currentValue);

                    if (currentValue !== lastTickValue) {
                        lastTickValue = currentValue;
                        valueEl.classList.remove('tick');
                        requestAnimationFrame(function () {
                            valueEl.classList.add('tick');
                        });
                    }

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        ringEl.style.strokeDashoffset = String(targetOffset);
                        valueEl.textContent = String(finalScore);
                        valueEl.classList.remove('is-counting');
                    }
                }

                requestAnimationFrame(tick);
            }

            function getVolumeIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a2 2 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
            }

            function getVolumeDisplay() {
                const map = { '0_10': '0-10', '11_30': '11-30', '31_100': '31-100', '100_mais': '100+' };
                return map[answers.volume_leads] || '—';
            }

            function getCanalIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
            }

            function getFatIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>';
            }

            function getFatDisplay() {
                const map = { 'ate_10k': 'Até 10k', '10k_20k': '10-20k', '20k_50k': '20-50k', '50k_100k': '50-100k', 'acima_100k': '100k+' };
                return map[answers.faturamento] || '—';
            }

            function getDorIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            }

            function resetQuiz() {
                localStorage.removeItem('chamalead_quiz');
                answers = {};
                conditionalStep = null;
                currentStepIndex = 0;
                sessionId = generateSessionId();
                saveState();

                document.querySelectorAll('.option-btn').forEach(function (btn) {
                    btn.classList.remove('selected');
                });
                document.getElementById('inputNome').value = '';
                document.getElementById('inputWhatsapp').value = '';

                const refazerBtn = document.querySelector('.btn-refazer');
                if (refazerBtn) refazerBtn.remove();

                renderStep();
            }

            function getFaturamentoValor(key) {
                var map = {
                    'ate_10k': 10000,
                    '10k_20k': 20000,
                    '20k_50k': 50000,
                    '50k_100k': 100000,
                    'acima_100k': 150000,
                };
                return map[key] || 0;
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</body>
</html>
