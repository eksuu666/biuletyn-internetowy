# Stan funkcjonalny sŁodziaka — 26 października 2026

## Działający rdzeń

- Laravel 13, PHP 8.4.27+, Nginx, MariaDB 11.4, Redis, worker i scheduler.
- Produkcyjny seed bez fikcyjnych wiadomości; cztery rangi redakcji i Czytelnik.
- Obowiązkowy obieg: autor → korekta sekretarza → akceptacja i publikacja naczelnego.
- Prywatny podgląd, uwagi, historia zmian, przywracanie tytułu i treści,
  wykrywanie równoczesnych zapisów i wycofywanie publikacji.
- Sekretarz i naczelny mogą pisać własne materiały; administrator ma wszystkie funkcje.
- Podpis twórcy pozostaje niezmienny po cudzej korekcie. Klikany awatar prowadzi
  do profilu z nazwą, rangą i opisem. Własny opis i zdjęcie można zmieniać.
- Artykuły z kategoriami, tagami, zdjęciami, wyróżnieniami i embargiem.
- Prywatne i przyszłe publikacje nie trafiają do list, bezpośrednich adresów,
  wyszukiwania, rekomendacji, stron autorów, feedu ani mapy.
- Moderacja komentarzy; poprawiony komentarz ponownie wymaga akceptacji.
- Newsletter: zgoda, potwierdzenie adresu, kampanie w kolejce, podpisana rezygnacja.
- Wideo: przesłanie pliku, odczyt długości przez FFmpeg, miniatura w kolejce,
  edycja bez ponownego przesyłania, uprawnienia do własnych plików i streaming HTTP.
- Reklamy: obraz, adres, miejsce emisji, aktywacja i wygaśnięcie; świeże dane
  w publicznych widokach bez długiego cache nieaktualnej emisji.
- Polski panel głównych operacji redakcyjnych, profile, logowanie i komunikaty;
  pozostałe starsze moduły nadal wymagają dokończenia tłumaczeń.
- Oczyszczanie HTML artykułów, limity prób logowania i formularzy, regeneracja sesji.
- Kontrola bazy, cache, plików i kolejki, log ostrzeżeń oraz retencja analityki.
- Skrypty kopii bazy i plików z sumami kontrolnymi i odtwarzania do nowego stosu.

## Weryfikacja

W odrębnym projekcie Docker `slodziak-verification` sprawdzono przez HTTP:
logowanie z CSRF i sesją Redis, upload zdjęcia, utworzenie prywatnego szkicu,
korektę sekretarza i publikację naczelnego, niezmienne autorstwo i profil autora.
Sprawdzono również wideo (upload, FFmpeg, odczyt fragmentów pliku HTTP 206),
emisję reklamy oraz potwierdzenie zapisu, kampanię i rezygnację newslettera
przez worker Redis i testowy serwer SMTP.

Kopię bazy i storage odtworzono do oddzielnych wolumenów. Kontrola odtworzonego
zestawu potwierdziła 3 artykuły, 6 wpisów historii i brak brakujących mediów.
To stan kopii w chwili próby, późniejsze testy dodawały kolejne dane.

Regresja obejmuje obieg i uprawnienia, konflikty wersji, przywracanie treści,
własne teksty sekretarza i naczelnego, profile, newsletter, reklamy, wideo,
retencję, publikacje i uruchomienie produkcyjne. Końcowy przebieg na MariaDB:
**44 testy, 243 asercje — wszystkie poprawne** (PHP 8.4.27).
Audyty Composer i npm: brak znanych podatności w chwili sprawdzenia.

## Wdrożenie i pozostały zakres

Zobacz [DEPLOYMENT.md](DEPLOYMENT.md) i [EDITORIAL_WORKFLOW.md](EDITORIAL_WORKFLOW.md).
Potrzebne są własne domena, HTTPS, hasła, klucz aplikacji, SMTP i monitoring.
Lokalny stos służył do weryfikacji wariantu serwerowego.

Do kolejnych etapów pozostają redesign, relacje na żywo, powiadomienia push,
rozbudowana redakcja strony głównej, transkodowanie wideo do wielu jakości,
CDN, testy obciążeniowe i skalowanie oraz tłumaczenia mniej używanych modułów.
Właściciel musi zatwierdzić dane wydawcy i dokumenty prywatności.
Nie deklarujemy kompletności funkcji ani skali infrastruktury Onetu.
