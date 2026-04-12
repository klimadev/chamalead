# Design System: ChamaLead Landing Page

**Base analisada:** `index.php` + `modules/0.overlays-e-progress.html` a `modules/9.footer.html` (sem quiz)

## 1. Tema Visual e Atmosfera

O produto adota uma est\u00e9tica **dark premium**, com linguagem visual de alta convers\u00e3o para oferta digital. A atmosfera \u00e9:

- **Noturna e tecnol\u00f3gica**: fundos quase pretos, brilhos quentes e camadas transl\u00facidas.
- **Energ\u00e9tica e urgente**: acentos flamejantes (laranja/vermelho), termos de escassez e indicadores din\u00e2micos.
- **Imersiva e cin\u00e9tica**: blobs animados, spotlight, progress bar, ping/glow e simula\u00e7\u00f5es de conversa.
- **Confi\u00e1vel e orientada a resultado**: prova social, m\u00e9tricas, fluxo de caso real e refor\u00e7os de seguran\u00e7a/LGPD.

Em termos sem\u00e2nticos, o visual comunica: **"autom\u00e7\u00e3o agressiva em performance, com controle e seguran\u00e7a"**.

## 2. Paleta de Cores e Pap\u00e9is

### 2.1 Cores de marca (Tailwind custom)

- **Flame Core** (`#f97316` / `flame-500`): cor principal de marca para CTAs, \u00edcones de destaque e estados de foco visual.
- **Flame Action** (`#ea580c` / `flame-600`): intensidade de a\u00e7\u00e3o para bot\u00f5es prim\u00e1rios e gradientes.
- **Flame Deep** (`#c2410c` / `flame-700`): contraste em hover e elementos de profundidade.
- **Ember Accent** (`#ef4444` / `ember-500`): componente quente secund\u00e1rio para gradientes e energia visual.
- **Ember Action** (`#dc2626` / `ember-600`): fechamento de gradiente para CTA e barras de progresso.

### 2.2 Escala neutra (Tailwind custom)

- **Night Base** (`#0a0a0a` / `dark`): fundo-base geral.
- **Night Surface** (`#111111` / `dark-900`): se\u00e7\u00f5es de superf\u00edcie secund\u00e1ria.
- **Night Elevated** (`#1a1a1a` / `dark-800`): camadas elevadas (headers de chat, pain\u00e9is).
- **Night Overlay** (`#262626` / `dark-700`): bordas internas e estados de apoio.

### 2.3 Cores funcionais complementares

- **Success Green** (`#22c55e` aprox. via classes `green-500`): confirma\u00e7\u00e3o, checks, status online, prova de seguran\u00e7a.
- **Info Blue** (`#3b82f6` aprox. via classes `blue-500`): recursos t\u00e9cnicos e sinais de integra\u00e7\u00e3o.
- **Warning Yellow** (`#eab308` aprox. via classes `yellow-500`): alertas de custo/oportunidade.
- **Risk Red** (`#ef4444` aprox. via classes `red-500`): perdas, dor e pontos cr\u00edticos.
- **Text Zinc Scale** (`zinc-300` a `zinc-600`): hierarquia textual em ambiente escuro.

### 2.4 Regras sem\u00e2nticas de uso

- **Prim\u00e1rio comercial**: gradiente `flame-600 -> ember-600` para CTA principal.
- **Apoio de leitura**: texto principal em branco, secund\u00e1rio em `zinc-400`, metadados em `zinc-500/600`.
- **Fundo com profundidade**: nunca flat puro; sempre com gradiente radial, blur ou textura.
- **Estados de confian\u00e7a**: verde para valida\u00e7\u00f5es e selos; manter contraste alto.

## 3. Tipografia

### 3.1 Fam\u00edlias

- **Display:** `Space Grotesk` (`font-display`) para headlines, n\u00fameros de impacto e pre\u00e7os.
- **Texto corrido/UI:** `Inter` (`font-sans`) para par\u00e1grafos, labels, navega\u00e7\u00e3o e formul\u00e1rios.

### 3.2 Hierarquia

- **H1 hero:** `text-4xl` at\u00e9 `lg:text-6xl`, `font-black`, contraste m\u00e1ximo.
- **H2 de se\u00e7\u00e3o:** `text-4xl`/`md:text-5xl`, `font-display font-bold`.
- **Subheads e blocos-chave:** `text-xl`/`text-2xl`, peso `semibold`/`bold`.
- **Corpo principal:** `text-lg` com `leading-relaxed` para legibilidade.
- **Meta/apoio:** `text-sm` e `text-xs` com `tracking-widest` em chips e overlines.

### 3.3 Tom tipogr\u00e1fico

- T\u00edtulos em linguagem assertiva e orientada a resultado.
- Uso frequente de **quebra de linha intencional** para ritmo comercial.
- Caps e tracking amplo para microcopy de urg\u00eancia (ex.: vagas, entrega, prova).

## 4. Forma, Geometria e Profundidade

### 4.1 Curvatura

- **P\u00edlula:** `rounded-full` em badges, chips, indicadores e status.
- **Curva suave padr\u00e3o:** `rounded-lg` e `rounded-xl` para bot\u00f5es e inputs.
- **Curva generosa:** `rounded-2xl` e `rounded-3xl` para cards, pain\u00e9is e blocos de destaque.
- **Curva extrema contextual:** mockup de smartphone (`rounded-[38px]` a `rounded-[54px]`) para realismo.

### 4.2 Profundidade

- Estilo principal em **glassmorphism escuro** (vidro escuro + bordas transl\u00facidas).
- Sombras quentes (`shadow-flame-500/30`) para elevar CTAs.
- Blurs amplos de fundo (100-150px) para sensa\u00e7\u00e3o de ambiente vivo.
- Bordas com opacidade baixa (`border-white/10`) para separar camadas sem ru\u00eddo.

### 4.3 Texturas e efeitos de fundo

- Spotlight global e gradientes radiais.
- Noise overlay + dot-grid para evitar superf\u00edcie plana.
- Mesh blobs animados para movimento org\u00e2nico de marca.

## 5. Motion Language

O sistema usa anima\u00e7\u00e3o como argumento de produto (agilidade/autom\u00e7\u00e3o), n\u00e3o como enfeite.

- **Anima\u00e7\u00f5es utilit\u00e1rias:** `slide-up`, `scale-in`, reveal progressivo por scroll.
- **Anima\u00e7\u00f5es ambientais:** `mesh-1/2/3`, `float`, `float-reverse`, ring spins no hero.
- **Anima\u00e7\u00f5es de a\u00e7\u00e3o:** glow e hover-scale em bot\u00f5es, shimmer em superf\u00edcies de destaque.
- **Anima\u00e7\u00f5es de status:** `animate-ping`, `animate-pulse`, timeline ativa, typing indicator.

Diretriz: transi\u00e7\u00f5es curtas (300-600ms), loops longos para fundo (15-25s), sem comprometer leitura.

## 6. Componentes (Padr\u00f5es Sem\u00e2nticos)

### 6.1 Navega\u00e7\u00e3o

- Navbar fixa, transl\u00facida no scroll, com foco em CTA "Quero Automatizar".
- Menu desktop minimalista + menu mobile com fundo dark e blur.
- Logotipo composto por \u00edcone flame + wordmark bicolor (branco/laranja).

### 6.2 CTA Prim\u00e1rio

- Bot\u00e3o gradiente quente, texto branco em bold, \u00edcone de seta com micro-movimento.
- Estado hover com aumento de sombra e escala leve.
- Repeti\u00e7\u00e3o consistente em toda a p\u00e1gina para refor\u00e7o de convers\u00e3o.

### 6.3 Cards de conte\u00fado

- Cart\u00f5es de dor, solu\u00e7\u00e3o, depoimento e m\u00e9trica usam base `glass` + curvas amplas.
- Estrutura padr\u00e3o: \u00edcone em container tonal + t\u00edtulo forte + descri\u00e7\u00e3o + linha de apoio.
- Card hover discreto para feedback de interatividade.

### 6.4 Simula\u00e7\u00f5es de produto

- Hero com mockup iPhone detalhado simulando interface WhatsApp.
- Caso real com chat ao vivo, indicador de digita\u00e7\u00e3o e confirma\u00e7\u00e3o de pagamento.
- Objetivo: reduzir abstra\u00e7\u00e3o e mostrar prova operacional da automa\u00e7\u00e3o.

### 6.5 Formul\u00e1rio de captura

- Bloco principal com tratamento premium (`glass-orange`, borda superior de marca).
- Inputs amplos com \u00edcone contextual, r\u00f3tulos claros e contraste alto.
- Radios de faturamento em formato card selecion\u00e1vel (`peer-checked`).
- Feedback de envio dedicado (`leadFormFeedback`) + estados de loading no bot\u00e3o.

### 6.6 FAQ Accordion

- Pain\u00e9is `glass` com toggle por bot\u00e3o e \u00edcone `chevron` rotacion\u00e1vel.
- Conte\u00fado oculto/expandido com foco em clareza e obje\u00e7\u00f5es comerciais.

### 6.7 Footer

- Estrutura em colunas com refor\u00e7o de marca, links de navega\u00e7\u00e3o e compliance.
- \u00cdcones sociais em bot\u00f5es circulares com hover para flame.

## 7. Layout e Espa\u00e7amento

- **Container padr\u00e3o:** `max-w-7xl` com `px-6` para quase todas as se\u00e7\u00f5es.
- **Ritmo vertical:** blocos majoritariamente em `py-24` para respira\u00e7\u00e3o consistente.
- **Grade responsiva:** altern\u00e2ncia entre 1 coluna no mobile e 2-3 colunas em `md/lg`.
- **Foco de leitura:** cabe\u00e7alhos centralizados nas aberturas; conte\u00fado detalhado em grids laterais.
- **Camadas:** fundo (efeitos) -> conte\u00fado (z-10) -> elementos fixos (navbar/progress).

## 8. Padr\u00f5es de Conte\u00fado e Convers\u00e3o

- Estrutura narrativa em funil: problema -> solu\u00e7\u00e3o -> prova -> capta\u00e7\u00e3o -> obje\u00e7\u00f5es.
- Uso de n\u00fameros concretos e recortes temporais (48h, 24/7, +340%, 98%).
- Microcopy de urg\u00eancia e seguran\u00e7a distribu\u00edda ao longo da jornada.
- Refor\u00e7o de LGPD e privacidade no hero, formul\u00e1rio e rodap\u00e9.

## 9. Responsividade

- Navega\u00e7\u00e3o com comportamento dedicado para mobile (`md:hidden`).
- Escalas tipogr\u00e1ficas adaptativas (`md`, `lg`) para manter impacto sem quebrar legibilidade.
- CTAs empilhados em telas pequenas e horizontais em telas maiores.
- Grids de cards e m\u00e9tricas colapsam progressivamente para 1 coluna.

## 10. Princ\u00edpios para Novas Telas

Para extens\u00f5es coerentes com este design system:

1. Manter base dark com acentos quentes (`flame/ember`) como assinatura de marca.
2. Preservar contraste forte de leitura (texto branco/zinc sobre fundos escuros).
3. Priorizar blocos em vidro escuro com borda transl\u00facida e curvas amplas.
4. Usar anima\u00e7\u00f5es com intencionalidade funcional (status, progresso, foco comercial).
5. Repetir padr\u00f5es de CTA gradiente para continuidade de jornada.
6. Incluir sinais de confian\u00e7a (compliance, prova social, valida\u00e7\u00f5es visuais).
7. Sustentar narrativa orientada a convers\u00e3o em toda nova se\u00e7\u00e3o.

## 11. Invent\u00e1rio de M\u00f3dulos Analisados

- `modules/0.overlays-e-progress.html`: spotlight do mouse, barra de progresso e embers flutuantes.
- `modules/1.navbar.html`: navega\u00e7\u00e3o fixa e CTA principal.
- `modules/2.hero.html`: proposta de valor, mockup WhatsApp e efeitos imersivos.
- `modules/3.problema.html`: dores de mercado, custos e argumento de inefici\u00eancia.
- `modules/4.solucao.html`: stack de oferta, benef\u00edcios e pricing box.
- `modules/5.case.html`: timeline de caso real + chat demonstrativo.
- `modules/6.prova-social.html`: m\u00e9tricas e depoimentos.
- `modules/7.formulario.html`: captura de lead com prova de seguran\u00e7a.
- `modules/8.faq.html`: obje\u00e7\u00f5es e respostas.
- `modules/9.footer.html`: fechamento institucional e compliance.
