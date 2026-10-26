---
title: Patterns in lib/ezdb
audience: developer
category: standards
order: 541
summary: Every class, view, fetch function, operator and plugin point of lib/ezdb with its design patterns.
keywords: lib/ezdb, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezdb

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezdb` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (11)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expMongoDB` | class extends eZDBInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdb/classes/expmongodb.php:18` | – |
| `expPDODB` | class extends eZDBInterface | – | `lib/ezdb/classes/exppdodb.php:35` | A generic database driver over PDO, for engines Exponential has no native driver for: Microsoft SQL Server (pdo_sqlsrv, pdo_dblib, or pdo_odbc with a… |
| `eZDB` | class | [Singleton / instance()](singleton.md) | `lib/ezdb/classes/ezdb.php:80` | – |
| `eZDBInterface` | class | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdb/classes/ezdbinterface.php:21` | The eZDBInterface defines the interface for all database implementations |
| `eZDBQueryCache` | class | [Registry](registry.md), [Utility (static helper)](utility.md) | `lib/ezdb/classes/ezdbquerycache.php:37` | A result cache for the SQL drivers (MySQL/MariaDB, PostgreSQL, SQLite): the rows a SELECT returned, answered again until a write to one of the tables… |
| `eZDBTool` | class | [Utility (static helper)](utility.md) | `lib/ezdb/classes/ezdbtool.php:18` | – |
| `eZMySQLCharset` | abstract class | [Template Method / abstract base class](template-method.md) | `lib/ezdb/classes/ezmysqlcharset.php:13` | – |
| `eZMySQLiDB` | class extends eZDBInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdb/classes/ezmysqlidb.php:22` | – |
| `eZNullDB` | class extends eZDBInterface | – | `lib/ezdb/classes/eznulldb.php:20` | – |
| `eZPostgreSQLDB` | class extends eZDBInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdb/classes/ezpostgresqldb.php:23` | – |
| `eZSQLite3DB` | class extends eZDBInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdb/classes/ezsqlite3db.php:3` | – |
