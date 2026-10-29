---
title: Datenbank-Backups je Engine
audience: hosting, admin
category: hosting
order: 70
summary: Die Befehle für Backup, Prüfung und Wiederherstellung für jede Datenbank, die Exponential unterstützt, in der offiziellen Reihenfolge (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), mit den Fallen jeder Engine und einem portablen Schema-Dump für den Umzug zwischen SQL-Engines.
keywords: Datenbank-Backup, SQLite-Backup, VACUUM INTO, pg_dump, pg_restore, mongodump, mongorestore, mariadb-dump, mysqldump, expdp, impdp, datapump, Wiederherstellung, integrity_check, single-transaction, database backup, SQLite backup, restore
type: help
---
# Datenbank-Backups je Engine

Diese Seite ist für Systemadministratoren, die die Datenbanken gehosteter Exponential-Websites sichern und wiederherstellen.
Sie beantwortet: welcher Befehl jede unterstützte Datenbank konsistent sichert, während die Website läuft, wie Sie die Kopie
prüfen, wie Sie sie wiederherstellen und worauf Sie bei jeder Engine achten müssen.

Die Befehle sind die aus [Datenbankunterstützung, Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung),
[10.9 Backups und Wiederherstellung](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) und den Abschnitten zu den
einzelnen Engines in [Kapitel 9](../../../installation/09-datenbanken.md). Benutzer- und Datenbanknamen unten sind Platzhalter
(`exponential`); verwenden Sie Ihre eigenen.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlicher Arbeit möglich (in 6.0.x gibt es keinen Treiber). Siehe [Datenbankunterstützung](../../datenbankunterstuetzung.md)
> und [Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md).

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [1. SQLite 3](#1-sqlite-3)
3. [2. PostgreSQL](#2-postgresql)
4. [3. MongoDB](#3-mongodb)
5. [4. MariaDB](#4-mariadb)
6. [5. Oracle](#5-oracle)
7. [6. MySQL](#6-mysql)
8. [Microsoft SQL Server](#microsoft-sql-server)
9. [Passwörter und Berechtigungen](#passwörter-und-berechtigungen)
10. [Ein portabler Dump zwischen SQL-Engines](#ein-portabler-dump-zwischen-sql-engines)
11. [Checkliste](#checkliste)

## Auf einen Blick

| Datenbank | Backup | Wiederherstellung |
|---|---|---|
| 1. SQLite 3 | `sqlite3 <db file> ".backup '/backup/exponential.db'"` | `sqlite3 <db file> ".restore '/backup/exponential.db'"` |
| 2. PostgreSQL | `pg_dump -U exponential -Fc exponential > exponential.dump` | `pg_restore -U exponential -d exponential --clean exponential.dump` |
| 3. MongoDB | `mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/exponential` | `mongorestore --uri="..." --drop /backup/exponential` |
| 4. MariaDB | `mariadb-dump --single-transaction -u exponential -p exponential > exponential.sql` | `mariadb -u exponential -p exponential < exponential.sql` |
| 5. Oracle | `php bin/php/console ext:ezoracle:datapump` (siehe sein `--help`) oder Oracles `expdp` | Oracles `impdp` |
| 6. MySQL | `mysqldump --single-transaction -u exponential -p exponential > exponential.sql` | `mysql -u exponential -p exponential < exponential.sql` |

Ein Datenbank-Backup ist nur die Hälfte eines Backups der Website; sichern Sie die Dateien im selben Fenster
([Was gesichert werden muss](backups-was-gesichert-werden-muss.md)).

## 1. SQLite 3

Die ganze Datenbank ist eine Datei, standardmäßig unter `var/storage/sqlite3/` (`exponential.db` ist der Name, den
`exp:install` verwendet; `ls var/storage/sqlite3/` zeigt Ihren). Daneben liegen `<database>-wal` und `<database>-shm`, die zu
ihr gehören.

**Sichern Sie mit dem Online-Backup, nie durch Kopieren.** Kopieren Sie nie eine Datenbankdatei, die in Benutzung ist: Eine
Kopie, die während eines Schreibvorgangs oder ohne ihre Datei `-wal` erstellt wird, kann inkonsistent sein
([SQLite: Backup und Wiederherstellung](../../../installation/09-datenbanken.md#sqlite-sicherung-und--wiederherstellung)).

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential-$(date +%F).db'"
# or a compacted copy (SQLite 3.27 and later)
sqlite3 var/storage/sqlite3/exponential.db "VACUUM INTO '/backup/exponential-$(date +%F).db'"
# check the copy: prints ok
sqlite3 /backup/exponential-<date>.db "PRAGMA integrity_check"
```

**Stellen Sie über die eigenen Sperren von SQLite wieder her.** PHP-FPM-Worker und Velocity-Worker halten die Datenbank offen;
`.restore` kopiert das Backup daher Seite für Seite unter einer Schreibsperre hinein:

```bash
php bin/php/maintenance.php on --message="Restoring" --allow-root-user
sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential-<date>.db'"
php bin/php/maintenance.php off --allow-root-user
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/console exp:velocity cache clear --apply --allow-root-user
```

Wenn `.restore` einen Fehler meldet, ist die Live-Datenbank unverändert; halten Sie dann jeden Prozess an, der die Datei halten
könnte (Velocity, PHP-FPM, Cron), verschieben Sie die alte Datenbank mit ihren Dateien `-wal` und `-shm` gemeinsam zur Seite,
legen Sie das Backup an ihre Stelle, korrigieren Sie Eigentümer und Modus und starten Sie wieder.

> **Hinweis**
> Ein SQLite-Backup braucht zuerst Speicherplatz auf derselben Maschine. Halten Sie Platz für die doppelte Größe der Datenbank
> für `VACUUM` und Backups frei ([Eine SQLite-Website überwachen](../../../installation/09-datenbanken.md#überwachung-einer-sqlite-site)).

## 2. PostgreSQL

```bash
pg_dump -U exponential -Fc exponential > exponential.dump          # custom format, compressed
pg_restore -U exponential -d exponential --clean exponential.dump
```

`pg_dump` erstellt einen konsistenten Snapshot, während die Website läuft. Die Quellen beschreiben PostgreSQL mit eigener
Replikation und eigener Wiederherstellung zu einem Zeitpunkt; richten Sie diese mit der Dokumentation von PostgreSQL ein, wenn
ein Tarif die Wiederherstellung zu einem Moment zwischen zwei Dumps verspricht. Prüfen Sie nach der Wiederherstellung in eine
frische Datenbank die Sequenzen: Der Kernel liest neue IDs aus Sequenzen mit dem Namen `<table>_<column>_seq`
([Sequenzen](../../../installation/09-datenbanken.md#sequenzen)).

## 3. MongoDB

```bash
mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/$(date +%F)
mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/<date>
```

Das Werkzeug ist unter [mongodump](https://www.mongodb.com/docs/database-tools/mongodump/) beschrieben. Legen Sie nach einer
Wiederherstellung auf einem neuen Server die Indizes aus `bin/mongodb/create_indexes.js` an; ohne sie durchsuchen Abfragen von
Teilbäumen und das Nachschlagen von URL-Aliasen ganze Collections
([Leistung und Skalierung](../../datenbankunterstuetzung.md#performance-und-skalierung)). Lesen Sie die
[Grenzen von MongoDB](../../datenbankunterstuetzung.md#grenzen-von-mongodb), bevor Sie es anbieten.

## 4. MariaDB

```bash
mariadb-dump --single-transaction -u exponential -p exponential > exponential.sql
mariadb -u exponential -p exponential < exponential.sql
```

`--single-transaction` ergibt einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu sperren. MariaDB läuft mit demselben
Treiber wie MySQL; der Installer legt die Tabellen als InnoDB an, wenn der Server es anbietet, und die Datenbank sollte in
`utf8mb4` angelegt werden ([Zeichensatz und Speicher-Engine](../../../installation/09-datenbanken.md#zeichensatz-und-speicher-engine)).
Stellen Sie in eine Datenbank wieder her, die genauso angelegt wurde, in `utf8mb4`.

## 5. Oracle

Oracle (19c und später) wird über die Erweiterung `ezoracle` unterstützt. Ihr Konsolenbefehl `datapump` umschließt Oracle Data
Pump; der andere Weg sind Oracles eigene `expdp` und `impdp`:

```bash
php bin/php/console ext:ezoracle:datapump --help
```

`ezoracle` liefert außerdem die Befehle `health`, `gather-stats`, `recompile`, `purge-recyclebin`, `sequence-sync` und andere mit,
sowie die Cronjob-Teile `ezoracle` (nächtlich) und `ezoraclehealth` (stündlich), die nichts tun, bis sie in
`ezoracle.ini [CronjobSettings]` aktiviert werden ([9.6 Wartung](../../../installation/09-datenbanken.md#wartung)). Stimmen Sie
Oracle-Backups mit dem Datenbankadministrator des Kunden ab; die meisten Oracle-Websites haben bereits ein Backup-Verfahren.

## 6. MySQL

```bash
mysqldump --single-transaction -u exponential -p exponential > exponential.sql
mysql -u exponential -p exponential < exponential.sql
```

Es gelten dieselben Hinweise wie für MariaDB ([mysqldump](https://dev.mysql.com/doc/refman/8.4/en/mysqldump.html)). MySQL steht
in der Reihenfolge zuletzt, nicht weil es weniger gut funktioniert, sondern weil das Projekt für neue Websites zuerst die
anderen empfiehlt.

## Microsoft SQL Server

In Exponential 6.0.x gibt es keinen Treiber, also gibt es über Exponential nichts zu sichern. Es ist derzeit bekanntermaßen mit
zusätzlicher Arbeit möglich; siehe
[Microsoft SQL Server: mit zusätzlicher Arbeit möglich](../../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)
und [Stand von Microsoft SQL Server](../grundlagen/stand-von-microsoft-sql-server.md).

## Passwörter und Berechtigungen

- `-p` lässt den Client nach dem Passwort fragen; schreiben Sie es nie auf die Kommandozeile, wo es in der Shell-Historie
  landet und für andere Benutzer der Maschine sichtbar ist. Für unbeaufsichtigte Jobs nutzen Sie die Optionsdatei oder die
  Umgebung des Clients, nur für den Backup-Benutzer lesbar.
- Führen Sie Backups als Benutzer aus, der die Datenbank lesen darf, aber die Dateien der Website nicht ändern kann.
- Speichern Sie Dumps mit restriktiven Berechtigungen; sie enthalten die Daten jedes Benutzerkontos.

## Ein portabler Dump zwischen SQL-Engines

Um eine Website zwischen SQL-Engines umzuziehen (SQLite, PostgreSQL, MariaDB, MySQL und Oracle mit `ezoracle`), erstellen Sie
mit `bin/php/ezsqldumpschema.php --format=generic --output-array --output-types=all` einen Dump in eine `.dba`-Datei und laden
ihn mit `bin/php/ezsqlinsertschema.php --insert-types=all`, wobei Sie die Engine mit `--type` wählen
([Eine Website auf eine andere Datenbank umziehen](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)). Das ist ein
Werkzeug für Migrationen, kein Ersatz für das eigene Backup der Engine.

## Checkliste

- [ ] Der Backup-Befehl für die Engine der Website ist der oben genannte, mit `--single-transaction` für MariaDB und MySQL.
- [ ] SQLite-Dateien werden nie kopiert, während sie in Benutzung sind; `.backup` oder `VACUUM INTO` wird genutzt und die Kopie geprüft.
- [ ] Kein Passwort steht auf einer Kommandozeile oder in einer Crontab-Zeile.
- [ ] Wiederherstellungen leeren danach jeden Cache und auch den Antwort-Cache von Velocity.
- [ ] Wiederherstellungen von MongoDB legen die Indizes neu an; Wiederherstellungen von PostgreSQL haben korrekte Sequenzen.
- [ ] Oracle-Backups sind mit dem Datenbankadministrator des Kunden abgestimmt.

## Verwandte Seiten

- [Was gesichert werden muss](backups-was-gesichert-werden-muss.md)
- [Wiederherstellung und Wiederherstellungstests](wiederherstellung-und-wiederherstellungstests.md)
- [SQLite für Hoster](../grundlagen/sqlite-fuer-hoster.md), [PostgreSQL für Hoster](../grundlagen/postgresql-fuer-hoster.md),
  [MongoDB für Hoster](../grundlagen/mongodb-fuer-hoster.md), [MariaDB für Hoster](../grundlagen/mariadb-fuer-hoster.md),
  [Oracle für Hoster](../grundlagen/oracle-fuer-hoster.md), [MySQL für Hoster](../grundlagen/mysql-fuer-hoster.md)
- [Die Datenbank-Engine wechseln](../migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md), [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
