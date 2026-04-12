#!/usr/bin/env bash
# ============================================
# Banco de Dados - SQLite Operations
# ============================================

set -euo pipefail

init_db() {
  if [[ ! -f "$DB_FILE" ]]; then
    sqlite3 "$DB_FILE" <<'SQL'
CREATE TABLE IF NOT EXISTS prospects (
  phone TEXT PRIMARY KEY,
  context TEXT,
  status TEXT DEFAULT 'success',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  source_file TEXT,
  instance TEXT DEFAULT ''
);
CREATE INDEX IF NOT EXISTS idx_phone ON prospects(phone);
CREATE INDEX IF NOT EXISTS idx_created_at ON prospects(created_at);
CREATE INDEX IF NOT EXISTS idx_instance ON prospects(instance);
SQL
    printf 'Banco de dados criado: %s\n' "$DB_FILE"
  else
    # Migration: adicionar coluna instance se não existir
    local has_column
    has_column=$(sqlite3 "$DB_FILE" "PRAGMA table_info(prospects);" | grep -c "instance" || true)
    if [[ "$has_column" -eq 0 ]]; then
      sqlite3 "$DB_FILE" "ALTER TABLE prospects ADD COLUMN instance TEXT DEFAULT '';"
      sqlite3 "$DB_FILE" "CREATE INDEX IF NOT EXISTS idx_instance ON prospects(instance);"
      printf 'Migration: coluna instance adicionada.\n'
    fi
  fi
}

phone_exists_in_db() {
  local phone="$1"
  local safe_phone
  safe_phone=$(printf '%s' "$phone" | tr -cd '0-9')
  local count
  count=$(sqlite3 "$DB_FILE" "SELECT COUNT(*) FROM prospects WHERE phone = '${safe_phone}';")
  [[ "$count" -gt 0 ]]
}

get_today_count() {
  sqlite3 "$DB_FILE" "SELECT COUNT(*) FROM prospects WHERE date(created_at) = date('now', 'localtime');"
}

get_today_count_by_instance() {
  local instance="$1"
  sqlite3 "$DB_FILE" "SELECT COUNT(*) FROM prospects WHERE instance = '${instance}' AND date(created_at) = date('now', 'localtime');"
}

save_successful_prospect() {
  local phone="$1"
  local context="$2"
  local source_file="$3"
  local instance="$4"

  local safe_phone
  safe_phone=$(printf '%s' "$phone" | tr -cd '0-9')

  sqlite3 "$DB_FILE" "INSERT OR IGNORE INTO prospects (phone, context, status, source_file, instance) VALUES ('${safe_phone}', '${context}', 'success', '${source_file}', '${instance}');"
}
