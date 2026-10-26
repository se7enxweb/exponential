---
title: Patterns in extension/ezoracle
audience: developer
category: standards
order: 452
summary: Every class, view, fetch function, operator and plugin point of extension/ezoracle with its design patterns.
keywords: extension/ezoracle, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezoracle

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezoracle` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZDBFileHandlerOracleBackend` | class | [Strategy / handler](strategy-handler.md) | `extension/ezoracle/clusterfilehandlers/dbbackends/oracle.php:68` | – |
| `eZDFSFileHandlerOracleBackend` | class | [Strategy / handler](strategy-handler.md) | `extension/ezoracle/clusterfilehandlers/dfsbackends/oracle.php:55` | – |
| `eZOracleDB` | class extends eZDBInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezoracle/ezdb/dbms-drivers/ezoracledb.php:50` | \class eZOracleDB ezoracledb.php \ingroup eZDB \brief Provides Oracle database functions for eZDB subsystem |
| `ezoracleInfo` | class | [Value object / struct](value-object.md) | `extension/ezoracle/ezinfo.php:11` | – |
| `eZOracleSchema` | class extends eZDBSchemaInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezoracle/ezdb/dbms-schema/ezoracleschema.php:39` | – |
| `eZOracleTools` | class | – | `extension/ezoracle/classes/ezoracletools.php:22` | Maintenance and diagnostics for an Exponential installation on Oracle, shared by the console commands (extension/ezoracle/bin/php, ext:ezoracle:*) an… |
| `ezpDbOracleClusterGateway` | class extends ezpClusterGateway | – | `extension/ezoracle/clusterfilehandlers/gateways/db.php:13` | DB/Oracle cluster gateway |
| `ezpDfsOracleClusterGateway` | class extends ezpClusterGateway | – | `extension/ezoracle/clusterfilehandlers/gateways/dfs.php:13` | DFS/Oracle cluster gateway |

## Cronjob parts (4)

| Cronjob | File | Description |
|---|---|---|
| `ezoracle_health` | `extension/ezoracle/cronjobs/ezoracle_health.php` | – |
| `ezoracle_recyclebin` | `extension/ezoracle/cronjobs/ezoracle_recyclebin.php` | – |
| `ezoracle_sequences` | `extension/ezoracle/cronjobs/ezoracle_sequences.php` | – |
| `ezoracle_statistics` | `extension/ezoracle/cronjobs/ezoracle_statistics.php` | – |

## INI plugin points (3)

| Setting | Class | Set in |
|---|---|---|
| `[SchemaSettings] SchemaHandlerClasses[oracle]` | `eZOracleSchema` | `extension/ezoracle/settings/dbschema.ini.append.php:4` |
| `[DatabaseSettings] ImplementationAlias[oracle]` | `eZOracleDB` | `extension/ezoracle/settings/site.ini.append.php:3` |
| `[DatabaseSettings] ImplementationAlias[ezoracle]` | `eZOracleDB` | `extension/ezoracle/settings/site.ini.append.php:4` |
