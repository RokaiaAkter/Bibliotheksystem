#!/usr/bin/env sh

set -eu

project_dir="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
cd "$project_dir"

if [ ! -f .env ]; then
    echo "Missing .env. Copy .env.example to .env first." >&2
    exit 1
fi

set -a
. ./.env
set +a

mkdir -p storage/backups
timestamp="$(date '+%Y%m%d_%H%M%S')"
backup_file="storage/backups/bibliothksystem_${timestamp}.sql"

docker compose exec -T \
    -e MYSQL_PWD="${DB_PASSWORD}" \
    db mysqldump \
    --single-transaction \
    --routines \
    --triggers \
    --default-character-set=utf8mb4 \
    -u "${DB_USERNAME}" \
    "${DB_DATABASE}" > "$backup_file"

test -s "$backup_file"
chmod 600 "$backup_file"
echo "Backup created: ${backup_file}"

