# Spec: QuizPublicRoute

Scope: feature

# QuizPublicRoute

## Objetivo
Migrar o quiz atual de `quiz.php` para uma estrutura dedicada em `/quiz/`, com organização modular interna, mantendo o comportamento visual, funcional e comercial do fluxo existente.

## Escopo
- Publicar `/quiz/` como rota principal do quiz.
- Estruturar a feature em uma pasta dedicada com `index.php` como entrada principal.
- Separar a implementação em módulos internos de baixo risco.
- Preservar a API do quiz em estrutura equivalente dentro da pasta dedicada.
- Manter compatibilidade temporária com `quiz.php` e `quiz-api.php` durante a transição controlada.

## Invariantes
- O quiz deve continuar com a mesma ordem de etapas, mesmas decisões condicionais e mesmo fluxo de navegação.
- O HTML final relevante para o comportamento do JS deve permanecer compatível: mesmos IDs, `data-step`, classes de controle e estrutura necessária para eventos.
- O CSS resultante deve preservar aparência, densidade, responsividade e comportamento sem rolagem.
- O JavaScript deve preservar:
  - chave de `localStorage` `chamalead_quiz`
  - cálculo de progresso e etapas condicionais
  - payload enviado ao backend
  - integração com Meta Pixel
  - mensagens e CTA de resultado
- A API deve preservar:
  - contrato JSON de entrada e saída
  - gravação em `quiz_leads`
  - score, classificação e trilha
  - integrações com Evolution e Meta Conversions API
- A migração não deve alterar regras de negócio na mesma entrega estrutural.

## Estratégia de Entrega
1. Criar a nova superfície `/quiz/` com `index.php` e `api.php`.
2. Centralizar resolução de paths/URLs para evitar que assets e chamadas AJAX quebrem ao sair da raiz.
3. Fazer uma migração inicial sem reescrita de lógica, apenas reorganização segura.
4. Depois da nova rota estável, quebrar `index.php` em módulos estáveis:
   - bootstrap
   - head
   - styles
   - steps/layout
   - script principal
5. Tratar compatibilidade legada como camada temporária, não como arquitetura final.

## Requisitos de Segurança da Refatoração
- Não remover entradas antigas no primeiro deploy.
- Não renomear campos, eventos, chaves de storage, parâmetros de tracking ou nomes de colunas.
- Não introduzir bundler, build frontend, framework JS ou alteração de stack nesta etapa.
- Não misturar refatoração estrutural com redesign ou recalibração de score.
- Evitar duplicação nova; quando possível, centralizar mapeamentos e constantes sem alterar comportamento.

## Critérios de Aceite
- `/quiz/` abre corretamente e reproduz o fluxo atual do quiz.
- Nome, WhatsApp, etapas condicionais, urgência, resultado e refazer funcionam como hoje.
- Assets carregam corretamente na nova rota.
- A chamada da API funciona na nova estrutura sem erro de caminho.
- O backend continua gravando em `quiz_leads` e retornando `score`, `classificacao`, `trilha` e `status`.
- O estado salvo em `localStorage` continua restaurando o progresso.
- Não há regressão visual perceptível entre a versão antiga e a nova.
- `php -l` passa em todos os arquivos PHP tocados.

## Compatibilidade e Transição
- `quiz.php` e `quiz-api.php` podem permanecer como ponte temporária durante a migração.
- A remoção ou redirecionamento das rotas legadas deve ocorrer apenas após validação da nova rota em produção ou homologação.

## Fora de Escopo
- Reescrever o quiz em framework/frontend moderno.
- Trocar banco, contrato da API ou integrações externas.
- Alterar copy, design, score ou classificação por motivos de produto.
- Fazer otimizações amplas fora do domínio do quiz.

## Riscos Conhecidos
- Quebra de path relativo ao mover a entrada para `/quiz/`.
- Quebra de JS por alteração involuntária em IDs, `data-step` ou ordem estrutural.
- Divergência entre regras duplicadas de front e back.
- Perda de tracking ou falha de POST se a URL da API não for centralizada.

## Validação Recomendada
- Checklist manual do fluxo completo.
- Verificação visual das telas inicial, nome, WhatsApp e resultado.
- Inspeção de request da API no navegador.
- Verificação do persistence/restore com recarga de página.
- Checagem de logs/retorno do backend para submissão final.