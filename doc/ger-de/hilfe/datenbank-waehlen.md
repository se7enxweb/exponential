---
title: Eine Datenbank wählen
audience: admin, developer, dba
category: install-upgrade
order: 9
summary: Jede Datenbank, die Exponential 6.0.15 erreicht, in einer Matrix (Stand in 6.0.14 und 6.0.15, Site-Datenbank, DFS-Cluster, Lesereplikate, Installer, Datentabellen-Engine, Importquelle, PHP-Erweiterung, Betriebssystem, CI, getestete Aktualisierungen), mit den gemessenen Zeiten und einer Entscheidungshilfe je Einsatzzweck - ein Server, ein Cluster, eine verwaltete Cloud-Datenbank, eingebettete und Demo-Sites, Auswertungen und Importe, Windows sowie der Umzug weg von MS Access oder einer anderen alten Datenbank.
keywords: Datenbank wählen, Datenbankmatrix, welche Datenbank, choosing a database, SQLite, PostgreSQL, MongoDB, MariaDB, MySQL, Oracle, Microsoft SQL Server, Azure SQL, TiDB, Vitess, PlanetScale, SingleStore, MariaDB Xpand, Aurora MySQL, CockroachDB, YugabyteDB, Spanner, PGAdapter, Neon, Supabase, Aurora PostgreSQL, Firebird, DuckDB, libSQL, sqld, Turso, ODBC, MS Access, exp:access:import, Cassandra, ScyllaDB, CQL, exp:cassandra:import, Datentabellen, sqliimport, DFS-Cluster, Lesereplikate, xne, Aktualisierung, Windows, pdo_firebird, FFI, sqlsrv, pdo_sqlsrv, pdo_odbc, odbc, Couchbase, SQL++, N1QL, CouchDB, Mango, exp:couchbase:import, exp:couchdb:import, SAP HANA, SAP ASE, Sybase
featured: true
context: setup/info, setup/systemupgrade
type: help
---
# Eine Datenbank wählen

Diese Seite richtet sich an Administratoren, Datenbankadministratoren und Entwickler, die entscheiden, auf welcher
Datenbank eine neue Exponential-Site läuft, oder ob sich eine vorhandene Datenbank erreichen lässt. Exponential 6.0.14
lief auf fünf Datenbanken; 6.0.15 erreicht mehr als zwanzig, auf verschiedenen Stufen: Manche tragen eine ganze Site,
manche halten nur Datentabellen und liefern Importe, manche sind nach der Dokumentation ihres Herstellers geschrieben
und liefen hier noch nie. Diese Seite stellt alle nebeneinander, sagt ehrlich, was getestet wurde und was nicht, und
beantwortet die Frage dann je Einsatzzweck.

Jede Aussage hier stammt aus dem Code der Linie 6.0.15 und aus den Seiten der Treiber selbst. Einzelheiten,
Einstellungen und Grenzen jeder Datenbank stehen auf ihrer eigenen Seite, die in den Tabellen verlinkt ist.
[Datenbankunterstützung](datenbankunterstuetzung.md) ist die Aussage zur Unterstützung mit Versionen und
Einstellungen; das Kapitel für den Betrieb ist [Kapitel 9 der Installationsanleitung](../installation/09-datenbanken.md). Die Unterstützungsstufe
jeder Datenbank in einer Tabelle (Datentabellen, ganze Website, DFS, Cluster mit mehreren Servern, Setup-Assistent,
`exp:install` und Kickstarter, xne-Upgrade getestet), dieselbe Tabelle, die network.se7enx.com und se7enx.com zeigen,
steht unter [Unterstützungsstufen auf einen Blick](datenbankunterstuetzung.md#unterstützungsstufen-auf-einen-blick).

## Inhalt

1. [Die Stufen](#die-stufen)
2. [Die Matrix: Stand und Einsatz](#die-matrix-stand-und-einsatz)
3. [Die Matrix: Voraussetzungen und Tests](#die-matrix-voraussetzungen-und-tests)
4. [Sicherungen und Sicherungspunkte vor Aktualisierungen](#sicherungen-und-sicherungspunkte-vor-aktualisierungen)
5. [Gemessene Zeiten](#gemessene-zeiten)
6. [Welche Datenbank für welchen Zweck](#welche-datenbank-für-welchen-zweck)
7. [Geplant](#geplant)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Stufen

| Stufe | Bedeutung |
|---|---|
| **Site-Datenbank** | die Installer legen darin das Kernel-Schema und ein Site-Paket an, und die Site läuft daraus |
| **Site-Datenbank, nicht gemessen** | Treiber und Profil gibt es, und sie bestehen ihre Tests ohne Server, aber hier wurde noch nie eine Site auf der Datenbank installiert (kein Container-Image, oder ein verwalteter Dienst, bei dem sich niemand angemeldet hat) |
| **Datentabellen und Import** | eine [Datentabellen-Verbindung](../funktionen/6.0/datentabellen.md) kann darauf Tabellen anlegen, lesen und schreiben, und Daten lassen sich daraus importieren; eine Site trägt sie nicht |
| **Nur Import** | Daten werden daraus in eine Datenbank kopiert, die die Site trägt |

Die offizielle Reihenfolge der unterstützten Datenbanken bleibt, wie [Datenbankunterstützung](datenbankunterstuetzung.md)
sie angibt: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft
SQL Server und Azure SQL. Die übrigen Datenbanken von 6.0.15 folgen danach, nach Familien geordnet.

## Die Matrix: Stand und Einsatz

„6.0.14“ sagt, ob das vorige Release die Datenbank überhaupt hatte. „Installer“ nennt den Setup-Assistenten im
Browser, den Kickstarter (`kickstart.ini`) und `exp:install`
([Installation über die Konsole](../installation/07-installation-ueber-die-konsole.md)); der Wert ist der Name für
`--db=`. „DFS-Cluster“ heißt: mehrere Webserver auf einer Datenbank, mit den Metadaten der Dateien darin
(`exp:cluster:enable`, [Clustering in Exponential 6.0](../funktionen/6.0/clustering-ueberblick.md)).

| Datenbank | 6.0.14 | 6.0.15 | Site-Datenbank | DFS-Cluster | Lesereplikate (`UseSlaveServer`) | Installer |
|---|---|---|---|---|---|---|
| [SQLite 3](../funktionen/6.0/sqlite-datenbank.md) | ja | ja, der Standard | ja | nur ein Rechner (ein Cluster aus einem Knoten) | nein | Assistent, Kickstarter, `exp:install` (`sqlite`, der Standard) |
| [PostgreSQL 13 bis 19](../funktionen/6.0/postgresql-datenbank.md) | ja | ja | ja | ja, im Kernel | ja | Assistent, Kickstarter, `pgsql` |
| [MongoDB 6.0 und neuer](../funktionen/6.0/mongodb-datenbankunterstuetzung.md) | ja | ja | ja, über einen Adapter | ja, im Kernel | das eigene Replica Set von MongoDB | Assistent, Kickstarter, `mongodb` |
| [MariaDB](../funktionen/6.0/mariadb-und-mysql-datenbank.md) | ja | ja | ja | ja (MySQLi-Backend) | ja | Assistent („MariaDB / MySQL Improved“), Kickstarter, `mariadb` |
| [Oracle 19c und neuer (`ezoracle`)](../funktionen/6.0/oracle-datenbank.md) | ja | ja | ja | ja, im Kernel | ja | Assistent, Kickstarter, `oracle` |
| [MySQL](../funktionen/6.0/mariadb-und-mysql-datenbank.md) | ja | ja | ja | ja (MySQLi-Backend) | ja | Assistent („MariaDB / MySQL Improved“), Kickstarter, `mysql` |
| [SQL Server 2017 bis 2025, Azure SQL](../funktionen/6.0/sqlserver-datenbanktreiber.md) | nein | neu | ja | ja, im Kernel | ja (Always On, `ApplicationIntent=ReadOnly`) | Assistent (mit `sqlsrv` oder `pdo_sqlsrv`), Kickstarter, `sqlserver` |
| [TiDB](../funktionen/6.0/mysql-wire-datenbanken.md) | nein | neu | ja | ja (MySQLi-Backend, gemessen) | der Mechanismus von MySQL, nicht getestet | Assistent, Kickstarter, `tidb` |
| [Vitess, PlanetScale](../funktionen/6.0/mysql-wire-datenbanken.md) | nein | neu | ja, in einem ungesplitteten Keyspace | ja (auf Vitess gemessen) | der Mechanismus von MySQL, nicht getestet | Assistent, Kickstarter, `vitess` |
| [SingleStore](../funktionen/6.0/mysql-wire-datenbanken.md) | nein | neu | ja | ja (gemessen) | der Mechanismus von MySQL, nicht getestet | Assistent, Kickstarter, `singlestore` |
| [MariaDB Xpand](../funktionen/6.0/mysql-wire-datenbanken.md) | nein | neu, **ungetestet** | nicht gemessen | nicht gemessen | nicht gemessen | Assistent, Kickstarter, `xpand` |
| [Amazon Aurora MySQL](../funktionen/6.0/mysql-wire-datenbanken.md) | nein | neu, **ungetestet** | nicht gemessen | nicht gemessen | der Reader-Endpunkt, nicht gemessen | Assistent, Kickstarter, `aurora` |
| [CockroachDB 23.2 und neuer](../funktionen/6.0/postgresql-wire-datenbanken.md) | nein | neu | ja | ja (PostgreSQL-Backend, gemessen) | nicht getestet | Assistent, Kickstarter, `cockroachdb` |
| [YugabyteDB 2.20 und neuer](../funktionen/6.0/postgresql-wire-datenbanken.md) | nein | neu | ja | ja (PostgreSQL-Backend, gemessen; kein Lock-Timeout) | nicht getestet | Assistent, Kickstarter, `yugabytedb` |
| [Neon](../funktionen/6.0/postgresql-wire-datenbanken.md) | nein | neues Profil, **ungetestet** | wie PostgreSQL, nicht gemessen | PostgreSQL-Backend, **ungetestet** | eine Read-Replica-Compute, nicht gemessen | Assistent, Kickstarter, `neon` |
| [Supabase](../funktionen/6.0/postgresql-wire-datenbanken.md) | nein | neues Profil, **ungetestet** | wie PostgreSQL, nicht gemessen | PostgreSQL-Backend, **ungetestet** | nicht gemessen | Assistent, Kickstarter, `supabase` |
| [Amazon Aurora PostgreSQL](../funktionen/6.0/postgresql-wire-datenbanken.md) | nein | neues Profil, **ungetestet** | wie PostgreSQL, nicht gemessen | PostgreSQL-Backend, **ungetestet** | der Reader-Endpunkt, nicht gemessen | Assistent, Kickstarter, `aurorapostgresql` |
| [Google Cloud Spanner, PostgreSQL-Dialekt](../funktionen/6.0/postgresql-wire-datenbanken.md) | nein | neu | **nein**: Datentabellen und Import | nein | nein | keiner |
| [Firebird 4.0 und 5.0](../funktionen/6.0/firebird-datenbanktreiber.md) | nein | neu | ja | **noch nicht** | nein | Assistent (mit `pdo_firebird`), Kickstarter, `firebird` |
| [DuckDB 1.5 und 2.0](../funktionen/6.0/duckdb-datenbanktreiber.md) | nein | neu | ja, immer nur ein schreibender Prozess | **nein** | nein | Assistent (mit `ffi` und `ffi.enable=true`), Kickstarter, `duckdb` |
| [libSQL (sqld), Turso](../funktionen/6.0/libsql-datenbanktreiber.md) | nein | neu; Turso **ungetestet** | ja (gemessen auf sqld 0.24) | **kein** Backend; mehrere Webserver teilen sich die Datenbank, die Dateien brauchen ein geteiltes Verzeichnis | nein (die Replikate von Turso antworten unter derselben URL) | Assistent (mit `curl`; ein Server oder der eingebettete Dateimodus), Kickstarter, `libsql`, `turso` |
| [IBM Db2 11.5 und 12.1](../funktionen/6.0/db2-datenbanktreiber.md) | nein | neu | ja | **noch nicht** | nein | Assistent (mit `pdo_ibm` oder `ibm_db2`), Kickstarter, `db2` |
| [IBM Informix 14.10 und 15](../funktionen/6.0/informix-datenbanktreiber.md) | nein | neu | ja (auf 15 gemessen) | **nein** | nein | Assistent (mit `ibm_db2` oder `pdo_ibm`), Kickstarter, `informix` |
| [MonetDB Dec2023 und später](../funktionen/6.0/spaltenorientierte-datenbanken.md#monetdb) | nein | neu | ja, immer nur ein Schreiber | **nein** | nein | Assistent (als Letzte aufgeführt; keine PHP-Extension nötig), Kickstarter, `monetdb` |
| [ClickHouse 24.8 und später](../funktionen/6.0/spaltenorientierte-datenbanken.md#clickhouse) | nein | neu | **nein**: Datentabellen und Import ([warum](../funktionen/6.0/spaltenorientierte-datenbanken.md#warum-keine-site-datenbank)) | nein | nein | keiner |
| [SAP HANA 2.0 ab SPS 05](../funktionen/6.0/hana-datenbanktreiber.md) | nein | neu | ja (an HANA Express 2.00.088 gemessen) | **nein** | nein | Assistent (mit `odbc` oder `pdo_odbc` und registriertem HANA-ODBC-Treiber von SAP), Kickstarter, `hana` |
| [SAP ASE 15.7 und 16.0](../funktionen/6.0/ase-datenbanktreiber.md) | nein | neu | **ungetestet**: geschrieben, kein Server zum Testen | **nein** | nein | Kickstarter, `ase` (vom Assistenten nicht angeboten) |
| [Jede Datenbank über ODBC](../funktionen/6.0/odbc-datenbanktreiber.md) | nein | neu | ja auf SQL Server, PostgreSQL, MariaDB/MySQL und Db2 ([eine Site über ODBC](../funktionen/6.0/odbc-datenbanktreiber.md#eine-site-über-odbc)); Datentabellen und Import auf jeder | ja: das von SQL Server und PostgreSQL über PDO_ODBC, das von MariaDB über `mysqli` | nein | Assistent, Kickstarter, `odbc` mit `--db-dialect` |
| [MS Access (`.accdb`, `.mdb`)](../funktionen/6.0/ms-access-unterstuetzung.md) | nein | neu | **nein**: Import, unter Windows auch Datentabellen | nein | nein | keiner (`exp:access:import`) |
| [Apache Cassandra 4.0 bis 5.0, ScyllaDB 2025.1](../funktionen/6.0/cassandra-scylladb-treiber.md) | nein | neu | **nein**: Datentabellen und Import ([warum](../funktionen/6.0/cassandra-scylladb-treiber.md#warum-keine-site-auf-cassandra-läuft)) | nein | nein | keiner (`exp:cassandra:import`) |
| [Couchbase 7.2 und neuer](../funktionen/6.0/couchbase-treiber.md) | nein | neu | **nein** (gemessen): Datentabellen und Import | nein | nein | keiner (`exp:couchbase:import`) |
| [Apache CouchDB 3.3 und 3.4](../funktionen/6.0/couchdb-treiber.md) | nein | neu | **nein** (gemessen): Datentabellen und Import | nein | nein | keiner (`exp:couchdb:import`) |

Anmerkungen zur Tabelle:

- **Der Setup-Assistent** führt eine Datenbank auf, wenn das PHP, das ihn ausführt, die Erweiterung der Datenbank hat:
  `sqlite3`, `mysqli` (mit TiDB, Vitess / PlanetScale, SingleStore, MariaDB Xpand und Aurora MySQL nach MySQL),
  `pgsql` (mit CockroachDB, YugabyteDB, Neon, Supabase und Aurora PostgreSQL nach PostgreSQL), `mongodb`, `oci8`,
  `sqlsrv` oder `pdo_sqlsrv` (SQL Server), `pdo_firebird`, `pdo_ibm` oder `ibm_db2` (Db2, Informix), `ffi`
  (DuckDB, nur mit `ffi.enable=true` und `libduckdb`) sowie `curl` (libSQL, mit einem Server oder im eingebetteten
  Dateimodus, und Turso danach), `pdo_odbc` oder `odbc` (SQL Server, PostgreSQL, MariaDB / MySQL und Db2 über ODBC,
  und SAP HANA, wenn SAPs HANA-ODBC-Treiber registriert ist). MonetDB braucht keine Extension und steht zuletzt, wann
  immer PHP einen Socket öffnen darf. SAP ASE wird nie angeboten. Die Reihenfolge seit dem 27. November 2026: MariaDB /
  MySQL Improved, PostgreSQL, SQLite (vorausgewählt, daher oben), MongoDB, Oracle; dann SQL Server, SQL Server über
  ODBC, Firebird und DuckDB; dann die Protokollfamilien; dann die später hinzugekommenen Datenbanken; MonetDB zuletzt.
  Jede Datenbank der Tabelle, die eine Site trägt, lässt sich auch mit dem Kickstarter oder `exp:install` installieren
  ([Setup-Assistent](../installation/05-setup-assistent.md#535-datenbanksystem-auswählen)).
- **DFS auf den Datenbanken mit dem MySQL-Protokoll**: Das Backend öffnet seine eigene `mysqli`-Verbindung ohne TLS. Bei
  PlanetScale, TiDB Cloud und SingleStore Helios, die TLS verlangen, liegen die DFS-Metadaten besser in einer anderen
  Datenbank.
- **DFS auf den Datenbanken mit dem PostgreSQL-Protokoll**: `exp:cluster:enable` gibt CockroachDB, YugabyteDB, Neon,
  Supabase und Aurora PostgreSQL das PostgreSQL-Backend, mit seinen Tabellen in der eigenen Datenbank der Site. Es hat
  die DFS-Prüfungen live auf CockroachDB 25.2 und YugabyteDB 2.25 bestanden; auf Neon, Supabase und Aurora lief es
  nicht gegen den Dienst.

## Die Matrix: Voraussetzungen und Tests

„Datentabellen-Engine“ ist das `Engine=` einer benannten Verbindung in `datatable.ini`
([Datentabellen](../funktionen/6.0/datentabellen.md)). „Importquelle“ sagt, wie Daten aus der Datenbank in eine Site
kommen: über die eigenen Befehle des Kernels, über eine Datentabellen-Verbindung oder über die Quellen der Extension
SQLI Import ([SQLI Import](sqli-import/README.md)). „xne-Aktualisierung“ ist der eigene Integrationstest des
Aktualisierungswerkzeugs: Er installiert eine echte Site mit 6.0.14, aktualisiert sie auf den aktuellen Code und
nimmt die Aktualisierung wieder zurück.

| Datenbank | PHP-Erweiterung | Datentabellen-Engine | Importquelle | Betriebssysteme | In der CI getestet | xne-Aktualisierung getestet |
|---|---|---|---|---|---|---|
| SQLite 3 | `sqlite3` | `sqlite` | die SQL-Quelle von SQLI Import (`pdo_sqlite`) | Linux, Windows (CI), macOS | Treibertests, PHP 8.1 bis 8.5; eine ganze Site unter Windows | **ja**: 6.0.14 auf main und zurück, unter Linux und im Windows-Job |
| PostgreSQL | `pgsql` | `postgresql` | SQL-Quelle (`pdo_pgsql`), benannte Verbindung | Linux, Windows | Datenbanktests auf 13, 15, 18, 19; DFS auf 13 bis 19 | **ja**: 6.0.14 auf main und zurück, und ein Fehler zurückgerollt |
| MongoDB | `mongodb` und die Bibliothek `mongodb/mongodb`; PHP 8.2 und neuer | `mongodb` | die MongoDB-Quelle von SQLI Import | Linux, Windows | Adaptertests; MongoDB 8.0 im DFS-Job | **ja**, ein Beweislauf: ein vollständiges Upgrade einer Kickstarter-Site und ein exaktes `xne rollback` ([Upgrade mit xne](../anleitungen/aktualisieren-mit-xne.md#datenbanken)) |
| MariaDB | `mysqli` | `mysql` | SQL-Quelle (`pdo_mysql`), benannte Verbindung | Linux, Windows | über die MySQL-Jobs (derselbe Treiber) | **ja**: 6.0.14 auf main und zurück, und ein Fehler zurückgerollt (die Integrationssuite von xne läuft auf MariaDB) |
| Oracle | `oci8` und die Extension `ezoracle` | `oracle` | SQL-Quelle (`oci8`), benannte Verbindung | Linux, Windows | das DFS-Backend auf Oracle Free 23 | **ja**, ein Beweislauf: ein vollständiges Upgrade einer Kickstarter-Site über `ezoracle` und ein exaktes `xne rollback` |
| MySQL | `mysqli` | `mysql` | SQL-Quelle (`pdo_mysql`), benannte Verbindung | Linux, Windows | Datenbanktests auf MySQL 8.0 | derselbe Treiber und Weg wie MariaDB, auf der die Integrationssuite von xne läuft |
| SQL Server, Azure SQL | `sqlsrv`, `pdo_sqlsrv`, `pdo_odbc` oder `pdo_dblib` | `sqlserver` | SQL-Quelle (`pdo_sqlsrv`, `pdo_dblib` oder PDO_ODBC), benannte Verbindung | Linux, Windows (LocalDB in der CI) | 2017, 2019, 2022, 2025 über drei PHP-Schichten, PHP 8.1 bis 8.5; LocalDB unter Windows | noch nicht (neu in 6.0.15) |
| TiDB, Vitess, SingleStore | `mysqli` | `tidb`, `vitess`, `singlestore` (oder `mysql` mit der Option `profile`) | benannte Verbindung der Engine `mysql` | Linux | Live-Tests in Containern (Job `mysqlwire`) | noch nicht |
| Xpand, Aurora MySQL | `mysqli` | `xpand`, `aurora` | benannte Verbindung der Engine `mysql` | Linux | nur Tests ohne Server | noch nicht |
| CockroachDB, YugabyteDB | `pgsql` | `cockroachdb`, `yugabytedb` | eine Datentabellen-Verbindung; SQL-Quelle über eine benannte Verbindung | Linux | Live-Tests in Containern (Job „PostgreSQL wire protocol“) | noch nicht |
| Neon, Supabase, Aurora PostgreSQL | `pgsql` | `neon`, `supabase`, `aurorapostgresql` | eine Datentabellen-Verbindung; SQL-Quelle über eine benannte Verbindung; Supabase auch die Supabase-Quelle (REST) von SQLI Import | Linux | nur Tests ohne Server | noch nicht |
| Spanner (PGAdapter) | `pgsql` | `spanner` | eine Datentabellen-Verbindung (`table_to_content`); SQL-Quelle über eine benannte Verbindung | Linux | Live-Tests gegen den Emulator hinter PGAdapter | entfällt |
| Firebird | `pdo_firebird` ab **PHP 8.4** und die Client-Bibliothek von Firebird | `firebird` | eine Datentabellen-Verbindung; SQL-Quelle über eine benannte Verbindung | Linux | Live-Tests auf Firebird 4 und 5, PHP 8.4 und 8.5 (Job `firebird`) | noch nicht |
| DuckDB | `ffi` (mit `ffi.enable=true` für das Web) und `libduckdb` | `duckdb` | eine Datentabellen-Verbindung; SQL-Quelle über eine benannte Verbindung; die DuckDB-Quelle von SQLI Import (Parquet-, CSV-, JSON- und DuckDB-Dateien) | Linux (`libduckdb.so`); Windows und macOS nicht getestet | Live-Tests auf DuckDB 1.5.6 und 2.0.1 und eine ganz installierte Site (Job `duckdb`) | noch nicht; kein eingebauter Sicherungspunkt (unten) |
| IBM Db2, IBM Informix | `pdo_ibm` oder `ibm_db2` (PECL) mit dem Data Server Driver von IBM; Informix braucht für eine Site `ibm_db2` | `db2`, `informix` | eine Datentabellen-Verbindung; SQL-Quelle über eine benannte Verbindung | Linux | Live-Tests auf Db2 12.1 und 11.5 sowie Informix 15 und 14.10 (Job `db2`) | noch nicht |
| SAP HANA | `odbc` (oder `pdo_odbc`, mit `odbc` für die Trigger des Schemas) und SAPs HANA-ODBC-Treiber | `hana` | eine Datentabellen-Verbindung | Linux | Live-Tests auf HANA Express 2.00.088, beide ODBC-Schichten (Job `hana`) | noch nicht |
| SAP ASE | `pdo_dblib` (FreeTDS) oder `pdo_odbc` | `ase` | eine Datentabellen-Verbindung (ungetestet) | keines getestet | nur Tests ohne Server (Job `hana`) | noch nicht |
| ODBC | `pdo_odbc` oder `odbc` und der ODBC-Treiber der Datenbank | `odbc` | eine Datentabellen-Verbindung; SQL-Quelle über eine benannte Verbindung; `exp:access:import` in sie hinein | Linux, Windows | Live-Tests auf SQL Server, PostgreSQL, MariaDB und SQLite, beide Schichten, ganze Sites installiert (Job `odbc`); LocalDB unter Windows | die Upgrade-Stufen über ODBC auf MariaDB gemessen (`exp:upgrade:database`); xne wie PostgreSQL oder MySQL |
| MS Access | Windows: `odbc` und `pdo_odbc` mit der Access Database Engine (x64); Linux: die Programme von MDBTools (nur Import) | `msaccess` (Windows) | `exp:access:import`; SQL-Quelle über eine benannte Verbindung (Windows) | Windows (Treiber und Import), Linux und macOS (Import mit MDBTools) | der Import unter Linux; Treiber und Import auf windows-2022 und windows-2025 (Job `msaccess-windows`) | entfällt |
| libSQL (sqld), Turso | `curl` (oder `allow_url_fopen`); `sqlite3` nicht nötig | `libsql`, `turso` | eine Datentabellen-Verbindung | Linux (sqld in einem Container); die PHP-Seite auf jedem System mit curl | Tests ohne Server, Live-Tests auf sqld 0.24.33 und eine ganze Site installiert (Job `libsql`); Turso nicht getestet | noch nicht; kein eingebauter Sicherungspunkt (unten) |
| MonetDB | keine: MAPI in reinem PHP | `monetdb` | eine Datentabellen-Verbindung (`table_to_content`) | Linux | Live-Tests auf Dec2023-SP4 und Dec2025-SP5 und eine ganz installierte Site (Job `columnar`) | noch nicht; kein eingebauter Sicherungspunkt (unten) |
| ClickHouse | keine: HTTP über `curl` oder PHPs HTTP-Stream | `clickhouse` | eine Datentabellen-Verbindung; die ClickHouse-Quelle von SQLI Import | Linux | Live-Tests auf 24.8 und 25.8 (Job `columnar`) | nicht anwendbar |
| Cassandra, ScyllaDB | PHP 8.1 und die Bibliothek `mroosz/php-cassandra` (keine Erweiterung) | `cassandra`, `scylladb` | eine Datentabellen-Verbindung; `exp:cassandra:import` eines Keyspace | Linux | Live-Tests auf Cassandra 5.0 und ScyllaDB 2025.1, PHP 8.1 und 8.5 (Job `cassandra`) | entfällt |
| Couchbase | `curl` (die REST-API des Query-Dienstes; kein SDK) | `couchbase` | `exp:couchbase:import`; eine Datentabellen-Verbindung | Linux | Live-Tests auf Couchbase Community 7.6.2 und 7.2.4 (Job `couch`) | entfällt |
| Apache CouchDB | `curl` und `sqlite3` | `couchdb` | `exp:couchdb:import`; eine Datentabellen-Verbindung | Linux | Live-Tests auf CouchDB 3.4.3 und 3.3.3 (Job `couch`) | entfällt |

Die Tests des Aktualisierungswerkzeugs sind in seiner eigenen Dokumentation beschrieben; eine Aktualisierung von 6.0.14
gibt es nur für die Datenbanken, die 6.0.14 hatte. Eine Site auf einer der neuen Datenbanken wird zum ersten Mal von dem
Release nach 6.0.15 aktualisiert, und dieser Weg ist auf keiner von ihnen bisher getestet.

## Sicherungen und Sicherungspunkte vor Aktualisierungen

Vor einer Aktualisierung oder einem Lauf der Migrationen legt der Aktualisierungsbefehl eine Sicherheitskopie der
Datenbank an (einen Sicherungspunkt, `exp:upgrade:backup`). Einen eingebauten Dump hat er nur für vier Engines; auf
jeder anderen Datenbank sichern Sie selbst mit dem Werkzeug der Datenbank und führen die Aktualisierung mit
`--no-backup` aus.

| Datenbank | Eingebauter Sicherungspunkt | Ihre eigene Sicherung |
|---|---|---|
| SQLite 3 | `VACUUM INTO` (eine Kopie der Datei, wenn die Bibliothek zu alt ist) | `sqlite3 ... ".backup ..."` |
| PostgreSQL | `pg_dump` in der Version des Servers | `pg_dump -Fc`, `pg_restore` |
| MongoDB | `mongodump` | `mongodump`, `mongorestore` |
| MariaDB, MySQL | `mysqldump --single-transaction` | `mariadb-dump` oder `mysqldump` |
| TiDB, Vitess, SingleStore, Xpand, Aurora MySQL | `mysqldump` wird ausgeführt (der Treiber meldet `mysql`); auf diesen Datenbanken nicht gemessen | die Sicherung des Herstellers |
| CockroachDB, YugabyteDB, Neon, Supabase, Aurora PostgreSQL | `pg_dump` wird ausgeführt (der Treiber meldet `postgresql`) | YugabyteDB: **`ysql_dump`** (ein Dump von `pg_dump` aus PostgreSQL 17 setzt `transaction_timeout`, was YugabyteDB 2.25 beim Wiederherstellen ablehnte); CockroachDB und die Cloud-Dienste: ihre eigene Sicherung |
| Oracle | **keiner**: mit `--no-backup` ausführen | `expdp` / `impdp` oder `ext:ezoracle:datapump` |
| SQL Server, Azure SQL | **keiner** | `BACKUP DATABASE` oder die Sicherungen des Dienstes |
| Firebird | **keiner** | `gbak` oder `nbackup` |
| IBM Db2, IBM Informix | **keiner** | die eigene Sicherung der Datenbank |
| SAP HANA, SAP ASE | **keiner** | `BACKUP DATA` (HANA), `dump database` (ASE) |
| DuckDB | **keiner** („no built-in dump for duckdb“) | die Datei kopieren, solange kein Prozess schreibt, oder `EXPORT DATABASE` aus dem Programm `duckdb` |
| Cassandra, ScyllaDB (nur Datentabellen) | **keiner** | `nodetool snapshot` oder das Backup-Werkzeug des Herstellers |
| libSQL (sqld), Turso | **keiner** („no built-in dump for a libSQL server“) | eine Kopie des Datenverzeichnisses von sqld, während er angehalten ist; Turso's Point-in-Time-Restore |
| MonetDB | **keiner** | `msqldump` oder MonetDBs Schnappschuss (`monetdb snapshot`) |

Das vollständige Verfahren je Engine: [Sicherungen je Engine](datenbankadministratoren/sicherungen-je-engine.md).

## Gemessene Zeiten

Was die Seiten der Treiber angeben, jede auf ihrem eigenen Rechner und an ihrem eigenen Tag. Die meisten dieser
Rechner waren während der Messung mit anderer Arbeit beschäftigt; die Zahlen sagen also, wie lange etwas dort dauerte,
nicht, wie zwei Datenbanken im Vergleich abschneiden.

| Was | Wo | Zeit |
|---|---|---|
| Ein Site-Paket installiert (`sevenx_multisite`, 16 Schritte) | SQLite, Kickstarter | 54 bis 128 s |
| | SingleStore 10.10 | 279 s |
| | Vitess 25 (Transaktions-Timeout auf 600 s erhöht) | 526 s |
| | MongoDB 8.0, Kickstarter | 588 bis 916 s |
| | TiDB 8.5.1 | 764 s |
| | Oracle 26ai Free, Kickstarter | 901 bis 1030 s |
| | Firebird 4.0 und 5.0 | 21 und 22 Minuten |
| | DuckDB 2.0.1 | 31 Minuten |
| | libSQL, sqld 0.24.33 über HTTP | 22 Minuten |
| | MonetDB Dec2025-SP5 | 29 Minuten |
| | YugabyteDB 2.25 | 42 Minuten (langsame DDL) |
| | CockroachDB 25.2 | 47 Minuten (langsame DDL) |
| | Informix 15 | 2.016 s |
| | SAP HANA Express 2.00.088 (ein Testserver unter Last 20 bis 40) | 109 Minuten (der Suchindex, Wort für Wort) |
| Eine Anweisung | Firebird, in eigener Transaktion / innerhalb einer Transaktion | etwa 20 ms / etwa 4 ms |
| | DuckDB, eine Zeile über ihren Schlüssel gelesen / mit Commit eingefügt / innerhalb einer Transaktion | 2,7 ms / 7,4 ms / 2,7 ms |
| | libSQL (sqld über HTTP), eine Zeile über ihren Schlüssel gelesen / mit Commit geschrieben / innerhalb einer Transaktion | 2,1 ms / 76 bis 101 ms / 2,3 bis 6,7 ms |
| | MonetDB, eine Zeile über ihren Schlüssel gelesen / mit Schreibsperre und Commit eingefügt / innerhalb einer Transaktion | 3,7 bis 4,2 ms / 22 ms / 3,9 ms |
| | ClickHouse, eine Zeile über ihren Schlüssel gelesen / eine Zeile eingefügt / eine Million Zeilen eingefügt | 30 bis 48 ms / 9 bis 18 ms / 214 ms |
| | SAP HANA, `SELECT 1` / ein Insert einer Zeile in eine Column-Tabelle mit ihrem Trigger | etwa 3 ms / 6 bis 14 ms |
| Eine Startseite | DuckDB, Ansichts-Cache gefüllt / leer | 2,3 bis 3,5 s / 17 s |
| | MonetDB, Ansichts-Cache gefüllt / leer | 0,05 bis 0,09 s / 2,6 s |
| Eine Access-Datei importiert (4.103 Zeilen, `exp:access:import`) | in SQLite, MariaDB, PostgreSQL, SQL Server über ODBC | 0,4 s, 2,5 s, 2,6 s, 31 s |
| Eine Anweisung | Cassandra 5.0 / ScyllaDB 2025.1, eine Zeile über ihren Schlüssel gelesen / mit neuer ID eingefügt | 2,2 ms / 0,6 ms; 17,6 ms / 17,8 ms |
| Eine Keyspace-Tabelle importiert (2.000 Zeilen, `exp:cassandra:import`) | aus Cassandra / ScyllaDB nach SQLite | 0,20 s / 0,51 s |

SQLite und MySQL beantworten einen Zugriff über den Schlüssel in einigen zehn Mikrosekunden, DuckDB, Firebird und libSQL (ein Umlauf über HTTP) in
Millisekunden; deshalb braucht eine Seite mit Hunderten von Anweisungen auf diesen den Ansichts-Cache.

## Welche Datenbank für welchen Zweck

### Ein Server

- **SQLite 3**, der Standard: gar kein Datenbankserver, eine ganze Site in einer Datei, eine vollwertige
  Produktionsdatenbank für eine Site, die von einem Rechner ausgeliefert wird. Schreibende Zugriffe warten in einer
  Schlange.
- **PostgreSQL**, wenn Sie von Anfang an einen Datenbankserver wollen oder später einen zweiten Webserver erwarten.
- **MariaDB oder MySQL**, wenn der Hoster sie und nichts anderes anbietet. Für MariaDB oder MySQL wählen Sie in der
  Datenbankliste des Setup-Assistenten „MariaDB / MySQL Improved“.
- **IBM Db2 oder Informix**, wenn Ihre Organisation sie betreibt: eine ganze Site auf einem Webserver (kein DFS-Backend).
- **Firebird 4 oder 5**, wenn Ihre Organisation Firebird betreibt; es braucht PHP 8.4 oder neuer und hat noch kein
  Cluster-Backend, bleibt also auf einem Webserver.
- **libSQL** (`sqld`), wenn Sie das SQL und die Einfachheit von SQLite mögen, die Datenbank aber auf einem eigenen
  Server wollen: ein Container neben der Site, das Schema von SQLite, keine PHP-Erweiterung außer curl. Schreiben
  außerhalb einer Transaktion kostet das Commit des Servers (einige zehn Millisekunden); Installationen und Importe
  dauern daher länger als auf einer SQLite-Datei.

### Mehrere Webserver (ein Cluster)

Die Datenbank muss die DFS-Metadaten der Dateien für alle Webserver halten: **PostgreSQL** (empfohlen),
**MariaDB/MySQL**, **Oracle**, **SQL Server/Azure SQL** oder **MongoDB**, jede mit ihrem Backend im Kernel. TiDB,
Vitess und SingleStore liefen über das MySQL-Backend als DFS-Cluster aus einem Knoten; bei einem verwalteten Dienst,
der TLS verlangt, liegen die DFS-Metadaten besser woanders. CockroachDB und YugabyteDB haben die DFS-Prüfungen über das
PostgreSQL-Backend bestanden; Neon, Supabase und Aurora PostgreSQL verwenden dasselbe Backend, ungetestet. Nicht für
einen Cluster: SQLite (ein Rechner), DuckDB (ein schreibender Prozess, eine lokale Dateisperre), Firebird (noch kein
Backend), Spanner (hält keine Site). Einen **libSQL-Server** können sich mehrere
Webserver für die Tabellen der Site teilen, er hält aber keine DFS-Metadaten: die Dateien in ein geteiltes Verzeichnis
legen, oder die DFS-Metadaten in eine der Datenbanken oben.

### Eine verwaltete Cloud-Datenbank

| Sie haben | Verwenden Sie | Stand |
|---|---|---|
| Azure SQL Database oder Managed Instance | `sqlserver` | gegen SQL Server getestet; die vorübergehenden Fehler von Azure werden wiederholt |
| Amazon RDS for PostgreSQL, Google Cloud SQL, einen beliebigen PostgreSQL-Dienst | `pgsql` | wie PostgreSQL |
| Amazon Aurora PostgreSQL | `aurorapostgresql` | Profil, **ungetestet** |
| Amazon Aurora MySQL | `aurora` | Profil, **ungetestet** |
| Neon, Supabase | `neon`, `supabase` | Profile, **ungetestet**; über eine direkte Verbindung oder einen Session-Pooler installieren |
| PlanetScale | `vitess` (`planetscale`) | auf Vitess gemessen; Transaktionen über 20 s werden abgebrochen, also anderswo installieren und importieren |
| TiDB Cloud, SingleStore Helios | `tidb`, `singlestore` | auf den selbst betriebenen Servern gemessen; TLS mit `--db-ssl=verify` |
| CockroachDB, YugabyteDB (selbst betrieben oder in der Cloud) | `cockroachdb`, `yugabytedb` | je auf einem Knoten gemessen; langsame DDL; DFS über das PostgreSQL-Backend |
| Google Cloud Spanner | `spanner` als Datentabellen-Verbindung | keine Site-Datenbank |
| DataStax Astra DB, ScyllaDB Cloud, Amazon Keyspaces | `cassandra` oder `scylladb` als Datentabellen-Verbindung (TLS) | keine Site-Datenbank; die Dienste selbst nicht ausprobiert |
| Turso (libSQL in der Cloud) | `turso` | Profil des libSQL-Treibers, **ungetestet** (kein Konto benutzt); dasselbe Protokoll wie beim getesteten sqld, über HTTPS mit einem Token |

### Eingebettete, Demo- und Build-Sites

- **SQLite 3** ist die Antwort für fast jede Demo: Die Installer nehmen es als Standard, und es installiert in etwa
  einer Minute.
- **DuckDB** hält eine Site ebenfalls in einer Datei, aber es schreibt immer nur ein Prozess, und jede Anweisung kostet
  Millisekunden: für eine Site mit einem Redakteur, eine Site, die einmal gebaut und dann ausgeliefert wird
  (`AccessMode=read_only`), oder eine Demo des Treibers selbst.
- **Eine libSQL-Datenbankdatei** (was `sqld` schreibt) ist eine SQLite-Datei: der SQLite-Treiber öffnet eine Kopie
  davon. Die eingebetteten Replikate von libSQL brauchen die Client-Bibliothek von libSQL, die PHP nicht laden kann; der
  Treiber bietet sie nicht an.
- **MonetDB** hält eine ganze Site auf einem Server, mit immer nur einem Schreiber und Millisekunden je Anweisung: für
  eine Site, die vor allem gelesen wird und deren Daten auch spaltenorientiert ausgewertet werden.

### Auswertungen, Berichte und Importe

- **Eine DuckDB-Datei als Datentabellen-Verbindung** (`Engine=duckdb`) hält Auswertungen neben einer Site auf jeder
  Datenbank; die DuckDB-Quelle von SQLI Import liest Parquet-, CSV- und JSON-Dateien über DuckDB.
- **ClickHouse** (`Engine=clickhouse`) hält Auswertungstabellen mit Milliarden von Zeilen, in Blöcken geschrieben und
  mit Aggregaten gelesen; es ist keine Site-Datenbank (keine durchgesetzten Schlüssel, keine Transaktionen). Die
  ClickHouse-Quelle von SQLI Import liest ein `SELECT` in Inhalte.
- **Spanner** (über PGAdapter), **Firebird**, **Cassandra und ScyllaDB** und **jede ODBC-Datenbank** erreichen eine
  Site als Datentabellen-Verbindungen: Dort werden Tabellen angelegt, gelesen und geschrieben, und Zeilen gelangen über die
  Werkzeuge der Datentabellen in Inhalte.
- **Die SQL-Quelle von SQLI Import** liest ein einzelnes `SELECT` aus MySQL oder MariaDB, PostgreSQL, SQLite, Oracle
  oder SQL Server direkt und über eine benannte Verbindung aus jeder relationalen Engine der Datentabellen-Verbindungen
  (auch den Datenbanken mit dem PostgreSQL- und MySQL-Protokoll, Spanner, Firebird, DuckDB, Db2, Informix, ODBC und
  MS Access); der Treiber des Kernels öffnet sie, und die Abfrage wird im Dialekt dieser Engine geprüft.
- **Couchbase und CouchDB**, die Dokumentenspeicher, sind Datentabellen-Verbindungen (ein Scope eines Buckets; eine
  CouchDB-Datenbank) und Importquellen: `exp:couchbase:import` und `exp:couchdb:import` kopieren ihre Dokumente in
  Tabellen, die Felder einer Stichprobe als Spalten und den Schlüssel des Dokuments als `doc_id`. Keine von beiden trägt
  eine Site: Couchbase beantwortet das SQL des Kernels über SQL++, aber zu langsam, und CouchDB hat gar kein SQL
  ([Couchbase](../funktionen/6.0/couchbase-treiber.md#warum-keine-site-auf-couchbase-läuft),
  [CouchDB](../funktionen/6.0/couchdb-treiber.md#warum-keine-site-auf-couchdb-läuft)).

### Windows

SQLite (die Windows-CI installiert darauf eine ganze Site) und **SQL Server** sind unter Windows die getesteten
Möglichkeiten: Der SQL-Server-Treiber läuft über `sqlsrv` oder `pdo_sqlsrv`, LocalDB und Express genügen für die
Entwicklung. PostgreSQL, MySQL, MariaDB, Oracle und MongoDB haben Windows-Builds ihrer PHP-Erweiterungen. Der
**ODBC**-Treiber ist unter Windows gegen LocalDB getestet, und **MS-Access**-Dateien werden dort über die Access
Database Engine von Microsoft gelesen und geschrieben. Firebird und DuckDB wurden unter Windows nicht ausprobiert.
Siehe [Exponential unter Windows](exponential-unter-windows.md).

### Weg von MS Access oder einer anderen alten Datenbank

- **Von MS Access**: `exp:access:import` kopiert die Tabellen einer `.accdb`- oder `.mdb`-Datei in die Datenbank der
  Site oder in eine Datentabellen-Verbindung, unter Linux mit MDBTools (ohne Access), unter Windows mit dem
  ODBC-Treiber von Access. Betreiben Sie keine Site auf Access; die [Seite zu MS Access](../funktionen/6.0/ms-access-unterstuetzung.md)
  sagt, warum.
- **Von einer Datenbank, auf der die Site nicht laufen kann** (ein ERP auf Firebird, ein CRM hinter einem ODBC-Treiber,
  ein Intranet auf SQL Server): machen Sie sie zu einer Datentabellen-Verbindung und lesen Sie sie an Ort und Stelle,
  oder importieren Sie sie.
- **Eine ganze Site auf eine andere Engine umziehen**: [Weg von einer alten Datenbank](weg-von-einer-alten-datenbank.md)
  und [Zwischen Engines wechseln](datenbankadministratoren/zwischen-engines-wechseln.md).

## Geplant

Jeder Treiber in den Tabellen oben ist im Hauptzweig und kommt mit 6.0.15; keiner ist mehr in Entwicklung. Geplant
sind zwei Arten von Arbeit, jede mit dem, was sie bräuchte, und einer geschätzten Größe unter
[Geplante Datenbanken](datenbankunterstuetzung.md#geplante-datenbanken-und-was-jede-bräuchte):

- **Datenbanken messen, die ein Protokoll sprechen, das Exponential schon hat**, ohne neuen Code: AlloyDB,
  TimescaleDB, Citus und Azure Cosmos DB for PostgreSQL (eine ganze Website erwartet); QuestDB, Materialize,
  RisingWave und CrateDB; Percona Server und XtraDB Cluster, Dolt und OceanBase; StarRocks, Apache Doris und Manticore
  Search; SQLCipher, LiteFS, rqlite und Cloudflare D1; Amazon DocumentDB, Azure Cosmos DB for MongoDB und FerretDB;
  und über ODBC, für Datentabellen und Importe, Snowflake, Google BigQuery, Databricks, Teradata, Progress OpenEdge,
  FileMaker Server, Exasol und Ingres.
- **Neue Treiberarbeit**, für Datentabellen und Importe: Amazon DynamoDB, Google Firestore und Datastore, Azure Cosmos
  DB (NoSQL-API); Neo4j, ArangoDB, Amazon Neptune und SurrealDB; Redis und Valkey, Riak, RethinkDB und RavenDB;
  InterBase, älteres Sybase, Apache Derby, H2 und HSQLDB.

Nichts auf dieser Liste ist eine Zusage für ein Release, und eine Datenbank kommt erst in die Tabellen oben, wenn sie
hier gelaufen ist.

## Verwandte Seiten

- [Datenbankunterstützung](datenbankunterstuetzung.md): Versionen, Erweiterungen, Einstellungen und die Aussage zur
  Unterstützung.
- [Kapitel 9, Datenbanken](../installation/09-datenbanken.md) und [Kapitel 2, Voraussetzungen](../installation/02-voraussetzungen.md).
- Die Treiber: [SQL Server](../funktionen/6.0/sqlserver-datenbanktreiber.md),
  [MySQL-Protokoll](../funktionen/6.0/mysql-wire-datenbanken.md),
  [PostgreSQL-Protokoll](../funktionen/6.0/postgresql-wire-datenbanken.md),
  [Firebird](../funktionen/6.0/firebird-datenbanktreiber.md), [DuckDB](../funktionen/6.0/duckdb-datenbanktreiber.md),
  [libSQL und Turso](../funktionen/6.0/libsql-datenbanktreiber.md),
  [MonetDB und ClickHouse](../funktionen/6.0/spaltenorientierte-datenbanken.md),
  [IBM Db2](../funktionen/6.0/db2-datenbanktreiber.md), [IBM Informix](../funktionen/6.0/informix-datenbanktreiber.md),
  [SAP HANA](../funktionen/6.0/hana-datenbanktreiber.md), [SAP ASE](../funktionen/6.0/ase-datenbanktreiber.md),
  [Cassandra und ScyllaDB](../funktionen/6.0/cassandra-scylladb-treiber.md),
  [ODBC](../funktionen/6.0/odbc-datenbanktreiber.md), [MS Access](../funktionen/6.0/ms-access-unterstuetzung.md),
  [Couchbase](../funktionen/6.0/couchbase-treiber.md), [Apache CouchDB](../funktionen/6.0/couchdb-treiber.md).
- [Clustering in Exponential 6.0](../funktionen/6.0/clustering-ueberblick.md), [Datentabellen](../funktionen/6.0/datentabellen.md).
- [PHP-Erweiterungen je Engine](datenbankadministratoren/php-erweiterungen-je-engine.md),
  [Eine Engine wählen](datenbankadministratoren/eine-engine-waehlen.md) (die Fragen des Datenbankadministrators).
