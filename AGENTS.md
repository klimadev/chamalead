# AGENTS.md - UI/UX Principles

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

## Vanguarda Tecnológica (2024/2025)

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