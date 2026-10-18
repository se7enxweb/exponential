---
title: Patterns in extension/ezprestapi
audience: developer
category: standards
order: 452
summary: Every class, view, fetch function, operator and plugin point of extension/ezprestapi with its design patterns.
keywords: extension/ezprestapi, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezprestapi

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezprestapi` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (9)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Command\Extension\Ezprestapi\Ezrestcall` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/ezprestapi/classes/runnable/commands/php_ezrestcall.php:24` | The code of extension/ezprestapi/bin/php/ezrestcall.php, moved into a class (#207 stage 1). |
| `ezp7xRestApiProvider` | class implements ezpRestProviderInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezprestapi/classes/rest_provider.php:25` | The routes of the ezp provider. |
| `ezp7xRestContentController` | class extends ezpRestMvcController | – | `extension/ezprestapi/classes/controllers/content.php:13` | This controller is used for serving content |
| `ezprestapiInfo` | class | [Value object / struct](value-object.md) | `extension/ezprestapi/ezinfo.php:10` | – |
| `ezpRestApiViewController` | class implements ezpRestViewControllerInterface | – | `extension/ezprestapi/classes/view_controller.php:12` | – |
| `ezpRestAtomController` | class extends ezcMvcController | – | `extension/ezprestapi/classes/controllers/atom.php:18` | Controller class for producing atom feeds of content structure. |
| `ezpRestAtomDecorator` | class extends ezpRestFeedDecorator | [Decorator / filter](decorator.md) | `extension/ezprestapi/classes/views/atom_decorator.php:10` | – |
| `ezpRestAtomView` | class extends ezcMvcView | – | `extension/ezprestapi/classes/views/atom.php:13` | View providing atomfeed of the output |
| `ezpRestContentModel` | class extends ezpRestModel | [Utility (static helper)](utility.md) | `extension/ezprestapi/classes/models/rest_content_model.php:13` | Model class containing backend method for REST content controller |

## INI plugin points (2)

| Setting | Class | Set in |
|---|---|---|
| `[ApiProvider] ProviderClass[ezp]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:8` |
| `[ApiProvider] ProviderClass[ezpl]` | `ezp7xRestApiProvider` | `extension/ezprestapi/settings/rest.ini.append.php:9` |
