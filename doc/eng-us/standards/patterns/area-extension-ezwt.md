---
title: Patterns in extension/ezwt
audience: developer
category: standards
order: 460
summary: Every class, view, fetch function, operator and plugin point of extension/ezwt with its design patterns.
keywords: extension/ezwt, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezwt

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezwt` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Sort` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezwt/classes/runnable/views/websitetoolbar/sort.php:34` | The code of extension/ezwt/modules/websitetoolbar/sort.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezwt\Websitetoolbar\Websitetoolbar` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezwt/classes/runnable/views/websitetoolbar/websitetoolbar.php:33` | The code of extension/ezwt/modules/websitetoolbar/websitetoolbar.php, moved into a class (#207 stage 1). |
| `eZCreateClassListGroups` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezwt/autoloads/ezcreateclasslistgroups.php:28` | – |
| `ezwtServerCallFunctions` | class | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezwt/classes/ezwtservercallfunctions.php:15` | Implements methods called remotely by sending XHR calls |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/websitetoolbar/sort` | `sort.php` | use | `extension/ezwt/modules/websitetoolbar/module.php:35` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `websitetoolbar/use` | – | `extension/ezwt/modules/websitetoolbar/module.php:44` |

## Template operators (1)

| Operators | Class | Registered in |
|---|---|---|
| ezcreateclasslistgroups | `eZCreateClassListGroups` | `extension/ezwt/autoloads/eztemplateautoload.php:29` |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[ezjscServer_ezwt] Class` | `ezwtServerCallFunctions` | `extension/ezwt/settings/ezjscore.ini.append.php:4` |
