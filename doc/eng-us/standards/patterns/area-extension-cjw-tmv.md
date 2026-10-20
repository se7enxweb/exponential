---
title: Patterns in extension/cjw_tmv
audience: developer
category: standards
order: 408
summary: Every class, view, fetch function, operator and plugin point of extension/cjw_tmv with its design patterns.
keywords: extension/cjw_tmv, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/cjw_tmv

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/cjw_tmv` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `cjwTmvClient` | class | – | `extension/cjw_tmv/classes/cjwtmvclient.php:13` | The client of the TMV event database (Tourismusverband Mecklenburg-Vorpommern, infomax imxplatform JSON API). |
| `cjwTmvEventsBlockHandler` | class implements expLayoutsBlockHandlerInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/cjw_tmv/classes/cjwtmveventsblockhandler.php:11` | The Layouts block "tmv_events" (v2: Cjw\TmvBundle TmvEventsHandler): the events of a tmv_container, with the filter toolbar (view type list), only th… |
| `cjwTmvFeed` | class | [Factory and static factory methods](factory.md) | `extension/cjw_tmv/classes/cjwtmvfeed.php:14` | The TMV events of one event container (a tmv_container node), read from the content objects that the importer (cjwTmvImporter, cronjob part cjw_tmv)… |
| `cjwTmvFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `extension/cjw_tmv/classes/cjwtmvfunctioncollection.php:10` | The fetch functions of the cjw_tmv module (modules/cjw_tmv/function_definition.php). |
| `cjwTmvHtml` | class | [Utility (static helper)](utility.md) | `extension/cjw_tmv/classes/cjwtmvhtml.php:12` | Cleans the HTML of a TMV event text before it is cached and drawn: only paragraphs, line breaks, emphasis, lists, small headings and links (http, htt… |
| `cjwTmvImporter` | class | [Factory and static factory methods](factory.md) | `extension/cjw_tmv/classes/cjwtmvimporter.php:28` | Imports the events of the TMV event database as content objects below their tmv_container, as the legacy extension cjw_tmv_veranstdb did (TmvHelper::… |
| `cjw_tmvInfo` | class | [Value object / struct](value-object.md) | `extension/cjw_tmv/ezinfo.php:7` | – |

## Fetch functions (4)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'cjw_tmv', 'events' )` | `cjwTmvFunctionCollection::events()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:12` |
| `fetch( 'cjw_tmv', 'categories' )` | `cjwTmvFunctionCollection::categories()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:24` |
| `fetch( 'cjw_tmv', 'places' )` | `cjwTmvFunctionCollection::places()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:32` |
| `fetch( 'cjw_tmv', 'filter_params' )` | `cjwTmvFunctionCollection::filterParams()` | `extension/cjw_tmv/modules/cjw_tmv/function_definition.php:40` |

## Cronjob parts (1)

| Cronjob | File | Description |
|---|---|---|
| `cjw_tmv_refresh` | `extension/cjw_tmv/cronjobs/cjw_tmv_refresh.php` | Cronjob part cjw_tmv: imports and updates the TMV events of every tmv_container as content objects (cjwTmvImporter), and removes the past ones. |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[BlockDefinition_tmv_events] Handler` | `cjwTmvEventsBlockHandler` | `extension/cjw_tmv/settings/siteaccess/cjw_starter/explayouts.ini.append.php:10` |
