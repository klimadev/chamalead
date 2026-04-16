---
plan name: QuizPerf
plan description: performance core vitals quiz
plan status: done
---

## Idea
Otimizar especificamente o diretório `quiz/` para melhorar PageSpeed mobile e Core Web Vitals sem alterar comportamento funcional nem arquitetura. O foco é remover bloqueios de renderização causados por dependências críticas no head, reduzir o custo do first paint da tela inicial, melhorar o recurso mais provável de LCP no topo da página, diminuir custo inicial do backend em `quiz/api.php` e aplicar cache/rede de forma incremental e validável. A execução deve preservar HTML/CSS/JS vanilla e PHP nativo, evitando refatoração ampla e frameworks.

## Implementation
- Auditar `quiz/index.php` e os partials carregados para mapear dependências críticas do head, scripts inline, CSS inicial e o recurso mais provável de LCP da dobra inicial.
- Remover ou adiar recursos bloqueantes do primeiro paint, priorizando a eliminação do `cdn.tailwindcss.com`, o carregamento síncrono de fontes externas e a execução precoce de scripts não necessários antes da interação.
- Separar o CSS necessário para a tela inicial do quiz e postergar estilos não críticos, principalmente estilos exclusivos de resultado, animações pesadas e blocos usados apenas após avanço de etapas.
- Aplicar otimizações de LCP e CLS no topo da página, incluindo tratamento explícito da logo/imagem inicial com preload ou fetchpriority quando aplicável, dimensões fixas e revisão do elemento real mais provável de LCP na etapa welcome.
- Reestruturar a entrega do JavaScript do quiz para que o bootstrap essencial continue funcional, mas listeners, validações e lógica de resultado sejam carregados com `defer`, `DOMContentLoaded` ou lazy init sem bloquear renderização inicial.
- Revisar `quiz/api.php` para reduzir trabalho síncrono em requests frequentes, especialmente validação de telefone, e planejar headers HTTP adequados, cache defensivo para respostas não dinâmicas e orientação objetiva sobre OPcache no ambiente PHP.
- Definir estratégia de cache para assets referenciados pelo quiz, com versionamento simples por query string ou mtime, `Cache-Control` alto para estáticos e regras separadas para HTML e JSON.
- Documentar recomendações de compressão de rede e servidor para o escopo do quiz, incluindo exemplos de `.htaccess` ou Nginx para gzip/brotli e expires de assets, sem mudar a arquitetura do projeto.
- Executar validação pragmática pós-implementação no escopo do quiz, verificando sintaxe PHP, funcionamento do fluxo principal, ausência de regressão visual na etapa inicial e impacto esperado nas métricas LCP, FCP, render blocking e TTFB.

## Required Specs
<!-- SPECS_START -->
- QuizPerfSpec
<!-- SPECS_END -->