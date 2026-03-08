#!/usr/bin/env bash

set -euo pipefail

WEBHOOK_URL="http://localhost:5678/webhook-test/prospectchamalead"

usage() {
  printf 'Uso: %s caminho/do/arquivo.csv\n' "$(basename "$0")"
}

trim() {
  local value="$1"
  value="${value#"${value%%[![:space:]]*}"}"
  value="${value%"${value##*[![:space:]]}"}"
  printf '%s' "$value"
}

sanitize_phone() {
  local value="$1"
  printf '%s' "$value" | tr -cd '0-9'
}

escape_json() {
  local value="$1"
  value=${value//\\/\\\\}
  value=${value//"/\\"}
  value=${value//$'\n'/\\n}
  value=${value//$'\r'/\\r}
  value=${value//$'\t'/\\t}
  printf '%s' "$value"
}

random_delay() {
  python3 - <<'PY'
import random
print(f"{random.uniform(6, 12):.6f}")
PY
}

if [[ $# -ne 1 ]]; then
  usage
  exit 1
fi

CSV_FILE="$1"

if [[ ! -f "$CSV_FILE" ]]; then
  printf 'Arquivo nao encontrado: %s\n' "$CSV_FILE" >&2
  exit 1
fi

if ! command -v python3 >/dev/null 2>&1; then
  printf 'python3 e obrigatorio para ler o CSV e gerar intervalos aleatorios.\n' >&2
  exit 1
fi

mapfile -t CSV_LINES < <(python3 - "$CSV_FILE" <<'PY'
import csv
import sys

path = sys.argv[1]

with open(path, newline='', encoding='utf-8-sig') as handle:
    rows = list(csv.reader(handle))

if not rows:
    sys.exit(1)

for row in rows:
    print("\t".join(cell.replace("\t", " ").strip() for cell in row))
PY
)

if [[ ${#CSV_LINES[@]} -eq 0 ]]; then
  printf 'CSV vazio ou invalido: %s\n' "$CSV_FILE" >&2
  exit 1
fi

IFS=$'\t' read -r -a HEADERS <<< "${CSV_LINES[0]}"

if [[ ${#HEADERS[@]} -eq 0 ]]; then
  printf 'Nao foi possivel identificar o cabecalho do CSV.\n' >&2
  exit 1
fi

printf 'Colunas encontradas:\n'
for i in "${!HEADERS[@]}"; do
  printf '  [%s] %s\n' "$((i + 1))" "${HEADERS[$i]:-(sem nome)}"
done

PHONE_INDEX=""
while [[ -z "$PHONE_INDEX" ]]; do
  read -r -p 'Qual coluna e o telefone? (numero): ' selected_column
  if [[ "$selected_column" =~ ^[0-9]+$ ]] && (( selected_column >= 1 && selected_column <= ${#HEADERS[@]} )); then
    PHONE_INDEX=$((selected_column - 1))
  else
    printf 'Escolha invalida. Digite um numero entre 1 e %s.\n' "${#HEADERS[@]}"
  fi
done

read -r -p 'Mensagem extra de contexto (opcional): ' EXTRA_CONTEXT
EXTRA_CONTEXT=$(trim "$EXTRA_CONTEXT")

TOTAL_SENT=0
TOTAL_SKIPPED=0

for ((line_index = 1; line_index < ${#CSV_LINES[@]}; line_index++)); do
  IFS=$'\t' read -r -a COLUMNS <<< "${CSV_LINES[$line_index]}"

  if [[ ${#COLUMNS[@]} -eq 0 ]]; then
    ((TOTAL_SKIPPED+=1))
    continue
  fi

  RAW_PHONE="${COLUMNS[$PHONE_INDEX]:-}"
  PHONE=$(sanitize_phone "$RAW_PHONE")

  if [[ -z "$PHONE" || ${#PHONE} -lt 10 ]]; then
    printf 'Linha %s ignorada: telefone invalido (%s).\n' "$((line_index + 1))" "$RAW_PHONE"
    ((TOTAL_SKIPPED+=1))
    continue
  fi

  CONTEXT_PARTS=()
  for column_index in "${!HEADERS[@]}"; do
    if (( column_index == PHONE_INDEX )); then
      continue
    fi

    header=$(trim "${HEADERS[$column_index]:-Campo $((column_index + 1))}")
    value=$(trim "${COLUMNS[$column_index]:-}")

    if [[ -n "$value" ]]; then
      CONTEXT_PARTS+=("${header}: ${value}")
    fi
  done

  if [[ -n "$EXTRA_CONTEXT" ]]; then
    CONTEXT_PARTS+=("Mensagem extra: ${EXTRA_CONTEXT}")
  fi

  CONTEXT=''
  if [[ ${#CONTEXT_PARTS[@]} -gt 0 ]]; then
    printf -v CONTEXT '%s | ' "${CONTEXT_PARTS[@]}"
    CONTEXT=${CONTEXT% | }
  fi

  JSON_PAYLOAD=$(printf '{"phone":"%s","context":"%s"}' \
    "$(escape_json "$PHONE")" \
    "$(escape_json "$CONTEXT")")

  printf 'Enviando linha %s para %s...\n' "$((line_index + 1))" "$PHONE"

  HTTP_STATUS=$(curl -sS -o /tmp/import-prospects-response.$$ \
    -w '%{http_code}' \
    -X POST "$WEBHOOK_URL" \
    -H 'Content-Type: application/json' \
    -H 'Accept: application/json' \
    --data "$JSON_PAYLOAD")

  if [[ "$HTTP_STATUS" -lt 200 || "$HTTP_STATUS" -ge 300 ]]; then
    printf 'Falha na linha %s. HTTP %s. Resposta salva em /tmp/import-prospects-response.%s\n' \
      "$((line_index + 1))" "$HTTP_STATUS" "$$" >&2
    rm -f /tmp/import-prospects-response.$$
    exit 1
  fi

  rm -f /tmp/import-prospects-response.$$
  ((TOTAL_SENT+=1))

  if (( line_index < ${#CSV_LINES[@]} - 1 )); then
    DELAY=$(random_delay)
    printf 'Aguardando %s segundos antes do proximo envio...\n' "$DELAY"
    sleep "$DELAY"
  fi
done

printf 'Concluido. Enviados: %s | Ignorados: %s\n' "$TOTAL_SENT" "$TOTAL_SKIPPED"
