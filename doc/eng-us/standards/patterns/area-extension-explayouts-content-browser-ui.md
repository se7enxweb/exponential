---
title: Patterns in extension/explayouts_content_browser_ui
audience: developer
category: standards
order: 419
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_content_browser_ui with its design patterns.
keywords: extension/explayouts_content_browser_ui, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_content_browser_ui

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_content_browser_ui` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (2)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `explayouts_content_browser_uiInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_content_browser_ui/ezinfo.php:12` | – |
| `Exponential\View\Extension\ExplayoutsContentBrowserUi\ExplayoutsContentBrowserUi\Browser` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/explayouts_content_browser_ui/classes/runnable/views/explayouts_content_browser_ui/browser.php:10` | The code of extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/browser.php, moved into a class (#207 stage 1). |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/explayouts_content_browser_ui/browser` | `browser.php` | read | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:7` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `explayouts_content_browser_ui/read` | – | `extension/explayouts_content_browser_ui/modules/explayouts_content_browser_ui/module.php:15` |
