---
title: Patterns in extension/ezautosave
audience: developer
category: standards
order: 439
summary: Every class, view, fetch function, operator and plugin point of extension/ezautosave with its design patterns.
keywords: extension/ezautosave, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezautosave

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezautosave` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezautosaveInfo` | class | [Value object / struct](value-object.md) | `extension/ezautosave/ezinfo.php:7` | – |
| `ezjscServerFunctionsAutosave` | class extends ezjscServerFunctions | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:16` | This class handles AJAX calls for the autosave feature |

## Event hooks (1)

| Event | Kind | Called in |
|---|---|---|
| `content/cache/version` | notify | `extension/ezautosave/classes/ezjscserverfunctionsautosave.php:225` |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_ezautosave] Class` | `ezjscServerFunctionsAutosave` | `extension/ezautosave/settings/ezjscore.ini.append.php:4` |
