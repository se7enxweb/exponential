---
title: Patterns in extension/ezstarrating
audience: developer
category: standards
order: 455
summary: Every class, view, fetch function, operator and plugin point of extension/ezstarrating with its design patterns.
keywords: extension/ezstarrating, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezstarrating

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezstarrating` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezsrRatingDataObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/ezstarrating/classes/ezsrratingdataobject.php:27` | – |
| `ezsrRatingFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezstarrating/classes/ezsrratingfilter.php:32` | – |
| `ezsrRatingObject` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/ezstarrating/classes/ezsrratingobject.php:27` | – |
| `ezsrRatingObjectTreeNode` | class extends eZContentObjectTreeNode | [Active Record (eZPersistentObject)](active-record.md), [Factory and static factory methods](factory.md) | `extension/ezstarrating/classes/ezsrratingobjecttreenode.php:36` | Only to be used by {@link ezsrRatingObject::fetchNodeByRating()} As other fetch functions won't have the needed rating values in the returned data. |
| `ezsrRatingType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/ezstarrating/datatypes/ezsrrating/ezsrratingtype.php:27` | – |
| `ezsrServerFunctions` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezstarrating/classes/ezsrserverfunctions.php:36` | – |
| `ezsrTemplateOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php:27` | – |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| fetch_starrating_data, fetch_starrating_stats, fetch_by_starrating | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/eztemplateautoload.php:14` |

## INI plugin points (2)

| Setting | Class | Set in |
|---|---|---|
| `[ezsrRatingFilter] ClassName` | `ezsrRatingFilter` | `extension/ezstarrating/settings/extendedattributefilter.ini.append.php:6` |
| `[ezjscServer_ezstarrating] Class` | `ezsrServerFunctions` | `extension/ezstarrating/settings/ezjscore.ini.append.php:11` |
