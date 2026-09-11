# Kontrola techniczna — 11 września 2026

Sprawdzono inicjalizację produkcyjną na SQLite: pierwsze uruchomienie,
ponowne uruchomienie po edycji treści, odrzucenie pustego hasła administratora
oraz odpowiedzi HTTP strony głównej, kontaktu i endpointu `/up`.

Naprawiono nadpisywanie ustawień strony, kontaktu i sekcji „O nas” przez
ProductionSeeder przy każdym restarcie. Dane startowe są teraz zapisywane
wyłącznie przy ich tworzeniu. Pierwsze hasło administratora wymaga minimum
16 znaków i nie może być wartością przykładową `CHANGE_ME`.

Ustawiono Redis `retry_after` na 180 sekund, powyżej limitu workera (120 sekund).
Obsługa analityki akceptuje również żądania bez nagłówka User-Agent.

To kontrola regresji, nie pełna akceptacja produkcyjna. Nadal wymagane są testy
całego stosu Docker na MariaDB i Redis, SMTP, uploadów oraz odtwarzania kopii.
Panel zawiera jeszcze perskie teksty. Redesign pozostaje osobnym etapem.
