# Microsoft-SQL-Server-Treiber

Diese Seite richtet sich an Administratoren und Entwickler, die Exponential auf Microsoft SQL Server betreiben wollen,
und an alle, die am Treiber mitarbeiten. Ab Exponential 6.0.15 hat der Kernel einen eigenen SQL-Server-Treiber,
`expSQLServerDB` in `lib/ezdb/classes/`, neu geschrieben unter der GPL (Version 2 oder später). Er ist nicht von der
alten Erweiterung xrowmssql abgeleitet; sie wurde nur auf ihre Fehler hin gelesen, und jeder davon ist jetzt ein Test.

**Stand.** Treiber und Tests sind fertig (Welle W1); es folgten der Schema-Handler `expSQLServerSchema`, das
Kernel-Schema in `kernel/sql/sqlserver/kernel_schema.sql` und die portablen SQL-Helfer aller Treiber. Es folgen die
Installer-Einträge, die Anweisungen des Kernels, die nur MySQL, PostgreSQL und SQLite verstehen, und das
DFS-Cluster-Backend auf SQL Server. Bis dahin lässt sich keine Site auf SQL Server installieren.

## Inhalt

1. [Unterstützte Server und PHP-Schichten](#unterstützte-server-und-php-schichten)
2. [Einstellungen](#einstellungen)
3. [Was der Treiber anders macht](#was-der-treiber-anders-macht)
4. [Helfer aller Treiber](#helfer-aller-treiber)
5. [Lesereplikate und Failover](#lesereplikate-und-failover)
6. [Geprüfte Werkzeugkette (W0)](#geprüfte-werkzeugkette-w0)
7. [Tests und Rückverfolgbarkeit](#tests-und-rückverfolgbarkeit)

## Unterstützte Server und PHP-Schichten

| Server | Unterstützung |
|---|---|
| SQL Server 2017 (14.x) | unterstützt; die Untergrenze (`STRING_AGG`, `TRIM`, `CONCAT_WS`). `md5()` stimmt nur für ASCII-Text mit PHP überein |
| SQL Server 2019 (15.x) | unterstützt; UTF-8-Sortierungen |
| SQL Server 2022 (16.x) | unterstützt; Entwicklungs- und Haupt-CI-Ziel; `Encrypt=strict` (TDS 8.0) |
| SQL Server 2025 (17.x) | unterstützt; in der CI-Matrix |
| Azure SQL Database, Azure SQL Managed Instance | unterstützt (Engine-Editionen 5 und 8); vorübergehende Fehler werden wiederholt |
| Azure SQL Edge | **nicht unterstützt**: Microsoft hat es am 30. September 2025 eingestellt. Stattdessen SQL Server 2022 oder neuer unter Linux |
| SQL Server 2016 und älter | der Treiber verbindet sich und warnt; nicht unterstützt |

`databaseName()` lautet genau `SQLServer`. PHP-Schichten, mit `Layer=auto` in dieser Reihenfolge gewählt: `sqlsrv`
(empfohlen), `pdo_sqlsrv` (der Treiber schaltet MARS ab), `pdo_odbc` mit einem ODBC-Treiber für SQL Server (Text wird
immer gebunden), `pdo_dblib` über FreeTDS (Rückfallebene, ohne `MultiSubnetFailover` und `ApplicationIntent`).

## Einstellungen

```ini
[DatabaseSettings]
DatabaseImplementation=sqlserver
Server=sql1.example.com
Port=1433
```

`ImplementationAlias[sqlserver]`, `[mssql]` und `[sqlsrv]` nennen alle `expSQLServerDB`. Die Gruppe
`[SQLServerSettings]` in `site.ini` (alle mit Vorgaben): `Layer`, `ODBCDriver`, `Encrypt` (yes, no, strict),
`TrustServerCertificate`, `MultiSubnetFailover`, `ApplicationIntent`, `ReplicaApplicationIntent`, `LoginTimeout`,
`ConnectRetryCount` (0: ODBCs eigene Wiederverbindung aus, der Treiber verbindet selbst neu), `LockTimeout`
(Millisekunden, -1 wartet), `LiteralMode` (bind, prefix), `DeadlockRetries`, `DeadlockRetryWait`, `ReconnectOnLoss`,
`ApplicationName`, `InListChunkSize`, `DatabaseCollation`.

**Serverseitig.** Die Datenbank mit `ALTER DATABASE [site] SET READ_COMMITTED_SNAPSHOT ON` betreiben: Lesende warten dann
nie auf Schreibende. Eine UTF-8-Sortierung (`Latin1_General_100_CI_AS_SC_UTF8`, ab 2019) wird empfohlen; Textspalten
sollten `NVARCHAR` sein.

## Was der Treiber anders macht

- **Blättern** auf dem Server mit `ORDER BY ... OFFSET n ROWS FETCH NEXT m ROWS ONLY`; ohne äußeres `ORDER BY` kommt
  `ORDER BY (SELECT NULL)` dazu, `TOP`, ein eigenes `OFFSET`, `DISTINCT` oder `UNION` ohne `ORDER BY` werden
  eingepackt. `limit` 0 schickt keine Anweisung.
- **Neue IDs** über `SCOPE_IDENTITY()` im Batch des INSERT selbst, nicht über `@@IDENTITY` (die ID eines Triggers).
  `NULL` oder `0` für die Identity-Spalte lassen die Spalte weg; eine echte Zahl schaltet `IDENTITY_INSERT` nur für
  diese Anweisung ein und auch nach einem Fehler wieder aus.
- **Text** als gebundene `NVARCHAR`-Parameter, in DDL, bei `GROUP BY` und ab 2.000 Literalen als `N'...'`.
- **Sitzung**: `NOCOUNT`, `XACT_ABORT`, ANSI-Optionen, `DATEFORMAT ymd`, `LOCK_TIMEOUT`, `READ COMMITTED`.
- **Fehler**: Deadlock (1205), Sperr-Timeout (1222), doppelter Schlüssel (2627/2601) und verlorene Verbindungen werden
  unterschieden; außerhalb einer Transaktion wird nach einem Deadlock wiederholt und eine verlorene Verbindung neu
  geöffnet, innerhalb wird die Transaktion ungültig. `retryOnDeadlock()` wiederholt eine ganze Transaktion.
- **Sperren**: `lock()` mit `TABLOCKX, HOLDLOCK`; `getAppLock()` / `releaseAppLock()` (`sp_getapplock`).
- **Temporäre Tabellen**: `CREATE TEMPORARY TABLE x` wird zu `#x` und beim nächsten Request-Reset entfernt.

## Helfer aller Treiber

Bisher MySQL-eigenes SQL des Kernels wird über Treiber-Helfer portabel, nie über Verzweigungen je Datenbank:
`sqlLimit()`, `sqlUpsert()` (hier `MERGE ... WITH (HOLDLOCK)`), `sqlGroupConcat()` (`STRING_AGG`),
`sqlUnixTimestamp()` / `sqlFromUnixTimestamp()` (sicher über 2038 hinaus) und `sqlQuoteIdentifier()`. Alle Treiber haben
sie: `eZDBInterface` schreibt MySQLs Wörter, jeder Treiber nennt seinen Dialekt (`sqlHelperDialect()`).

## Schema-Handler

`expSQLServerSchema` setzt `.dba`-Schemas in SQL-Server-DDL um und liest eine Datenbank zurück: `NVARCHAR`-Text,
`INT IDENTITY` für `auto_increment`, Primärschlüssel `PK_<Tabelle>`, Vorgaben `DF_<Tabelle>_<Spalte>`. Eine Textspalte
im Schlüssel eines Index wird `NVARCHAR(840)`, weil SQL Server höchstens 1.700 Byte Schlüssel und keine
`NVARCHAR(MAX)`-Spalte indiziert. Kernel-Schema und -Daten wurden damit in SQL Server 2022 angelegt und ohne
Unterschied zurückgelesen.

## Lesereplikate und Failover

`UseSlaveServer=enabled` mit dem Always-On-Listener als `SlaveServerArray`: einfache SELECTs einer anonymen GET-Anfrage
gehen mit `ApplicationIntent=ReadOnly` dorthin. Schreibzugriffe, Sperren, `#temp`-Tabellen, Sequenzen,
Sitzungswerte, Transaktionen und angemeldete Benutzer bleiben auf dem Primärserver. Ein nicht lesbares oder weiter als
`SlaveServerMaxLag` Sekunden zurückliegendes Replikat wird nicht benutzt. Für Verfügbarkeitsgruppen über mehrere
Subnetze `MultiSubnetFailover=enabled` setzen.

## Geprüfte Werkzeugkette (W0)

Gemessen am 24.11.2026 auf AlmaLinux 10.3 mit SQL Server 2022 CU28 (16.0.4325.4) im Linux-Container von Microsoft.
Alle Builds liefen in Wegwerf-Containern; die PHP-Konfiguration des Hosts blieb unberührt.

| PHP | sqlsrv / pdo_sqlsrv | Ergebnis |
|---|---|---|
| 8.0.30 | 5.11.1 | beide bauen; `sqlsrv` besteht; `pdo_sqlsrv` 5.11 liest `TrustServerCertificate=yes` als falsch (der Treiber schreibt 1/0) |
| 8.1.34, 8.2.36 | 5.12.0 | beide bauen und bestehen |
| 8.3.37, 8.4.28, 8.5.13 | 5.13.3 | beide bauen und bestehen |
| 8.5.13 (Plesk) | 5.13.3 | beide bauen gegen die Plesk-Header und bestehen; `PDO_ODBC` besteht ebenfalls |

ODBC Driver 18 (18.7.1.1) und `sqlcmd` 18 aus Microsofts RHEL-10-Repository laufen auf AlmaLinux 10. `PDO_ODBC` schickt
den Anweisungstext in der Codepage des Clients (`N'ä'` kam als `N'Ã¤'` an), liest `VARCHAR`-Spalten ebenso und
bereitet jedes `query()` vor; deshalb bindet der Treiber dort Text und schickt Batches ohne Zeilen über `exec()`.
`pdo_dblib`: FreeTDS 1.5 ist installiert, aber keiner der PHP-Builds hat `pdo_dblib`.

## Tests und Rückverfolgbarkeit

`tests/tests/lib/ezdb/sqlserver/` (ohne Datenbank, Testsuite `sqlserver`) und `.../sqlserver/live/` (Testsuite
`sqlserver-live`, ohne `EXP_TEST_SQLSERVER_HOST` übersprungen). Der GitHub-Job `sqlserver` prüft gegen SQL Server
2017, 2019, 2022 und 2025 auf allen drei Schichten; `sqlserver-windows-localdb` ist ein Rauchtest unter Windows.
Die vollständige Tabelle, welche Tests welche Fehler der alten Erweiterung abdecken, steht auf der englischen Seite
[Microsoft SQL Server driver](../../../eng-us/features/6.0/sqlserver-database-driver.md#traceability-the-old-extensions-problems).
