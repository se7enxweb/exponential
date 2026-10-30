---
title: Muster in extension/sevenx_dse
audience: developer
category: standards
order: 474
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/sevenx_dse mit den Entwurfsmustern.
keywords: extension/sevenx_dse, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/sevenx_dse

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/sevenx_dse` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (53)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `AdminNeo\Admin` | class extends Origin | – | `extension/sevenx_dse/adminneo/core/Admin.php:7` | – |
| `AdminNeo\ClickHouseConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:14` | – |
| `AdminNeo\ClickHouseDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:196` | – |
| `AdminNeo\ClickHouseResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:130` | – |
| `AdminNeo\Config` | class | – | `extension/sevenx_dse/adminneo/core/Config.php:5` | – |
| `AdminNeo\Connection` | abstract class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/core/Connection.php:5` | – |
| `AdminNeo\Driver` | abstract class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – |
| `AdminNeo\Drivers` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_dse/adminneo/core/Drivers.php:5` | – |
| `AdminNeo\DummyConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/core/DummyConnection.php:5` | – |
| `AdminNeo\ElasticConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:15` | – |
| `AdminNeo\ElasticDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:159` | – |
| `AdminNeo\ElasticResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:123` | – |
| `AdminNeo\EzExit` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `extension/sevenx_dse/adminneo/core/EzExit.php:13` | Wird anstelle von exit() ausgelöst, wenn AdminNeo in Exponential eingebettet ist. |
| `AdminNeo\FileModes` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_dse/adminneo/core/FileModes.php:11` | Gibt den Dateien und Verzeichnissen, die AdminNeo anlegt (kompilierte Assets, Sperrdateien), ihre Rechte innerhalb der Grenzen, die der Exponential-Kernel setzt (EZP_FILE_MO… |
| `AdminNeo\Hash` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_dse/adminneo/core/Hash.php:5` | – |
| `AdminNeo\Locale` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/sevenx_dse/adminneo/core/Locale.php:5` | – |
| `AdminNeo\MongoConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:23` | – |
| `AdminNeo\MongoDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:204` | – |
| `AdminNeo\MongoResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:114` | – |
| `AdminNeo\MsSqlConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:20` | – |
| `AdminNeo\MsSqlDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:310` | – |
| `AdminNeo\MsSqlPdoConnection` | abstract class extends PdoConnection | [Modulansicht (Page Controller)](modulansicht.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:236` | – |
| `AdminNeo\MsSqlResult` | class extends Result | – | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:135` | – |
| `AdminNeo\MySqlConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:19` | – |
| `AdminNeo\MySqlDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:242` | – |
| `AdminNeo\MySqlResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:145` | – |
| `AdminNeo\OracleConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:14` | – |
| `AdminNeo\OracleDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:215` | – |
| `AdminNeo\OracleResult` | class extends Result | – | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:102` | – |
| `AdminNeo\Origin` | abstract class extends Plugin | [Fabrik und statische Fabrikmethoden](fabrik.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/core/Origin.php:7` | – |
| `AdminNeo\PdoConnection` | abstract class extends Connection | [Modulansicht (Page Controller)](modulansicht.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/include/pdo.inc.php:11` | – |
| `AdminNeo\PdoResult` | class extends Result | – | `extension/sevenx_dse/adminneo/include/pdo.inc.php:84` | – |
| `AdminNeo\PgSqlConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:14` | – |
| `AdminNeo\PgSqlDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:279` | – |
| `AdminNeo\PgSqlResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:149` | – |
| `AdminNeo\Plugin` | abstract class | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/core/Plugin.php:13` | Plugin-Elternklasse. |
| `AdminNeo\Pluginer` | class | – | `extension/sevenx_dse/adminneo/core/Pluginer.php:14` | Anpassung für Admin/Editor, die die Verwendung von Plugins erlaubt. |
| `AdminNeo\Queries` | class | – | `extension/sevenx_dse/adminneo/include/functions.inc.php:572` | Leitet anhand gespeicherter Abfragen weiter. |
| `AdminNeo\Random` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/sevenx_dse/adminneo/core/Random.php:7` | – |
| `AdminNeo\Result` | abstract class | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md), [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/core/Result.php:7` | – |
| `AdminNeo\Server` | class | – | `extension/sevenx_dse/adminneo/core/Server.php:5` | – |
| `AdminNeo\Settings` | class | – | `extension/sevenx_dse/adminneo/core/Settings.php:5` | – |
| `AdminNeo\SimpleDbConnection` | class extends Connection | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:14` | – |
| `AdminNeo\SimpleDbDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:183` | – |
| `AdminNeo\SimpleDbResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:96` | – |
| `AdminNeo\SqLiteConnection` | class extends SqLiteConnectionBase | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:129` | – |
| `AdminNeo\SqLiteConnectionBase` | abstract class extends Connection | [Modulansicht (Page Controller)](modulansicht.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:18` | – |
| `AdminNeo\SqliteDriver` | class extends Driver | [Strategie / Handler](strategie-handler.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:174` | – |
| `AdminNeo\SqLiteResult` | class extends Result | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:65` | – |
| `AdminNeo\TmpFile` | class | – | `extension/sevenx_dse/adminneo/core/TmpFile.php:5` | – |
| `Exponential\View\Extension\SevenxDse\Dse\Adminneo` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/classes/runnable/views/dse/adminneo.php:28` | Der Code von extension/sevenx_dse/modules/dse/adminneo.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\SevenxDse\Dse\Dashboard` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/sevenx_dse/classes/runnable/views/dse/dashboard.php:50` | Der Code von extension/sevenx_dse/modules/dse/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). |
| `sevenx_dseInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/sevenx_dse/ezinfo.php:7` | – |
