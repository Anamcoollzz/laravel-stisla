#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
STUBS="$ROOT/stisla-package/stubs"

echo "Syncing project files to \"$STUBS\"..."

SYNC_DIRS=(
    "app/Http/Controllers"
    "app/Models"
    "app/Console/Commands"
    "app/Exports"
    "app/Imports"
    "app/Http/Middleware"
    "app/Http/Requests"
    "app/Mail"
    "app/Providers"
    "app/Services"
    "resources/views"
    "database/migrations"
    "database/seeders"
    "database/factories"
    "lang"
)

for dir in "${SYNC_DIRS[@]}"; do
    if [[ -d "$ROOT/$dir" ]]; then
        mkdir -p "$STUBS/$dir"
        if ! rsync -a --delete "$ROOT/$dir/" "$STUBS/$dir/"; then
            echo "[ERROR] Failed syncing $dir" >&2
            exit 1
        fi
    fi
done

if [[ -f "$ROOT/database/indonesia.sql" ]]; then
    mkdir -p "$STUBS/database"
    cp -f "$ROOT/database/indonesia.sql" "$STUBS/database/indonesia.sql"
fi

mkdir -p "$STUBS/config"
CONFIG_FILES=(
    "captcha.php"
    "company.php"
    "excel.php"
    "permission.php"
    "settings.php"
    "stisla.php"
)
for file in "${CONFIG_FILES[@]}"; do
    if [[ -f "$ROOT/config/$file" ]]; then
        cp -f "$ROOT/config/$file" "$STUBS/config/$file"
    fi
done

mkdir -p "$STUBS/routes"
ROUTE_FILES=(
    "stisla-web.php"
    "stisla-pages-web.php"
    "spark-admin-web.php"
    "ecommerce-pages-web.php"
    "news-web.php"
    "spp-payment-web.php"
    "lp-compro-web.php"
)
for file in "${ROUTE_FILES[@]}"; do
    if [[ -f "$ROOT/routes/$file" ]]; then
        cp -f "$ROOT/routes/$file" "$STUBS/routes/$file"
    fi
done

PUBLIC_DIRS=(
    "assets"
    "lp-assets"
    "lp-build"
    "mantu"
    "modules"
    "news-assets"
    "node_modules"
    "templates"
)
for pub in "${PUBLIC_DIRS[@]}"; do
    if [[ -d "$ROOT/public/$pub" ]]; then
        mkdir -p "$STUBS/public/$pub"
        if ! rsync -a --delete "$ROOT/public/$pub/" "$STUBS/public/$pub/"; then
            echo "[ERROR] Failed syncing public/$pub" >&2
            exit 1
        fi
    fi
done

echo "Done! All stubs synced successfully."
