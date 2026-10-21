# MongoDB als Datenbank

Diese Seite ist für Administratoren, deren Organisation bereits MongoDB betreibt oder die einen Dokumentenspeicher hinter
einem ausgereiften Inhaltsmodell möchten. Exponential 6.0.14 (Juni 2026) kann das ganze Inhalts-Repository in MongoDB
statt in MySQL, PostgreSQL oder SQLite speichern, und der Einrichtungsassistent bietet **MongoDB** neben den anderen
Datenbanksystemen an. Lesen Sie die Grenzen unten, bevor Sie es für eine Produktionswebsite wählen.

Diese Seite ist der Einstieg. Die vollständige Referenz der Portierung, mit jeder Kernel-Klasse, dem Schema der
Collections und einem Testplan, ist
[MongoDB-Unterstützung im Kernel](../../bc/6.0/mongodb-kernel-unterstuetzung.md); lesen Sie dort Abschnitt 24 für
eine Installation Schritt für Schritt und Abschnitt 26 für die bekannten Grenzen.

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit mit zusätzlichem
> Aufwand bekanntermaßen möglich. Einzelheiten: [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md).

## Was hinzugekommen ist

| Teil | Datei | Rolle |
|---|---|---|
| Datenbankadapter | `lib/ezdb/classes/expmongodb.php` (`expMongoDB`, erweitert `eZDBInterface`) | Verbindet sich mit der PHP-Erweiterung `mongodb` und `MongoDB\Client`; wandelt die SQL-artigen Aufrufe des Kernels in Operationen `find`, `aggregate`, `insert`, `upsert` und `deleteWhere` um. |
| Schema-Handler | `lib/ezdbschema/classes/expmongoschema.php` (`expMongoSchema`) | Legt die Collections einer Installation an. |
| Kompatibilitätsschicht im Kernel | Kernel-Klassen und einige Ansichtsskripte (die Referenz führt sie in ihrem Abschnitt 5 auf; die Anzahl wurde nicht erneut geprüft) | Abfragen, die rohes SQL verwendeten (Inhaltsbaum, Rollen, URL-Aliase, Benutzer-Cache), wurden in portable Aufrufe umgeschrieben. |
| Index-Skript | `bin/mongodb/create_indexes.js` | Legt die Indizes an, auf die die Abfragen des Kernels angewiesen sind. |
| Konvertierungshelfer | `bin/mongodb/export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`, `validate_ndjson.sh` | Eine bestehende MySQL-Website nach MongoDB übertragen (siehe Abschnitt 18 der Referenz). |
| Änderungen am Assistenten | Schritte in `kernel/setup/` und `design/standard/templates/setup/init/database_init.tpl` | MongoDB als Wahlmöglichkeit; ein Feld **Database name**, sodass Sie sich gegen eine bestimmte Datenbank authentifizieren können statt nur gegen die Datenbank `admin`. |

## Einstellungen

| Datei | Block | Schlüssel | Wert | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `[DatabaseSettings]` | `DatabaseImplementation` | `mongodb` | Siteaccess |
| `settings/site.ini` | `[DatabaseSettings]` | `ImplementationAlias[mongodb]` | `expMongoDB` | global |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaHandlerClasses[mongo]` | `expMongoSchema` | global |
| `settings/setup.ini` | `[DatabaseSettings]` | `DefaultType` | `sqlite3`; wählen Sie im Assistenten `mongodb` | global |

Der Assistent (`kernel/setup/ezsetupcommon.php`) führt das Datenbanksystem `mongodb` mit der benötigten Version 4.0 und
Unicode-Unterstützung auf; das Referenzdokument (Abschnitt 24.1) nennt MongoDB 6.0 als praktisches Minimum und 8.3 mit
PHP 8.5 als getestete Kombination.

## Installation im Überblick

1. Voraussetzungen: PHP 8.2 oder neuer mit der Erweiterung `mongodb` (`php -m | grep mongodb`), ein MongoDB-Server, ein
   Anwendungsbenutzer mit `readWrite` und `dbAdmin` auf der Datenbank.
2. Geben Sie dem Einrichtungsassistenten Zeit: Der Konfigurationsschritt kann ein bis drei Minuten dauern. Erhöhen Sie
   vor dem Beginn `request_terminate_timeout` von PHP (PHP-FPM).
3. Öffnen Sie die Website; der Einrichtungsassistent startet. Wählen Sie auf der Datenbankseite **MongoDB** und geben
   Sie Host, Port, Datenbankname und Zugangsdaten ein.
4. Legen Sie nach dem Assistenten die Indizes an (ohne sie durchsuchen Abfragen auf Teilbäume und Nachschlagen von
   URL-Aliasen ganze Collections):

   ```bash
   mongosh "mongodb://<user>:<password>@localhost:27017/<database>" --file bin/mongodb/create_indexes.js
   ```
5. Führen Sie die Cronjobs einmal aus, um den Suchindex aufzubauen (`php runcronjobs.php`).

## Was funktioniert und was nicht (Juni 2026)

Funktioniert: Bearbeiten und Veröffentlichen von Inhalten, der Inhaltsbaum, URL-Aliase, Rollen für die
Zugriffskontrolle, die Administrationsoberfläche, die Front-Website, der Einrichtungsassistent.

Grenzen, mit denen Sie planen müssen (vollständige Liste in Abschnitt 26 der Referenz):

- Das tiefe Neuerzeugen von URL-Aliasen nach dem Verschieben von Teilbäumen ist unvollständig.
- Die Listenseiten für Rollen und Richtlinien können leer sein, obwohl die Berechtigungen durchgesetzt werden.
- Einige Cronjobs (Aufräumen des statischen Caches, Benachrichtigung, Speicherbereinigung der Sitzungen) sind nicht
  portiert.
- Die Extension `eztags` ist auf MongoDB nicht verwendbar.
- Der Bestellvorgang des Shops und die Planung von Blöcken in `ezflow` sind nicht durchgehend getestet.
- Der Adapter führt nie echtes SQL aus: Code, der handgeschriebenes SQL über `arrayQuery()` sendet, erhält nichts zurück
  und protokolliert eine Warnung (suchen Sie in den Protokollen nach `MONGO TODO`).

## Dasselbe Release hat andere Engines verbessert

Die Arbeit am Assistenten für MongoDB fand Stellen, an denen für MySQL geschriebener Code in andere Engines durchsickerte.
In denselben Wochen behoben (`e0d617f28e`, `93cdd0cf47`, `7773850ade`):

- **SQLite und PostgreSQL**: Während der Arbeit an MongoDB waren mehrere SQL-Pfade auf MySQL beschränkt worden, sodass
  diese Engines keine Inhalte luden. Jetzt verwenden sie dasselbe SQL wie MySQL, und nur MongoDB nimmt einen eigenen
  Weg.
- **MySQL auf Shared Hosting**: Ein Datenbankbenutzer mit gewöhnlichen Rechten pro Datenbank (select, insert, update,
  delete, create, drop, index, alter, lock tables, create temporary tables) kann den Assistenten abschließen; er braucht
  weder root noch SUPER noch das Recht, Datenbanken aufzulisten.
- **PostgreSQL**: Der Assistent lässt sich erneut auf einer Datenbank ausführen, die bereits Tabellen und Sequenzen hat
  (`CREATE ... IF NOT EXISTS`, vorhandene Tabellen übersprungen), und die Korrektur der Sequenzen verwendet keine in
  PostgreSQL 12 entfernte Spalte mehr.
- **Alle Engines**: `implode(null)` im Seitenlayout der Administrationsoberfläche und ein fehlender Schlüssel
  `role_limitations` im Benutzer-Cache führen nicht mehr zu Abstürzen oder Schleifen.

## Verwandte Seiten

- [MongoDB-Unterstützung im Kernel (Referenz der Portierung)](../../bc/6.0/mongodb-kernel-unterstuetzung.md)
- [Unterstützung für die Datenbank SQLite](sqlite-datenbank.md), [Spezifikation des SQLite-Treibers](../../spezifikationen/6.0/sqlite3-datenbanktreiber.md)
- [Datenbanktreiber und Installer: SQLite, PostgreSQL, MySQL und Oracle, 16. bis 30. September 2026](../../spezifikationen/6.0/datenbanktreiber-2026-09.md)
- [Paketinstallation in Teilschritten](paketinstallation-in-teilschritten.md)
- [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md)
- [Chronik: Juni 2026, erste Hälfte](../../geschichte/2026/2026-06a.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
