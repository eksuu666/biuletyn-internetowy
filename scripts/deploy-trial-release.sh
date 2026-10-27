#!/bin/sh
# Install the tested images and configuration uploaded to a fresh trial VM.
set -eu
test "$(id -u)" -eq 0
source_dir=$(realpath "${1:?Upload directory required}")
test -f "$source_dir/portal-config.tar.gz"
test -f "$source_dir/portal-images.tar.gz"
test -f "$source_dir/.env.production"
install -d -m 0700 /opt/slodziak
cd /opt/slodziak
test ! -f .env.production || { echo 'Deployment already configured; refusing to replace secrets'; exit 1; }
tar -xzf "$source_dir/portal-config.tar.gz" --no-same-owner
install -m 0600 "$source_dir/.env.production" .env.production
gzip -dc "$source_dir/portal-images.tar.gz" | docker load
docker compose --env-file .env.production -f compose.production.yml -f docker/cloud/compose.micro.yml up -d --no-build --wait --wait-timeout 600
docker compose --env-file .env.production -f compose.production.yml exec -T app php artisan portal:check
rm -- "$source_dir/.env.production" "$source_dir/portal-config.tar.gz" "$source_dir/portal-images.tar.gz"
echo 'sLodziak deployment ready'
