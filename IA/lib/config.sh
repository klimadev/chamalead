#!/usr/bin/env bash
# ============================================
# Configuração - Constantes e Carregamento
# ============================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DB_FILE="${SCRIPT_DIR}/prospects.db"
CSV_DIR="${SCRIPT_DIR}/csv"
PROCESSED_DIR="${SCRIPT_DIR}/csv/processed"
CONFIG_FILE="${SCRIPT_DIR}/config.env"

load_config() {
  if [[ -f "$CONFIG_FILE" ]]; then
    set -a
    # shellcheck source=/dev/null
    source "$CONFIG_FILE"
    set +a
  fi

  # Defaults caso config.env não exista
  MODE="${MODE:-prod}"
  BASE_URL="${BASE_URL:-http://localhost:5678}"
  WEBHOOK_PATH="${WEBHOOK_PATH:-}"
  INSTANCES="${INSTANCES:-CHAMALEAD_PROSPECT}"
  MAX_PER_RUN="${MAX_PER_RUN:-30}"
  DAILY_LIMIT="${DAILY_LIMIT:-60}"
  DAILY_LIMIT_PER_INSTANCE="${DAILY_LIMIT_PER_INSTANCE:-30}"
  DELAY_MIN="${DELAY_MIN:-120}"
  DELAY_MAX="${DELAY_MAX:-240}"
  BATCH_SIZE="${BATCH_SIZE:-10}"
  BATCH_PAUSE="${BATCH_PAUSE:-300}"
  FORBIDDEN_START="${FORBIDDEN_START:-21}"
  FORBIDDEN_END="${FORBIDDEN_END:-23}"
  ERROR_BACKOFF="${ERROR_BACKOFF:-600}"
  IDLE_PAUSE="${IDLE_PAUSE:-60}"

  WEBHOOK_URL="${BASE_URL}${WEBHOOK_PATH}"

  # Converte INSTANCES de CSV para array
  IFS=',' read -ra INSTANCE_LIST <<< "$INSTANCES"
  INSTANCE_INDEX=0
}

init_dirs() {
  mkdir -p "$PROCESSED_DIR"
}
