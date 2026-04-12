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
