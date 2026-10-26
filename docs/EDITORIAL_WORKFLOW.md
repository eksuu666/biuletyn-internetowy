# Obieg redakcyjny sŁodziaka

## Konta i role

Administrator przypisuje rangę w panelu użytkowników. Nowa rejestracja otrzymuje
wyłącznie rangę Czytelnik. Ostatniego administratora nie można zdegradować ani usunąć.

| Ranga | Zadania |
| --- | --- |
| Redaktor | Pisze i edytuje własne szkice, przekazuje materiał do korekty. |
| Sekretarz redakcji | Koryguje przekazane materiały, zapisuje poprawki i oznacza gotowość do akceptacji. |
| Redaktor naczelny | Poprawia materiały po korekcie sekretarza, publikuje lub odsyła z uwagami, wycofuje publikacje. |
| Administrator | Ma wszystkie funkcje oraz zarządza kontami, rangami i newsletterem. |

Techniczne nazwy rang: `Editor`, `Secretary`, `EditorInChief`, `Admin`.
Starsza ranga `Author` ma takie same uprawnienia jak Redaktor.
Sekretarz i naczelny mogą pisać własne teksty. Ich materiały przechodzą ten sam obieg.
Sekretarz może skorygować własny tekst; o publikacji decyduje naczelny.
Naczelny przekazuje swój tekst sekretarzowi. Administrator może wykonać wszystkie
etapy, ale również musi zapisać etap korekty przed publikacją.

## Od szkicu do publikacji

1. Autor zapisuje artykuł jako **Szkic**. Wybiera kategorię, zdjęcie, tagi i datę.
2. W widoku podglądu przekazuje tekst **Do korekty**.
3. Sekretarz otwiera edycję, zapisuje poprawki, następnie oznacza materiał
   **Do akceptacji naczelnego**. Sam zapis poprawek jeszcze nie zatwierdza korekty.
4. Naczelny może poprawić tekst i go opublikować. Przyszła data oznacza embargo:
   materiał stanie się publiczny dopiero o tej godzinie, w strefie Europe/Warsaw.
5. Sekretarz lub naczelny może odesłać materiał z obowiązkowymi uwagami.
   Autor poprawia i ponownie przekazuje tekst przez etap korekty.

Redaktor nie widzi cudzych szkiców. Edycja przekazanego tekstu przez autora cofa
go do szkicu. Poprawienie przez sekretarza tekstu oczekującego na naczelnego
wymaga ponownego oznaczenia gotowości. Naczelny może poprawiać opublikowany tekst;
zmiana jest natychmiast publiczna i zapisana w historii.

Wycofanie publikacji ukrywa ją również w wyszukiwarce, listach, RSS i sitemapie.
Ponowna publikacja wymaga korekty. Opublikowanego tekstu nie można usunąć bez
wcześniejszego wycofania. Usunięcie jest miękkie: historia pozostaje w bazie.

## Historia i konflikty edycji

Podgląd pokazuje historię zapisów i przejść, osobę, rangę, czas i uwagi.
Przywrócenie wersji odtwarza **tytuł i treść**, wymaga ponownej korekty;
nie przywraca zdjęcia, tagów, adresu ani decyzji publikacyjnej.
Równoległa edycja jest wykrywana numerem wersji; przy konflikcie trzeba odświeżyć
formularz. Podgląd szkiców wymaga zalogowania i jest oznaczony `noindex`.

Migracja zachowuje dotychczas opublikowane artykuły jako opublikowane.
Nie dopisuje im fikcyjnych zatwierdzeń. Nowe materiały podlegają pełnemu obiegowi.
Domyślne rangi są synchronizowane przy uruchomieniu produkcyjnego seeda;
własne zmiany ich uprawnień należy wprowadzać w konfiguracji repozytorium.

## Autor pod artykułem

Podpis pozostaje przypisany do twórcy materiału, także po korekcie innej osoby.
Kliknięcie zdjęcia prowadzi do profilu z nazwą, rangą, opisem i publicznymi tekstami.
Każdy autor ustawia nazwę, zdjęcie i opis (do 600 znaków) we własnym profilu.
Brak zdjęcia wyświetla domyślny awatar.

## Newsletter

Zapis wymaga zgody i kliknięcia w link potwierdzający z poczty (ważny 7 dni).
Starsze zapisy bez potwierdzenia nie są odbiorcami kampanii.
Administrator przygotowuje temat i treść, sprawdza podgląd i uruchamia wysyłkę.
Kolejka rozsyła tylko do potwierdzonych adresów, ponownie sprawdzając rezygnację
przed każdą wiadomością. Kampanii nie można uruchomić drugi raz.
Każdy e-mail zawiera podpisany link rezygnacji z dodatkowym potwierdzeniem.
Przy awarii dokładnie po przyjęciu e-maila przez SMTP, a przed zapisem wyniku
w bazie, ponowienie zadania może wysłać duplikat; SMTP nie zapewnia transakcji z bazą.
