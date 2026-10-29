---
title: Datenbankunterstützung
audience: admin, developer
category: install-upgrade
order: 10
summary: Welche Datenbanken Exponential 6.0.x unterstützt und in welcher Reihenfolge (zuerst SQLite, dann PostgreSQL, MongoDB, MariaDB, Oracle und MySQL), jeweils mit Versionen, PHP-Erweiterungen, Einstellungen, Installern, Zeichensätzen, Performance, Backup und Wiederherstellung, Grenzen, Migration und Testabdeckung, und was nötig wäre, um auf Microsoft SQL Server zu laufen.
keywords: Datenbank, Datenbanken, unterstützte Datenbanken, Datenbanktreiber, Sicherung, Wiederherstellung, Migration, Zeichensatz, Anforderungen, Versionen, Testabdeckung, SQLite, SQLite3, PostgreSQL, Postgres, MongoDB, MariaDB, MySQL, Oracle, ezoracle, oci8, pgsql, mysqli, sqlite3, mongodb, Microsoft SQL Server, MSSQL, SQL Server, sqlsrv, xrowmssql, xrow, FreeTDS, dblib, DatabaseImplementation, DatabaseSettings, site.ini, database, databases, supported databases, backup, restore, migration, character set, utf8mb4, AL32UTF8, CI, requirements, versions
featured: true
context: setup/info, setup/systemupgrade, dse
type: help
---
# Datenbankunterstützung

Diese Seite richtet sich an Administratoren, die die Datenbank einer Exponential-Site auswählen, installieren und
betreiben, und an Entwickler, deren Code auf jeder Datenbank funktionieren muss. Sie beantwortet vier Fragen an einer
Stelle: welche Datenbanken Exponential 6.0.x unterstützt, in welcher Reihenfolge sie empfohlen werden, was jede von
ihnen braucht und wie gründlich jede getestet wird. Jede Angabe auf dieser Seite wurde am Code und an der Continuous
Integration der Linie 6.0.15 überprüft; wo sich etwas nicht überprüfen ließ, sagt die Seite das.

Das Schritt-für-Schritt-Kapitel für den Betrieb, in dem jede Einstellung erklärt wird, ist
[Kapitel 9 der Installationsanleitung, Datenbanken](../installation/09-datenbanken.md). Diese Seite ist der Überblick und die
Aussage zur Unterstützung; überall, wo es um Einzelheiten geht, verweist sie auf dieses Kapitel.

## Inhalt

1. [Die unterstützten Datenbanken in ihrer Reihenfolge](#die-unterstützten-datenbanken-in-ihrer-reihenfolge)
2. [Auf einen Blick](#auf-einen-blick)
3. [Versionen, am Code überprüft](#versionen-am-code-überprüft)
4. [PHP-Erweiterungen](#php-erweiterungen)
5. [Die Einstellungen: DatabaseSettings in site.ini](#die-einstellungen-databasesettings-in-siteini)
6. [Unterstützung durch die Installer](#unterstützung-durch-die-installer)
7. [SQLite 3, der Standard](#1-sqlite-3-der-standard)
8. [PostgreSQL](#2-postgresql)
9. [MongoDB](#3-mongodb)
10. [MariaDB](#4-mariadb)
11. [Oracle](#5-oracle)
12. [MySQL](#6-mysql)
13. [Microsoft SQL Server: möglich mit zusätzlichem Aufwand](#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)
    - [Ein Ausgangspunkt: die Erweiterung xrowmssql](#ein-ausgangspunkt-die-erweiterung-xrowmssql)
14. [Zeichensätze und Kollationen](#zeichensätze-und-kollationen)
15. [Performance und Skalierung](#performance-und-skalierung)
16. [Backup und Wiederherstellung](#backup-und-wiederherstellung)
17. [Eine Site auf eine andere Datenbank umziehen](#eine-site-auf-eine-andere-datenbank-umziehen)
18. [Testabdeckung (CI)](#testabdeckung-ci)
19. [Welche Datenbank verwendet meine Site?](#welche-datenbank-verwendet-meine-site)
20. [Verwandte Seiten](#verwandte-seiten)

## Die unterstützten Datenbanken in ihrer Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x) in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit nachweislich mit
> zusätzlichem Aufwand möglich.

Dies ist die offizielle Reihenfolge des Projekts für Exponential 6.0.x. In dieser Reihenfolge werden die Datenbanken
empfohlen, dokumentiert und in der gesamten Dokumentation aufgeführt. Alle sechs werden unterstützt: Jede von ihnen
hat einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen Eintrag im Setup-Assistenten und einen Weg
in den Installern für die Kommandozeile.

Was die Reihenfolge in der Praxis bedeutet:

1. **SQLite 3** ist der Standard. Der Setup-Assistent wählt es vor, `exp:install` verwendet es, wenn kein `--db`
   angegeben ist, und die Vorlage des Kickstarters nennt es zuerst. Es braucht keinen Datenbankserver: Das gesamte
   Content-Repository ist eine einzige Datei. Mit dem Treiber, den Exponential mitliefert, ist es eine vollwertige
   Produktionsdatenbank für eine Site, die von einem Rechner ausgeliefert wird.
2. **PostgreSQL** ist der empfohlene Datenbankserver: striktes SQL, Sperren auf Zeilenebene, eigene Replikation und
   Point-in-Time-Recovery, und es wird bei jedem Push in der CI getestet.
3. **MongoDB** wird über einen Adapter unterstützt, der die SQL-artigen Aufrufe des Kernels in MongoDB-Operationen
   übersetzt. Es ist die Wahl für Organisationen, die auf MongoDB standardisiert haben; lesen Sie seine
   [Grenzen](#grenzen-von-mongodb), bevor Sie sich dafür entscheiden.
4. **MariaDB** läuft auf demselben Treiber wie MySQL (`mysqli`). Es ist der MySQL-kompatible Server, den das Projekt
   bevorzugt.
5. **Oracle** (19c und neuer) wird über die Erweiterung `ezoracle` unterstützt, die `composer.json` voraussetzt; sie
   ist deshalb in jeder Installation vorhanden.
6. **MySQL** wird auf demselben Treiber wie MariaDB unterstützt und in der CI getestet. Es steht am Ende der
   Reihenfolge, nicht weil es schlechter funktioniert, sondern weil das Projekt für neue Sites zuerst die anderen
   empfiehlt.

Für Microsoft SQL Server gibt es in Exponential 6.0.x keinen Treiber. Er ist nachweislich möglich: Die
Datenbankschicht ist eine kleine, dokumentierte Schnittstelle, und alles, was geschrieben werden müsste, ist unter
[Microsoft SQL Server](#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand) aufgeführt. Eine für die Linie 4.x
geschriebene Erweiterung, `se7enxweb/xrowmssql`, ist der naheliegende Ausgangspunkt für diese Arbeit.

## Auf einen Blick

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` |
| Treiberklasse | `eZSQLite3DB` | `eZPostgreSQLDB` | `expMongoDB` | `eZMySQLiDB` | `eZOracleDB` | `eZMySQLiDB` |
| PHP-Erweiterung | `sqlite3` | `pgsql` | `mongodb` + Bibliothek `mongodb/mongodb` | `mysqli` | `oci8` | `mysqli` |
| Datenbankserver | keiner (eine Datei) | ja | ja | ja | ja | ja |
| Standard der Installer | ja | | | | | |
| Setup-Assistent | ja | ja | ja (langsamer Schritt) | ja | ja | ja |
| `exp:install --db=` | `sqlite` (Standard) | `pgsql` | `mongodb` | `mariadb` | `oracle` | `mysql` |
| Mehrere Webserver an einer Datenbank | nein | ja | ja | ja | ja | ja |
| Zeichensatz | UTF-8 | `UTF8` | UTF-8 (BSON) | `utf8mb4` | `AL32UTF8` | `utf8mb4` |
| Dateien für Schema-Updates | `update/database/sqlite/` | `update/database/postgresql/` | keine (kein SQL) | `update/database/mysql/` | in `ezoracle` | `update/database/mysql/` |
| In der CI des Projekts getestet | Unit-Tests des Treibers | ja (PostgreSQL 15) | Unit-Tests des Adapters | nein | nein | ja (MySQL 8.0) |

## Versionen, am Code überprüft

Für jede Datenbank gibt es drei verschiedene Zahlen, die man leicht verwechselt:

- **Untergrenze des Assistenten**: die älteste Serverversion, die der Setup-Assistent akzeptiert, aus
  `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php`. Sie ist eine technische Untergrenze aus früheren
  Releases, keine Empfehlung.
- **Praktisches Minimum**: die älteste Version, die die aktuellen Treiber tatsächlich brauchen, wie es der Treibercode
  und die Treiberdokumentation angeben.
- **Im Einsatz gesehen**: die Versionen, auf denen das Projekt seinen Entwicklungsserver und seine CI betreibt.

| Datenbank | Untergrenze des Assistenten | Praktisches Minimum | Im Einsatz gesehen |
|---|---|---|---|
| SQLite 3 | 3.0.1 | **3.33** (der Treiber schreibt MySQLs `UPDATE ... JOIN` in `UPDATE ... FROM` um, das SQLite ab 3.33 kennt; `eZSQLite3DB::rewriteJoinedUpdate()`) | Bibliothek 3.34.1 in PHP 8.5 auf dem Entwicklungsserver; die SQLite-Bibliothek der CI-Images (Version nicht festgelegt) |
| PostgreSQL | 8.0 | **13** (der Installer legt `pgcrypto` an, ab PostgreSQL 13 eine vertrauenswürdige Erweiterung); ab 15 muss der Benutzer der Site Eigentümer der Datenbank sein | 13.23 auf dem Entwicklungsserver; 15 in der CI |
| MongoDB | 4.0 | Server **6.0**, PHP **8.2** oder neuer, PHP-Erweiterung `mongodb` **1.18** oder neuer ([MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md)) | Server 8.3.13 mit Erweiterung 2.3.3 auf PHP 8.5 auf dem Entwicklungsserver |
| MariaDB | 4.1.1 (die MySQL-Nummer) | ein Release, das Ihr Anbieter noch unterstützt; `utf8mb4` und InnoDB | 10.5.29 auf dem Entwicklungsserver |
| Oracle | 19.0 | **19c** (der Treiber verwendet `STANDARD_HASH` und `OFFSET`/`FETCH`; 11g und älter werden nicht unterstützt) | Oracle AI Database 26ai Free (23.26) mit oci8 3.4.1 und Instant Client 23.26 auf PHP 8.5 |
| MySQL | 4.1.1 | ein Release, das Ihr Anbieter noch unterstützt; `utf8mb4` und InnoDB | 8.0 in der CI |

Verwenden Sie eine Serverversion, für die Ihr Anbieter noch Sicherheitskorrekturen liefert. Die Untergrenzen gibt es,
damit der Assistent einen alten Server nicht von vornherein ablehnt; sie sind kein Versprechen, dass ein Server dieses
Alters mit den aktuellen Treibern funktioniert.

**PHP.** Exponential 6.0.x läuft auf PHP 7.4 bis 8.5 (`composer.json`, `"php": "^7.4 || ... || ^8.8"`). Die
Datenbanktreiber folgen diesem Bereich mit einer Ausnahme: Der MongoDB-Adapter braucht PHP 8.2 oder neuer. Velocity,
der empfohlene Webserver, braucht PHP 8.1. Siehe [Kapitel 2](../installation/02-voraussetzungen.md#22-php).

## PHP-Erweiterungen

Eine Datenbankerweiterung ist erforderlich; der kritische Test `database_extensions` des Setup-Assistenten ist
bestanden, wenn mindestens eine von `sqlite3`, `mysqli`, `pgsql`, `mongodb` und `oci8` geladen ist
(`settings/setup.ini`, `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one`).
Installieren Sie nur die, die Ihre Site verwendet.

| Datenbank | Erweiterung | Woher sie kommt |
|---|---|---|
| SQLite 3 | `sqlite3` | in PHP enthalten; RHEL-Familie `php-pdo` (enthält auch `pdo_sqlite`), Debian und Ubuntu `php<version>-sqlite3`. `pdo_sqlite` braucht der Kernel nicht; die Installer von Exponential Platform verwenden es. |
| PostgreSQL | `pgsql` | in PHP enthalten; `php-pgsql`, `php<version>-pgsql` |
| MongoDB | `mongodb` (PECL) und die Composer-Bibliothek `mongodb/mongodb` | `pecl install mongodb` oder `php-pecl-mongodb`; die Bibliothek mit `composer require mongodb/mongodb` (`composer.json` schlägt sie nur vor). Die CI installiert `mongodb/mongodb` `^1.21 || ^2.0`. |
| MariaDB, MySQL | `mysqli` | in PHP enthalten (mysqlnd); `php-mysqlnd`, `php<version>-mysql` |
| Oracle | `oci8` (PECL) | PECL oci8 3.4 für PHP 8.4 und 8.5, 3.2 für PHP 8.1 bis 8.3, gebaut gegen einen Oracle-Client (der Instant Client genügt); EPEL, Remi oder Ihr Anbieter |

Prüfen Sie mit `php -m`, welche Erweiterungen ein PHP hat, und denken Sie daran, dass das PHP des Webservers und das
PHP der Kommandozeile getrennt konfiguriert sind: Prüfen Sie beide. Der optionale Test `database_all_extensions` des
Assistenten listet die fehlenden Datenbankerweiterungen auf; er dient nur zur Information.

## Die Einstellungen: DatabaseSettings in site.ini

Die Verbindung wird in `settings/site.ini` im Block `[DatabaseSettings]` konfiguriert und in
`settings/override/site.ini.append.php` oder in der `site.ini.append.php` eines Siteaccess überschrieben (nie an Ort
und Stelle geändert). Die Installer schreiben diese Überschreibung für Sie. `DatabaseImplementation` benennt den
Treiber mit einem Alias, den `ImplementationAlias[]` einer Klasse zuordnet:

| Alias in `DatabaseImplementation` | Klasse |
|---|---|
| `sqlite3` | `eZSQLite3DB` (`lib/ezdb/classes/ezsqlite3db.php`) |
| `ezpostgresql`, `postgresql`, `pgsql` | `eZPostgreSQLDB` (`lib/ezdb/classes/ezpostgresqldb.php`) |
| `mongodb` | `expMongoDB` (`lib/ezdb/classes/expmongodb.php`) |
| `ezmysqli`, `mysqli`, `ezmysql`, `mysql` | `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`) |
| `ezoracle`, `oracle` | `eZOracleDB` (in der Erweiterung `ezoracle`, registriert durch deren eigene `site.ini.append.php`) |

Die folgenden Beispiele verwenden Platzhalter für Namen und Passwörter; tragen Sie Ihre eigenen Werte in Ihre
Überschreibungsdatei ein und committen Sie diese Datei nie. Jeder Schlüssel wird in
[9.1 Wie Exponential einen Datenbanktreiber wählt](../installation/09-datenbanken.md#91-wie-exponential-einen-datenbanktreiber-auswählt) erklärt.

**1. SQLite 3**

```ini
[DatabaseSettings]
DatabaseImplementation=sqlite3
# a plain name lives in var/storage/sqlite3/; a name starting with / is an absolute path
Database=exponential.db
# seconds a transaction waits for the writers ahead of it; keep it below the web server's request timeout
SQLiteTransactionWait=25
# PRAGMAs over the driver's defaults, one name=value per line (empty keeps the defaults)
SQLitePragmas[]
```

**2. PostgreSQL**

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential
UsePersistentConnection=disabled
```

**3. MongoDB**

```ini
[DatabaseSettings]
DatabaseImplementation=mongodb
Server=localhost
Port=27017
# required, also on a server without access control
User=exponential
Password=<the password>
Database=exponential
```

**4. MariaDB und 6. MySQL**

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=exponential
Password=<the password>
Database=exponential
# a Unix socket instead of host and port
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
```

**5. Oracle**

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<the password>
# the connect string: Easy Connect host:port/service_name, a TNS alias or a full descriptor;
# Server and Port are not used by this driver
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

Leeren Sie nach einer Änderung den INI-Cache, damit jeder Prozess sie liest, und starten Sie die Worker von Velocity
neu, wenn Velocity die Site ausliefert:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

Den wirksamen Wert lesen Sie mit `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation`.
Ein falsch geschriebener Alias bricht die Anfrage nicht sofort ab: Der Kernel weicht auf eine Null-Datenbank aus und
protokolliert „Database implementation not supported“ in `var/log/error.log`, und danach schlägt jede Inhaltsseite
fehl. Lesen Sie zuerst `error.log`, wenn sich eine Site verhält, als wäre ihre Datenbank nicht erreichbar.

## Unterstützung durch die Installer

Exponential hat drei Installer im Kernel. Alle drei unterstützen alle sechs Datenbanken.

| Datenbank | Setup-Assistent (Browser) | Kickstarter (`kickstart.ini`, `[database_choice] Type=`) | `exp:install --db=` |
|---|---|---|---|
| 1. SQLite 3 | ja, als Erstes aufgeführt und vorgewählt (`settings/setup.ini [DatabaseSettings] DefaultType=sqlite3`); schlägt `sqlite.db` vor | `sqlite3` (der Standard in `kickstart.ini-dist`) | `sqlite` (der Standard; auch `sqlite3`), Datei `exponential.db` |
| 2. PostgreSQL | ja; füllt Port 5432 und Benutzer `postgres` vor; legt `pgcrypto` an oder sagt Ihnen, wie | `pgsql` (`postgresql` wird akzeptiert) | `pgsql` (auch `postgresql`, `postgres`) |
| 3. MongoDB | ja; der Konfigurationsschritt kann eine bis drei Minuten dauern, erhöhen Sie also `request_terminate_timeout` von PHP-FPM oder verwenden Sie die Konsole | `mongodb` | `mongodb` (auch `mongo`) |
| 4. MariaDB | ja, als „MySQL Improved“; prüft, ob die Datenbank UTF-8 ist | `mysqli` (`mysql` wird akzeptiert) | `mariadb` (dasselbe wie `mysql`) |
| 5. Oracle | ja, wenn `oci8` geladen und `extension/ezoracle` vorhanden ist; aktiviert die Erweiterung für die neue Site | `oci8` (`oracle` wird akzeptiert); `Database` ist der Connect-String | `oracle` (auch `oci8`, `ezoracle`); `--db-name` ist der Dienstname, Standard `FREEPDB1` |
| 6. MySQL | ja, als „MySQL Improved“ | `mysqli` (`mysql` wird akzeptiert) | `mysql` (auch `mysqli`) |

- Fehlt die Erweiterung `sqlite3`, wählt der Assistent die erste Datenbank, deren Erweiterung geladen ist, und weist
  darauf hin.
- Keiner der Installer legt die Datenbank oder den Benutzer auf einem Datenbankserver an. Legen Sie beides vorher an;
  die Abschnitte zu den einzelnen Datenbanken unten zeigen, wie.
- `exp:install` nimmt das Passwort aus der Umgebungsvariablen `EXP_INSTALL_DB_PASSWORD`, sodass es weder in der
  Shell-History noch in der Prozessliste erscheint.
- **Demo-Installationen.** Dieses Repository hat keinen eigenen Demo-Installer; Demonstrations-Sites werden mit
  denselben Befehlen installiert (`exp:install` oder der Kickstarter), meist auf SQLite.
- **Exponential Platform** (das auf Symfony basierende Schwesterprodukt, nicht der 6.0.x-Kernel) hat einen eigenen
  SQLite-Installer, der `pdo_sqlite` verwendet; siehe [SQLite für Exponential Platform](../funktionen/6.0/plattform-sqlite-installation.md).

Die Installer sind in [Kapitel 5, der Setup-Assistent](../installation/05-setup-assistent.md),
[Kapitel 6, der Kickstarter](../installation/06-kickstarter.md) und [Kapitel 7, die Installation über die Konsole](../installation/07-installation-ueber-die-konsole.md) beschrieben.

## 1. SQLite 3, der Standard

**Was es ist.** Das gesamte Content-Repository in einer Datei unterhalb von `var/storage/sqlite3/`, geöffnet vom
Treiber `eZSQLite3DB`. Kein Serverprozess, kein Netzwerk, keine Benutzerkonten zu verwalten.

**Was der Treiber zu SQLite hinzufügt.** Write-Ahead-Logging (WAL), damit Seitenaufrufe weiterlesen können, während
ein Redakteur veröffentlicht; für einen Webserver gewählte PRAGMAs der Verbindung (`synchronous=NORMAL`, ein Seiten-Cache
von 64 MB, eine Memory-Map von 256 MB, ein Busy-Timeout von 5 Sekunden); die Funktionen, die SQLite fehlen (`md5()`,
die Aggregate `BIT_OR` und `BIT_AND`, mit denen die Content-Engine Sprachmasken bildet); eine Umschreibung von MySQLs
`UPDATE ... JOIN`; und seit 6.0.15 eine **Schreibwarteschlange**: Jede Transaktion nimmt eine exklusive Sperre auf
`<database>.writer-lock` und führt dann `BEGIN IMMEDIATE` aus, sodass gleichzeitige Veröffentlichungen warten, bis sie
an der Reihe sind (bis zu `SQLiteTransactionWait` Sekunden, Standard 25), statt mit „database is locked“
fehlzuschlagen. Sitzungsanweisungen, die es nur in MySQL gibt (`SET NAMES`, `SET FOREIGN_KEY_CHECKS` und ähnliche),
werden angenommen und ignoriert.

**Wann es passt.** Ein Rechner, beliebig viele Leser, ein Redaktionsteam. In den Lasttests des Projekts skalierten
gleichzeitige Leser mit der Anzahl der Prozesse, und 16 gleichzeitige Schreiber liefen ohne Fehler durch, in der
Warteschlange. Wählen Sie stattdessen einen Datenbankserver, wenn mehr als ein Rechner in die Datenbank schreiben muss
(WAL braucht gemeinsamen Speicher auf einem Host, ein Netzwerkdateisystem ist also kein Ersatz), wenn viele Redakteure
den ganzen Tag über gleichzeitig große Änderungen veröffentlichen oder wenn Sie Replikation oder Benutzer und
Berechtigungen auf Datenbankebene brauchen.

**Dateien und Berechtigungen.** Die Datenbank besteht aus vier Dateien: der Datenbank, `-wal`, `-shm` und
`.writer-lock`. Jeder Prozess, der die Site verwendet (das PHP des Webservers, die Worker von Velocity, Cronjobs,
Konsolenbefehle), muss in das Verzeichnis und die Dateien schreiben können; geben Sie ihnen eine gemeinsame Gruppe, den
Verzeichnismodus `2775` und eine umask von `0002`. Liefern Sie das Verzeichnis nie über HTTP aus (die mitgelieferten
Rewrite-Regeln tun das nicht).

**Grenzen.** Ein Host; ein Schreiber zur selben Zeit (in der Warteschlange); keine eigene Replikation; die
Zugriffskontrolle ist die des Dateisystems.

Die vollständige Behandlung mit jedem PRAGMA, dem Verhalten der Warteschlange unter Last, Überwachung und Wartung
(`ANALYZE`, `VACUUM`, die WAL-Datei) finden Sie in [9.2 SQLite 3: eine Produktionsdatenbank in einer Datei](../installation/09-datenbanken.md#92-sqlite-3-eine-produktionsdatenbank-in-einer-datei).
Die aufgabenorientierte Einführung ist [SQLite-Datenbankunterstützung](../funktionen/6.0/sqlite-datenbank.md), die
Treiberspezifikation [Der SQLite3-Datenbanktreiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md).

## 2. PostgreSQL

**Was es ist.** Der Treiber `eZPostgreSQLDB` auf der PHP-Erweiterung `pgsql`.

**Die Datenbank anlegen.** Machen Sie den Benutzer der Site zum **Eigentümer** der Datenbank (ab PostgreSQL 15 dürfen
andere Benutzer im Schema `public` keine Tabellen mehr anlegen):

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

**pgcrypto.** Der Treiber berechnet MD5 mit `digest()` aus `pgcrypto`. Der Installer führt
`CREATE EXTENSION IF NOT EXISTS pgcrypto` aus; als vertrauenswürdige Erweiterung (PostgreSQL 13 und neuer) darf der
Eigentümer der Datenbank sie anlegen. Fehlt sie auf dem Server, installieren Sie das Contrib-Paket von PostgreSQL
Ihrer Distribution und legen Sie sie als Eigentümer oder als Superuser an. Die Meldung des Assistenten, wenn sie fehlt,
lautet „The 'digest' function is not available in your database, and Exponential cannot run without it.“

**Sequenzen.** Auto-Increment-Spalten verwenden Sequenzen mit dem Namen `<table>_<column>_seq`. Nachdem Sie Daten mit
expliziten IDs geladen haben, korrigieren Sie sie
(`SELECT setval('ezcontentobject_id_seq', (SELECT max(id) FROM ezcontentobject));`); der Schema-Installer tut das
selbst, nachdem er Daten geladen hat.

**Grenzen.** Keine, die den eigenen Code von Exponential betreffen. Erweiterungscode, der für MySQL geschrieben wurde
(Quoting mit Backticks, `SET`-Anweisungen, die es nur in MySQL gibt), schlägt hier fehl; siehe
[Hinweise für alle Engines](../installation/09-datenbanken.md#98-hinweise-über-engines-hinweg).

Einzelheiten: [9.4 PostgreSQL](../installation/09-datenbanken.md#94-postgresql).

## 3. MongoDB

**Was es ist.** Der Adapter `expMongoDB` auf der PHP-Erweiterung `mongodb` und dem `MongoDB\Client` der Bibliothek. Er
übersetzt die SQL-artigen Aufrufe des Kernels in die Operationen `find`, `aggregate`, Insert, Upsert und Delete.
MongoDB kam mit 6.0.14; sein Schema-Handler ist `expMongoSchema` (`settings/dbschema.ini`, Typ `mongo`).

**Den Benutzer anlegen**, und zwar in der eigenen Datenbank der Site (der Adapter setzt immer `<user>:<password>@` in
die Verbindungs-URI, und MongoDB verwendet die Datenbank in der URI als Authentifizierungsdatenbank):

```bash
mongosh --eval '
  db.getSiblingDB("exponential").createUser({
    user: "exponential",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "exponential" }, { role: "dbAdmin", db: "exponential" } ]
  })'
```

**Nach der Installation** legen Sie die Indizes an, auf die sich die Abfragen des Kernels verlassen
(`mongosh "mongodb://exponential@localhost:27017/exponential" --file bin/mongodb/create_indexes.js`), und führen dann
die Cronjobs einmal aus, um den Suchindex aufzubauen.

### Grenzen von MongoDB

Aus [MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md) und Abschnitt 26 der
[Portierungsreferenz](../bc/6.0/mongodb-kernel-unterstuetzung.md):

- Der Adapter führt nie echtes SQL aus. Handgeschriebenes SQL, das er nicht übersetzen kann, liefert ein leeres
  Ergebnis und protokolliert eine Warnung (suchen Sie in den Logs nach `MONGO TODO`). Erweiterungen mit eigenem SQL
  brauchen unter Umständen einen eigenen Weg für MongoDB.
- Das tiefe Neuerzeugen der URL-Aliase nach dem Verschieben von Teilbäumen ist unvollständig; die Listenseiten für
  Rollen und Richtlinien können leer sein, obwohl die Berechtigungen durchgesetzt werden; einige Cronjobs sind nicht
  portiert; `eztags` ist nicht nutzbar; der Checkout des Shops und die Zeitplanung von `ezflow` sind nicht durchgehend
  getestet.
- Der SQL-Abfrage-Cache greift nicht.
- Es gibt keine SQL-Update-Dateien. Die Upgrade-Prüfung vergleicht Collections statt Tabellen und gibt die
  `mongosh`-Befehle aus, die die fehlenden anlegen.

Einzelheiten: [9.5 MongoDB](../installation/09-datenbanken.md#95-mongodb).

## 4. MariaDB

**Was es ist.** Derselbe Treiber wie für MySQL, `eZMySQLiDB`, auf der PHP-Erweiterung `mysqli`. Alles unter
[6. MySQL](#6-mysql) gilt auch hier; die folgenden Hinweise betreffen nur die Unterschiede.

- Installieren Sie mit `exp:install --db=mariadb` (dasselbe wie `--db=mysql`) oder wählen Sie im Assistenten „MySQL
  Improved“.
- Legen Sie die Datenbank wie bei MySQL in `utf8mb4` mit InnoDB als Standard an. In MariaDB bedeutet der
  Verbindungsname `utf8` wie in MySQL das Drei-Byte-`utf8mb3`; siehe [Zeichensätze und Kollationen](#zeichensätze-und-kollationen).
- Backups mit `mariadb-dump` (oder dem älteren Namen `mysqldump`) `--single-transaction`.
- MariaDB ist nicht in der CI des Projekts; der Job für MySQL 8.0 testet denselben Treiber. MariaDB 10.5 läuft auf dem
  Entwicklungsserver.

## 5. Oracle

**Was es ist.** Die Erweiterung `ezoracle` (Version 2.3.7, von `composer.json` als `~2.3.7` vorausgesetzt) liefert den
Treiber `eZOracleDB`, den Schema-Handler `eZOracleSchema`, Konsolenbefehle (`ext:ezoracle:<name>`) und Cronjobs. Weil
`composer.json` sie voraussetzt, liegt sie in jeder Installation unter `extension/ezoracle`; aktiviert wird sie nur
für Sites, die Oracle verwenden.

**Voraussetzungen.** Oracle Database 19c oder neuer mit dem Zeichensatz `AL32UTF8`; PHP mit `oci8`, gebaut gegen einen
Oracle-Client (der Instant Client genügt; `ORACLE_HOME` wird mit ihm nicht gebraucht); ein Benutzer, der Sitzungen,
Tabellen, Views, Trigger, Sequenzen und Prozeduren anlegen darf. `php extension/ezoracle/bin/php/ora-grant.php` gibt
die Berechtigungen aus, die ein DBA vergeben muss.

**Verbindungen.** `ezoracle.ini [ConnectionSettings]` bietet persistente Verbindungen, Database Resident Connection
Pooling (DRCP, damit sich Hunderte von PHP-Workern einen Pool von Serverprozessen teilen), Wiederholungen, Failover,
Timeouts für Aufrufe und Sitzungs-Tracing. Jede dieser Funktionen ist standardmäßig ausgeschaltet. Kommen die
Einstellungen aus einer Site-Erweiterung statt aus `settings/override/`, führen Sie diese Erweiterung in
`ActiveExtensions[]` **vor** `ezoracle` auf.

**Oracles eigenes Verhalten, das Sie kennen sollten.** Oracle speichert `''` als NULL
(`OracleEmptyStringForNull=enabled` gibt für Textspalten `''` zurück); es vergleicht Zeichenketten unter Beachtung der
Groß- und Kleinschreibung (`OracleCaseInsensitive=enabled` mit linguistischen Indizes aus
`ext:ezoracle:ci-indexes --create`); und es lehnt `AS` vor einem Tabellenalias ab.

**Grenzen.** Exponential 6.0.15 oder neuer ist nötig (frühere Versionen bieten Oracle in den Installern nicht an).
Oracle ist nicht in der CI des Projekts; Tests nur für Oracle in der Testsuite des Kernels werden auf anderen Engines
übersprungen. Der SQL-Abfrage-Cache greift.

Einzelheiten: [9.6 Oracle 19c und neuer](../installation/09-datenbanken.md#96-oracle-19c-und-neuer-ezoracle) sowie
`README.md` und `INSTALL` der Erweiterung in `extension/ezoracle/`.

## 6. MySQL

**Was es ist.** Der Treiber `eZMySQLiDB` auf `mysqli`, die Engine, für die die meisten Erweiterungen von Drittanbietern
ursprünglich geschrieben wurden.

**Die Datenbank und den Benutzer anlegen.** Eine globale Berechtigung ist nicht nötig:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

**InnoDB.** Der Installer legt InnoDB-Tabellen an, wenn der Server sie anbietet; Transaktionen brauchen InnoDB.
`php bin/php/ezconvertmysqltabletype.php --list` listet die Tabellentypen einer älteren Installation auf,
`--newtype=InnoDB` konvertiert sie.

**Lese-Replikate.** `UseSlaveServer=enabled` mit `SlaveServerArray[]` und den zugehörigen Schlüsseln schickt
Lesezugriffe an ein Replikat, das pro Anfrage zufällig gewählt wird; Schreibzugriffe gehen an `Server`. Dies ist die
einzige Engine mit eingebauter Aufteilung der Lesezugriffe.

**Grenzen.** Der Verbindungszeichensatz `utf-8` wird auf MySQLs `utf8` (drei Bytes) abgebildet, sodass Zeichen
außerhalb der Basic Multilingual Plane, etwa Emoji, nicht durch die Verbindung gelangen, selbst wenn die Tabellen
`utf8mb4` sind.

Einzelheiten: [9.3 MySQL und MariaDB](../installation/09-datenbanken.md#93-mysql-und-mariadb).

## Microsoft SQL Server: möglich mit zusätzlichem Aufwand

**Die Aussage.** Microsoft SQL Server ist derzeit nachweislich mit zusätzlichem Aufwand möglich. In Exponential 6.0.x
wird er nicht unterstützt: Es gibt keinen Treiber, keinen Schema-Handler und keinen Eintrag in den Installern dafür,
und nichts auf dieser Seite ist als Versprechen eines Releases zu verstehen, das einen solchen enthält.

**Was es heute gibt.**

- **Eine kleine Treiberschnittstelle.** Jede Datenbank wird über `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`)
  angesprochen, und die fünf vorhandenen Treiber zeigen, wie man sie für so unterschiedliche Engines wie eine
  Dateidatenbank, drei SQL-Server und einen Dokumentenspeicher implementiert. Besonders der MongoDB-Adapter beweist,
  dass der Kernel nicht von einem SQL-Dialekt abhängt.
- **Ein Engine-neutrales Schema.** Das Schema und die Anfangsdaten des Kernels liegen im generischen Format `.dba`
  vor (`share/db_schema.dba`, `share/db_data.dba`), das ein Schema-Handler in die DDL der jeweiligen Engine umsetzt.
- **Austauschbare Schema-Handler.** `settings/dbschema.ini` ordnet einem Typ eine Handler-Klasse zu (`SchemaPaths[]`,
  `SchemaHandlerClasses[]`), sodass eine neue Engine über Einstellungen registriert wird und nicht durch eine Änderung
  am Kernel.
- **Erweiterbare Treiber-Aliase.** `site.ini [DatabaseSettings] ImplementationAlias[]` wird aus der
  `site.ini.append.php` einer Erweiterung ergänzt, so wie `ezoracle` die Aliase `ezoracle` und `oracle` hinzufügt.
- **Erweiterbare Installer-Typen.** Die Vorlage des Kickstarters sagt „other values are possible if provided by
  extension“.
- **Werkzeuge, die SQL Server bereits lesen.** Der Datenbankmanager der Erweiterung `sevenx_dse` (AdminNeo, ein Fork
  von Adminer) bringt einen MS-SQL-Treiber zum Durchsehen und Bearbeiten von Tabellen mit, und der generische
  SQLAlchemy-Exporter in Abschnitt 18.6 der [MongoDB-Portierungsreferenz](../bc/6.0/mongodb-kernel-unterstuetzung.md)
  kann SQL Server über `pyodbc` lesen. Keines von beiden ist ein Weg, eine Site auf SQL Server zu betreiben.
- **PHP-Treiber.** Microsoft veröffentlicht die Erweiterungen `sqlsrv` und `pdo_sqlsrv` für PHP unter Linux und
  Windows. Prüfen Sie in Microsofts Support-Matrix, welche PHP-Versionen ein Release abdeckt; mit Exponential wurde das
  nicht getestet.

### Ein Ausgangspunkt: die Erweiterung xrowmssql

Eine Erweiterung für SQL Server gibt es bereits, geschrieben für frühere Fassungen der Datenbank-API:
[se7enxweb/xrowmssql](https://github.com/se7enxweb/xrowmssql), „A full-featured open source Microsoft SQL Server
database plugin“ (Beschreibung des Repositorys). Ihre README nennt sie „Microsoft SQL Server database extension for eZ
Publish 4.x and SQL Server 2005/2008“, „Developed by xrow GmbH“, und sagt, dass sie einen Datenbanktreiber und eine
Datenbank-Schemadefinition enthält. Das Repository enthält den Code, wie er im Oktober 2013 aus `xrow/xrowmssql`
kopiert wurde; seitdem kamen nur Funding-Dateien hinzu (sein `git log`). Auf Exponential 6 wurde sie nicht ausgeführt,
und diese Seite behauptet nicht, dass sie dort funktioniert.

**Was sie bereitstellte** (gelesen aus ihren Verzeichnissen `classes/`, `settings/`, `sql/`, `patch/` und `docs/` am
18. Oktober 2026):

| Teil | Datei | Was er tat |
|---|---|---|
| Treiber | `classes/ezmssqldb.php` (`eZMSSQLDB`) | Ein `eZDBInterface`-Treiber: Verbinden, Abfragen, `arrayQuery()`, Transaktionen, `lastSerialID()` über `SELECT @@identity`, `escapeString()` (verdoppelte Anführungszeichen), `subString()` (`substring()`), `concatString()` (der Operator `+`), `md5()` (`HashBytes('md5', ...)`, in Hex umgewandelt), `eZTableList()` aus `sysobjects`, Datenbanklisten und Serverversion |
| Verbindungsschicht | `classes/ezmssqlconnector.php` (`eZMSSQLFunctions`) | Zwei Backends: Microsofts Erweiterung `sqlsrv` unter Windows und die alten `mssql_*`-Funktionen über FreeTDS (`dblib`) unter Linux |
| Unicode-Literale | `eZMSSQLDB::_appendN()` | Stellte nicht numerischen Zeichenkettenliteralen ein `N` voran, sodass Text als Unicode in `NVARCHAR`-Spalten gelangte |
| Identity-Inserts | `eZMSSQLDB::query()` | Schaltete `IDENTITY_INSERT` um Inserts herum ein und aus, die die Schlüsselspalte setzen |
| Schema-Handler | `classes/ezmssqlschema.php` (`eZMssqlSchema`) | Setzte `.dba` in DDL für SQL Server um (`nvarchar`, `ntext`, `int IDENTITY(0,1)`, Primärschlüssel und Indizes, Insert-Trigger) und las eine vorhandene Datenbank in ein Schema zurück |
| Einstellungen | `settings/site.ini.append`, `settings/dbschema.ini.append` | Die Aliase `mssql` und `ezmssql` für `ImplementationAlias[]`, Port 1433, der Schema-Handler unter `SchemaHandlerClasses[mssql]` und Übersetzungen von Typ und `NOT NULL` pro Spalte für lange Textspalten |
| Serverobjekte | `sql/PROCEDURE.sql`, `sql/FUNCTION.sql`, `sql/clustering.sql` | Eine Stored Procedure und eine Funktion, die zuerst in SQL Server anzulegen sind, und die Cluster-Tabellen |
| Kernel-Patch | `patch/4.3/kernel.patch` | Eine Änderung an `kernel/classes/ezcontentlanguage.php` für die Arithmetik von SQL Server auf Sprachmasken |
| Installationshinweise | `README`, `docs/` | Einrichtung des Clients (SQL Server Native Client unter Windows, FreeTDS unter Linux), Laden des Schemas mit `ezsqlinsertschema.php --type=mssql`, Fehlerbehebung und bekannte Fehler |

Ihre `extension.xml` nennt die Version 1.0.0 und „GNU General Public License v2.0“, während die Datei `LICENSE` des
Repositorys die GPL in Version 3 ist; prüfen Sie beides, bevor Sie Code wiederverwenden. Die Einstellungen erwarten,
dass das Verzeichnis der Erweiterung `extension/mssql` heißt.

**Wie sie sich auf die Arbeitsliste unten abbilden lässt und was modernisiert werden müsste:**

| Zeile der Arbeitsliste | Was xrowmssql schon hat | Was sich ändern müsste |
|---|---|---|
| Eine Treiberklasse | Die meisten Methoden von `eZDBInterface` | PHP 8: Ihr Konstruktor im Stil von PHP 4 (`function eZMSSQLDB()`, der `$this->eZDBInterface()` aufruft) läuft nicht auf PHP 8, wo `eZDBInterface` nur `__construct()` hat; `connect()` fehlt der Parameter `$port` der heutigen Treiber; die `mssql_*`-Funktionen wurden ab PHP 7 entfernt, sodass als Backend nur `sqlsrv` bleibt, auch unter Linux |
| Offset und Limit | Nichts in Gebrauch: Die Umschreibung mit `ROW_NUMBER()` in `arrayQuery()` ist auskommentiert, sodass `limit` und `offset` ignoriert werden | `ORDER BY ... OFFSET n ROWS FETCH NEXT m ROWS ONLY`, was SQL Server 2012 oder neuer braucht |
| `lastSerialID()` | `@@identity` | `SCOPE_IDENTITY()`, das von Triggern nicht verändert wird, oder `OUTPUT inserted.id` |
| `md5()`, `subString()`, `concatString()` | Vorhanden | `CONVERT(VARCHAR(32), HASHBYTES('MD5', ...), 2)` statt `master.dbo.fn_varbintohexstr`; `NVARCHAR(MAX)` statt der Obergrenze von 8000 Zeichen |
| `bitAnd()`/`bitOr()` | Die Standardimplementierungen des Kernels (die bitweisen Operatoren AND und OR) passen bereits für SQL Server | Nichts |
| Das Aggregat `BIT_OR` | Nicht vorhanden | Noch zu tun, wie unten beschrieben |
| `UPDATE ... JOIN` | Nicht vorhanden | Noch zu tun (`UPDATE t SET ... FROM t JOIN ...`) |
| Ein Schema-Handler | `eZMssqlSchema`, in beide Richtungen | Konstruktor für PHP 8; `NVARCHAR(MAX)` statt des veralteten `ntext`; eine Überprüfung der Spaltenübersetzungen anhand der heutigen `share/db_schema.dba` |
| SQL-Dateien, Installer-Einträge, Erweiterungen, Tests und CI | Nur Installationshinweise | Alles noch zu tun |
| Sitzungsanweisungen, Groß- und Kleinschreibung und Kollation | Nicht behandelt | Noch zu tun |

Zusammengenommen zeigt xrowmssql, wie jedes Teil für SQL Server schon einmal gelöst wurde, und liefert einen
Schema-Handler und ein Treibergerüst als Ausgangspunkt; damit es auf Exponential 6 läuft, muss es auf PHP 8 und
`sqlsrv` portiert, seine Idiome aus SQL Server 2005 müssen ersetzt und die Zeilen ergänzt werden, die es nie abgedeckt
hat.

**Was fehlt: die Arbeitsliste.**

| Teil | Wo | Was dazugehört |
|---|---|---|
| Eine Treiberklasse | `lib/ezdb/classes/` oder eine Erweiterung (so wie bei `ezoracle`) | `eZDBInterface` auf `sqlsrv` implementieren: Verbinden, Abfragen, `arrayQuery()` mit Offset und Limit (`OFFSET ... FETCH`, SQL Server 2012 und neuer), Transaktionen, `lastSerialID()` (Identity-Spalten, `SCOPE_IDENTITY()`), `escapeString()`, `subString()`, `concatString()` (`+` oder `CONCAT()`), `md5()` (`HASHBYTES('MD5', ...)`, in Hex umgewandelt), `bitAnd()`/`bitOr()` (die Operatoren `&` und `|`), Tabellen- und Sequenzlisten für `eZTableList()` |
| Das Aggregat `BIT_OR` | der Treiber | SQL Server hat kein Aggregat `BIT_OR`; die Content-Engine fasst damit Sprachmasken zusammen. Der SQLite-Treiber hat dieselbe Lücke geschlossen, indem er ein Aggregat registriert; auf SQL Server braucht es eine Umschreibung dieser Abfragen oder ein CLR-Aggregat |
| `UPDATE ... JOIN` | der Treiber | SQL Server schreibt es als `UPDATE t SET ... FROM t JOIN ...`; `rewriteJoinedUpdate()` des SQLite-Treibers zeigt das Muster |
| Ein Schema-Handler | `lib/ezdbschema/classes/` und `settings/dbschema.ini` | `.dba` in `CREATE TABLE` mit den Typen von SQL Server umsetzen (`NVARCHAR`, `NVARCHAR(MAX)`, `INT IDENTITY`), Indizes (innerhalb der Größengrenzen von SQL Server für Indexschlüssel) und wieder zurück für die Upgrade-Prüfung |
| SQL-Dateien | `kernel/sql/<engine>/`, `update/database/<engine>/` | das erzeugte Schema und die Update-Dateien jedes späteren Releases |
| Installer-Einträge | `eZSetupDatabaseMap()` in `kernel/setup/ezsetupcommon.php`, `settings/setup.ini [database_extensions]`, `bin/php/install.php` | die Auswahl im Assistenten, die Prüfung der Erweiterung, der Alias für `--db` |
| Sitzungsanweisungen | der Treiber | `SET`-Anweisungen, die es nur in MySQL gibt, ignorieren, wie es der SQLite-Treiber tut |
| Groß- und Kleinschreibung und Kollation | die Datenbank | Die Standardkollationen von SQL Server vergleichen wie die von MySQL ohne Beachtung der Groß- und Kleinschreibung; wählen Sie eine UTF-8-Kollation (SQL Server 2019 und neuer) oder `NVARCHAR`-Spalten |
| Erweiterungen | jede Erweiterung mit eigenem SQL | dieselbe Überprüfung, die die Portierungen auf PostgreSQL und Oracle brauchten: Backticks, `LIMIT`, MySQL-Funktionen |
| Tests und CI | `tests/`, `.github/workflows/` | ein CI-Job mit einem SQL-Server-Container, wie ihn die Jobs für MySQL und PostgreSQL haben |

Wie groß diese Arbeit ist, wurde nicht gemessen, und eine Schätzung wird hier nicht gegeben. Wer sie übernehmen möchte,
ist willkommen: Beginnen Sie bei [xrowmssql](#ein-ausgangspunkt-die-erweiterung-xrowmssql) für die Besonderheiten von SQL
Server und bei der Erweiterung `ezoracle` als Vorbild für einen aktuellen Treiber, der außerhalb des Kernels
ausgeliefert wird, und eröffnen Sie ein Issue im
[Software-Tracker](https://github.com/se7enxweb/exponential/issues), um die Arbeit abzustimmen.

## Zeichensätze und Kollationen

Exponential speichert und liefert Text als UTF-8. Jede Engine nennt das anders:

| Datenbank | Die Datenbank anlegen mit | Hinweise |
|---|---|---|
| 1. SQLite 3 | nichts zu wählen | SQLite speichert Text als UTF-8. `=` vergleicht unter Beachtung der Groß- und Kleinschreibung; `LIKE` ignoriert sie nur für ASCII-Buchstaben. |
| 2. PostgreSQL | `--encoding=UTF8 --template=template0` | Mit `template0` können Sie die Kodierung unabhängig vom Standard des Servers wählen. Vergleiche beachten die Groß- und Kleinschreibung. |
| 3. MongoDB | nichts zu wählen | BSON-Zeichenketten sind UTF-8. Vergleiche beachten die Groß- und Kleinschreibung, sofern für die Abfrage oder den Index keine Kollation gesetzt ist. |
| 4. MariaDB | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | Der Assistent lehnt eine Datenbank ab, deren Standardzeichensatz nicht UTF-8 ist („cannot be used, the setup wizard wants to create the site in ...“). Die Standardkollationen vergleichen ohne Beachtung der Groß- und Kleinschreibung. |
| 5. Oracle | Datenbankzeichensatz `AL32UTF8` | Der Treiber verbindet sich in AL32UTF8; `NLS_LANG` wird nicht gebraucht. Beachtet die Groß- und Kleinschreibung, sofern nicht `OracleCaseInsensitive=enabled` gesetzt ist. |
| 6. MySQL | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | Wie bei MariaDB. |

**Die Verbindung bei MySQL und MariaDB.** `Charset=utf-8` (der Standard) wird als `utf8` an den Server geschickt, was
beide Server als das Drei-Byte-`utf8mb3` behandeln. Halten Sie die Datenbank trotzdem in `utf8mb4`: Es kostet nichts.
Lässt sich der Zeichensatz der Verbindung nicht setzen, läuft der Treiber weiter und protokolliert die Warnung „could
not set the connection charset“; sie wird nur protokolliert, wenn Warnungen protokolliert werden
(`site.ini [DebugSettings] AlwaysLog[]=warning`).

**Groß- und Kleinschreibung.** Eine Suche nach Login oder Kennung, die für die Kollationen von MySQL ohne Beachtung der
Groß- und Kleinschreibung geschrieben wurde, findet auf PostgreSQL, Oracle oder MongoDB unter Umständen nichts.
Vergleichen Sie `lower(...)` auf beiden Seiten, wo die Groß- und Kleinschreibung keine Rolle spielen darf.

**Die Seite Systeminformationen** (Setup > Systeminformationen) warnt, wenn der Zeichensatz der Verbindung nicht UTF-8
ist.

## Performance und Skalierung

- **Die meisten Anfragen erreichen die Datenbank nie.** Der Response-Cache von Velocity (anonyme Besucher), der
  View-Cache für Inhalte und der Template-Block-Cache antworten zuerst; darunter beantwortet der optionale
  SQL-Abfrage-Cache (`settings/querycache.ini`, standardmäßig `Mode=off`) wiederholte `SELECT`-Anweisungen auf jeder
  Engine außer MongoDB. Messen Sie mit `php bin/php/console exp:benchmark`.
- **SQLite** skaliert Lesezugriffe mit der Anzahl der Prozesse; Schreibzugriffe kommen in die Warteschlange. Rechnen
  Sie mit bis zu 64 MB Seiten-Cache je Prozess, der die Datenbank geöffnet hat (`SQLitePragmas[]=cache_size=-16384`
  senkt das auf 16 MB).
- **PostgreSQL, MariaDB, MySQL** skalieren Schreibzugriffe mit Sperren auf Zeilenebene.
  `UsePersistentConnection=enabled` hält eine Verbindung je PHP-Prozess; unter den persistenten Workern von Velocity
  behält jeder Worker seine Verbindung ohnehin. MySQL und MariaDB können Lesezugriffe an Replikate schicken
  (`UseSlaveServer`).
- **MongoDB**: Die Performance hängt von der Anzahl der Anweisungen pro Seite ab; legen Sie die Indizes aus
  `bin/mongodb/create_indexes.js` an, ohne die Abfragen auf Teilbäume und Suchen nach URL-Aliasen ganze Collections
  durchlaufen.
- **Oracle**: Eine neue dedizierte Sitzung kostet einige zehn Millisekunden; verwenden Sie `Persistent=enabled` und
  DRCP (`ezoracle.ini [ConnectionSettings]`) sowie die `Prefetch`-Einstellungen von `[PerformanceSettings]`.
- **Langsame Anweisungen**: `[DatabaseSettings] SlowQueriesOutput=<milliseconds>` mit `SQLOutput=enabled` listet auf
  jeder Engine die langsameren Anweisungen in der Debug-Ausgabe auf; auf MySQL und MariaDB ergänzt
  `QueryAnalysisOutput=enabled` ein `EXPLAIN`; Oracle protokolliert langsame Anweisungen in `var/log/oracle-slow.log`,
  wenn `[LogSettings] SlowQueryThreshold` gesetzt ist.

Die Vergleichstabelle in [9.7](../installation/09-datenbanken.md#97-vergleich) stellt Nebenläufigkeit, Replikate und
persistente Verbindungen nebeneinander.

## Backup und Wiederherstellung

Ein vollständiges Backup umfasst die Datenbank **und** die Dateien: `var/storage` (Uploads und Bildvarianten),
`settings/override` und `settings/siteaccess`. Schalten Sie für eine Wiederherstellung den Wartungsmodus ein
(`php bin/php/maintenance.php on --allow-root-user`) und leeren Sie danach jeden Cache
(`php bin/php/ezcache.php --clear-all --allow-root-user` und, mit Velocity, `php bin/php/console exp:velocity cache clear --apply --allow-root-user`).
Das vollständige Verfahren steht in [Kapitel 10, Backups und Wiederherstellung](../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung).

| Datenbank | Backup | Wiederherstellung |
|---|---|---|
| 1. SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential.db'"` (online, konsistent) oder `"VACUUM INTO '/backup/exponential.db'"`; prüfen mit `PRAGMA integrity_check` | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential.db'"`; kopieren Sie nie eine Datenbankdatei, die in Gebrauch ist |
| 2. PostgreSQL | `pg_dump -U exponential -Fc exponential > exponential.dump` | `pg_restore -U exponential -d exponential --clean exponential.dump` |
| 3. MongoDB | `mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/exponential` | `mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/exponential` |
| 4. MariaDB | `mariadb-dump --single-transaction -u exponential -p exponential > exponential.sql` | `mariadb -u exponential -p exponential < exponential.sql` |
| 5. Oracle | `php bin/php/console ext:ezoracle:datapump` (siehe dessen `--help`) oder Oracles `expdp` | Oracles `impdp` |
| 6. MySQL | `mysqldump --single-transaction -u exponential -p exponential > exponential.sql` | `mysql -u exponential -p exponential < exponential.sql` |

`--single-transaction` erzeugt einen konsistenten Dump von InnoDB-Tabellen, ohne sie zu sperren. Befehle, die ein
Passwort auf der Kommandozeile erhalten, zeigen es anderen Benutzern des Rechners; verwenden Sie lieber die
Passwortabfragen der Werkzeuge oder Optionsdateien.

## Eine Site auf eine andere Datenbank umziehen

Die Inhalte bleiben dieselben, wenn eine Site ihre Datenbank wechselt; nur der Ort, an dem sie gespeichert sind,
ändert sich. Planen Sie ein Wartungsfenster ein: Zwischen dem Dump und der Umstellung darf nichts veröffentlicht
werden.

- **Zwischen SQL-Engines** (SQLite, PostgreSQL, MariaDB, MySQL und Oracle mit `ezoracle`): mit
  `bin/php/ezsqldumpschema.php --format=generic --output-array --output-types=all` in eine `.dba`-Datei exportieren und
  mit `bin/php/ezsqlinsertschema.php --insert-types=all` laden, wobei Sie die Engine mit `--type` wählen (`mysql`,
  `postgresql`, `sqlite3`, `oracle`). Richten Sie dann `DatabaseImplementation` und die Verbindungsschlüssel auf die
  neue Engine aus, leeren Sie alle Caches und starten Sie die Worker neu.
- **Nach Oracle**: die eigenen Befehle der Erweiterung, `ext:ezoracle:mysql2oracle-schema` und
  `ext:ezoracle:mysql2oracle-data`.
- **Nach MongoDB**: die Skripte in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`,
  `validate_ndjson.sh`) und Abschnitt 18 der Portierungsreferenz.
- **Vor dem Wiederfreigeben prüfen**: Vergleichen Sie die Zeilenzahlen von `ezcontentobject`, `ezcontentobject_tree`
  und `ezcontentobject_attribute` auf beiden Seiten; korrigieren Sie auf PostgreSQL die Sequenzen; melden Sie sich an
  und veröffentlichen Sie ein Testobjekt.
- **Spätere Upgrades** verwenden die Update-Dateien der Engine, auf der die Site jetzt läuft, nicht die der Engine, auf
  der sie installiert wurde.

Die Schritt-für-Schritt-Fassung ist [9.9 Eine bestehende Site auf eine andere Engine umstellen](../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen).

## Testabdeckung (CI)

Was die Continuous Integration des Projekts (`.github/workflows/`) bei jedem Push ausführt, je Datenbank.
„Unit-Tests des Treibers“ bedeutet Tests, die eine echte Datenbankdatei öffnen oder das SQL des Treibers ohne Server
testen; „Datenbanktests“ bedeutet die Tests des Legacy-Toolkits, die gegen einen laufenden Server mit einer
Wegwerf-Datenbank pro Test laufen.

| Datenbank | In der CI | Wie |
|---|---|---|
| 1. SQLite 3 | ja, Unit-Tests des Treibers | der PHPUnit-Job auf PHP 8.1, 8.2, 8.3, 8.4 und 8.5 lädt `sqlite3`; die SQLite-Tests in `tests/tests/lib/ezdb/` prüfen den Treiber, teils auf einer In-Memory- oder temporären Datenbank (Anzahl der Abfragen, das Schema-Spiegelbild von `kernel/sql/sqlite/`), teils durch Lesen seiner Einstellungen (die Wartezeit für Transaktionen) |
| 2. PostgreSQL | ja, Datenbanktests | der Job „Database tests (postgresql, PHP 8.3)“ mit einem `postgres:15`-Service und `pgcrypto`, `tests/runtests.php --dsn postgresql://... --db-per-test` |
| 3. MongoDB | Unit-Tests des Adapters | der PHPUnit-Job lädt `mongodb` und installiert `mongodb/mongodb`; die Suite `mongodb` (Adaptertests ohne Server) läuft; die Gruppe `mongodb-live` braucht laufende MongoDB- und MySQL-Server und ist von der CI ausgenommen |
| 4. MariaDB | nein | indirekt durch den MySQL-Job abgedeckt (derselbe Treiber) |
| 5. Oracle | nein | Oracle-spezifische Kernel-Tests werden auf anderen Engines übersprungen; die Erweiterung `ezoracle` hat in der installierten Kopie keinen eigenen Workflow (upstream nicht geprüft) |
| 6. MySQL | ja, Datenbanktests | der Job „Database tests (mysql, PHP 8.3)“ mit einem `mysql:8.0`-Service |

PHP 7.4, die älteste unterstützte Version, und PHP 8.0 prüft der Quality-Workflow mit einer Syntaxprüfung jeder PHP-Datei und einem
Boot-Smoke-Test ohne Datenbank. Die Testsuiten und wie man sie lokal ausführt, beschreibt
[Continuous Integration](../spezifikationen/6.0/kontinuierliche-integration.md).

## Welche Datenbank verwendet meine Site?

- In der Administration: **Setup > Systeminformationen**, die Karte **Datenbank**: Engine und Serverversion,
  Treiberklasse, die Datei (SQLite) oder Server und Datenbankname, Zeichensatz, Anzahl der Tabellen und Größe.
- Auf der Kommandozeile:

  ```bash
  php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
  ```

- **Setup > Upgrade-Prüfung**, „Überprüfe Datenbankkonsistenz“, vergleicht die Tabellen, Felder und Indizes mit dem
  Schema des Release für MySQL und MariaDB, PostgreSQL, SQLite und MongoDB ([Die Upgrade-Prüfung](../anleitungen/upgrade-pruefung.md)).
- Im Code: `eZDB::instance()->databaseName()` liefert `sqlite`, `postgresql`, `mongo`, `mysql` oder `oracle`.

## Verwandte Seiten

- [Kapitel 9, Datenbanken](../installation/09-datenbanken.md): die Referenz für den Betrieb jeder Engine.
- [Kapitel 2, Anforderungen](../installation/02-voraussetzungen.md#26-datenbanken) und [Unterstützte Betriebssysteme](betriebssystemunterstuetzung.md).
- [SQLite-Datenbankunterstützung](../funktionen/6.0/sqlite-datenbank.md),
  [SQLite: Transaktionen warten in einer Warteschlange auf die Schreibsperre](../bc/6.0/sqlite-transaktionen.md),
  [Der SQLite3-Datenbanktreiber](../spezifikationen/6.0/sqlite3-datenbanktreiber.md).
- [MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md) und die
  [Portierungsreferenz zur MongoDB-Unterstützung im Kernel](../bc/6.0/mongodb-kernel-unterstuetzung.md).
- [Die Treiber für SQLite und Oracle](../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md),
  [Datenbanktreiber und Installer, September 2026](../spezifikationen/6.0/datenbanktreiber-2026-09.md).
- [SQL-Abfrage-Cache](../bc/6.0/sql-abfrage-cache.md), [Installation mit einem Befehl](../funktionen/6.0/installation-mit-einem-befehl.md),
  [Kickstarter](../funktionen/6.0/kickstarter-cli.md), [Systeminformationen](../anleitungen/systeminformationen.md).
- [Freie Dokumentation](freie-dokumentation.md): Diese Seiten dürfen frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
