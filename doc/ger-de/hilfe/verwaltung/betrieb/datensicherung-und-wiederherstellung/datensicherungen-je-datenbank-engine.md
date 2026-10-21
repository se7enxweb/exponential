---
title: Datensicherung je Datenbank-Engine
audience: government, hosting, admin
category: public-sector
order: 30
summary: Für Datenbank- und Betriebsteams in öffentlichen Stellen. Der Sicherungs- und Wiederherstellungsbefehl jeder Datenbank, die Exponential unterstützt, in der offiziellen Reihenfolge, wie Sie eine Kopie prüfen und wie Sie die Datensicherung der Engine in die Werkzeuge einbinden, die Ihr Datenbankteam bereits betreibt.
keywords: Datenbanksicherung, SQLite .backup, pg_dump, pg_restore, mongodump, mariadb-dump, mysqldump, expdp, Data Pump, ezoracle, Integritätsprüfung, Wiederherstellung, Datenbankteam, Unternehmens-Sicherungssystem, database backup, datapump, integrity check, restore, database team, enterprise backup
type: help
---
# Datensicherung je Datenbank-Engine

Diese Seite richtet sich an Datenbankadministratorinnen und -administratoren sowie Betriebsteams in öffentlichen
Stellen, die die Datenbankhälfte einer Exponential-Website sichern. Sie nennt den Sicherungs- und
Wiederherstellungsbefehl jeder unterstützten Engine, wie Sie die Kopie prüfen und wie die Werkzeuge der Engine in ein
bestehendes Sicherungssystem der Organisation passen. Die vollständige Hosting-Seite ist
[Datenbanksicherungen je Engine](../../../hosting/betrieb/datenbank-backups-je-engine.md).

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich.

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [SQLite erfordert Sorgfalt](#sqlite-erfordert-sorgfalt)
3. [Server-Engines](#server-engines)
4. [Einbindung in Ihr Sicherungssystem](#einbindung-in-ihr-sicherungssystem)
5. [Passwörter auf der Kommandozeile](#passwörter-auf-der-kommandozeile)
6. [Checkliste](#checkliste)

## Auf einen Blick

| Datenbank | Datensicherung | Wiederherstellung |
|---|---|---|
| 1. SQLite 3 | `sqlite3 <db file> ".backup '/backup/exponential.db'"` | `sqlite3 <db file> ".restore '/backup/exponential.db'"` |
| 2. PostgreSQL | `pg_dump -U USER -Fc DATABASE > site.dump` | `pg_restore -U USER -d DATABASE --clean site.dump` |
| 3. MongoDB | `mongodump --uri="mongodb://USER@HOST:27017/DATABASE" --out=/backup/site` | `mongorestore --uri="..." --drop /backup/site` |
| 4. MariaDB | `mariadb-dump --single-transaction -u USER -p DATABASE > site.sql` | `mariadb -u USER -p DATABASE < site.sql` |
| 5. Oracle | `php bin/php/console ext:ezoracle:datapump` (siehe dessen `--help`) oder Oracles `expdp` | Oracles `impdp` |
| 6. MySQL | `mysqldump --single-transaction -u USER -p DATABASE > site.sql` | `mysql -u USER -p DATABASE < site.sql` |

Welche Engine eine Website verwendet, finden Sie mit `grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php` heraus.

## SQLite erfordert Sorgfalt

Die gesamte Datenbank ist eine Datei unter `var/storage/sqlite3/` (`exponential.db` ist der Standardname; `ls
var/storage/sqlite3/` zeigt Ihren), daneben liegen `-wal`- und `-shm`-Dateien. „Kopieren Sie nie eine Datenbankdatei,
die in Gebrauch ist“ ([9.2 SQLite sichern und wiederherstellen](../../../../installation/09-datenbanken.md#sqlite-sicherung-und--wiederherstellung)).
Verwenden Sie die Online-Sicherung und prüfen Sie die Kopie:

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential-$(date +%F).db'"
sqlite3 /backup/exponential-<date>.db "PRAGMA integrity_check"     # prints ok
```

Ein Sicherungsagent auf Dateiebene, der `var/` kopiert, während die Website läuft, erzeugt für sich allein keine
brauchbare SQLite-Sicherung. Führen Sie zuerst den Befehl `.backup` aus, in ein Verzeichnis, das der Agent dann
kopiert. Halten Sie freien Festplattenplatz für „die Datenbank in doppelter Größe“ bereit.

## Server-Engines

- **PostgreSQL**: `pg_dump` erstellt einen konsistenten Schnappschuss, während die Website läuft; PostgreSQL hat eigene
  Replikation und zeitpunktgenaue Wiederherstellung, falls Ihr Wiederherstellungspunkt mehr als nächtliche Dumps
  verlangt. Prüfen Sie nach einer Wiederherstellung in eine neue Datenbank die Sequenzen mit dem Namen
  `<table>_<column>_seq` ([9.4](../../../../installation/09-datenbanken.md#sequenzen)).
- **MariaDB und MySQL**: `--single-transaction` liefert einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu
  sperren; stellen Sie in eine Datenbank wieder her, die in `utf8mb4` angelegt wurde.
- **MongoDB**: Legen Sie nach einer Wiederherstellung auf einem neuen Server die Indizes aus
  `bin/mongodb/create_indexes.js` an.
- **Oracle**: `ext:ezoracle:datapump` oder die Data-Pump-Jobs Ihrer Datenbankadministration.

## Einbindung in Ihr Sicherungssystem

Die meisten öffentlichen Stellen haben ein Unternehmens-Sicherungssystem und ein Datenbankteam mit eigenen Verfahren.
Praktische Punkte:

| Punkt | Empfehlung |
|---|---|
| Wer für die Datenbanksicherung verantwortlich ist | das Datenbankteam bei Server-Engines; das Anwendungsteam bei SQLite, das keinen Server hat |
| Derselbe Zeitpunkt wie bei den Dateien | planen Sie den Dump und die Dateisicherung im selben Zeitfenster ([Was zu sichern ist](was-gesichert-werden-muss.md)) |
| Aufbewahrung | stimmen Sie sie mit der Schriftgutverwaltung ab ([Sicherungspläne und Aufbewahrung](sicherungsplaene-und-aufbewahrung.md)) |
| Nachweis | ein Wiederherstellungstest je Engine, nicht nur ein erfolgreiches Job-Log ([Wiederherstellung testen](wiederherstellungstests.md)) |
| Vor riskanten Änderungen | ein zusätzlicher Dump vor Aktualisierungen, Engine-Wechseln und Wiederherstellungen |

## Passwörter auf der Kommandozeile

`-p` lässt die MySQL- und MariaDB-Clients nach dem Passwort fragen; schreiben Sie das Passwort selbst nie auf die
Kommandozeile, wo es im Shell-Verlauf und in der Prozessliste landet
([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)). Verwenden Sie für planmäßige Jobs die
Optionsdatei des Clients oder Ihren Geheimnisspeicher, nur für den Sicherungsbenutzer lesbar.

## Checkliste

- [ ] Der Sicherungsbefehl der Engine der Website ist eingeplant, und ein benanntes Team ist dafür verantwortlich.
- [ ] SQLite wird mit `.backup` gesichert, nie durch Kopieren der Live-Datei.
- [ ] Die Kopie wird geprüft (`PRAGMA integrity_check`, eine Testwiederherstellung).
- [ ] Kein Passwort erscheint auf einer Kommandozeile oder in einer Crontab.
- [ ] Der Dump und die Dateisicherung werden im selben Zeitfenster erstellt.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Eine Datenbank in der offiziellen Reihenfolge wählen](../hosting/datenbankwahl-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung, Sicherung und Wiederherstellung](../../../datenbankunterstuetzung.md#backup-und-wiederherstellung)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
