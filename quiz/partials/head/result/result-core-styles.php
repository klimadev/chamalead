
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
