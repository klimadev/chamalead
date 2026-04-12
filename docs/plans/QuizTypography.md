---
plan name: QuizTypography
plan description: conversion type overhaul
plan status: active
---

## Idea
Realizar um overhaul tipográfico do quiz do ChamaLead com foco em legibilidade, hierarquia de conversão, consistência responsiva e redução de improvisos no CSS atual. O plano parte do sistema já usado no quiz (Inter para texto, Space Grotesk para display/meta) e estrutura a execução em quatro camadas: auditoria detalhada do estado atual, decisão estratégica via OODA, implementação de um sistema tipográfico mais explícito e validável, e uma etapa formal de pré-mortem/red-team para antecipar regressões de leitura, performance, CLS, perda de contraste, excesso de dramatização visual e inconsistência entre estados do fluxo.

## Implementation
- Auditar o quiz atual em `quiz.php`, inventariando papéis tipográficos, tamanhos, pesos, espaçamentos, tracking, line-height, breakpoints e fontes realmente usadas em welcome, perguntas, inputs, opções, CTA e resultado.
- Executar a fase Observe/Orient do OODA: relacionar os padrões atuais do quiz com os objetivos de conversão do produto, os padrões já consolidados no projeto e os pontos de atrito visíveis no mobile e no desktop.
- Executar a fase Decide do OODA: fechar a direção tipográfica do quiz, incluindo estratégia de família tipográfica, escala responsiva, pesos válidos, regras de labels/meta, comportamento de títulos longos e política para CTA e tela de resultado.
- Planejar a fundação da implementação com tokens e/ou grupos tipográficos explícitos no CSS do quiz, reduzindo repetição de `font-family`, `font-size`, `letter-spacing` e `line-height` espalhados pelo arquivo.
- Planejar a refinação por superfície do fluxo: hero de entrada, perguntas principais, textos de apoio, botões de opção, campos de input, feedback de erro, CTA fixo e card de resultado.
- Planejar a otimização de carregamento de fontes, verificando se os pesos importados em Google Fonts correspondem ao uso real e definindo fallback stack segura para evitar custo desnecessário e instabilidade visual.
- Executar um pré-mortem/red-team do redesign tipográfico, listando falhas prováveis como perda de contraste, títulos bonitos mas lentos de ler, labels excessivamente condensados, quebra ruim em telas pequenas, drift entre estados e regressões no fluxo do quiz.
- Definir a validação final da mudança com checklist de QA tipográfico em 420px, 640px e 1024px, cobrindo leitura, densidade, hierarquia, foco, estados de erro, continuidade do progresso e estabilidade visual durante a navegação entre etapas.
- Documentar a decisão final, o racional das escolhas, os trade-offs rejeitados e os próximos passos caso a melhoria precise depois ser expandida para landing ou outros fluxos.

## Required Specs
<!-- SPECS_START -->
- QuizTypeSpec
<!-- SPECS_END -->