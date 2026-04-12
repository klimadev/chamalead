---
plan name: QuizNoScroll
plan description: Fluxo sem barras inteligente
plan status: active
---

## Idea
Redesenhar o quiz para funcionar em viewport fixa sem depender de qualquer scrollbar (nem da página nem de containers internos), preservando a narrativa por etapas, acessibilidade, performance e taxa de conversão em mobile e desktop. O plano deve substituir overflow por orquestração de layout (header/content/footer), modo compacto adaptativo e governança de conteúdo por orçamento de altura por etapa.

## Implementation
- Auditar todas as fontes de overflow em quiz.php (estrutura, CSS e JS) e registrar em quais etapas o conteúdo extrapola a viewport.
- Definir contrato de layout no-scroll com grid de 3 zonas (header, stage, footer) em 100dvh e limites máximos por zona para cada breakpoint.
- Implementar sistema de 'budget vertical' por etapa (classes/data-attributes) com modo compacto progressivo antes de qualquer overflow.
- Refatorar renderização das etapas para viewport única, garantindo que só a etapa ativa seja exibida e que CTA permaneça sempre visível.
- Criar fallback inteligente para conteúdo longo (quebra de alternativas em blocos, microcopy curta e variação de densidade tipográfica) sem scroll.
- Adicionar telemetria de segurança de layout (detecção de risco de overflow por step) para validar em produção sem impactar UX.
- Validar em matrizes de dispositivos (mobile baixo, mobile padrão, desktop) incluindo teclado virtual, orientação e safe-area; ajustar tokens responsivos.
- Executar testes manuais de acessibilidade (foco, teclado, leitura) e documentar critérios de aceite no-scroll para evitar regressão futura.

## Required Specs
<!-- SPECS_START -->
<!-- SPECS_END -->