---
plan name: minify-quiz2
plan description: Minificar index.html de 1349 para ~400 linhas
plan status: active
---

## Idea
Minificar o projeto quiz2 de 1349 linhas para ~400-500 linhas mantendo toda a qualidade visual, features e UX — sem perder nada.

## Implementation
- 1. Extrair CSS para arquivo separado (index.css, ~350 linhas compactadas com CSS Nesting e remocao de espacamento redundante)
- 2. Converter HTML em template JS-driven: mover conteudo das 16 sections para arrays de config, renderizar com JS (~200 linhas HTML restantes)
- 3. Minificar JS: remover espacamento redundante, usar arrow functions compactas, delegacao de eventos em vez de handlers individuais (~250 linhas JS)
- 4. Executar validacao Lighthouse para garantir Performance >= 90, FCP < 1s, TTI < 2s
- 5. Atualizar AGENTS.md com meta de linha ajustada se necessario

## Required Specs
<!-- SPECS_START -->
<!-- SPECS_END -->