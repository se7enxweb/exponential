---
title: Patterns in extension/enhancedselection2
audience: developer
category: standards
order: 410
summary: Every class, view, fetch function, operator and plugin point of extension/enhancedselection2 with its design patterns.
keywords: extension/enhancedselection2, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/enhancedselection2

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/enhancedselection2` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `enhancedselection2Info` | class | [Value object / struct](value-object.md) | `extension/enhancedselection2/ezinfo.php:14` | Enhanced Selection extension |
| `Exponential\Command\Extension\Enhancedselection2\MigrateToDatabase` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/enhancedselection2/classes/runnable/commands/php_migrate_to_database.php:11` | The code of extension/enhancedselection2/bin/php/migrate_to_database.php, moved into a class (#207 stage 1). |
| `Exponential\Command\Extension\Enhancedselection2\Updateenhancedselection` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/enhancedselection2/classes/runnable/commands/php_updateenhancedselection.php:10` | The code of extension/enhancedselection2/bin/php/updateenhancedselection.php, moved into a class (#207 stage 1). |
| `ezfSolrDocumentFieldSckEnhancedSelection` | class extends ezfSolrDocumentFieldBase | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/enhancedselection2/classes/ezfsolrdocumentfieldsckenhancedselection.php:2` | – |
| `SckEnhancedSelection` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Utility (static helper)](utility.md) | `extension/enhancedselection2/classes/sckenhancedselection.php:3` | – |
| `SckEnhancedSelectionType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/enhancedselection2/datatypes/sckenhancedselection/sckenhancedselectiontype.php:3` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[SolrFieldMapSettings] CustomMap[sckenhancedselection]` | `ezfSolrDocumentFieldSckEnhancedSelection` | `extension/enhancedselection2/settings/ezfind.ini.append.php:4` |
