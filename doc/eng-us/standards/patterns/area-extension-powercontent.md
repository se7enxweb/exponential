---
title: Patterns in extension/powercontent
audience: developer
category: standards
order: 471
summary: Every class, view, fetch function, operator and plugin point of extension/powercontent with its design patterns.
keywords: extension/powercontent, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/powercontent

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/powercontent` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Powercontent\Powercontent\Action` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/powercontent/classes/runnable/views/powercontent/action.php:10` | The code of extension/powercontent/modules/powercontent/action.php, moved into a class (#207 stage 1). |
| `PowerContent` | class | – | `extension/powercontent/classes/powercontent.php:3` | – |
| `powercontentInfo` | class | [Value object / struct](value-object.md) | `extension/powercontent/ezinfo.php:2` | – |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/powercontent/action` | `action.php` | – | `extension/powercontent/modules/powercontent/module.php:6` |
