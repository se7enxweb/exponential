---
title: Patterns in extension/ezflow
audience: developer
category: standards
order: 443
summary: Every class, view, fetch function, operator and plugin point of extension/ezflow with its design patterns.
keywords: extension/ezflow, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezflow

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezflow` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (28)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `eZFeedReader` | class | [Factory and static factory methods](factory.md), [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezflow/autoloads/ezfeedreader.php:32` | eZFeedReader class impelement feedreader tpl operator methods |
| `eZFlowAjaxContent` | class | [Singleton / instance()](singleton.md) | `extension/ezflow/classes/ezflowajaxcontent.php:35` | – |
| `eZFlowBlock` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezflow/classes/ezflowblock.php:28` | – |
| `eZFlowFetchInterface` | interface | [Interface (contract)](interface.md) | `extension/ezflow/classes/ezflowfetchinterface.php:28` | – |
| `eZFlowFetchParameters` | class | – | `extension/ezflow/classes/ezflowfetchparameters.php:28` | – |
| `eZFlowFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/ezflow/modules/ezflow/functioncollection.php:30` | – |
| `ezflowInfo` | class | [Value object / struct](value-object.md) | `extension/ezflow/ezinfo.php:12` | – |
| `eZFlowKeywordsFetch` | class implements eZFlowFetchInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezflow/classes/fetches/ezflowkeywordsfetch.php:28` | – |
| `eZFlowLatestContent` | class implements eZFlowFetchInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezflow/classes/fetches/ezflowlatestcontent.php:28` | – |
| `eZFlowLatestObjects` | class implements eZFlowFetchInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezflow/classes/fetches/ezflowlatestobjects.php:28` | – |
| `eZFlowMCFetch` | class implements eZFlowFetchInterface | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/ezflow/classes/fetches/ezflowmcfetch.php:3` | – |
| `eZFlowOperations` | class | [Utility (static helper)](utility.md) | `extension/ezflow/classes/ezflowoperations.php:28` | – |
| `eZFlowPool` | class | [Utility (static helper)](utility.md) | `extension/ezflow/classes/ezflowpool.php:28` | – |
| `eZFlowPoolItem` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/ezflow/classes/ezflowpoolitem.php:28` | – |
| `eZFlowServerCallFunctions` | class | [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezflow/classes/ezflowservercallfunctions.php:7` | Implements methods called remotely by sending XHR calls |
| `eZHTTPCacheManager` | class | [Facade / service](facade.md) | `extension/ezflow/classes/ezhttpcachemanager.php:33` | eZHTTPCacheManager class implements methods required for loading user definied http cache handlers |
| `eZHTTPCacheManagerInterface` | interface | [Interface (contract)](interface.md) | `extension/ezflow/classes/ezhttpcachemanagerinterface.php:32` | Interface declaration |
| `eZJSON` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezflow/autoloads/ezjson.php:32` | eZJSON class impelement json tpl operator methods |
| `eZPage` | class | [Factory and static factory methods](factory.md) | `extension/ezflow/classes/ezpage.php:28` | – |
| `eZPageBlock` | class | [Factory and static factory methods](factory.md) | `extension/ezflow/classes/ezpageblock.php:28` | – |
| `eZPageBlockItem` | class | [Factory and static factory methods](factory.md) | `extension/ezflow/classes/ezpageblockitem.php:28` | – |
| `eZPageLink` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezflow/autoloads/ezpagelink.php:32` | eZPageLink class implements pagelink tpl operator methods |
| `eZPageSwapType` | class extends eZWorkflowEventType | [Workflow event type (Command, plugin)](workflow-event.md) | `extension/ezflow/eventtypes/event/ezpageswap/ezpageswaptype.php:28` | – |
| `eZPageType` | class extends eZDataType | [Datatype (Strategy for content fields)](datatype.md) | `extension/ezflow/datatypes/ezpage/ezpagetype.php:28` | – |
| `eZPageZone` | class | [Factory and static factory methods](factory.md) | `extension/ezflow/classes/ezpagezone.php:28` | – |
| `eZRed5StreamListOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezflow/autoloads/ezred5streamlist.php:32` | eZRed5StreamListOperator class impelement red5list tpl operator methods |
| `eZSquidCacheManager` | class implements eZHTTPCacheManagerInterface | [Facade / service](facade.md), [INI plugin point (extension point)](ini-plugin-point.md), [Utility (static helper)](utility.md) | `extension/ezflow/classes/ezsquidcachemanager.php:30` | – |
| `eZUnserialize` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/ezflow/autoloads/ezunserialize.php:32` | eZUnserialize class impelement unserialize tpl operator methods |

## Module views (8)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezflow/get` | `get.php` | edit | `extension/ezflow/modules/ezflow/module.php:32` |
| `/ezflow/timeline` | `timeline.php` | timeline | `extension/ezflow/modules/ezflow/module.php:35` |
| `/ezflow/preview` | `preview.php` | timeline | `extension/ezflow/modules/ezflow/module.php:39` |
| `/ezflow/zone` | `zone.php` | edit | `extension/ezflow/modules/ezflow/module.php:43` |
| `/ezflow/request` | `request.php` | edit | `extension/ezflow/modules/ezflow/module.php:47` |
| `/ezflow/push` | `push.php` | edit | `extension/ezflow/modules/ezflow/module.php:52` |
| `/ezflow/block` | `block.php` | call | `extension/ezflow/modules/ezflow/module.php:58` |
| `/flash/embed` | `embed.php` | – | `extension/ezflow/modules/flash/module.php:31` |

## Policy functions (4)

| Function | Limitations | Defined in |
|---|---|---|
| `ezflow/timeline` | – | `extension/ezflow/modules/ezflow/module.php:63` |
| `ezflow/edit` | – | `extension/ezflow/modules/ezflow/module.php:64` |
| `ezflow/call` | – | `extension/ezflow/modules/ezflow/module.php:65` |
| `ezflow/changelayout` | – | `extension/ezflow/modules/ezflow/module.php:66` |

## Fetch functions (6)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'ezflow', 'waiting' )` | `eZFlowFunctionCollection::fetchWaiting()` | `extension/ezflow/modules/ezflow/function_definition.php:30` |
| `fetch( 'ezflow', 'valid' )` | `eZFlowFunctionCollection::fetchValid()` | `extension/ezflow/modules/ezflow/function_definition.php:42` |
| `fetch( 'ezflow', 'archived' )` | `eZFlowFunctionCollection::fetchArchived()` | `extension/ezflow/modules/ezflow/function_definition.php:54` |
| `fetch( 'ezflow', 'valid_nodes' )` | `eZFlowFunctionCollection::fetchValidNodes()` | `extension/ezflow/modules/ezflow/function_definition.php:66` |
| `fetch( 'ezflow', 'block' )` | `eZFlowFunctionCollection::fetchBlock()` | `extension/ezflow/modules/ezflow/function_definition.php:78` |
| `fetch( 'ezflow', 'allowed_zones' )` | `eZFlowFunctionCollection::fetchAllowedZones()` | `extension/ezflow/modules/ezflow/function_definition.php:90` |

## Template operators (5)

| Operators | Class | Registered in |
|---|---|---|
| unserialize | `eZUnserialize` | `extension/ezflow/autoloads/eztemplateautoload.php:29` |
| json | `eZJSON` | `extension/ezflow/autoloads/eztemplateautoload.php:32` |
| feedreader | `eZFeedReader` | `extension/ezflow/autoloads/eztemplateautoload.php:35` |
| red5list | `eZRed5StreamListOperator` | `extension/ezflow/autoloads/eztemplateautoload.php:38` |
| pagelink | `eZPageLink` | `extension/ezflow/autoloads/eztemplateautoload.php:41` |

## Cronjob parts (2)

| Cronjob | File | Description |
|---|---|---|
| `ezflowcleanup` | `extension/ezflow/cronjobs/ezflowcleanup.php` | – |
| `ezflowupdate` | `extension/ezflow/cronjobs/ezflowupdate.php` | – |

## INI plugin points (6)

| Setting | Class | Set in |
|---|---|---|
| `[Dynamic3Items] FetchClass` | `eZFlowLatestObjects` | `extension/ezflow/settings/block.ini:151` |
| `[Keyword] FetchClass` | `eZFlowKeywordsFetch` | `extension/ezflow/settings/block.ini:246` |
| `[MultimediaCarousel] FetchClass` | `eZFlowMCFetch` | `extension/ezflow/settings/block.ini:275` |
| `[LatestContent] FetchClass` | `eZFlowLatestContent` | `extension/ezflow/settings/block.ini:302` |
| `[ezjscServer_ezflow] Class` | `eZFlowServerCallFunctions` | `extension/ezflow/settings/ezjscore.ini.append.php:4` |
| `[HTTPCacheHandlers] Handlers[]` | `eZSquidCacheManager` | `extension/ezflow/settings/http.ini:5` |
