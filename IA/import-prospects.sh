#!/usr/bin/env bash
# ============================================
# ChamaLead - Importador Infinito de Prospects
# Monitora pasta csv/ e processa automaticamente
# ============================================

set -euo pipefail

# Carrega bibliotecas
LIB_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/lib" && pwd)"
source "${LIB_DIR}/config.sh"
source "${LIB_DIR}/utils.sh"
source "${LIB_DIR}/db.sh"
source "${LIB_DIR}/csv.sh"
source "${LIB_DIR}/api.sh"
source "${LIB_DIR}/rate-limit.sh"

load_config

# ============================================
# PROCESSAMENTO DE CSV
# ============================================

process_csv_file() {
  local csv_file="$1"
  local filename
  filename=$(basename "$csv_file")

  printf '\n========================================\n'
  printf 'PROCESSANDO: %s\n' "$filename"
  printf '========================================\n'

  local -a HEADERS=()
  local -a CSV_LINES=()

  if ! read_csv "$csv_file" HEADERS CSV_LINES; then
    printf '[ERRO] Falha ao ler CSV: %s\n' "$filename"
    return 1
  fi

  local phone_index
  phone_index=$(detect_phone_column "${HEADERS[@]}") || phone_index=0

  local phone_col_name="${HEADERS[$phone_index]:-Telefone}"
  printf 'Coluna de telefone: %s (índice %s)\n' "$phone_col_name" "$((phone_index + 1))"
  printf 'Total de linhas: %s\n' "${#CSV_LINES[@]}"

  local run_limit
  run_limit=$(calc_run_limit)
  local today_count
  today_count=$(get_today_count)

  printf '\n--- Limite esta execução: %s ---\n' "$run_limit"
  printf -- '--- Já enviados hoje: %s / %s ---\n' "$today_count" "$DAILY_LIMIT"

  if [[ $run_limit -eq 0 ]]; then
    printf '[AGUARDANDO] Limite diário atingido.\n'
    return 1
  fi

  local total_sent=0
  local total_skipped=0
  local total_already_exists=0
  local instance_idx=0

  local -a PIDS=()

  for line in "${CSV_LINES[@]}"; do
    if [[ $total_sent -ge $run_limit ]]; then
      break
    fi

    if ! check_daily_limit; then
      break
    fi

    IFS=$'\t' read -r -a COLUMNS <<< "$line"

    if [[ ${#COLUMNS[@]} -eq 0 ]]; then
      ((total_skipped++)) || true
      continue
    fi

    local raw_phone="${COLUMNS[$phone_index]:-}"
    local phone
    phone=$(sanitize_phone "$raw_phone")

    if [[ -z "$phone" || ${#phone} -lt 10 ]]; then
      ((total_skipped++)) || true
      continue
    fi

    if ! is_valid_br_phone "$phone"; then
      ((total_skipped++)) || true
      continue
    fi

    if phone_exists_in_db "$phone"; then
      ((total_already_exists++)) || true
      continue
    fi

    local context_parts=()
    for i in "${!HEADERS[@]}"; do
      if (( i == phone_index )); then
        continue
      fi

      local header="${HEADERS[$i]:-Campo $((i + 1))}"
      local value="${COLUMNS[$i]:-}"
      value=$(trim "$value")

      if [[ -n "$value" ]]; then
        context_parts+=("${header}: ${value}")
      fi
    done

    local context=""
    if [[ ${#context_parts[@]} -gt 0 ]]; then
      printf -v context '%s | ' "${context_parts[@]}"
      context=${context% | }
    fi

    local instance="${INSTANCE_LIST[$instance_idx]}"
    ((instance_idx = (instance_idx + 1) % ${#INSTANCE_LIST[@]})) || true

    send_lead "$phone" "$context" "$instance" "$filename" &
    PIDS+=($!)

    ((total_sent++)) || true

    if [[ ${#PIDS[@]} -ge ${#INSTANCE_LIST[@]} ]]; then
      for pid in "${PIDS[@]}"; do
        wait "$pid" 2>/dev/null || true
      done
      PIDS=()
    fi

    sleep 1
  done

  # Aguarda processos restantes
  for pid in "${PIDS[@]}"; do
    wait "$pid" 2>/dev/null || true
  done

  printf '\n========================================\n'
  printf 'CONCLUÍDO: %s\n' "$filename"
  printf '========================================\n'
  printf 'Enviados: %s\n' "$total_sent"
  printf 'Ignorados (inválidos): %s\n' "$total_skipped"
  printf 'Ignorados (já existentes): %s\n' "$total_already_exists"
  printf 'Enviados hoje: %s / %s\n' "$(get_today_count)" "$DAILY_LIMIT"

  return 0
}

# ============================================
# LOOP PRINCIPAL (INFINITO)
# ============================================

# Exporta funções para subshells paralelas
export -f send_lead
export -f escape_json
export -f save_successful_prospect
export -f random_delay
export DB_FILE WEBHOOK_URL

main() {
  init_db
  init_dirs

  printf '========================================\n'
  printf 'ChamaLead - Importador Infinito\n'
  printf '========================================\n'
  printf 'Pasta monitorada: %s\n' "$CSV_DIR"
  printf 'Limite diário: %s\n' "$DAILY_LIMIT"
  printf 'Limite por ciclo: %s\n' "$MAX_PER_RUN"
  printf 'Horário proibido: %02d:00 - %02d:00\n' "$FORBIDDEN_START" "$FORBIDDEN_END"
  printf 'Instâncias: %s\n' "$INSTANCES"
  printf '========================================\n\n'

  local cycle=0

  while true; do
    ((cycle++)) || true
    local timestamp
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')

    printf '\n=== CICLO %s | %s ===\n' "$cycle" "$timestamp"

    # Verifica horário proibido
    if is_forbidden_hour; then
      printf '[BLOQUEADO] Horário proibido (%02d:00-%02d:00).\n' "$FORBIDDEN_START" "$FORBIDDEN_END"
      printf 'Aguardando até %02d:00...\n' "$FORBIDDEN_END"
      sleep 600
      continue
    fi

    # Verifica limite diário
    if ! check_daily_limit; then
      local hour
      hour=$(date +%-H)

      if [[ $hour -ge 23 || $hour -lt 6 ]]; then
        printf '[NOVA JANELA] Resetando contagem diária...\n'
      else
        printf '[LIMITE] Limite diário atingido (%s/%s).\n' "$(get_today_count)" "$DAILY_LIMIT"
        printf 'Aguardando...\n'
        sleep 600
        continue
      fi
    fi

    # Lista CSVs na pasta
    local -a csv_files=()
    find_csv_files "$CSV_DIR" csv_files

    if [[ ${#csv_files[@]} -eq 0 ]]; then
      printf '[OCIOSO] Nenhum CSV encontrado. Aguardando %s segundos...\n' "$IDLE_PAUSE"
      sleep "$IDLE_PAUSE"
      continue
    fi

    printf '[ENCONTRADOS] %s arquivo(s) para processar.\n' "${#csv_files[@]}"

    local processed_count=0

    for csv_file in "${csv_files[@]}"; do
      if ! check_daily_limit; then
        printf '[LIMITE] Limite diário atingido. Parando.\n'
        break
      fi

      if is_forbidden_hour; then
        printf '[BLOQUEADO] Horário proibido. Parando ciclo.\n'
        break
      fi

      process_csv_file "$csv_file"
      local result=$?

      if [[ $result -eq 0 ]]; then
        ((processed_count++)) || true
      elif [[ $result -eq 1 ]]; then
        break
      fi
    done

    if [[ $processed_count -gt 0 ]]; then
      printf '\n[RESUMO] %s arquivo(s) processado(s) neste ciclo.\n' "$processed_count"
    fi

    printf '\n[Aguardando próximo ciclo...]\n'
    sleep "$IDLE_PAUSE"
  done
}

main "$@"
