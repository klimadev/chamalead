# SPEC - Refactor PHP + Tailwind (CDN)

## 1) Grounding da codebase atual

### 1.1 Arquitetura de alto nível
- **Landing + captação de leads (root):** `index.html` (Tailwind via CDN + CSS inline), `api.php` (validação e persistência de leads em SQLite), `config.php` (schema/utilitários), `admin-login.php` e `admin.php` (já em Tailwind CDN).
- **Painel operacional (subapp em `/panel`):** fluxo de autenticação (`auth.php`, `login.php`, `logout.php`), dashboard (`index.php`), ações AJAX (`instance-actions.php`, `deep-link-actions.php`), deep-link público (`deep-link.php`), integração Evolution API (`EvolutionApiService.php`), configuração (`Config.php`), persistência local (`db.php`) e logs (`Logger.php`).
- **Frontend do painel:** HTML server-side em PHP + `panel/panel.js` (modais, chamadas AJAX, filtros, paginação, polling de conexão, geração de deep-link).

### 1.2 Dependências de interface no estado atual
- **CSS legado local:** `panel/styles.css` (layout completo do dashboard/login/modais/toasts), `panel/modal-styles.css` (variante de estilos de modal, atualmente sem import ativo no fluxo principal).
- **Bibliotecas de UI:** Lucide (`https://unpkg.com/lucide@latest`), Toastify JS (`https://unpkg.com/toastify-js` + CSS de CDN em `panel/index.php`), Google Fonts.
- **Pontos de acoplamento visual:** classes utilitárias customizadas (`.btn`, `.input`, `.modal-*`, `.instance-*`, `.stat-*`, `.empty-state`, etc.) usadas em `panel/index.php` e `panel/login.php`; `panel/deep-link.php` usa CSS embutido em `<style>`.

### 1.3 Fluxo backend principal (painel)
- `panel/login.php` autentica via `auth.php` (CSRF, sessão, rate-limit), e redireciona para `panel/index.php`.
- `panel/index.php` faz `redirect_if_not_auth()`, carrega instâncias com `EvolutionApiService::fetchInstances()`, calcula métricas (`total/online/offline`) e renderiza cards/listagem/modais no HTML inicial.
- `panel/panel.js` envia `POST` para `panel/instance-actions.php` com `action` + `csrf_token` para criar/editar/excluir instâncias, obter detalhes, conectar WhatsApp, gerar deep-link.
- `panel/deep-link.php` valida assinatura com `DeepLinkService::validate()` e consome `panel/deep-link-actions.php` via polling para obter QR code e estado de conexão.

## 2) Escopo do refactor visual (Tailwind-only)

### 2.1 Diretriz técnica mandatória
- Stack alvo: **PHP + HTML5**, com injeção de Tailwind via:

```html
<script src="https://cdn.tailwindcss.com"></script>
```

- Todo layout deverá ser reconstruído com classes utilitárias Tailwind (grid, flex, spacing, tipografia, cor, borda, sombra, animações utilitárias).
- CSS inline ficará restrito a casos estritamente necessários (ex.: animação não representável por utilitário padrão, otimização crítica de runtime).

### 2.2 Arquivos CSS legados a descartar
1. `panel/styles.css`
2. `panel/modal-styles.css`

> Observação: além dos arquivos locais, remover também imports de CSS externo no painel (ex.: `toastify.min.css`) para cumprir a regra de não depender de arquivos `.css` avulsos.

## 3) Mapa de mudanças por arquivo

### 3.1 Reescrita de páginas PHP com transposição visual
1. **`panel/index.php` (REWRITE de marcação visual)**
   - Remover `<link rel="stylesheet" href="styles.css">`.
   - Remover `<link rel="stylesheet" href="https://unpkg.com/toastify-js/src/toastify.min.css">`.
   - Inserir Tailwind CDN no `<head>`.
   - Reescrever classes atuais (`.nav`, `.stats-grid`, `.instance-card`, `.modal-*`, `.btn`, `.input`, etc.) para classes utilitárias Tailwind diretamente no HTML.
   - Preservar IDs, `name`, `aria-*`, `data-*`, `onclick`, estrutura de formulários e pontos consumidos por `panel.js`.
   - Ajustar CSP para permitir `https://cdn.tailwindcss.com` em `script-src` sem relaxar além do necessário.

2. **`panel/login.php` (REWRITE de marcação visual)**
   - Remover `<link rel="stylesheet" href="styles.css">`.
   - Inserir Tailwind CDN.
   - Converter layout do login para utilitários Tailwind mantendo os mesmos campos, `name`, validações HTML, CSRF e comportamento de submit.
   - Ajustar CSP para o CDN do Tailwind.

3. **`panel/deep-link.php` (REWRITE de estrutura + estilos inline para Tailwind)**
   - Substituir `<style>...</style>` por classes Tailwind na própria marcação.
   - Manter IDs JavaScript (`expiryBox`, `expiryLabel`, `qrWrap`, etc.) para não quebrar polling e atualização do QR.
   - Inserir Tailwind CDN e atualizar CSP para aceitar o script do CDN.

### 3.2 Ajustes JS de compatibilidade visual
4. **`panel/panel.js` (PATCH de compatibilidade)**
   - Revisar trechos que injetam HTML com classes legadas (`instance-status-*`, `empty-state-*`, etc.) e converter para classes Tailwind equivalentes.
   - Manter contratos de API (`apiCall`, actions, polling) inalterados.
   - Se Toastify continuar, operar sem CSS externo (estilo inline já existente); opcionalmente migrar para toast próprio Tailwind para consistência total.

### 3.3 Arquivos sem alteração funcional prevista
- Backend de negócio e integração: `panel/auth.php`, `panel/instance-actions.php`, `panel/deep-link-actions.php`, `panel/EvolutionApiService.php`, `panel/DeepLinkService.php`, `panel/Config.php`, `panel/db.php`, `panel/Logger.php`, `panel/logout.php`, `panel/health.php`.
- Área administrativa root (`admin.php`, `admin-login.php`) já está em Tailwind e não depende dos CSS legados de `/panel`.

## 4) Fluxo de dados no novo HTML (pós-refactor)

### 4.1 Render server-side (PHP -> HTML)
- `panel/index.php` continuará injetando dados de sessão e instâncias diretamente no HTML (cards/linhas/modais), apenas trocando classes de estilo para Tailwind.
- Cálculos de estatística (`$total`, `$online`, `$offline`) continuam no PHP e serão exibidos em componentes Tailwind.

### 4.2 Interação client-side (HTML -> JS -> PHP -> API)
- `panel/panel.js` permanece responsável por serializar formulários, enviar `POST` para `instance-actions.php`, e refletir resposta JSON na UI.
- O HTML refeito deve preservar os mesmos seletores de script (IDs/classes mínimas usadas para query e eventos), garantindo que submissão e polling não sofram regressão.

### 4.3 Deep-link/QR
- `panel/deep-link.php` continua recebendo `instance`, `exp`, `sig` por query string.
- Validação criptográfica e polling para QR permanecem idênticos; apenas o invólucro visual migra para Tailwind.

## 5) Garantias de não regressão (formulários e interface)

- Manter `name` de todos inputs e hidden fields (ex.: `instanceName`, `rejectCall`, `csrf_token`) para preservar parsing no backend.
- Manter `id` referenciados pelo JS (ex.: `connectSubmitBtn`, `pairingCodeDisplay`, `deepLinkUrl`, `searchInput`, `instancesGrid`).
- Manter semântica de `type="submit"`, `form="..."`, `required`, `pattern`, `minlength/maxlength`.
- Não alterar endpoints nem payloads (`action` values atuais em `instance-actions.php` e `deep-link-actions.php`).
- Preservar comportamento de autenticação/sessão/CSRF e de redirecionamentos.

## 6) Critérios de aceitação (RFC 2119)

1. A aplicação **MUST** renderizar corretamente com Tailwind via CDN (`https://cdn.tailwindcss.com`) nas páginas refatoradas do painel.
2. A aplicação **MUST NOT** conter arquivos `.css` avulsos no módulo refatorado (`/panel`) nem importar CSS externo para compor layout principal.
3. A aplicação **SHOULD** usar CSS inline apenas em casos extremos e justificados (animações/otimizações não expressáveis com utilitários Tailwind).
4. A refatoração **MUST** preservar todos os fluxos funcionais existentes: login/logout, listagem de instâncias, create/edit/delete, connect/pairing, geração e consumo de deep-link.
5. A refatoração **MUST** manter compatibilidade com validações e submissões de formulários PHP sem alteração de contratos de payload.

## 7) Sequência de execução recomendada

1. Reescrever `panel/login.php` para estabelecer padrão Tailwind de tokens/classes.
2. Reescrever `panel/index.php` (incluindo modais) e atualizar `panel/panel.js` para classes/HTML injetado.
3. Reescrever `panel/deep-link.php` com Tailwind.
4. Remover `panel/styles.css` e `panel/modal-styles.css` do repositório.
5. Validar visualmente os fluxos críticos e validar funcionalmente chamadas AJAX/formulários.

## 8) Factibilidade

- O plano é factível porque a camada de negócio está majoritariamente desacoplada da camada visual: backend permanece em endpoints PHP e a maior parte da mudança é de marcação/classes.
- O risco principal está no acoplamento do `panel.js` a IDs/classes específicas; por isso o plano fixa a preservação de IDs e contratos de formulário, minimizando chance de quebra de submissão e interações.
