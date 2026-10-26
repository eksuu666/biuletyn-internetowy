# Stan funkcjonalny sŁodziaka — 26 października 2026

## Działający rdzeń

- Laravel 13, PHP 8.4, Nginx, MariaDB 11.4, Redis 7.4, worker i scheduler.
- Konto redakcji i role z produkcyjnego seeda bez fikcyjnych wiadomości.
- Dodawanie i edycja wiadomości/artykułów z obrazem, kategorią i tagami.
- Szkic: wyłączona publikacja. Zaplanowany: publikacja włączona i przyszła data.
  Opublikowany: publikacja włączona i data nie późniejsza niż teraz.
- Widoczność jest sprawdzana również dla bezpośrednich adresów, wyszukiwania,
  kategorii, tagów, autorów, rekomendacji, menu, RSS i mapy witryny.
- Wycofanie wiadomości działa bez czekania na wielogodzinny cache list.
- Edycja nie zmienia autora. Slug normalizuje się przed kontrolą unikalności.
- Uprawnienia chronią również otwieranie formularzy redakcyjnych.
- Komentarze trafiają do moderacji; edycja zatwierdzonego komentarza wymaga
  ponownej moderacji. Komentować można tylko publiczne materiały.
- `/feed`: kanał Atom w języku polskim, bez prywatnego e-maila autora.
- `/sitemap.xml`: dynamiczny indeks mapy; wiadomości dzielone po 1000 adresów.
- Formularze wiadomości używają daty gregoriańskiej i strefy Europe/Warsaw.
- Worker i scheduler czekają na zdrową aplikację. Kod PHP jest wykonywany
  wyłącznie przez front controller, a uploady pozostają w trwałym storage.

## Weryfikacja

W osobnym projekcie Docker `slodziak-verification` uruchomiono pełny stos.
Przez HTTP sprawdzono gotowość, stronę główną, kontakt, feed i sitemapę oraz
logowanie administratora z CSRF i sesją Redis, dodanie kategorii, upload obrazu,
publikację do MariaDB, wyszukanie i otwarcie wiadomości oraz odczyt jej zdjęcia.
Przetworzenie testowego zadania przez worker Redis potwierdzono odczytem cache.

Testy regresji obejmują publikację, embargo, wycofanie materiału, dostęp do
formularzy, zachowanie autora, uploady, moderację i zachowanie danych po restarcie.
Środowisko weryfikacyjne jest oddzielone od istniejącej bazy projektu.
Pełny przebieg na MariaDB: **30 testów, 139 asercji, wszystkie poprawne**.
Wcześniejszy przebieg na SQLite: 30 testów, 127 asercji; później rozszerzono
kontrolę formularzy i mapy witryny.

## Uruchomienie na serwerze

Zobacz [DEPLOYMENT.md](DEPLOYMENT.md). Potrzebne są własna domena, hasła,
klucz aplikacji, HTTPS i dane SMTP. Dane testowe nie są konfiguracją produkcyjną.
Port HTTP jest domyślnie związany z `127.0.0.1`, na potrzeby reverse proxy.
Wymuszenie innego interfejsu wymaga ustawienia `HTTP_BIND_ADDRESS`.

## Dalsze etapy do poziomu dużego portalu

- pełna polonizacja pozostałych formularzy i komunikatów modułów,
- redakcyjna akceptacja materiałów, historia wersji, audyt zmian i podgląd szkiców,
- relacje na żywo, powiadomienia, wysyłka newslettera z potwierdzeniem zapisu,
- rozbudowane wideo, obsługa reklam i planowanie ekspozycji na stronie głównej,
- optymalizacja wydajności i cache z selektywnym unieważnianiem, CDN i testy obciążenia,
- polityki prywatności/retencji, monitoring i praktyczny test przywracania backupu,
- docelowy redesign według wskazówek właściciela.

Nie jest to jeszcze komplet funkcji ani skala infrastruktury Onetu.
