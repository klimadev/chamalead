(function () {
    'use strict';

    const STEPS = ['welcome', 'nome', 'whatsapp', 'cargo', 'faturamento', 'canal', 'volume', 'dor', 'urgencia', 'resultado'];
    const CONDITIONAL_STEPS = {
        atendimento_lento: 'dor_atendimento_lento',
        fora_horario: 'dor_fora_horario',
        falta_followup: 'dor_falta_followup',
        prospeccao_inconsistente: 'dor_prospeccao',
        converte_mal: 'dor_converte',
        organizacao_baguncada: 'dor_organizacao'
    };
    const TOTAL_STEPS = 10;
    const STORAGE_KEY = 'chamalead_quiz';
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let currentStepIndex = 0;
    let conditionalStep = null;
    let answers = {};
    let sessionId = '';
    let fitRaf = null;
    let phoneValidationTimeout = null;
    let phoneValidationController = null;
    let phoneValidated = false;
    let lastValidatedPhone = '';

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
            if (done) {
                return;
            }

            done = true;
            element.removeEventListener('transitionend', handle);
            callback();
        }

        function handle(event) {
            if (event.target !== element || event.propertyName !== 'opacity') {
                return;
            }

            finish();
        }

        element.addEventListener('transitionend', handle);
        window.setTimeout(finish, motionDelay(520));
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
            if (done) {
                return;
            }

            done = true;
            element.removeEventListener('animationend', handle);
            callback();
        }

        function handle(event) {
            if (event.target !== element) {
                return;
            }

            finish();
        }

        element.addEventListener('animationend', handle);
        window.setTimeout(finish, motionDelay(fallbackMs || 900));
    }

    function revealResultPhases(phases) {
        if (!Array.isArray(phases) || !phases.length) {
            return;
        }

        let index = 0;
        function revealNext() {
            const phase = phases[index];
            if (!phase) {
                return;
            }

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
            utm_term: params.get('utm_term') || ''
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
            const saved = localStorage.getItem(STORAGE_KEY);
            if (!saved) {
                return false;
            }

            const data = JSON.parse(saved);
            if (!data.sessionId || !data.answers) {
                return false;
            }

            sessionId = data.sessionId;
            answers = data.answers;
            currentStepIndex = data.currentStepIndex || 0;
            conditionalStep = data.conditionalStep || null;

            return true;
        } catch (error) {
            return false;
        }
    }

    function saveState() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                sessionId: sessionId,
                answers: answers,
                currentStepIndex: currentStepIndex,
                conditionalStep: conditionalStep
            }));
        } catch (error) {
        }
    }

    function getBackgroundPreset(stepKey) {
        if (stepKey === 'welcome') {
            return { mood: 'welcome', bias: 'desire', energy: 0.86, speed: '14s', angle: '128deg', warm: 0.22, alert: 0.09, focusX: '44%', focusY: '28%', grid: 0.2 };
        }
        if (stepKey === 'nome' || stepKey === 'whatsapp') {
            return { mood: 'contact', bias: 'clarity', energy: 0.48, speed: '20s', angle: '140deg', warm: 0.13, alert: 0.07, focusX: '48%', focusY: '30%', grid: 0.14 };
        }
        if (stepKey === 'dor' || (stepKey && stepKey.indexOf('dor_') === 0)) {
            return { mood: 'pain', bias: 'risk', energy: 0.78, speed: '12s', angle: '118deg', warm: 0.14, alert: 0.2, focusX: '58%', focusY: '26%', grid: 0.18 };
        }
        if (stepKey === 'urgencia') {
            return { mood: 'urgency', bias: 'action', energy: 0.92, speed: '10.5s', angle: '106deg', warm: 0.2, alert: 0.2, focusX: '62%', focusY: '32%', grid: 0.16 };
        }
        if (stepKey === 'resultado') {
            return { mood: 'result', bias: 'reward', energy: 0.62, speed: '17s', angle: '134deg', warm: 0.2, alert: 0.08, focusX: '50%', focusY: '24%', grid: 0.22 };
        }

        return { mood: 'diagnosis', bias: 'focus', energy: 0.66, speed: '16s', angle: '130deg', warm: 0.17, alert: 0.1, focusX: '52%', focusY: '29%', grid: 0.18 };
    }

    function applyBackgroundMood(stepKey) {
        const quizBg = document.getElementById('quizBg');
        if (!quizBg) {
            return;
        }

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
        if (!quizBg || !spotlight || prefersReducedMotion) {
            return;
        }

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
            if (!event.touches || !event.touches[0]) {
                return;
            }

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
        if (index >= STEPS.length) {
            return null;
        }

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
        if (currentStepIndex === 0) {
            return 0;
        }

        const effectiveTotal = getEffectiveTotal();
        const stepNum = currentStepIndex;
        return Math.min((stepNum / effectiveTotal) * 100, 100);
    }

    function getProgressLabels() {
        const effectiveTotal = getEffectiveTotal();
        const stepNum = Math.max(0, Math.min(currentStepIndex, effectiveTotal));
        return { stepNum: stepNum, effectiveTotal: effectiveTotal, percent: Math.round(getProgress()) };
    }

    function getProgressStage(stepNum, total) {
        if (!stepNum || stepNum <= 2) {
            return { label: 'Descoberta', tone: 'stage-discovery', icon: '<circle cx="12" cy="12" r="9"></circle><path d="M9 12l2 2 4-4"></path>' };
        }
        if (stepNum < total) {
            return { label: 'Diagnóstico', tone: 'stage-diagnosis', icon: '<path d="M12 3v18"></path><path d="M3 12h18"></path><circle cx="12" cy="12" r="9"></circle>' };
        }

        return { label: 'Resultado', tone: 'stage-result', icon: '<path d="M12 3l2.6 5.2L20 9l-4 3.9.9 5.5L12 15.8 7.1 18.4 8 12.9 4 9l5.4-.8L12 3z"></path>' };
    }

    function renderProgressStage(stage) {
        return '<span class="progress-stage-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + (stage.icon || '') + '</svg></span><span class="progress-stage-text">' + (stage.label || '') + '</span>';
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
            organizacao_baguncada: 'Padronização de etapas reduz perda de oportunidades no meio do funil.'
        };
        const staticMap = {
            welcome: 'Sem pegadinha: no final você recebe um diagnóstico prático para o seu cenário.',
            nome: 'Vamos personalizar o diagnóstico para você.',
            whatsapp: 'Usamos esse contato para continuar o diagnóstico com recomendações práticas.',
            cargo: 'Seu papel muda o tipo de automação mais eficiente.',
            faturamento: 'Essa resposta ajuda a calibrar o nível de prioridade e implementação.',
            canal: 'Canal principal define onde focar resposta e qualificação automática.',
            volume: 'Volume indica se o gargalo é velocidade, processo ou capacidade.',
            dor: 'Selecione uma ou mais opcoes para gerar recomendacoes mais assertivas.',
            dor_atendimento_lento: byDor.atendimento_lento,
            dor_fora_horario: byDor.fora_horario,
            dor_falta_followup: byDor.falta_followup,
            dor_prospeccao: byDor.prospeccao_inconsistente,
            dor_converte: byDor.converte_mal,
            dor_organizacao: byDor.organizacao_baguncada,
            urgencia: 'Quanto mais cedo começar, mais rápido você recupera oportunidades perdidas.',
            resultado: 'Seu potencial foi calculado com base no seu contexto atual.'
        };

        const primaryDor = getPrimaryDor();
        if (stepKey === 'urgencia' && primaryDor && byDor[primaryDor]) {
            return byDor[primaryDor];
        }

        return staticMap[stepKey] || '';
    }

    function updateGuidance(stepKey) {
        const guidance = document.getElementById('stepGuidance');
        if (!guidance) {
            return;
        }

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
            resultado: '<path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-6"></path>'
        };
        const icon = iconMap[stepKey] || '<circle cx="12" cy="12" r="9"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path>';

        guidance.classList.remove('active');
        guidance.innerHTML = '<span class="guidance-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + icon + '</svg></span><span class="guidance-text">' + text + '</span>';
        requestAnimationFrame(function () {
            guidance.classList.add('active');
        });
    }

    function checkContentFits() {
        const content = document.getElementById('quizContent');
        if (!content) {
            return true;
        }

        return content.scrollHeight <= content.clientHeight + 1;
    }

    function applyDensity(density) {
        const container = document.querySelector('.quiz-container');
        if (container) {
            container.dataset.density = density;
        }
    }

    function fitNoScrollLayout() {
        const container = document.querySelector('.quiz-container');
        if (!container || !getStepKey(currentStepIndex)) {
            return;
        }

        container.dataset.overflowRisk = '0';
        applyDensity('normal');
        if (checkContentFits()) {
            return;
        }

        applyDensity('tight');
        if (checkContentFits()) {
            return;
        }

        applyDensity('compact');
        if (checkContentFits()) {
            return;
        }

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
        const inputNome = document.getElementById('inputNome');
        const inputWhatsapp = document.getElementById('inputWhatsapp');

        if (stepKey === 'nome' && inputNome && answers.nome) {
            inputNome.value = answers.nome;
        }
        if (stepKey === 'whatsapp' && inputWhatsapp && answers.whatsapp) {
            inputWhatsapp.value = formatPhoneDisplay(answers.whatsapp);
        }

        document.querySelectorAll('.option-btn').forEach(function (btn) {
            btn.classList.remove('selected');
        });

        const currentStepEl = document.querySelector('.step.active');
        if (!currentStepEl) {
            return;
        }

        const optionBtn = currentStepEl.querySelector('.option-btn');
        const field = optionBtn ? optionBtn.dataset.field : '';

        if (field === 'dor_principal') {
            const selectedValues = Array.isArray(answers.dor_principal) ? answers.dor_principal : (answers.dor_principal ? [answers.dor_principal] : []);
            selectedValues.forEach(function (value) {
                const selected = currentStepEl.querySelector('[data-value="' + value + '"]');
                if (selected) {
                    selected.classList.add('selected');
                }
            });
        } else if (field && answers[field]) {
            const selected = currentStepEl.querySelector('[data-value="' + answers[field] + '"]');
            if (selected) {
                selected.classList.add('selected');
            }
        }
    }

    function renderStep() {
        const stepKey = getStepKey(currentStepIndex);
        window.stepKey = stepKey;
        if (!stepKey) {
            return;
        }

        document.querySelectorAll('.step').forEach(function (el) {
            el.classList.remove('active', 'exiting');
            el.style.display = 'none';
        });

        const target = document.querySelector('[data-step="' + stepKey + '"]');
        if (!target) {
            return;
        }

        target.style.display = '';
        target.classList.add('active');

        applyBackgroundMood(stepKey);
        updateProgressUI();
        updateGuidance(stepKey);
        updateFooter();
        restoreInputValues();
        scheduleLayoutFit();
    }

    function updateFooter() {
        const footer = document.getElementById('footerCta');
        const btn = document.getElementById('ctaBtn');
        const stepKey = getStepKey(currentStepIndex);
        if (!footer || !btn || !stepKey) {
            return;
        }

        footer.classList.remove('footer-cta--hidden');

        if (stepKey === 'welcome') {
            btn.innerHTML = '<span>Começar diagnóstico</span>';
            btn.disabled = false;
            btn.onclick = function () { goToNext(); };
        } else if (stepKey === 'nome') {
            btn.innerHTML = '<span>Continuar</span>';
            btn.disabled = false;
            btn.onclick = function () { validateAndNext('nome'); };
        } else if (stepKey === 'whatsapp') {
            const phoneInput = document.getElementById('inputWhatsapp');
            const phoneDigits = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';
            btn.innerHTML = '<span>Continuar</span>';
            btn.disabled = phoneDigits.length !== 11 || !phoneValidated;
            btn.onclick = function () { validateAndNext('whatsapp'); };
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
            const selectedValues = Array.isArray(answers.dor_principal) ? answers.dor_principal : (answers.dor_principal ? [answers.dor_principal] : []);
            btn.innerHTML = '<span>Continuar</span>';
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
        const digits = String(value || '').replace(/\D/g, '');
        if (digits.length <= 2) {
            return digits.length ? '(' + digits : '';
        }
        if (digits.length <= 7) {
            return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
        }
        return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7, 11);
    }

    function clearError(field) {
        const suffix = field.charAt(0).toUpperCase() + field.slice(1);
        const errorEl = document.getElementById('error' + suffix);
        const inputEl = document.getElementById('input' + suffix);
        if (errorEl) {
            errorEl.classList.remove('visible');
        }
        if (inputEl) {
            inputEl.classList.remove('input-error');
            inputEl.setAttribute('aria-invalid', 'false');
        }
    }

    function showError(field) {
        const suffix = field.charAt(0).toUpperCase() + field.slice(1);
        const errorEl = document.getElementById('error' + suffix);
        const inputEl = document.getElementById('input' + suffix);
        if (errorEl) {
            errorEl.classList.add('visible');
        }
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
            if (val.length < 10 || val.length > 11 || !phoneValidated) {
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
        currentStepIndex += 1;
        saveState();
        renderStep();

        if (currentEl) {
            currentEl.classList.add('exiting');
        }
    }

    function clearPhoneValidationState() {
        phoneValidated = false;
        lastValidatedPhone = '';
        const badgeState = document.getElementById('badgeState');
        const badgeCarrier = document.getElementById('badgeCarrier');
        const statusEl = document.getElementById('phoneValidationStatus');
        if (badgeState) {
            badgeState.classList.add('hidden');
        }
        if (badgeCarrier) {
            badgeCarrier.classList.add('hidden');
        }
        if (statusEl) {
            statusEl.innerHTML = '';
        }
        updateWhatsappButtonState();
    }

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

    function getStateFromDDDFallback(ddd) {
        const dddToState = { '11': 'SP', '12': 'SP', '13': 'SP', '14': 'SP', '15': 'SP', '16': 'SP', '17': 'SP', '18': 'SP', '19': 'SP', '21': 'RJ', '22': 'RJ', '24': 'RJ', '31': 'MG', '32': 'MG', '33': 'MG', '34': 'MG', '35': 'MG', '36': 'MG', '37': 'MG', '38': 'MG', '41': 'PR', '42': 'PR', '43': 'PR', '44': 'PR', '45': 'PR', '46': 'PR', '47': 'SC', '48': 'SC', '49': 'SC', '51': 'RS', '52': 'RS', '53': 'RS', '54': 'RS', '55': 'RS', '61': 'DF', '62': 'GO', '63': 'GO', '64': 'GO', '65': 'MT', '66': 'MT', '67': 'MT', '68': 'MT', '69': 'RO', '71': 'BA', '73': 'BA', '74': 'BA', '75': 'BA', '77': 'BA', '79': 'PI', '81': 'PE', '82': 'PB', '84': 'RN', '85': 'CE', '86': 'CE', '87': 'CE', '88': 'CE', '89': 'PI', '91': 'PA', '92': 'AM', '93': 'AM', '94': 'PA', '95': 'PA', '96': 'PA', '97': 'AM', '98': 'AM', '99': 'AM' };
        return dddToState[ddd] || '';
    }

    function getCarrierFromDigitsFallback(digits) {
        if (digits.length < 4) {
            return 'Desconhecida';
        }

        const prefix = digits.substring(0, 4);
        const carrierPrefixes = {
            Vivo: ['9191', '9192', '9193', '9194', '9195', '9196', '9197', '9198', '9199', '2191', '2192', '2193', '2194', '4191', '4192', '4193', '5191', '5192', '5193', '6191', '6192', '7191', '7192', '8191', '8192'],
            Claro: ['2195', '2196', '2197', '2198', '2199', '4194', '4195', '4196', '4197', '4198', '4199', '5194', '5195', '5196', '5197', '5198', '5199', '6193', '6194', '6195', '6196', '6197', '7193', '7194', '7195', '7196', '7197', '8193', '8194', '8195', '8196'],
            TIM: ['2191', '2192', '2193', '4195', '4196', '5191', '5192', '6191', '6192', '7191', '8191', '8192', '8193', '9194', '9195', '9196', '9197'],
            Oi: ['3191', '3192', '3193', '3194', '3195', '3196', '3197', '3198', '3199', '2194', '2195', '5193', '5194', '5195', '5196', '6195', '6196', '7198', '7199', '8197', '8198', '8199', '9191', '9192', '9193']
        };

        for (const carrier in carrierPrefixes) {
            if (carrierPrefixes[carrier].indexOf(prefix) !== -1) {
                return carrier;
            }
        }

        return 'Desconhecida';
    }

    function updateWhatsappButtonState() {
        const input = document.getElementById('inputWhatsapp');
        const btn = document.getElementById('ctaBtn');
        const phoneDigits = input ? input.value.replace(/\D/g, '') : '';
        if (btn && window.stepKey === 'whatsapp') {
            btn.disabled = phoneDigits.length !== 11 || !phoneValidated;
        }
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
            body: JSON.stringify({ action: 'validate-phone', session_id: sessionId, phone: phone }),
            signal: signal
        })
            .then(function (res) {
                clearTimeout(timeoutId);
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }
                return res.json();
            })
            .then(function (data) {
                if (signal.aborted) {
                    return;
                }

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
                    if (badgeCarrier) {
                        badgeCarrier.classList.add('hidden');
                    }
                }

                updateWhatsappButtonState();
            })
            .catch(function (error) {
                clearTimeout(timeoutId);
                if (statusEl) {
                    statusEl.innerHTML = '';
                }

                if (signal.aborted || error.name === 'AbortError') {
                    return;
                }

                phoneValidated = false;
                lastValidatedPhone = '';

                const digits = phone.replace(/\D/g, '');
                if (digits.length === 11) {
                    const state = getStateFromDDDFallback(digits.substring(0, 2));
                    const carrier = getCarrierFromDigitsFallback(digits);
                    const badgeState = document.getElementById('badgeState');
                    const badgeCarrier = document.getElementById('badgeCarrier');

                    phoneValidated = true;
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
                } else if (statusEl) {
                    statusEl.innerHTML = '<div class="validation-badge validation-badge--invalid">✗ Número inválido</div>';
                }

                updateWhatsappButtonState();
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

    function buildSubmitPayload() {
        const utm = getUTMParams();
        return {
            action: 'submit',
            session_id: sessionId,
            nome: answers.nome || '',
            whatsapp: answers.whatsapp || '',
            cargo: answers.cargo || '',
            faturamento: answers.faturamento || '',
            canal: answers.canal || '',
            volume_leads: answers.volume_leads || '',
            dor_principal: getPrimaryDor(),
            dor_detalhe: answers.dor_detalhe || '',
            timing: answers.timing || '',
            current_step: TOTAL_STEPS,
            fbp: getCookie('_fbp'),
            fbc: getCookie('_fbc'),
            client_ip_address: '',
            client_user_agent: navigator.userAgent || '',
            utm_source: utm.utm_source,
            utm_medium: utm.utm_medium,
            utm_campaign: utm.utm_campaign,
            utm_content: utm.utm_content,
            utm_term: utm.utm_term
        };
    }

    function setSubmitLoadingState() {
        const btn = document.getElementById('ctaBtn');
        if (btn) {
            btn.innerHTML = '<div class="spinner"></div>';
            btn.disabled = true;
        }

        const insightText = document.getElementById('resultInsightText');
        if (insightText) {
            insightText.textContent = getFinalLoadingCopy();
        }
    }

    function handleSubmitError(message) {
        const btn = document.getElementById('ctaBtn');
        const insightText = document.getElementById('resultInsightText');
        if (btn) {
            btn.innerHTML = '<span>' + getResultCtaByScore(calculateScore()) + '</span>';
            btn.disabled = false;
        }
        if (insightText) {
            insightText.textContent = message || 'Não conseguimos finalizar agora. Tente novamente em instantes.';
        }
    }

    function submitQuiz() {
        const btn = document.getElementById('ctaBtn');
        if (btn && btn.disabled) {
            return;
        }

        setSubmitLoadingState();

        fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(buildSubmitPayload())
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return null;
                }).then(function (data) {
                    if (!response.ok || !data || !data.success) {
                        const message = data && data.message ? data.message : 'Falha ao finalizar o diagnóstico.';
                        throw new Error(message);
                    }

                    return data;
                });
            })
            .then(function (data) {
                currentStepIndex = STEPS.indexOf('resultado');
                saveState();
                renderResult(data);
            })
            .catch(function (error) {
                handleSubmitError(error.message);
            });
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

        if (!badge || !metrics || !insight || !insightText || !scoreRing || !scoreValue) {
            return;
        }

        const score = calculateScore();
        const scoreOffset = 264 - (264 * score / 100);

        badge.classList.remove('is-visible');
        metrics.classList.remove('is-visible');
        insight.classList.remove('is-visible');
        scoreRing.classList.remove('animate');
        if (scoreRingWrap) {
            scoreRingWrap.classList.remove('is-live');
        }
        scoreValue.classList.remove('is-live', 'is-counting', 'tick');
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
            if (scoreRingWrap) {
                scoreRingWrap.classList.add('is-live');
            }
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

        const canalLabels = { whatsapp_direto: 'WhatsApp', instagram_whatsapp: 'Instagram', trafego_pago: 'Tráfego pago', indicacao: 'Indicação', prospeccao_ativa: 'Prospecção', varios_canais: 'Multicanal' };
        const dorLabels = { atendimento_lento: 'Atendimento lento', fora_horario: 'Fora do horário', falta_followup: 'Sem follow-up', prospeccao_inconsistente: 'Prospecção fraca', converte_mal: 'Baixa conversão', organizacao_baguncada: 'Desorganização' };

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
        if (btn) {
            btn.innerHTML = '<span>' + getResultCtaByScore(score) + '</span>';
            btn.disabled = false;
            btn.onclick = function () { submitQuiz(); };
        }

        if (footer && !footer.querySelector('.btn-refazer')) {
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
        const btn = document.getElementById('ctaBtn');
        if (!badge || !btn) {
            return;
        }

        let badgeClass = 'result-badge--cold';
        let badgeText = 'Em análise';
        if (data.classificacao === 'quente') {
            badgeClass = 'result-badge--hot';
            badgeText = 'Alta prioridade';
        } else if (data.classificacao === 'morno') {
            badgeClass = 'result-badge--warm';
            badgeText = 'Oportunidade identificada';
        }

        badge.innerHTML = '<span class="result-badge ' + badgeClass + '">' + badgeText + '</span>';
        btn.innerHTML = '<div class="spinner"></div>';
        btn.disabled = true;

        const spinner = btn.querySelector('.spinner');
        onAnimationEnd(spinner, function () {
            btn.innerHTML = '<span>' + getResultCtaByClassificacao(data.classificacao) + '</span>';
            btn.disabled = false;
            localStorage.removeItem(STORAGE_KEY);
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
            organizacao_baguncada: 'Organizando trilha ideal de atendimento...'
        };
        const primaryDor = getPrimaryDor();
        return primaryDor && byDor[primaryDor] ? byDor[primaryDor] : 'Analisando seu cenario...';
    }

    function calculateScore() {
        let score = 20;
        const fatMap = { ate_10k: 10, '10k_20k': 20, '20k_50k': 30, '50k_100k': 40, acima_100k: 50 };
        const volumeMap = { '0_10': 5, '11_30': 15, '31_100': 25, '100_mais': 35 };
        const timingMap = { agora: 15, este_mes: 10, proximo_mes: 5, entendendo: 0 };
        score += fatMap[answers.faturamento] || 10;
        score += volumeMap[answers.volume_leads] || 5;
        score += ['atendimento_lento', 'fora_horario', 'falta_followup'].includes(getPrimaryDor()) ? 15 : 10;
        score += timingMap[answers.timing] || 5;
        return Math.min(Math.max(score, 15), 98);
    }

    function animateScoreRingAndValue(ringEl, valueEl, score, duration) {
        if (!ringEl || !valueEl) {
            return;
        }

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
            const progress = Math.min((now - start) / duration, 1);
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
        const map = { ate_10k: 'Até 10k', '10k_20k': '10-20k', '20k_50k': '20-50k', '50k_100k': '50-100k', acima_100k: '100k+' };
        return map[answers.faturamento] || '—';
    }

    function getDorIcon() {
        return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(249,115,22,0.78)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
    }

    function resetQuiz() {
        localStorage.removeItem(STORAGE_KEY);
        answers = {};
        conditionalStep = null;
        currentStepIndex = 0;
        sessionId = generateSessionId();
        phoneValidated = false;
        lastValidatedPhone = '';
        saveState();

        document.querySelectorAll('.option-btn').forEach(function (btn) {
            btn.classList.remove('selected');
        });
        document.getElementById('inputNome').value = '';
        document.getElementById('inputWhatsapp').value = '';

        const refazerBtn = document.querySelector('.btn-refazer');
        if (refazerBtn) {
            refazerBtn.remove();
        }

        clearPhoneValidationState();
        renderStep();
    }

    function bindEvents() {
        document.querySelectorAll('.option-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const parent = this.closest('.step');
                const field = this.dataset.field;
                const value = this.dataset.value;

                if (field === 'dor_principal') {
                    const selectedValues = Array.isArray(answers.dor_principal) ? answers.dor_principal.slice() : (answers.dor_principal ? [answers.dor_principal] : []);
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
                    conditionalStep = primaryDor && CONDITIONAL_STEPS[primaryDor] ? CONDITIONAL_STEPS[primaryDor] : null;
                    saveState();
                    updateFooter();
                    return;
                }

                parent.querySelectorAll('.option-btn').forEach(function (b) {
                    b.classList.remove('selected');
                });
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

        document.getElementById('inputNome').addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                validateAndNext('nome');
            }
        });

        document.getElementById('inputNome').addEventListener('input', function () {
            clearError('nome');
        });

        document.getElementById('inputWhatsapp').addEventListener('input', function () {
            const pos = this.selectionStart || this.value.length;
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

        document.getElementById('inputWhatsapp').addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                validateAndNext('whatsapp');
            }
        });

        window.addEventListener('resize', scheduleLayoutFit);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', scheduleLayoutFit);
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

        const container = document.querySelector('.quiz-container');
        if (container) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    container.dataset.animated = '1';
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
