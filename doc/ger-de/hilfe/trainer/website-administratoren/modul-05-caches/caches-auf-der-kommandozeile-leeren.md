---
title: "Lektion 5.3: Caches auf der Kommandozeile leeren"
audience: trainer, admin
category: training
order: 53
summary: Die Teilnehmenden listen die Caches mit ihren Größen auf, leeren nach ID und nach Tag mit ezcache.php und exp:cache, prüfen vorab mit --dry-run, lesen PASS und FAIL und leeren den Antwort-Cache von Velocity. 35 Minuten.
keywords: website-administration, ezcache.php, exp:cache, cache.php, clear-tag, clear-id, clear-all, dry-run, purge, list-tags, velocity cache clear, velocity restart, PASS, FAIL, kommandozeile, cache leeren, site administration, command line, clear cache
type: help
---
# Lektion 5.3: Caches auf der Kommandozeile leeren

Deploy-Skripte, Cronjobs und Support-Mitarbeiter ohne Browsersitzung leeren Caches in einem Terminal. Zwei Befehle
erledigen das: das altbewährte `bin/php/ezcache.php` und `exp:cache`, das seinen Code mit der Seite Caches teilt und
`PASS` oder `FAIL` meldet. Diese Lektion verwendet beide und sieht immer erst hin, bevor sie leert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 5: Caches](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, die mit einem Terminal vertraut sind |
| Voraussetzungen | [Lektion 5.2](caches-in-der-administration-leeren.md); ein Terminal im Wurzelverzeichnis der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Caches mit ihren Tags und Größen auflisten;
- mit `ezcache.php` einen Cache nach ID und nach Tag leeren;
- ein Leeren mit `exp:cache ... --dry-run` vorab prüfen und dessen `PASS`-Zeile lesen;
- den Status der Caches vor PHP lesen;
- den Antwort-Cache von Velocity leeren und sagen, wann Velocity einen Neustart braucht.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Eine Website betreiben, Teile 1 und 2](../../../../anleitungen/betrieb-einer-website.md),
[Caches, aus einer Shell](../../../../anleitungen/caches.md#5-in-einer-shell) und
[Cache-Steuerung über die Konsole](../../../../bc/6.0/cache-konsole.md).

1. **Erst hinsehen** (3 Minuten). `php bin/php/cache.php list --sizes` listet jeden Cache mit Tag, Zustand,
   Verzeichnis und Größe; `php bin/php/cache.php status` zeigt die Caches vor PHP auf einem Bildschirm.
2. **ezcache.php** (3 Minuten). `--clear-id=<id>` für einen oder mehrere Caches, `--clear-tag=<tag>` nach Tag,
   `--clear-all` für alles; `--list-tags` und `--list-ids` geben die gültigen Namen aus. `--purge` entfernt Einträge
   physisch, statt sie als abgelaufen zu markieren.
3. **exp:cache** (2 Minuten). Dieselbe Arbeit mit `PASS`/`FAIL` in der letzten Zeile und `--json` für das
   Monitoring; `--dry-run` zeigt, was geleert würde, mit Größen.
4. **Velocity** (2 Minuten). `exp:velocity cache clear` leert den Antwort-Cache; `exp:velocity restart` lässt Velocity
   geänderte Einstellungen lesen. Auf einem Server erledigt `exp:velocity deploy` jeden Schritt einer Bereitstellung in
   der richtigen Reihenfolge ([Hosting und Betrieb](../../hosting-betrieb/README.md), Lektion 4.1).

### Häufige Fragen

**Brauche ich `--allow-root-user`?** Nur wenn Sie den Befehl als root-Benutzer des Betriebssystems ausführen; sonst
verweigern die Skripte root. Besser: Führen Sie sie als der Benutzer aus, dem die Dateien gehören.

**`--clear-id=ini` hat meine Einstellung nicht übernommen.** Verwenden Sie den Tag: `--clear-tag=ini` leert auch den
globalen INI-Cache.

**Wird das Leeren auf der Kommandozeile protokolliert?** Ja, ein Eintrag im Audit-Protokoll pro Ausführung, mit dem
Benutzer des Betriebssystems. Die Seite Caches zeigt sie in ihrer Spalte der letzten Leerung.

### Stolperfallen

- **Als root ausführen und Dateien hinterlassen, die root gehören.** Ein Leeren als root kann Verzeichnisse
  hinterlassen, in die der Webserver nicht schreiben kann. Zeigen Sie nach einem Leeren als root `ls -l var/`, wenn
  Ihre Übungsumgebung das erlaubt.
- **`--clear-all` in einem Cronjob.** Das macht die Website bei jeder Ausführung langsam. Leeren Sie das Kleinste oder
  gar nichts: Das Veröffentlichen leert die betroffenen Ansichten selbst.
- **Falsches Verzeichnis.** Die Befehle müssen im Wurzelverzeichnis der Installation laufen (dem Verzeichnis mit
  `index.php`).

## Praktische Übung

In einem Terminal, im Wurzelverzeichnis Ihrer Schulungsinstallation:

1. Listen Sie die Caches mit ihren Größen auf:

   ```bash
   php bin/php/cache.php list --sizes
   ```

2. Listen Sie die gültigen Tags und IDs auf:

   ```bash
   php bin/php/ezcache.php --list-tags
   php bin/php/ezcache.php --list-ids
   ```

3. Prüfen Sie vorab, was das Leeren aller Caches betreffen würde:

   ```bash
   php bin/php/cache.php clear --all --dry-run
   ```

4. Leeren Sie nur die INI-Caches nach Tag und lesen Sie die Ausgabe:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

5. Leeren Sie die Caches der Template-Blöcke und der Inhalte nach ID:

   ```bash
   php bin/php/ezcache.php --clear-id=template-block,content
   ```

6. Leeren Sie mit `exp:cache` nach Tag und lesen Sie die letzte Zeile:

   ```bash
   php bin/php/cache.php clear --tag=template
   ```

7. Lesen Sie den Status der Caches vor PHP:

   ```bash
   php bin/php/cache.php status
   ```

8. Leeren Sie den Antwort-Cache von Velocity:

   ```bash
   php bin/php/console exp:velocity cache clear --apply
   ```

9. Öffnen Sie im Browser **Setup > Cache-Verwaltung** und lesen Sie die Spalte der letzten Leerung: Welche Leerungen
   wurden aus einer Shell vorgenommen, und von welchem Benutzer des Betriebssystems?

## Erwartetes Ergebnis

- Schritt 3 listet die Caches, die er leeren würde, und ändert nichts.
- Schritt 6 endet mit einer Zeile, die mit `PASS` beginnt.
- Schritt 7 endet mit `PASS  status read` und hat je eine Zeile für den statischen Cache, den HTTP-Cache, den Cache der
  Abfragen, Velocity und PHP.
- Schritt 9 zeigt die Leerungen aus der Shell mit dem Betriebssystem-Benutzer der Teilnehmerin oder des Teilnehmers.
- Der Trainer prüft das, indem er fragt, welchen Befehl man nach einer Template-Änderung verwenden würde.

## Quiz

1. Welche Option von `ezcache.php` leert jeden Cache, der einen bestimmten Tag trägt?
   A. `--clear-id` B. `--clear-tag` C. `--purge` D. `--list-tags`
2. Was bewirkt `--dry-run` bei `exp:cache`?
3. Warum ist nach einer Änderung an den Einstellungen `--clear-tag=ini` besser als `--clear-id=ini`?
4. Welcher Befehl leert den Antwort-Cache von Velocity?
5. Wann braucht ein Skript `--allow-root-user`?

<details>
<summary>Antworten</summary>

1. **B.**
2. Es zeigt, was geleert würde, mit Größen, und ändert nichts.
3. Der Tag leert auch den globalen INI-Cache; die ID allein kann alte Einstellungen in Gebrauch lassen
   ([Cache-Steuerung über die Konsole](../../../../bc/6.0/cache-konsole.md#kurz-gesagt)).
4. `exp:velocity cache clear` (oder `php bin/php/cache.php velocity clear`).
5. Nur wenn es als root-Benutzer des Betriebssystems läuft.

</details>

## Weiterführende Literatur

- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md): die Caches, Teil für Teil.
- [Cache-Steuerung über die Konsole](../../../../bc/6.0/cache-konsole.md): jede Gruppe und Option von `exp:cache`.
- [Caches](../../../../anleitungen/caches.md#5-in-einer-shell): die Shell-Befehle neben der Seite.

## Verwandte Seiten

- [Modul 5: Caches](README.md)
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
