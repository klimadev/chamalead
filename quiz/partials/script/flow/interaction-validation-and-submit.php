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

                fetch('api.php', {
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
