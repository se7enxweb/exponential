---
title: Patterns in lib/ezlocale
audience: developer
category: standards
order: 543
summary: Every class, view, fetch function, operator and plugin point of lib/ezlocale with its design patterns.
keywords: lib/ezlocale, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezlocale

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezlocale` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZCurrency` | class | – | `lib/ezlocale/classes/ezcurrency.php:33` | – |
| `eZDate` | class | [Factory and static factory methods](factory.md) | `lib/ezlocale/classes/ezdate.php:58` | – |
| `eZDateTime` | class | [Factory and static factory methods](factory.md) | `lib/ezlocale/classes/ezdatetime.php:62` | – |
| `eZDateUtils` | class | [Utility (static helper)](utility.md) | `lib/ezlocale/classes/ezdateutils.php:18` | – |
| `eZLocale` | class | [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `lib/ezlocale/classes/ezlocale.php:111` | – |
| `eZTime` | class | [Factory and static factory methods](factory.md) | `lib/ezlocale/classes/eztime.php:59` | – |
