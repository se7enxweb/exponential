---
title: Patterns in lib/ezdbschema
audience: developer
category: standards
order: 538
summary: Every class, view, fetch function, operator and plugin point of lib/ezdbschema with its design patterns.
keywords: lib/ezdbschema, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezdbschema

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezdbschema` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expMongoSchema` | class extends eZDBSchemaInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdbschema/classes/expmongoschema.php:11` | MongoDB schema handler for eZDbSchema. |
| `eZDbSchema` | class | [Singleton / instance()](singleton.md), [Utility (static helper)](utility.md) | `lib/ezdbschema/classes/ezdbschema.php:19` | – |
| `eZDbSchemaChecker` | class | [Utility (static helper)](utility.md) | `lib/ezdbschema/classes/ezdbschemachecker.php:19` | – |
| `eZDBSchemaInterface` | class | – | `lib/ezdbschema/classes/ezdbschemainterface.php:62` | – |
| `eZLintSchema` | class extends eZDBSchemaInterface | – | `lib/ezdbschema/classes/ezlintschema.php:41` | – |
| `eZMysqlSchema` | class extends eZDBSchemaInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdbschema/classes/ezmysqlschema.php:19` | – |
| `eZPgsqlSchema` | class extends eZDBSchemaInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdbschema/classes/ezpgsqlschema.php:19` | – |
| `eZSQLiteSchema` | class extends eZDBSchemaInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezdbschema/classes/ezsqliteschema.php:3` | – |
