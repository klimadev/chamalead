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
                <div class="phone-validation-container">
                    <div class="input-wrap">
                        <label class="input-label" for="inputWhatsapp">WhatsApp com DDD</label>
                        <input type="tel" class="input-field" id="inputWhatsapp" placeholder="(11) 99999-9999" autocomplete="tel" maxlength="15" aria-describedby="errorWhatsapp">
                        <p class="error-msg" id="errorWhatsapp">Digite um WhatsApp valido com DDD. Ex.: (11) 99999-9999</p>
                    </div>
                    <div class="phone-badges" id="phoneBadges">
                        <div class="phone-badge phone-badge--state hidden" id="badgeState"></div>
                        <div class="phone-badge phone-badge--carrier hidden" id="badgeCarrier"></div>
                    </div>
                    <div class="phone-validation-status" id="phoneValidationStatus"></div>
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
                    <h2 class="step-headline">Onde você sente que mais <span class="accent">perde oportunidades</span>? (múltipla escolha)</h2>
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
