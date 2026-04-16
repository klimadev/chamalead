---
plan name: QuizPhoneValidation
plan description: Validação de telefone com carrier
plan status: active
---

## Idea
Implementar validação de telefone brasileiro no quiz com verificação de número real via API, detecção de operadora (carrier) e estado (via DDD), mostrando em badges. Isso previne spam e números inválidos.

## Implementation
- 1. Adicionar nova API endpoint em quiz/api.php para validação de telefone (POST /validate-phone)
- 2. Implementar função PHP em config.php usando Numverify/Abstract API para validar se número é real e obter carrier
- 3. Adicionar integração com BrasilAPI para detectar estado a partir do DDD do telefone
- 4. Modificar frontend (interaction-validation-and-submit.php) para chamar API de validação ao digitar/sair do campo whatsapp
- 5. Exibir badges de validação: carregando → válido/inválido com cor verde/vermelha
- 6. Exibir badge de operadora (Oi, Vivo, Claro, TIM, etc.) em azul
- 7. Exibir badge de estado (SP, RJ, MG, etc.) em cinza
- 8. Implementar rate limiting: no máximo 3 verificações por sessão para evitar spam
- 9. Armazenar resultado de validação no banco para evitar revalidação
- 10. Só liberar botão final se telefone validado = true

## Required Specs
<!-- SPECS_START -->
- QuizPhoneValidation
- QuizPhoneValidationOffline
<!-- SPECS_END -->