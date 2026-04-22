---
plan name: pagespeed-fix
plan description: Plano para acelerar mobile
plan status: active
---

## Idea
Melhorar a página quiz2 com foco em Performance mobile, mantendo o desktop já quase ideal. O plano parte dos relatórios Lighthouse gerados para a URL https://chamalead.com/quiz2/ e ataca os gargalos observados: LCP dominado por render delay no h1 inicial, TBT alto no mobile, main-thread work concentrado em style/layout e long tasks, e pequenas oportunidades de corte de custo visual e carregamento. A correção deve preservar a experiência premium escura existente, mas reduzir o custo de pintura e composição no primeiro viewport, especialmente em mobile, sem degradar a conversão nem o comportamento do fluxo do quiz.

## Implementation
- Converter a folha de estilos não crítica em carregamento assíncrono, mantendo no HTML apenas o CSS essencial do primeiro viewport.
- Reduzir o custo visual do hero inicial no mobile, simplificando gradientes, sombras, blur e animações contínuas que aumentam render delay e style/layout.
- Revisar o boot e o quiz para evitar qualquer trabalho de JavaScript no carregamento inicial que não seja necessário para o primeiro paint.
- Aplicar ajustes responsivos específicos para mobile nas áreas com maior peso de layout e composição, sem regressão visual no desktop.
- Validar os resultados com nova rodada de Lighthouse em mobile e desktop e comparar scores, LCP e TBT com o baseline atual.

## Required Specs
<!-- SPECS_START -->
- pagespeed-mobile
<!-- SPECS_END -->