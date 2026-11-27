# ODBC-Treiber

Diese Seite richtet sich an Administratoren, die eine Datenbank über ihren ODBC-Treiber erreichen wollen, und an
Entwickler, die am Treiber mitarbeiten. Ab Exponential 6.0.15 hat der Kernel einen allgemeinen ODBC-Treiber,
`expODBCDB` in `lib/ezdb/classes/`: ein Treiber für jede Datenbank mit ODBC-Treiber. Das SQL, das er selbst schreibt
(Seiten, Maskierung, die neue ID, der Katalog, die portablen Helfer), wählt ein Dialekt in den Einstellungen.

**Wofür er da ist.** Die eigene Datenbank einer Site über ODBC (SQL Server, PostgreSQL, MariaDB/MySQL, Db2): die
Installer, der Einrichtungsassistent und der Kickstarter installieren sie, `exp:cluster:enable` setzt einen
DFS-Cluster darauf, und die Upgrade-Stufen laufen darüber ([Eine Site über ODBC](#eine-site-über-odbc)). Außerdem:
benannte Verbindungen der Datentabellen-Schicht ([Datentabellen](datentabellen.md)) zu Datenbanken, für die die
Installation keinen eigenen Treiber hat oder die ein Hoster nur über ODBC anbietet, und der Import von
[MS-Access-Dateien](ms-access-unterstuetzung.md).

**ODBC ist ein Transportweg.** Für SQL Server und Db2, deren eigene Treiber eine ODBC-Schicht haben, öffnet
`DatabaseImplementation=odbc` diesen Treiber (`expSQLServerDB`, `expDb2DB`) über PDO_ODBC: Eine Site auf SQL Server
über ODBC verhält sich genau wie eine auf dem SQL-Server-Treiber. PostgreSQL und MariaDB/MySQL laufen auf
`expODBCDB`, mit ihren eigenen Schema-Handlern.

## Inhalt

1. [Einstellungen](#einstellungen)
2. [Dialekte](#dialekte)
3. [Eine Site über ODBC](#eine-site-über-odbc)
4. [Was geht und was nicht, je Dialekt](#was-geht-und-was-nicht-je-dialekt)
5. [PHP-Schichten](#php-schichten)
6. [Eine Datentabellen-Verbindung über ODBC](#eine-datentabellen-verbindung-über-odbc)
7. [Tests](#tests)

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=odbc
Server=db.example.com
Port=5432
User=crm
Password=...
Database=crm

[ODBCSettings]
Dialect=postgresql
Driver=PostgreSQL Unicode
```

`ImplementationAlias[odbc]` nennt `expODBCDB`. `[ODBCSettings]` der `site.ini`:

| Einstellung | Bedeutung |
|---|---|
| `Dialect` | `sqlserver`, `postgresql`, `mysql` (MySQL und MariaDB), `sqlite`, `oracle`, `msaccess` oder `generic` |
| `Driver` | der Name des ODBC-Treibers (`odbcinst.ini` unter Linux, ODBC-Administrator unter Windows); leer: der übliche des Dialekts |
| `DataSourceName` | ein DSN aus `odbc.ini` statt `Driver` und der Server-Einstellungen |
| `ConnectionString` | weitere `Schlüssel=Wert`-Paare, mit `;` getrennt (`Encrypt=yes;TrustServerCertificate=yes`) |
| `Layer` | `auto`, `pdo_odbc` oder `odbc` (unten) |

Benutzer und Passwort gehen an den Verbindungsaufruf, nie in die Verbindungszeichenkette; die Meldung einer
fehlgeschlagenen Verbindung zeigt das Passwort maskiert. Im Code stehen dieselben Einstellungen in den Parametern:
`new expODBCDB( array( 'server' => 'db', 'database' => 'crm', 'user' => 'u', 'password' => $p, 'odbc' => array( 'Dialect' => 'mysql' ) ) )`.

Vorgabe-Treiber (die Namen, die die Pakete eintragen): `ODBC Driver 18 for SQL Server`; `PostgreSQL Unicode` (Debian
und Ubuntu `odbc-postgresql`; `PostgreSQL Unicode(x64)` unter Windows); `MariaDB Unicode` (`odbc-mariadb`); `SQLite3`
(`libsqliteodbc`); `Microsoft Access Driver (*.mdb, *.accdb)` unter Windows und `MDBTools` sonst.

## Dialekte

Der Dialekt entscheidet alles, was der Treiber selbst schreibt; `databaseName()` ist der Kernel-Name der Engine
(`SQLServer`, `postgresql`, `mysql`, `sqlite`, `oracle`, `MSAccess`, und `odbc` für `generic`), so behandeln die
Schema-Handler und die Datentabellen-Schicht die Verbindung als diese Engine.

| Dialekt | Seiten | Neue ID | Text und Maskierung | Sitzung |
|---|---|---|---|---|
| `sqlserver` | `OFFSET ... FETCH` (der Code des SQL-Server-Treibers) | `SCOPE_IDENTITY()` im Batch des INSERT | String-Literale als Parameter gebunden (Unicode), Backticks zu eckigen Klammern | `NOCOUNT`, `XACT_ABORT`, ANSI-Optionen, `DATEFORMAT ymd` |
| `postgresql` | `LIMIT ... OFFSET` | `currval()` der Sequenz der Spalte (`SERIAL` oder die Kernel-Sequenz `<tabelle>_<spalte>_seq`), sonst `lastval()` | Hochkommas verdoppelt | |
| `mysql` | `LIMIT ... OFFSET` | `LAST_INSERT_ID()` | Backslashes und Hochkommas maskiert (`quote()` von PDO_ODBC kennt keinen Backslash) | `SET NAMES utf8mb4` |
| `sqlite` | `LIMIT ... OFFSET` | `last_insert_rowid()` | Hochkommas verdoppelt | |
| `oracle` | `OFFSET ... FETCH` | die Sequenz der Tabelle (`s_<name>`, wie ezoracle) | Hochkommas verdoppelt | |
| `msaccess` | `TOP offset+limit`, die ersten Zeilen in PHP verworfen | `@@IDENTITY` | Hochkommas verdoppelt | siehe [MS Access](ms-access-unterstuetzung.md) |
| `generic` | `LIMIT ... OFFSET` | `lastInsertId()` der Schicht (meist keine) | Hochkommas verdoppelt | |

Die portablen Helfer (`sqlLimit()`, `sqlUpsert()`, `sqlGroupConcat()`, `sqlUnixTimestamp()`,
`sqlFromUnixTimestamp()`, `sqlForUpdate()`, `sqlQuoteIdentifier()`) folgen dem Dialekt wie bei den eigenen Treibern.

## Eine Site über ODBC

Ab 6.0.15 lässt sich eine Site über den ODBC-Treiber der Datenbank auf SQL Server, PostgreSQL, MariaDB/MySQL oder
Db2 installieren. Die Site bekommt `DatabaseImplementation=odbc` und `[ODBCSettings]` (in `settings/override` und
jedem Siteaccess); alles andere liest den Dialekt als Datenbanksystem.

| Dialekt | Läuft auf | Schema-Handler | DFS-Cluster-Backend | Sicherung und Rücknahme mit xne |
|---|---|---|---|---|
| `sqlserver` | `expSQLServerDB` mit `[SQLServerSettings] Layer=pdo_odbc` | `expSQLServerSchema` | das von SQL Server, über PDO_ODBC (`Layer=pdo_odbc`, vom Installer geschrieben) | abgelehnt, wie bei SQL Server |
| `postgresql` | `expODBCDB` | `eZPgsqlSchema` | das von PostgreSQL, über PDO_ODBC (`DBODBCDriver`, von `exp:cluster:enable` geschrieben); `pdo_pgsql` nicht nötig | wie PostgreSQL, über `pdo_pgsql` des PHP, mit dem xne läuft |
| `mysql` (auch MariaDB) | `expODBCDB` | `eZMysqlSchema` | das von MySQLi (braucht PHPs `mysqli`) | wie MySQL, über `pdo_mysql` des PHP, mit dem xne läuft |
| `db2` | `expDb2DB` mit `[Db2Settings] Layer=pdo_odbc` | `expDb2Schema` | keins (Db2 hat keins) | abgelehnt, wie bei Db2 |

```bash
# install.php (exp:install) - SQL Server, PostgreSQL, MariaDB über ihre ODBC-Treiber
EXP_INSTALL_DB_PASSWORD=... php bin/php/install.php --db=odbc --db-dialect=sqlserver --db-host=sql.example \
    --db-name=exponential --db-user=exp --db-trust-server-certificate --apply
php bin/php/install.php --db=odbc --db-dialect=postgresql --db-host=pg.example --db-name=exponential --db-user=exp --apply
php bin/php/install.php --db=odbc --db-dialect=mysql --db-dsn=exp-mariadb --db-name=exponential --db-user=exp --apply
```

| Option | Bedeutung |
|---|---|
| `--db-dialect` | `sqlserver`, `postgresql`, `mysql` (auch MariaDB) oder `db2`; Port, Name und Benutzer haben die Vorgaben des Dialekts |
| `--db-odbc-driver` | der Name des ODBC-Treibers; Vorgabe der übliche des Dialekts (unten) |
| `--db-dsn` | `postgresql` und `mysql`: eine Datenquelle aus `odbc.ini` statt Treiber, Host und Port |
| `--db-connection-string` | weitere Paare `Schlüssel=Wert` |
| `--cluster=one-node` | mit `postgresql`, `sqlserver` oder `mysql`: nach der Installation ein DFS-Cluster mit einem Knoten |

Der Kickstarter nimmt `[database_choice] Type=odbc` mit `Dialect=` und `[database_init] ODBCDriver`,
`DataSourceName`, `ConnectionString` (`kickstart.ini-dist`); `odbc_sqlserver`, `odbc_pgsql`, `odbc_mysql` und
`odbc_db2` gelten ebenfalls als Typ. Der Einrichtungsassistent bietet diese vier Typen an, wenn PHP `pdo_odbc` oder
`odbc` hat.

**Gemessen** am 27.11.2026 (PHP 8.5.13 nur mit PDO_ODBC und der odbc-Erweiterung, Ubuntu 24.04, das Paket
`sevenx_multisite`):

| Was | PostgreSQL 17, psqlODBC 16 | MariaDB 11.4, MariaDB Connector/ODBC 3.1 |
|---|---|---|
| Installation (`install.php --db=odbc`) | geht, 302 s | geht, 220 s |
| Startseite, Admin-Anmeldung, Suche | 200 | 200 |
| Schema zurückgelesen gegen `share/db_schema.dba` | dieselben Unterschiede wie beim PostgreSQL-Treiber auf derselben Datenbank (die Tabellen und der Index, die die Migrationen von 6.0.15 anlegen, bigint-Vorgaben als `'0'` gelesen) | die Tabellen und der Index der Migrationen, sonst nichts |
| `exp:upgrade:database` (zuvor zwei Kernel-Tabellen gelöscht) | - | geht: ein Sicherungspunkt mit `mysqldump`, beide Tabellen neu angelegt, Schema zurückgelesen wie vorher |
| `exp:cluster:enable` und DFS | geht ohne `pdo_pgsql`: 312 Dateien übernommen, eine Datei von 200 KB in 34 ms gespeichert, zurückgelesen, gemessen und gelöscht, ein gespeichertes Bild vom Gateway ausgeliefert | - |

SQL Server 2022 über ODBC Driver 18 und MariaDB installiert der GitHub-Job `odbc` (Job `pdo_odbc`), PostgreSQL der
Job `odbc` mit PHPs odbc-Erweiterung.

**Upgrades.** Die Upgrade-Stufen (`exp:upgrade`) laufen über ODBC mit dem Schema-Handler des Dialekts, und der
Sicherungspunkt davor nutzt das Sicherungsprogramm des Dialekts (`mysqldump`, `pg_dump`; wie der SQL-Server-Treiber
hat SQL Server über ODBC keine Sicherung im Sicherungspunkt: zuerst die Datenbank sichern oder mit `--no-backup`
laufen). Eine über ODBC installierte Site beginnt mit 6.0.15; es gibt keine frühere Version, von der aus sie
aktualisiert würde. Das erste Upgrade über ODBC ist das von 6.0.15 auf die nächste Version.

**Nicht hier.** SQLite, Oracle und MS Access als Datenbank einer Site über ODBC (SQLite und Oracle haben eigene
Treiber; zu Access siehe [MS Access](ms-access-unterstuetzung.md)); eine DSN für SQL Server und Db2 (ihre Treiber
bauen die Verbindung aus Host und Port).

## Was geht und was nicht, je Dialekt

Gemessen am 25. November 2026 mit den Live-Tests (unten) unter Ubuntu 24.04 (unixODBC 2.3.12, PHP 8.5.13) gegen SQL
Server 2022 (ODBC Driver 18.7), PostgreSQL 17 (psqlODBC 16), MariaDB 11.4 (MariaDB Connector/ODBC 3.1) und SQLite
3.45 (sqliteodbc 0.99991), auf beiden PHP-Schichten; dieselben Tests laufen im GitHub-Job `odbc`.

| Test | SQL Server 2022 | PostgreSQL 17 | MariaDB 11.4 | SQLite 3.45 |
|---|---|---|---|---|
| Verbindung, Version, `eZTableList()` | geht | geht | geht | geht |
| neue ID (`lastSerialID()`) | geht (`SCOPE_IDENTITY()` im Batch des INSERT) | geht (Sequenz) | geht | geht |
| Seiten (Limit/Offset gleich dem Ausschnitt, Limit 0, hinter dem Ende) | geht | geht | geht | geht |
| Transaktionen (Rollback, Commit, verschachtelt) | geht | geht | geht | geht |
| Unicode (Umlaute, Emoji, CJK, kombinierende Zeichen; im `WHERE`) | geht (Text gebunden) | geht | geht | geht |
| langer Text (1,2 MB) | geht | geht | geht | **geht nicht**: sqliteodbc beschreibt `TEXT` als 255 Zeichen; PDO_ODBC lehnt den Wert ab (`01004 data right truncated`), die odbc-Erweiterung schneidet ihn auf 255 Bytes |
| Maskierung (Hochkomma, Backslash, NUL entfernt, `''` ist nicht `NULL`) | geht | geht | geht | geht |
| `sqlUpsert()` | geht nur mit `IDENTITY_INSERT`, wenn der Schlüssel eine Identity-Spalte ist (MERGE übersetzt seinen INSERT-Zweig mit dem Schlüssel; `expSQLServerDB` schaltet es selbst) | geht | geht | geht |
| `sqlGroupConcat()` | geht | geht | geht | geht |
| Unix-Zeitstempel hin und zurück | geht, nach 2038 | geht, nach 2038 | geht nur vor 2038 (MariaDB vor 11.5: `FROM_UNIXTIME()` endet 2038; eine Eigenschaft von MariaDB, nicht von ODBC) | geht, nach 2038 |
| ein Schlüsselwort als Name (`sqlQuoteIdentifier()`) | geht | geht | geht | geht |
| doppelter Schlüssel (false oder `eZDBException`) | geht | geht | geht | geht |
| Datentabellen-Verbindung (Engine `odbc`): Test, Tabelle anlegen, schreiben, erneut planen: nichts zu tun | geht | geht (nachdem der PostgreSQL-Schema-Handler die `1` von psqlODBC als wahr liest) | geht | geht |
| Access-Import (die 3 Dateien von MDBTools, 4.103 Zeilen) | geht; 31 s (ein Umlauf je Zeile) | geht, 2,6 s | geht, 2,5 s | geht, 0,4 s |

Beide Schichten lieferten dieselben Ergebnisse; Oracle (Dialekt `oracle`) ist nur durch die Tests ohne Datenbank abgedeckt.

## PHP-Schichten

| Schicht | Erweiterung | Hinweise |
|---|---|---|
| `pdo_odbc` | PDO_ODBC von PHP | die Vorgabe (`auto`). Wandelt gebundenen Text in UTF-16, wenn der Treiber breite Zeichen verlangt |
| `odbc` | die odbc-Erweiterung von PHP | die einzige Schicht mit den Katalog-Aufrufen (`SQLTables`, `SQLColumns`, `SQLStatistics`); `auto` nimmt sie für MS Access zuerst. Einen gebundenen Wert `'in Hochkommas'` liest die Erweiterung als Dateinamen, daher behält eine solche Anweisung ihre Literale |

Beide Schichten senden den Anweisungstext in der Codepage des Clients: Microsofts SQL-Server-Treiber machte aus UTF-8
in einem `N'...'`-Literal auf beiden Zeichensalat; deshalb bindet der Dialekt `sqlserver` die String-Literale
(gemessen: `ä😀` kam als Literal als `Ã¤ð` an, als Parameter unverändert). Die Unicode-Treiber von PostgreSQL,
MariaDB und SQLite nehmen UTF-8-Anweisungstext, wie er ist.

## Eine Datentabellen-Verbindung über ODBC

Die Datentabellen-Schicht hat die Engine `odbc` (`datatable.ini [ConnectionSettings] Engines[]`); Dialekt und Treiber
stehen in der Verbindung:

```ini
[Connection_crm]
Engine=odbc
Server=db.example.com
Port=5432
Database=crm
User=crm
Secret=env:CRM_DB_PASSWORD
Dialect=postgresql
ODBCDriver=PostgreSQL Unicode
ConnectionString=SSLmode=require
```

`expDataConnections::test()` öffnet sie und liest Version und Tabellen; `expDataTableSchema` legt Tabellen darauf an
und bringt sie auf Stand, über den Schema-Handler des Dialekts (MySQL, PostgreSQL, SQLite) oder eigene Anweisungen
(SQL Server, Oracle, MS Access).

## Tests

`tests/tests/lib/ezdb/odbc/` (ohne Datenbank, Testsuite `odbc`) und `.../odbc/live/` (Testsuite `odbc-live`;
übersprungen ohne `EXP_TEST_ODBC_SQLSERVER`, `_POSTGRESQL`, `_MYSQL`, `_SQLITE` mit `_USER`, `_PASSWORD`, `_DRIVER`
sowie `EXP_TEST_ODBC_LAYER`). Der GitHub-Job `odbc` führt sie auf beiden Schichten mit allen Zielen aus.

| Datei | Was |
|---|---|
| `expODBCDBTest` | Schichten, Vorgabe-Treiber, Verbindungszeichenketten, Engine-Namen, Maskierung, Seiten, maskierte Passwörter, Einstellungen; der Transportweg zu den Treibern von SQL Server und Db2 (`resolve()`) |
| `expODBCSiteInstallTest` | die ODBC-Typen des Einrichtungsassistenten, die Optionen des Kickstarters, die Parameter der Schritte, `install.php --db=odbc` |
| `expMSAccessDialectTest` | der Access-Dialekt (siehe [MS Access](ms-access-unterstuetzung.md)) |
| `live/expODBCLiveTest` | je Ziel und Schicht: Verbindung, neue IDs, Seiten, Transaktionen, Unicode, 1 MB Text, Maskierung, Helfer, doppelte Schlüssel, eine Datentabellen-Verbindung |
