# Audyt startowy repozytorium

Stan bazowy przed pracami: Laravel 10, PHP 8.1+, modułowa architektura `nwidart/laravel-modules`, Blade, Vite i MySQL.

## Co można zachować

- artykuły, kategorie, tagi i strony,
- panel redakcyjny z rolami i uprawnieniami,
- komentarze z moderacją,
- media, reklamy, newsletter i formularz kontaktowy,
- SEO, przekierowania, feed i sitemapę,
- podstawową analitykę odsłon.

## Problemy zastane

- publiczny szablon i panel były przygotowane po persku oraz w układzie RTL,
- strefa czasowa była ustawiona na Teheran, a format daty używał kalendarza Jalali,
- Open Graph miał stałe `fa-ir`,
- brakowało kompletnego wdrożenia produkcyjnego, kolejki, schedulera i trwałego modelu storage,
- domyślna konfiguracja włączała debug i lokalne, synchroniczne sterowniki,
- migracja `site_details` przy rollbacku próbowała usunąć tabelę `header`,
- model `SiteDetail` odrzucał identyfikatory logotypów podczas seeda,
- dwa odnośniki/teksty alternatywne w sekcji głównej generowały błędne adresy,
- strona główna zakładała istnienie artykułów i mogła się wywrócić na pustej bazie,
- brakowało endpointu gotowości i podstawowych nagłówków bezpieczeństwa.

## Zrealizowany fundament

- polska strefa czasowa, język, format dat i publiczny układ LTR,
- pierwsza warstwa identyfikacji sŁodziaka i polskie teksty strony publicznej,
- bezpieczny pusty stan przed publikacją pierwszego artykułu,
- powtarzalne wdrożenie: Nginx, PHP-FPM, MariaDB, Redis, worker i scheduler,
- produkcyjny plik konfiguracyjny bez sekretów,
- idempotentny seed startowy bez fikcyjnych treści,
- naprawy wykrytych błędów migracji i widoków,
- endpoint `/up` oraz bazowe nagłówki bezpieczeństwa.
- aktualizacja do Laravel 13 i PHP 8.4 oraz odświeżenie zależności bez znanych alertów bezpieczeństwa,
- przeniesienie `nwidart/laravel-modules` do zależności produkcyjnych, aby instalacja `--no-dev` zawierała kod modułów.

## Następne etapy

1. Pełna polonizacja i przebudowa panelu redakcyjnego z RTL na LTR.
2. Docelowa identyfikacja wizualna: logo, favicon, typografia, kolory i grafika social preview.
3. Nowy układ strony głównej oraz stron artykułu, kategorii, wyszukiwania i autora.
4. Prywatność: polityka prywatności, cookies/zgody, retencja danych i eksport/usuwanie danych.
5. Testy publikacji, uprawnień, uploadów, kolejek, maili, SEO i odtwarzania backupu.
6. Automatyczne wdrożenie z właściwego repozytorium oraz monitoring błędów i dostępności.
