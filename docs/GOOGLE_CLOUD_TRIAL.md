# Google Cloud — oszczędne wdrożenie próbne

Zasoby utworzono 26.10.2026, wdrożenie sprawdzono 27.10.2026.
Uruchomienie zaakceptowane przez właściciela w ramach istniejącego
Free Trial. Nie aktywowano płatnej wersji konta.

- Projekt: `slodziak-biuletyn-20261026`.
- VM: `slodziak-portal`, strefa `us-central1-a`, `e2-micro` (1 GB RAM).
- Dysk: 30 GB `pd-standard`; dodatkowo 2 GB swap na tym dysku.
- Aplikacja, MariaDB i Redis działają na jednej maszynie, w Dockerze.
- Publiczny adres: `https://slodziak.35.209.115.44.sslip.io`.
- Panel: `/redakcja`. Konto administratora i klucze nie są zapisane w Git.
- SMTP pozostaje niepodłączone zgodnie z wyborem właściciela.
  Potwierdzenia zapisu, reset hasła i kampanie nie dostarczają obecnie e-maili.

## Koszty i ograniczenia

Stan przy uruchomieniu: około 1069 zł kredytu i 64 dni wersji próbnej.
Kredyt i limity Free Tier są współdzielone z innymi projektami konta.
Maszyna i dysk kwalifikują się do limitów Free Tier; publiczny IPv4 kosztuje
0,005 USD za godzinę (około 3,65 USD miesięcznie). Transfer ponad darmowe limity
oraz wykorzystanie limitów przez inne projekty może pomniejszać kredyt.

Ustawiono budżet informacyjny 20 PLN/miesiąc, tylko dla tego projektu, z progami
50%, 90% i 100%, bez uwzględniania kredytów. **Budżet nie blokuje wydatków.**
Brak przejścia na płatne konto oznacza zatrzymanie usług po zakończeniu triala
lub wykorzystaniu kredytu. Google może potem usunąć zasoby. Przed tym terminem
trzeba wyeksportować bazę, pliki i konfigurację poza Google Cloud.

To wariant dla małego ruchu. Swap ułatwia rozruch, ale nie zastępuje RAM przy
większym obciążeniu. Nie uruchamiaj kompilacji obrazów na tej maszynie.
Przełączenie konta na płatne wymaga osobnej decyzji właściciela.

Źródła: [Free Trial i Free Tier](https://docs.cloud.google.com/free/docs/free-cloud-features),
[ceny adresów IP](https://cloud.google.com/vpc/pricing).

## Konfiguracja i obsługa

Na serwerze aplikacja znajduje się w `/opt/slodziak`; plik `.env.production`
ma uprawnienia tylko dla roota. Dane bazy i mediów pozostają w wolumenach.
Maszyna nie ma konta serwisowego z dostępem do usług Google. Firewall dopuszcza
publicznie tylko TCP 80 i 443. SSH jest dostępne wyłącznie przez IAP.

```bash
gcloud compute ssh slodziak-portal --project=slodziak-biuletyn-20261026 \
  --zone=us-central1-a --tunnel-through-iap
sudo -i
cd /opt/slodziak

# Stan usług i diagnostyka
docker compose --env-file .env.production -f compose.production.yml \
  -f docker/cloud/compose.micro.yml ps
docker compose --env-file .env.production -f compose.production.yml \
  -f docker/cloud/compose.micro.yml exec app php artisan portal:check

# Kopia bazy i mediów (osobno zachowaj APP_KEY i konfigurację)
sh scripts/backup.sh .env.production
```

Timer systemd `slodziak-backup.timer` wykonuje lokalną kopię codziennie około
03:20 czasu Europe/Warsaw. Zachowuje siedem ostatnich ukończonych kopii.
Kopia na tym samym dysku nie chroni przed usunięciem projektu — eksportuj ją
regularnie poza Google Cloud. Stan: `systemctl list-timers slodziak-backup.timer`;
log: `journalctl -u slodziak-backup.service`.

## Weryfikacja wdrożenia

27.10.2026 przez publiczny adres sprawdzono poprawny certyfikat TLS,
przekierowanie HTTP → HTTPS, `/up`, stronę główną, kontakt, logowanie, feed
i sitemapę. Konto administratora zalogowano przez sesję z CSRF. Utworzono
kategorię „Aktualności”, sprawdzono zapis szkicu ze zdjęciem, przekazanie do
korekty i akceptacji oraz historię. Szkic nie był publiczny; materiały techniczne
usunięto miękko po próbie. Nie importowano fikcyjnych wiadomości ani kont testowych.

`portal:check` potwierdził bazę, cache, zapis plików oraz brak nieudanych zadań.
Pierwszą kopię wykonano usługą systemd i pobrano poza Google Cloud.
Sprawdzono konfigurację obu plików Compose i składnię skryptów powłoki.

Overlay `docker/cloud/compose.micro.yml` ustawia limity pamięci, mniejszy bufor
MariaDB, Redis bez usuwania kluczy kolejki, dwa procesy PHP oraz Caddy z HTTPS.
Zmiana parametrów wymaga oceny obciążenia. Do aktualizacji potrzebne są oba pliki
Compose i nowy tag **wcześniej zbudowanych** obrazów app/web.

Tymczasowy DNS zależy od sslip.io i aktualnego adresu IPv4. Po zatrzymaniu i
ponownym uruchomieniu VM adres efemeryczny może się zmienić. Wówczas zmień
`APP_URL` i `PORTAL_DOMAIN`, odtwórz kontenery i sprawdź certyfikat.
Docelowo podłącz własną domenę. Po podłączeniu SMTP zmień `MAIL_MAILER` z `log`
na `smtp`, uzupełnij dane dostawcy i sprawdź odbiór wiadomości.

Przy tworzeniu VM wyłączono automatyczne usuwanie dysku razem z maszyną, aby
przypadkowe usunięcie VM nie kasowało bazy. Pozostawiony dysk nadal jest zasobem
rozliczanym — uwzględnij go przy kończeniu wdrożenia.
