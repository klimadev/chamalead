#!/usr/bin/env bash
# ============================================
# CSV - Leitura e Parsing
# ============================================

set -euo pipefail

SCRIPT_DIR_CSV="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

read_csv() {
  local csv_file="$1"
  local -n headers_ref="$2"
  local -n lines_ref="$3"

  if ! command -v python3 >/dev/null 2>&1; then
    printf 'python3 é obrigatório.\n' >&2
    return 1
  fi

  mapfile -t CSV_LINES < <(python3 - "$csv_file" <<'PY'
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
    return 1
  fi

  IFS=$'\t' read -r -a headers_ref <<< "${CSV_LINES[0]}"
  lines_ref=("${CSV_LINES[@]:1}")

  return 0
}

detect_phone_column() {
  local headers=("$@")
  for i in "${!headers[@]}"; do
    local header
    header=$(trim "${headers[$i]}" | tr '[:upper:]' '[:lower:]')
    if [[ "$header" =~ (phone|telefone|fone|mobile|celular|whatsapp|contato) ]]; then
      printf '%s' "$i"
      return 0
    fi
  done
  return 1
}

find_csv_files() {
  local csv_dir="$1"
  local -n files_ref="$2"

  while IFS= read -r -d '' file; do
    files_ref+=("$file")
  done < <(find "$csv_dir" -maxdepth 1 -name '*.csv' -type f -print0 2>/dev/null)
}

move_to_processed() {
  local csv_file="$1"
  local processed_dir="$2"
  mkdir -p "$processed_dir"
  mv "$csv_file" "$processed_dir/"
}
