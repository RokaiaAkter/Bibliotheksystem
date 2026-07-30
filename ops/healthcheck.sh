#!/usr/bin/env sh

set -eu

app_url="${APP_URL:-http://localhost:8080}"
health_url="${app_url%/}/index.php?route=health"

echo "Checking ${health_url}"
curl --fail --silent --show-error "${health_url}"
echo
echo "Bibliothksystem health check passed."

