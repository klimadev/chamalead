---
plan name: QuizModular
plan description: safe folder migration
plan status: active
---

## Idea
Refatorar o quiz monolítico atual (`quiz.php` + `quiz-api.php`) para uma estrutura dedicada em `/quiz/`, preservando comportamento visual, regras de negócio, tracking e persistência. A abordagem deve ser compatibilidade-first: publicar `/quiz/` como rota principal, manter uma camada temporária de compatibilidade para `quiz.php`/`quiz-api.php`, extrair o arquivo em módulos pequenos sem alterar HTML final nem lógica, e validar cada etapa com checklist visual, comparação funcional e checagem de PHP.

## Implementation
- Inventariar o comportamento atual do quiz, incluindo etapas, estado salvo em localStorage, tracking Meta Pixel, chamada para `quiz-api.php`, assets e regras duplicadas entre front e back.
- Criar a nova superfície pública `/quiz/` com `index.php` e `api.php`, mas manter compatibilidade temporária para `quiz.php` e `quiz-api.php` durante a transição.
- Fazer a primeira migração sem mudança funcional: mover o conteúdo atual para `/quiz/` com paths centralizados e previsíveis, garantindo que assets, fetch da API e includes funcionem tanto na nova rota quanto na compatibilidade temporária.
- Quebrar o `index.php` do quiz em módulos estáveis e de baixo risco, separando bootstrap PHP, head, estilos, layout/steps e script principal, sem reescrever regras nem alterar a ordem de renderização.
- Extrair configurações e estruturas de dados duplicadas para pontos únicos de verdade quando isso não mudar o comportamento, priorizando constantes, mapeamentos e labels antes de funções com efeito visual.
- Executar validação incremental a cada corte: `php -l` nos arquivos tocados, checklist manual do fluxo completo do quiz, verificação do POST final para a API e revisão dos paths/tracking em `/quiz/`.
- Somente após a nova rota `/quiz/` estar estável, decidir se `quiz.php` vira redirecionamento temporário, alias definitivo ou é removido em uma segunda etapa controlada.

## Required Specs
<!-- SPECS_START -->
- QuizPublicRoute
<!-- SPECS_END -->