# Documentação do Quiz Principal (CHAMALEAD)

## Visão Geral

O quiz principal está localizado em `/quiz` e possui um fluxo de **10 etapas base** com **etapas condicionais** baseadas na seleção da dor principal.

---

## Fluxo Completo

```
welcome → nome → whatsapp → cargo → faturamento → canal → volume → dor → [dor_detalhe]* → urgencia → resultado
```

*Etapa condicional: aparece dependendo da seleção em "dor"

---

## Etapas e Perguntas

### Etapa 1: Welcome

**Título:** Descubra em 2 minutos se sua operação está perdendo leads todos os dias

**Descrição:** Responda algumas perguntas e veja se já faz sentido automatizar sua prospecção, atendimento e follow-up no WhatsApp.

**Tipo de input:** Botão CTA
- "Começar diagnóstico"

---

### Etapa 2: Nome

**Título:** Como posso te chamar?

**Campo:** `nome`

**Tipo de input:** Texto livre
- Validação: mínimo 2 caracteres

---

### Etapa 3: WhatsApp

**Título:** Qual é o seu melhor WhatsApp com DDD?

**Campo:** `whatsapp`

**Tipo de input:** Telefone com formatação automática
- Validação: 11 dígitos (máscara `(00) 00000-0000`)
- Exibe badges de estado e carrier

---

### Etapa 4: Cargo

**Título:** Qual dessas opções melhor te descreve hoje?

**Campo:** `cargo`

**Opções:**
| Value | Label |
|-------|-------|
| `dono` | Sou dono(a) / sócio(a) |
| `gestor` | Sou gestor(a) comercial / atendimento |
| `time` | Sou parte do time |
| `outro` | Outro |

---

### Etapa 5: Faturamento

**Título:** Em média, quanto sua empresa fatura por mês?

**Campo:** `faturamento`

**Opções:**
| Value | Label |
|-------|-------|
| `ate_10k` | Até R$ 10 mil |
| `10k_20k` | R$ 10 mil a R$ 20 mil |
| `20k_50k` | R$ 20 mil a R$ 50 mil |
| `50k_100k` | R$ 50 mil a R$ 100 mil |
| `acima_100k` | Acima de R$ 100 mil |

---

### Etapa 6: Canal

**Título:** Hoje, por onde entram mais oportunidades no seu comercial?

**Campo:** `canal`

**Opções:**
| Value | Label |
|-------|-------|
| `whatsapp_direto` | WhatsApp direto |
| `instagram_whatsapp` | Instagram → WhatsApp |
| `trafego_pago` | Tráfego pago |
| `indicacao` | Indicação |
| `prospeccao_ativa` | Prospecção ativa |
| `varios_canais` | Vários canais misturados |

---

### Etapa 7: Volume

**Título:** Quantos novos leads ou conversas comerciais recebem por semana?

**Campo:** `volume_leads`

**Opções:**
| Value | Label |
|-------|-------|
| `0_10` | 0 a 10 |
| `11_30` | 11 a 30 |
| `31_100` | 31 a 100 |
| `100_mais` | 100+ |

---

### Etapa 8: Dor (Multipla escolha)

**Título:** Onde você sente que mais perde oportunidades? (múltipla escolha)

**Campo:** `dor_principal`

**Opções:**
| Value | Label | Etapa Condicional |
|-------|-------|-----------------|
| `atendimento_lento` | Demora no primeiro atendimento | `dor_atendimento_lento` |
| `fora_horario` | Leads chegam fora do horário e ninguém responde | `dor_fora_horario` |
| `falta_followup` | Falta de follow-up | `dor_falta_followup` |
| `prospeccao_inconsistente` | A prospecção não acontece de forma consistente | `dor_prospeccao` |
| `converte_mal` | O comercial conversa, mas converte mal | `dor_converte` |
| `organizacao_baguncada` | Agendamento / repasse / organização são bagunçados | `dor_organizacao` |

---

### Etapas Condicionais de Detalhe da Dor

Cada seleção na etapa "dor" leva a uma pergunta específica de aprofundamento:

#### Se `atendimento_lento` → `dor_atendimento_lento`

**Título:** Quanto tempo um lead costuma esperar pelo primeiro retorno?

**Campo:** `dor_detalhe`

**Opções:**
- `menos_5min` - Menos de 5 min
- `5_30min` - 5 a 30 min
- `mais_30min` - Mais de 30 min
- `so_horario_comercial` - Só no horário comercial
- `nao_sei` - Não sei / varia muito

#### Se `fora_horario` → `dor_fora_horario`

**Título:** O que acontece quando alguém chama à noite ou no fim de semana?

**Opções:**
- `ninguem_responde` - Ninguém responde
- `responde_dia_seguinte` - Responde no dia seguinte
- `as_vezes_cobre` - Às vezes alguém cobre
- `tem_plantao` - Já temos plantão
- `sem_volume` - Não temos volume nesse horário

#### Se `falta_followup` → `dor_falta_followup`

**Título:** Quando o lead some, existe um processo para retomar a conversa?

**Opções:**
- `nao_existe` - Não existe
- `manual` - Existe, mas é manual
- `parcial` - Existe parcialmente
- `estruturado` - Sim, é bem estruturado

#### Se `prospeccao_inconsistente` → `dor_prospeccao`

**Título:** Sua prospecção acontece todos os dias ou depende do time lembrar?

**Opções:**
- `todo_dia` - Acontece todo dia
- `alguns_dias` - Acontece alguns dias
- `irregular` - É irregular
- `quase_nao` - Quase não acontece

#### Se `converte_mal` → `dor_converte`

**Título:** O que mais trava o fechamento hoje?

**Opções:**
- `lead_desqualificado` - Lead desqualificado
- `resposta_lenta` - Resposta lenta
- `falta_followup` - Falta de follow-up
- `objecoes_preco` - Objeções / preço
- `sem_processo` - Falta de processo comercial

#### Se `organizacao_baguncada` → `dor_organizacao`

**Título:** Hoje o lead consegue avançar sem依赖 de alguém do seu time estar online?

**Opções:**
- `nao` - Não
- `poucos_casos` - Em poucos casos
- `maioria` - Na maioria dos casos
- `sim` - Sim

---

### Etapa 9: Urgência

**Título:** Se a automação começasse a rodar nos próximos dias, qual cenário faz mais sentido?

**Campo:** `timing`

**Opções:**
| Value | Label |
|-------|-------|
| `agora` | Quero resolver isso agora |
| `este_mes` | Ainda neste mês |
| `proximo_mes` | Talvez no próximo mês |
| `entendendo` | Só estoy entendendo por enquanto |

---

### Etapa 10: Resultado (Finalização)

**Título:** Seu diagnóstico inicial está pronto

**Tipo:** Tela de resultado interativo

**Elementos exibidos:**
- Círculo de score animado (0-100)
- Badge de classificação (`quente` / `morno` / `frio`)
- Métricas calculadas
- Insight personalizado
- Botão CTA conforme pontuação

---

## Lógica de Finalização

1. Usuário chega na etapa `resultado`
2. Score é calculado em tempo real (client-side preview)
3. Ao clicar no CTA final:
   - `submitQuiz()` envia todas as respostas para API
   - API valida e calcula score/server-side
   - Classificação: `quente` (score ≥ 70), `morno` (40-69), `frio` (< 40)
   - Trilha: `consultiva` (até R$ 50k) ou `acelerada` (acima de R$ 50k)
4. Webhook enviado para Evolution API + Meta CAPI
5. LocalStorage limpo
6. CTA final é selecionado según score:
   - Score ≥ 70: "Alta prioridade: quero acelerar minha automação"
   - Score 40-69: "Quero ver o plano ideal para meu cenário"
   - Score < 40: "Quero mapear meus próximas passos"

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
| dor | dor_atendimento_lento (se atendimento_lento selecionado) |
| dor | dor_fora_horario (se fora_horario selecionado) |
| dor | dor_falta_followup (se falta_followup selecionado) |
| dor | dor_prospeccao (se prospeccao_inconsistente selecionado) |
| dor | dor_converte (se converte_mal selecionado) |
| dor | dor_organizacao (se organizacao_baguncada selecionado) |
| [qualquer dor_detalhe] | urgencia |
| urgencia | resultado |
| resultado | FIM (envio para API) |