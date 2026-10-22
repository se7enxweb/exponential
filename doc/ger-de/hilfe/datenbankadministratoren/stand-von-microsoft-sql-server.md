---
title: "Microsoft SQL Server: Stand für Datenbankadministratoren"
audience: dba
category: database-administration
order: 22
summary: Wo Microsoft SQL Server für Exponential 6.0.x steht - nicht unterstützt, bekanntermaßen mit zusätzlicher Arbeit möglich -, was es gibt, worauf solche Arbeit aufbauen könnte (die Treiberschnittstelle, das .dba-Format, austauschbare Schema-Handler, die alte Erweiterung xrowmssql), was fehlt und was ein Datenbankadministrator antworten sollte, wenn er gebeten wird, eine Website auf SQL Server zu betreiben.
keywords: Microsoft SQL Server, MSSQL, SQL Server, sqlsrv, pdo_sqlsrv, xrowmssql, nicht unterstützt, mit zusätzlicher Arbeit möglich, eZDBInterface, Schema-Handler, BIT_OR, OFFSET FETCH, SCOPE_IDENTITY, NVARCHAR, not supported, possible with additional work, schema handler
type: help
---
# Microsoft SQL Server: Stand für Datenbankadministratoren

Diese Seite richtet sich an Datenbankadministratoren in Organisationen, die auf Microsoft SQL Server standardisiert sind und
gefragt werden, ob eine Exponential-Website darauf laufen kann. Die kurze Antwort: **nicht in Exponential 6.0.x**. SQL Server
ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich; es gibt keinen Treiber, keinen Schema-Handler und keinen
Installer-Eintrag dafür, und nichts hier verspricht ein Release, das einen hat. Diese Seite fasst zusammen, was es gibt, was
fehlt und was Sie in der Zwischenzeit tun. Die vollständige Aussage und die Arbeitsliste stehen in
[Datenbankunterstützung](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand).

## Inhalt

1. [Die Aussage](#die-aussage)
2. [Was es gibt, um darauf aufzubauen](#was-es-gibt-um-darauf-aufzubauen)
3. [Die Erweiterung xrowmssql](#die-erweiterung-xrowmssql)
4. [Was fehlt](#was-fehlt)
5. [Was Sie in der Zwischenzeit tun](#was-sie-in-der-zwischenzeit-tun)
6. [Die Arbeit beitragen](#die-arbeit-beitragen)

## Die Aussage

> Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

Planen Sie mit Exponential 6.0.x keine Produktionswebsite auf SQL Server. Installieren Sie keinen online gefundenen Treiber
eines Dritten ohne vollständigen Test auf einer Produktionswebsite: Das Projekt hat keinen SQL-Server-Treiber auf
Exponential 6 betrieben.

## Was es gibt, um darauf aufzubauen

- **Eine kleine Treiberschnittstelle.** Jede Datenbank wird über `eZDBInterface` erreicht
  (`lib/ezdb/classes/ezdbinterface.php`); fünf Treiber implementieren sie bereits, für eine Dateidatenbank, drei SQL-Server
  und einen Dokumentenspeicher ([Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md)).
- **Ein engine-neutrales Schema.** `share/db_schema.dba` und `share/db_data.dba` werden von einem Schema-Handler in die DDL
  jeder Engine umgewandelt ([Das .dba-Schemaformat](das-dba-schemaformat.md)).
- **Austauschbare Schema-Handler und Aliase.** `settings/dbschema.ini` (`SchemaPaths[]`, `SchemaHandlerClasses[]`) und
  `site.ini [DatabaseSettings] ImplementationAlias[]` lassen sich aus einer Erweiterung erweitern, so wie `ezoracle` es tut.
- **PHP-Treiber.** Microsoft veröffentlicht `sqlsrv` und `pdo_sqlsrv` für PHP unter Linux und Windows; prüfen Sie Microsofts
  Unterstützungsmatrix für PHP-Versionen. Nicht mit Exponential getestet.
- **Werkzeuge, die SQL Server lesen** (keine Wege, eine Website zu betreiben): Der Datenbankmanager der Erweiterung
  `sevenx_dse` liefert einen MS-SQL-Treiber zum Durchsehen von Tabellen.

## Die Erweiterung xrowmssql

[se7enxweb/xrowmssql](https://github.com/se7enxweb/xrowmssql) ist eine SQL-Server-Erweiterung, geschrieben für die Linie 4.x
und SQL Server 2005/2008, im Oktober 2013 von `xrow/xrowmssql` kopiert. Sie hat einen Treiber (`eZMSSQLDB`), einen
Schema-Handler (`eZMssqlSchema`), Einstellungen und Installationshinweise. Sie wurde nicht auf Exponential 6 betrieben. Um
dort zu funktionieren, braucht sie mindestens: einen PHP-8-Konstruktor, das Backend `sqlsrv` unter Linux (die alten
`mssql_*`-Funktionen gibt es seit PHP 7 nicht mehr), echtes Offset und Limit (`OFFSET ... FETCH`, SQL Server 2012 und
neuer), `SCOPE_IDENTITY()` statt `@@identity` und `NVARCHAR(MAX)` statt `ntext`. Ihre `extension.xml` nennt GPL v2, während
ihre Datei `LICENSE` GPL v3 ist; prüfen Sie beides, bevor Sie Code wiederverwenden.

## Was fehlt

| Teil | Was dazugehört |
|---|---|
| Ein Treiber | `eZDBInterface` auf `sqlsrv`: Verbinden, Abfragen, Offset und Limit, Transaktionen, `lastSerialID()`, Maskierung, Zeichenkettenhelfer, `md5()` über `HASHBYTES` |
| Das Aggregat `BIT_OR` | SQL Server hat keines; die Inhalts-Engine fasst Sprachmasken damit zusammen |
| `UPDATE ... JOIN` | Auf SQL Server als `UPDATE t SET ... FROM t JOIN ...` geschrieben |
| Ein Schema-Handler | `.dba` in SQL-Server-DDL und zurück, für den Installer und die Upgrade-Prüfung |
| SQL- und Update-Dateien | `kernel/sql/<engine>/`, `update/database/<engine>/` |
| Installer-Einträge | der Assistent, `settings/setup.ini [database_extensions]`, `exp:install --db` |
| Session-Anweisungen, Groß-/Kleinschreibung und Sortierfolge | reine MySQL-`SET` ignorieren; eine UTF-8-Sortierfolge (2019 und neuer) oder `NVARCHAR` wählen |
| Erweiterungen | jede Erweiterung mit eigenem SQL prüfen |
| Tests und CI | ein CI-Job mit einem SQL-Server-Container |

Wie groß diese Arbeit ist, wurde nicht gemessen, und es wird keine Schätzung gegeben.

## Was Sie in der Zwischenzeit tun

- Betreiben Sie die Website auf einer der sechs unterstützten Engines. PostgreSQL ist der empfohlene Server
  ([Eine Engine wählen](eine-engine-waehlen.md)).
- Verlangt Ihre Organisation Berichte aus SQL Server, replizieren oder exportieren Sie Daten aus der unterstützten Engine mit
  Ihren üblichen Integrationswerkzeugen nach SQL Server; die Website selbst behält ihre eigene Datenbank.
- Behandeln Sie jede Behauptung einer SQL-Server-Unterstützung, der Sie begegnen, als ungeprüft, bis sie einen
  veröffentlichten, getesteten Treiber nennt.

## Die Arbeit beitragen

Wer sie übernehmen möchte, ist willkommen: Beginnen Sie mit xrowmssql für die Besonderheiten von SQL Server und mit
`ezoracle` als Vorbild eines aktuellen Treibers, der außerhalb des Kernels ausgeliefert wird, und eröffnen Sie ein Issue im
[Software-Tracker](https://github.com/se7enxweb/exponential/issues), um sich abzustimmen.

## Verwandte Seiten

- [Datenbankunterstützung: Microsoft SQL Server](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)
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
