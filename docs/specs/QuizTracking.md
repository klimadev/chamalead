# Spec: QuizTracking

Scope: feature

# Quiz Tracking and Analytics

## Objetivo
Implementar observabilidade completa do funil em `quiz/`, com persistencia incremental no SQLite a cada passo valido, trilha append-only de eventos por sessao/resposta, captura completa de atribuicao de anuncio e uma tela dedicada em `/quiz` para analytics operacionais e de conversao.

## Resultado esperado
- Cada sessao do quiz deve ser identificada por `session_id` e persistida no servidor desde o primeiro passo relevante.
- Cada resposta e avanço de etapa deve gerar eventos rastreaveis para analise de funil e abandono.
- UTM e click IDs de anuncios devem ficar salvos no banco para corte por campanha/origem/anuncio.
- O envio final para Evolution/Meta deve continuar funcionando sem regressao.
- Deve existir uma tela nova em `/quiz` para visualizar funil, conversao e principais origens.

## Modelo de dados

### Tabela existente: `quiz_leads`
Manter como snapshot da sessao/lead atual.

Adicionar colunas de contexto e atribuicao:
- `landing_url TEXT`
- `referer TEXT`
- `gclid TEXT`
- `fbclid TEXT`
- `ttclid TEXT`
- `wbraid TEXT`
- `gbraid TEXT`
- `fbp TEXT`
- `fbc TEXT`
- `first_seen_at DATETIME`
- `completed_at DATETIME`
- `last_event_at DATETIME`
- `phone_state TEXT`
- `phone_state_name TEXT`
- `phone_carrier TEXT`
- `phone_line_type TEXT`

Regras:
- `created_at` deve representar a primeira persistencia da sessao.
- `updated_at` deve refletir o ultimo snapshot salvo.
- `first_seen_at` e `created_at` devem ser preenchidos na primeira gravacao.
- `completed_at` deve ser preenchido apenas quando o quiz for concluido.
- `status` deve distinguir ao menos: `started`, `in_progress`, `completed`, `abandoned`.
- `current_step` deve refletir a ultima etapa atingida com sucesso.

### Nova tabela: `quiz_events`
Tabela append-only para analise detalhada.

Campos recomendados:
- `id INTEGER PRIMARY KEY AUTOINCREMENT`
- `session_id TEXT NOT NULL`
- `event_type TEXT NOT NULL`
- `step_key TEXT`
- `step_index INTEGER DEFAULT 0`
- `field_name TEXT`
- `field_value TEXT`
- `page_url TEXT`
- `referer TEXT`
- `utm_source TEXT`
- `utm_medium TEXT`
- `utm_campaign TEXT`
- `utm_content TEXT`
- `utm_term TEXT`
- `gclid TEXT`
- `fbclid TEXT`
- `ttclid TEXT`
- `wbraid TEXT`
- `gbraid TEXT`
- `fbp TEXT`
- `fbc TEXT`
- `client_ip TEXT`
- `user_agent TEXT`
- `created_at DATETIME DEFAULT CURRENT_TIMESTAMP`

Tipos de evento esperados:
- `landing_view`
- `step_view`
- `answer_selected`
- `step_completed`
- `quiz_completed`

Indices recomendados:
- `(session_id, created_at)`
- `(event_type, created_at)`
- `(step_key, created_at)`
- `(utm_source, utm_campaign, created_at)`

## Contrato da API do quiz

### Endpoint existente: `quiz/api.php`
Deve continuar sendo o unico backend do fluxo.

Acoes esperadas:
- `validate-phone`: manter comportamento atual e retornar dados tecnicos do telefone.
- `track-progress`: novo modo para salvar snapshot parcial da sessao/lead sem exigir todos os campos finais.
- `track-event`: novo modo para gravar evento append-only em `quiz_events`.
- `submit`: manter finalizacao, mas reaproveitar snapshot salvo e marcar conclusao.

Regras de validacao:
- `track-progress` deve validar apenas os campos enviados ou os obrigatorios da etapa atual.
- `submit` continua validando todos os campos finais necessarios.
- `session_id` continua obrigatorio em todas as acoes.
- Campos multi-select devem ser normalizados antes de persistir no snapshot.

Regras de persistencia:
- Substituir `INSERT OR REPLACE` por upsert baseado em `ON CONFLICT(session_id) DO UPDATE` para preservar `created_at` e evitar apagar dados associados.
- Gravar sempre o maior `current_step` ja alcancado para evitar regressao em salvamentos fora de ordem.
- `track-event` nunca deve apagar ou sobrescrever historico.
- `submit` deve preencher `completed_at`, `status = completed` e registrar evento `quiz_completed`.

## Instrumentacao frontend

### Arquivo existente: `quiz/assets/quiz-app.js`
Responsabilidades previstas:
- Capturar parametros da URL e cookies de atribuicao logo no bootstrap.
- Construir um objeto persistente de contexto da visita: `session_id`, page URL, referer, UTM, click IDs, `_fbp`, `_fbc`, user agent.
- Registrar `landing_view` ao carregar o quiz.
- Registrar `step_view` quando uma etapa for renderizada.
- Registrar `answer_selected` quando o usuario escolher opcao ou preencher dado confirmado.
- Enviar `track-progress` a cada avanço valido de etapa.
- Reaproveitar dados de validacao do telefone para preencher estado/operadora/tipo de linha no snapshot.
- Evitar duplicidade excessiva de eventos por render repetido ou restore de `localStorage`.

Comportamento minimo por ponto do fluxo:
- `init()`: gerar/restaurar sessao e salvar contexto inicial de atribuicao.
- `renderStep()`: disparar `step_view` apenas quando a etapa ativa mudar de fato.
- `bindEvents()`: registrar selecoes de opcao e multi-select da dor principal.
- `validateAndNext()`: salvar resposta confirmada e disparar `track-progress`.
- `goToNext()`: avancar UI apenas depois do snapshot local consistente; o envio pode ser assíncrono e tolerante a falhas.
- `submitQuiz()`: continuar envio final, mas incluir todos os campos de atribuicao e contexto ja capturados.

## Dashboard do quiz

### Nova tela sugerida: `quiz/dashboard.php`
Tela dedicada para analytics do quiz, separada do fluxo publico.

Requisitos:
- Reaproveitar `startAdminSession()` e a flag `$_SESSION['admin_authenticated']` existentes em `config.php`.
- Se nao autenticado, redirecionar para `/admin-login.php`.
- Ter filtros GET simples: periodo, `utm_source`, `utm_campaign`.
- Exibir KPIs principais:
  - sessoes iniciadas
  - leads com nome preenchido
  - leads com WhatsApp valido
  - quizzes concluidos
  - taxa de conversao total
- Exibir funil por etapa com contagem absoluta e percentual.
- Exibir resumo por origem/campanha.
- Exibir principais dores e principais canais.
- Exibir lista recente de sessoes concluidas ou em andamento.

### Novo helper sugerido: `quiz/dashboard-data.php`
Responsavel por encapsular consultas agregadas do dashboard para evitar concentrar SQL em `dashboard.php`.

Consultas minimas:
- total de sessoes por periodo
- total de concluidos
- avanço maximo por etapa usando `quiz_leads.current_step`
- distribuicao por `utm_source` e `utm_campaign`
- agregacao por `canal`, `dor_principal`, `timing`
- sessoes recentes com status e timestamps

## Arquivos a modificar

### `config.php`
- Expandir `ensureQuizSchema()` com novas colunas de atribuicao/contexto em `quiz_leads`.
- Criar `quiz_events` e indices associados.
- Adicionar helpers pequenos para queries de analytics se fizer sentido manter no arquivo central.
- Preservar compatibilidade com bancos existentes via migrations condicionais.

### `quiz/api.php`
- Introduzir novos `action` values para progresso e eventos.
- Permitir salvamento parcial real por etapa.
- Persistir snapshots sem exigir todos os campos finais.
- Salvar contexto de atribuicao completo.
- Registrar eventos em `quiz_events`.
- Finalizar sessao sem quebrar webhook Evolution nem Meta CAPI.

### `quiz/assets/quiz-app.js`
- Instrumentar eventos e snapshots em todos os pontos de interacao relevantes.
- Capturar `landing_url`, `document.referrer`, UTM e click IDs.
- Salvar contexto tecnico do telefone apos validacao.
- Evitar duplicacao de eventos ao restaurar estado local.

### `quiz/index.php`
- Se necessario, incluir link discreto para dashboard apenas em contexto autenticado administrativo ou manter sem alteracao caso a tela seja acessada diretamente por URL.
- Nao deve mudar a experiencia publica do quiz sem necessidade real.

### `quiz.php`
- Manter redirect atual; sem mudanca funcional prevista, salvo necessidade de documentacao/descoberta do dashboard.

### `quiz-api.php`
- Manter redirect atual; sem mudanca funcional prevista.

## Arquivos a criar

### `quiz/dashboard.php`
Tela protegida com UI simples e rapida para leitura operacional do funil.

### `quiz/dashboard-data.php`
Camada pequena de funcoes/queries para analytics.

## Riscos e cuidados
- Nao duplicar envio final ao Evolution/Meta por causa de retentativas de progresso.
- Nao tornar o quiz lento com chamadas sincronas em toda interacao; preferir envio leve e tolerante a falha para eventos.
- Nao perder `created_at` por causa de `INSERT OR REPLACE`.
- Nao sobrescrever atribuicao inicial com valores vazios em requests posteriores.
- Nao registrar PII desnecessaria em excesso na tabela de eventos alem do estritamente util para analytics.

## Validacao esperada na implementacao
- Concluir quiz completo e verificar snapshot final em `quiz_leads`.
- Abandonar em etapas diferentes e verificar `current_step`, `status` e eventos correspondentes.
- Acessar com UTM/click IDs e conferir persistencia no banco e no dashboard.
- Verificar que `dashboard.php` mostra funil coerente com os dados salvos.
- Confirmar que `submit` continua disparando webhook e Meta CAPI apenas na conclusao.