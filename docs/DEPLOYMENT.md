# Wdrożenie sŁodziaka

Repozytorium zawiera kompletny wariant kontenerowy: aplikację Laravel/PHP-FPM, Nginx, MariaDB, Redis, proces kolejki i scheduler. Dane bazy oraz pliki redakcyjne są przechowywane w trwałych wolumenach Dockera.

> Ważne: kod bazowy nadal używa Laravel 10, którego [wsparcie bezpieczeństwa zakończyło się 4 lutego 2025 r.](https://laravel.com/framework/docs/10.x/releases#support-policy) Stosu wdrożeniowego można używać do integracji i testów, ale publiczne uruchomienie powinno nastąpić dopiero po aktualizacji frameworka i pakietów do wspieranej wersji.

## Wymagania serwera

- Linux x86_64 lub arm64 z Docker Engine i Docker Compose,
- minimum 2 GB RAM (zalecane 4 GB),
- domena skierowana na serwer,
- reverse proxy lub load balancer kończący HTTPS,
- dostęp do serwera SMTP.

## Pierwsze uruchomienie

1. Skopiuj konfigurację produkcyjną:

   ```bash
   cp .env.production.example .env.production
   ```

2. Uzupełnij domenę, hasła bazy, SMTP, konto administratora i dane kontaktowe. Wygeneruj klucz aplikacji:

   ```bash
   docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
   ```

   Wklej wynik jako `APP_KEY`. Żadna wartość `CHANGE_ME_*` nie może pozostać w pliku.

3. Zbuduj i uruchom całość:

   ```bash
   docker compose --env-file .env.production -f compose.production.yml up -d --build
   ```

   Kontener aplikacji wykona migracje i bezpieczny seed startowy: role, uprawnienia, konto redakcyjne, dane sŁodziaka, kontakt i stronę „O nas”. Nie ładuje fikcyjnych artykułów ani użytkowników.

4. Sprawdź stan:

   ```bash
   docker compose --env-file .env.production -f compose.production.yml ps
   curl --fail http://127.0.0.1:${HTTP_PORT:-8080}/up
   ```

5. Skieruj reverse proxy na port `HTTP_PORT` (domyślnie `8080`) i ustaw certyfikat TLS. Sam kontener aplikacji nie powinien być wystawiany bezpośrednio do Internetu.

## Aktualizacja

```bash
git pull --ff-only
docker compose --env-file .env.production -f compose.production.yml up -d --build --remove-orphans
```

Migracje są wykonywane automatycznie przez główny kontener aplikacji. Worker i scheduler startują dopiero po gotowości MariaDB i Redis.

## Kopia bazy danych

```bash
mkdir -p backups
docker compose --env-file .env.production -f compose.production.yml exec -T db \
  sh -c 'mariadb-dump --single-transaction --quick -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' \
  | gzip > "backups/slodziak-$(date +%F-%H%M%S).sql.gz"
```

Osobno należy archiwizować wolumen `slodziak_app_storage`, bo zawiera obrazy i filmy wgrane przez redakcję. Kopię bazy i storage trzeba wykonywać razem, szyfrować i okresowo testować odtwarzanie.

## Ważne operacje

```bash
# Logi
docker compose --env-file .env.production -f compose.production.yml logs -f --tail=200

# Tryb serwisowy
docker compose --env-file .env.production -f compose.production.yml exec app php artisan down --secret="tymczasowy-sekretny-adres"
docker compose --env-file .env.production -f compose.production.yml exec app php artisan up

# Ponowne uruchomienie workera po zmianach
docker compose --env-file .env.production -f compose.production.yml restart worker
```

## Architektura danych

- MariaDB: artykuły, konta, role, komentarze, ustawienia, newsletter i analityka.
- Redis: cache, sesje i kolejki.
- `app_storage`: obrazy, wideo, pliki publiczne i logi aplikacji.
- SMTP: wiadomości systemowe, reset hasła i newsletter.

Endpoint `/up` zwraca powodzenie dopiero wtedy, gdy Laravel ma działające połączenie z bazą danych.
