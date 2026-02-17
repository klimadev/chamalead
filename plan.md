# ChamaLead Admin + Form Redesign Plan (LLM-Ready)

## 1) Contexto e objetivo

Este plano define a **correcao, implementacao e redesign** do painel admin e do formulario principal para:

- melhorar branding (consistencia entre landing e admin);
- aumentar facilidade de uso (UX mais direta e menos friccao);
- elevar qualidade visual (painel mais claro, bonito e funcional);
- melhorar acessibilidade e seguranca basica sem trocar stack.

Escopo imediato: **planejamento executavel** para uma IA implementar em fases.

## 2) Stack e restricoes

- Stack atual: PHP server-rendered + SQLite + HTML/CSS/JS vanilla + Tailwind via CDN.
- Arquivos-chave:
  - `index.html`
  - `admin.php`
  - `api.php`
  - `config.php`
  - `leads.db`
- Restricoes:
  - sem migracao para framework pesado neste ciclo;
  - manter compatibilidade com dados existentes;
  - evitar processos bloqueantes (sem dev server/watch).

## 3) Diagnostico (estado atual)

### UX
- formulario principal aparece tarde no fluxo da landing;
- campos coletados no front (`nome`, `desafio`) nao sao persistidos no backend atual;
- feedback de envio usa `alert()` (interacao pobre e bloqueante);
- admin nao oferece fluxo claro de triagem por status (novo -> contatado etc.).

### Branding/Visual
- identidade inconsistente entre landing (ChamaLead) e admin (branding divergente);
- excesso de efeitos visuais perto do CTA reduz clareza;
- ausencia de tokens compartilhados entre landing e admin.

### Acessibilidade
- associacao label/campo incompleta em partes do formulario;
- feedback sem regiao ARIA live;
- navegacao por teclado/foco fragil em alguns controles;
- suporte a reduced motion ausente/incompleto.

### Seguranca/tecnica
- acesso admin por segredo em query string;
- acao destrutiva via GET (delete), sem CSRF;
- arquivos monoliticos dificultam manutencao;
- codigo/asset potencialmente ocioso.

## 4) Metas de produto (resultado esperado)

1. Formulario mais rapido de preencher, com validacao clara e sucesso in-page.
2. Dados de lead consistentes fim-a-fim (front -> API -> DB -> admin).
3. Painel admin com triagem rapida, acoes frequentes em 1 clique e fluxo por status.
4. Branding unificado ChamaLead entre `index.html` e `admin.php`.
5. Baseline de acessibilidade e seguranca significativamente melhor.

## 5) Arquitetura de redesign (sem trocar stack)

### 5.1 Sistema visual compartilhado
- criar tokens de marca reutilizaveis (cores, tipografia, espacamento, estados de foco);
- aplicar tokens em landing e admin para eliminar divergencia;
- reduzir animacoes nao essenciais em areas de conversao.

### 5.2 Formulario (landing)
- mover/duplicar bloco compacto do formulario para zona alta da pagina (hero/primeiro scroll);
- manter versao expandida abaixo, com progressao clara;
- trocar `alert()` por feedback inline (erro/sucesso) com ARIA live;
- garantir mascaramento/normalizacao de WhatsApp e validacao objetiva.

### 5.3 Admin
- reorganizar layout para triagem:
  - fila de novos leads visivel;
  - filtros/sort mais objetivos;
  - acoes rapidas (marcar contatado, abrir WhatsApp, copiar dados);
- converter delete para POST com confirmacao segura;
- preparar autenticacao por sessao no lugar de segredo em URL.

## 6) Roadmap de implementacao (fases)

## Fase 1 - Integridade de dados + UX minima confiavel

**Status:** aprovada

**Entregas**
- alinhar campos do formulario e persistencia (`nome`, `desafio`, demais campos coletados);
- substituir `alert()` por feedback inline e estado de envio;
- incluir atualizacao de status no admin;
- trocar delete GET por POST.

**Arquivos-alvo provaveis**
- `index.html`, `api.php`, `config.php`, `admin.php`.

**Criterios de aceite**
- todo campo obrigatorio do form chega ao DB e aparece no admin;
- envio bem-sucedido e falhas aparecem no proprio formulario;
- status pode ser alterado sem editar DB manualmente;
- delete nao ocorre por link GET.

## Fase 2 - Acesso e seguranca

**Status:** aprovada

**Entregas**
- login admin com sessao;
- protecao CSRF para operacoes de mutacao (delete/update status);
- remover dependencia de segredo em query string.

**Arquivos-alvo provaveis**
- `admin.php`, `config.php` (e possivel novo arquivo de auth/session).

**Criterios de aceite**
- admin so acessivel com sessao valida;
- mutacoes rejeitadas sem token valido;
- URL nao expoe credencial de acesso.

## Fase 3 - Redesign visual + fluxo de triagem

**Status:** aprovada

**Entregas**
- sistema visual unificado ChamaLead aplicado ao admin e formulario;
- hierarquia visual limpa com CTA mais evidente;
- painel com foco em operacao diaria (menos cliques, acoes frequentes destacadas).

**Arquivos-alvo provaveis**
- `index.html`, `admin.php`.

**Criterios de aceite**
- consistencia visual clara entre paginas;
- caminho CTA -> envio mais curto;
- tarefas comuns do admin executadas mais rapido.

## Fase 4 - Acessibilidade, limpeza e performance

**Status:** aprovada

**Entregas**
- labels/ids/fieldset corretos, foco visivel e navegacao por teclado robusta;
- suporte a `prefers-reduced-motion`;
- revisao de contraste e remocao de codigo/asset ocioso.

**Arquivos-alvo provaveis**
- `index.html`, `admin.php`, assets/JS relacionados.

**Criterios de aceite**
- fluxo completo via teclado;
- sem falhas obvias de contraste em textos importantes;
- menor ruido visual/performance em area critica de conversao.

## 7) Backlog executavel por arquivo (checklist)

## `index.html`
- [x] elevar formulario compacto para parte alta da pagina;
- [ ] consolidar componentes de campo (label + hint + erro);
- [x] implementar feedback inline de sucesso/erro;
- [x] harmonizar tokens visuais com admin;
- [ ] reduzir efeitos visuais concorrentes perto do CTA.

## `api.php`
- [x] validar e persistir todos os campos definidos como escopo de negocio;
- [x] padronizar payload de erro/sucesso para front;
- [x] reforcar validacao server-side (metodo, required, formato).

## `config.php` / schema
- [x] garantir schema compativel com campos do front;
- [x] planejar migracao segura para DB existente;
- [ ] revisar defaults de status e indices uteis para listagem.

## `admin.php`
- [x] layout de triagem por status;
- [x] acao de update de status com UX rapida;
- [x] delete via POST com confirmacao segura;
- [x] unificar identidade visual ChamaLead;
- [x] preparar/implantar auth por sessao + CSRF.

## 8) Riscos e mitigacoes

- **Risco:** migracao de schema quebrar dados antigos.
  - **Mitigacao:** migracao idempotente + fallback para colunas ausentes.
- **Risco:** regressao mobile no redesign.
  - **Mitigacao:** validar breakpoints-chave antes de concluir fase.
- **Risco:** endurecimento de acesso bloquear admin legitimo.
  - **Mitigacao:** rota de login clara + mensagens de erro objetivas + rollout faseado.

## 9) Validacao obrigatoria por ciclo

Executar em cada iteracao de implementacao:

1. Lint/cheque estatico aplicavel ao stack:
   - `php -l config.php`
   - `php -l api.php`
   - `php -l admin.php`
2. Build (se existir no projeto).
3. Revisao logica manual:
   - fluxo principal de captura;
   - operacoes criticas no admin;
   - casos de borda obvios.

## 10) Prompt pronto para a IA implementadora

Use este prompt na proxima etapa de execucao:

```md
Implemente o redesign e correcoes do ChamaLead seguindo estritamente o arquivo `plan.md`.

Objetivo:
- melhorar UX do formulario e do admin;
- unificar branding entre `index.html` e `admin.php`;
- corrigir integridade de dados (front -> api -> db -> admin);
- elevar seguranca basica (sessao + CSRF + mutacoes via POST).

Regras de execucao:
1) Trabalhar por fases (Fase 1 -> Fase 4) sem pular criterios de aceite.
2) Em cada fase, editar todos os arquivos relevantes na mesma iteracao.
3) Nao rodar processos bloqueantes; nao usar dev server/watch.
4) Ao final de cada iteracao: rodar `php -l` nos arquivos PHP e revisar logica.
5) Se falhar, corrigir todos os problemas relacionados e repetir pipeline.

Entregue:
- lista de arquivos alterados;
- o que mudou e por que;
- evidencias de validacao;
- pendencias/riscos remanescentes.
```

## 11) Definition of Done

- formulario mais curto e claro no topo da jornada;
- persistencia completa dos dados de lead previstos no formulario;
- admin com triagem por status e acoes frequentes simplificadas;
- identidade ChamaLead consistente nas interfaces;
- operacoes sensiveis protegidas (sessao/CSRF/POST);
- validacao tecnica e revisao logica concluida sem pendencias criticas.
