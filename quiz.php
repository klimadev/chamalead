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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'chama': {
                            bg: '#0b0710',
                            surface: '#140e1e',
                            surfaceAlt: '#1c1428',
                            border: '#2a1f3a',
                        },
                        'coral': {
                            400: '#f97066',
                            500: '#f25c5c',
                            600: '#e04545',
                            700: '#c73030',
                        },
                        'wine': {
                            500: '#9b2c4a',
                            600: '#7a2040',
                            700: '#5c1830',
                        }
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
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0b0710;
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
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(155, 44, 74, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 90% 10%, rgba(242, 92, 92, 0.05) 0%, transparent 50%),
                #0b0710;
        }

        .quiz-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 0% 100%, rgba(242, 92, 92, 0.06) 0%, transparent 40%),
                radial-gradient(circle at 100% 0%, rgba(155, 44, 74, 0.04) 0%, transparent 40%);
        }

        .quiz-bg::after {
            content: '';
            position: absolute;
            top: -1px;
            right: -1px;
            width: 300px;
            height: 300px;
            border: 1px solid rgba(242, 92, 92, 0.06);
            border-radius: 50%;
            opacity: 0.4;
        }

        .corner-line {
            position: absolute;
            z-index: 0;
        }

        .corner-line--tl {
            top: 60px;
            left: 0;
            width: 120px;
            height: 1px;
            background: linear-gradient(90deg, rgba(242, 92, 92, 0.15), transparent);
        }

        .corner-line--br {
            bottom: 80px;
            right: 0;
            width: 160px;
            height: 1px;
            background: linear-gradient(270deg, rgba(155, 44, 74, 0.12), transparent);
        }

        .corner-line--bl {
            bottom: 0;
            left: 40px;
            width: 1px;
            height: 80px;
            background: linear-gradient(0deg, rgba(242, 92, 92, 0.1), transparent);
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
            font-family: 'Space Grotesk', sans-serif;
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
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #f25c5c, #9b2c4a);
            border-radius: 2px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            width: 0%;
        }

        .quiz-content {
            position: relative;
            z-index: 10;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 24px;
            overflow-y: auto;
            min-height: 0;
        }

        .step {
            display: none;
            animation: stepIn 0.35s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .step.active {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .step.exiting {
            animation: stepOut 0.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes stepIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes stepOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-12px); }
        }

        .step-number {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(242, 92, 92, 0.6);
        }

        .step-headline {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(24px, 5vw, 36px);
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #ffffff;
        }

        .step-headline .accent {
            background: linear-gradient(135deg, #f25c5c, #9b2c4a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .step-sub {
            font-size: 15px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.5);
            max-width: 480px;
        }

        .options-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
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
            font-size: 15px;
            font-weight: 400;
            line-height: 1.4;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .option-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(242, 92, 92, 0.08), rgba(155, 44, 74, 0.04));
            opacity: 0;
            transition: opacity 0.2s;
        }

        .option-btn:hover {
            border-color: rgba(242, 92, 92, 0.3);
            transform: translateY(-1px);
        }

        .option-btn:hover::before {
            opacity: 1;
        }

        .option-btn:active {
            transform: scale(0.98);
        }

        .option-btn.selected {
            border-color: rgba(242, 92, 92, 0.5);
            background: rgba(242, 92, 92, 0.08);
            color: #ffffff;
        }

        .option-btn.selected::before {
            opacity: 1;
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
            transition: all 0.2s;
            position: relative;
            z-index: 1;
        }

        .option-btn.selected .option-icon {
            border-color: #f25c5c;
            background: #f25c5c;
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
        }

        .input-field {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-field::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .input-field:focus {
            border-color: rgba(242, 92, 92, 0.5);
            box-shadow: 0 0 0 3px rgba(242, 92, 92, 0.08);
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 40px;
            background: linear-gradient(135deg, #f25c5c, #9b2c4a);
            color: #ffffff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.01em;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }

        .cta-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #f97066, #c73030);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(242, 92, 92, 0.25);
        }

        .cta-btn:hover::before {
            opacity: 1;
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
        }

        .footer-cta--hidden {
            display: none;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 24px;
            margin-top: 8px;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 28px;
            margin-top: 8px;
            position: relative;
            overflow: hidden;
        }

        .result-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #f25c5c, #9b2c4a, #f25c5c);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .result-score-ring {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
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
            transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .result-score-ring-fill.animate {
            stroke-dashoffset: var(--score-offset);
        }

        .result-score-value {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
        }

        .result-score-label {
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            text-align: center;
            margin-top: -8px;
            margin-bottom: 16px;
        }

        .result-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
            margin-bottom: 16px;
        }

        .result-metric {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 14px;
            text-align: center;
        }

        .result-metric-icon {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .result-metric-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .result-metric-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
            line-height: 1.3;
        }

        .result-insight {
            background: linear-gradient(135deg, rgba(242, 92, 92, 0.08), rgba(155, 44, 74, 0.05));
            border: 1px solid rgba(242, 92, 92, 0.15);
            border-radius: 12px;
            padding: 14px 16px;
            margin-top: 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
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
            font-size: 13px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.7);
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
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
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
        }

        .result-badge--hot {
            background: rgba(242, 92, 92, 0.12);
            color: #f25c5c;
            border: 1px solid rgba(242, 92, 92, 0.2);
        }

        .result-badge--warm {
            background: rgba(242, 92, 92, 0.08);
            color: #f97066;
            border: 1px solid rgba(242, 92, 92, 0.15);
        }

        .result-badge--cold {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .result-text {
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 12px;
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
            gap: 12px;
            margin-top: 8px;
        }

        .welcome-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
        }

        .welcome-feature-dot {
            width: 4px;
            height: 4px;
            background: rgba(242, 92, 92, 0.5);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .input-error {
            border-color: rgba(242, 92, 92, 0.5) !important;
            box-shadow: 0 0 0 3px rgba(242, 92, 92, 0.1) !important;
        }

        .error-msg {
            font-size: 12px;
            color: #f25c5c;
            margin-top: -12px;
            display: none;
        }

        .error-msg.visible {
            display: block;
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
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-bg"></div>

        <div class="corner-line corner-line--tl"></div>
        <div class="corner-line corner-line--br"></div>
        <div class="corner-line corner-line--bl"></div>

        <header class="quiz-header">
            <div class="logo">
                <img src="logo_new.png" alt="ChamaLead">
                <span class="logo-text">CHAMALEAD</span>
            </div>
            <div class="progress-track" id="progressTrack" style="display: none;">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </header>

        <main class="quiz-content" id="quizContent">

            <div class="step active" data-step="welcome">
                <div>
                    <p class="step-number">Diagnóstico rápido</p>
                    <h1 class="step-headline">
                        Descubra em 2 minutos se sua operação está <span class="accent">perdendo leads</span> todos os dias
                    </h1>
                    <p class="step-sub" style="margin-top: 16px;">
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
                <div>
                    <input type="text" class="input-field" id="inputNome" placeholder="Seu nome" autocomplete="given-name" maxlength="120">
                    <p class="error-msg" id="errorNome">Por favor, informe seu nome</p>
                </div>
            </div>

            <div class="step" data-step="whatsapp">
                <div>
                    <p class="step-number">Etapa 2 de 10</p>
                    <h2 class="step-headline">Qual é o seu melhor WhatsApp <span class="accent">com DDD</span>?</h2>
                </div>
                <div>
                    <input type="tel" class="input-field" id="inputWhatsapp" placeholder="(11) 99999-9999" autocomplete="tel" maxlength="15">
                    <p class="error-msg" id="errorWhatsapp">Número de WhatsApp inválido</p>
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
                                        <stop offset="0%" stop-color="#f25c5c" />
                                        <stop offset="100%" stop-color="#9b2c4a" />
                                    </linearGradient>
                                </defs>
                                <circle class="result-score-ring-bg" cx="48" cy="48" r="42" />
                                <circle class="result-score-ring-fill" id="scoreRing" cx="48" cy="48" r="42" />
                            </svg>
                            <div class="result-score-value" id="scoreValue">0</div>
                        </div>
                        <p class="result-score-label">Potencial de automação</p>
                        <div id="resultBadge"></div>
                        <div class="result-metrics" id="resultMetrics"></div>
                        <div class="result-insight" id="resultInsight">
                            <div class="result-insight-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f25c5c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                renderStep();
                bindEvents();
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

                const progressTrack = document.getElementById('progressTrack');
                const progressFill = document.getElementById('progressFill');

                if (currentStepIndex === 0) {
                    progressTrack.style.display = 'none';
                } else {
                    progressTrack.style.display = '';
                    progressFill.style.width = getProgress() + '%';
                }

                updateFooter();
                restoreInputValues();
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
                    btn.innerHTML = '<span>Quero ser chamado agora</span>';
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
                        }, 300);
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
            }

            function clearError(field) {
                const errorEl = document.getElementById('error' + field.charAt(0).toUpperCase() + field.slice(1));
                const inputEl = document.getElementById('input' + field.charAt(0).toUpperCase() + field.slice(1));
                if (errorEl) errorEl.classList.remove('visible');
                if (inputEl) inputEl.classList.remove('input-error');
            }

            function showError(field) {
                const errorEl = document.getElementById('error' + field.charAt(0).toUpperCase() + field.slice(1));
                const inputEl = document.getElementById('input' + field.charAt(0).toUpperCase() + field.slice(1));
                if (errorEl) errorEl.classList.add('visible');
                if (inputEl) inputEl.classList.add('input-error');
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
                    }, 180);
                } else {
                    currentStepIndex++;
                    saveState();
                    renderStep();
                }
            }

            function submitQuiz() {
                const btn = document.getElementById('ctaBtn');
                btn.disabled = true;
                btn.innerHTML = '<div class="spinner"></div>';

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
                const insightText = document.getElementById('resultInsightText');
                const scoreRing = document.getElementById('scoreRing');
                const scoreValue = document.getElementById('scoreValue');

                const score = calculateScore();
                const scoreOffset = 264 - (264 * score / 100);

                scoreRing.style.setProperty('--score-offset', scoreOffset + 'px');
                scoreValue.textContent = '0';

                setTimeout(function () {
                    scoreRing.classList.add('animate');
                    animateScore(0, score, 1000);
                }, 100);

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

                if (score >= 70) {
                    insightText.textContent = 'Seu cenário indica forte potencial de ganho com automação comercial. Nossa IA vai continuar esse diagnóstico com você no WhatsApp e mostrar o caminho exato.';
                } else if (score >= 40) {
                    insightText.textContent = 'Você já tem sinais de oportunidade. Vale entender qual automação faria mais sentido no seu momento atual. Continue no WhatsApp.';
                } else {
                    insightText.textContent = 'Seu momento pode não exigir automação pesada agora, mas nossa IA pode ajudar a identificar exatamente o que faria diferença. Continue no WhatsApp.';
                }

                const footer = document.getElementById('footerCta');
                const btn = document.getElementById('ctaBtn');

                const existingRefazer = footer.querySelector('.btn-refazer');
                if (!existingRefazer) {
                    const refazerBtn = document.createElement('button');
                    refazerBtn.className = 'btn-refazer';
                    refazerBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg> Refazer diagnóstico';
                    refazerBtn.onclick = function () { resetQuiz(); };
                    footer.appendChild(refazerBtn);
                }
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
                setTimeout(function () {
                    btn.innerHTML = '<span>Pronto! Vamos te chamar em breve.</span>';
                }, 1500);
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

            function getVolumeIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(242,92,92,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a2 2 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
            }

            function getVolumeDisplay() {
                const map = { '0_10': '0-10', '11_30': '11-30', '31_100': '31-100', '100_mais': '100+' };
                return map[answers.volume_leads] || '—';
            }

            function getCanalIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(242,92,92,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
            }

            function getFatIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(242,92,92,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>';
            }

            function getFatDisplay() {
                const map = { 'ate_10k': 'Até 10k', '10k_20k': '10-20k', '20k_50k': '20-50k', '50k_100k': '50-100k', 'acima_100k': '100k+' };
                return map[answers.faturamento] || '—';
            }

            function getDorIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(242,92,92,0.7)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
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
