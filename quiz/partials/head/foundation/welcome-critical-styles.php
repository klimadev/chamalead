        .cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px 40px;
            min-width: 200px;
            border: none;
            border-radius: 100px;
            background: linear-gradient(135deg, var(--flame-600), var(--ember-600));
            box-shadow: 0 8px 22px rgba(249, 115, 22, 0.22);
            color: #ffffff;
            cursor: pointer;
            font-family: var(--ff-display);
            font-size: var(--type-cta-size);
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: 0.01em;
            transition: transform 0.24s var(--spring-soft), box-shadow 0.28s var(--spring-soft), filter 0.24s ease;
        }

        .cta-btn span {
            position: relative;
            z-index: 1;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            filter: saturate(1.04) brightness(1.02);
            box-shadow: 0 14px 30px rgba(249, 115, 22, 0.28);
        }

        .cta-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.24), 0 10px 34px rgba(249, 115, 22, 0.26);
        }

        .cta-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .footer-cta {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: center;
            padding: 20px 24px 32px;
            background: transparent;
            flex-shrink: 0;
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
            color: rgba(255, 255, 255, 0.58);
            font-size: var(--type-body-sm-size);
            line-height: 1.55;
        }

        .welcome-feature-dot {
            width: 4px;
            height: 4px;
            flex-shrink: 0;
            border-radius: 50%;
            background: rgba(249, 115, 22, 0.68);
            box-shadow: 0 0 8px rgba(249, 115, 22, 0.24);
        }

        @media (min-width: 640px) {
            .quiz-content,
            .quiz-header,
            .footer-cta {
                width: 100%;
                max-width: 560px;
                margin: 0 auto;
            }
        }

        @media (min-width: 1024px) {
            .quiz-content,
            .quiz-header,
            .footer-cta {
                max-width: 640px;
            }
        }

        @media (max-width: 420px) {
            :root {
                --type-headline-size: clamp(22px, 7.2vw, 30px);
                --type-body-md-size: 14px;
                --type-body-md-lh: 1.55;
                --type-cta-size: 15px;
                --type-progress-size: 11px;
                --type-progress-stage-size: 10px;
            }

            .quiz-content {
                padding: 18px;
            }

            .step.active {
                gap: 18px;
            }

            .footer-cta {
                padding: 16px 18px 26px;
            }

            .cta-btn {
                width: 100%;
                min-width: 100%;
                padding: 15px 20px;
            }
        }
    </style>

    <link rel="preload" href="<?= htmlspecialchars(quiz_asset_url('assets/quiz-deferred.css'), ENT_QUOTES, 'UTF-8') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="<?= htmlspecialchars(quiz_asset_url('assets/quiz-deferred.css'), ENT_QUOTES, 'UTF-8') ?>">
    </noscript>
</head>
