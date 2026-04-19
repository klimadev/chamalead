---
plan name: QuizAnalytics
plan description: eventos funil dashboard
plan status: active
---

## Idea
Implementar analytics completos no fluxo `quiz/` com persistência incremental no SQLite a cada passo, histórico append-only de eventos por resposta, captura ampla de atribuição de mídia paga e uma tela dedicada dentro de `/quiz` para visualizar funil, drop-off por etapa, respostas mais comuns e conversão por origem/campanha. O objetivo é transformar o quiz de submissão final em um funil observável ponta a ponta sem quebrar o envio final existente para webhook/Meta.

## Implementation
- Expandir o esquema do banco para suportar analytics do quiz: preservar snapshot em `quiz_leads`, adicionar colunas de atribuição completas e criar uma tabela append-only de eventos por sessão/passo/resposta.
- Refatorar o backend de `quiz/api.php` para aceitar salvamento parcial real por passo, distinguir ações de progresso e finalização, validar apenas os campos relevantes do passo atual e substituir `INSERT OR REPLACE` por um upsert que preserve `created_at`.
- Instrumentar `quiz/assets/quiz-app.js` para capturar landing context e UTMs/click IDs, registrar visualização de passo, seleção de resposta e avanço válido de etapa, enviando snapshots incrementais e eventos sem duplicar submissões finais.
- Criar consultas agregadas para funil do quiz com métricas rápidas: visitas por sessão, avanço por etapa, drop-off, conclusão, taxa de conversão total e cortes por `utm_source`, `utm_campaign`, click IDs e principais respostas.
- Construir uma nova tela de analytics em `/quiz` protegida por autenticação administrativa existente, com cards de KPIs, tabela de etapas, resumo por origem/anúncio e filtros básicos por período/origem/campanha.
- Validar o fluxo inteiro em ambiente local com cenários de abandono e conclusão, confirmar integridade dos dados gravados no SQLite e revisar riscos de volume, duplicidade de eventos e impacto no webhook final.

## Required Specs
<!-- SPECS_START -->
- QuizTracking
<!-- SPECS_END -->