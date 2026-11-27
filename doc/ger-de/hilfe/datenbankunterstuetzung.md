---
title: Datenbankunterstützung
audience: admin, developer
category: install-upgrade
order: 10
summary: Welche Datenbanken Exponential 6.0.x unterstützt und in welcher Reihenfolge (zuerst SQLite, dann PostgreSQL, MongoDB, MariaDB, Oracle, MySQL sowie Microsoft SQL Server mit Azure SQL), mit einer Unterstützungsmatrix und jeweils mit Versionen, PHP-Erweiterungen, Einstellungen, Installern, Clustering, Zeichensätzen, Performance, Backup und Wiederherstellung, Grenzen, Migration und Testabdeckung.
keywords: Datenbank, Datenbanken, unterstützte Datenbanken, Unterstützungsmatrix, support matrix, Azure SQL, expSQLServerDB, pdo_sqlsrv, pdo_odbc, Datenbanktreiber, Sicherung, Wiederherstellung, Migration, Zeichensatz, Anforderungen, Versionen, Testabdeckung, SQLite, SQLite3, PostgreSQL, Postgres, MongoDB, MariaDB, MySQL, Oracle, ezoracle, oci8, pgsql, mysqli, sqlite3, mongodb, Microsoft SQL Server, MSSQL, SQL Server, sqlsrv, xrowmssql, xrow, FreeTDS, dblib, DatabaseImplementation, DatabaseSettings, site.ini, database, databases, supported databases, backup, restore, migration, character set, utf8mb4, AL32UTF8, CI, requirements, versions
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
2. [Unterstützungsmatrix](#unterstützungsmatrix)
3. [Auf einen Blick](#auf-einen-blick)
4. [Versionen, am Code überprüft](#versionen-am-code-überprüft)
5. [PHP-Erweiterungen](#php-erweiterungen)
6. [Die Einstellungen: DatabaseSettings in site.ini](#die-einstellungen-databasesettings-in-siteini)
7. [Unterstützung durch die Installer](#unterstützung-durch-die-installer)
8. [SQLite 3, der Standard](#1-sqlite-3-der-standard)
9. [PostgreSQL](#2-postgresql)
10. [MongoDB](#3-mongodb)
11. [MariaDB](#4-mariadb)
12. [Oracle](#5-oracle)
13. [MySQL](#6-mysql)
14. [Microsoft SQL Server und Azure SQL](#7-microsoft-sql-server-und-azure-sql)
    - [Datenbanken mit dem PostgreSQL-Protokoll](#datenbanken-mit-dem-postgresql-protokoll)
15. [Datenbanken, die das MySQL-Protokoll sprechen: TiDB, Vitess, SingleStore, Xpand, Aurora](#datenbanken-die-das-mysql-protokoll-sprechen)
16. [Zeichensätze und Kollationen](#zeichensätze-und-kollationen)
17. [Performance und Skalierung](#performance-und-skalierung)
18. [Backup und Wiederherstellung](#backup-und-wiederherstellung)
19. [Eine Site auf eine andere Datenbank umziehen](#eine-site-auf-eine-andere-datenbank-umziehen)
20. [Testabdeckung (CI)](#testabdeckung-ci)
21. [Welche Datenbank verwendet meine Site?](#welche-datenbank-verwendet-meine-site)
22. [Verwandte Seiten](#verwandte-seiten)

## Die unterstützten Datenbanken in ihrer Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x) in der Reihenfolge der Empfehlung:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server und Azure SQL.

Dies ist die offizielle Reihenfolge des Projekts für Exponential 6.0.x. In dieser Reihenfolge werden die Datenbanken
empfohlen, dokumentiert und in der gesamten Dokumentation aufgeführt. Alle sieben werden unterstützt: Jede von ihnen
hat einen Treiber im Kernel oder in einer mitgelieferten Erweiterung, einen Weg in den Installern für die
Kommandozeile und ein DFS-Cluster-Backend im Kernel.

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

7. **Microsoft SQL Server und Azure SQL** (SQL Server 2017 bis 2025, Azure SQL Database und Managed Instance) laufen
   auf dem eigenen Treiber des Kernels, `expSQLServerDB`, neu in 6.0.15, unter Linux und unter Windows. Es ist die
   Wahl für Organisationen, die auf die Datenbank von Microsoft standardisiert haben
   ([Abschnitt 7](#7-microsoft-sql-server-und-azure-sql)).

## Unterstützungsmatrix

| Engine | Versionen | PHP-Erweiterung | Installer | Cluster (DFS-Metadaten) | Bei jedem Push in der CI getestet | Stand |
|---|---|---|---|---|---|---|
| SQLite 3 | 3.33 und neuer | `sqlite3` | Setup-Assistent (Standard), Kickstarter, `exp:install` (Standard) | nur ein Rechner (`expDFSFileHandlerSQLiteBackend`) | Treibertests auf PHP 8.1 bis 8.5; das DFS-Backend | unterstützt, der Standard |
| PostgreSQL | 13 bis 19 | `pgsql` | Setup-Assistent, Kickstarter, `exp:install` | beliebig viele Server (`expDFSFileHandlerPostgresqlBackend`), Lesereplikate | 13, 15, 18 und 19 für die Site, 13 bis 19 für das DFS-Backend | unterstützt, der empfohlene Server |
| MongoDB | 6.0 und neuer, PHP 8.2 und neuer | `mongodb` und die Bibliothek `mongodb/mongodb` | Setup-Assistent, Kickstarter, `exp:install` | beliebig viele Server, ein Server oder ein Replica Set (`expDFSFileHandlerMongoDBBackend`, 5.0 und neuer) | Adaptertests; MongoDB 7 und 8.0 in den Datenbank- und DFS-Jobs | unterstützt |
| MariaDB | Versionen, die Ihr Anbieter noch pflegt | `mysqli` | Setup-Assistent, Kickstarter, `exp:install` | beliebig viele Server (MySQLi-Backend), Lesereplikate | über die MySQL-Jobs (derselbe Treiber) | unterstützt |
| Oracle | 19c und neuer | `oci8` | Setup-Assistent, Kickstarter, `exp:install` (Treiber aus `ezoracle`) | beliebig viele Server (`expDFSFileHandlerOracleBackend`, 12.1 und neuer), Lesereplikate | das DFS-Backend auf Oracle Free 23 | unterstützt |
| MySQL | Versionen, die Ihr Anbieter noch pflegt | `mysqli` | Setup-Assistent, Kickstarter, `exp:install` | beliebig viele Server (das Standard-Backend), Lesereplikate | MySQL 8.0 | unterstützt |
| Microsoft SQL Server, Azure SQL | 2017, 2019, 2022, 2025; Azure SQL Database und Managed Instance | `sqlsrv`, `pdo_sqlsrv`, `pdo_odbc` oder `pdo_dblib` | Kickstarter, `exp:install` | beliebig viele Server (`expDFSFileHandlerSQLServerBackend`), Lesereplikate mit Always On | 2017, 2019, 2022 und 2025 auf PHP 8.1 bis 8.5 über drei PHP-Schichten; LocalDB unter Windows | unterstützt (neu in 6.0.15) |
| TiDB, Vitess / PlanetScale, SingleStore | TiDB 6.5+, Vitess (ein ungesplitteter Keyspace), SingleStore 8.5+ | `mysqli` | Kickstarter, `exp:install` | beliebig viele Server (das MySQLi-Backend) | TiDB 8.5, Vitess und SingleStore in Containern (Job `mysqlwire`) | unterstützt (neu in 6.0.15): [das MySQL-Protokoll](#datenbanken-die-das-mysql-protokoll-sprechen) |
| MariaDB Xpand, Amazon Aurora MySQL | Xpand 6, Aurora MySQL 3 | `mysqli` | Kickstarter, `exp:install` | das MySQLi-Backend | nein (kein Image; ein verwalteter Dienst) | Treiber und Profile, nicht gemessen |
| DuckDB | 1.5 und 2.0 (1.5.6 und 2.0.1 getestet) | `ffi` mit DuckDBs C-Bibliothek `libduckdb` | Kickstarter, `exp:install` | keiner: es schreibt immer nur ein Prozess | Treiber- und Live-Tests auf DuckDB 1.5.6 und 2.0.1 und eine ganz installierte Site | unterstützt für kleine Sites, die vor allem gelesen werden (neu in 6.0.15); [was geht und was nicht](../funktionen/6.0/duckdb-datenbanktreiber.md#was-geht-und-was-nicht) |
| CockroachDB (PostgreSQL-Protokoll) | 23.2 und neuer | `pgsql` | Setup-Assistent, Kickstarter, `exp:install` (`cockroachdb`) | nicht getestet | die Live-Suite `pgwire-live` auf CockroachDB 25.2, PHP 8.1 und 8.5 | vollständige Site-Datenbank (neu in 6.0.15) |
| YugabyteDB (PostgreSQL-Protokoll) | 2.20 und neuer | `pgsql` | Setup-Assistent, Kickstarter, `exp:install` (`yugabytedb`) | nicht getestet | die Live-Suite `pgwire-live` auf YugabyteDB 2.25, PHP 8.1 und 8.5 | vollständige Site-Datenbank (neu in 6.0.15) |
| Google Cloud Spanner, PostgreSQL-Dialekt (PGAdapter) | der Spanner-Emulator, PGAdapter | `pgsql` | keiner: keine Site-Datenbank | keins | die Live-Suite `pgwire-live` auf dem Emulator hinter PGAdapter | Datentabellen-Verbindung und Import (neu in 6.0.15) |
| Neon, Supabase, Amazon Aurora PostgreSQL | das PostgreSQL der Dienste (13 und neuer) | `pgsql` | Setup-Assistent, Kickstarter, `exp:install` (`neon`, `supabase`, `aurorapostgresql`) | wie PostgreSQL, nicht getestet | nur Profile und Unit-Tests; **nicht live getestet** | vollständige Site-Datenbank wie PostgreSQL; Profile neu in 6.0.15 |
| IBM Db2 | 11.5 und 12.1 (11.5.9 und 12.1.6 getestet) | `pdo_ibm` oder `ibm_db2` mit IBMs Data Server Driver | Setup-Assistent, Kickstarter, `exp:install` (`db2`) | noch keiner: ein Webserver | Treiber- und Live-Tests auf Db2 12.1 und 11.5, auf beiden PHP-Schichten, und eine ganz installierte Site | vollständige Site-Datenbank (neu in 6.0.15); [was geht und was nicht](../funktionen/6.0/db2-datenbanktreiber.md#was-geht-und-was-nicht) |
| IBM Informix | 14.10 und 15 (14.10.FC9W1 und 15.0.1 getestet) | `ibm_db2` mit IBMs Data Server Driver, über den DRDA-Listener | Setup-Assistent, Kickstarter, `exp:install` (`informix`) | noch keiner: ein Webserver | Treiber- und Live-Tests auf Informix 15 und 14.10; eine ganze Site außerhalb der CI auf 15 installiert | vollständige Site-Datenbank (neu in 6.0.15); [was geht und was nicht](../funktionen/6.0/informix-datenbanktreiber.md#was-geht-und-was-nicht) |

**In Entwicklung.** Treiber für Microsoft Access und für Datenbanken, die über eine allgemeine ODBC-Verbindung erreicht
werden, entstehen gerade; sie sind noch in keinem Release enthalten.

**Firebird** (neu in 6.0.15, nicht in der Reihenfolge oben): Firebird 4.0 und 5.0 tragen eine ganze Site mit
`expFirebirdDB` aus dem Kernel (`DatabaseImplementation=firebird`, pdo_firebird ab PHP 8.4, `exp:install
--db=firebird`), bei jedem Push gegen beide Versionen getestet. Ein DFS-Cluster-Backend gibt es dafür noch nicht und
keine Lesereplikate: eine Site mit Firebird läuft auf einem Webserver. Einzelheiten und Grenzen:
[Firebird-Treiber](../funktionen/6.0/firebird-datenbanktreiber.md).

**DuckDB** (`DatabaseImplementation=duckdb`, `exp:install --db=duckdb`) hält eine ganze Site in einer Datei, erreicht
über PHPs FFI-Erweiterung. DuckDB lässt immer nur einen Prozess schreiben; es passt daher zu kleinen Sites, die vor allem
gelesen werden, zu einzelnen Redakteuren, Build- und Demo-Sites; einen DFS-Cluster gibt es darauf nicht. Siehe
[DuckDB-Treiber](../funktionen/6.0/duckdb-datenbanktreiber.md).

**IBM Db2 und IBM Informix** (neu in 6.0.15, nicht in der Reihenfolge oben): `expDb2DB` aus dem Kernel
(`DatabaseImplementation=db2`, Db2 11.5 und 12.1) und `expInformixDB` (`DatabaseImplementation=informix`, Informix 14.10
und 15 über dessen DRDA-Listener) erreichen ihre Server über IBMs PECL-Erweiterungen `pdo_ibm` und `ibm_db2`
(`exp:install --db=db2`, `--db=informix`), bei jedem Push gegen beide Versionen jeder Datenbank getestet. Eine ganze Site installiert sich und läuft auf beiden
(Db2 12.1 und Informix 15, gemessen).
Ein DFS-Cluster-Backend gibt es dafür nicht und keine Lesereplikate. Einzelheiten, die Vorbereitung des Servers und
Grenzen: [IBM-Db2-Treiber](../funktionen/6.0/db2-datenbanktreiber.md),
[IBM-Informix-Treiber](../funktionen/6.0/informix-datenbanktreiber.md).

Eine bestehende Website von einer Engine auf eine andere umziehen: [Weg von einer alten Datenbank](weg-von-einer-alten-datenbank.md).

## Auf einen Blick

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL | 7. SQL Server |
|---|---|---|---|---|---|---| --- |
| `DatabaseImplementation` | `sqlite3` | `ezpostgresql` | `mongodb` | `ezmysqli` | `ezoracle` | `ezmysqli` | `sqlserver` |
| Treiberklasse | `eZSQLite3DB` | `eZPostgreSQLDB` | `expMongoDB` | `eZMySQLiDB` | `eZOracleDB` | `eZMySQLiDB` | `expSQLServerDB` |
| PHP-Erweiterung | `sqlite3` | `pgsql` | `mongodb` + Bibliothek `mongodb/mongodb` | `mysqli` | `oci8` | `mysqli` | `sqlsrv`, `pdo_sqlsrv`, `pdo_odbc` oder `pdo_dblib` |
| Datenbankserver | keiner (eine Datei) | ja | ja | ja | ja | ja | ja |
| Standard der Installer | ja | | | | | |  |
| Setup-Assistent | ja | ja | ja (langsamer Schritt) | ja | ja | ja | nein (Kickstarter und `exp:install`) |
| `exp:install --db=` | `sqlite` (Standard) | `pgsql` | `mongodb` | `mariadb` | `oracle` | `mysql` | `sqlserver` |
| Mehrere Webserver an einer Datenbank | nein | ja | ja | ja | ja | ja | ja |
| Zeichensatz | UTF-8 | `UTF8` | UTF-8 (BSON) | `utf8mb4` | `AL32UTF8` | `utf8mb4` | UTF-8-Kollation (`_UTF8`, 2019 und neuer) oder `NVARCHAR` |
| Dateien für Schema-Updates | `update/database/sqlite/` | `update/database/postgresql/` | keine (kein SQL) | `update/database/mysql/` | in `ezoracle` | `update/database/mysql/` | noch keine (das Schema ist `kernel/sql/sqlserver/`) |
| In der CI des Projekts getestet | Unit-Tests des Treibers | ja (PostgreSQL 13, 15, 18 und 19) | Unit-Tests des Adapters | nein | nein | ja (MySQL 8.0) | ja (2017, 2019, 2022, 2025; LocalDB unter Windows) |

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
| PostgreSQL | 8.0 | **13** (der Installer legt `pgcrypto` an, ab PostgreSQL 13 eine vertrauenswürdige Erweiterung); ab 15 muss der Benutzer der Site Eigentümer der Datenbank sein | 13.23 auf dem Entwicklungsserver; 13, 15, 18 und 19 in der CI (alle Suiten), 13 bis 19 für das DFS-Cluster-Backend; 18.7 und 19.0 zusätzlich geprüft für Installation, Upgrade-Sicherung, Datenbankstufe des Upgrades, Umstellung von MySQL auf PostgreSQL und Lesereplikat |
| MongoDB | 4.0 | Server **6.0**, PHP **8.2** oder neuer, PHP-Erweiterung `mongodb` **1.18** oder neuer ([MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md)) | Server 8.3.13 mit Erweiterung 2.3.3 auf PHP 8.5 auf dem Entwicklungsserver |
| MariaDB | 4.1.1 (die MySQL-Nummer) | ein Release, das Ihr Anbieter noch unterstützt; `utf8mb4` und InnoDB | 10.5.29 auf dem Entwicklungsserver |
| Oracle | 19.0 | **19c** (der Treiber verwendet `STANDARD_HASH` und `OFFSET`/`FETCH`; 11g und älter werden nicht unterstützt) | Oracle AI Database 26ai Free (23.26) mit oci8 3.4.1 und Instant Client 23.26 auf PHP 8.5 |
| MySQL | 4.1.1 | ein Release, das Ihr Anbieter noch unterstützt; `utf8mb4` und InnoDB | 8.0 in der CI |

Verwenden Sie eine Serverversion, für die Ihr Anbieter noch Sicherheitskorrekturen liefert. Die Untergrenzen gibt es,
damit der Assistent einen alten Server nicht von vornherein ablehnt; sie sind kein Versprechen, dass ein Server dieses
Alters mit den aktuellen Treibern funktioniert.

**PHP.** Exponential 6.0.x läuft auf PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) (`composer.json`, `"php": "^7.4 || ... || ^8.8"`). Die
Datenbanktreiber folgen diesem Bereich mit einer Ausnahme: Der MongoDB-Adapter braucht PHP 8.2 oder neuer. Velocity,
der empfohlene Webserver, braucht PHP 8.0. Siehe [Kapitel 2](../installation/02-voraussetzungen.md#22-php).

## PHP-Erweiterungen

Eine Datenbankerweiterung ist erforderlich; der kritische Test `database_extensions` des Setup-Assistenten ist
bestanden, wenn mindestens eine von `sqlite3`, `mysqli`, `pgsql`, `mongodb`, `oci8`, `sqlsrv` und `pdo_sqlsrv` geladen
ist (`settings/setup.ini`, `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8;sqlsrv;pdo_sqlsrv`,
`Require=one`).
Installieren Sie nur die, die Ihre Site verwendet.

| Datenbank | Erweiterung | Woher sie kommt |
|---|---|---|
| SQLite 3 | `sqlite3` | in PHP enthalten; RHEL-Familie `php-pdo` (enthält auch `pdo_sqlite`), Debian und Ubuntu `php<version>-sqlite3`. `pdo_sqlite` braucht der Kernel nicht; die Installer von Exponential Platform verwenden es. |
| PostgreSQL | `pgsql` | in PHP enthalten; `php-pgsql`, `php<version>-pgsql` |
| MongoDB | `mongodb` (PECL) und die Composer-Bibliothek `mongodb/mongodb` | `pecl install mongodb` oder `php-pecl-mongodb`; die Bibliothek mit `composer require mongodb/mongodb` (`composer.json` schlägt sie nur vor). Die CI installiert `mongodb/mongodb` `^1.21 || ^2.0`. |
| MariaDB, MySQL | `mysqli` | in PHP enthalten (mysqlnd); `php-mysqlnd`, `php<version>-mysql` |
| SQL Server, Azure SQL | `sqlsrv` oder `pdo_sqlsrv` (PECL, Microsoft), oder `pdo_odbc` mit Microsofts ODBC Driver 17 oder 18, oder `pdo_dblib` (FreeTDS) | Microsofts Pakete für Linux und seine DLLs für Windows; `pdo_odbc` und `pdo_dblib` gehören zu PHP ([Schichten](../funktionen/6.0/sqlserver-datenbanktreiber.md)) |
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
| `duckdb` | `expDuckDBDB` (`lib/ezdb/classes/expduckdbdb.php`) |
| `db2`, `ibm_db2` | `expDb2DB` (`lib/ezdb/classes/expdb2db.php`) |
| `informix`, `ifx` | `expInformixDB` (`lib/ezdb/classes/expinformixdb.php`) |

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

Exponential hat drei Installer im Kernel. Alle drei unterstützen die ersten sechs Datenbanken; Microsoft SQL Server wird
mit dem Kickstarter und `exp:install` installiert.

| Datenbank | Setup-Assistent (Browser) | Kickstarter (`kickstart.ini`, `[database_choice] Type=`) | `exp:install --db=` |
|---|---|---|---|
| 1. SQLite 3 | ja, als Erstes aufgeführt und vorgewählt (`settings/setup.ini [DatabaseSettings] DefaultType=sqlite3`); schlägt `sqlite.db` vor | `sqlite3` (der Standard in `kickstart.ini-dist`) | `sqlite` (der Standard; auch `sqlite3`), Datei `exponential.db` |
| 2. PostgreSQL | ja; füllt Port 5432 und Benutzer `postgres` vor; legt `pgcrypto` an oder sagt Ihnen, wie | `pgsql` (`postgresql` wird akzeptiert) | `pgsql` (auch `postgresql`, `postgres`) |
| 3. MongoDB | ja; der Konfigurationsschritt kann eine bis drei Minuten dauern, erhöhen Sie also `request_terminate_timeout` von PHP-FPM oder verwenden Sie die Konsole | `mongodb` | `mongodb` (auch `mongo`) |
| 4. MariaDB | ja, als „MySQL Improved“; prüft, ob die Datenbank UTF-8 ist | `mysqli` (`mysql` wird akzeptiert) | `mariadb` (dasselbe wie `mysql`) |
| 5. Oracle | ja, wenn `oci8` geladen und `extension/ezoracle` vorhanden ist; aktiviert die Erweiterung für die neue Site | `oci8` (`oracle` wird akzeptiert); `Database` ist der Connect-String | `oracle` (auch `oci8`, `ezoracle`); `--db-name` ist der Dienstname, Standard `FREEPDB1` |
| 6. MySQL | ja, als „MySQL Improved“ | `mysqli` (`mysql` wird akzeptiert) | `mysql` (auch `mysqli`) |
| 7. SQL Server, Azure SQL | | `sqlserver` (`mssql`, `sqlsrv` werden akzeptiert), mit `Encrypt` und `TrustServerCertificate` | `sqlserver` (auch `mssql`, `sqlsrv`); `--db-encrypt`, `--db-trust-server-certificate`; die Datenbank muss existieren |
| CockroachDB, YugabyteDB, Neon, Supabase, Aurora | ja, nach PostgreSQL aufgeführt, wenn `pgsql` geladen ist | `cockroachdb`, `yugabytedb`, `neon`, `supabase`, `aurorapostgresql`, mit `SSLMode`, `SSLRootCert`, `Pooler`, `Endpoint` | dieselben Namen; `--db-sslmode`, `--db-sslrootcert`, `--db-pooler`, `--db-endpoint` |

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
Lesezugriffe an ein Replikat, das pro Anfrage zufällig gewählt wird; Schreibzugriffe gehen an `Server`. Seit 6.0.15 folgen
PostgreSQL, MySQL, MariaDB und Oracle (ezoracle) einer Regel: nur einfache SELECTs anonymer GET-/HEAD-Anfragen, die
noch nichts geschrieben haben, und nur von einem Replikat, das aktuell ist, sodass eine Änderung nie veraltet
zurückgelesen wird; SQLite hat kein Replikat ([Lesereplikate](../bc/6.0/postgresql-lesereplikat.md)).

**Grenzen.** Der Verbindungszeichensatz `utf-8` wird auf MySQLs `utf8` (drei Bytes) abgebildet, sodass Zeichen
außerhalb der Basic Multilingual Plane, etwa Emoji, nicht durch die Verbindung gelangen, selbst wenn die Tabellen
`utf8mb4` sind.

Einzelheiten: [9.3 MySQL und MariaDB](../installation/09-datenbanken.md#93-mysql-und-mariadb).

<a id="microsoft-sql-server-möglich-mit-zusätzlichem-aufwand"></a>

## 7. Microsoft SQL Server und Azure SQL

Exponential 6.0.15 hat einen eigenen SQL-Server-Treiber im Kernel, `expSQLServerDB`
(`DatabaseImplementation=sqlserver`; `mssql` und `sqlsrv` bezeichnen denselben Treiber), neu unter der GPL geschrieben,
mit seinem Schema-Handler `expSQLServerSchema`, dem Kernel-Schema in `kernel/sql/sqlserver/`, Einträgen in den
Installern und einem DFS-Cluster-Backend.

- **Server**: SQL Server 2017, 2019, 2022 und 2025, Azure SQL Database und Azure SQL Managed Instance (vorübergehende
  Fehler werden wiederholt). Azure SQL Edge, das Microsoft eingestellt hat, und SQL Server 2016 und älter werden nicht
  unterstützt.
- **PHP-Schichten**: Microsofts `sqlsrv` (empfohlen) oder `pdo_sqlsrv`, PHPs `pdo_odbc` mit ODBC Driver 17 oder 18
  oder `pdo_dblib` über FreeTDS; `[SQLServerSettings] Layer=auto` nimmt die erste, die geladen ist. Unter Linux und
  unter Windows.
- **Installieren**: Legen Sie zuerst die Datenbank an (eine UTF-8-Kollation, `READ_COMMITTED_SNAPSHOT`), dann
  `exp:install --apply --db=sqlserver` oder der Kickstarter mit `Type=sqlserver`. Gemessen auf SQL Server 2022: 16
  Schritte, keine Fehler, 323 Inhaltsobjekte.
- **Clustering**: `expDFSFileHandlerSQLServerBackend` hält die DFS-Metadaten auf SQL Server oder Azure SQL für
  beliebig viele Webserver; `exp:cluster:enable --backend=sqlserver` richtet einen Cluster mit einem Knoten ein.
- **Lesereplikate**: Always-On-Verfügbarkeitsgruppen mit `ApplicationIntent=ReadOnly`.
- **Getestet**: bei jedem Push auf SQL Server 2017, 2019, 2022 und 2025 mit `sqlsrv`, `pdo_sqlsrv` und `pdo_odbc` und
  PHP 8.1 bis 8.5 sowie auf SQL Server LocalDB unter Windows.

Einzelheiten: [Microsoft-SQL-Server-Treiber](../funktionen/6.0/sqlserver-datenbanktreiber.md) und
[Clustering auf SQL Server](../funktionen/6.0/sqlserver-dfs-cluster.md).

### Datenbanken mit dem PostgreSQL-Protokoll

Sechs Datenbanken, die das Protokoll von PostgreSQL sprechen, laufen mit einem Treiber, `expPostgreSQLWireDB` (neu in
6.0.15), einer Unterklasse des PostgreSQL-Treibers mit einem Profil je Datenbank. Für den Kernel ist jede von ihnen
PostgreSQL (`databaseName()` ist `postgresql`); das Profil ändert, was anders sein muss: Sperren, Wiederholungen,
Sequenzen, DDL, Pooler, TLS und Kaltstarts.

| Datenbank | `DatabaseImplementation` | Stufe | Live getestet | Geht | Geht nicht oder anders |
|---|---|---|---|---|---|
| CockroachDB 23.2+ | `cockroachdb` | vollständige Site-Datenbank | ja, 25.2 | Kernel-Schema und ein Site-Paket, Inhalte, Administration; `READ COMMITTED`; Sequenzen; Datentabellen-Verbindungen | kein `LOCK TABLE` (Zeilensperren in `exp_wire_lock`); Advisory Locks sperren nichts; `SERIALIZABLE`-Transaktionen müssen eventuell wiederholt werden (`40001`); langsames DDL |
| YugabyteDB 2.20+ | `yugabytedb` | vollständige Site-Datenbank | ja, 2.25 | Kernel-Schema und ein Site-Paket, Inhalte, Administration; Sequenzen, temporäre Tabellen; Datentabellen-Verbindungen | kein `LOCK TABLE ... ACCESS EXCLUSIVE` (Zeilensperren); Advisory Locks nur als Preview-Flag; kein pgcrypto; IDs nicht in Einfügereihenfolge; langsames DDL |
| Google Cloud Spanner (PostgreSQL-Dialekt) | `spanner` | Datentabellen-Verbindung und Import | ja, der Emulator hinter PGAdapter | Tabellen, Indizes, Upserts, 64-Bit-IDs aus bit-umgekehrten Sequenzen (`RETURNING`), persistente Klassen auf der Verbindung | keine Site: keine gewöhnlichen Sequenzen, keine temporären Tabellen, kein `LOCK`, keine Advisory Locks, kein `md5()`, kein `smallint` |
| Neon | `neon` | vollständige Site-Datenbank (PostgreSQL) | **nein** | wie PostgreSQL; Kaltstarts werden wiederholt; TLS | der `-pooler`-Host ist Transaktionsmodus: kein Sitzungszustand |
| Supabase | `supabase` | vollständige Site-Datenbank (PostgreSQL) | **nein** | wie PostgreSQL; TLS; Supavisor im Sitzungsmodus | Port 6543 ist Transaktionsmodus; der direkte Host nur über IPv6 |
| Amazon Aurora PostgreSQL | `aurorapostgresql` | vollständige Site-Datenbank (PostgreSQL) | **nein** | wie PostgreSQL; Neuverbindung beim Failover außerhalb von Transaktionen; Reader-Endpunkt als Replikat | eine Transaktion während eines Failovers ist verloren; IAM-Tokens laufen ab |

Einzelheiten: [Datenbanken mit dem PostgreSQL-Protokoll](../funktionen/6.0/postgresql-wire-datenbanken.md).

## Datenbanken, die das MySQL-Protokoll sprechen

TiDB, Vitess und PlanetScale, SingleStore, MariaDB Xpand und Amazon Aurora MySQL laufen ab 6.0.15 auf eigenen
Treibern: jeder ist der MySQL-Treiber mit einem Profil dessen, was seine Datenbank anders macht (`expTiDBDB`,
`expVitessDB`, `expSingleStoreDB`, `expXpandDB`, `expAuroraMySQLDB`; `DatabaseImplementation` `tidb`, `vitess`,
`singlestore`, `xpand`, `aurora`). Sie brauchen `mysqli`, installieren sich mit `exp:install --db=<name>` oder dem
Kickstarter und nutzen das MySQL-DFS-Backend.

| Datenbank | Erreichte Stufe | Geht | Geht nicht oder anders |
|---|---|---|---|
| TiDB | ganze Site, gemessen auf 8.5.1 | eine ganze Site, DFS-Cluster, Fremdschlüssel, Savepoints | `READ UNCOMMITTED`, `SERIALIZABLE`, `XA`; Lesesperren werden `FOR UPDATE`; `LOCK TABLES` wird `GET_LOCK()`; kein Volltextindex |
| Vitess, PlanetScale | ganze Site in einem ungesplitteten Keyspace, gemessen auf Vitess 25 | eine ganze Site, DFS-Cluster, alles, was MySQL 8 kann | keine Fremdschlüssel (weggelassen); Transaktionen werden nach der Zeitgrenze des Tablets abgebrochen (PlanetScale 20 s): mit einer längeren installieren; gesplittete Keyspaces nicht für eine Site |
| SingleStore | ganze Site, gemessen auf 10.10 | eine ganze Site (Referenztabellen), DFS-Cluster, Zeitstempel nach 2038 | keine Fremdschlüssel, kein `FULLTEXT` auf Rowstore, `SAVEPOINT` tut nichts, nur `READ COMMITTED`, kein `GET_LOCK()` |
| MariaDB Xpand | Treiber und Profil, nicht gemessen | erwartet: wie MySQL | kein `FULLTEXT`; IDs nicht in Einfügereihenfolge |
| Aurora MySQL | Treiber und Profil, nicht gemessen | erwartet: wie MySQL 8.0 | nach einem Failover verbindet sich der Treiber neu, wenn der Writer nur noch lesbar ist |

Einzelheiten, die Einstellungen (`[MySQLWireSettings]`) und jede Regel der Treiber:
[Datenbanken, die das MySQL-Protokoll sprechen](../funktionen/6.0/mysql-wire-datenbanken.md).

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
  behält jeder Worker seine Verbindung ohnehin. MySQL, MariaDB, PostgreSQL und Oracle können Lesezugriffe
  an Replikate schicken (`UseSlaveServer`).
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

Die Schritt-für-Schritt-Fassung ist [9.9 Eine bestehende Site auf eine andere Engine umstellen](../installation/09-datenbanken.md#99-eine-bestehende-site-auf-eine-andere-engine-umstellen);
warum und wann Sie umziehen und auf welche Engine: [Weg von einer alten Datenbank](weg-von-einer-alten-datenbank.md).

## Testabdeckung (CI)

Was die Continuous Integration des Projekts (`.github/workflows/`) bei jedem Push ausführt, je Datenbank.
„Unit-Tests des Treibers“ bedeutet Tests, die eine echte Datenbankdatei öffnen oder das SQL des Treibers ohne Server
testen; „Datenbanktests“ bedeutet die Tests des Legacy-Toolkits, die gegen einen laufenden Server mit einer
Wegwerf-Datenbank pro Test laufen.

| Datenbank | In der CI | Wie |
|---|---|---|
| 1. SQLite 3 | ja, Unit-Tests des Treibers | der PHPUnit-Job auf PHP 8.1, 8.2, 8.3, 8.4 und 8.5 lädt `sqlite3`; die SQLite-Tests in `tests/tests/lib/ezdb/` prüfen den Treiber, teils auf einer In-Memory- oder temporären Datenbank (Anzahl der Abfragen, das Schema-Spiegelbild von `kernel/sql/sqlite/`), teils durch Lesen seiner Einstellungen (die Wartezeit für Transaktionen) |
| 2. PostgreSQL | ja, Datenbanktests | die Jobs „Database tests (postgresql 13, 15, 18 und 19, PHP 8.3)“ mit einem `postgres:<Version>`-Service, dem PGDG-Client dieser Version und `pgcrypto`, `tests/runtests.php --dsn postgresql://... --db-per-test` |
| 3. MongoDB | Unit-Tests des Adapters | der PHPUnit-Job lädt `mongodb` und installiert `mongodb/mongodb`; die Suite `mongodb` (Adaptertests ohne Server) läuft; die Gruppe `mongodb-live` braucht laufende MongoDB- und MySQL-Server und ist von der CI ausgenommen |
| 4. MariaDB | nein | indirekt durch den MySQL-Job abgedeckt (derselbe Treiber) |
| 5. Oracle | das DFS-Cluster-Backend | der Job „cluster-oracle“ führt die Tests des Oracle-DFS-Backends gegen Oracle Free 23 mit `oci8` aus; Oracle-spezifische Kernel-Tests werden auf anderen Engines übersprungen |
| 6. MySQL | ja, Datenbanktests | der Job „Database tests (mysql, PHP 8.3)“ mit einem `mysql:8.0`-Service |
| Firebird | ja, Treiber-, Live- und Kernel-Schema-Tests | der Job „firebird“ mit `firebirdsql/firebird` 5 und 4 als Service-Container und `pdo_firebird`, PHP 8.4 und 8.5 |
| IBM Db2, IBM Informix | ja, Treiber-, Live- und Kernel-Schema-Tests, eine ganze Site auf Db2 | der Job „db2“ mit Db2 Community Edition 12.1 und 11.5 und Informix Developer Edition 15 und 14.10 als Container, `pdo_ibm` und `ibm_db2`, PHP 8.5 und 8.3 |
| 7. SQL Server | ja, Treiber-, Live- und DFS-Tests | der Job „sqlserver“ mit `mcr.microsoft.com/mssql/server` 2017, 2019, 2022 und 2025 und ODBC Driver 18, auf `sqlsrv`, `pdo_sqlsrv` und `pdo_odbc` und PHP 8.1 bis 8.5; der Job „SQL Server LocalDB smoke“ unter Windows mit PHP 8.4 |
| PostgreSQL-Protokoll | ja, Treiber- und Live-Tests | die Testsuite `pgwire` im PHPUnit-Job; der Job „PostgreSQL wire protocol“ führt `pgwire-live` gegen CockroachDB 25.2, YugabyteDB 2.25 und den Spanner-Emulator hinter PGAdapter aus, PHP 8.1 und 8.5. Neon, Supabase und Aurora: nur Unit-Tests |

PHP 8.0, die älteste unterstützte Version, und PHP 7.4 (ohne Support nutzbar) prüft der Quality-Workflow mit einer Syntaxprüfung jeder PHP-Datei und einem
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
- Im Code: `eZDB::instance()->databaseName()` liefert `sqlite`, `postgresql`, `mongo`, `mysql`, `oracle` oder `SQLServer`.

## Verwandte Seiten

- [Kapitel 9, Datenbanken](../installation/09-datenbanken.md): die Referenz für den Betrieb jeder Engine.
- [Kapitel 2, Anforderungen](../installation/02-voraussetzungen.md#26-datenbanken) und [Unterstützte Betriebssysteme](betriebssystemunterstuetzung.md).
- [Weg von einer alten Datenbank](weg-von-einer-alten-datenbank.md), [Exponential unter Windows](exponential-unter-windows.md).
- [Microsoft-SQL-Server-Treiber](../funktionen/6.0/sqlserver-datenbanktreiber.md),
  [Datenbanken mit dem PostgreSQL-Protokoll](../funktionen/6.0/postgresql-wire-datenbanken.md),
  [Clustering in Exponential 6.0](../funktionen/6.0/clustering-ueberblick.md).
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
