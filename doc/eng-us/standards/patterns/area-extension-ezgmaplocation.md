---
title: Patterns in extension/ezgmaplocation
audience: developer
category: standards
order: 445
summary: Every class, view, fetch function, operator and plugin point of extension/ezgmaplocation with its design patterns.
keywords: extension/ezgmaplocation, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezgmaplocation

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezgmaplocation` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZGmapLocation` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/ezgmaplocation/classes/ezgmaplocation.php:40` | Helper class used along with the eZGmapLocationType datatype. |
| `ezgmaplocationInfo` | class | [Value object / struct](value-object.md) | `extension/ezgmaplocation/ezinfo.php:7` | – |
| `eZGmapLocationType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/ezgmaplocation/datatypes/ezgmaplocation/ezgmaplocationtype.php:39` | Class providing the eZGmapLocationType datatype. |
| `ezgmlLocationFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezgmaplocation/classes/ezgmllocationfilter.php:32` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezgmlLocationFilter] ClassName` | `ezgmlLocationFilter` | `extension/ezgmaplocation/settings/extendedattributefilter.ini.append.php:5` |
