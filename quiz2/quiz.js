const steps = [
  {
    id: "welcome",
    titleClass: "text-gradient",
    title: "Sua empresa pode estar <span class=text-highlight>perdendo dinheiro</span> sem perceber.",
    eyebrow: "Analise rapida",
    text: "Responda algumas perguntas rapidas e veja onde podem estar os principais problemas no seu atendimento, nas vendas e no contato com os clientes.",
    cta: { type: "primary", class: "btn-lava", text: "Comecar agora", action: () => nextStep() },
  },
  {
    id: "identify",
    title: "Antes de comecar, como voce se chama?",
    eyebrow: "Primeira parte",
    helper: "E por esse numero que vamos falar com voce e, se cair em analise, nossa equipe pode te ligar.",
    fields: [
      { label: "Seu nome", id: "inputName", type: "text", placeholder: "Digite seu nome", error: "nameError", autoComplete: "name" },
      { label: "Seu WhatsApp com DDD", id: "inputPhone", type: "tel", placeholder: "(00) 00000-0000", error: "phoneError", autoComplete: "tel", mask: true },
    ],
    cta: { text: "Continuar", action: validateIdentify },
  },
  { id: "cargo", eyebrow: "Seu papel", title: "Agora me conta um pouco sobre sua empresa.", subtitle: "Qual e sua funcao na empresa?", question: "cargo", options: ["Sou o dono", "Sou socio", "Sou gerente", "Trabalho no atendimento ou nas vendas", "Outra funcao"] },
  { id: "faturamento", eyebrow: "Tamanho da empresa", title: "Quanto sua empresa fatura por mes, mais ou menos?", question: "faturamento", options: ["Ate R$ 20 mil", "De R$ 20 mil a R$ 50 mil", "De R$ 50 mil a R$ 100 mil", "De R$ 100 mil a R$ 300 mil", "Acima de R$ 300 mil"] },
  { id: "origem_negocio", eyebrow: "Como a empresa gira", title: "Hoje sua empresa vive mais de que?", question: "origem_negocio", options: ["Agendamentos", "Orcamentos", "Clientes que compram de novo", "Vendas pelo WhatsApp ou Instagram", "Um pouco de tudo"] },
  { id: "volume", eyebrow: "Dois numeros", title: "Quantos contatos, atendimentos ou pedidos sua empresa recebe por mes?", question: "volume", options: ["Ate 30", "De 31 a 100", "De 101 a 300", "De 301 a 1000", "Mais de 1000"] },
  { id: "valor_medio", eyebrow: "Dois numeros", title: "Em media, quanto vale uma venda, um atendimento ou um servico seu?", question: "valor_medio", options: ["Ate R$ 100", "De R$ 101 a R$ 300", "De R$ 301 a R$ 800", "De R$ 801 a R$ 2.000", "Acima de R$ 2.000"] },
  {
    id: "dor",
    eyebrow: "Onde aperta mais",
    title: "Onde voce sente que mais perde clientes ou dinheiro hoje?",
    subtitle: "Escolha a opcao que mais parece com sua realidade.",
    question: "dor",
    options: [
      { text: "Demoram para responder e a pessoa desiste", next: "dor_resposta" },
      { text: "A pessoa pede informacoes, mas depois some", next: "dor_some" },
      { text: "Clientes antigos deixam de comprar e ninguem vai atras", next: "dor_clientes_antigos" },
      { text: "Fora do horario quase ninguem responde", next: "dor_fora_horario" },
      { text: "A agenda fica com horarios vazios", next: "dor_agenda" },
      { text: "Esta tudo meio baguncado e dificil de acompanhar", next: "dor_organizacao" },
    ],
  },
  { id: "dor_resposta", eyebrow: "Entendi", title: "Quanto tempo voces costumam levar para responder?", question: "detalhe_dor", options: ["Na mesma hora", "Em ate 1 hora", "Em algumas horas", "So quando alguem consegue", "Nao sei dizer"] },
  { id: "dor_some", eyebrow: "Entendi", title: "Quando a pessoa some, voces tentam falar de novo quantas vezes?", question: "detalhe_dor", options: ["5 vezes ou mais", "3 ou 4 vezes", "1 ou 2 vezes", "Quase nunca", "Nao fazemos isso"] },
  { id: "dor_clientes_antigos", eyebrow: "Entendi", title: "Hoje voces costumam chamar de volta clientes antigos?", question: "detalhe_dor", options: ["Sim, sempre", "As vezes", "Muito pouco", "Nao", "Nem temos isso organizado"] },
  { id: "dor_fora_horario", eyebrow: "Entendi", title: "O que acontece a noite ou no fim de semana?", question: "detalhe_dor", options: ["Continuamos respondendo", "Respondemos so alguns", "Fica para o dia seguinte", "Quase tudo fica parado"] },
  { id: "dor_agenda", eyebrow: "Entendi", title: "Quantos horarios ou oportunidades voce acha que perde por semana?", question: "detalhe_dor", options: ["De 1 a 3", "De 4 a 10", "De 11 a 20", "Mais de 20", "Nao sei"] },
  { id: "dor_organizacao", eyebrow: "Entendi", title: "Hoje as informacoes dos clientes ficam onde?", question: "detalhe_dor", options: ["Em um sistema bem organizado", "Em planilha", "No WhatsApp", "Em varios lugares diferentes", "Nao temos controle direito"] },
  { id: "historico", eyebrow: "Ultima parte", title: "Agora preciso entender se da para analisar seu caso de verdade.", subtitle: "Voce tem a lista dos seus clientes, conversas ou agendamentos dos ultimos 3 meses?", question: "historico", options: ["Sim, esta tudo organizado", "Tenho boa parte", "Tenho algumas coisas soltas", "Nao tenho isso organizado"] },
  { id: "envio_info", eyebrow: "Ultima parte", title: "Se a sua analise for liberada, voce consegue mandar essas informacoes na hora da reuniao?", question: "envio_info", options: ["Sim", "Consigo mandar logo depois", "Talvez", "Nao"] },
  { id: "urgencia", eyebrow: "Ultima parte", title: "Quando voce quer resolver isso?", question: "urgencia", options: ["Agora", "Ainda esta semana", "Ainda este mes", "So quero entender melhor por enquanto"] },
  { id: "decisao", eyebrow: "Ultima parte", title: "Voce pode decidir isso na reuniao?", question: "decisao", options: ["Sim", "Eu participo da decisao", "Nao"] },
  {
    id: "resultado",
    eyebrow: "Resultado",
    title: "Sua analise foi liberada.",
    noteLabel: "Existe chance real de recuperar cerca de",
    value: "R$ 0",
    note: "nos proximos 30 dias, de forma conservadora.",
    summary: ["Sua lista de clientes", "Seus agendamentos", "Ou suas conversas dos ultimos 3 meses"],
    resultText: "Pelas suas respostas, encontramos sinais de que sua empresa pode estar deixando dinheiro na mesa.",
    resultExtra: "Na reuniao, vamos olhar seu caso com voce, entender onde estao as perdas e mostrar o caminho mais rapido para corrigir isso.",
    cta: { text: "Escolher meu horario", action: handleResultCTA, class: "btn-lava", id: "resultCta" },
  },
]

const conditionalSteps = ["dor_resposta", "dor_some", "dor_clientes_antigos", "dor_fora_horario", "dor_agenda", "dor_organizacao"];

const baseFlow = [
  "welcome", "identify", "cargo", "faturamento", "origem_negocio", "volume", "valor_medio", "dor", "historico", "envio_info", "urgencia", "decisao", "resultado",
];

const DIAGNOSIS_URL = "https://cal.com/chamalead/diagnostico-selecionado";
const ANALYSIS_REDIRECT_URL = "/";
const QUALIFIED_REDIRECT_DELAY_MS = 5200;
const ANALYSIS_REDIRECT_DELAY_MS = 4600;

const state = { flow: [...baseFlow], currentIndex: 0, answers: {}, qualified: false, estimatedValue: 0 };

const quizContainer = document.getElementById("quizContainer");
const progressContainer = document.getElementById("progress-container");
const progressFill = document.getElementById("progress-fill");
const root = document.documentElement;
const stepsById = Object.fromEntries(steps.map((step) => [step.id, step]));
const renderedSteps = new Map();
const supportsViewTransitions = typeof document.startViewTransition === "function";
let identifyInputsReady = false;
let stepAccentTimer = 0;
const shouldAutoFocusInputs = window.matchMedia("(pointer: fine)").matches;
const inputPhone = () => document.getElementById("inputPhone");
const inputName = () => document.getElementById("inputName");
const welcomeCta = document.getElementById("welcomeCta");
let resultFlowTimers = [];
let resultFlowCountdown = null;
let resultFlowCountdownText = "Redirecionando automaticamente em {s}s.";
let qualifiedClosingStarted = false;

const existingWelcomeStep = document.getElementById("welcome");
if (existingWelcomeStep && existingWelcomeStep.closest("#quizContainer") === quizContainer) {
  existingWelcomeStep.setAttribute("aria-live", "polite");
  renderedSteps.set("welcome", existingWelcomeStep);
}

if (welcomeCta) {
  welcomeCta.disabled = false;
  welcomeCta.textContent = stepsById.welcome.cta.text;
}

const Mascot = {
  body: document.getElementById("mascotBody"),
  bubble: document.getElementById("mascotBubble"),
  timeout: null,
  say(text, duration = 4000) {
    this.bubble.textContent = text;
    this.bubble.classList.add("show");
    clearTimeout(this.timeout);
    if (duration > 0) this.timeout = setTimeout(() => this.bubble.classList.remove("show"), duration);
  },
  setEmotion(emotionClass) {
    this.body.className = "mascot-body";
    if (emotionClass) this.body.classList.add(emotionClass);
  },
  poke() {
    this.setEmotion("happy");
    this.say("Estou aqui com voce. Falta pouco.", 2500);
    setTimeout(() => this.setEmotion(""), 2500);
  },
};

Mascot.body.addEventListener("click", () => Mascot.poke());

const trackQuizEvent = (eventName, detail = {}) => {
  const payload = { event: eventName, ...detail };
  window.dispatchEvent(new CustomEvent(`quiz:${eventName}`, { detail: payload }));
  if (Array.isArray(window.dataLayer)) window.dataLayer.push(payload);
};

const queueResultFlow = (callback, delay) => {
  const timerId = window.setTimeout(callback, delay);
  resultFlowTimers.push(timerId);
  return timerId;
};

const clearResultFlow = () => {
  resultFlowTimers.forEach((timerId) => window.clearTimeout(timerId));
  resultFlowTimers = [];
  if (resultFlowCountdown) {
    window.clearInterval(resultFlowCountdown);
    resultFlowCountdown = null;
  }
};

const syncStepViewportState = (stepId) => {
  const isResultStep = stepId === "resultado";
  document.body.classList.toggle("is-result-step", isResultStep);
  document.body.dataset.activeStep = stepId;
};

const getResultNodes = () => {
  const resultStep = renderedSteps.get("resultado") || renderStep("resultado");
  if (!resultStep) return null;

  return {
    resultStep,
    amountWrap: resultStep.querySelector("#resultAmountWrap"),
    resultTitle: resultStep.querySelector("#resultTitle"),
    resultText: resultStep.querySelector("#resultText"),
    resultExtra: resultStep.querySelector("#resultExtra"),
    resultList: resultStep.querySelector("#resultList"),
    resultCta: resultStep.querySelector("#resultCta"),
    flowPanel: resultStep.querySelector("#resultFlowPanel"),
    flowKicker: resultStep.querySelector("#resultFlowKicker"),
    flowTitle: resultStep.querySelector("#resultFlowTitle"),
    flowText: resultStep.querySelector("#resultFlowText"),
    flowList: resultStep.querySelector("#resultFlowList"),
    flowCountdown: resultStep.querySelector("#resultFlowCountdown"),
    flowButton: resultStep.querySelector("#resultRedirectNow"),
    inlineCountdown: resultStep.querySelector("#resultInlineCountdown"),
  };
};

const resetResultClosingState = () => {
  clearResultFlow();
  resultFlowCountdownText = "Redirecionando automaticamente em {s}s.";
  qualifiedClosingStarted = false;
  const nodes = getResultNodes();
  if (!nodes) return;
  nodes.resultStep.classList.remove("result-closing");
  delete nodes.resultStep.dataset.phase;
  if (nodes.flowPanel) nodes.flowPanel.hidden = true;
  if (nodes.flowList) nodes.flowList.innerHTML = "";
  if (nodes.flowCountdown) nodes.flowCountdown.textContent = "";
  if (nodes.inlineCountdown) {
    nodes.inlineCountdown.textContent = "";
    nodes.inlineCountdown.hidden = true;
  }
  if (nodes.flowButton) {
    nodes.flowButton.hidden = true;
    nodes.flowButton.disabled = false;
  }
  if (nodes.resultCta) {
    nodes.resultCta.disabled = false;
    nodes.resultCta.style.display = "block";
  }
};

const updateFlowChecklist = (items) => {
  const nodes = getResultNodes();
  if (!nodes?.flowList) return;
  nodes.flowList.innerHTML = "";
  items.forEach((item) => {
    const li = document.createElement("li");
    li.textContent = item;
    nodes.flowList.appendChild(li);
  });
};

const updateRedirectCountdown = (deadline, textTemplate = "Redirecionando automaticamente em {s}s.") => {
  const nodes = getResultNodes();
  if (!nodes?.flowCountdown) return;
  resultFlowCountdownText = textTemplate;

  const renderCountdown = () => {
    const remainingMs = Math.max(0, deadline - Date.now());
    const remainingSeconds = Math.max(1, Math.ceil(remainingMs / 1000));
    nodes.flowCountdown.textContent = resultFlowCountdownText.replace("{s}", String(remainingSeconds));
  };

  renderCountdown();
  resultFlowCountdown = window.setInterval(renderCountdown, 250);
};

const redirectToUrl = (url, source, status) => {
  clearResultFlow();
  const nodes = getResultNodes();
  if (nodes?.flowButton) nodes.flowButton.disabled = true;
  trackQuizEvent("redirect_started", { source, qualified: state.qualified, status, destination: url });
  window.location.assign(url);
};

const setResultIcon = (status) => {
  const nodes = getResultNodes();
  const iconWrapper = nodes?.resultStep?.querySelector("#svgIconWrapper");
  if (!iconWrapper) return;

  if (status === "qualified") {
    iconWrapper.innerHTML = '<svg viewBox="0 0 50 50" aria-hidden="true"><circle cx="25" cy="25" r="22"></circle><path d="M14 25L22 33L36 17"></path></svg>';
    iconWrapper.dataset.icon = "qualified";
    return;
  }

  iconWrapper.innerHTML = '<svg viewBox="0 0 50 50" aria-hidden="true"><circle cx="25" cy="25" r="22"></circle><path d="M18 15C18 15 17 23 23 28C29 33 35 32 35 32"></path><path d="M31 30L36 31L34 36"></path></svg>';
  iconWrapper.dataset.icon = "analysis";
};

const startResultFinalizationFlow = ({ status = "analysis", source = "auto" } = {}) => {
  const nodes = getResultNodes();
  if (!nodes?.flowPanel) return;

  const isQualified = status === "qualified";
  const redirectUrl = isQualified ? DIAGNOSIS_URL : ANALYSIS_REDIRECT_URL;
  const redirectDelay = isQualified ? QUALIFIED_REDIRECT_DELAY_MS : ANALYSIS_REDIRECT_DELAY_MS;

  nodes.resultStep.classList.add("result-closing");
  nodes.resultStep.dataset.phase = isQualified ? "approved" : "analysis";
  nodes.flowPanel.hidden = false;
  nodes.flowList.innerHTML = "";

  if (isQualified) {
    nodes.flowKicker.textContent = "Pre-aprovado";
    nodes.flowTitle.textContent = "Agendamento liberado.";
    nodes.flowText.textContent = "Seu diagnostico esta pronto para a proxima etapa. Vamos abrir o agendamento automaticamente.";
    updateFlowChecklist(stepsById.resultado.summary);
    nodes.flowButton.textContent = "Ir para agendamento";
    nodes.flowButton.hidden = false;
    nodes.flowButton.disabled = false;
  } else {
    nodes.flowKicker.textContent = "Vamos analisar";
    nodes.flowTitle.textContent = "Recebemos seu caso.";
    nodes.flowText.textContent = "Vamos revisar suas respostas e, se fizer sentido, nossa equipe liga para voce no numero informado.";
    updateFlowChecklist([
      "Seu envio entrou na fila de analise",
      "Nosso time revisa o contexto do seu negocio",
      "Se houver aderencia, entramos em contato por ligacao",
    ]);
    nodes.flowButton.textContent = "Ir para o inicio";
    nodes.flowButton.hidden = false;
    nodes.flowButton.disabled = false;
  }

  clearResultFlow();

  nodes.flowButton.onclick = () => {
    redirectToUrl(redirectUrl, "manual", status);
  };

  nodes.flowCountdown.setAttribute("aria-live", "polite");
  updateRedirectCountdown(
    Date.now() + redirectDelay,
    isQualified ? "Abrindo o agendamento em {s}s." : "Voltando para o inicio em {s}s.",
  );
  queueResultFlow(() => redirectToUrl(redirectUrl, source, status), redirectDelay);
  setResultIcon(status);
  trackQuizEvent("result_finalization_started", { status, source });
};

const startQualifiedClosingFlow = (source = "auto") => {
  if (!state.qualified) return;
  if (qualifiedClosingStarted) {
    redirectToUrl(DIAGNOSIS_URL, "manual", "qualified");
    return;
  }

  clearResultFlow();
  qualifiedClosingStarted = true;

  const nodes = getResultNodes();
  if (!nodes?.inlineCountdown || !nodes.resultCta) return;

  const deadline = Date.now() + QUALIFIED_REDIRECT_DELAY_MS;
  nodes.resultStep.dataset.phase = "approved";
  nodes.inlineCountdown.hidden = false;
  nodes.inlineCountdown.setAttribute("aria-live", "polite");
  nodes.resultCta.textContent = "Ir para agendamento agora";
  nodes.resultCta.disabled = false;

  const renderInlineCountdown = () => {
    const remainingMs = Math.max(0, deadline - Date.now());
    const remainingSeconds = Math.max(1, Math.ceil(remainingMs / 1000));
    nodes.inlineCountdown.textContent = `Abrindo o agendamento em ${remainingSeconds}s.`;
  };

  renderInlineCountdown();
  resultFlowCountdown = window.setInterval(renderInlineCountdown, 250);
  queueResultFlow(() => redirectToUrl(DIAGNOSIS_URL, source, "qualified"), QUALIFIED_REDIRECT_DELAY_MS);
  Mascot.setEmotion("happy");
  Mascot.say("Analise liberada. Vou abrir seu agendamento.", 2200);
};

const startAnalysisClosingFlow = (source = "manual") => {
  if (state.qualified) return;
  startResultFinalizationFlow({ status: "analysis", source });
  Mascot.setEmotion("think");
  Mascot.say("Perfeito. Vamos analisar e te ligar se fizer sentido.", 2800);
};

const createQuestion = (step) => {
  const cached = renderedSteps.get(step.id);
  if (cached) return cached;

  const section = document.createElement("section");
  section.className = "step";
  section.id = step.id;
  section.setAttribute("aria-live", "polite");
  if (step.id === "resultado") section.setAttribute("data-view", "result");

  const header = document.createElement("div");
  header.className = "step-header";

  const eyebrow = document.createElement("span");
  eyebrow.className = "micro-copy";
  eyebrow.textContent = step.eyebrow || "";
  header.appendChild(eyebrow);

    if (step.title) {
      const h = document.createElement(step.id === "welcome" ? "h1" : "h2");
    h.className = step.titleClass || "step-title";
    h.innerHTML = step.title;
    header.appendChild(h);
  }

  if (step.subtitle) {
    const p = document.createElement("p");
    p.className = "subtitle";
    p.textContent = step.subtitle;
    header.appendChild(p);
  }

  if (step.text) {
    const p = document.createElement("p");
    p.className = "subtitle";
    p.style.marginTop = "6px";
    p.style.fontSize = "1.05rem";
    p.textContent = step.text;
    header.appendChild(p);
  }

  section.appendChild(header);

  if (step.helper) {
    const p = document.createElement("p");
    p.className = "helper-text";
    p.id = `helper-${step.id}`;
    p.textContent = step.helper;
    section.appendChild(p);
  }

  if (step.fields) {
    const inputGroup = document.createElement("div");
    inputGroup.className = "input-group";
    for (const field of step.fields) {
      const wrapper = document.createElement("div");
      wrapper.className = "input-field";
      const label = document.createElement("label");
      label.className = "field-label";
      label.setAttribute("for", field.id);
      label.textContent = field.label;
      const input = document.createElement("input");
      input.type = field.type;
      input.id = field.id;
      input.placeholder = field.placeholder;
      input.autocomplete = field.autoComplete;
      if (field.id === "inputPhone") input.setAttribute("aria-describedby", "helper-identify");
      const error = document.createElement("p");
      error.className = "error-msg";
      error.id = field.error;
      error.textContent = field.id === "inputName" ? "Escreva pelo menos 2 letras." : "Digite um numero valido com DDD.";

      wrapper.append(label, input, error);
      inputGroup.appendChild(wrapper);
    }
    section.appendChild(inputGroup);
  }

  if (step.question) {
    const fieldset = document.createElement("fieldset");
    fieldset.className = "option-list";
    fieldset.removeAttribute("disabled");
    const legend = document.createElement("legend");
    legend.className = "sr-only";
    legend.textContent = step.title;
    fieldset.appendChild(legend);
    for (const item of step.options) {
      const option = typeof item === "string" ? { text: item } : item;
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "option-card";
      btn.innerHTML = `${option.text}<span class=indicator></span>`;
      btn.dataset.question = step.question;
      btn.dataset.value = option.text;
      if (step.id === "dor") btn.dataset.conditional = option.next;
      fieldset.appendChild(btn);
    }
    section.appendChild(fieldset);
  }

  if (step.id === "resultado") {
    const orb = document.createElement("div");
    orb.className = "svg-check";
    orb.id = "svgIconWrapper";
    orb.innerHTML = '<svg viewBox="0 0 50 50" aria-hidden="true"><circle cx="25" cy="25" r="22"></circle><path d="M14 25L22 33L36 17"></path></svg>';
    section.appendChild(orb);

    const title = document.createElement("h2");
    title.className = "step-title";
    title.id = "resultTitle";
    title.textContent = step.title;
    section.appendChild(title);

    const amount = document.createElement("div");
    amount.id = "resultAmountWrap";
    amount.innerHTML = `<p class=result-amount-label>${step.noteLabel}</p><div class=value-highlight id=finalValue>${step.value}</div><p class=result-note>${step.note}</p>`;
    section.appendChild(amount);

    const resultText = document.createElement("p");
    resultText.className = "subtitle";
    resultText.style.margin = "8px 0";
    resultText.id = "resultText";
    resultText.textContent = step.resultText;
    section.appendChild(resultText);

    const list = document.createElement("ul");
    list.className = "result-list";
    list.id = "resultList";
    step.summary.forEach((item) => {
      const li = document.createElement("li");
      li.textContent = item;
      list.append(li);
    });
    section.appendChild(list);

    const resultExtra = document.createElement("p");
    resultExtra.className = "subtitle";
    resultExtra.style.marginBottom = "24px";
    resultExtra.id = "resultExtra";
    resultExtra.textContent = step.resultExtra;
    section.appendChild(resultExtra);

    const inlineCountdown = document.createElement("p");
    inlineCountdown.className = "result-inline-countdown";
    inlineCountdown.id = "resultInlineCountdown";
    inlineCountdown.hidden = true;
    section.appendChild(inlineCountdown);

    const flowPanel = document.createElement("div");
    flowPanel.className = "result-flow-panel";
    flowPanel.id = "resultFlowPanel";
    flowPanel.hidden = true;
    flowPanel.innerHTML = `
      <p class=result-flow-kicker id=resultFlowKicker>Checkup concluido</p>
      <h3 class=result-flow-title id=resultFlowTitle>Sua analise foi liberada.</h3>
      <p class="subtitle result-flow-text" id=resultFlowText>Estamos preparando suas proximas instrucoes.</p>
      <ul class=result-flow-list id=resultFlowList></ul>
      <p class=result-flow-countdown id=resultFlowCountdown></p>
      <button class="btn-primary btn-lava result-flow-button" id=resultRedirectNow type=button hidden>Ir agora</button>
    `;
    section.appendChild(flowPanel);
  }

  const hasCTA = step.cta;
  const actionClass = hasCTA ? `${step.cta.class || ""}` : "";
  if (hasCTA) {
    const cta = document.createElement("button");
    cta.className = `btn-primary ${actionClass}`;
    cta.type = "button";
    if (step.cta.id) cta.id = step.cta.id;
    cta.textContent = step.cta.text;
    cta.addEventListener("click", (event) => {
      step.cta.action(event);
    });
    section.appendChild(cta);
  }

  if (step.id === "welcome") {
    const caption = document.createElement("p");
    caption.className = "subtitle";
    caption.style.fontSize = ".84rem";
    caption.textContent = "Leva menos de 2 minutos.";
    section.append(caption);
    section.style.textAlign = "center";
  }

  if (step.id === "identify") {
    const ctaWrap = section.lastElementChild;
    ctaWrap.classList.add("btn-primary");
  }

  renderedSteps.set(step.id, section);
  return section;
};

const renderStep = (stepId) => {
  const step = stepsById[stepId];
  if (!step) return null;
  const section = createQuestion(step);
  if (!section.parentNode) {
    quizContainer.appendChild(section);
    if (step.id === "identify") setupInputs();
  }
  return section;
};

const onOptionClick = (event) => {
  const button = event.target.closest(".option-card");
  if (!button || !quizContainer.contains(button)) return;
  const { question, value } = button.dataset;
  handleChoice(question, value, button.dataset.conditional || null, button);
};

quizContainer.addEventListener("click", onOptionClick);

const updateProgress = () => {
  const isStart = state.currentIndex === 0;
  const isEnd = state.currentIndex === state.flow.length - 1;
  if (isStart || isEnd) {
    progressContainer.style.opacity = "0";
    return;
  }
  progressContainer.style.opacity = "1";
  const progress = state.currentIndex / (state.flow.length - 2);
  progressFill.style.setProperty("--progress", progress);
};

const clearStepTransitionNames = (...elements) => {
  elements.forEach((element) => {
    if (element) element.style.viewTransitionName = "none";
  });
};

const focusStepInput = (inputTarget) => {
  if (!inputTarget || !shouldAutoFocusInputs) return;
  requestAnimationFrame(() => inputTarget.focus?.({ preventScroll: true }));
};

const accentStepFocus = (stepEl, inputTarget) => {
  window.clearTimeout(stepAccentTimer);

  const target = inputTarget?.closest(".input-field")
    || stepEl.querySelector("#resultCta")
    || stepEl.querySelector(".option-card")
    || stepEl.querySelector(".btn-primary");

  if (!target) return;

  target.classList.remove("motion-accent");
  requestAnimationFrame(() => target.classList.add("motion-accent"));
  stepAccentTimer = window.setTimeout(() => target.classList.remove("motion-accent"), 320);
};

const animateStepElements = (stepEl) => {
  if (!stepEl) return;

  const header = stepEl.querySelector(".step-header");
  const inputGroup = stepEl.querySelector(".input-group");
  const optionList = stepEl.querySelector(".option-list");
  const cta = stepEl.querySelector(".btn-primary");
  const helper = stepEl.querySelector(".helper-text");
  const subtitleEls = header ? [...header.querySelectorAll(".subtitle")] : [];
  const inputEls = inputGroup ? [...inputGroup.querySelectorAll(".input-field")] : [];
  const optionEls = optionList ? [...optionList.querySelectorAll(".option-card")] : [];
  const resultEls = [...stepEl.querySelectorAll("#resultTitle, #resultAmountWrap, #resultText, #resultList, #resultExtra, #resultCta")];

  const elements = [
    header?.querySelector(".micro-copy"),
    header?.querySelector(".step-title, .text-gradient"),
    subtitleEls,
    helper,
    inputEls,
    optionEls,
    cta,
    resultEls,
  ].flat().filter(Boolean);

  const durations = { micro: ".34s", title: ".38s", subtitle: ".42s", input: ".46s", option: ".5s", cta: ".54s", result: ".48s" };
  const baseDelay = .04;
  let delay = baseDelay;

  const getDuration = (el) => {
    if (el.classList.contains("micro-copy")) return durations.micro;
    if (el.classList.contains("step-title") || el.classList.contains("text-gradient")) return durations.title;
    if (el.classList.contains("subtitle")) return durations.subtitle;
    if (el.classList.contains("input-field")) return durations.input;
    if (el.classList.contains("option-card")) return durations.option;
    if (el.classList.contains("btn-primary")) return durations.cta;
    if (el.matches?.("#resultTitle, #resultAmountWrap, #resultText, #resultList, #resultExtra, #resultCta")) return durations.result;
    return durations.subtitle;
  };

  elements.forEach((el) => {
    el.style.opacity = "0";
    el.style.animation = `welcome-element-in ${getDuration(el)} var(--motion-enter) forwards`;
    el.style.animationDelay = `${delay}s`;
    delay += .04;
  });
};

const showStep = (currentId, nextId, onShown = null) => {
  const currentEl = document.getElementById(currentId);
  const nextEl = renderStep(nextId);
  if (!currentEl || !nextEl) return;
  const inputTarget = nextEl.querySelector("input");

  const finalizeStepEntry = () => {
    nextEl.classList.remove("step-enter-pending");
    if (typeof onShown === "function") onShown(nextEl);
    animateStepElements(nextEl);
    accentStepFocus(nextEl, inputTarget);
    focusStepInput(inputTarget);
  };

  const commitStepChange = () => {
    // The new snapshot must contain only one named transition target.
    currentEl.style.viewTransitionName = "none";
    currentEl.classList.remove("active");
    nextEl.classList.add("step-enter-pending");
    nextEl.classList.add("active");
    nextEl.style.viewTransitionName = "quiz-step";
    state.currentIndex += 1;
    syncStepViewportState(nextId);
    updateProgress();
  };

  if (!supportsViewTransitions) {
    commitStepChange();
    finalizeStepEntry();
    return;
  }

  root.dataset.stepTransition = nextId === "welcome" ? "welcome" : "flow";
  currentEl.style.viewTransitionName = "quiz-step";
  nextEl.style.viewTransitionName = "none";

  const transition = document.startViewTransition(() => {
    commitStepChange();
  });

  transition.finished.finally(() => {
    delete root.dataset.stepTransition;
    clearStepTransitionNames(currentEl, nextEl);
    finalizeStepEntry();
  });
};

const nextStep = () => {
  const currentId = state.flow[state.currentIndex];
  const nextId = state.flow[state.currentIndex + 1];
  if (!nextId) return;

  if (nextId === "identify") {
    Mascot.setEmotion("");
    Mascot.say("Primeiro, preciso do seu nome e do seu WhatsApp.", 3500);
  }
  if (nextId === "cargo") {
    const firstName = (state.answers.nome || "").split(" ")[0];
    Mascot.setEmotion("happy");
    Mascot.say(firstName ? `${firstName}, agora quero entender sua empresa.` : "Agora quero entender sua empresa.", 3200);
  }
  if (nextId === "volume") {
    Mascot.setEmotion("");
    Mascot.say("Agora preciso de dois numeros rapidos.", 3000);
  }
  if (nextId === "dor") {
    Mascot.setEmotion("think");
    Mascot.say("Aqui costuma aparecer onde o dinheiro esta escapando.", 3800);
  }
  if (nextId === "historico") {
    Mascot.setEmotion("");
    Mascot.say("Ultima parte. Isso ajuda a saber se da para olhar seu caso com calma.", 4200);
  }
   showStep(currentId, nextId, () => {
     if (nextId !== "resultado") return;

     prepareResult();
      if (state.qualified) {
        animateScoreDisplay(state.estimatedValue);
        Mascot.setEmotion("happy");
        Mascot.say("Sua analise foi liberada.", 2200);
        queueResultFlow(() => startQualifiedClosingFlow("auto"), 1600);
      } else {
        Mascot.setEmotion("think");
        Mascot.say("Recebemos suas respostas. Vamos analisar seu caso.", 2600);
      }
    });
};

const setupInputs = () => {
  if (identifyInputsReady) return;

  const name = inputName();
  const phone = inputPhone();
  if (!name || !phone) return;

  [name, phone].forEach((input) => {
    input.addEventListener("focus", () => Mascot.setEmotion("look-left"));
    input.addEventListener("input", () => Mascot.setEmotion("typing"));
    input.addEventListener("blur", () => Mascot.setEmotion(""));
  });

  phone.addEventListener("input", (event) => {
    const match = event.target.value.replace(/\D/g, "").match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    event.target.value = !match[2] ? match[1] : `(${match[1]}) ${match[2]}${match[3] ? `-${match[3]}` : ""}`;
  });

  ["inputName", "inputPhone"].forEach((id) => {
    document.getElementById(id).addEventListener("keydown", (event) => {
      if (event.key === "Enter") validateIdentify();
    });
  });

  identifyInputsReady = true;
};

function validateIdentify() {
  const nameValue = inputName().value.trim();
  const phoneValue = inputPhone().value.replace(/\D/g, "");
  const hasValidName = nameValue.replace(/[^A-Za-zÀ-ÿ]/g, "").length >= 2;
  const hasValidPhone = phoneValue.length >= 10;

  document.getElementById("nameError").classList.toggle("show", !hasValidName);
  document.getElementById("phoneError").classList.toggle("show", !hasValidPhone);
  if (!hasValidName || !hasValidPhone) {
    Mascot.setEmotion("think");
    Mascot.say("Me passa seu nome e um WhatsApp valido para eu continuar.", 3000);
    return;
  }

  state.answers.nome = nameValue;
  state.answers.whatsapp = inputPhone().value;
  nextStep();
}

const handleChoice = (question, value, conditionalId = null, button = null) => {
  if (button) {
    const siblings = button.parentElement.querySelectorAll(".option-card");
    siblings.forEach((card) => card.classList.remove("selected"));
    button.classList.add("selected");
  }
  state.answers[question] = value;

  if (question === "dor") {
    state.flow = state.flow.filter((stepId) => !conditionalSteps.includes(stepId));
    delete state.answers.detalhe_dor;
    const dorIndex = state.flow.indexOf("dor");
    if (conditionalId) state.flow.splice(dorIndex + 1, 0, conditionalId);
  }

  if (question === "faturamento" && value === "Acima de R$ 300 mil") {
    Mascot.setEmotion("shock");
    Mascot.say("Entendi. Sua empresa ja tem bastante movimento.", 2200);
  }
  if (question === "volume" && value === "Mais de 1000") {
    Mascot.setEmotion("shock");
    Mascot.say("Com esse volume, pequenos atrasos podem custar caro.", 2600);
  }
  if (question === "detalhe_dor") {
    Mascot.setEmotion("think");
    Mascot.say("Agora ficou mais claro onde pode estar o problema.", 2400);
  }

  setTimeout(nextStep, 320);
};

const isQualified = () => {
  let signals = 0;
  if (["Sim, esta tudo organizado", "Tenho boa parte"].includes(state.answers.historico)) signals += 1;
  if (["Sim", "Consigo mandar logo depois"].includes(state.answers.envio_info)) signals += 1;
  if (["Agora", "Ainda esta semana", "Ainda este mes"].includes(state.answers.urgencia)) signals += 1;
  if (["Sim", "Eu participo da decisao"].includes(state.answers.decisao)) signals += 1;
  return signals >= 3;
};

const calculateRecoverableValue = () => {
  const volumeMap = { "Ate 30": 20, "De 31 a 100": 65, "De 101 a 300": 200, "De 301 a 1000": 650, "Mais de 1000": 1200 };
  const valueMap = { "Ate R$ 100": 80, "De R$ 101 a R$ 300": 200, "De R$ 301 a R$ 800": 550, "De R$ 801 a R$ 2.000": 1300, "Acima de R$ 2.000": 3000 };
  const lossFactorMap = {
    "Demoram para responder e a pessoa desiste": .18,
    "A pessoa pede informacoes, mas depois some": .12,
    "Clientes antigos deixam de comprar e ninguem vai atras": .1,
    "Fora do horario quase ninguem responde": .14,
    "A agenda fica com horarios vazios": .16,
    "Esta tudo meio baguncado e dificil de acompanhar": .11,
  };
  const urgencyFactorMap = { Agora: 1, "Ainda esta semana": .92, "Ainda este mes": .82, "So quero entender melhor por enquanto": .68 };

  const volume = volumeMap[state.answers.volume] || 30;
  const averageValue = valueMap[state.answers.valor_medio] || 100;
  const lossFactor = lossFactorMap[state.answers.dor] || .1;
  const urgencyFactor = urgencyFactorMap[state.answers.urgencia] || .7;
  return Math.max(3000, Math.round(volume * averageValue * lossFactor * .45 * urgencyFactor / 100) * 100);
};

const prepareResult = () => {
  resetResultClosingState();
  state.qualified = isQualified();
  state.estimatedValue = calculateRecoverableValue();

  const nodes = getResultNodes();
  const firstName = (state.answers.nome || "").split(" ")[0];

  if (!nodes?.resultTitle || !nodes.resultText || !nodes.resultExtra || !nodes.resultList || !nodes.resultCta || !nodes.amountWrap) return;

  if (state.qualified) {
    nodes.resultTitle.textContent = firstName ? `${firstName}, sua analise foi liberada.` : "Sua analise foi liberada.";
    nodes.resultText.textContent = "Pelas suas respostas, encontramos sinais de que sua empresa pode estar deixando dinheiro na mesa.";
    nodes.resultExtra.textContent = "Daqui a pouco voce sera redirecionado para escolher o melhor horario de diagnostico.";
    nodes.resultList.style.display = "flex";
    nodes.amountWrap.style.display = "block";
    nodes.resultCta.textContent = "Continuar agora";
    if (nodes.inlineCountdown) nodes.inlineCountdown.hidden = true;
    setResultIcon("qualified");
  } else {
    nodes.resultTitle.textContent = "Recebemos suas respostas.";
    nodes.resultText.textContent = "Vimos alguns sinais importantes no seu caso e agora vamos analisar seu contexto com mais cuidado.";
    nodes.resultExtra.textContent = "Se fizer sentido para o seu momento, nossa equipe vai te ligar no numero informado para explicar o proximo passo.";
    nodes.resultList.style.display = "none";
    nodes.amountWrap.style.display = "none";
    nodes.resultCta.textContent = "Finalizar analise";
    if (nodes.inlineCountdown) nodes.inlineCountdown.hidden = true;
    setResultIcon("analysis");
  }
};

const animateScoreDisplay = (targetValue) => {
  const valueEl = document.getElementById("finalValue");
  if (!valueEl) return;
  const startTime = performance.now();
  const duration = 1800;
  const updateCounter = (currentTime) => {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easeOut = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
    valueEl.textContent = `R$ ${Math.floor(easeOut * targetValue).toLocaleString("pt-BR")}`;
    if (progress < 1) requestAnimationFrame(updateCounter);
  };
  requestAnimationFrame(updateCounter);
};

function handleResultCTA() {
  if (state.qualified) {
    startQualifiedClosingFlow("manual");
    return;
  }
  startAnalysisClosingFlow("manual");
}

window.__startQuiz = () => {
  if (state.currentIndex !== 0) return;
  nextStep();
};

renderStep(state.flow[0]);
const welcomeStep = renderedSteps.get("welcome");
if (welcomeStep) welcomeStep.classList.add("active");
syncStepViewportState(state.flow[0]);
updateProgress();
