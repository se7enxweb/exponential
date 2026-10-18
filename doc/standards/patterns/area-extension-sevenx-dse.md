---
title: Patterns in extension/sevenx_dse
audience: developer
category: standards
order: 469
summary: Every class, view, fetch function, operator and plugin point of extension/sevenx_dse with its design patterns.
keywords: extension/sevenx_dse, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/sevenx_dse

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/sevenx_dse` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (53)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `AdminNeo\Admin` | class extends Origin | – | `extension/sevenx_dse/adminneo/core/Admin.php:7` | – |
| `AdminNeo\ClickHouseConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:14` | – |
| `AdminNeo\ClickHouseDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:196` | – |
| `AdminNeo\ClickHouseResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/clickhouse.inc.php:130` | – |
| `AdminNeo\Config` | class | – | `extension/sevenx_dse/adminneo/core/Config.php:5` | – |
| `AdminNeo\Connection` | abstract class | [Factory and static factory methods](factory.md), [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/core/Connection.php:5` | – |
| `AdminNeo\Driver` | abstract class | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/core/Driver.php:5` | – |
| `AdminNeo\Drivers` | class | [Utility (static helper)](utility.md) | `extension/sevenx_dse/adminneo/core/Drivers.php:5` | – |
| `AdminNeo\DummyConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/core/DummyConnection.php:5` | – |
| `AdminNeo\ElasticConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:15` | – |
| `AdminNeo\ElasticDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:159` | – |
| `AdminNeo\ElasticResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/elastic.inc.php:123` | – |
| `AdminNeo\EzExit` | class extends RuntimeException | [Exception hierarchy](exception.md) | `extension/sevenx_dse/adminneo/core/EzExit.php:13` | Thrown instead of exit() when AdminNeo is embedded inside Exponential. |
| `AdminNeo\FileModes` | class | [Utility (static helper)](utility.md) | `extension/sevenx_dse/adminneo/core/FileModes.php:11` | Gives the files and directories AdminNeo creates (compiled assets, lock files) their modes within the limits the Exponential kernel sets (EZP_FILE_MO… |
| `AdminNeo\Hash` | class | [Utility (static helper)](utility.md) | `extension/sevenx_dse/adminneo/core/Hash.php:5` | – |
| `AdminNeo\Locale` | class | [Factory and static factory methods](factory.md) | `extension/sevenx_dse/adminneo/core/Locale.php:5` | – |
| `AdminNeo\MongoConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:23` | – |
| `AdminNeo\MongoDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:204` | – |
| `AdminNeo\MongoResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/mongo.inc.php:114` | – |
| `AdminNeo\MsSqlConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:20` | – |
| `AdminNeo\MsSqlDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:310` | – |
| `AdminNeo\MsSqlPdoConnection` | abstract class extends PdoConnection | [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:236` | – |
| `AdminNeo\MsSqlResult` | class extends Result | – | `extension/sevenx_dse/adminneo/drivers/mssql.inc.php:135` | – |
| `AdminNeo\MySqlConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:19` | – |
| `AdminNeo\MySqlDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:242` | – |
| `AdminNeo\MySqlResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/mysql.inc.php:145` | – |
| `AdminNeo\OracleConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:14` | – |
| `AdminNeo\OracleDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:215` | – |
| `AdminNeo\OracleResult` | class extends Result | – | `extension/sevenx_dse/adminneo/drivers/oracle.inc.php:102` | – |
| `AdminNeo\Origin` | abstract class extends Plugin | [Factory and static factory methods](factory.md), [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/core/Origin.php:7` | – |
| `AdminNeo\PdoConnection` | abstract class extends Connection | [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/include/pdo.inc.php:11` | – |
| `AdminNeo\PdoResult` | class extends Result | – | `extension/sevenx_dse/adminneo/include/pdo.inc.php:84` | – |
| `AdminNeo\PgSqlConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:14` | – |
| `AdminNeo\PgSqlDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:279` | – |
| `AdminNeo\PgSqlResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/pgsql.inc.php:149` | – |
| `AdminNeo\Plugin` | abstract class | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/core/Plugin.php:13` | Plugin parent class. |
| `AdminNeo\Pluginer` | class | – | `extension/sevenx_dse/adminneo/core/Pluginer.php:14` | Admin/Editor customization allowing usage of plugins. |
| `AdminNeo\Queries` | class | – | `extension/sevenx_dse/adminneo/include/functions.inc.php:572` | Redirects by remembered queries. |
| `AdminNeo\Random` | class | [Utility (static helper)](utility.md) | `extension/sevenx_dse/adminneo/core/Random.php:7` | – |
| `AdminNeo\Result` | abstract class | [Template Method / abstract base class](template-method.md), [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/core/Result.php:7` | – |
| `AdminNeo\Server` | class | – | `extension/sevenx_dse/adminneo/core/Server.php:5` | – |
| `AdminNeo\Settings` | class | – | `extension/sevenx_dse/adminneo/core/Settings.php:5` | – |
| `AdminNeo\SimpleDbConnection` | class extends Connection | – | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:14` | – |
| `AdminNeo\SimpleDbDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:183` | – |
| `AdminNeo\SimpleDbResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/simpledb.inc.php:96` | – |
| `AdminNeo\SqLiteConnection` | class extends SqLiteConnectionBase | – | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:129` | – |
| `AdminNeo\SqLiteConnectionBase` | abstract class extends Connection | [Template Method / abstract base class](template-method.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:18` | – |
| `AdminNeo\SqliteDriver` | class extends Driver | [Strategy / handler](strategy-handler.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:174` | – |
| `AdminNeo\SqLiteResult` | class extends Result | [Value object / struct](value-object.md) | `extension/sevenx_dse/adminneo/drivers/sqlite.inc.php:65` | – |
| `AdminNeo\TmpFile` | class | – | `extension/sevenx_dse/adminneo/core/TmpFile.php:5` | – |
| `Exponential\View\Extension\SevenxDse\Dse\Adminneo` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/sevenx_dse/classes/runnable/views/dse/adminneo.php:28` | The code of extension/sevenx_dse/modules/dse/adminneo.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\SevenxDse\Dse\Dashboard` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/sevenx_dse/classes/runnable/views/dse/dashboard.php:50` | The code of extension/sevenx_dse/modules/dse/dashboard.php, moved into a class (#207 stage 1). |
| `sevenx_dseInfo` | class | [Value object / struct](value-object.md) | `extension/sevenx_dse/ezinfo.php:7` | – |
