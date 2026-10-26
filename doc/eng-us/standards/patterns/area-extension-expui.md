---
title: Patterns in extension/expui
audience: developer
category: standards
order: 435
summary: Every class, view, fetch function, operator and plugin point of extension/expui with its design patterns.
keywords: extension/expui, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expui

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expui` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Expui\Expui\Test` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/expui/classes/runnable/views/expui/test.php:23` | The code of extension/expui/modules/expui/test.php, moved into a class (#207 stage 1). |
| `expuiInfo` | class | [Value object / struct](value-object.md) | `extension/expui/ezinfo.php:7` | – |
| `expUIServerFunctions` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/expui/classes/expuiserverfunctions.php:30` | – |
| `expUITemplateOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/expui/classes/expuitemplateoperators.php:17` | – |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| exp_config | `expUITemplateOperators` | `extension/expui/autoloads/eztemplateautoload.php:8` |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_exp] Class` | `expUIServerFunctions` | `extension/expui/settings/ezjscore.ini.append.php:5` |
