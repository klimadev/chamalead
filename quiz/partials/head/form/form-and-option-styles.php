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
