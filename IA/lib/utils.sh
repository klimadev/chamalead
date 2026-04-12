#!/usr/bin/env bash
# ============================================
# Utilitários - Helpers Gerais
# ============================================

set -euo pipefail

trim() {
  local value="$1"
  value="${value#"${value%%[![:space:]]*}"}"
  value="${value%"${value##*[![:space:]]}"}"
  printf '%s' "$value"
}

sanitize_phone() {
  local value="$1"
  value=$(printf '%s' "$value" | tr -cd '0-9')

  if [[ ${#value} -gt 12 ]]; then
    value=${value: -12}
  fi

  if [[ ${#value} -eq 10 ]]; then
    value="55${value}"
  fi

  printf '%s' "$value"
}

is_valid_br_phone() {
  local phone="$1"
  if [[ ${#phone} -eq 12 && "$phone" =~ ^55[1-9][0-9]{9}$ ]]; then
    return 0
  fi
  if [[ ${#phone} -eq 11 && "$phone" =~ ^55[1-9][0-9]{8}$ ]]; then
    return 0
  fi
  if [[ ${#phone} -eq 11 && "$phone" =~ ^[1-9][0-9]{10}$ ]]; then
    return 0
  fi
  if [[ ${#phone} -eq 10 && "$phone" =~ ^[1-9][0-9]{9}$ ]]; then
    return 0
  fi
  return 1
}

escape_json() {
  local value="$1"
  value="${value//\\/\\\\}"
  value="${value//\"/\\}"
  value="${value//$'\n'/\\n}"
  value="${value//$'\r'/\\r}"
  value="${value//$'\t'/\\t}"
  printf '%s' "$value"
}

escape_sql() {
  local value="$1"
  value=${value//\'/\'\'}
  printf '%s' "$value"
}

random_delay() {
  local delay=$((RANDOM % (DELAY_MAX - DELAY_MIN + 1) + DELAY_MIN))
  local jitter
  jitter=$(awk 'BEGIN{srand(); print 0.8+rand()*0.4}')
  printf '%.0f' "$(awk -v d="$delay" -v j="$jitter" 'BEGIN{printf "%.2f", d*j}')"
}
