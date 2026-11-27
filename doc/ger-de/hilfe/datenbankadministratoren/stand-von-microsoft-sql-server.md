---
title: "Microsoft SQL Server: Stand für Datenbankadministratoren"
audience: dba
category: database-administration
order: 22
summary: Wo Microsoft SQL Server für Exponential steht - unterstützt ab 6.0.15 mit dem eigenen Treiber des Kernels, expSQLServerDB (SQL Server 2017 bis 2025, Azure SQL), nicht in 6.0.14 und älter -, mit dem, was ein Datenbankadministrator vorbereitet (Kollation, Read Committed Snapshot, Logins), den PHP-Schichten, Verschlüsselung, Pooling, Lesereplikaten, dem DFS-Cluster, Sicherungen und dem, was noch fehlt.
keywords: Microsoft SQL Server, MSSQL, SQL Server, Azure SQL, expSQLServerDB, expSQLServerSchema, sqlsrv, pdo_sqlsrv, pdo_odbc, pdo_dblib, ODBC Driver 18, READ_COMMITTED_SNAPSHOT, UTF-8-Kollation, NVARCHAR, Always On, ApplicationIntent, DFS-Cluster, BACKUP DATABASE, xrowmssql
type: help
---
# Microsoft SQL Server: Stand für Datenbankadministratoren

Diese Seite richtet sich an Datenbankadministratoren in Organisationen, die auf Microsoft SQL Server standardisiert
haben und eine Exponential-Site darauf betreiben sollen. Die kurze Antwort: **ja, ab Exponential 6.0.15**. Der Kernel
hat einen eigenen Treiber, `expSQLServerDB`, mit dem Schema-Handler `expSQLServerSchema`, dem Kernel-Schema in
`kernel/sql/sqlserver/`, Einträgen in den Installern und einem DFS-Cluster-Backend. Exponential 6.0.14 und älter haben
nichts davon. Die vollständige Referenz ist die Seite
[Microsoft-SQL-Server-Treiber](../../funktionen/6.0/sqlserver-datenbanktreiber.md).

## Inhalt

1. [Server und PHP-Schichten](#server-und-php-schichten)
2. [Die Datenbank vorbereiten](#die-datenbank-vorbereiten)
3. [Verschlüsselung, Pooling und Sitzungen](#verschlüsselung-pooling-und-sitzungen)
4. [Lesereplikate und der Cluster](#lesereplikate-und-der-cluster)
5. [Sicherungen und Aktualisierungen](#sicherungen-und-aktualisierungen)
6. [Was noch fehlt](#was-noch-fehlt)
7. [Die alte Erweiterung xrowmssql](#die-alte-erweiterung-xrowmssql)
8. [Verwandte Seiten](#verwandte-seiten)

## Server und PHP-Schichten

| Server | Stand |
|---|---|
| SQL Server 2017 (14.x) | unterstützt; die Untergrenze (`STRING_AGG`, `TRIM`, `CONCAT_WS`) |
| SQL Server 2019, 2022, 2025 | unterstützt; 2022 ist das Hauptziel der CI, alle vier laufen in der CI |
| Azure SQL Database, Managed Instance | unterstützt; vorübergehende Fehler werden wiederholt |
| Azure SQL Edge, SQL Server 2016 und älter | nicht unterstützt |

PHP erreicht ihn über `sqlsrv` (empfohlen), `pdo_sqlsrv`, `pdo_odbc` mit dem ODBC Driver 17 oder 18 von Microsoft oder
`pdo_dblib` über FreeTDS; `[SQLServerSettings] Layer=auto` nimmt die erste geladene. Gemessen: `sqlsrv` und
`pdo_sqlsrv` 5.11 bis 5.13 lassen sich auf PHP 8.0 bis 8.5 bauen und bestehen die Tests (5.11 auf PHP 8.0 liest mit
`pdo_sqlsrv` `TrustServerCertificate=yes` als falsch).

## Die Datenbank vorbereiten

Die Installer legen die Datenbank auf SQL Server nicht an. Legen Sie sie und Login und Benutzer der Site zuerst an:

```sql
CREATE DATABASE [site] COLLATE Latin1_General_100_CI_AS_SC_UTF8;
ALTER DATABASE [site] SET READ_COMMITTED_SNAPSHOT ON;
```

- **Read Committed Snapshot** lässt Leser nie auf Schreiber warten, wie es der Kernel von MySQL und PostgreSQL erwartet;
  der Treiber vermerkt es in der Debug-Ausgabe, wenn es ausgeschaltet ist.
- **Eine UTF-8-Kollation** (2019 und neuer) oder Spalten vom Typ `NVARCHAR` bewahren jedes Zeichen; `VARCHAR` in einer
  Latin-Kollation verliert Zeichen, und PDO_ODBC liest `VARCHAR` in der Codepage des Clients.
- **Indexschlüssel**: SQL Server indiziert höchstens 1.700 Byte und kein `NVARCHAR(MAX)`; der Schema-Handler legt die
  acht Textspalten des Kernels in einem Indexschlüssel als `NVARCHAR(840)` an.

Dann `exp:install --apply --db=sqlserver --db-host=... --db-name=site --db-user=...` oder `Type=sqlserver` im
Kickstarter ([eine Site installieren](../../funktionen/6.0/sqlserver-datenbanktreiber.md#eine-site-auf-sql-server-installieren)).

## Verschlüsselung, Pooling und Sitzungen

- `--db-encrypt` / `[SQLServerSettings] Encrypt`: `yes` (der Standard), `no` oder `strict` (TDS 8.0, SQL Server 2022
  und neuer); `TrustServerCertificate` akzeptiert ein selbst signiertes Zertifikat in einem vertrauenswürdigen Netz.
- `ConnectionPooling=disabled` (der Standard): Eine gepoolte `sqlsrv`-Verbindung würde von abgespaltenen Workern geerbt.
  Lassen Sie unter Linux auch das Pooling in `odbcinst.ini` aus.
- Jede Sitzung setzt `NOCOUNT`, `XACT_ABORT`, die ANSI-Optionen, `DATEFORMAT ymd`, `LOCK_TIMEOUT` und `READ COMMITTED`.
  Deadlocks (1205) und Sperr-Timeouts (1222) werden unterschieden; eine einzelne Anweisung außerhalb einer Transaktion
  wird wiederholt.

## Lesereplikate und der Cluster

- `UseSlaveServer=enabled` mit dem Always-On-Listener oder einem Sekundärknoten: anonyme Lesezugriffe gehen dorthin mit
  `ApplicationIntent=ReadOnly`; ein Replikat, das weiter zurückliegt als `SlaveServerMaxLag`, wird nicht verwendet. Über
  Subnetze hinweg setzen Sie `MultiSubnetFailover=enabled`.
- Das DFS-Cluster-Backend `expDFSFileHandlerSQLServerBackend` hält die Metadaten der Dateien in derselben Datenbank für
  beliebig viele Webserver; `exp:cluster:enable --backend=sqlserver` macht einen Cluster aus einem Knoten
  ([Clustering auf SQL Server](../../funktionen/6.0/sqlserver-dfs-cluster.md)). Dateinamen haben höchstens 830 Zeichen.

## Sicherungen und Aktualisierungen

Die Sicherheitskopie, die `exp:upgrade` vor dem Schreiben anlegt, hat für SQL Server keinen eingebauten Dump. Sichern
Sie mit `BACKUP DATABASE` (oder auf Azure mit den Sicherungen des Dienstes) und führen Sie die Aktualisierung mit
`--no-backup` aus. Eine Aktualisierung von einem älteren Release wurde auf SQL Server nicht getestet: 6.0.15 ist das
erste Release mit dem Treiber.

## Was noch fehlt

- Der Setup-Assistent im Browser führt SQL Server nicht auf; verwenden Sie den Kickstarter oder `exp:install`.
- Keine Dateien in `update/database/` für SQL Server; das Schema ist `kernel/sql/sqlserver/kernel_schema.sql`.
- `pdo_dblib` ist nur durch die Tests ohne Server abgedeckt.

## Die alte Erweiterung xrowmssql

Der Treiber ist nicht von xrowmssql abgeleitet. Diese Erweiterung wurde nur auf ihre Probleme hin gelesen, und jedes
davon ist jetzt ein Test (die Tabelle zur Rückverfolgbarkeit auf der
[Treiberseite](../../funktionen/6.0/sqlserver-datenbanktreiber.md#tests-und-rückverfolgbarkeit)). Installieren Sie
xrowmssql nicht auf 6.0.15.

## Verwandte Seiten

- [Datenbankunterstützung: Microsoft SQL Server](../datenbankunterstuetzung.md#7-microsoft-sql-server-und-azure-sql),
  [Eine Datenbank wählen](../datenbank-waehlen.md)
- [Stand von Microsoft SQL Server](../hosting/grundlagen/stand-von-microsoft-sql-server.md) (für Hosting-Unternehmen)
- [Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
