#!/usr/bin/env bash
# ============================================
# API - Webhook e Envio
# ============================================

set -euo pipefail

send_lead() {
  local phone="$1"
  local context="$2"
  local instance="$3"
  local filename="$4"

  local json_payload
  json_payload=$(printf '{"phone":"%s","context":"%s","instance":"%s"}' \
    "$(escape_json "$phone")" \
    "$(escape_json "$context")" \
    "$instance")

  printf '[%s] Enviando %s\n' "$instance" "$phone"

  local http_status
  http_status=$(curl -sS -o "/tmp/import-${instance}.$$" \
    -w '%{http_code}' \
    -X POST "$WEBHOOK_URL" \
    -H 'Content-Type: application/json' \
    -H 'Accept: application/json' \
    --data "$json_payload")

  if [[ "$http_status" -lt 200 || "$http_status" -ge 300 ]]; then
    printf '[%s] ERRO HTTP %s para %s\n' "$instance" "$http_status" "$phone"
    rm -f "/tmp/import-${instance}.$$"
    return 1
  fi

  rm -f "/tmp/import-${instance}.$$"
  save_successful_prospect "$phone" "$context" "$filename" "$instance"
  printf '[%s] OK %s\n' "$instance" "$phone"

  local delay
  delay=$(random_delay)
  printf '[%s] Delay %s segundos\n' "$instance" "$delay"
  sleep "$delay"

  return 0
}
