#!/usr/bin/env sh

set -eu

if [ "${CONFIRM_RESTORE:-}" != "YES" ]; then
    echo "Restore changes database data." >&2
    echo "Run with CONFIRM_RESTORE=YES and an explicit backup file." >&2
    exit 1
fi

if [ "$#" -ne 1 ] || [ ! -f "$1" ]; then
    echo "Usage: CONFIRM_RESTORE=YES ./ops/restore.sh path/to/backup.sql" >&2
    exit 1
fi

project_dir="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
backup_file="$(CDPATH= cd -- "$(dirname -- "$1")" && pwd)/$(basename -- "$1")"
cd "$project_dir"

if [ ! -f .env ]; then
    echo "Missing .env." >&2
    exit 1
fi

set -a
. ./.env
set +a

docker compose exec -T \
    -e MYSQL_PWD="${DB_PASSWORD}" \
    db mysql \
    --default-character-set=utf8mb4 \
    -u "${DB_USERNAME}" \
    "${DB_DATABASE}" < "$backup_file"

echo "Restore completed from: ${backup_file}"
echo "Run ./ops/healthcheck.sh and the manual test checklist now."

