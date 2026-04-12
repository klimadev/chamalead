#!/bin/bash

if [ -z "$1" ]; then
    echo "Uso: $0 <número> [número2] [número3] ..."
    echo "Exemplo: $0 555199309404 555199999999"
    exit 1
fi

DB_FILE="prospects.db"

for phone in "$@"; do
    echo "Removendo $phone..."
    sqlite3 "$DB_FILE" "DELETE FROM prospects WHERE phone = '$phone';"
    if [ $? -eq 0 ]; then
        echo "✓ $phone removido com sucesso"
    else
        echo "✗ Erro ao remover $phone"
    fi
done
