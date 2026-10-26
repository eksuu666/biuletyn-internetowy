# Wdrożenie sŁodziaka

Repozytorium zawiera kompletny wariant kontenerowy: aplikację Laravel 13/PHP-FPM, Nginx, MariaDB, Redis, proces kolejki i scheduler. Dane bazy oraz pliki redakcyjne są przechowywane w trwałych wolumenach Dockera.

## Wymagania serwera

- Linux x86_64 lub arm64 z Docker Engine i Docker Compose (PHP w obrazie: co najmniej 8.4.27),
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
   docker run --rm php:8.4-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
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

5. Skieruj reverse proxy na port `HTTP_PORT` (domyślnie `8080`) i ustaw certyfikat TLS. Port jest domyślnie dostępny na `127.0.0.1`; do zmiany interfejsu służy `HTTP_BIND_ADDRESS`. Proxy powinno przekazywać `Host` i `X-Forwarded-Proto`. Sam kontener aplikacji nie powinien być wystawiany bezpośrednio do Internetu.

6. Panel redakcji znajduje się pod `/<PANEL_PREFIX>` (domyślnie `/redakcja`).
   Logowanie używa `ADMIN_EMAIL` i `ADMIN_PASSWORD`. Utwórz kategorię, a następnie
   wiadomość ze zdjęciem. Materiał wymaga przekazania do sekretarza, oznaczenia
   korekty i akceptacji naczelnego. Zobacz [obieg redakcyjny](EDITORIAL_WORKFLOW.md).
   Przyszła data zatwierdzonego materiału oznacza embargo. Strona `/sitemap.xml`
   i kanał `/feed` zawierają wyłącznie publiczne materiały.

## Aktualizacja

```bash
git pull --ff-only
docker compose --env-file .env.production -f compose.production.yml up -d --build --remove-orphans
```

Migracje są wykonywane automatycznie przez główny kontener aplikacji. Worker i scheduler startują dopiero po gotowości MariaDB i Redis.

## Kopia bazy i plików

```bash
sh scripts/backup.sh .env.production
```

Skrypt włącza na czas kopii tryb serwisowy i zapisuje dump bazy, pliki `storage/app`
oraz sumy SHA-256. Przy błędzie przywraca wcześniejszy stan trybu serwisowego.
Wykonuj kopie poza okresem wysyłki kampanii i przetwarzania mediów: tryb serwisowy
nie zatrzymuje workera. Harmonogram kopii ustaw na serwerze, np. codziennie w nocy.
Przenoś zaszyfrowane kopie poza serwer. Osobno przechowuj bezpiecznie `APP_KEY`
i konfigurację środowiska — repozytorium ani archiwum ich nie zawiera.

Próba odtworzenia do **nowego** projektu (nigdy do działającej bazy):

```bash
sh scripts/restore-new-stack.sh backups/NAZWA_KOPII .env.production slodziak-restore-check
```

Skrypt sprawdza sumy, importuje bazę i pliki do osobnych wolumenów, uruchamia tylko
bazę i Redis. Odmawia użycia istniejącego projektu. Przed uruchomieniem web ustaw
osobny port, zgodny `APP_KEY` i sprawdź dane. Przećwicz procedurę przed wdrożeniem
i po istotnych zmianach; wolumeny usuwaj wyłącznie po sprawdzeniu nazwy projektu.

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

Endpoint `/up` sprawdza bazę oraz cache. Dodatkowa kontrola:

```bash
docker compose --env-file .env.production -f compose.production.yml exec app php artisan portal:check
docker compose --env-file .env.production -f compose.production.yml exec app php artisan queue:failed
```

`portal:check` sprawdza również zapis i odczyt plików oraz zaległości kolejki;
zwraca kod błędu przy nieudanych zadaniach lub ponad 100 oczekujących.
Podłącz `/up`, logi i kod wyjścia polecenia do monitoringu serwera. Scheduler
sprawdza rozmiar kolejki co 5 minut i zapisuje ostrzeżenia w logu.

## Poczta i retencja

SMTP musi mieć poprawne `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
`MAIL_FROM_ADDRESS` oraz ustawienie szyfrowania zgodne z dostawcą.
Bez działającego workera nie ruszą potwierdzenia newslettera i kampanie.
Przed startem sprawdź prawdziwą skrzynkę, reset hasła, SPF/DKIM/DMARC oraz
adresy linków wygenerowane na podstawie `APP_URL` z HTTPS.

`ANALYTICS_RETENTION_DAYS` (domyślnie 90) określa retencję historii żądań i IP.
Scheduler usuwa stare dane codziennie. Próbę bez kasowania wykonasz poleceniem
`php artisan portal:prune-analytics --pretend`. Konta, artykuły i historia
redakcyjna nie podlegają temu poleceniu. Właściciel musi uzupełnić dokumenty
prywatności, dane wydawcy i zasady retencji odpowiednie do swojego wdrożenia.
