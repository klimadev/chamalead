# AGENTS.md - UI/UX Principles

## Diretrizes para LLMs (operacional)

- Priorize instruções objetivas, curtas e acionáveis.
- Aplique esta ordem de decisão: Acessibilidade e semântica > Performance e Core Web Vitals > Experiência visual > Conveniência.
- Sempre que houver conflito, escolha a regra mais restritiva (sem exceção).
- Em mudanças de HTML/CSS/JS, entregue sempre:
  - impacto no comportamento
  - impacto em performance e acessibilidade
  - risco e rollback
- Mantenha mudanças pequenas e locais. Evite refatoração especulativa.
- Use linguagem padrão e estrutura consistente para facilitar automação (verbos + alvo + motivo).

## Padrões de Performance LP2 (mandatório)

- Remover dependências de CSS/JS por CDN em produção (ex.: Tailwind CDN).
- Não depender de framework utilitário no runtime; manter CSS próprio versionado no projeto.
- Injetar CSS crítico inline no `<head>`, com prioridade para navegação e seção hero.
- Evitar fontes externas pesadas (Google Fonts); usar stack nativa do sistema.
- Preservar estilo visual atual (igual ou superior), sem regressão de hierarquia, contraste e legibilidade.
- Validar meta de carregamento instantâneo: perseguir `FCP < 1s` e registrar regressão quando ultrapassar baseline.
- Garantir contraste AA mínimo em textos, labels e navegação, especialmente em superfícies escuras e estados secundários.

## Fundamentos (Norman, Apple, Dieter Rams)

- **Affordance**: Elementos devem mostrar como usá-los. Botões parecem clicáveis, campos parecem editáveis.
- **Affordances perceptivas imediatas**: Cor, forma, posição, movimento. Se precisa explicar, falhou.
- **Feedback instantâneo**: Toda ação tem resposta visual <100ms.
- **Less is more**: Remove o que não é essencial. Espaço negativo é recurso.
- **Icon first**: Preferir ícone + label short. Ícone alone só se universal (✓, +, −, ←).
- **Convenção sobre configuração**: Padrões visuais consistentes evitam customização desnecessária.

## UI/UX Moderno

- Dark premium operacional
- Bordas sutis (1px), superfícies grafite (#1a1a1a), brilho localizado
- Densidade alta com respiro suficiente para toque (min 44px touch targets)
- Tipografia menor e mais densa (14px base, não 16px)
- Dock inferior não corta em safe area mobile
- Kanban legível em telas pequenas (scroll horizontal se necessário)
- Prioridade visual por tamanho, não cor apenas

## Vanguarda Tecnológica (2024/2025/2026)

### Filosofia de Design

- Mantenha Don Norman e Nielsen como base, mas trate a interface como um sistema vivo.
- Adote `Digital Native Motion`: micro-interações devem comunicar estado, intenção e continuidade.
- A UI não deve apenas funcionar; deve parecer rápida, precisa e animada com elegância.
- Evite movimentos genéricos ou decorativos. Toda animação precisa existir para orientar percepção.

### Padrão de Transições

- `View Transitions API` é o padrão mandatório para mudanças de tema e navegação de rotas.
- Proibido usar overlays, máscaras artificiais, clones do DOM ou truques legados para transições de estado.
- **Spatial UI**: Floating docks, sidebars e drawers que flutuam sobre o conteúdo são permitidos se usarem APIs nativas (`backdrop-filter`, `transform`, `opacity`) sem bibliotecas externas.
- Não introduzir camadas de suporte visuais quando a transição puder ser resolvida nativamente.

### Desempenho Perceptivo e Refinamento

- Animações devem parecer físicas, não robóticas.
- Use curvas contemporâneas e naturais, como `cubic-bezier(0.4, 0, 0.2, 1)` ou equivalentes mais refinadas quando justificadas.
- Duração padrão de motion: entre `300ms` e `600ms`.
- Animações pesadas, como `clip-path`, devem ser otimizadas para manter `60fps` e aproveitar camadas de renderização do navegador.
- Se uma transição comprometer fluidez, simplifique a composição antes de adicionar complexidade visual.

### Métricas e Performance Mensurável (PageSpeed)

- Priorize sempre melhoria real de Core Web Vitals em todas as telas críticas, com auditoria contínua no fluxo de entrega:
  - `FCP` (First Contentful Paint)
  - `LCP` (Largest Contentful Paint)
  - `INP` (Interaction to Next Paint)
  - `CLS` (Cumulative Layout Shift)
  - `TTFB` (Time to First Byte)
  - `TBT` (Total Blocking Time)
  - `Speed Index`
- Performance sempre em primeiro lugar: meta mínima de base `LCP <= 2,5s`, `INP <= 200ms`, `CLS <= 0,1`, `TBT <= 200ms`, `FCP o mais baixo possível`, sem quebrar acessibilidade.
- No CI/revisão, priorize páginas com notas altas em:
  - `Performance`
  - `Acessibilidade`
  - `Boas Práticas`
  - `SEO`
- Aplique otimizações nativas no build: preload de recursos críticos, `preconnect`/`dns-prefetch`, compressão, minificação, bundling leve, cache-control correto e imagens responsivas com `srcset`, `sizes`, `loading="lazy"`, dimensões explícitas e formatos modernos (`AVIF/WebP`).
- Exija validação regular com PageSpeed/Lighthouse e registre regressão quando qualquer métrica cair abaixo do baseline acordado.

### Modern Web Stack

- Priorize APIs nativas e CSS moderno antes de bibliotecas de terceiros.
- Prefira `Container Queries`, `:has()` e CSS Nesting quando melhorarem clareza ou adaptabilidade.
- Mantenha o código lean, direto e focado em performance percebida.
- Evite dependências extras para efeitos que o navegador já entrega com qualidade superior.

### Glassmorphism e Floating Dock

- **Floating Glass Dock**: Sidebars e docks flutuantes devem:
  - Usar `backdrop-filter: blur(12px)` (ou valor similar) para efeito glass
  - Bordas sutis (`1px solid rgba(255,255,255,0.1)` no tema escuro)
  - Border-radius generoso (24px+) para visual premium
  - Margem da borda da tela (`left/top/bottom: 16px` ou similar)
  - `transform-gpu` e `will-change` para aceleração de hardware
- **Zero-Reflow**: Componentes que expandem no hover (sidebars, menus) devem usar `position: absolute/fixed` para sobrepor conteúdo sem redimensionar a área principal
- **Foco visual com `:has()`**: Use a pseudo-classe `:has()` para aplicar estados de foco (blur, escurecimento) no conteúdo principal quando componentes flutuantes estiverem ativos

## Código HTML Limpo

### Sintaxe Concisa

- Prefira **auto-fechamento** em tags que não precisam de closing: `<img>`, `<input>`, `<br>`, `<hr>`, `<link>`, `<meta>`
- Use **atributos booleanos**: `<input disabled>` em vez de `disabled="true"`
- **Omita quotes** em atributos quando o valor é simples: `<div class=foo>`
- Quebre linhas apenas quando melhora legibilidade (listas de atributos longos, blocos de texto)

### Estrutura Lean

- Cada tag indentation = 2 espaços (não tabs)
- Separe blocos lógicos com 1 linha em branco (máximo 1)
- Evite `div`/span wrappers desnecessários — use CSS quando possível
- Extraia componentes repetidos para Includes ou Web Components

### Limite de Linhas

- **Target**: ≤ 700 linhas por arquivo HTML
- **Hard limit**: 1000 linhas absoluto
- Ultrapassar limite = refactor obrigatório (extrair parcial, componentizar, ou server-side include)
- Use server-side rendering (PHP/Laravel includes, Jinja2, etc.) para manter arquivos menores
- Quebre por partials/includes quando atingir ~500 linhas

### Clean Code

- Sem CSS inline (use classes ou arquivo externo)
- Sem JS inline (use arquivo externo ou defer)
- Atributos `aria-*` e `alt` são obrigatórios em elementos interativos/imagens
- Mantém "uma coisa por arquivo" — estrutura, estilo, comportamento separados
