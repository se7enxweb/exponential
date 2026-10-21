---
title: "Lektion 4.2: Das Datenbank-Runbook: Vorabprüfung, Phase A und Phase B"
audience: trainer, admin, developer
category: training
order: 42
summary: Die Teilnehmenden planen die In-place-Migration der Datenbank einer alten Website - zuerst Zeichensatz und Tabellentyp, die Vorabprüfungen, die SQL-Update-Dateien der Reihe nach (Phase A), dann die Reparaturskripte gegen das fertige Schema (Phase B) - und wissen, warum diese Reihenfolge in zwei Phasen die ist, die mit dem heutigen Code funktioniert. 60 Minuten.
keywords: migration zu exponential, datenbank-update, update-dateien, vorabprüfung, phase a, phase b, reparaturskripte, remote-id, migrating to exponential, database update, dbupdate, update files, preflight queries, phase A, phase B, repair scripts, ezconvertdbcharset, ezconvertmysqltabletype, checkdbfiles, remote id, runbook
type: help
---
# Lektion 4.2: Das Datenbank-Runbook: Vorabprüfung, Phase A und Phase B

Die Datenbank einer alten Website wird nicht exportiert und neu importiert; sie wird mit den Update-Dateien des
Repositorys auf den aktuellen Stand gebracht. Diese Lektion vermittelt das Runbook aus Kapitel 14.5 in der
Reihenfolge, die mit der heutigen einheitlichen Codebasis funktioniert, und lässt die Teilnehmenden es für eine
gegebene Ausgangsversion planen.

> **Hinweis**
> Das Runbook wird immer auf einer eingespielten Kopie geprobt, niemals auf der Produktionsdatenbank. In dieser
> Lektion planen die Teilnehmenden es und führen nur lesende Prüfungen aus.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 4: Von der 4.x-Linie](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler, die den Datenbank-Strang betreuen |
| Voraussetzungen | [Lektion 4.1](bestandsaufnahme-der-alten-website.md); SQL-Grundlagen; Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Schritte des Datenbank-Runbooks in ihre Reihenfolge bringen: Zeichensatz und Tabellentyp, Vorabprüfung,
  Phase A, Phase B;
- die SQL-Update-Dateien von einer gegebenen Ausgangsversion bis 6.0.15 auflisten;
- erklären, warum die Reparaturskripte nach dem gesamten Schema laufen (Phase B), mit einer Ausnahme;
- die Vorabprüfungen ausführen und sagen, was zu tun ist, wenn sie Zeilen zurückgeben;
- mit `exp:checkdbfiles` prüfen, ob der Baum der Update-Dateien vollständig ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 14.5](../../../../installation/14-migration-von-4x.md#145-die-datenbank-schritt-für-schritt).

1. **Zuerst Zeichensatz und Tabellentyp** (4 Minuten). Unter MySQL: InnoDB mit `ezconvertmysqltabletype.php`, UTF-8
   mit `ezconvertdbcharset.php`; setzen Sie den Standardzeichensatz der Datenbank auf den der umgewandelten Tabellen,
   bevor die Update-Dateien neue Tabellen anlegen (14.5.2).
2. **Vorabprüfung** (5 Minuten). Drei Anweisungen der alten Kette scheitern an Daten, die ältere Versionen zuließen:
   doppelte Remote-IDs von Objekten, doppelte Digest-Adressen, Benutzer ohne `ezuser_setting`-Zeile. Jede Abfrage muss
   keine Zeilen zurückgeben, oder ihre Behebung wird eingeplant (14.5.3).
3. **Zwei Phasen** (6 Minuten). Die Reparaturskripte laufen auf dem 6.0-Kernel, dessen Definitionen der persistenten
   Objekte das aktuelle Schema beschreiben. Deshalb wendet Phase A jede SQL-Datei der Reihe nach an, und Phase B führt
   die Reparaturskripte gegen das fertige Schema aus. Die einzige Ausnahme: `updateimagesystem.php` läuft vor der
   4.1-Datei, und nur, wenn `ezimage` Zeilen hat (14.5.4).
4. **Phase A** (5 Minuten). Zeigen Sie die Tabelle aus 14.5.5: eine Datei pro Schritt, von der Datei nach Ihrer
   Version bis `6.0/dbupdate-6.0.0-6.0.15.sql`. Halten Sie beim ersten Fehler an (`mysql` hält von selbst an;
   `psql -v ON_ERROR_STOP=1`). Die Markierungen für Patch-Versionen `-- START: from 4.0.1` enthalten Anweisungen, die
   eine spätere Patch-Version bereits ausgeliefert hat.
5. **Phase B** (5 Minuten). Zeigen Sie die Tabelle aus 14.5.6. Zuerst Probeläufe; `addlockstategroup.php` genau
   einmal; `deduplicatecontentstategrouplanguage.php` ist für Websites, die 5.0 durchlaufen haben, Pflicht.

### Häufige Fragen

**Was, wenn eine Datei mittendrin scheitert?** Notieren Sie die Anweisung, spielen Sie die Sicherung ein, beheben Sie
die Ursache (meist ein Fall aus der Vorabprüfung) und beginnen Sie erneut ab der Sicherung (14.5.5). Führen Sie nie
eine Datei ein zweites Mal aus, um einen halb fertigen Lauf zu reparieren (14.14).

**Gibt es eine Kette für SQLite oder MongoDB?** Nein. Die Unterstützung für SQLite beginnt mit 6.0.1, die für MongoDB
mit 6.0.14: Migrieren Sie unter MySQL oder PostgreSQL; ein späterer Wechsel der Engine ist ein eigenes Projekt.

**Und Oracle?** Die Erweiterung `ezoracle` führt die Kette bis 5.3; die späteren Schritte werden von Hand aus den
MySQL-Dateien geschrieben. Kapitel 14.5.5 rät, diesen Weg doppelt so oft zu proben.

**Was bedeutet „Unknown column 'priority'“ aus einem Reparaturskript?** Ein Skript lief gegen ein Zwischenschema;
zuerst Phase A, dann Phase B (14.13).

### Stolperfallen

- **Alte Kopien von Update-Dateien.** Kopien von vor Oktober 2026 begannen mit `SET storage_engine`, was aktuelle
  MySQL- und MariaDB-Versionen ablehnen. Nehmen Sie die aktuellen Dateien.
- **Skripte und Dateien abwechselnd ausführen**, wie es die alten Seiten pro Version taten. Mit dem heutigen Code
  scheitert das.
- **Die Aufzeichnung der Zeiten überspringen.** Notieren Sie, wie lange jede Datei und jedes Skript gedauert hat; das
  Zeitfenster der Umstellung ergibt sich daraus.

## Praktische Übung

Teil 1 liest nur auf Ihrer Schulungsinstallation; Teil 2 ist eine Planungsübung.

1. Listen Sie im Wurzelverzeichnis der Installation die Update-Verzeichnisse für MySQL und ihre Dateien auf:

   ```bash
   ls update/database/mysql/
   ls update/database/mysql/4.1/ update/database/mysql/6.0/
   ```

2. Prüfen Sie, ob der Baum der Update-Dateien vollständig ist:

   ```bash
   php bin/php/console exp:checkdbfiles --no-verify-branches
   ```

3. Listen Sie die Reparaturskripte der 5.x-Schritte auf:

   ```bash
   ls update/common/scripts/5.0/ update/common/scripts/5.4/
   ```

4. Führen Sie die drei Vorabprüfungen aus [Kapitel 14.5.3](../../../../installation/14-migration-von-4x.md#1453-vorabprüfungsabfragen)
   mit `sqlite3 -readonly var/storage/sqlite3/exponential.db` gegen Ihre Schulungsdatenbank aus (die Digest-Tabelle
   existiert auf einer frischen Installation möglicherweise nicht; notieren Sie in diesem Fall die Meldung).
5. Planung: Eine alte Website läuft auf **4.3** unter MySQL 5.5 mit MyISAM-Tabellen in `latin1`, und
   `SELECT COUNT(*) FROM ezimage;` gibt 0 zurück. Schreiben Sie ihr Runbook:
   - die Schritte für Zeichensatz und Tabellentyp mit ihren Befehlen;
   - die Vorabprüfungen;
   - Phase A: jede SQL-Datei der Reihe nach, von der ersten nach 4.3 bis 6.0.15;
   - Phase B: jedes Reparaturskript der durchlaufenen Versionen der Reihe nach, mit den Probeläufen.
6. Markieren Sie die Schritte, bei denen der Lauf bei einem Fehler anhalten muss, und was Sie dann tun.

## Erwartetes Ergebnis

- Schritt 1 zeigt die Verzeichnisse `4.0` bis `5.4`, `6.0` und die historischen `6.12` und `7.3`; das Verzeichnis
  `6.0` enthält `dbupdate-5.4.0-6.0.0.sql` und `dbupdate-6.0.0-6.0.15.sql`.
- Schritt 2 gibt nichts aus, wenn der Baum vollständig ist.
- Schritt 4: Die Abfragen geben auf einer frischen Installation keine Zeilen zurück.
- Schritt 5: Phase A beginnt mit `4.4/dbupdate-4.3.0-to-4.4.0.sql` und endet mit `6.0/dbupdate-6.0.0-6.0.15.sql`;
  `updateimagesystem.php` entfällt, weil `ezimage` leer ist; Phase B beginnt mit `4.5/updatesectionidentifier.php`
  (die Kopien von 4.4 und 4.5 sind identisch) und enthält `deduplicatecontentstategrouplanguage.php` aus 5.0.
- Der Trainer prüft ein Runbook anhand der Tabellen aus 14.5.5 und 14.5.6.

## Quiz

1. Warum laufen die Reparaturskripte nach allen SQL-Dateien und nicht nach jeder einzelnen Datei?
2. Welches Skript muss, falls nötig, **vor** der 4.1-SQL-Datei laufen?
   A. `addlockstategroup.php` B. `updateimagesystem.php` C. `createaudittables.php` D. `fixorphanimages.php`
3. Was gibt `exp:checkdbfiles --no-verify-branches` aus, wenn der Baum der Update-Dateien vollständig ist?
4. Eine Vorabprüfung findet doppelte Remote-IDs von Objekten. Welcher Weg ist dokumentiert?
5. Richtig oder falsch: SQLite hat eine Update-Kette ab 4.x.

<details>
<summary>Antworten</summary>

1. Die Skripte laufen auf dem 6.0-Kernel, dessen Definitionen das aktuelle Schema beschreiben
   ([Kapitel 14.5.4](../../../../installation/14-migration-von-4x.md#1454-zwei-phasen-zuerst-das-schema-danach-die-datenreparatur)).
2. **B**, und nur, wenn `ezimage` Zeilen hat (Kapitel 14.5.4).
3. Nichts: Keine Ausgabe bedeutet, dass der Baum vollständig ist
   ([Kapitel 14.5.5](../../../../installation/14-migration-von-4x.md#1455-phase-a-die-sql-dateien)).
4. Die 4.1-Datei ohne die Zeile für den eindeutigen Index ausführen, die Kette abschließen,
   `fixobjectremoteid.php --mode=a` ausführen und dann den Index hinzufügen
   ([Kapitel 14.5.3](../../../../installation/14-migration-von-4x.md#1453-vorabprüfungsabfragen)).
5. **Falsch.** Die Unterstützung für SQLite beginnt mit 6.0.1; migrieren Sie unter MySQL oder PostgreSQL (Kapitel
   14.5.5).

</details>

## Weiterführende Informationen

- [Kapitel 14.5: Die Datenbank Schritt für Schritt](../../../../installation/14-migration-von-4x.md#145-die-datenbank-schritt-für-schritt):
  das vollständige Runbook mit jeder Datei und jedem Skript.
- [Kapitel 11: Aktualisieren](../../../../installation/11-aktualisieren.md): die Update-Kette und die Schritte für 6.0.x.
- [Kapitel 17.9: Werkzeuge für Migration und Upgrade](../../../../installation/17-migrationsreferenz.md#179-werkzeuge-für-migration-und-upgrade):
  jedes Skript mit seinen Optionen.

## Verwandte Seiten

- [Modul 4: Von der 4.x-Linie](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
