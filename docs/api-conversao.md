# API de Conversão - ChamaLead

O sistema possui 3 endpoints principais para captura e conversão de leads.

## 1. API Pública de Leads Simples

**Arquivo:** `/var/www/chamalead/api.php`

**Método:** POST

**Endpoint:** `api.php`

**Payload:**
```json
{
  "nome": "João Silva",
  "empresa": "Empresa Ltda",
  "whatsapp": "11999999999",
  "instagram": "username",
  "faturamento": "ate_10k",
  "desafio": "gerar_leads"
}
```

**Validações:**
- `nome`: obrigatório, 2-120 caracteres
- `empresa`: obrigatório, 2-120 caracteres
- `whatsapp`: obrigatório, telefone brasileiro válido (10-11 dígitos após formatação)
- `instagram`: opcional, username válido (1-30 caracteres alfanuméricos)
- `faturamento`: obrigatório, deve constar em `['ate_10k', '10k_20k', '20k_50k', '50k_100k', 'acima_100k']`
- `desafio`: obrigatório, chave válida de `getDesafioLabels()`

**Resposta (sucesso):**
```json
{
  "success": true,
  "message": "Lead cadastrado com sucesso",
  "id": 123
}
```

**Resposta (erro):**
```json
{
  "success": false,
  "message": "Formulario invalido",
  "errors": ["Nome completo e obrigatorio", ...]
}
```

**Fluxo:**
1. Recebe dados via POST
2. Valida campos individualmente
3. Formata WhatsApp com `formatWhatsApp()`
4. Converte faixa de faturamento para valor numérico com `getFaturamentoValor()`
5. Insere na tabela `leads` com status `novo`
6. Retorna JSON com ID do lead inserido

---

## 2. API do Quiz Comercial

**Arquivo:** `/var/www/chamalead/quiz/api.php`

**Método:** POST

**Endpoint:** `quiz/api.php`

**Content-Type:** `application/json`

**Payload (submit):**
```json
{
  "action": "submit",
  "session_id": "abc123",
  "nome": "João Silva",
  "whatsapp": "11999999999",
  "cargo": "gestor",
  "faturamento": "20k_50k",
  "canal": "instagram",
  "volume_leads": "50_100",
  "dor_principal": "quantidade",
  "dor_detalhe": "Preciso de mais leads",
  "timing": "imediato",
  "current_step": 10,
  "utm_source": "facebook",
  "utm_medium": "cpc",
  "utm_campaign": "quiz_comercial",
  "fbp": "fb.1.123456",
  "fbc": "fb.1.123456.abc",
  "client_ip_address": "192.168.1.1",
  "client_user_agent": "Mozilla/5.0..."
}
```

**Payload (validate-phone):**
```json
{
  "action": "validate-phone",
  "phone": "11999999999"
}
```

**Validações (submit):**
- `session_id`: obrigatório
- `nome`: obrigatório
- `whatsapp`: obrigatório, validado via `PhoneParser::parse()`
- `cargo`: opcional, validado via `validateQuizField('cargo', $cargo)`
- `faturamento`: opcional, validado via `validateQuizField('faturamento', $faturamento)`
- `canal`: opcional, validado via `validateQuizField('canal', $canal)`
- `volume_leads`: opcional, validado via `validateQuizField('volume_leads', $volumeLeads)`
- `dor_principal`: opcional, validado via `validateQuizField('dor_principal', $dorPrincipal)`
- `timing`: opcional, validado via `validateQuizField('timing', $timing)`

**Resposta (validate-phone):**
```json
{
  "success": true,
  "valid": true,
  "normalized": "551199999999",
  "ddd": "11",
  "carrier": "Vivo",
  "carrier_is_guaranteed": false,
  "line_type": "mobile",
  "state": "SP",
  "state_name": "São Paulo"
}
```

**Resposta (submit):**
```json
{
  "success": true,
  "message": "Quiz finalizado com sucesso",
  "score": 85,
  "classificacao": "quente",
  "trilha": "media",
  "status": "completed"
}
```

**Fluxo (submit):**
1. Recebe dados via JSON no body
2. Valida telefone com `PhoneParser::parse()` (DDD, formato, tamanho)
3. Valida todos os campos com `validateQuizField()`
4. Calcula score com `calculateQuizScore($answers)`
5. Determina trilha com `determineTrack($faturamento)`
6. Insere/atualiza na tabela `quiz_leads` via `INSERT OR REPLACE`
7. Se `status === 'completed'` (current_step >= 10):
   - Envia webhook para Evolution API via `sendQuizLeadToEvolution()`
   - Envia evento para Meta CAPI via `MetaConversionsApiService::sendLead()`
   - Salva resposta dos webhooks em `webhook_response`

**Parâmetros UTM:**
- `utm_source`
- `utm_medium`
- `utm_campaign`
- `utm_content`
- `utm_term`

**Parâmetros Meta CAPI:**
- `fbp` (Facebook Pixel ID)
- `fbc` (Facebook Click ID)
- `client_ip_address`
- `client_user_agent`

---

## 3. Meta Conversions API (Facebook)

**Arquivo:** `/var/www/chamalead/MetaConversionsApiService.php`

**Classe:** `MetaConversionsApiService`

**Construtor:**
```php
$meta = new MetaConversionsApiService(
    $pixelId,      // default: env META_CAPI_PIXEL_ID ou '1386130056894015'
    $accessToken, // default: env META_CAPI_ACCESS_TOKEN
    $apiVersion   // default: env META_CAPI_API_VERSION ou 'v25.0'
);
```

**Método:** `sendLead(array $leadData): array`

**Payload `$leadData`:**
```php
[
    'session_id' => 'abc123',
    'whatsapp' => '551199999999',
    'email' => 'email@exemplo.com',
    'fbp' => 'fb.1.123456',
    'fbc' => 'fb.1.123456.abc',
    'client_ip_address' => '192.168.1.1',
    'client_user_agent' => 'Mozilla/5.0...',
    'score' => 85,
    'classificacao' => 'quente',
    'faturamento' => '20k_50k',
    'trilha' => 'media',
    'event_id' => 'quiz_abc123',
    'event_time' => 1712000000,
    'event_source_url' => 'https://exemplo.com/quiz',
    'test_event_code' => 'TEST12345', // opcional
]
```

**Requisição Meta CAPI:**
```json
{
  "data": [{
    "event_name": "Lead",
    "event_time": 1712000000,
    "event_id": "quiz_abc123",
    "action_source": "website",
    "event_source_url": "https://exemplo.com/quiz",
    "user_data": {
      "fbp": "fb.1.123456",
      "fbc": "fb.1.123456.abc",
      "client_ip_address": "192.168.1.1",
      "client_user_agent": "Mozilla/5.0...",
      "ph": "sha256_hash_do_telefone",
      "em": "sha256_hash_do_email"
    },
    "custom_data": {
      "content_name": "Quiz Comercial",
      "content_category": "lead_generation",
      "status": "quente",
      "score": 85,
      "faturamento": "20k_50k",
      "trilha": "media"
    }
  }]
}
```

**URL:** `https://graph.facebook.com/v25.0/{pixel_id}/events`

**Resposta:**
```json
{
  "success": true,
  "http_code": 200,
  "response": {
    "events_received": 1,
    "fbtrace_id": "abc123"
  }
}
```

**Resposta (erro):**
```json
{
  "success": false,
  "http_code": 400,
  "response": {
    "error": {
      "message": "Invalid parameter",
      "type": "OAuthException",
      "code": 190
    }
  }
}
```

**Método `isConfigured()`:**
```php
$meta->isConfigured(); // bool
```
Retorna `true` se `pixelId` e `accessToken` estiverem configurados.

---

## PhoneParser - Biblioteca de Validação de Telefone

**Arquivo:** `/var/www/chamalead/panel/PhoneParser.php`

**Namespace:** `Panel`

**Método:** `PhoneParser::parse(string $input): array`

**Exemplo de uso:**
```php
$parsed = Panel\PhoneParser::parse('11999999999');

/*
$result = [
    'is_valid' => true,
    'input' => '11999999999',
    'normalized' => '551199999999',
    'country_code' => '55',
    'ddd' => '11',
    'subscriber' => '999999999',
    'type' => 'mobile',
    'carrier_inferred_from_prefix' => 'Vivo',
    'carrier_is_guaranteed' => false
]
*/
```

**Validações:**
- Comprimento: 10-13 dígitos com código do país (+55)
- DDD válido: 11-99
- Celular deve começar com 9 após o DDD

**Carrier detection:**
- Arquivo: `/var/www/chamalead/carrier.txt` (formato: `prefix|carrier`)
- Prefixos fixos manuais para correções
- Prioridade: prefixos maiores (mais específicos) primeiro

**DDD para Estado:**
```php
Panel\PhoneParser::getStateFromDDD('11');      // 'SP'
Panel\PhoneParser::getStateNameFromDDD('11');   // 'São Paulo'
```

---

## Tabelas SQLite

### `leads`
| Coluna | Tipo | Descrição |
|--------|------|------------|
| id | INTEGER | PK auto-incremento |
| nome | TEXT | Nome completo |
| empresa | TEXT | Nome da empresa |
| whatsapp | TEXT | Telefone formatado |
| instagram | TEXT | Username |
| faturamento | TEXT | Faixa de faturamento |
| faturamento_valor | INTEGER | Valor numérico |
| desafio | TEXT | Desafio selecionado |
| status | TEXT | Status do lead |
| created_at | DATETIME | Criação |
| updated_at | DATETIME | Atualização |

### `quiz_leads`
| Coluna | Tipo | Descrição |
|--------|------|------------|
| session_id | TEXT | PK única |
| nome | TEXT | Nome |
| whatsapp | TEXT | Telefone |
| cargo | TEXT | Cargo |
| faturamento | TEXT | Faixa |
| faturamento_valor | INTEGER | Valor numérico |
| canal | TEXT | Canal de origem |
| volume_leads | TEXT | Volume |
| dor_principal | TEXT | Dor principal |
| dor_detalhe | TEXT | Detalhe |
| timing | TEXT | Timing |
| score | INTEGER | Score calculado |
| classificacao | TEXT | Classificação |
| trilha | TEXT | Trilha |
| utm_source | TEXT | UTM source |
| utm_medium | TEXT | UTM medium |
| utm_campaign | TEXT | UTM campaign |
| utm_content | TEXT | UTM content |
| utm_term | TEXT | UTM term |
| status | TEXT | Status |
| current_step | INTEGER | Passo atual |
| webhook_sent_at | DATETIME | Envio webhook |
| webhook_response | TEXT | Resposta webhook |
| updated_at | DATETIME | Atualização |

---

## Variáveis de Ambiente

```bash
# Meta Conversions API
META_CAPI_PIXEL_ID=1386130056894015
META_CAPI_ACCESS_TOKEN=seu_token_aqui
META_CAPI_API_VERSION=v25.0
META_CAPI_TEST_EVENT_CODE=TEST12345

# Evolution API (config.php)
EVOLUTION_API_URL=https://...
EVOLUTION_API_KEY=...
```