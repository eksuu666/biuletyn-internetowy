#!/bin/sh
set -eu
umask 077
cd "$(dirname "$0")/.."
test "$#" -eq 3 || { echo "Usage: sh scripts/restore-new-stack.sh BACKUP_DIR ENV_FILE NEW_PROJECT" >&2; exit 1; }
backup_dir=$(cd "$1" && pwd)
env_file=$2
project=$3
case "$project" in ''|*[!a-z0-9-]*|slodziak|slodziak-verification) echo "Choose a new isolated project name" >&2; exit 1;; esac
test -f "$env_file" || exit 1
APP_ENV_FILE=$(cd "$(dirname "$env_file")" && pwd)/$(basename "$env_file")
export APP_ENV_FILE
test -z "$(docker ps -aq --filter "label=com.docker.compose.project=$project")" || { echo "Target project already exists; refusing to overwrite it" >&2; exit 1; }
(cd "$backup_dir" && sha256sum -c SHA256SUMS)
# Reject archive entries escaping storage/app.
tar -tzf "$backup_dir/storage.tar.gz" | while IFS= read -r entry; do
    case "$entry" in /*|../*|*/../*) echo "Unsafe archive path" >&2; exit 1;; esac
done
docker compose -p "$project" --env-file "$env_file" -f compose.production.yml up -d --wait db redis
restore_input=$(mktemp "$backup_dir/restore-input.XXXXXX")
trap 'rm -f "$restore_input"' EXIT
gzip -dc "$backup_dir/database.sql.gz" > "$restore_input"
docker compose -p "$project" --env-file "$env_file" -f compose.production.yml exec -T db sh -c 'exec mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' < "$restore_input"
docker compose -p "$project" --env-file "$env_file" -f compose.production.yml run --rm -T --no-deps -e RUN_MIGRATIONS=false -e RUN_BOOTSTRAP_SEED=false app sh -c 'tar -xzf - --no-same-owner -C /var/www/html/storage/app && chown -R www-data:www-data /var/www/html/storage/app' < "$backup_dir/storage.tar.gz"
echo "Restored into isolated project $project. Before starting web, set a separate HTTP_PORT and verify APP_KEY."
