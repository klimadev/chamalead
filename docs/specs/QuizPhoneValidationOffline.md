# Spec: QuizPhoneValidationOffline

Scope: feature

# SPEC: Validação de Telefone Offline (Sem API)

## 1. Visão Geral

Implementar validação de telefone brasileiro no quiz usando apenas dicionários locais (dados públicos da Anatel). Sem chamadas externas.

## 2. Validação Offline

### 2.1 Formato Brasileiro
- **Celular**: DDD + 9 dígitos (11 dígitos total) — começa com 9 após DDD
- **Fixo**: DDD + 8 dígitos (10 dígitos total) — não é WhatsApp
- **DDD válido**: 11-99 (regiões brasileiras)

### 2.2 Dicionário DDD → Estado
PHP array com todos os 88 DDDs do Brasil:
```php
$dddToState = [
    '11' => 'SP', '12' => 'SP', '13' => 'SP', '14' => 'SP', '15' => 'SP',
    '16' => 'SP', '17' => 'SP', '18' => 'SP', '19' => 'SP',
    '21' => 'RJ', '22' => 'RJ', '24' => 'RJ',
    '31' => 'MG', '32' => 'MG', '33' => 'MG', '34' => 'MG', '35' => 'MG', '36' => 'MG', '37' => 'MG', '38' => 'MG',
    // ... todos os 88 DDDs
];
```

### 2.3 Dicionário Ranges de Operadoras (Anatel)
Ranges de números por operadora (prefixos públicos):
```php
$carrierRanges = [
    'Vivo' => ['21', '22', '23', '24', '25', '26', '27', '28', '29', '31', '32', '33', '34', '35', '41', '42', '43', '44', '45', '46', '51', '52', '53', '54', '55', '61', '62', '63', '64', '65', '66', '67', '68', '69', '71', '73', '74', '75', '77', '79', '81', '82', '83', '84', '85', '86', '87', '88', '89', '91', '92', '93', '94', '95', '96', '97', '98', '99'],
    'Claro' => ['21', '22', '23', '24', '41', '42', '43', '44', '45', '51', '52', '53', '61', '62', '63', '64', '65', '66', '67', '71', '72', '73', '74', '75', '77', '81', '82', '83', '84', '85', '86', '87', '91', '92', '93', '94', '95', '96', '97'],
    'TIM' => ['21', '22', '23', '24', '41', '42', '43', '44', '45', '46', '51', '52', '53', '61', '62', '63', '64', '65', '66', '71', '81', '82', '83', '84', '85', '91', '92', '93', '94', '95', '96', '97', '98', '99'],
    'Oi' => ['31', '32', '33', '34', '35', '36', '37', '38', '21', '22', '23', '24', '51', '52', '53', '54', '55', '61', '62', '63', '64', '65', '71', '72', '73', '74', '75', '76', '77', '78', '79', '81', '82', '83', '84', '85', '86', '87', '88', '89', '91', '92', '93', '94', '95', '96'],
    // Obs: ranges podem overlaps, usar heurística baseada no segundo dígito após DDD
];
```

### 2.4 Heurística de Carrier (Offline)
Para determinar operadora sem API:
- Analisar o **primeiro dígito após o DDD** (9 para celular)
- Se 9 → verificar se é 91-99 (Vivo), 21-99 (Claro), etc
- Usar dados públicos de prefixos Anatel

## 3. Backend (PHP)

### 3.1 Função: validatePhoneOffline(string $phone): array
```php
function validatePhoneOffline(string $phone): array {
    $digits = preg_replace('/[^0-9]/', '', $phone);
    
    // 1. Validar comprimento
    if (strlen($digits) === 11 && $digits[2] === '9') {
        // Celular válido
        $ddd = substr($digits, 0, 2);
        $state = getStateFromDDD($ddd);
        $carrier = getCarrierFromDigits($digits);
        
        return [
            'valid' => true,
            'carrier' => $carrier,
            'state' => $state,
            'line_type' => 'mobile'
        ];
    }
    
    if (strlen($digits) === 10) {
        return [
            'valid' => false,
            'error' => 'Número fixo (não serve para WhatsApp)'
        ];
    }
    
    return ['valid' => false, 'error' => 'Formato inválido'];
}
```

## 4. Frontend

### 4.1 Validação em Tempo Real
- Ao digitar (debounce 500ms): validar formato localmente
- Mostrar badge "Celular" / "Fixo" / "Inválido"
- Mostrar Estado (SP, RJ, MG) - via DDD local
- Mostrar Operadora предполагаемую - via heurística

### 4.2 Badges UI (Atualizado)

| Badge | Cor | Conteúdo |
|-------|-----|----------|
| Inválido | Vermelho | "✗ Número inválido" |
| Fixo | Amarelo | "Número fixo (WhatsApp só para celular)" |
| Estado | Cinza | "SP", "RJ", "MG" etc |
| Operadora | Azul | "Vivo", "Claro", "TIM", "Oi" |

### 4.3 Validação Real (sem API)
Para **verificar se o número existe mesmo** (sem API):
- Não é possível offline com 100% certeza
- Apenas validar **formato válido de celular brasileiro**
- O usuário confirma via código SMS depois (se implementar)
- Por agora: aceitar qualquer celular válido (11 dígitos, começa com 9)

## 5. Spam Prevention (Sem API)

### 5.1 Rate Limit Local (PHP session)
- Contador em memória: `$session['phone_validation_attempts']`
- Máximo 3 tentativas por IP + session
- Incremental: bloquear após 3 falhas

### 5.2 Frontend
- Desabilitar botão submit enquanto telefone não válido
- Mostrar erro claro se tentar burlar

## 6. Banco de Dados

Não precisa de nova tabela — validar on-the-fly.

## 7. Diferenças da Versão com API

| Aspecto | Com API | Offline |
|---------|---------|---------|
| Valida número existe | ✅ Sim | ❌ Não |
| Carrier real | ✅ Sim | ⚠️ Suposição |
| Estado real | ✅ Sim (BrasilAPI) | ✅ Sim (DDD) |
| Custo | $$$ | Grátis |
| Confiabilidade | Alta | Média |

## 8. Trade-off

**Sem API**: Validamos formato, DDD → Estado, e detectamos celular vs fixo. Não sabemos se o número realmente existe ( precisa de API para isso).

**Recomendação**: Aceitar formato válido como "verificado" e adicionar verificação por OTP/SMS se quiser certeza total. Para agora, formato válido é suficiente para bloquear spam óbvio.

## 9. Implementação

1. Adicionar `validatePhoneOffline()` em config.php
2. Adicionar endpoint em quiz/api.php
3. Frontend: badges de Estado + Operadora (heurística)
4. Validar formato antes de permitir submit
5. Rate limit por session/IP