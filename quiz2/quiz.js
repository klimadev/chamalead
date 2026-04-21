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
    helper: "E por esse numero que vamos falar com voce, caso sua analise seja liberada.",
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

const state = { flow: [...baseFlow], currentIndex: 0, answers: {}, qualified: false, estimatedValue: 0 };

const quizContainer = document.getElementById("quizContainer");
const progressContainer = document.getElementById("progress-container");
const progressFill = document.getElementById("progress-fill");
const inputPhone = () => document.getElementById("inputPhone");
const inputName = () => document.getElementById("inputName");

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

document.getElementById("mascotBody").addEventListener("click", () => Mascot.poke());
setTimeout(() => Mascot.say("Vou te fazer perguntas simples para entender sua empresa.", 4200), 900);

const createQuestion = (step) => {
  const section = document.createElement("section");
  section.className = `step${step.id === "welcome" ? " active" : ""}`;
  section.id = step.id;
  section.setAttribute("aria-live", "polite");

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
    const list = document.createElement("div");
    list.className = "option-list";
    for (const item of step.options) {
      const option = typeof item === "string" ? { text: item } : item;
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "option-card";
      btn.innerHTML = `${option.text}<span class=indicator></span>`;
      btn.dataset.question = step.question;
      btn.dataset.value = option.text;
      if (step.id === "dor") btn.dataset.conditional = option.next;
      list.appendChild(btn);
    }
    section.appendChild(list);
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
  }

  const hasCTA = step.cta;
  const actionClass = hasCTA ? `${step.cta.class || ""}` : "";
  if (hasCTA) {
    const cta = document.createElement("button");
    cta.className = `btn-primary ${actionClass}`;
    cta.type = "button";
    if (step.cta.id) cta.id = step.cta.id;
    cta.textContent = step.cta.text;
    cta.addEventListener("click", step.cta.action);
    section.appendChild(cta);
  }

  if (step.id === "welcome") {
    const caption = document.createElement("p");
    caption.className = "subtitle";
    caption.style.fontSize = ".84rem";
    caption.textContent = "Leva menos de 2 minutos.";
    section.append(caption);
    section.style.textAlign = "center";
    const glow = document.createElement("div");
    glow.className = "glow-orb";
    section.insertBefore(glow, section.firstChild);
  }

  if (step.id === "identify") {
    const ctaWrap = section.lastElementChild;
    ctaWrap.classList.add("btn-primary");
  }

  return section;
};

for (const step of steps) quizContainer.appendChild(createQuestion(step));
const allSections = [...document.querySelectorAll(".step")];

for (const section of allSections) {
  const cards = section.querySelectorAll(".option-card");
  cards.forEach((button) => {
    button.addEventListener("click", () => {
      const { question, value } = button.dataset;
      handleChoice(question, value, button.dataset.conditional || null, button);
    });
  });
}

const updateProgress = () => {
  const isStart = state.currentIndex === 0;
  const isEnd = state.currentIndex === state.flow.length - 1;
  if (isStart || isEnd) {
    progressContainer.style.opacity = "0";
    return;
  }
  progressContainer.style.opacity = "1";
  progressFill.style.width = `${(state.currentIndex / (state.flow.length - 2)) * 100}%`;
};

const showStep = (currentId, nextId) => {
  const currentEl = document.getElementById(currentId);
  const nextEl = document.getElementById(nextId);
  currentEl.classList.remove("active");
  currentEl.classList.add("exit-up");
  state.currentIndex += 1;
  nextEl.classList.add("active");
  nextEl.classList.remove("exit-up");
  updateProgress();
  const inputTarget = nextEl.querySelector("input");
  if (inputTarget) setTimeout(() => inputTarget.focus(), 350);
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
  if (nextId === "resultado") {
    prepareResult();
  }

  showStep(currentId, nextId);

  if (nextId === "resultado") {
    setTimeout(triggerFireworks, 300);
    if (state.qualified) {
      animateScoreDisplay(state.estimatedValue);
      Mascot.setEmotion("happy");
      Mascot.say("Sua analise foi liberada.", 0);
    } else {
      Mascot.setEmotion("");
      Mascot.say("Recebemos suas respostas.", 0);
    }
  }
};

const setupInputs = () => {
  const name = inputName();
  const phone = inputPhone();
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
  state.qualified = isQualified();
  state.estimatedValue = calculateRecoverableValue();

  const amountWrap = document.getElementById("resultAmountWrap");
  const resultTitle = document.getElementById("resultTitle");
  const resultText = document.getElementById("resultText");
  const resultExtra = document.getElementById("resultExtra");
  const resultList = document.getElementById("resultList");
  const resultCta = document.getElementById("resultCta");
  const firstName = (state.answers.nome || "").split(" ")[0];

  if (state.qualified) {
    resultTitle.textContent = firstName ? `${firstName}, sua analise foi liberada.` : "Sua analise foi liberada.";
    resultText.textContent = "Pelas suas respostas, encontramos sinais de que sua empresa pode estar deixando dinheiro na mesa.";
    resultExtra.textContent = "Na reuniao, vamos olhar seu caso com voce, entender onde estao as perdas e mostrar o caminho mais rapido para corrigir isso.";
    resultList.style.display = "flex";
    amountWrap.style.display = "block";
    resultCta.textContent = "Escolher meu horario";
  } else {
    resultTitle.textContent = "Recebemos suas respostas.";
    resultText.textContent = "Vimos alguns sinais importantes no seu caso e nossa equipe pode te chamar para entender melhor sua situacao.";
    resultExtra.textContent = "Se fizer sentido, vamos falar com voce pelo WhatsApp e explicar o proximo passo.";
    resultList.style.display = "none";
    amountWrap.style.display = "none";
    resultCta.textContent = "Finalizar";
  }
};

const animateScoreDisplay = (targetValue) => {
  const valueEl = document.getElementById("finalValue");
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
    window.alert("Abrir agenda para escolher o horario.");
    return;
  }
  window.alert("Tudo certo. Se fizer sentido, nossa equipe pode falar com voce pelo WhatsApp.");
}

const triggerFireworks = () => {
  const container = document.getElementById("svgIconWrapper");
  for (let index = 0; index < 40; index += 1) {
    const spark = document.createElement("div");
    spark.className = "spark";
    container.appendChild(spark);
    const angle = Math.random() * Math.PI * 2;
    const velocity = 60 + Math.random() * 120;
    const tx = Math.cos(angle) * velocity;
    const ty = Math.sin(angle) * velocity;
    spark.animate(
      [
        { transform: "translate(-50%, -50%) scale(1)", opacity: 1, backgroundColor: "#FF8A00" },
        { transform: `translate(calc(-50% + ${tx}px), calc(-50% + ${ty}px)) scale(0)`, opacity: 0, backgroundColor: "#FF0000" },
      ],
      { duration: 800 + Math.random() * 600, easing: "cubic-bezier(0.25, 1, 0.5, 1)", fill: "forwards" },
    );
    setTimeout(() => spark.remove(), 1500);
  }
};

setupInputs();
updateProgress();
