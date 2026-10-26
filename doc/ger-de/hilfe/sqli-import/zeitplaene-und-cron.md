---
title: Zeitpläne und Cron in 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 30
summary: Wie 7x SQLI Import 2.0 Importe von selbst startet - die Arten von Zeitplänen von einmal bis Cron-Ausdruck, Zeitzonen, verpasste Läufe, die Seite Schedules, die Cron-Teile sqliimport und expqueue - und wie ein anderes System einen Import mit einem Webhook oder API-Schlüssel startet.
keywords: sqliimport, Zeitplan, Seite Schedules, sqliimport/schedules, anhalten, fortsetzen, Cron, Cron-Ausdruck, Intervall, stündlich, täglich, wöchentlich, monatlich, Zeitzone, Sommerzeit, nachholen, benachrichtigen, runcronjobs, tick, Webhook, Auslöser, API-Schlüssel, signierter Aufruf, HMAC, sqliimport/trigger, sqliimport_run, sqliimport_cleanup, schedule, cron expression, time zone, trigger, API key
type: help
---
# Zeitpläne und Cron in 7x SQLI Import

Ein Import kann von selbst starten: nach einem **Zeitplan** (jede Nacht um 02:30, alle 10 Minuten) oder wenn ein
**anderes System** ihn aufruft (ein Shop, der gerade seine Produkte exportiert hat). Diese Seite zeigt beides.

## Inhalt

1. [Ein Zeitplan, Schritt für Schritt](#ein-zeitplan-schritt-für-schritt)
2. [Arten von Zeitplänen](#arten-von-zeitplänen)
3. [Die Seite Schedules](#die-seite-schedules)
4. [Zeitzonen und verpasste Läufe](#zeitzonen-und-verpasste-läufe)
5. [Die Cron-Teile](#die-cron-teile)
6. [Von einem anderen System gestartet](#von-einem-anderen-system-gestartet)

## Ein Zeitplan, Schritt für Schritt

Das Beispiel ist der Demo-Import „Demo: PHP news (Atom feed)“, der alle 10 Minuten einen Newsfeed liest.

1. Öffnen Sie den Import mit **Edit** und wählen Sie den Schritt **5 Schedule**.
2. Wählen Sie unter **Runs by itself** die Art **Every N minutes, from the date and time below**.
3. **Date and time**: der erste Lauf. **Time zone**: `UTC` (oder Ihre eigene, etwa `Europe/Berlin`).
4. **Every how many minutes**: `10` (mindestens 5).
5. **When runs were missed**: **Run once as soon as possible** (einmal so bald wie möglich).
6. **E-mail when a run fails**: eine Adresse wie `imports@example.invalid`, oder leer.
7. Lassen Sie **The schedule is active** angehakt und klicken Sie auf **Save**. Der Schritt listet die nächsten fünf
   Laufzeiten.

![Der Schritt Zeitplan des PHP-News-Imports: Every N minutes gewählt, Datum und Uhrzeit, Zeitzone UTC, alle 10 Minuten, das Feld für den Cron-Ausdruck leer, Run once as soon as possible, der Zeitplan aktiv und die nächsten fünf Läufe im Abstand von zehn Minuten](../bilder/sqli-import/sqli-import-editor-schedule.webp)

Ab dann reiht der Cron-Teil `sqliimport` alle 10 Minuten einen Lauf ein. Ein Feed, der sich nicht geändert hat,
ergibt Läufe mit nur **Unchanged**-Datensätzen und keine neuen Versionen.

## Arten von Zeitplänen

Der Editor führt einen Zeitplan pro Import; **Never** entfernt ihn.

| Art | Läuft |
|---|---|
| Once | Zu einem Datum und einer Uhrzeit |
| Every N minutes | Ab Datum und Uhrzeit alle N Minuten, mindestens 5 |
| Every hour | Zur Minute der Uhrzeit |
| Every day | Zur Uhrzeit |
| Every week | Am Wochentag zur Uhrzeit |
| Every month | Am Tag zur Uhrzeit (in einem kürzeren Monat am letzten Tag) |
| A cron expression | Fünf Felder, etwa `30 2 * * *` (täglich um 02:30) oder `0 6 * * mon-fri` (werktags um 06:00) |

Cron-Ausdrücke unterstützen Listen, Bereiche, Schritte, die Namen von Monaten und Tagen sowie die Kürzel `@hourly`,
`@daily`, `@weekly`, `@monthly` und `@yearly`. Einzelheiten: `/exphelp/viewer/ext/sqliimport/ger-de/schedules.md`.

## Die Seite Schedules

**Schedules** im linken Menü des Reiters Importe (`sqliimport/schedules`) listet jeden Zeitplan mit seinem Import,
dem Zeitplan in Worten, ob er aktiv oder angehalten ist, den nächsten drei Laufzeiten und dem letzten Lauf.

![Die Seite Schedules: der Import Demo: PHP news (Atom feed), alle 10 Minuten, aktiv, seine nächsten drei Läufe im Abstand von zehn Minuten, der letzte Lauf #122 und die Schaltflächen Pause, Edit und Delete](../bilder/sqli-import/sqli-import-schedules.webp)

Benutzer mit Manage imports bekommen außerdem diese Aktionen:

- **Pause** schaltet den Zeitplan ab und behält ihn; **Resume** schaltet ihn wieder ein und berechnet seinen nächsten
  Lauf ab jetzt;
- **Edit** öffnet den Schritt Zeitplan des Imports im Editor;
- **Delete** entfernt den Zeitplan nach einer Rückfrage; der Import bleibt.

Ein deaktivierter Import behält seinen Zeitplan, aber seine geplanten Läufe werden übersprungen, bis er wieder
aktiviert ist.

## Zeitzonen und verpasste Läufe

- Jeder Zeitplan hat eine Zeitzone (Vorgabe `UTC`). „Täglich um 02:30“ in `Europe/Berlin` bleibt das ganze Jahr um
  02:30 Ortszeit. Am Tag der Umstellung auf Sommerzeit wird eine Uhrzeit, die es nicht gibt, übersprungen; am Tag der
  Rückstellung läuft eine Uhrzeit, die es zweimal gibt, einmal.
- Lief der Cron eine Weile nicht, läuft ein Zeitplan einmal zum Nachholen (die Vorgabe) oder wartet auf seine nächste
  Zeit, wenn er verpasste Läufe **überspringen** soll.
- Läuft der Import noch, wenn sein Zeitplan fällig wird, wird dieser Lauf nicht gestartet; der Zeitplan wartet auf
  seine nächste Zeit.
- Die E-Mail-Adressen des Zeitplans erfahren, wenn einer seiner Läufe fehlschlägt oder mit fehlgeschlagenen
  Datensätzen endet, zusätzlich zu den Benachrichtigungen des Imports
  ([Importe und Läufe](importe-und-laeufe.md#benachrichtigungen-und-die-glocke)).

## Die Cron-Teile

Zwei Cron-Teile müssen jede Minute laufen:

```bash
* * * * * cd /path/to/exponential && php runcronjobs.php -q expqueue
* * * * * cd /path/to/exponential && php runcronjobs.php -q sqliimport
```

- `sqliimport` reiht die Läufe fälliger Zeitpläne und offener Importanfragen von 1.x ein, beendet Läufe, deren Jobs
  beendet sind, und entfernt alte Läufe. Er reiht nur ein; Importarbeit leistet er selbst nicht.
- `expqueue` betreibt die Worker, die die Läufe verarbeiten. expqueue kann stattdessen auch als systemd-Dienst
  laufen.

`./console ext:sqliimport:tick` führt eine Runde des Cron-Teils `sqliimport` von Hand aus und gibt aus, was sie
getan hat.

Die Cron-Teile `sqliimport_run` und `sqliimport_cleanup` von 1.x funktionieren noch, sind aber veraltet. Ersetzen Sie
sie durch `sqliimport`.

## Von einem anderen System gestartet

Ein anderes System (ein Shop, ein CRM, eine Build-Pipeline) kann einen Import starten, indem es eine Adresse der
Website aufruft, und die Daten im Aufruf mitschicken. Das ist der **Webhook- / API-Auslöser** des Imports. Er ist für
jeden Import aus, bis Sie ihn im Schritt **Schedule**, Abschnitt **Started by another system (webhook or API)**,
einschalten:

![Der Abschnitt Started by another system des Bücher-Imports: The trigger is on, Run as user (ID), A call may bring the data, Signing secret by name, die Adresse POST https://alpha.se7enx.com/admin/sqliimport/trigger/demo_books_datatable und die Formulare für das Signiergeheimnis und einen neuen API-Schlüssel](../bilder/sqli-import/sqli-import-editor-trigger.webp)

1. Haken Sie **The trigger is on** an und tragen Sie **Run as user (ID)** ein: einen Benutzer mit Run imports oder
   Manage imports für diesen Import. Ein ausgelöster Lauf läuft nie als anonymer Benutzer.
2. Haken Sie **A call may bring the data** an, wenn der Aufrufer die Datei selbst schickt (bis 20 MB, im Format des
   Imports).
3. **Save the trigger**. Der Abschnitt zeigt die Adresse, etwa
   `POST https://www.example.invalid/admin/sqliimport/trigger/books`, auf dem Siteaccess, in dem die Erweiterung aktiv
   ist.
4. Lassen Sie die Erweiterung ein **Signiergeheimnis** erzeugen (es wird einmal gezeigt; der Aufrufer signiert jeden
   Aufruf mit HMAC-SHA256), oder geben Sie dem Schlüssel einen Namen und klicken auf **Create an API key** (ebenfalls
   einmal gezeigt). Bewahren Sie ihn im anderen System auf, nie in einer Seite oder einem Repository.

Ein Aufruf mit API-Schlüssel, der eine CSV-Datei als Daten des Laufs schickt:

```bash
curl -sS -X POST "https://www.example.invalid/admin/sqliimport/trigger/books" \
     -H "Authorization: Bearer sq-demo-token-REPLACE" \
     -H "Content-Type: text/csv" --data-binary @books.csv
```

Die Antwort ist JSON, `202` mit der ID des neuen Laufs, etwa `{"run_id": 42, "status": "queued", "status_url": "..."}`;
derselbe Schlüssel kann unter `status_url` den Status des Laufs abfragen. Ein Aufruf ohne gültige Signatur oder
gültigen Schlüssel bekommt `401`, ein Import mit ausgeschaltetem Auslöser antwortet `404`, als gäbe es ihn nicht, und
`409` heißt, dass noch ein Lauf des Imports im Gange ist. Die Seite des Laufs zeigt **REST API** als Auslöser. Jeder
Aufruf wird protokolliert und ist begrenzt. Wie ein Aufruf signiert wird, alle Antworten und die Grenzen:
`/exphelp/viewer/ext/sqliimport/ger-de/triggers.md`.

Weiter: [Aktualisierung von 1.x](aktualisierung-von-1x.md).
