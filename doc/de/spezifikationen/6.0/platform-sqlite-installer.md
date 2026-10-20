# Spezifikation: der SQLite-Installer von Exponential Platform

Diese Seite ist die Referenz dafür, wie die Installer von Exponential Platform (der auf Symfony basierenden Plattform) eine
vollständige Datenbank in einer einzigen SQLite-Datei anlegen: die Installationstypen, wie sich der Installationsbefehl unter
SQLite verhält, wie Schema und Startdaten aufgebaut werden und welche Einstellungen SQLite auswählen. Lesen Sie sie, wenn Sie
einen Plattform-Installer pflegen oder wissen müssen, warum sich eine Installation unter SQLite so verhält, wie sie es tut.
Schritt-für-Schritt-Anweisungen finden Sie unter
[SQLite für Exponential Platform](../../../en/us/features/6.0/platform-sqlite-install.md). Für den eigenen Treiber des Legacy-Kernels
siehe [SQLite3-Datenbanktreiber](sqlite3-datenbanktreiber.md).

## Kurz gesagt

- Ein Installationstyp ist eine benannte Installer-Klasse; `install <type>` führt deren `importSchema()`, `importData()` und
  `importBinaries()` aus.
- Startdatendateien werden je Datenbank-Engine gesucht: `<baseDataDir>/<dbms>/<relative>`, wobei `<dbms>` einer der Werte
  `mysql`, `postgresql` oder `sqlite` ist.
- Unter SQLite legt der Installationsbefehl keine Datenbank an; die Datei entsteht bei der ersten Verbindung.
- Zusammengesetzte Primärschlüssel (`id`, `version`) bleiben erhalten, sodass Entwürfe ohne Fehler wegen einer verletzten
  Eindeutigkeitsbedingung bearbeitet werden können.

## Installationstypen

Ein Installationstyp wird mit einem Service-Tag registriert. Der Befehl `install <type>` führt `importSchema()`,
`importData()` und `importBinaries()` der Klasse hinter dem Namen aus.

| Typ | Klasse | Tag | Linie | Was installiert wird |
|---|---|---|---|---|
| `exponential-oss` | `ExponentialOssInstaller` (erweitert `CoreInstaller`) | `ezplatform.installer` (Linie 2.5, `ezpublish-kernel`, 2026-04-09); `ibexa.installer` (Platform v5: der Nexus-Starter und `exponential-platform-dxp-core` registrieren ihn jeweils) | 2.5, v5 | Leere Plattforminhalte aus `data/<dbms>/cleandata.sql`; unter SQLite `data/sqlite/cleandata.sql` |
| `ibexa-oss` (Legacy-Alias `clean`) | `CoreInstaller` | `ezplatform.installer` (Linie 3.x / 4.6); `ibexa.installer` (v5) | 3.x, 4.6, v5 | Upstream-Startdaten. Der 3.x-Kernel und `core` in v5 bringen `data/sqlite/cleandata.sql` mit (v5: 322 Zeilen, 2026-04-29), daher läuft auch dieser Typ unter SQLite |
| `exponential-media` | `ExponentialMediaInstaller` (Paket `se7enxweb/exponential-platform-dxp-core`) | `ibexa.installer` | v5 | Die Nexus-Demo: `media_schema.sql` und `media_data.sql` der verwendeten Engine, aus `data/{mysql,sqlite,postgresql}/`. Ersetzt den nur für MySQL verfügbaren Typ `netgen-media` |
| `exponential-cjw` | `ExponentialCjwInstaller` (`src/AppBundle/Installer/`) | `ezplatform.installer` | Nexus 1.x | CJW-Starterinhalte. Unter SQLite: `sqlite/schema.sql` (164 Tabellen, zusammengesetzte Schlüssel bleiben erhalten) und `sqlite/data.sql` (41.568 Inserts mit benannten Spalten) |

### Suche nach Startdatendateien

`DbBasedInstaller::getKernelSQLFileForDBMS( $relative )` gibt `<baseDataDir>/<dbms>/<relative>` zurück. `<dbms>` ist
`mysql`, `postgresql` oder `sqlite` und wird über einen `instanceof`-Test auf die Doctrine-Plattform gewählt (seit
2026-04-29). Eine fehlende oder nicht lesbare Datei bricht die Installation mit einer Fehlermeldung ab, die den Pfad nennt.

## Der Installationsbefehl unter SQLite

`InstallPlatformCommand::checkCreateDatabase()` tut drei Dinge, wenn die Plattform der Verbindung SQLite ist:

1. Er überspringt `doctrine:database:create --if-not-exists`. Dieser Befehl schlägt unter SQLite mit
   „getListDatabasesSQL is not supported by platform“ fehl.
2. Bei einer Dateidatenbank (nicht `:memory:`) legt er das übergeordnete Verzeichnis mit dem Modus `0755` an, wenn es fehlt.
   Schlägt das fehl, endet der Befehl mit dem allgemeinen Exit-Code für Datenbankfehler.
3. Er gibt `Using SQLite database <path> (file created automatically on first connection).` aus und kehrt zurück.

Andere Engines behalten den Weg über den Unterprozess. In der Linie 2.5 ist der Indizierungsschritt in ein try/catch
eingeschlossen, sodass eine nicht fatale Warnung bei der Neuindizierung einer leeren Installation die Installation nicht
abbricht.

## Erzeugung des Schemas

`CoreInstaller::importSchema()` baut das Schema aus den Ereignissen des Schema-Builders auf und erzeugt die DDL für die
Plattform der Verbindung. Unter SQLite wird das Plattformobjekt, wenn die Verbindung die nackte `SqlitePlatform` meldet,
durch `Ibexa\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform` ersetzt.

Warum: Tabellen mit einem zusammengesetzten Primärschlüssel (`id`, `version`), etwa die Tabelle der Inhaltsfelder, dürfen
kein `AUTOINCREMENT` auf `id` tragen. SQLite unterstützt diese Kombination nicht. Die generische Plattform erzeugte einen
einspaltigen Schlüssel, sodass das Bearbeiten eines Entwurfs eine Verletzung der Eindeutigkeitsbedingung auslöste: Dieselbe
Feld-ID kommt in mehreren Versionen vor.

Der 3.x- / 4.6-Kernel ist älter als diese Klasse, daher wird der Fehler stattdessen in der Startdatendatei korrigiert.
`data/sqlite/cleandata.sql` beginnt nach den Pragmas mit Blöcken aus `DROP TABLE IF EXISTS`, `CREATE TABLE`
(zusammengesetzter Schlüssel) und `CREATE INDEX` für `ezcontentclass`, `ezcontentclass_attribute` und
`ezcontentobject_attribute` (96 Zeilen), noch vor jedem Insert. Aus demselben Grund wurde außerdem ein `AUTOINCREMENT` von
`ezcontentobject_attribute.id` entfernt.

## Konventionen für Startdaten

- Startdatendateien sind SQL mit einer Anweisungsgruppe je Tabelle: `INSERT INTO "<table>" (...) VALUES (...)`.
- MySQL-Escape-Sequenzen werden umgewandelt: `\'` wird zu `''`, `\"` wird zu `"`, `\\` wird zu `\`.
- In den Startdaten der Plattform werden `\n`, `\r` und `\t` zu echtem Leerraum. Die `data.sql` von Nexus 1.x behält sie als
  zweistellige Literale, damit Anweisungen sicher getrennt werden können, und wandelt sie nach dem Trennen in
  `runQueriesFromFile()` um. XML-Feldwerte werden dann korrekt geparst.
- An die Startdaten von `ibexa-oss` im 3.x-Kernel sind 73 Tabellen des Legacy-Schemas angehängt, sodass der Legacy-Kernel die
  Datei mitnutzen kann (2026-03-29).

## Einstellungen

| Linie | Datei | Einstellung | Standard | Geltungsbereich |
|---|---|---|---|---|
| Platform v5 | `.env.local` | `DATABASE_URL` | `sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db` (dokumentierter Wert) | je Umgebung |
| Platform v5 | `.env.local` | `MESSENGER_TRANSPORT_DSN` | `sync://` (dokumentierter Wert; mit SQLite erforderlich) | je Umgebung |
| Linie 2.5 | `app/config/parameters.yml` | `database_driver` | `pdo_sqlite` wählt SQLite | Projekt |
| Linie 2.5 | `app/config/default_parameters.yml` | `database_path` | `%kernel.project_dir%/var/data_%kernel.environment%.db` | Projekt |
| Linie 2.5 | Umgebungsvariable | `DATABASE_PATH` | nicht gesetzt; überschreibt `database_path` über `app/config/env/generic.php` | Prozess |
| Linie 2.5 | `app/config/config.yml` | `doctrine.dbal.default_connection.path` | `'%database_path%'` | Projekt |
| Nexus 1.x | `app/config/default_parameters.yml` | `ngsite.default.locations.tree_root.id` | `168` in den CJW-Startdaten | Projekt |

## Der Legacy-Kernel auf derselben Datei

`LegacyMapper\Configuration` in der Legacy-Bridge führt eine Zuordnung von Doctrine-Treibern zu Legacy-Treibern. Seit
`v4.0.0.1` enthält sie `pdo_sqlite => sqlite3`. Für diesen Treiber schreibt sie Doctrines Parameter `path` in den
Legacy-Wert `site.ini` `[DatabaseSettings] Database`, weil Doctrine die SQLite-Datei unter `path` ablegt, nicht unter
`dbname`. Vorher erhielt der Legacy-Kernel keinen Datenbanknamen.

## Eine Installation prüfen

Führen Sie Folgendes im Wurzelverzeichnis des Projekts aus (nur lesend):

```bash
php -m | grep -i sqlite                                    # expect pdo_sqlite and sqlite3
sqlite3 var/data_dev.db ".tables" | tr -s ' ' '\n' | wc -l # number of tables
sqlite3 var/data_dev.db "SELECT COUNT(*) FROM ezcontentobject;"
```

`ezcontentobject` ist die Inhaltstabelle im 3.x-Kernel; unter v5 fragen Sie stattdessen `ibexa_content` ab.

## Verwandte Seiten

- Spezifikationen: [Paketübersicht der Plattform](platform-paketzuordnung.md), [Namen der Konsolenbefehle der Plattform](platform-konsolenbefehle.md), [Legacy-Bridge-Bundle](legacy-bridge-bundle.md), [SQLite3-Datenbanktreiber](sqlite3-datenbanktreiber.md)
- Funktionen: [SQLite für Exponential Platform](../../../en/us/features/6.0/platform-sqlite-install.md), [SQLite-Datenbankunterstützung](../../../en/us/features/6.0/sqlite-database.md), [Administrationsoberfläche der Plattform](../../../en/us/features/6.0/platform-admin-ui-fork.md), [DXP-Skeleton](../../../en/us/features/6.0/platform-dxp-skeleton.md), [Layouts auf der Plattform](../../../en/us/features/6.0/platform-layouts-core-fork.md), [Nexus-Starter](../../../en/us/features/6.0/platform-nexus-starter.md), [Framework-Forks für PHP 8.5](../../../en/us/features/6.0/platform-php85-framework-forks.md), [Site-Bundles](../../../en/us/features/6.0/platform-site-bundles.md), [Legacy-Bridge](../../../en/us/features/6.0/legacy-bridge.md), [Datenbankverwaltung AdminNeo](../../../en/us/features/6.0/adminneo-database-manager.md)
- Upgrade-Hinweise: [Paket-Forks und umbenannte Befehle](../../../en/us/bc/6.0/platform-package-forks-and-command-renames.md), [SQLite: Transaktionen warten in einer Schlange auf die Schreibsperre](../../../en/us/bc/6.0/sqlite-transactions.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Geschichte: [Überblick über das Ökosystem](../../../en/us/history/ecosystem.md), [Monate des Ökosystems](../../../en/us/history/ecosystem/months/2026-04.md), [Änderungsverzeichnis](../../../en/us/history/ledger/README.md); Repositorys [core](../../../en/us/history/ecosystem/core.md), [ezplatform-kernel](../../../en/us/history/ecosystem/ezplatform-kernel.md), [ezpublish-kernel](../../../en/us/history/ecosystem/ezpublish-kernel.md), [exponential-platform-legacy](../../../en/us/history/ecosystem/exponential-platform-legacy.md), [exponential-platform-nexus](../../../en/us/history/ecosystem/exponential-platform-nexus.md), [legacyBridge](../../../en/us/history/ecosystem/legacyBridge.md); Monate [Januar 2024, erste Hälfte](../../../en/us/history/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../../en/us/history/2024/2024-01b.md), [Juni 2026, zweite Hälfte](../../../en/us/history/2026/2026-06b.md)
