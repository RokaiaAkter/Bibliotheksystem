#!/usr/bin/env sh

set -eu

app_url="${APP_URL:-http://localhost:8080}"

curl --fail --silent --show-error \
    "${app_url%/}/index.php?route=health" >/dev/null

login_page="$(curl --fail --silent --show-error \
    "${app_url%/}/index.php?route=login")"

echo "$login_page" | grep -q "Bibliothksystem"
echo "$login_page" | grep -q 'name="_csrf"'

echo "HTTP smoke test passed."

