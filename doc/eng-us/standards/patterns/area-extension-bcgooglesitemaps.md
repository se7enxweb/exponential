---
title: Patterns in extension/bcgooglesitemaps
audience: developer
category: standards
order: 403
summary: Every class, view, fetch function, operator and plugin point of extension/bcgooglesitemaps with its design patterns.
keywords: extension/bcgooglesitemaps, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/bcgooglesitemaps

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/bcgooglesitemaps` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `bcGoogleSitemapsInfo` | class | [Value object / struct](value-object.md) | `extension/bcgooglesitemaps/ezinfo.php:11` | – |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generate` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php:49` | The code of extension/bcgooglesitemaps/cronjobs/generate.php, moved into a class (#207 stage 1). |
| `Exponential\Cronjob\Extension\Bcgooglesitemaps\Generatemultilingual` | class extends CronjobPart | [Command (console, scripts, cronjobs)](command.md) | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php:88` | Alert the user to how long the script execution took place * |
| `Exponential\View\Extension\Bcgooglesitemaps\Googlesitemapdynamic\Sitemap` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/bcgooglesitemaps/classes/runnable/views/googlesitemapdynamic/sitemap.php:23` | The code of extension/bcgooglesitemaps/modules/googlesitemapdynamic/sitemap.php, moved into a class (#207 stage 1). |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/googlesitemapdynamic/sitemap` | `sitemap.php` | sitemap | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:15` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `googlesitemapdynamic/sitemap` | – | `extension/bcgooglesitemaps/modules/googlesitemapdynamic/module.php:19` |

## Cronjob parts (4)

| Cronjob | File | Description |
|---|---|---|
| `generate` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generate.php` | The code of extension/bcgooglesitemaps/cronjobs/generate.php, moved into a class (#207 stage 1). |
| `generatemultilingual` | `extension/bcgooglesitemaps/classes/runnable/cronjobs/generatemultilingual.php` | The code of extension/bcgooglesitemaps/cronjobs/generatemultilingual.php, moved into a class (#207 stage 1). |
| `generate` | `extension/bcgooglesitemaps/cronjobs/generate.php` | – |
| `generatemultilingual` | `extension/bcgooglesitemaps/cronjobs/generatemultilingual.php` | – |
