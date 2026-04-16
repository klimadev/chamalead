    <script>
        (function () {
            'use strict';

            const STEPS = [
                'welcome',
                'nome',
                'whatsapp',
                'cargo',
                'faturamento',
                'canal',
                'volume',
                'dor',
                'urgencia',
                'resultado',
            ];

            const CONDITIONAL_STEPS = {
                atendimento_lento: 'dor_atendimento_lento',
                fora_horario: 'dor_fora_horario',
                falta_followup: 'dor_falta_followup',
                prospeccao_inconsistente: 'dor_prospeccao',
                converte_mal: 'dor_converte',
                organizacao_baguncada: 'dor_organizacao',
            };

            const TOTAL_STEPS = 10;

            let currentStepIndex = 0;
            let conditionalStep = null;
            let answers = {};
            let sessionId = '';
            const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            let fitRaf = null;

            function motionDelay(ms) {
                return prefersReducedMotion ? 0 : ms;
            }

            function onPhaseTransitionEnd(element, callback) {
                if (!element || typeof callback !== 'function') {
                    return;
                }

                if (prefersReducedMotion) {
                    callback();
                    return;
                }

                let done = false;
                function finish() {
                    if (done) return;
                    done = true;
                    element.removeEventListener('transitionend', handle);
                    callback();
                }

                function handle(event) {
                    if (event.target !== element) return;
                    if (event.propertyName !== 'opacity') return;
                    finish();
                }

                element.addEventListener('transitionend', handle);
                setTimeout(finish, motionDelay(520));
            }

            function onAnimationEnd(element, callback, fallbackMs) {
                if (!element || typeof callback !== 'function') {
                    return;
                }

                if (prefersReducedMotion) {
                    callback();
                    return;
                }

                let done = false;
                function finish() {
                    if (done) return;
                    done = true;
                    element.removeEventListener('animationend', handle);
                    callback();
                }

                function handle(event) {
                    if (event.target !== element) return;
                    finish();
                }

                element.addEventListener('animationend', handle);
                setTimeout(finish, motionDelay(fallbackMs || 900));
            }

            function revealResultPhases(phases) {
                if (!Array.isArray(phases) || !phases.length) {
                    return;
                }

                let index = 0;
                function revealNext() {
                    const phase = phases[index];
                    if (!phase) return;

                    phase.classList.add('is-visible');
                    index += 1;

                    if (index < phases.length) {
                        onPhaseTransitionEnd(phase, revealNext);
                    }
                }

                revealNext();
            }

            function generateSessionId() {
                return crypto.randomUUID ? crypto.randomUUID() : [Date.now().toString(36), Math.random().toString(36).slice(2)].join('-');
            }

            function getUTMParams() {
                const params = new URLSearchParams(window.location.search);
                return {
                    utm_source: params.get('utm_source') || '',
                    utm_medium: params.get('utm_medium') || '',
                    utm_campaign: params.get('utm_campaign') || '',
                    utm_content: params.get('utm_content') || '',
                    utm_term: params.get('utm_term') || '',
                };
            }

            function getPrimaryDor() {
                if (Array.isArray(answers.dor_principal)) {
                    return answers.dor_principal[0] || '';
                }

                return answers.dor_principal || '';
            }

            function loadState() {
                try {
                    const saved = localStorage.getItem('chamalead_quiz');
                    if (saved) {
                        const data = JSON.parse(saved);
                        if (data.sessionId && data.answers) {
                            sessionId = data.sessionId;
                            answers = data.answers;
                            currentStepIndex = data.currentStepIndex || 0;
                            conditionalStep = data.conditionalStep || null;
                            return true;
                        }
                    }
                } catch (e) {
                }
                return false;
            }

            function saveState() {
                try {
                    localStorage.setItem('chamalead_quiz', JSON.stringify({
                        sessionId,
                        answers,
                        currentStepIndex,
                        conditionalStep,
                    }));
                } catch (e) {
                }
            }

            function init() {
                const restored = loadState();
                if (!restored) {
                    sessionId = generateSessionId();
                    answers = {};
                    currentStepIndex = 0;
                    conditionalStep = null;
                }
                saveState();
                bindBackgroundInteractivity();
                renderStep();
                bindEvents();
            }

            function getBackgroundPreset(stepKey) {
                if (stepKey === 'welcome') {
                    return {
                        mood: 'welcome',
                        bias: 'desire',
                        energy: 0.86,
                        speed: '14s',
                        angle: '128deg',
                        warm: 0.22,
                        alert: 0.09,
                        focusX: '44%',
                        focusY: '28%',
                        grid: 0.2,
                    };
                }

                if (stepKey === 'nome' || stepKey === 'whatsapp') {
                    return {
                        mood: 'contact',
                        bias: 'clarity',
                        energy: 0.48,
                        speed: '20s',
                        angle: '140deg',
                        warm: 0.13,
                        alert: 0.07,
                        focusX: '48%',
                        focusY: '30%',
                        grid: 0.14,
                    };
                }

                if (stepKey === 'dor' || (stepKey && stepKey.indexOf('dor_') === 0)) {
                    return {
                        mood: 'pain',
                        bias: 'risk',
                        energy: 0.78,
                        speed: '12s',
                        angle: '118deg',
                        warm: 0.14,
                        alert: 0.2,
                        focusX: '58%',
                        focusY: '26%',
                        grid: 0.18,
                    };
                }

                if (stepKey === 'urgencia') {
                    return {
                        mood: 'urgency',
                        bias: 'action',
                        energy: 0.92,
                        speed: '10.5s',
                        angle: '106deg',
                        warm: 0.2,
                        alert: 0.2,
                        focusX: '62%',
                        focusY: '32%',
                        grid: 0.16,
                    };
                }

                if (stepKey === 'resultado') {
                    return {
                        mood: 'result',
                        bias: 'reward',
                        energy: 0.62,
                        speed: '17s',
                        angle: '134deg',
                        warm: 0.2,
                        alert: 0.08,
                        focusX: '50%',
                        focusY: '24%',
                        grid: 0.22,
                    };
                }

                return {
                    mood: 'diagnosis',
                    bias: 'focus',
                    energy: 0.66,
                    speed: '16s',
                    angle: '130deg',
                    warm: 0.17,
                    alert: 0.1,
                    focusX: '52%',
                    focusY: '29%',
                    grid: 0.18,
                };
            }

            function applyBackgroundMood(stepKey) {
                const quizBg = document.getElementById('quizBg');
                if (!quizBg) return;

                const preset = getBackgroundPreset(stepKey || 'welcome');
                quizBg.dataset.mood = preset.mood;
                quizBg.dataset.bias = preset.bias;

                const style = document.documentElement.style;
                style.setProperty('--bg-energy', String(preset.energy));
                style.setProperty('--bg-speed', preset.speed);
                style.setProperty('--bg-angle', preset.angle);
                style.setProperty('--bg-warm-alpha', String(preset.warm));
                style.setProperty('--bg-alert-alpha', String(preset.alert));
                style.setProperty('--bg-focus-x', preset.focusX);
                style.setProperty('--bg-focus-y', preset.focusY);
                style.setProperty('--bg-grid-opacity', String(preset.grid));
            }

            function bindBackgroundInteractivity() {
                const quizBg = document.getElementById('quizBg');
                const spotlight = document.getElementById('quizBgSpotlight');
                if (!quizBg || !spotlight || prefersReducedMotion) return;

                let rafId = null;
                let pointerX = window.innerWidth * 0.5;
                let pointerY = window.innerHeight * 0.35;
                let targetX = pointerX;
                let targetY = pointerY;

                function updateSpotlight() {
                    pointerX += (targetX - pointerX) * 0.08;
                    pointerY += (targetY - pointerY) * 0.08;
                    spotlight.style.left = pointerX + 'px';
                    spotlight.style.top = pointerY + 'px';
                    rafId = window.requestAnimationFrame(updateSpotlight);
