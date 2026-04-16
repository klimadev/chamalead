                        '<div class="result-metric-icon">' + getFatIcon() + '</div>' +
                        '<div class="result-metric-value">' + getFatDisplay() + '</div>' +
                        '<div class="result-metric-label">Faturamento</div>' +
                    '</div>' +
                    '<div class="result-metric result-metric-enter">' +
                        '<div class="result-metric-icon">' + getDorIcon() + '</div>' +
                        '<div class="result-metric-value">' + (dorLabels[getPrimaryDor()] || '—') + '</div>' +
                        '<div class="result-metric-label">Ponto de atenção</div>' +
                    '</div>';

                revealResultPhases([badge, metrics, insight]);

                if (score >= 70) {
                    insightText.textContent = 'Seu cenário tem alto potencial de ganho com automação. No WhatsApp, vamos te mostrar o plano ideal para acelerar resultados.';
                } else if (score >= 40) {
                    insightText.textContent = 'Você já tem sinais claros de oportunidade. No WhatsApp, vamos indicar o melhor próximo passo para o seu momento.';
                } else {
                    insightText.textContent = 'Seu cenário pede ajustes pontuais. No WhatsApp, mostramos prioridades práticas para gerar ganho sem complexidade.';
                }

                const footer = document.getElementById('footerCta');
                const btn = document.getElementById('ctaBtn');
                btn.innerHTML = '<span>' + getResultCtaByScore(score) + '</span>';
                btn.disabled = false;
                btn.onclick = function () { submitQuiz(); };

                const existingRefazer = footer.querySelector('.btn-refazer');
                if (!existingRefazer) {
                    const refazerBtn = document.createElement('button');
                    refazerBtn.className = 'btn-refazer';
                    refazerBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg> Refazer diagnóstico';
                    refazerBtn.onclick = function () { resetQuiz(); };
                    footer.appendChild(refazerBtn);
                }

                scheduleLayoutFit();
            }

            function renderResult(data) {
                const badge = document.getElementById('resultBadge');

                const isHot = data.classificacao === 'quente';
                const isWarm = data.classificacao === 'morno';

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

                const btn = document.getElementById('ctaBtn');
                btn.innerHTML = '<div class="spinner"></div>';
                btn.disabled = true;

                const spinner = btn.querySelector('.spinner');
                onAnimationEnd(spinner, function () {
                    btn.innerHTML = '<span>' + getResultCtaByClassificacao(data.classificacao) + '</span>';
                    btn.disabled = false;
                    scheduleLayoutFit();
                }, 1500);
            }

            function getFinalLoadingCopy() {
                const byDor = {
                    atendimento_lento: 'Calculando tempo de resposta ideal...',
                    fora_horario: 'Analisando oportunidades fora do horario...',
                    falta_followup: 'Projetando cadencia de follow-up...',
                    prospeccao_inconsistente: 'Avaliando previsibilidade de prospeccao...',
                    converte_mal: 'Mapeando gargalos de conversao...',
                    organizacao_baguncada: 'Organizando trilha ideal de atendimento...',
                };

                const primaryDor = getPrimaryDor();
                if (primaryDor && byDor[primaryDor]) {
                    return byDor[primaryDor];
                }

                return 'Analisando seu cenario...';
            }

            function calculateScore() {
                let score = 20;

                const fatMap = { 'ate_10k': 10, '10k_20k': 20, '20k_50k': 30, '50k_100k': 40, 'acima_100k': 50 };
                score += fatMap[answers.faturamento] || 10;

                const volumeMap = { '0_10': 5, '11_30': 15, '31_100': 25, '100_mais': 35 };
                score += volumeMap[answers.volume_leads] || 5;

                const dorImpact = ['atendimento_lento', 'fora_horario', 'falta_followup'].includes(getPrimaryDor()) ? 15 : 10;
                score += dorImpact;

                const timingMap = { 'agora': 15, 'este_mes': 10, 'proximo_mes': 5, 'entendendo': 0 };
                score += timingMap[answers.timing] || 5;

                return Math.min(Math.max(score, 15), 98);
            }

            function animateScore(from, to, duration) {
                const el = document.getElementById('scoreValue');
                if (!el) return;

                if (prefersReducedMotion || duration <= 0) {
                    el.textContent = String(Math.round(to));
                    return;
                }

                const start = performance.now();
                function tick(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.round(from + (to - from) * eased);
                    el.textContent = current;
                    if (progress < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
            }

            function animateScoreRingAndValue(ringEl, valueEl, score, duration) {
                if (!ringEl || !valueEl) return;

                const startOffset = 264;
                const targetOffset = 264 - (264 * score / 100);
                const finalScore = Math.max(0, Math.min(100, Math.round(score)));

                if (prefersReducedMotion || duration <= 0) {
                    ringEl.style.strokeDashoffset = String(targetOffset);
                    valueEl.textContent = String(finalScore);
                    return;
                }

                valueEl.textContent = '0';
                valueEl.classList.add('is-counting');

                const start = performance.now();
                let lastTickValue = -1;
                function springEase(t) {
                    const p = Math.min(Math.max(t, 0), 1);
                    return 1 - Math.pow(1 - p, 3) + (Math.sin(p * Math.PI * 2.2) * (1 - p) * 0.06);
                }

                function valueEase(t) {
                    const p = Math.min(Math.max(t, 0), 1);
                    return 1 - Math.pow(1 - p, 2.2);
                }

                function tick(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const ringProgress = springEase(progress);
                    const countProgress = valueEase(progress);
                    const currentOffset = startOffset + (targetOffset - startOffset) * ringProgress;
                    const currentValue = Math.max(0, Math.min(100, Math.floor(finalScore * countProgress)));

                    ringEl.style.strokeDashoffset = String(currentOffset);
                    valueEl.textContent = String(currentValue);

                    if (currentValue !== lastTickValue) {
                        lastTickValue = currentValue;
                        valueEl.classList.remove('tick');
                        requestAnimationFrame(function () {
                            valueEl.classList.add('tick');
                        });
                    }

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        ringEl.style.strokeDashoffset = String(targetOffset);
                        valueEl.textContent = String(finalScore);
                        valueEl.classList.remove('is-counting');
                    }
                }

                requestAnimationFrame(tick);
            }

            function getVolumeIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a2 2 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
            }

            function getVolumeDisplay() {
                const map = { '0_10': '0-10', '11_30': '11-30', '31_100': '31-100', '100_mais': '100+' };
                return map[answers.volume_leads] || '—';
            }

            function getCanalIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
            }

            function getFatIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>';
            }

            function getFatDisplay() {
                const map = { 'ate_10k': 'Até 10k', '10k_20k': '10-20k', '20k_50k': '20-50k', '50k_100k': '50-100k', 'acima_100k': '100k+' };
                return map[answers.faturamento] || '—';
            }

            function getDorIcon() {
                return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            }

            function resetQuiz() {
                localStorage.removeItem('chamalead_quiz');
                answers = {};
                conditionalStep = null;
                currentStepIndex = 0;
                sessionId = generateSessionId();
                saveState();

                document.querySelectorAll('.option-btn').forEach(function (btn) {
                    btn.classList.remove('selected');
                });
                document.getElementById('inputNome').value = '';
                document.getElementById('inputWhatsapp').value = '';

                const refazerBtn = document.querySelector('.btn-refazer');
                if (refazerBtn) refazerBtn.remove();

                renderStep();
            }

            function getFaturamentoValor(key) {
                var map = {
                    'ate_10k': 10000,
                    '10k_20k': 20000,
                    '20k_50k': 50000,
                    '50k_100k': 100000,
                    'acima_100k': 150000,
                };
                return map[key] || 0;
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
</body>
</html>
