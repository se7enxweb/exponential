---
title: Code für jede unterstützte Datenbank
audience: php-developer, developer
category: php-development
order: 190
summary: PHP und SQL schreiben, die auf allen sechs von Exponential unterstützten Datenbanken funktionieren - SQLite 3 (der Standard), PostgreSQL, MongoDB, MariaDB, Oracle und MySQL - die Treiberklassen, die portablen Hilfsfunktionen von eZDBInterface, Schemadateien statt handgeschriebener DDL, die Grenzen des MongoDB-Adapters und wie Sie über Engines hinweg testen.
keywords: Datenbanken, portables SQL, Treiber, Schema, Datenbanktests, databases, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, DatabaseImplementation, eZSQLite3DB, eZPostgreSQLDB, expMongoDB, eZMySQLiDB, eZOracleDB, portable SQL, subString, concatString, bitAnd, dba schema, MONGO TODO
type: help
---
# Code für jede unterstützte Datenbank

Diese Seite richtet sich an PHP-Entwickler, deren Code SQL ausführt. Exponential 6.0.x unterstützt sechs Datenbanken,
und eine Erweiterung, die für die Community ausgeliefert wird, kann auf jede von ihnen treffen. Die Aussage zur
Unterstützung mit Versionen und Einstellungen finden Sie unter [Datenbankunterstützung](../datenbankunterstuetzung.md); diese
Seite handelt davon, Code zu schreiben, der auf allen funktioniert.

## Inhalt

1. [Die sechs Datenbanken und ihre Treiber](#die-sechs-datenbanken-und-ihre-treiber)
2. [Regeln für portables SQL](#regeln-für-portables-sql)
3. [Die portablen Hilfsfunktionen](#die-portablen-hilfsfunktionen)
4. [Schema: .dba-Dateien, nicht DDL](#schema-dba-dateien-nicht-ddl)
5. [MongoDB ist anders](#mongodb-ist-anders)
6. [Prüfen, welche Datenbank läuft](#prüfen-welche-datenbank-läuft)
7. [Über Engines hinweg testen](#über-engines-hinweg-testen)

## Die sechs Datenbanken und ihre Treiber

In der Reihenfolge der Präferenz des Projekts:

| Datenbank | `DatabaseImplementation` | Treiberklasse | PHP-Erweiterung |
|---|---|---|---|
| 1. SQLite 3 (der Standard des Installers) | `sqlite3` | `eZSQLite3DB` | `sqlite3` |
| 2. PostgreSQL | `ezpostgresql` | `eZPostgreSQLDB` | `pgsql` |
| 3. MongoDB | `mongodb` | `expMongoDB` | `mongodb` und die Bibliothek `mongodb/mongodb` |
| 4. MariaDB | `ezmysqli` | `eZMySQLiDB` | `mysqli` |
| 5. Oracle | `ezoracle` | `eZOracleDB` (Erweiterung `ezoracle`) | `oci8` |
| 6. MySQL | `ezmysqli` | `eZMySQLiDB` | `mysqli` |

Microsoft SQL Server ist mit zusätzlichem Aufwand möglich ([Datenbankunterstützung](../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)).

## Regeln für portables SQL

| Tun Sie | Vermeiden Sie |
|---|---|
| Tabellen- und Spaltennamen in Kleinbuchstaben, ohne Anführungszeichen (`acme_report_item`) | reservierte Wörter, gemischte Schreibung, Anführung mit Backticks |
| `arrayQuery( $sql, array( 'limit' => 20, 'offset' => 40 ) )` | `LIMIT 40, 20` im SQL |
| Standard-Joins und `WHERE`-Klauseln | `UPDATE ... JOIN` (SQLite braucht 3.33 und eine Umschreibung; der Treiber schreibt die des Kernels selbst um) |
| Ganzzahlen für Zeitstempel (`time()`) | Engine-spezifische Datumstypen und -funktionen |
| die Hilfsfunktionen unten für Strings und Bits | von Hand geschriebenes `CONCAT()`, `SUBSTRING()`, `&` |
| kurze Transaktionen | lang laufende Schreibvorgänge (SQLite erlaubt jeweils nur einen Schreiber) |
| `utf8mb4` bei MariaDB und MySQL | `utf8` (drei Bytes), das Zeichen außerhalb der Basic Multilingual Plane verliert |

## Die portablen Hilfsfunktionen

`eZDBInterface` bietet Funktionen, die jeder Treiber in seinem eigenen Dialekt schreibt:

| Hilfsfunktion | Liefert |
|---|---|
| `$db->concatString( array( 'a', "'-'", 'b' ) )` | String-Verkettung |
| `$db->subString( 'title', 1, 10 )` | einen Teilstring-Ausdruck |
| `$db->md5( 'title' )` | einen MD5-Ausdruck |
| `$db->bitAnd( 'flags', 4 )` | einen Ausdruck für bitweises UND |
| `$db->generateSQLINStatement( $ids, 'id', false, true, 'int' )` | eine `IN`-Liste mit gecasteten Werten |
| `$db->escapeString( $value )` | die String-Maskierung des Treibers |
| `$db->databaseServerVersion()` | die Serverversion, wenn Sie verzweigen müssen |

Nach der Klasse des Treibers zu verzweigen ist das letzte Mittel; bevorzugen Sie eine Hilfsfunktion, oder verlagern Sie
die Abfrage in `eZPersistentObject`.

## Schema: .dba-Dateien, nicht DDL

Liefern Sie keine `CREATE TABLE`-Anweisungen für eine einzelne Engine aus. Beschreiben Sie Ihre Tabellen einmal in einer
`.dba`-Schemadatei im `share/` der Erweiterung und lassen Sie den Schema-Handler (`lib/ezdbschema/`) die DDL für jede
Engine erzeugen; das Schema des Kernels selbst liegt auf dieselbe Weise vor, mit SQL je Engine in `kernel/sql/`.
Update-Skripte je Engine liegen in `update/database/sqlite/`, `postgresql/` und `mysql/`
([Datenbankschema und .dba-Dateien](../ersteller/anwendungsentwickler/schema-und-dba-dateien.md)).

## MongoDB ist anders

Der MongoDB-Adapter `expMongoDB` (seit 6.0.14) führt niemals echtes SQL aus. Er übersetzt die SQL-förmigen Aufrufe des
Kernels in Operationen `find`, `aggregate`, Insert, Upsert und Delete:

- Handgeschriebenes SQL, das er nicht übersetzen kann, liefert ein **leeres Ergebnis** und protokolliert eine Warnung;
  durchsuchen Sie die Logs nach `MONGO TODO`. Erweiterungen mit eigenem SQL brauchen möglicherweise einen MongoDB-Pfad.
- Der Adapter braucht PHP 8.2 oder neuer; der Rest des Kernels läuft unter 8.0.
- Der SQL-Abfrage-Cache gilt nicht.
- Es gibt keine SQL-Update-Dateien; die Upgrade-Prüfung vergleicht Collections.

Code, der `eZPersistentObject` und die Content-API verwendet, funktioniert weit öfter ohne Änderungen als
handgeschriebenes SQL ([MongoDB als Datenbank](../../funktionen/6.0/mongodb-datenbankunterstuetzung.md),
[Referenz zur MongoDB-Portierung](../../bc/6.0/mongodb-kernel-unterstuetzung.md)).

## Prüfen, welche Datenbank läuft

```php
$db = eZDB::instance();
$name = $db->databaseName();    // 'sqlite', 'postgresql', 'mongo', 'mysql' or 'oracle'
```

MariaDB und MySQL antworten beide mit `mysql`, weil sie sich den Treiber teilen. Auf der Kommandozeile:

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation
```

In der Administration hat **Setup > Systeminformationen** eine Karte **Datenbank**
([Datenbankunterstützung](../datenbankunterstuetzung.md#welche-datenbank-verwendet-meine-site)).

## Über Engines hinweg testen

| Engine | Wie Sie Ihren Code testen |
|---|---|
| SQLite | die Standardinstallation; Ihr Entwicklungsrechner |
| PostgreSQL, MySQL | `tests/runtests.php --dsn ... --db-per-test`, wie die CI ([Datenbank- und Live-Tests](datenbank-und-live-tests.md)) |
| MariaDB | durch den MySQL-Treiber abgedeckt; testen Sie auf MariaDB, wenn Ihr SQL ungewöhnlich ist |
| MongoDB | eine Installation mit `--db=mongodb`; lesen Sie die Logs nach `MONGO TODO` |
| Oracle | eine Installation mit der Erweiterung `ezoracle` |

Testen Sie mindestens auf SQLite und auf einer Server-Datenbank, bevor Sie ein Release veröffentlichen.

## Verwandte Seiten

- [SQL mit eZDB, sicher](sql-mit-ezdb.md)
- [Standard für Datenbanken und SQL](../../standards/datenbanken-und-sql.md)
- [Kapitel 9: Datenbanken](../../installation/09-datenbanken.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
