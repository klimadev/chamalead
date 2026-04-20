# Documentação do Quiz Analise Rápida

## Visão Geral

Quiz de qualificação para a Chamalead que avalia o potencial de clientes B2B em recuperar receita perdida por problemas no funil de vendas e atendimento. O quiz foi reformulado para usar linguagem simples e direta, sem jargões técnicos.

### Objetivos

1. Identificar onde a operação está perdendo dinheiro
2. Classificar o lead como qualificado ou não qualificado
3. Calcular uma estimativa conservadora de valor recuperável
4. Direcionar para a próxima etapa (agendamento ou nutrição)

---

## Fluxo do Quiz

### Etapas

| # | Passo ID | Título / Pergunta |
|---|---------|------------------|
| 1 | `welcome` | Sua empresa pode estar perdendo dinheiro sem perceber. |
| 2 | `identify` | Antes de começar, como você se chama? (nome + WhatsApp) |
| 3 | `cargo` | Qual é sua função na empresa? |
| 4 | `faturamento` | Quanto sua empresa fatura por mês? |
| 5 | `origem_negocio` | Hoje sua empresa vive mais de quê? |
| 6 | `volume` | Quantos contatos/atendimentos por mês? |
| 7 | `valor_medio` | Em média, quanto vale uma venda/atendimento? |
| 8 | `dor` | Onde você sente que mais perde clientes ou dinheiro? |
| 9 | `[condicional]` | Pergunta extra conforme a resposta anterior |
| 10 | `historico` | Você tem lista dos últimos 3 meses? |
| 11 | `envio_info` | Consegue mandar info na reunião? |
| 12 | `urgencia` | Quando quer resolver? |
| 13 | `decisao` | Você pode decidir na reunião? |
| 14 | `resultado` | Análise liberada ou recebida |

### Condicionais de Problema

| Problema Escolhido | ID da Condicional | Pergunta Extra |
|-------------------|------------------|----------------|
| Demoram para responder... | `dor_resposta` | Quanto tempo levam para responder? |
| A pessoa pede info, mas some... | `dor_some` | Tentam falar de novo quantas vezes? |
| Clientes antigos... | `dor_clientes_antigos` | Costumam chamar clientes de volta? |
| Fora do horário... | `dor_fora_horario` | O que acontece à noite/fim de semana? |
| Agenda vazia... | `dor_agenda` | Quantos horários perde por semana? |
| Tudo bagunçado... | `dor_organizacao` | Onde ficam as informações? |

---

## Lógica de Qualificação

### Regra: 3 de 4

O lead é **qualificado** quando satisfaz **pelo menos 3** destes 4 critérios:

| Critério | Respostas Que Contam |
|---------|-------------------|
| **Tem histórico** | "Sim, está tudo organizado" / "Tenho boa parte" |
| **Consegue enviar** | "Sim" / "Consigo mandar logo depois" |
| **Quer resolver** | "Agora" / "Ainda esta semana" / "Ainda este mês" |
| **Pode decidir** | "Sim" / "Eu participo da decisão" |

### Resultados

- **Qualificado (3+ sinais)**: Acesso à análise completa estimada + CTA para agendar
- **Não qualificado (< 3 sinais)**: Mensagem genérica + CTA para finalizar

---

## Cálculo do Valor Recuperável

### Fórmula

```
valor_recuperavel = volume_mensal × valor_medio × fator_perda × 0.45 × fator_urgencia
```

### Parâmetros

| Volume | Valor Médio | Perda | Urgência |
|--------|------------|-------|----------|
| Até 30 → 20 | Até R$ 100 → 80 | Demoram 18% | Agora → 1.0 |
| 31-100 → 65 | R$ 101-300 → 200 | Some 12% | Esta semana → 0.92 |
| 101-300 → 200 | R$ 301-800 → 550 | Clientes antigos 10% | Este mês → 0.82 |
| 301-1000 → 650 | R$ 801-2000 → 1300 | Fora horário 14% | Só olhar → 0.68 |
| 1000+ → 1200 | 2000+ → 3000 | Agenda 16% | - |
| - | - | Bagunçado 11% | - |

### Observações

- `0.45` é o fator conservador (45% do valor teorizado)
- Valor mínimo garantizado: R$ 3.000
- Arredondado para a centena mais próxima

---

## Mascote Ignis

O mascote visual foi mantido exatamente como no desenho original. Apenas as falas foram atualizadas para o tom conversado.

### Falas do Ignis

| Momento | Fala |
|--------|------|
| Início (1s) | "Vou te fazer perguntas simples para entender sua empresa." |
| Tela ID | "Primeiro, preciso do seu nome e do seu WhatsApp." |
| Cargo | "[Nome], agora quero entender sua empresa." |
| Volume | "Agora preciso de dois números rápidos." |
| Problema | "Aqui costuma aparecer onde o dinheiro está escapando." |
| Histórico | "Última parte. Isso ajuda a saber se dá para olhar seu caso." |
| Resultado (Q) | "Sua análise foi liberada." |
| Resultado (NQ) | "Recebemos suas respostas." |
| Ao clicar | "Estou aqui com você. Falta pouco." |

### Estados Visuais

| Classe | Descrição |
|--------|----------|
| (padrão) | Olhos abertos, flutuando |
| `look-left` | Olhos olham para a esquerda |
| `typing` | Olhos apertados (digitando) |
| `shock` | Olhos SURPRESO |
| `happy` | Olhos felizes |
| `think` | Olhos pensativos |

---

## Validação

### Teste Automatizado (Playwright)

Dois cenários foram testados no navegador real:

#### Cenário 1: Lead Qualificado

Respostas:
- Nome: Carlos
- WhatsApp: 11987654321
- Cargo: Sou o dono
- Faturamento: De R$ 100 mil a R$ 300 mil
- Origem: Agendamentos
- Volume: De 301 a 1000
- Valor médio: De R$ 801 a R$ 2.000
- Problema: Demoram para responder
- Detalhe: Em algumas horas
- Histórico: Sim, está tudo organizado
- Envio: Sim
- Urgência: Agora
- Decisão: Sim

**Resultado Validado:**
- Título: "Carlos, sua análise foi liberada."
- CTA: "Escolher meu horário"
- Valor: R$ 67.306 ✓

#### Cenário 2: Lead Não Qualificado

Respostas:
- Nome: Maria
- WhatsApp: 11911112222
- Cargo: Sou gerente
- Faturamento: Até R$ 20 mil
- Origem: Um pouco de tudo
- Volume: Até 30
- Valor médio: Até R$ 100
- Problema: Está tudo bagunçado
- Detalhe: Não temos controle direito
- Histórico: Não tenho isso organizado
- Envio: Não
- Urgência: Só quero entender melhor
- Decisão: Não

**Resultado Validado:**
- Título: "Recebemos suas respostas."
- CTA: "Finalizar"
- Valor estimado: oculto ✓

### Console do Navegador

- Erros: 1 (apenas `favicon.ico` 404)
- Warnings: 0
- JS funcional: 100%

---

## Palavras Proibidas Removidas

A copy original continha termos técnicos que foram substituídos:

| Antes | Depois |
|-------|--------|
| lead | contato |
| follow-up | tentar falar de novo |
| funil | - (não usado) |
| conversão | - (não usado) |
| automação | - (não usado) |
| ticket | valor |
| CRM | sistema |
| diagnóstico | análise |
| pipeline | - (não usado) |
| score | valor |
| follow-up | tentar falar de novo |
| followup | tentar falar de novo |

---

## Arquitetura Técnica

### Estrutura de Arquivos

```
quiz2/
├── index.html          # Quiz completo (único arquivo)
├── DOCUMENTATION.md  # Este arquivo
└── (temporário)      # Servidor local para testes
```

### Estrutura HTML

- Tudo em um único arquivo `index.html`
- CSS inline no `<head>`
- JS inline antes do `</body>`
- Sem dependências externas de runtime

### performance

- CSS crítico inline (~6KB)
- Sem CDN externo
- Sem bibliotecas JS
- Sem webfonts
- Meta FCP: < 1s (calculado)

### Acessibilidade

- Labels em todos os campos
- Roles ARIA no mascote
- Estados de focus visíveis
- Contraste AA no tema escuro

---

## Próximos Passos Recomendados

1. **Integração com agenda**: Substituir `alert()` do CTA por link real (Calendly, Hubspot, etc)
2. **Webhook de dados**: Enviar respostas para o CRM após submissão
3. **Analytics**: Rastrear eventos de conversão por etapa
4. **Testes E2E**: Criar suite de testes com Jest + Playwright
5. **SEO**: Adicionar meta description e dados estruturados

---

## Changelog

| Data | Mudança |
|------|--------|
| 2026-04-20 | Versão inicial reescrita com nova copy simples |
| 2026-04-20 | Bifurcação final implementada (3 de 4) |
| 2026-04-20 | Mascote mantido com falas atualizadas |
| 2026-04-20 | Testes Playwright validados |