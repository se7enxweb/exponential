---
title: Patterns in extension/explayouts_ui_api
audience: developer
category: standards
order: 423
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_ui_api with its design patterns.
keywords: extension/explayouts_ui_api, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_ui_api

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_ui_api` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsUIApplicationApi` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php:2` | – |
| `explayouts_ui_apiInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_ui_api/ezinfo.php:12` | – |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\App` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/app.php:10` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/app.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Blocks` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/blocks.php:38` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/blocks.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Layouts` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/layouts.php:31` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/layouts.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\ExplayoutsUiApi\ExplayoutsUiApi\Rules` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_ui_api/classes/runnable/views/explayouts_ui_api/rules.php:50` | The code of extension/explayouts_ui_api/modules/explayouts_ui_api/rules.php, moved into a class (#207 stage 1). |

## Module views (4)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts_ui_api/layouts` | `layouts.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:7` |
| `/explayouts_ui_api/rules` | `rules.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:14` |
| `/explayouts_ui_api/blocks` | `blocks.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:21` |
| `/explayouts_ui_api/app` | `app.php` | read | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:28` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts_ui_api/read` | – | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:36` |
| `explayouts_ui_api/edit` | – | `extension/explayouts_ui_api/modules/explayouts_ui_api/module.php:39` |
