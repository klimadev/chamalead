
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
