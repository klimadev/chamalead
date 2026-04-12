#!/usr/bin/env bash
# ============================================
# Rate Limiting - Controle de Limites
# ============================================

set -euo pipefail

check_daily_limit() {
  local today_count
  today_count=$(get_today_count)
  if [[ $today_count -ge $DAILY_LIMIT ]]; then
    return 1
  fi
  return 0
}

calc_run_limit() {
  local today_count
  today_count=$(get_today_count)
  local remaining=$((DAILY_LIMIT - today_count))
  if [[ $remaining -gt $MAX_PER_RUN ]]; then
    remaining=$MAX_PER_RUN
  fi
  if [[ $remaining -lt 0 ]]; then
    remaining=0
  fi
  printf '%s' "$remaining"
}

is_forbidden_hour() {
  local hour
  hour=$(date +%-H)
  [[ $hour -ge $FORBIDDEN_START && $hour -lt $FORBIDDEN_END ]]
}

select_instance() {
  local available_instances=()

  for instance in "${INSTANCE_LIST[@]}"; do
    local count
    count=$(get_today_count_by_instance "$instance")

    if [[ $count -lt $DAILY_LIMIT_PER_INSTANCE ]]; then
      available_instances+=("$instance")
    fi
  done

  if [[ ${#available_instances[@]} -eq 0 ]]; then
    printf '%s' ""
    return 1
  fi

  local attempts=0
  while [[ $attempts -lt ${#available_instances[@]} ]]; do
    local idx=$((INSTANCE_INDEX % ${#available_instances[@]}))
    ((INSTANCE_INDEX++)) || true
    local instance="${available_instances[$idx]}"

    local count
    count=$(get_today_count_by_instance "$instance")
    if [[ $count -lt $DAILY_LIMIT_PER_INSTANCE ]]; then
      printf '%s' "$instance"
      return 0
    fi

    ((attempts++)) || true
  done

  printf '%s' ""
  return 1
}
