---
title: Stand von Microsoft SQL Server
audience: hosting
category: hosting
order: 130
summary: Der Stand von Microsoft SQL Server für Exponential, wie ein Hoster ihn angeben sollte - unterstützt ab 6.0.15 mit dem eigenen Treiber des Kernels, expSQLServerDB (SQL Server 2017 bis 2025, Azure SQL Database und Managed Instance), nicht in 6.0.14 und älter -, was ein Hoster braucht, um ihn anzubieten, was er noch nicht kann und was man einem Kunden sagt, der fragt.
keywords: Microsoft SQL Server, SQL Server, MSSQL, Azure SQL, sqlsrv, pdo_sqlsrv, pdo_odbc, ODBC Driver 18, Windows-Hosting, LocalDB, Datenbankunterstützung, expSQLServerDB, DFS-Cluster, Always On, Kundenfrage, Hosting-Angebot
type: help
---
# Stand von Microsoft SQL Server

Diese Seite richtet sich an Produktmanager, Vertriebs- und Supportmitarbeiter eines Hosting-Unternehmens, besonders
eines mit Kunden aus der Windows- oder Microsoft-Welt, die gefragt werden, ob Exponential auf Microsoft SQL Server läuft.

## Inhalt

1. [Die Aussage](#die-aussage)
2. [Was ein Hoster braucht](#was-ein-hoster-braucht)
3. [Was noch nicht geht](#was-noch-nicht-geht)
4. [Was Sie einem Kunden sagen](#was-sie-einem-kunden-sagen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Aussage

> **Microsoft SQL Server und Azure SQL werden ab Exponential 6.0.15 unterstützt**, als siebte Datenbank in der
> offiziellen Reihenfolge des Projekts: 1. SQLite 3 · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL ·
> 7. Microsoft SQL Server und Azure SQL.

Der Kernel hat einen eigenen Treiber, `expSQLServerDB`, neu unter der GPL geschrieben, mit einem Schema-Handler, dem
Kernel-Schema für SQL Server, Einträgen in den Installern und einem DFS-Cluster-Backend. Exponential 6.0.14 und älter
haben keinen SQL-Server-Treiber: Ein Kunde mit einem älteren Release aktualisiert zuerst.

| | |
|---|---|
| Server | SQL Server 2017, 2019, 2022 und 2025; Azure SQL Database und Azure SQL Managed Instance. Nicht SQL Server 2016 und älter, nicht Azure SQL Edge (von Microsoft eingestellt) |
| PHP-Schichten | `sqlsrv` von Microsoft (empfohlen) oder `pdo_sqlsrv`, PHPs `pdo_odbc` mit ODBC Driver 17 oder 18, oder `pdo_dblib` über FreeTDS |
| Betriebssysteme | Linux und Windows; SQL Server Express und LocalDB genügen für die Entwicklung |
| Installer | der Kickstarter (`Type=sqlserver`) und `exp:install --db=sqlserver`; der Setup-Assistent im Browser bietet ihn nicht an |
| Mehrere Webserver | ja: das DFS-Cluster-Backend `expDFSFileHandlerSQLServerBackend`; Lesereplikate über Always On |
| Tests | bei jedem Push gegen SQL Server 2017, 2019, 2022 und 2025 über drei PHP-Schichten, PHP 8.1 bis 8.5, und LocalDB unter Windows |

## Was ein Hoster braucht

- **Eine bestehende Datenbank.** Der Installer legt sie auf SQL Server nicht an. Legen Sie sie mit einer
  UTF-8-Kollation (`Latin1_General_100_CI_AS_SC_UTF8`, SQL Server 2019 und neuer) und `READ_COMMITTED_SNAPSHOT ON` an.
- **Die PHP-Erweiterung** im PHP der Site und der Kommandozeile: `sqlsrv` oder `pdo_sqlsrv` von Microsoft (PECL unter
  Linux, DLLs unter Windows) oder `pdo_odbc` mit dem ODBC Driver 18 von Microsoft. ODBC Driver 18 verschlüsselt
  standardmäßig: Ein Server mit selbst signiertem Zertifikat braucht `--db-trust-server-certificate`.
- **Kein Connection Pooling** (der Standard, `[SQLServerSettings] ConnectionPooling=disabled`) und unter Linux auch kein
  Pooling in `odbcinst.ini`: Eine gepoolte Verbindung würde von abgespaltenen Workern geteilt.
- **Eigene Sicherungen**: Die eingebaute Sicherheitskopie der Aktualisierung hat für SQL Server keinen Dump. Verwenden
  Sie `BACKUP DATABASE` oder die Sicherungen des Dienstes und führen Sie Aktualisierungen mit `--no-backup` aus.

## Was noch nicht geht

- Der Setup-Assistent im Browser führt SQL Server nicht auf: Installieren Sie mit dem Kickstarter oder `exp:install`.
- Eine Aktualisierung von einem älteren Release wurde auf SQL Server noch nicht getestet: 6.0.15 ist das erste
  Release mit dem Treiber.
- Keine Dateien in `update/database/` für SQL Server: Das Schema liegt in `kernel/sql/sqlserver/`.
- Das DFS-Backend speichert Dateinamen von höchstens 830 Zeichen.

## Was Sie einem Kunden sagen

> Exponential 6.0.15 läuft auf Microsoft SQL Server 2017 bis 2025 und auf Azure SQL, unter Linux und unter Windows, mit
> eigenem Treiber und Cluster-Backend, bei jedem Push getestet. Die Datenbank muss vor der Installation bestehen;
> verwenden Sie eine UTF-8-Kollation und Read Committed Snapshot.

Bieten Sie SQL Server nicht für eine Site an, die auf 6.0.14 oder älter bleiben muss.

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Eine Datenbank wählen](../../datenbank-waehlen.md) und [Datenbankunterstützung: Microsoft SQL Server](../../datenbankunterstuetzung.md#7-microsoft-sql-server-und-azure-sql)
- [Microsoft-SQL-Server-Treiber](../../../funktionen/6.0/sqlserver-datenbanktreiber.md) und
  [Clustering auf SQL Server](../../../funktionen/6.0/sqlserver-dfs-cluster.md)
- [Exponential unter Windows](../../exponential-unter-windows.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
