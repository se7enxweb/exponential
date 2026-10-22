---
title: Die Referenz zu DatabaseSettings
audience: dba
category: database-administration
order: 6
summary: Jeder Schlüssel von site.ini [DatabaseSettings] mit seinem mitgelieferten Standard, den Engines, die ihn lesen, und dem, was er tut - Verbindung, Zeichensatz, Wiederholungen, persistente Verbindungen, Replikate, Transaktionen, Debug-Ausgabe und die SQLite-Schlüssel - sowie die Blöcke je Engine zum Kopieren nach settings/override.
keywords: DatabaseSettings, site.ini, DatabaseImplementation, Server, Port, Socket, User, Password, Database, Charset, UseBuiltinEncoding, ConnectRetries, UsePersistentConnection, UseSlaveServer, SlaveServerArray, Transactions, SQLOutput, SlowQueriesOutput, QueryAnalysisOutput, DebugTransactions, SQLitePragmas, SQLiteTransactionWait, ImplementationAlias, Verbindungseinstellungen, Referenz, connection settings, reference
type: help
---
# Die Referenz zu DatabaseSettings

Diese Seite richtet sich an Datenbankadministratoren, die die Verbindungseinstellungen einer Exponential-Website lesen oder
schreiben. Sie listet jeden Schlüssel von `settings/site.ini`, Block `[DatabaseSettings]`, wie er in 6.0.x mitgeliefert
wird, mit seinem Standard und den Engines, die ihn verwenden, und gibt einen minimalen Block je Engine an. Quellen:
`settings/site.ini` und [9.1 Wie Exponential einen Datenbanktreiber wählt](../../installation/09-datenbanken.md#91-wie-exponential-einen-datenbanktreiber-auswählt).

> **Hinweis**
> Bearbeiten Sie nie `settings/site.ini` selbst: Ein Upgrade ersetzt die Datei. Tragen Sie Ihre Werte in
> `settings/override/site.ini.append.php` (die ganze Installation) oder `settings/siteaccess/<name>/site.ini.append.php`
> (ein Siteaccess) ein. Siehe [Datenbankeinstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md).

## Inhalt

1. [Verbindungsschlüssel](#verbindungsschlüssel)
2. [Verhaltensschlüssel](#verhaltensschlüssel)
3. [Replikat-Schlüssel](#replikat-schlüssel)
4. [Debug-Schlüssel](#debug-schlüssel)
5. [SQLite-Schlüssel](#sqlite-schlüssel)
6. [Treiber-Aliase](#treiber-aliase)
7. [Schlüssel außerhalb dieses Blocks](#schlüssel-außerhalb-dieses-blocks)
8. [Minimale Blöcke je Engine](#minimale-blöcke-je-engine)

## Verbindungsschlüssel

| Schlüssel | Mitgelieferter Standard | Verwendet von | Bedeutung |
|---|---|---|---|
| `DatabaseImplementation` | `ezmysqli` | alle | Der Treiber-Alias ([Treiber-Aliase](#treiber-aliase)). Die Installer schreiben den Wert für Ihre Engine. |
| `Server` | `localhost` | MySQL, PostgreSQL, MongoDB | Hostname oder Adresse. Von Oracle nicht verwendet. |
| `Port` | leer | MySQL, PostgreSQL, MongoDB | Leer bedeutet den Standardport des Treibers (3306, 5432, 27017). Von Oracle nicht verwendet. |
| `Socket` | `disabled` | MySQL, MariaDB | Ein Unix-Socket-Pfad statt Host und Port. |
| `User`, `Password` | `root`, leer | alle außer SQLite | Die Anmeldung. Behalten Sie in der Produktion nie das mitgelieferte `root`. MongoDB verlangt einen Benutzer, auch ohne Zugriffskontrolle. |
| `Database` | `nextgen` | alle | Der Datenbankname; für SQLite die Datei; für Oracle der Connect-String. |
| `Charset` | `utf-8` | MySQL, Oracle | Der Zeichensatz der Verbindung ([Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md)). |
| `UseBuiltinEncoding` | `true` | | "Use charset conversion routines in DB if possible" (Kommentar in `site.ini`). |

## Verhaltensschlüssel

| Schlüssel | Mitgelieferter Standard | Verwendet von | Bedeutung |
|---|---|---|---|
| `ConnectRetries` | `0` | MySQL, PostgreSQL, SQLite | Wie oft es erneut versucht wird, wenn die erste Verbindung scheitert. MySQL und PostgreSQL warten zwischen den Versuchen; SQLite versucht es sofort erneut. Oracle hat sein eigenes `RetryCount` und fällt auf diesen Wert zurück. |
| `UsePersistentConnection` | `disabled` | MySQL, PostgreSQL, Oracle | Eine Verbindung über Anfragen desselben PHP-Prozesses hinweg wiederverwenden (Host-Präfix `p:` für mysqli, `pg_pconnect` für PostgreSQL). |
| `Transactions` | `enabled` | alle | Gruppierte Schreibzugriffe in Transaktionen ausführen. Lassen Sie es eingeschaltet. |

## Replikat-Schlüssel

| Schlüssel | Mitgelieferter Standard | Bedeutung |
|---|---|---|
| `UseSlaveServer` | `disabled` | Lesezugriffe an ein je Anfrage zufällig gewähltes Replikat senden; Schreibzugriffe gehen an `Server`. Nur MySQL und MariaDB. |
| `SlaveServerArray[]`, `SlaveServerPort[]` | auskommentiert | Hosts und Ports der Replikate. |
| `SlaverServerUser[]`, `SlaverServerPassword[]`, `SlaverServerDatabase[]` | auskommentiert | Anmeldungen und Datenbanknamen der Replikate. Die Schlüssel werden wirklich `Slaver...` geschrieben. |

Siehe [Lesereplikate für MySQL und MariaDB](mysql-lesereplikate.md).

## Debug-Schlüssel

| Schlüssel | Mitgelieferter Standard | Verwendet von | Bedeutung |
|---|---|---|---|
| `SQLOutput` | `disabled` | alle | Anweisungen in der Debug-Ausgabe zeigen. |
| `SlowQueriesOutput` | `0` | alle | Mit einem Wert über 0 nur Anweisungen, die langsamer als so viele Millisekunden sind. |
| `QueryAnalysisOutput` | `disabled` | MySQL, MariaDB | Mit `SQLOutput=enabled` MySQLs `EXPLAIN` jeder Anweisung. |
| `DebugTransactions` | `disabled` | alle | Für jedes Begin und Commit einen Stacktrace festhalten, um eine unausgeglichene Transaktion zu finden. |

Sie erscheinen nur, wenn die Debug-Ausgabe für Ihre Adresse eingeschaltet ist. Siehe [Langsame Abfragen finden](langsame-abfragen-finden.md).

## SQLite-Schlüssel

| Schlüssel | Mitgelieferter Standard | Bedeutung |
|---|---|---|
| `SQLitePragmas[]` | leer | PRAGMAs über die Standards des Treibers hinaus, ein `name=value` je Eintrag. |
| `SQLiteTransactionWait` | `25` | Sekunden, die eine Transaktion auf die Schreiber vor ihr wartet. Halten Sie den Wert unter dem Anfrage-Timeout des Webservers. |

Details: [SQLite: die Schreibwarteschlange und Sperren](sqlite-schreibwarteschlange-und-sperren.md) und
[SQLite-Wartung](sqlite-wartung.md).

## Treiber-Aliase

`ImplementationAlias[]` ordnet den Wert von `DatabaseImplementation` einer Klasse zu. Mitgeliefert:
`mysql`, `mysqli`, `ezmysql`, `ezmysqli` auf `eZMySQLiDB`; `postgresql`, `ezpostgresql`, `pgsql` auf `eZPostgreSQLDB`;
`sqlite3` auf `eZSQLite3DB`; `mongodb` auf `expMongoDB`. Die Erweiterung `ezoracle` fügt `ezoracle` und `oracle` hinzu.
Eine Erweiterung kann aus ihrer eigenen `site.ini.append.php` weitere Aliase hinzufügen.

## Schlüssel außerhalb dieses Blocks

| Einstellung | Wo | Bedeutung |
|---|---|---|
| Reine Oracle-Schlüssel `OracleEmptyStringForNull`, `OracleCaseInsensitive`, `OracleCaseInsensitiveSort` | `site.ini [DatabaseSettings]`, gelesen von `ezoracle` | Siehe [Oracle mit ezoracle einrichten](oracle-einrichten.md) |
| Verbindungspooling, Wiederholungen, Tracing für Oracle | `ezoracle.ini` | Siehe [Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md) |
| Der SQL-Abfrage-Cache | `querycache.ini [QueryCacheSettings]` | Siehe [Der SQL-Abfrage-Cache](sql-abfrage-cache.md) |
| Die Metadatendatenbank des DFS-Clusters | `file.ini [eZDFSClusteringSettings]` | Siehe [Clustering: DFS und seine Metadatendatenbank](clustering-dfs-und-die-datenbank.md) |
| Sessions in der Datenbank | `site.ini [Session] Handler` | Siehe [Sessions in der Datenbank](sessions-in-der-datenbank.md) |

## Minimale Blöcke je Engine

```ini
# SQLite 3
[DatabaseSettings]
DatabaseImplementation=sqlite3
Database=exponential.db

# PostgreSQL
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential

# MariaDB and MySQL
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
User=exponential
Password=<the password>
Database=exponential
Charset=utf-8
```

Die Blöcke für MongoDB und Oracle stehen unter [MongoDB einrichten](mongodb-einrichten.md) und [Oracle mit ezoracle einrichten](oracle-einrichten.md).
Die Platzhalterwerte sind Beispiele; verwenden Sie Ihre eigenen und committen Sie die Override-Datei nie.

## Verwandte Seiten

- [Datenbankeinstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md)
- [Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md)
- [Datenbankunterstützung, die Einstellungen](../datenbankunterstuetzung.md#die-einstellungen-databasesettings-in-siteini)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
