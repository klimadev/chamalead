# Spec: QuizPhoneValidation

Scope: feature

# SPEC: Validação de Telefone com Carrier e Estado no Quiz

## 1. Visão Geral

Implementar validação em tempo real do número de WhatsApp no quiz com:
- Verificação se número é real/válido via API externa
- Detecção da operadora (carrier) via API
- Detecção do estado via DDD (BrasilAPI)
- Exibição em badges visuais
- Bloqueio do botão final até validação positiva

## 2. APIs Utilizadas

### 2.1 BrasilAPI (DDD → Estado) - GRÁTIS
- **Endpoint**: `https://brasilapi.com.br/api/ddd/v1/{ddd}`
- **Retorno**: `{ "state": "SP", "cities": ["São Paulo", "Campinas", ...] }`
- **Rate limit**: 200 req/min
- **Fallback**: Dicionário local PHP (DDD → Estado)

### 2.2 AbstractAPI (Validação + Carrier) - PAGO (Free tier disponível)
- **Endpoint**: `https://phonevalidation.abstractapi.com/v1/`
- **Params**: `api_key`, `phone`, `country=br`
- **Retorno**: `{ "valid": true, "carrier": "Vivo", "line_type": "mobile", "country": "BR" }`
- **Custo**: 10k req/mês gratuito

## 3. Backend (PHP)

### 3.1 Novo endpoint: POST /quiz/api.php?action=validate-phone
```php
// Request
{ "phone": "11999999999" }

// Response sucesso
{
  "success": true,
  "valid": true,
  "carrier": "Vivo",
  "line_type": "mobile", // "mobile" | "landline"
  "state": "SP",
  "state_name": "São Paulo"
}

// Response erro/inválido
{
  "success": true,
  "valid": false,
  "error": "Número inválido ou inexistente"
}
```

### 3.2 Função PHP: validatePhoneNumber(string $phone): array
- Limpa phone (remove caracteres não numéricos)
- Valida formato brasileiro (10 ou 11 dígitos com DDD)
- Chama AbstractAPI para validação + carrier
- Chama BrasilAPI para estado via DDD
- Cache em memória (10 min) para evitar revalidação

### 3.3 Rate Limiting
- Máximo 3 validações por session_id
- Registrar tentativas em `quiz_phone_validations` table
- Bloquear após limite excedido

## 4. Frontend (JavaScript)

### 4.1 Fluxo de Validação
1. Usuário digita telefone → formatação visual (mask)
2. Ao sair do campo (blur) ou após 1 segundo sem digitar → chamada API
3. Mostrar badge "verificando..." (laranja, animado)
4. Receber resposta → mostrar badge resultado

### 4.2 Badges UI

| Badge | Cor | Conteúdo | Condição |
|-------|-----|----------|----------|
| Validando | Laranja (#f97316) | "Verificando..." + spinner | Enquanto carrega |
| Válido | Verde (#22c55e) | "✓ Número válido" | valid === true |
| Inválido | Vermelho (#ef4444) | "✗ Número inválido" | valid === false |
| Operadora | Azul (#3b82f6) | "Vivo" / "Claro" / "TIM" / "Oi" / "Outro" | Se valid |
| Estado | Cinza (#6b7280) | "SP" / "RJ" / "MG" | Se valid |

### 4.3 Posicionamento
- Acima do input de telefone
- Layout flexbox com gap de 8px
- Badges de validação (verde/vermelho) acima
- Badges de info (operadora + estado) abaixo do input

### 4.4 Código JS
```javascript
// Debounce de 1 segundo para validar
let phoneValidateTimeout = null;
inputWhatsapp.addEventListener('input', () => {
  clearTimeout(phoneValidateTimeout);
  phoneValidateTimeout = setTimeout(() => validatePhone(phoneValue), 1000);
});

inputWhatsapp.addEventListener('blur', () => {
  if (phoneValue.length >= 10) validatePhone(phoneValue);
});
```

## 5. Banco de Dados

### 5.1 Nova tabela: quiz_phone_validations
```sql
CREATE TABLE quiz_phone_validations (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  session_id TEXT NOT NULL,
  phone TEXT NOT NULL,
  valid INTEGER NOT NULL,
  carrier TEXT,
  line_type TEXT,
  state TEXT,
  validated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### 5.2 Alterar tabela: quiz_leads
Adicionar coluna `phone_validated` (BOOLEAN DEFAULT 0)

## 6. Segurança

- Rate limit: 3 requisições por sessão
- Não expor chaves de API no frontend (PHP faz as chamadas)
- Sanitizar input do phone antes de enviar
- Timeout de 5s na chamada API (evitar request lenta)

## 7. Fallbacks

- Se AbstractAPI indisponível → aceitar apenas formato válido (10-11 dígitos)
- Se BrasilAPI indisponível → usar dicionário local DDD→Estado
- Se todas APIs falharem → permitir envio (mas logar warning)

## 8. Testes

- Telefone válido (Vivo/SP): 11999999999 → valid=true, carrier=Vivo, state=SP
- Telefone inválido: 11999999998 → valid=false
- Telefone landline: 1139999999 → valid=true, line_type=landline (aceitar se for WhatsApp)
- Rate limit: 4ª tentativa → retornar erro "Limite atingido"

## 9. Cronologia de Execução

1. Criar tabela quiz_phone_validations + coluna phone_validated
2. Implementar validatePhoneNumber() em config.php
3. Adicionar endpoint /validate-phone em quiz/api.php
4. Adicionar UI badges em core-shell-and-context-steps.php
5. Implementar validatePhone() no frontend (JS)
6. Modificar submitQuiz() para checar phone_validated
7. Testes end-to-end