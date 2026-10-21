---
title: "Lektion 6.3: Eine Website wiederherstellen und Wiederherstellungstests"
audience: trainer, hosting, admin
category: training
order: 63
summary: Die Teilnehmenden stellen eine vollständige Website aus der Sicherung von Lektion 6.1 auf einer zweiten Installation in der dokumentierten Reihenfolge wieder her und prüfen das Ergebnis als Wiederherstellungstest. 50 Minuten.
keywords: hosting und betrieb, wiederherstellung, wiederherstellungstest, wartungsmodus, exp:velocity stop, eigentümer, caches leeren, autoloads, prüfungen nach der wiederherstellung, zweiter rechner, gleiches release, hosting and operations, restore, restore test, ownership, clear caches, checks after restore
type: help
---
# Lektion 6.3: Eine Website wiederherstellen und Wiederherstellungstests

„Eine Sicherung, die nie wiederhergestellt wurde, ist eine Hoffnung, keine Sicherung.“ In dieser Lektion beweisen die
Teilnehmenden ihre Sicherung aus Lektion 6.1, indem sie sie auf einer zweiten Schulungsinstallation wiederherstellen,
in der Reihenfolge, die das Installationsbuch angibt, und das Ergebnis so prüfen, wie es ein Wiederherstellungstest
sollte.

> **Hinweis**
> Stellen Sie auf einer zweiten Schulungsinstallation wieder her, nie über eine Website, die jemand anderes verwendet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 6: Sicherungen und Wiederherstellung](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende |
| Voraussetzungen | [Lektion 6.1](was-gesichert-werden-muss.md) mit ihrer Sicherung; eine zweite Schulungsinstallation mit demselben Release auf demselben Datenbanksystem |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- entscheiden, was wiederhergestellt wird und was verloren geht, und zuerst den aktuellen Zustand sichern;
- eine Website in der dokumentierten Reihenfolge wiederherstellen: Wartungsmodus an, die Engine stoppen, Datenbank,
  Dateien, Eigentümer, Caches und Autoloads, starten, den Antwort-Cache leeren, Wartungsmodus aus;
- erklären, warum das Ziel dasselbe Release und dieselbe `composer.lock` wie die Sicherung haben muss;
- eine wiederhergestellte Website prüfen: Inhalte, Bilder, Anmeldung, eine Veröffentlichung, die Logs;
- einen Wiederherstellungstest auf einem anderen Rechner ausführen und festhalten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md) und
[Kapitel 10, Wiederherstellung](../../../../installation/10-nach-der-installation.md#wiederherstellung).

1. **Vor der Wiederherstellung** (4 Minuten). Eine Wiederherstellung bringt die ganze Website auf den Zeitpunkt der
   Sicherung zurück; bei einer einzelnen gelöschten Seite sehen Sie zuerst in den Papierkorb und in ältere Versionen.
   Sichern Sie den aktuellen Zustand. Stellen Sie auf demselben Release wieder her; eine ältere Datenbank braucht danach
   die Upgrade-Schritte.
2. **Die Reihenfolge** (6 Minuten). Wartungsmodus an; unter Velocity den Server stoppen, damit kein Worker die alte
   Datenbank hält; die Datenbank mit dem Werkzeug ihres Datenbanksystems wiederherstellen und die Dateien entpacken;
   Eigentümer und Modus korrigieren; jeden Cache leeren und die Autoloads neu erzeugen; Velocity starten, seinen
   Antwort-Cache leeren, den Wartungsmodus ausschalten. Unter PHP-FPM den Pool neu laden, wenn sich Code oder
   Einstellungen geändert haben.
3. **Prüfungen** (3 Minuten). Startseite und einige tiefere Seiten, Bilder, Anmeldung an der Administration, eine
   Testveröffentlichung, die neuesten Zeilen von `error.log`.
4. **Wiederherstellungstests** (2 Minuten). Auf einem anderen Rechner, mindestens einmal und nach jedem größeren
   Upgrade; halten Sie das Datum, die verwendete Sicherung, die benötigte Zeit und das Geprüfte fest.

### Häufige Fragen

**Wiederhergestellte Dateien gehören root. Ist das ein Problem?** Ja: Der Webserver kann sie später nicht ersetzen,
und die Website schlägt an seltsamen Stellen fehl. Arbeiten Sie als Benutzer der Website oder korrigieren Sie die
Eigentümer danach.

**Die wiederhergestellte Website zeigt in Links die falsche Adresse.** `settings/override/` stammt aus einer anderen
Installation; passen Sie auf einem Testrechner die Einstellungen für die Website-Adresse an oder stellen Sie nur
`var/storage` und die Datenbank wieder her.

**Wie lange dauert eine Wiederherstellung?** Messen Sie es beim Test; die Richtlinie braucht diese Zahl.

### Stolperfallen

- **`settings/override/` über die zweite Installation entpacken** ersetzt deren Datenbankeinstellungen durch die der
  ersten. Entpacken Sie für die Übung nur `var/storage` und behalten Sie die Einstellungen der zweiten Installation.
- **Vergessen, Velocity zu stoppen**, bevor eine SQLite-Datei durch Ersetzen wiederhergestellt wird.
- **Unterschiedliche Releases.** Installieren Sie die zweite Installation aus demselben Release wie die erste.

## Praktische Übung

Verwenden Sie eine zweite Schulungsinstallation mit demselben Release und Datenbanksystem (zum Beispiel
`/var/www/training2`), als Benutzer der Website. `<first>` ist der Pfad der Installation, die Sie in Lektion 6.1
gesichert haben.

1. Erstellen Sie in der zweiten Installation eine schnelle Sicherung ihrer aktuellen Datenbank (Lektion 3.4), damit
   sich die Übung rückgängig machen lässt.
2. Schalten Sie den Wartungsmodus ein: `php bin/php/maintenance.php on --message="Restore practice" --until=30m`.
3. Stoppen Sie die Engine: `php bin/php/console exp:velocity stop`.
4. Stellen Sie die Datenbank aus `<first>/var/training-backup/` mit dem Werkzeug des Datenbanksystems wieder her
   (SQLite: `.restore` in die Datenbankdatei dieser Installation).
5. Entpacken Sie nur die Speicherdateien: `tar xzf <first>/var/training-backup/files-<date>.tar.gz var/storage`.
6. Prüfen Sie die Eigentümer: `ls -l var/storage | head` (korrigieren Sie bei Bedarf mit `chown` auf den Benutzer der
   Website).
7. Leeren Sie die Caches und erzeugen Sie die Autoloads neu:

   ```bash
   php bin/php/ezcache.php --clear-all
   php bin/php/ezpgenerateautoloads.php -e
   ```

8. Starten Sie die Engine und leeren Sie ihren Antwort-Cache:

   ```bash
   php bin/php/console exp:velocity start
   php bin/php/console exp:velocity cache clear
   ```

9. Schalten Sie den Wartungsmodus aus: `php bin/php/maintenance.php off`.
10. Prüfen Sie: Ihr Kursordner und die Artikel aus der ersten Installation sind da; ein Bild wird angezeigt; Sie können
    sich an der Administration anmelden; veröffentlichen Sie eine Teständerung;
    `exp:logs --tail=var/log/error.log --lines=10` zeigt nichts Neues.
11. Halten Sie den Wiederherstellungstest fest: Datum, Sicherungsdatei, benötigte Minuten, bestandene Prüfungen.

## Erwartetes Ergebnis

- Die zweite Installation zeigt die Inhalte der ersten, mit ihren Bildern.
- Anmeldung und Testveröffentlichung funktionieren.
- `error.log` hat keine neuen Fehler.
- Jede Person hat ein Protokoll des Wiederherstellungstests mit der benötigten Zeit.
- Der Trainer prüft das, indem er den wiederhergestellten Kursordner einer Person am Beamer öffnet.

## Quiz

1. Was prüfen Sie vor der Wiederherstellung einer ganzen Website zu den Daten, die verloren gehen?
2. Warum wird Velocity gestoppt, bevor die Datenbank wiederhergestellt wird?
   A. um Speicher zu sparen B. damit kein Worker die alte Datenbank hält C. um den Antwort-Cache zu leeren D. es ist
   nicht nötig
3. Warum muss das Ziel dasselbe Release wie die Sicherung haben?
4. Bringen Sie in die richtige Reihenfolge: Caches leeren, Wartungsmodus an, Datenbank wiederherstellen,
   Wartungsmodus aus.
5. Was macht einen Wiederherstellungstest für die Sicherungsrichtlinie nützlich?

<details>
<summary>Antworten</summary>

1. Dass seit der Sicherung veröffentlichte Inhalte verloren gehen; bei einer einzelnen gelöschten Seite kommen zuerst
   der Papierkorb und ältere Versionen.
2. **B.** ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md#die-wiederherstellung-schritt-für-schritt))
3. Eine Datenbank aus einem älteren Release braucht die Upgrade-Schritte; Code und Daten müssen zusammenpassen.
4. Wartungsmodus an, Datenbank wiederherstellen, Caches leeren, Wartungsmodus aus.
5. Er beweist, dass sich die Sicherung wiederherstellen lässt, und misst, wie lange es dauert, eine Zahl, die die
   Richtlinie braucht.

</details>

## Weiterführende Informationen

- [Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)
- [Runbook: aus einer Sicherung wiederherstellen](../../../hosting/runbooks/aus-einem-backup-wiederherstellen.md)
- [Kapitel 10, Sicherungen und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)
- [Eine Website zwischen Hostern umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md): dieselben
  Schritte, ein anderer Zweck.

## Verwandte Seiten

- [Modul 6: Sicherungen und Wiederherstellung](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
