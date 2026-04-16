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
