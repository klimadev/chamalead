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
