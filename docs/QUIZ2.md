# Documentação do Quiz 2

## Visão Geral

O quiz2 está localizado em `/quiz2` e possui um fluxo de **10 etapas** com **etapas condicionais** baseadas na seleção da dor principal. É uma aplicação HTML standalone com JavaScript inline.

---

## Fluxo Completo

```
welcome → nome → whatsapp → cargo → faturamento → canal → volume → dor → [dor_detalhe]* → urgencia → resultado
```

*Etapa condicional: aparece dependiendo da seleção em "dor"

---

## Etapas e Perguntas

### Etapa 1: Welcome

**Título:** Sua operação está queimando dinheiro?

**Descrição:** Descubra em 2 minutos exatos onde estão os furos do seu funil e como blindar sua conversão.

**Tipo de input:** Botão CTA
- "Começar Diagnóstico Rápido →"

---

### Etapa 2: Nome

**Título:** Como posso te chamar?

**Campo:** `nome`

**Tipo de input:** Texto livre
- Validação: mínimo 2 caracteres

---

### Etapa 3: WhatsApp

**Título:** Qual é o seu WhatsApp com DDD?

**Campo:** `whatsapp`

**Tipo de input:** Telefone com formatação automática
- Validação: 10+ dígitos (máscara `(00) 00000-0000`)

---

### Etapa 4: Cargo

**Título:** Qual é a sua função na empresa?

**Campo:** `cargo`

**Opções:**
| Value | Label |
|-------|-------|
| `Sócio / Fundador` | Sócio / Fundador |
| `Diretor / Head` | Diretor / Head |
| `Gestor de Vendas/MKT` | Gestor de Vendas/MKT |
| `Analista / SDR` | Analista / SDR / Closer |

---

### Etapa 5: Faturamento

**Título:** Qual a faixa de faturamento mensal atual?

**Campo:** `faturamento`

**Opções:**
| Value | Label |
|-------|-------|
| `Até R$ 50k` | Até R$ 50k |
| `R$ 50k a R$ 150k` | R$ 50k a R$ 150k |
| `R$ 150k a R$ 500k` | R$ 150k a R$ 500k |
| `Acima de R$ 500k` | Acima de R$ 500k |

---

### Etapa 6: Canal

**Título:** De onde vem a maior parte dos clientes?

**Campo:** `canal`

**Opções:**
| Value | Label |
|-------|-------|
| `Meta Ads` | Meta Ads (Insta/FB) |
| `Google Ads` | Google Ads |
| `Outbound` | Prospecção Ativa (Outbound) |
| `Orgânico` | Orgânico / Indicações |

---

### Etapa 7: Volume

**Título:** Quantos leads caem na base por mês?

**Campo:** `volume`

**Opções:**
| Value | Label |
|-------|-------|
| `Menos de 100 leads` | Menos de 100 leads |
| `100 a 500 leads` | 100 a 500 leads |
| `500 a 2000 leads` | 500 a 2000 leads |
| `Mais de 2000 leads` | Mais de 2000 leads |

---

### Etapa 8: Dor

**Título:** Qual o maior gargalo da sua operação?

**Campo:** `dor`

**Opções:**
| Value | Label | Etapa Condicional |
|-------|-------|-----------------|
| `Atendimento Lento` | Equipe demora muito para responder | `dor_atendimento_lento` |
| `Fora de Horário` | Perdemos leads à noite/finais de semana | `dor_fora_horario` |
| `Falta de Follow-up` | Leads esfriam por falta de follow-up | `dor_falta_followup` |
| `Converte Mal` | Geramos leads, mas a conversão é baixa | `dor_converte` |
| `Organização` | O CRM é uma bagunça/Não usamos | `dor_organizacao` |

---

### Etapas Condicionais de Detalhe da Dor

#### Se `Atendimento Lento` → `dor_atendimento_lento`

**Título:** Qual o tempo médio de resposta atual?

**Campo:** `detalhe_dor`

**Opções:**
- `Menos de 15 min` - Menos de 15 minutos
- `De 15min a 1h` - De 15 minutos a 1 hora
- `Mais de 1 hora` - Mais de 1 hora (Crítico)

#### Se `Fora de Horário` → `dor_fora_horario`

**Título:** Como os leads noturnos são tratados?

**Opções:**
- `Robô Genérico` - Robô genérico de WhatsApp
- `Ficam no vácuo` - Ficam no vácuo até o dia seguinte

#### Se `Falta de Follow-up` → `dor_falta_followup`

**Título:** Quantas tentativas fazem antes de desistir?

**Opções:**
- `1 ou 2` - Apenas 1 ou 2 contatos
- `3 a 5` - 3 a 5 contatos
- `Sem padrão` - Totalmente no improviso

#### Se `Converte Mal` → `dor_converte`

**Título:** Onde os leads costumam travar?

**Opções:**
- `Agendamento` - Não avançam para reunião/call
- `Fechamento` - Acham caro / Somem na proposta

#### Se `Organização` → `dor_organizacao`

**Título:** Qual sistema vocês usam hoje?

**Opções:**
- `Planilhas` - Excel / Planilhas do Google
- `Notion/Trello` - Trello / Notion
- `CRM` - CRM Padrão (Pipedrive, Hubspot)

---

### Etapa 9: Urgência

**Título:** Qual a sua urgência em resolver isso?

**Campo:** `urgencia`

**Opções:**
| Value | Label |
|-------|-------|
| `Imediata` | Para ontem (Sangrando dinheiro) |
| `30 dias` | Nos próximos 30 dias |
| `Sem pressa` | Só pesquisando |

---

### Etapa 10: Resultado (Finalização)

**Título:** Diagnóstico Concluído

**Elementos exibidos:**
- Título "Sua operação está deixando na mesa um potencial de:"
- Score animado (ex: "R$ 120.000")
- Descrição contextual
- Botão CTA: "Ver Plano de Ação 🔥"

---

## Lógica de Finalização

1. Usuário chega na etapa `resultado`
2. Mascote (Ignis Bot) exibe mensagem final
3. Efeitos visuais de fogos de artifício são disparados
4. Score potencial é calculado com animação:
   - Se faturamento "Acima de R$ 500k" → multiplier = 120.000
   - Se faturamento "R$ 150k a R$ 500k" → multiplier = 65.000
   - Demais → multiplier = 25.000
5. Ao clicar no CTA: alerta "Redirecionar para VSL!"

---

## Bifurcações Resumo

| De | Para (condição) |
|----|---------------|
| welcome | nome |
| nome | whatsapp |
| whatsapp | cargo |
| cargo | faturamento |
| faturamento | canal |
| canal | volume |
| volume | dor |
| dor | urgencia |
| dor | dor_atendimento_lento (se "Atendimento Lento") |
| dor | dor_fora_horario (se "Fora de Horário") |
| dor | dor_falta_followup (se "Falta de Follow-up") |
| dor | dor_converte (se "Converte Mal") |
| dor | dor_organizacao (se "Organização") |
| [qualquer dor_detalhe] | urgencia |
| urgencia | resultado |
| resultado | FIM (redirecionamento) |

---

## Diferenças Principais entre Quiz 1 e Quiz 2

| Aspecto | Quiz 1 (/quiz) | Quiz 2 (/quiz2) |
|--------|---------------|----------------|
| Tecnologia | PHP + JS | HTML Standalone |
| Validação phone | 11 dígitos + carrier detection | 10+ dígitos |
| Opçõescargo | 4 opções | 4 opções mais detalhadas |
| Opções faturamento | 5 faixas | 4 faixas |
| Opções canal | 6 canais | 4 canais |
| Volume | por semana | por mês |
| Cáclculo score | API server-side | Client-side only |
| Mascote | Não | Sim (Ignis Bot) |
| Efeitos finais | Score ring + badge | Fogos + animação currency |