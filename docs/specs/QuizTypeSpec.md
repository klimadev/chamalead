# Spec: QuizTypeSpec

Scope: feature

# Quiz Typography Overhaul Spec

## Scope

Aplica-se somente ao arquivo `quiz.php` e aos estilos inline/CSS embutidos que definem a experiência do quiz. Não inclui landing page, painel, páginas de proposta ou identidade tipográfica global do repositório.

## Current State

O quiz já usa um sistema dual de fontes:
- `Inter` para corpo, inputs e parte da interface.
- `Space Grotesk` para headlines, labels/meta, logo textual, CTA principal e métricas de resultado.

### Técnicas tipográficas já usadas no quiz
- Pareamento de duas famílias com separação entre corpo e display.
- `clamp()` para headline principal das etapas.
- Meta labels em uppercase com tracking ampliado.
- Gradiente animado aplicado ao texto de destaque dentro da headline.
- Ajuste responsivo pontual em telas até `420px`.
- Antialiasing explícito no `body`.
- Uso recorrente de contraste forte em ambiente dark para leitura imediata.

### Padrões encontrados no CSS do quiz
- `body` em `Inter`.
- `Space Grotesk` repetido manualmente em múltiplos blocos (`logo-text`, `progress-*`, `step-number`, `step-headline`, `input-label`, `cta-btn`, `result-score-value`, `result-metric-value`).
- Vários tamanhos, pesos, `letter-spacing` e `line-height` definidos diretamente por componente em vez de um sistema tipográfico explícito.
- Importação de Google Fonts com faixa de pesos mais ampla do que a aparentemente necessária para o quiz.

## Problems To Solve

1. Falta de um sistema tipográfico explícito e centralizado dentro do quiz.
2. Repetição manual de decisões tipográficas no CSS, dificultando ajuste fino e consistência.
3. Hierarquia ainda forte, mas com risco de drift entre etapas, CTA e tela de resultado.
4. Mobile com potencial de compressão em headlines longas, opções extensas e labels pequenas.
5. Possível desperdício de payload em pesos de fonte não usados.
6. Necessidade de reforçar legibilidade sem matar a personalidade premium/agressiva do quiz.

## Strategic Direction

### Default Direction
Preservar o par `Inter + Space Grotesk` como base do overhaul, porque ele já está alinhado ao quiz existente e ao restante da experiência pública principal.

### Type Roles
Definir papéis tipográficos claros no quiz:
- Display Hero: headline principal de entrada.
- Display Step: títulos de etapa e resultado.
- Meta Label: etapa, progresso, labels auxiliares, badges.
- Body Primary: subtítulos, insights, textos de apoio.
- Body UI: opções, inputs, mensagens e botões secundários.
- Numeric Emphasis: score e valores destacados.

### System Goals
- Melhor leitura em 1 passada.
- Hierarquia mais previsível entre estados.
- Menos decisões espalhadas e mais tokens/roles reutilizáveis.
- Responsividade consistente em `420px`, `640px` e `1024px`.
- Preservação do tom dark premium e do eixo flame/ember.

## Requirements

### Functional/Typography Requirements
1. Headlines devem continuar impactantes, mas com leitura rápida em telas pequenas.
2. Textos de apoio devem manter boa densidade e respiracao, sem parecerem apagados demais no dark mode.
3. Labels/meta devem ser legíveis mesmo com uppercase e tracking alto.
4. CTA fixo deve ter hierarquia clara sem competir demais com a pergunta principal.
5. Resultado final deve parecer continuação do fluxo, não uma linguagem tipográfica paralela.
6. O sistema deve permitir ajuste global sem caça manual por dezenas de declarações soltas.

### Implementation Requirements
1. Consolidar decisões tipográficas em tokens CSS locais ao quiz e/ou em grupos tipográficos reutilizáveis.
2. Reduzir repetição de `font-family`, `font-size`, `font-weight`, `line-height` e `letter-spacing` quando possível.
3. Revisar importação de fontes para refletir apenas pesos realmente necessários, se a auditoria confirmar isso.
4. Não alterar a lógica do fluxo, persistência, scoring ou analytics do quiz.
5. Não expandir escopo para outros arquivos sem necessidade direta.

## Non-Goals

- Rebranding completo do ChamaLead.
- Troca obrigatória de famílias tipográficas.
- Refatoração estrutural do JavaScript do quiz.
- Unificação com painel, landing principal ou propostas nesta etapa.

## OODA Frame

### Observe
Mapear roles existentes, redundâncias, quebras mobile e diferenças entre welcome, steps, CTA e resultado.

### Orient
Comparar o estado atual com objetivos de conversão, ritmo de leitura e coerência com os padrões já usados no quiz e no ecossistema público principal.

### Decide
Fechar o type system do quiz: famílias, pesos, escala responsiva, tracking, line-height e regras por role.

### Act
Aplicar o sistema no CSS do quiz, validar estados e revisar impacto visual/perceptivo antes de considerar expansão para outros fluxos.

## Pre-Mortem / Red-Team Risks

1. Headline ficar mais bonita e menos escaneável.
2. Mobile sofrer com quebras ruins em perguntas longas.
3. Labels ficarem pequenas demais por excesso de tracking e uppercase.
4. CTA chamar atenção demais e roubar foco da pergunta.
5. Resultado final parecer outra interface.
6. Troca de pesos/fontes gerar CLS ou sensação de layout instável.
7. Redução de contraste em textos de apoio piorar a leitura real.
8. Refatoração de CSS introduzir regressões silenciosas entre estados do quiz.

## Acceptance Criteria

1. O quiz mantém `Inter + Space Grotesk` ou documenta claramente qualquer mudança de direção.
2. Existe uma escala tipográfica explícita e coerente para hero, step title, meta, body, options, CTA e result.
3. O número de declarações tipográficas repetidas no CSS é reduzido de forma perceptível.
4. O quiz continua legível e equilibrado em `420px`, `640px` e `1024px`.
5. Nenhuma etapa perde clareza por quebra ruim, compressão ou contraste insuficiente.
6. O resultado final parece visualmente integrado ao restante do fluxo.
7. O racional de escolhas e riscos mitigados fica documentado no encerramento da tarefa.

## Validation Checklist

- Conferir welcome, perguntas curtas e perguntas longas.
- Conferir labels, erro de input, opções longas e CTA fixo.
- Conferir card de resultado, score, métricas e insight.
- Conferir navegação entre etapas sem instabilidade visual perceptível.
- Conferir peso/import das fontes usados no quiz após a revisão.
- Conferir que o redesign melhora leitura sem perder caráter premium.