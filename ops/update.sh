#!/usr/bin/env sh

set -eu

project_dir="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
cd "$project_dir"

./ops/backup.sh
docker compose build --pull app
docker compose up -d
./ops/healthcheck.sh

echo "Update completed. Review application and database logs:"
echo "docker compose logs --tail=100 app db"

