---
title: "Lektion 7.1: Die Logdateien"
audience: trainer, admin
category: training
order: 71
summary: Die Teilnehmenden nennen die Logdateien einer Installation und was jede enthält, finden sie auf der Festplatte, lesen die Anfrage-Logs 404.log und security.log und erklären, wie die Rotation die Logs klein hält. 35 Minuten.
keywords: website-administration, logdateien, error.log, warning.log, notice.log, debug.log, 404.log, security.log, var/log, log-rotation, LogFileMaxSize, exp:logs, site administration, log files, log rotation
type: help
---
# Lektion 7.1: Die Logdateien

Wenn etwas schiefgeht, beginnt ein Administrator bei den Logdateien. Exponential schreibt echte Fehler in `error.log`,
fehlende Seiten in `404.log` und Anfragen, die wie Angriffe aussehen, in `security.log`, und hält jedes Log klein,
indem es rotiert wird. Diese Lektion zeichnet die Karte der Dateien, bevor Lektion 7.2 den Viewer zeigt, der sie liest.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 7: Logs und das Audit-Protokoll](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | Eine Schulungsinstallation mit Shell-Zugang; [Modul 6](../modul-06-cronjobs-workflows-und-benachrichtigungen/README.md) hilft |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Kernel-Logs in `var/log` nennen und sagen, welche Art von Zeile jedes enthält;
- erklären, warum eine fehlende Seite in `404.log` steht und nicht in `error.log`;
- eine Zeile von `404.log` lesen und ihren Status, die Anfrage, den Siteaccess und den Grund nennen;
- erklären, wann ein Log rotiert wird und wie viele Archive standardmäßig aufbewahrt werden;
- die Logdateien mit `exp:logs --list` auflisten.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md) („Kurz gesagt“),
[Anfrage-Logs: 404.log und security.log](../../../../../eng-us/features/6.0/request-logs.md) und
[Log-Rotation und Bereinigung](../../../../../eng-us/features/6.0/log-rotation.md).

1. **Wo die Logs liegen** (3 Minuten). Die Kernel-Logs liegen in `var/log`: `error.log`, `warning.log`, `notice.log`,
   `debug.log`, `strict.log`, `deprecated.log` und `auth.log`, dazu die Cronjob-Logs. Eine Website kann ihre eigenen
   Logs in `var/<site>/log` haben. Auch Erweiterungen schreiben eigene Dateien.
2. **Eine Anfrage, ein Log** (4 Minuten). `error.log` enthält die echten Fehler. Eine Antwort 404 oder 410 geht mit
   dem Grund, den der Kernel gefunden hat, in `404.log`. Was der Anfrage-Klassifikator für eine Sondierung oder einen
   Angriff hält, geht unabhängig vom Status mit seiner Kategorie und Regel in `security.log`. Eine Anfrage steht nur in
   einem davon.
3. **Datenschutz** (2 Minuten). Die Anfrage-Logs schreiben maskierte Client-Adressen (`203.0.x.x`) und ersetzen in
   `404.log` die Werte der Query durch `***`. Nichts wird blockiert; der Klassifikator klassifiziert nur.
4. **Rotation** (4 Minuten). Wird ein Log größer als `site.ini [FileSettings] LogFileMaxSize` (2 MB), rotiert der
   nächste Schreibvorgang es: Die Datei wird zu `.1`, die älteren Archive rücken auf, und Archive über
   `LogFileRotateCount` (3) hinaus werden entfernt. Das Komprimieren der Archive ab `.2` ist optional und
   standardmäßig aus.
5. **Die Kommandozeile** (2 Minuten). `php bin/php/console exp:logs --list` listet die Dateien mit Größe, Zeit und
   Zeilen pro Minute.

### Häufige Fragen

**Warum ist mein alter Fehler weg?** Das Log wurde rotiert, und das Archiv, das ihn enthielt, wurde nach drei
Rotationen entfernt. Erhöhen Sie `LogFileRotateCount` in einem Override, wenn Sie mehr Verlauf brauchen.

**Ist `security.log` der Beweis für einen Angriff?** Nein. Es hält fest, was eine Regel klassifiziert hat, mit einer
Zuverlässigkeit von niedrig, mittel oder hoch. Lesen Sie es als Hinweise.

**root hat ein Log geschrieben, und jetzt kann die Website es nicht mehr.** Eine Rotation legt das neue Log mit
Eigentümer und Modus des alten an, aber ein Log, das root zuerst auf der Kommandozeile angelegt hat, kann den
Webserver trotzdem blockieren. Lektion 7.2 zeigt **Zustand der Logs**, das vor solchen Dateien warnt.

### Stolperfallen

- **Riesige Logs in einem Editor öffnen.** Ein `debug.log` kann groß sein. Verwenden Sie `tail` oder den Viewer.
- **Log-Zeilen in Tickets einfügen.** Auch maskierte Zeilen können Details enthalten; fügen Sie nie unmaskierte Zeilen
  ein.
- **Verschiedene Server, verschiedene Logs.** Jeder Web-Knoten hat sein eigenes `var/log`. Sehen Sie in einem Cluster
  auf dem Knoten nach, der geantwortet hat.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, in einem Terminal im Wurzelverzeichnis der Installation.

1. Listen Sie die Kernel-Logs auf: `ls -l var/log`. Notieren Sie, welche Dateien es gibt und wie groß sie sind.
2. Listen Sie die Logs so auf, wie der Viewer sie gruppiert:

   ```bash
   php bin/php/console exp:logs --list
   ```

3. Öffnen Sie in einem Browser eine Adresse Ihrer Schulungs-Website, die es nicht gibt, zum Beispiel
   `/no-such-page-training`.
4. Lesen Sie das Ende von `404.log`:

   ```bash
   tail -n 3 var/log/404.log
   ```

   Suchen Sie Ihre Anfrage und ihr Feld `reason=`.
5. Lesen Sie die letzten Einträge von `error.log` über die Konsole (maskiert):

   ```bash
   php bin/php/console exp:logs --tail=var/log/error.log --lines=10
   ```

6. Suchen Sie die verwendeten Einstellungen zur Rotation:

   ```bash
   grep -n "LogFileMaxSize\|LogFileRotateCount\|LogFileCompress" settings/site.ini
   ```

7. Sehen Sie eine Rotation von `debug.log` vorab an, ohne sie auszuführen:

   ```bash
   php bin/php/console exp:logs --rotate=debug --dry-run
   ```

8. Schreiben Sie für `error.log`, `404.log` und `security.log` je einen Satz dazu auf, welche Art von Zeile das Log
   enthält.

## Erwartetes Ergebnis

- Schritt 2 listet Gruppen wie **Kernel** und **Site** mit Größen und Zeiten.
- Schritt 4 zeigt eine Zeile, die mit der Zeit und einer maskierten Adresse beginnt, dann
  `404 GET /no-such-page-training`, den Siteaccess und einen Grund wie `reason=module:no-such-page-training` oder einen
  Knoten- oder Kernel-Grund.
- Schritt 6 zeigt `LogFileMaxSize=2097152` und `LogFileRotateCount=3`, sofern kein Override sie ändert.
- Schritt 7 sagt, was rotiert würde, und ändert nichts.
- Der Trainer prüft das, indem er eine Teilnehmerin oder einen Teilnehmer die eigene 404-Zeile laut vorlesen und das
  Feld des Grundes erklären lässt.

![Zustand der Logs: jede Logdatei mit ihrer Größe, ihrem Wachstum und der letzten Änderung](../../../../../eng-us/help/images/trainers/sa-log-health.webp)

## Quiz

1. Ein Besucher öffnet eine Seite, die es nicht gibt. Welches Log bekommt die Zeile?
   A. `error.log` B. `404.log` C. `security.log` D. `notice.log`
2. Wie groß kann ein Log mit den Standardeinstellungen werden, bevor es rotiert wird, und wie viele Archive werden
   aufbewahrt?
3. Eine Anfrage nach `/wp-login.php` auf Ihrer Website endet mit einem 404. In welchem Log steht sie, und warum?
4. Was geschieht mit der Client-Adresse in den Anfrage-Logs?
5. Welcher Befehl listet die Logdateien in einer Shell auf?

<details>
<summary>Antworten</summary>

1. **B.** Antworten „nicht gefunden“ gehen mit ihrem Grund in `404.log`
   ([Anfrage-Logs](../../../../../eng-us/features/6.0/request-logs.md)).
2. Etwa 2 MB (`LogFileMaxSize=2097152`), und drei Archive (`LogFileRotateCount=3`).
3. Nur in `security.log`: Der Klassifikator stuft sie als Sondierung ein, und eine klassifizierte Anfrage wird nur in
   ein Log geschrieben.
4. Sie wird maskiert (`203.0.x.x`); die echte Adresse wird nie in diese Dateien geschrieben.
5. `php bin/php/console exp:logs --list`.

</details>

## Weiterführende Literatur

- [Anfrage-Logs: 404.log und security.log](../../../../../eng-us/features/6.0/request-logs.md): jedes Feld und jede Einstellung.
- [Log-Rotation und Bereinigung](../../../../../eng-us/features/6.0/log-rotation.md): Rotation, Bereinigung und Grenzen pro Log.
- [Der Log-Viewer und die Rotation für Hoster](../../../hosting/betrieb/log-viewer-und-log-rotation.md): die Sicht des
  Hosters.
- [Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md): die ersten Schritte bei jedem Problem.

## Verwandte Seiten

- [Modul 7: Logs und das Audit-Protokoll](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
