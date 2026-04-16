# Spec: QuizPerfSpec

Scope: feature

# Quiz Performance Spec

## Objetivo
Otimizar exclusivamente o diretório `quiz/` para melhorar PageSpeed mobile e Core Web Vitals, com foco direto em LCP, FCP, TTFB, TBT e eliminação máxima de render blocking possível sem remover `https://cdn.tailwindcss.com`, sem introduzir frameworks e sem alterar o comportamento funcional do quiz.

## Restrições obrigatórias
- Manter arquitetura atual em PHP nativo + HTML/CSS/JS vanilla.
- Manter `tailwindcss.com` carregando em runtime.
- Não adicionar pipeline de build Tailwind nesta etapa.
- Não reescrever o projeto inteiro.
- Não quebrar compatibilidade do fluxo atual.
- Alterações devem ser incrementais, diretas, testáveis e de alto impacto real em PageSpeed.
- Escopo limitado a `quiz/`.

## Problemas atuais identificados
1. `quiz/partials/head/foundation/document-and-theme-bootstrap.php`
   - carrega `tailwindcss.com` no caminho crítico e isso permanecerá por restrição do projeto;
   - carrega Google Fonts como stylesheet bloqueante;
   - executa scripts no `head` antes da renderização completa;
   - agrega CSS inicial junto com partes que podem ser adiadas;
   - injeta Facebook Pixel no caminho crítico.
2. `quiz/partials/app-script.php`
   - injeta toda a lógica de aplicação como scripts inline/parciais, impedindo uso real de `defer`.
3. `quiz/partials/layout/steps/core-shell-and-context-steps.php`
   - a primeira dobra concentra o conteúdo mais provável de LCP: headline principal e logo.
4. `quiz/api.php`
   - faz bootstrap de dependências de forma ampla;
   - caminho de `validate-phone` pode carregar mais contexto do que o necessário;
   - faltam decisões explícitas de cache para respostas HTTP.
5. CSS/JS do resultado e de etapas posteriores participam cedo demais do payload inicial.
6. O background visual inicial tem custo de pintura/composição relevante no mobile.

## Resultado esperado
Após a implementação, a entrega final do `quiz/` deve manter o mesmo fluxo visual e funcional, mas com:
- menor tempo até o primeiro paint perceptível;
- menor custo de renderização da dobra inicial;
- menor bloqueio por scripts próprios;
- menor competição por recursos na fase inicial;
- backend mais enxuto nos caminhos quentes;
- headers de cache/rede mais adequados.

## Estratégia técnica

### 1. Render blocking
Como `tailwindcss.com` precisa permanecer, a otimização vai remover ou adiar todos os outros bloqueios evitáveis.

Será feito:
- mover scripts próprios do quiz para arquivos estáticos locais, permitindo `defer` real;
- reduzir scripts inline no `head` ao mínimo indispensável;
- postergar scripts analíticos ou de terceiros não essenciais para pintura inicial, sem remover o tracking necessário;
- reorganizar CSS para manter inline apenas o crítico da first viewport.

### 2. CSS crítico e CSS adiado
Será adotada uma divisão prática entre:
- CSS crítico inline: necessário para a primeira viewport e a etapa `welcome`;
- CSS não crítico adiado: estilos de etapas tardias, resultado, validação secundária e animações pesadas.

CSS crítico deve cobrir:
- `html`, `body`, tokens base;
- `.quiz-container`, `.quiz-content`, `.quiz-header`;
- `.logo`, `.progress-track`, `.progress-meta`;
- `.step.active` da welcome;
- `.step-number`, `.step-headline`, `.step-sub`;
- `.welcome-features`, `.footer-cta`, `.cta-btn`.

CSS adiado deve incluir prioritariamente:
- estilos de resultado;
- estilos responsivos exclusivos de resultado/rodapé avançado;
- estilos de campos e grids de opções que só aparecem após avanço;
- partes visuais custosas que não afetam a dobra inicial.

A técnica preferida será uma destas, escolhida pela menor mudança estrutural:
- `link rel="preload" as="style"` com ativação posterior;
- ou stylesheet assíncrono com `media` transitório e `onload`.

### 3. LCP
O provável LCP atual é o bloco principal de headline da welcome, com a logo como recurso concorrente secundário.

Será feito:
- definir `width` e `height` explícitos na logo;
- avaliar `fetchpriority="high"` na logo somente se ela participar materialmente do LCP observado;
- manter a headline pronta para pintar cedo com fontes em fallback rápido;
- aplicar `font-display: swap` e handshake otimizado para fontes externas;
- evitar que estilos do resultado ou scripts tardios concorram com a dobra inicial.

### 4. Fontes
Se Google Fonts continuar em uso, será otimizado:
- `preconnect` para `fonts.googleapis.com` e `fonts.gstatic.com`;
- `preload` do stylesheet de fontes se couber na estratégia adotada;
- `font-display: swap` garantido na URL de fonte;
- fallback robusto em `--ff-body` e `--ff-display`.

### 5. JavaScript próprio
O JS do quiz será reorganizado para reduzir custo de parse/execução inicial.

Diretrizes:
- sair de parciais inline para arquivos `.js` locais dentro de `quiz/`;
- usar `defer` em todos os scripts próprios não críticos;
- inicializar a aplicação após DOM pronto, sem bloquear parsing;
- carregar lógica de resultado e trechos não usados na welcome em arquivos separados;
- manter ordem e acoplamento atuais sem reescrever a arquitetura.

Divisão técnica sugerida da codebase JS final:
- `quiz/assets/quiz-core.js`: bootstrap, estado, navegação básica e binding essencial;
- `quiz/assets/quiz-flow.js`: validação, interações de opções, submit e validação de telefone;
- `quiz/assets/quiz-result.js`: renderização de resultado e score engine;
- opcionalmente `quiz/assets/quiz-layout.js`: lógica de fit/layout/viewport se isso continuar suficientemente separado para justificar o arquivo.

Observação: os nomes podem variar, mas o resultado final deve permitir `defer` real e reduzir execução cedo demais.

### 6. Backend / TTFB em `quiz/api.php`
O arquivo deve ser ajustado para reduzir trabalho síncrono nos caminhos mais frequentes.

Será feito:
- revisar bootstrap inicial e mover carregamentos pesados para uso tardio;
- evitar instanciar `MetaConversionsApiService` antes da necessidade real do submit final;
- manter `PhoneParser` disponível para `validate-phone`, mas evitar includes adicionais desnecessários nesse caminho;
- adicionar headers HTTP explícitos para JSON dinâmico, como `Cache-Control: no-store, no-cache, must-revalidate, max-age=0` quando apropriado;
- garantir `Content-Type` e possivelmente `Vary` coerentes;
- documentar recomendação de OPcache no ambiente como melhoria obrigatória de infraestrutura, não de código.

Também deve ser analisado se `session_start()` é realmente necessário em toda request do `quiz/api.php`; se não for indispensável para todos os caminhos, deve ser postergado ou removido do fluxo que não depende de sessão.

### 7. Cache e rede
Será definida uma política simples e segura:
- HTML/PHP principal do quiz: cache conservador;
- JSON dinâmico (`api.php`): sem cache;
- assets estáticos locais do quiz: `Cache-Control: public, max-age=31536000, immutable` com versionamento por query string ou mtime.

A codebase final deve refletir isso por:
- referências versionadas para JS/CSS locais;
- documentação de configuração de servidor para assets;
- eventual helper mínimo em PHP para acrescentar versão por `filemtime()` se isso puder ser feito sem complexidade excessiva.

### 8. Imagens
Será feito:
- `loading="lazy"` em imagens não críticas, se existirem no fluxo tardio;
- dimensões fixas nas imagens renderizadas;
- orientação documentada para conversão futura de imagens locais para WebP/AVIF quando houver ganho mensurável;
- análise da logo atual para confirmar se vale `preload` ou apenas prioridade alta.

### 9. Background e custo de pintura
Sem alterar a linguagem visual, a implementação deve reduzir o custo inicial da composição.

Possíveis ajustes permitidos:
- reduzir intensidade ou atraso de animações no estado `welcome`;
- simplificar efeitos visuais que entram antes da primeira interação;
- postergar classes/estados mais caros até depois do first paint;
- respeitar `prefers-reduced-motion` já existente e ampliar o uso quando útil.

### 10. Compressão servidor
A spec inclui recomendação operacional para o ambiente, sem alterar arquitetura:
- habilitar Gzip ou Brotli no servidor;
- configurar `Expires`/`Cache-Control` para assets;
- separar regras para HTML, JSON e estáticos.

## Estrutura final esperada da codebase
A organização final deve continuar simples e incremental.

### `quiz/index.php`
Resumo descritivo:
- continua como entrypoint mínimo do quiz;
- mantém `require` dos partials principais;
- se necessário, passa a expor helper/versionamento de assets sem adicionar lógica de negócio.

### `quiz/api.php`
Resumo descritivo:
- continua como endpoint único do quiz;
- passa a ter bootstrap mais enxuto por caminho de execução;
- define headers HTTP explícitos de resposta;
- posterga carregamento de dependências pesadas para o submit final;
- mantém contrato de payload e respostas sem alteração funcional.

### `quiz/partials/head.php`
Resumo descritivo:
- continua agregando os partials do head;
- pode passar a separar módulos críticos e adiados de forma mais clara.

### `quiz/partials/head/foundation/document-and-theme-bootstrap.php`
Resumo descritivo:
- permanece responsável por documento, meta tags, tema base e foundation crítica;
- será enxugado para conter apenas o que precisa participar da dobra inicial;
- manterá `tailwindcss.com` por restrição do projeto;
- otimizará fontes, ordem de recursos e scripts de terceiros.

### `quiz/partials/head/foundation/progress-and-shell-styles.php`
Resumo descritivo:
- permanecerá com estilos do shell e progresso;
- parte dele pode continuar crítica se afetar a viewport inicial;
- o restante pode ser movido para estilo adiado se não impactar a welcome.

### `quiz/partials/head/form/form-and-option-styles.php`
Resumo descritivo:
- concentrará estilos de inputs, botões e opções das etapas interativas;
- tende a sair do CSS crítico e ir para carga adiada, exceto se algum trecho for necessário na primeira dobra.

### `quiz/partials/head/result/result-core-styles.php`
Resumo descritivo:
- ficará explicitamente fora do CSS crítico;
- será tratado como bloco de estilo adiado, pois só afeta a etapa final.

### `quiz/partials/head/result/result-responsive-and-footer-styles.php`
Resumo descritivo:
- também ficará fora do CSS crítico, salvo regras mínimas do CTA inicial se hoje estiverem misturadas;
- deve permanecer focado em resultado/responsividade tardia.

### `quiz/partials/layout.php`
Resumo descritivo:
- continua agregando os blocos de markup do quiz;
- sem mudança estrutural ampla.

### `quiz/partials/layout/steps/core-shell-and-context-steps.php`
Resumo descritivo:
- manterá o shell, header e etapas iniciais;
- receberá ajustes de LCP/CLS, como dimensões fixas da logo e prioridades de recurso;
- continuará definindo a first viewport do quiz.

### `quiz/partials/layout/steps/pain-detail-urgency-result-footer.php`
Resumo descritivo:
- continuará com etapas tardias, resultado e footer;
- não deve participar do custo crítico além do markup inevitável;
- sua estilização deve depender majoritariamente do CSS adiado.

### `quiz/partials/app-script.php`
Resumo descritivo:
- deixará de injetar toda a aplicação inline;
- passará a referenciar scripts estáticos locais com `defer` e versionamento.

### `quiz/partials/script/runtime/state-persistence-and-bootstrap.php`
Resumo descritivo:
- seu conteúdo tende a ser migrado para um asset JS estático de bootstrap/runtime;
- a lógica permanece, mas sai do caminho inline bloqueante.

### `quiz/partials/script/runtime/progress-guidance-and-layout-fit.php`
Resumo descritivo:
- seu conteúdo tende a ir para asset JS próprio, possivelmente separado por responsabilidade;
- a lógica de ajuste de layout poderá ser adiada para após `DOMContentLoaded` ou primeira renderização.

### `quiz/partials/script/flow/interaction-validation-and-submit.php`
Resumo descritivo:
- tende a virar asset JS estático com `defer`;
- manterá validação, interação e submit sem mudança comportamental;
- validação de telefone deve continuar funcional, mas com menor custo inicial no parser/execução.

### `quiz/partials/script/result/result-render-and-score-engine.php`
Resumo descritivo:
- deve sair do inline e ir para asset JS separado;
- idealmente carregado de forma adiada em relação ao bootstrap principal, já que só é usado ao final do quiz.

### Novos assets locais esperados
Arquivos prováveis:
- `quiz/assets/quiz-core.js`
- `quiz/assets/quiz-flow.js`
- `quiz/assets/quiz-result.js`
- `quiz/assets/quiz-deferred.css` ou nomes equivalentes para CSS não crítico

Esses arquivos existirão para permitir:
- `defer` real;
- cache de longo prazo;
- menor HTML inline;
- melhor reaproveitamento do browser cache.

## O que será feito por arquivo

### Arquivos com alta chance de alteração direta
- `quiz/index.php`
  - ajustar referência/versionamento de assets se necessário.
- `quiz/api.php`
  - reduzir bootstrap e definir headers HTTP.
- `quiz/partials/head/foundation/document-and-theme-bootstrap.php`
  - otimizar ordem de recursos, fontes, CSS crítico e terceiros.
- `quiz/partials/head/foundation/progress-and-shell-styles.php`
  - reclassificar trechos entre crítico e adiado.
- `quiz/partials/head/form/form-and-option-styles.php`
  - mover para trilha não crítica, salvo regras necessárias cedo.
- `quiz/partials/head/result/result-core-styles.php`
  - adiar carregamento.
- `quiz/partials/head/result/result-responsive-and-footer-styles.php`
  - adiar carregamento.
- `quiz/partials/layout/steps/core-shell-and-context-steps.php`
  - aplicar ajustes de LCP/CLS na logo e na first viewport.
- `quiz/partials/app-script.php`
  - trocar inline por scripts locais com `defer`.

### Arquivos com chance de migração de conteúdo
- `quiz/partials/script/runtime/state-persistence-and-bootstrap.php`
- `quiz/partials/script/runtime/progress-guidance-and-layout-fit.php`
- `quiz/partials/script/flow/interaction-validation-and-submit.php`
- `quiz/partials/script/result/result-render-and-score-engine.php`

Resumo descritivo dessas migrações:
- o conteúdo funcional atual permanece;
- a mudança é de entrega/carregamento, não de regra de negócio;
- o objetivo é permitir parse e execução mais eficientes pelo navegador.

## Critérios de aceite
- O fluxo do quiz permanece funcional do início ao fim.
- A UI inicial continua visualmente equivalente.
- `tailwindcss.com` permanece carregando.
- Scripts próprios relevantes deixam de bloquear parsing inicial.
- CSS crítico cobre adequadamente a first viewport.
- Estilos de resultado e etapas tardias deixam de competir com a dobra inicial.
- `quiz/api.php` responde com headers coerentes e bootstrap mais enxuto.
- Logo e demais imagens visíveis evitam CLS por falta de dimensões.
- Assets locais passam a ser cacheáveis de forma agressiva.
- Há orientação objetiva de OPcache, gzip/brotli e expires para o ambiente.

## Validação esperada na implementação futura
- `php -l quiz/index.php`
- `php -l quiz/api.php`
- checagem de sintaxe dos partials PHP alterados;
- teste manual do fluxo do quiz em mobile/desktop;
- teste do submit e da validação de telefone;
- comparação de waterfall/network antes e depois;
- verificação do ganho em PageSpeed principalmente em render blocking, FCP e LCP.

## Fora de escopo
- remover Tailwind CDN;
- criar pipeline Tailwind build;
- refatoração ampla de arquitetura;
- migração para framework;
- alterações de regra de negócio do quiz;
- mudanças fora de `quiz/`.