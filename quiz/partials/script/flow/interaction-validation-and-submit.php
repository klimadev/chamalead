                if (stepKey === 'welcome') {
                    btn.innerHTML = '<span>Começar diagnóstico</span>';
                    btn.disabled = false;
                    btn.onclick = function () { goToNext(); };
                } else if (stepKey === 'nome') {
                    btn.innerHTML = '<span>Continuar</span>';
                    btn.disabled = false;
                    btn.onclick = function () { validateAndNext('nome'); };
                 } else if (stepKey === 'whatsapp') {
                     const phoneDigits = document.getElementById('inputWhatsapp').value.replace(/\D/g, '');
                     btn.innerHTML = '<span>Continuar</span>';
                     btn.disabled = phoneDigits.length !== 11 || !phoneValidated;
                     btn.onclick = function () {
                         validateAndNext('whatsapp');
                     };
                } else if (stepKey === 'resultado') {
                    renderResultPreview();
                    btn.innerHTML = '<span>' + getResultCtaByScore(calculateScore()) + '</span>';
                    btn.disabled = false;
                    btn.onclick = function () { submitQuiz(); };
                } else if (stepKey && CONDITIONAL_STEPS[getPrimaryDor()] === stepKey) {
                    btn.innerHTML = '<span>Continuar</span>';
                    btn.disabled = false;
                    btn.onclick = function () { goToNext(); };
                } else if (stepKey === 'dor') {
                    btn.innerHTML = '<span>Continuar</span>';
                    const selectedValues = Array.isArray(answers.dor_principal)
                        ? answers.dor_principal
                        : (answers.dor_principal ? [answers.dor_principal] : []);
                    btn.disabled = selectedValues.length === 0;
                    btn.onclick = function () {
                        if (!btn.disabled) {
                            goToNext();
                        }
                    };
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
                        const field = this.dataset.field;
                        const value = this.dataset.value;

                        if (field === 'dor_principal') {
                            const selectedValues = Array.isArray(answers.dor_principal)
                                ? answers.dor_principal.slice()
                                : (answers.dor_principal ? [answers.dor_principal] : []);
                            const valueIndex = selectedValues.indexOf(value);

                            if (valueIndex >= 0) {
                                selectedValues.splice(valueIndex, 1);
                                this.classList.remove('selected');
                            } else {
                                selectedValues.push(value);
                                this.classList.add('selected');
                            }

                            answers.dor_principal = selectedValues;
                            const primaryDor = getPrimaryDor();
                            conditionalStep = primaryDor && CONDITIONAL_STEPS[primaryDor]
                                ? CONDITIONAL_STEPS[primaryDor]
                                : null;

                            saveState();
                            updateFooter();
                            return;
                        }

                        parent.querySelectorAll('.option-btn').forEach(function (b) { b.classList.remove('selected'); });
                        this.classList.add('selected');
                        answers[field] = value;

                        if (field === 'dor_detalhe') {
                            conditionalStep = null;
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
                    clearPhoneValidationState();
                    debouncePhoneValidation();
                    updateWhatsappButtonState();
                });

                document.getElementById('inputWhatsapp').addEventListener('blur', function () {
                    const digits = this.value.replace(/\D/g, '');
                    if (digits.length >= 10) {
                        validatePhoneNow(digits);
                    }
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
                    if (!phoneValidated) {
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
                 
                 currentStepIndex++;
                 saveState();
                 renderStep();
                 
                 if (currentEl) {
                     currentEl.classList.add('exiting');
                 }
             }

            let phoneValidationTimeout = null;
            let phoneValidationController = null;
            let phoneValidated = false;
            let lastValidatedPhone = '';

             function debouncePhoneValidation() {
                 clearTimeout(phoneValidationTimeout);
                 if (phoneValidationController) {
                     phoneValidationController.abort();
                 }
                 phoneValidationTimeout = setTimeout(function () {
                     const digits = document.getElementById('inputWhatsapp').value.replace(/\D/g, '');
                     if (digits.length >= 10) {
                         validatePhoneNow(digits);
                     } else {
                         clearPhoneValidationState();
                     }
                     updateWhatsappButtonState();
                 }, 600);
             }

            function clearPhoneValidationState() {
                phoneValidated = false;
                lastValidatedPhone = '';
                const badgeState = document.getElementById('badgeState');
                const badgeCarrier = document.getElementById('badgeCarrier');
                const statusEl = document.getElementById('phoneValidationStatus');
                if (badgeState) badgeState.classList.add('hidden');
                if (badgeCarrier) badgeCarrier.classList.add('hidden');
                if (statusEl) statusEl.innerHTML = '';
                updateWhatsappButtonState();
            }

            function validatePhoneNow(phone) {
                if (phone === lastValidatedPhone && phoneValidated) {
                    return;
                }

                if (phoneValidationController) {
                    phoneValidationController.abort();
                }

                phoneValidationController = new AbortController();
                const signal = phoneValidationController.signal;

                const statusEl = document.getElementById('phoneValidationStatus');
                if (statusEl) {
                    statusEl.innerHTML = '<div class="validation-badge validation-badge--loading"><div class="spinner-small"></div><span>Verificando...</span></div>';
                }

                const controller = phoneValidationController;

                const timeoutId = setTimeout(function () {
                    controller.abort();
                }, 5000);

                fetch('api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'validate-phone',
                        session_id: sessionId,
                        phone: phone,
                    }),
                    signal: signal,
                })
                    .then(function (res) {
                        clearTimeout(timeoutId);
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        if (signal.aborted) return;

                        lastValidatedPhone = phone;
                        const badgeState = document.getElementById('badgeState');
                        const badgeCarrier = document.getElementById('badgeCarrier');

                        if (data.valid) {
                            phoneValidated = true;
                            if (statusEl) {
                                statusEl.innerHTML = '<div class="validation-badge validation-badge--valid">✓ Número válido</div>';
                            }
                            if (badgeState && data.state) {
                                badgeState.textContent = data.state;
                                badgeState.classList.remove('hidden');
                            }
                            if (badgeCarrier && data.carrier) {
                                badgeCarrier.textContent = data.carrier;
                                badgeCarrier.classList.remove('hidden');
                            }
                        } else {
                            phoneValidated = false;
                            if (statusEl) {
                                statusEl.innerHTML = '<div class="validation-badge validation-badge--invalid">✗ ' + (data.error || 'Número inválido') + '</div>';
                            }
                            if (badgeState && data.state) {
                                badgeState.textContent = data.state;
                                badgeState.classList.remove('hidden');
                            }
                            if (badgeCarrier) badgeCarrier.classList.add('hidden');
                        }

                        updateWhatsappButtonState();
                    })
                    .catch(function (err) {
                        clearTimeout(timeoutId);

                        // 🔥 ANTES DE QUALQUER COISA, LIMPA O LOADING. NUNCA FICA INFINITO.
                        if (statusEl) {
                            statusEl.innerHTML = '';
                        }

                        const isNetworkError = !navigator.onLine || err.message.includes('network') || err.message.includes('fetch');

                        if (signal.aborted || err.name === 'AbortError') {
                            const wasExplicitAbort = phone !== lastValidatedPhone && phoneValidationController;
                            if (wasExplicitAbort) {
                                return;
                            }
                        }

                        phoneValidated = false;
                        lastValidatedPhone = '';

                        const digits = phone.replace(/\D/g, '');
                        if (digits.length === 11) {
                            phoneValidated = true;
                            const state = getStateFromDDDFallback(digits.substring(0, 2));
                            const carrier = getCarrierFromDigitsFallback(digits);

                            const badgeState = document.getElementById('badgeState');
                            const badgeCarrier = document.getElementById('badgeCarrier');

                            if (statusEl) {
                                statusEl.innerHTML = '<div class="validation-badge validation-badge--valid">✓ Número válido (offline)</div>';
                            }
                            if (badgeState && state) {
                                badgeState.textContent = state;
                                badgeState.classList.remove('hidden');
                            }
                            if (badgeCarrier && carrier) {
                                badgeCarrier.textContent = carrier;
                                badgeCarrier.classList.remove('hidden');
                            }
                        } else {
                            if (statusEl) {
                                statusEl.innerHTML = '<div class="validation-badge validation-badge--invalid">✗ Número inválido</div>';
                            }
                        }

                        updateWhatsappButtonState();
                    });
            }

            function getStateFromDDDFallback(ddd) {
                const dddToState = {
                    '11': 'SP', '12': 'SP', '13': 'SP', '14': 'SP', '15': 'SP',
                    '16': 'SP', '17': 'SP', '18': 'SP', '19': 'SP',
                    '21': 'RJ', '22': 'RJ', '24': 'RJ',
                    '31': 'MG', '32': 'MG', '33': 'MG', '34': 'MG', '35': 'MG',
                    '36': 'MG', '37': 'MG', '38': 'MG',
                    '41': 'PR', '42': 'PR', '43': 'PR', '44': 'PR', '45': 'PR', '46': 'PR',
                    '47': 'SC', '48': 'SC', '49': 'SC',
                    '51': 'RS', '52': 'RS', '53': 'RS', '54': 'RS', '55': 'RS',
                    '61': 'DF', '62': 'GO', '63': 'GO', '64': 'GO',
                    '65': 'MT', '66': 'MT', '67': 'MT', '68': 'MT',
                    '69': 'RO', '71': 'BA', '73': 'BA', '74': 'BA', '75': 'BA', '77': 'BA',
                    '79': 'PI', '81': 'PE', '82': 'PB', '84': 'RN', '85': 'CE', '86': 'CE', '87': 'CE',
                    '88': 'CE', '89': 'PI', '91': 'PA', '92': 'AM', '93': 'AM', '94': 'PA',
                    '95': 'PA', '96': 'PA', '97': 'AM', '98': 'AM', '99': 'AM',
                };
                return dddToState[ddd] || '';
            }

            function getCarrierFromDigitsFallback(digits) {
                if (digits.length < 4) return 'Desconhecida';
                const prefix = digits.substring(0, 4);
                const carrierPrefixes = {
                    'Vivo': ['9191', '9192', '9193', '9194', '9195', '9196', '9197', '9198', '9199', '2191', '2192', '2193', '2194', '4191', '4192', '4193', '5191', '5192', '5193', '6191', '6192', '7191', '7192', '8191', '8192'],
                    'Claro': ['2195', '2196', '2197', '2198', '2199', '4194', '4195', '4196', '4197', '4198', '4199', '5194', '5195', '5196', '5197', '5198', '5199', '6193', '6194', '6195', '6196', '6197', '7193', '7194', '7195', '7196', '7197', '8193', '8194', '8195', '8196'],
                    'TIM': ['2191', '2192', '2193', '4195', '4196', '5191', '5192', '6191', '6192', '7191', '8191', '8192', '8193', '9194', '9195', '9196', '9197'],
                    'Oi': ['3191', '3192', '3193', '3194', '3195', '3196', '3197', '3198', '3199', '2194', '2195', '5193', '5194', '5195', '5196', '6195', '6196', '7198', '7199', '8197', '8198', '8199', '9191', '9192', '9193'],
                };

                for (const carrier in carrierPrefixes) {
                    if (carrierPrefixes[carrier].includes(prefix)) {
                        return carrier;
                    }
                }
                return 'Desconhecida';
            }

function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

             function updateWhatsappButtonState() {
                 const phoneDigits = document.getElementById('inputWhatsapp').value.replace(/\D/g, '');
                 const btn = document.getElementById('ctaBtn');
                 if (btn) {
                     const currentStepKey = window.stepKey;
                     if (currentStepKey === 'whatsapp') {
                         btn.disabled = phoneDigits.length !== 11 || !phoneValidated;
                     }
                 }
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
