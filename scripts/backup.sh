#!/bin/sh
set -eu
umask 077
cd "$(dirname "$0")/.."
env_file=${1:-.env.production}
test -f "$env_file" || { echo "Missing environment file" >&2; exit 1; }
APP_ENV_FILE=$(cd "$(dirname "$env_file")" && pwd)/$(basename "$env_file")
export APP_ENV_FILE
target=${2:-"backups/$(date -u +%Y%m%d-%H%M%S)"}
test ! -e "$target" || { echo "Backup destination already exists" >&2; exit 1; }
mkdir -p "$target"
maintenance_started=false
cleanup() {
    if [ "$maintenance_started" = true ]; then docker compose --env-file "$env_file" -f compose.production.yml exec -T app php artisan up; fi
}
trap cleanup EXIT
if docker compose --env-file "$env_file" -f compose.production.yml exec -T app test ! -f /var/www/html/storage/framework/down; then
    docker compose --env-file "$env_file" -f compose.production.yml exec -T app php artisan down --retry=60
    maintenance_started=true
fi
# First write an uncompressed dump: a failing database command must not look like a successful gzip.
docker compose --env-file "$env_file" -f compose.production.yml exec -T db sh -c 'exec mariadb-dump --single-transaction --quick -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' > "$target/database.sql"
gzip "$target/database.sql"
docker compose --env-file "$env_file" -f compose.production.yml exec -T app tar -czf - -C /var/www/html/storage/app . > "$target/storage.tar.gz"
(cd "$target" && sha256sum database.sql.gz storage.tar.gz > SHA256SUMS)
echo "Backup complete: $target. Keep APP_KEY and production configuration separately in secure storage."
