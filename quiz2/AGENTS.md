# AGENTS.md - quiz2

## Design Principles (OBRIGATÓRIO)

### Fundamentos (Norman, Apple, Dieter Rams)

- **Affordance**: Elementos devem mostrar como usá-los. Botões parecem clicáveis, campos parecem editáveis.
- **Affordances perceptivas imediatas**: Cor, forma, posição, movimento. Se precisa explicar, falhou.
- **Feedback instantâneo**: Toda ação tem resposta visual <100ms.
- **Less is more**: Remove o que não é essencial. Espaço negativo é recurso.
- **Icon first**: Preferir ícone + label curto. Ícone alone só se universal (✓, +, −, ←).
- **Convenção sobre configuração**: Padrões visuais consistentes evitam customização desnecessária.

### UI/UX Moderno

- Dark premium operacional
- Bordas sutis (1px), superfícies grafite (#1a1a1a), brilho localizado
- Densidade alta com respiro suficiente para toque (min 44px touch targets)
- Tipografia menor e mais densa (14px base, não 16px)
- Prioridade visual por tamanho, não cor apenas

### Vanguarda Tecnológica (2024/2025/2026)

#### Filosofia de Design

- Mantenha Don Norman e Nielsen como base, mas trate a interface como um sistema vivo.
- Adote `Digital Native Motion`: micro-interações devem comunicar estado, intenção e continuidade.
- A UI não deve apenas funcionar; deve parecer rápida, precisa e animada com elegância.
- Evite movimentos genéricos ou decorativos. Toda animação precisa existir para orientar percepção.

#### Padrão de Transições

- `View Transitions API` é o padrão mandatório para mudanças de tema e navegação de rotas.
- Proibido usar overlays, máscaras artificiais, clones do DOM ou truques legados para transições de estado.
- **Spatial UI**: Floating docks, sidebars e drawers que flutuam sobre o conteúdo são permitidos se usarem APIs nativas (`backdrop-filter`, `transform`, `opacity`) sem bibliotecas externas.
- Não introduzir camadas de suporte visuais quando a transição puder ser resolvida nativamente.

#### Desempenho Perceptivo e Refinamento

- Animações devem parecer físicas, não robóticas.
- Use curvas contemporâneas e naturais, como `cubic-bezier(0.4, 0, 0.2, 1)` ou equivalentes mais refinadas quando justificadas.
- Duração padrão de motion: entre `300ms` e `600ms`.
- Animações pesadas, como `clip-path`, devem ser otimizadas para manter `60fps` e aproveitar camadas de renderização do navegador.
- Se uma transição comprometer fluidez, simplifique a composição antes de adicionar complexidade visual.

#### Modern Web Stack

- Priorize APIs nativas e CSS moderno antes de bibliotecas de terceiros.
- Prefira `Container Queries`, `:has()` e CSS Nesting quando melhorarem clareza ou adaptabilidade.
- Mantenha o código lean, direto e focado em performance percebida.
- Evite dependências extras para efeitos que o navegador já entrega com qualidade superior.

#### Glassmorphism e Floating Dock

- **Floating Glass Dock**: Sidebars e docks flutuantes devem:
  - Usar `backdrop-filter: blur(12px)` (ou valor similar) para efeito glass
  - Bordas sutis (`1px solid rgba(255,255,255,0.1)` no tema escuro)
  - Border-radius generoso (24px+) para visual premium
  - Margem da borda da tela (`left/top/bottom: 16px` ou similar)
  - `transform-gpu` e `will-change` para aceleração de hardware
- **Zero-Reflow**: Componentes que expandem no hover (sidebars, menus) devem usar `position: absolute/fixed` para sobrepor conteúdo sem redimensionar a área principal
- **Foco visual com `:has()`**: Use a pseudo-classe `:has()` para aplicar estados de foco (blur, escurecimento) no conteúdo principal quando componentes flutuantes estiverem ativos

---

## Performance-First (Meta < 1s FCP)

### Zero Externo

- **Proibido CDN externo**: Tailwind CDN, Google Fonts e qualquer biblioteca externa em produção.
- **CSS crítico inline**: Estilos da hero section, navegação e above-the-fold DEVEM estar inline no `<head>`.
- **Font stack nativa**: Use `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif`.
- **Vanilla CSS-only**: Converta utilitários Tailwind para classes CSS customizadas quando o arquivo exceder ~50KB.

### Otimização de Carregamento

- **Meta FCP < 1 segundo**: Elimine bloqueadores de renderização.
- **Critical CSS**: Extraia e inline os primeiros ~10KB de CSS necessários para hero + nav.
- **Deferred full CSS**: Carregue CSS completo após First Contentful Paint quando necessário.
- **Fontes do SO**: Não carregue web fonts quando performance é prioridade.
- **Minimize requests**: Cada recurso externo adiciona ~50-200ms de latência.

### Validação

- Execute Lighthouse após cada mudança significativa.
- Meta mínima: Performance 90+, FCP < 1s, TTI < 2s.
- Registre regressão quando métricas caírem abaixo do baseline.

---

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

### Clean Code

- Sem CSS inline (use classes ou arquivo externo)
- Sem JS inline (use arquivo externo ou defer)
- Atributos `aria-*` e `alt` são obrigatórios em elementos interativos/imagens
- Mantém "uma coisa por arquivo" — estrutura, estilo, comportamento separados

---

## Métricas e Performance Mensurável (PageSpeed/Lighthouse)

### Core Web Vitals (obrigatório)

- **FCP** (First Contentful Paint): < 1s
- **LCP** (Largest Contentful Paint): <= 2.5s
- **INP** (Interaction to Next Paint): <= 200ms
- **CLS** (Cumulative Layout Shift): <= 0.1
- **TTFB** (Time to First Byte): o menor possível
- **TBT** (Total Blocking Time): <= 200ms
- **Speed Index**: o menor possível

### Meta por página

- Performance: 90+
- Acessibilidade: 90+
- Boas Práticas: 90+
- SEO: 90+

### Otimizações Obrigatórias

- `preload` para recursos críticos (hero image, CSS principal)
- `preconnect`/`dns-prefetch` para origens externas
- Compressão, minificação e bundling leve
- `cache-control` correto
- Imagens responsivas: `srcset`, `sizes`, `loading="lazy"`, dimensões explícitas
- Formatos modernos: AVIF/WebP
- Deferred de JS não crítico

### Validação

- Execute Lighthouse após cada mudança significativa
- Registre regressão quando qualquer métrica cair abaixo do baseline
- W3C HTML Validator para validação de marcação

---

## Acessibilidade (WCAG AA mínimo)

### Regras Obrigatórias

- Contraste mínimo 4.5:1 para texto normal, 3:1 para texto grande
- Labels em todos os campos de formulário
- `aria-*` em elementos interativos customizados
- `alt` em todas as imagens
- Focus visível em todos os elementos navegáveis
- Estrutura semântica: `<main>`, `<nav>`, `<header>`, `<footer>`, `<article>`
- `skip-link` para navegação por teclado

---

## SEO

### Regras Obrigatórias

- `<title>` único e descritivo por página
- `<meta description>` único por página (150-160 caracteres)
- Um `<h1>` por página
- Estrutura hierárquica: h1 → h2 → h3 (sem pular níveis)
- URLs semânticas e legíveis
- sitemap.xml e robots.txt
- Dados estruturados (Schema.org) quando aplicável

---

## Build/Validation

Para validar o HTML, use:
- Lighthouse no navegador
- W3C HTML Validator
- Verifique: performance, acessibilidade, SEO, boas práticas