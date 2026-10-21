---
title: "Lektion 3.4: Datenbanksicherungen und der Wechsel zwischen Datenbanksystemen"
audience: trainer, hosting, admin
category: training
order: 34
summary: Die Teilnehmenden sichern und prüfen eine Datenbank mit dem richtigen Werkzeug für jedes Datenbanksystem, stellen eine SQLite-Datenbank über deren eigene Sperren wieder her und planen mit den Schema-Werkzeugen den Umzug einer Website auf ein anderes Datenbanksystem. 45 Minuten.
keywords: hosting und betrieb, datenbanksicherung, sqlite3 .backup, .restore, integrity_check, pg_dump, pg_restore, mariadb-dump, mysqldump, single-transaction, mongodump, datapump, ezsqldumpschema, ezsqlinsertschema, datenbanksystem wechseln, hosting and operations, database backup, switching engines
type: help
---
# Lektion 3.4: Datenbanksicherungen und der Wechsel zwischen Datenbanksystemen

Eine Datenbanksicherung, die sich nicht wiederherstellen lässt, ist keine Sicherung. Diese Lektion vermittelt den
Befehl für eine konsistente Sicherung bei jedem unterstützten Datenbanksystem, wie man eine Kopie prüft, wie man SQLite
sicher wiederherstellt, während die Website Prozesse geöffnet hält, und wie eine Website auf ein anderes
Datenbanksystem umzieht. Sicherungen der ganzen Website, Zeitpläne und Wiederherstellungstests folgen in Modul 6.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 3: Datenbanken](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 3.3](datenbankserver.md); eine Schulungsinstallation auf SQLite und das Programm `sqlite3` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Befehl zum Sichern und Wiederherstellen für jedes der sechs unterstützten Datenbanksysteme nennen;
- eine konsistente SQLite-Sicherung erstellen, während die Website läuft, und sie mit `PRAGMA integrity_check` prüfen;
- eine SQLite-Datenbank mit `.restore` im Wartungsmodus wiederherstellen und danach die Caches leeren;
- erklären, warum `--single-transaction` für Dumps von MariaDB und MySQL wichtig ist;
- die Schritte skizzieren, mit denen eine Website mit `ezsqldumpschema.php` und `ezsqlinsertschema.php` zwischen
  SQL-Datenbanksystemen umzieht.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Datenbanksicherungen pro Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md) und
[Kapitel 9, Abschnitt 9.9](../../../../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen).

1. **Die Tabelle** (4 Minuten). Zeigen Sie *Auf einen Blick* der Seite über Sicherungen: `sqlite3 .backup`,
   `pg_dump -Fc`, `mongodump`, `mariadb-dump --single-transaction`, den Befehl `datapump` der Erweiterung `ezoracle`
   oder `expdp`, `mysqldump --single-transaction`. Eine Datenbanksicherung ist nur die Hälfte einer Sicherung der
   Website.
2. **SQLite** (5 Minuten). Kopieren Sie nie eine Datenbankdatei, die verwendet wird. `.backup` verwendet die
   Online-Backup-API von SQLite; `VACUUM INTO` schreibt eine verdichtete Kopie. Prüfen Sie mit
   `PRAGMA integrity_check`, das `ok` ausgibt. Stellen Sie mit `.restore` im Wartungsmodus wieder her und leeren Sie
   danach die Kernel-Caches und den Antwort-Cache von Velocity: Sie enthalten noch Seiten von vorher.
3. **Server-Datenbanksysteme** (4 Minuten). `--single-transaction` liefert einen konsistenten Dump von InnoDB-Tabellen,
   ohne sie zu sperren. Prüfen Sie nach einer PostgreSQL-Wiederherstellung in eine frische Datenbank die Sequenzen.
   Legen Sie nach einer MongoDB-Wiederherstellung auf einem neuen Server die Indizes aus
   `bin/mongodb/create_indexes.js` an.
4. **Datenbanksystem wechseln** (7 Minuten). Planen Sie ein Wartungsfenster; zwischen dem Dump und der Umstellung darf
   kein Redakteur veröffentlichen. Erstellen Sie mit `ezsqldumpschema.php` einen Dump im systemneutralen Format `.dba`,
   laden Sie ihn mit `ezsqlinsertschema.php`, setzen Sie `DatabaseImplementation` und die Verbindungsschlüssel, leeren
   Sie alle Caches und starten Sie die Worker neu. Zählen Sie auf beiden Seiten die Zeilen großer Tabellen und
   veröffentlichen Sie dann ein Testobjekt. Spätere Schema-Updates richten sich nach dem Datenbanksystem, auf dem die
   Website jetzt läuft.

### Häufige Fragen

**Die Wiederherstellung hat nichts ausgegeben. Hat sie funktioniert?** `.restore` gibt bei Erfolg nichts aus. Meldet es
einen Fehler, ist die laufende Datenbank unverändert; stoppen Sie dann jeden Prozess, der die Datei geöffnet haben
könnte, und ersetzen Sie die Datei, wobei Sie ihre Dateien `-wal` und `-shm` gemeinsam beiseitelegen.

**Bietet PostgreSQL eine Wiederherstellung auf einen Zeitpunkt zwischen zwei Dumps?** Nur mit seiner eigenen
Replikation und Point-in-Time-Recovery, eingerichtet nach der PostgreSQL-Dokumentation.

**Wer sichert eine Oracle-Website?** Stimmen Sie sich mit dem Datenbankadministrator des Kunden ab; die meisten
Oracle-Websites haben bereits ein Sicherungskonzept.

**Warum zeigen die Schema-Werkzeuge mein Passwort?** `--password=` ist in der Prozessliste für andere Benutzer des
Rechners sichtbar. Führen Sie die Übertragung auf einem Rechner aus, den Sie kontrollieren, oder mit einem temporären
Passwort, das Sie danach ändern.

### Stolperfallen

- **Sicherungen nur auf derselben Festplatte.** Die SQLite-Sicherung braucht zuerst lokalen Platz, aber eine Kopie,
  die den Rechner nie verlässt, geht mit ihm verloren (Modul 6).
- **Die Caches nach einer Wiederherstellung vergessen.** Seiten, die aus dem alten Inhalt dargestellt wurden,
  erscheinen weiter; es sieht aus, als sei die Wiederherstellung fehlgeschlagen.
- **Ein vergessenes `DatabaseImplementation`** ist der übliche Grund, warum nach einem Wechsel des Datenbanksystems
  keine Seite lädt.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation auf SQLite. Ersetzen Sie `exponential.db` durch Ihren Dateinamen.
Bewahren Sie die Sicherung für die Übung im Verzeichnis `var/` der Installation auf.

1. Legen Sie ein Sicherungsverzeichnis an: `mkdir -p var/training-backup`.
2. Erstellen Sie eine Online-Sicherung, während die Website läuft:

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db ".backup 'var/training-backup/exponential-$(date +%F).db'"
   ```

3. Prüfen Sie sie: `sqlite3 var/training-backup/exponential-<date>.db "PRAGMA integrity_check"`.
4. Benennen Sie in der Administration einen Ordner in Ihrem Kursbereich um (hängen Sie zum Beispiel ` (changed)` an
   seinen Namen an) und veröffentlichen Sie.
5. Schalten Sie den Wartungsmodus ein: `php bin/php/maintenance.php on --message="Restoring"`.
6. Stellen Sie die Sicherung wieder her:

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db ".restore 'var/training-backup/exponential-<date>.db'"
   ```

7. Schalten Sie den Wartungsmodus aus: `php bin/php/maintenance.php off`.
8. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-all
   php bin/php/console exp:velocity cache clear
   ```

9. Öffnen Sie den Ordner in der Administration und prüfen Sie seinen Namen.
10. Lesen Sie die Hilfe des Schema-Dump-Werkzeugs und notieren Sie die Werte für `--type`, die es auflistet:
    `php bin/php/ezsqldumpschema.php --help`.

## Erwartetes Ergebnis

- Schritt 3 gibt `ok` aus.
- Solange der Wartungsmodus eingeschaltet war, zeigte die Website die Wartungsseite.
- Schritt 9: Der Ordner hat seinen Namen von vor Schritt 4; die Änderung ist weg, weil die Sicherung älter war.
- Die Person kann die Typen nennen, die die Schema-Werkzeuge annehmen, und weiß, dass `settings/dbschema.ini` mehr
  auflistet als die Ausgabe von `--help`.
- Der Trainer prüft den Ordnernamen einer Person vor und nach der Wiederherstellung am Beamer.

## Quiz

1. Warum dürfen Sie nie eine SQLite-Datenbankdatei kopieren, die verwendet wird?
2. Welche Option macht einen Dump von MariaDB oder MySQL konsistent, ohne InnoDB-Tabellen zu sperren?
   A. `--lock-all-tables` B. `--single-transaction` C. `--quick` D. `--no-data`
3. Nach der Wiederherstellung einer Datenbank zeigt die Website noch die alten Seiten. Was wurde vergessen?
4. Was muss nach der Wiederherstellung von MongoDB auf einem neuen Server angelegt werden?
5. Nennen Sie die zwei Skripte, mit denen eine Website zwischen SQL-Datenbanksystemen umzieht.

<details>
<summary>Antworten</summary>

1. Eine Kopie, die während eines Schreibvorgangs oder ohne ihre `-wal`-Datei entsteht, kann inkonsistent sein;
   verwenden Sie `.backup` oder `VACUUM INTO`.
2. **B.** Sie liefert einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu sperren.
3. Das Leeren der Caches: Die Kernel-Caches und der Antwort-Cache von Velocity enthielten noch Seiten von vorher.
4. Die Indizes aus `bin/mongodb/create_indexes.js`
   ([Datenbanksicherungen pro Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md#3-mongodb)).
5. `bin/php/ezsqldumpschema.php` und `bin/php/ezsqlinsertschema.php`.

</details>

## Weiterführende Informationen

- [Datenbanksicherungen pro Datenbanksystem](../../../hosting/betrieb/datenbank-backups-je-engine.md): jeder Befehl.
- [Das Datenbanksystem wechseln](../../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md): das Verfahren für
  Hoster.
- [Kapitel 9, SQLite sichern und wiederherstellen](../../../../installation/09-datenbanken.md#sqlite-sicherung-und--wiederherstellung): die
  Einzelheiten.
- [Datenbankunterstützung, Sichern und Wiederherstellen](../../../datenbankunterstuetzung.md#backup-und-wiederherstellung): der
  Überblick.

## Verwandte Seiten

- [Modul 3: Datenbanken](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
