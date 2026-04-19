# Quiz 2 - Perguntas

Este documento mapeia as perguntas e etapas do quiz atual.

## Fluxo

1. `welcome`
   - Texto: `Descubra em 2 minutos se sua operação está perdendo leads todos os dias`
   - CTA: `Começar diagnóstico`
2. `nome`
   - Pergunta: `Como posso te chamar?`
   - Campo: nome
3. `whatsapp`
   - Pergunta: `Qual é o seu melhor WhatsApp com DDD?`
   - Campo: WhatsApp
4. `cargo`
   - Pergunta: cargo/função do respondente
5. `faturamento`
   - Pergunta: faixa de faturamento
6. `canal`
   - Pergunta: canal principal de aquisição/contato
7. `volume`
   - Pergunta: volume de leads
8. `dor`
   - Pergunta: principal dor da operação
   - Pode abrir uma etapa condicional dependendo da dor escolhida
9. `urgencia`
   - Pergunta: timing/urgência para agir
10. `resultado`
   - Tela final com score e CTA

## Etapas condicionais de dor

Quando a dor principal selecionada é uma destas, o quiz insere uma etapa extra:

- `atendimento_lento` -> `dor_atendimento_lento`
- `fora_horario` -> `dor_fora_horario`
- `falta_followup` -> `dor_falta_followup`
- `prospeccao_inconsistente` -> `dor_prospeccao`
- `converte_mal` -> `dor_converte`
- `organizacao_baguncada` -> `dor_organizacao`

## Observações

- O fluxo base tem 10 etapas, mas a etapa de dor pode adicionar 1 etapa condicional.
- O progresso exibido varia conforme essa condição.
