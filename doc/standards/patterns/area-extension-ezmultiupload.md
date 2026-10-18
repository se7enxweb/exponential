---
title: Patterns in extension/ezmultiupload
audience: developer
category: standards
order: 445
summary: Every class, view, fetch function, operator and plugin point of extension/ezmultiupload with its design patterns.
keywords: extension/ezmultiupload, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezmultiupload

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezmultiupload` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expMultiuploadFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `extension/ezmultiupload/classes/expmultiuploadfunctioncollection.php:10` | The template fetch functions of ezmultiupload (modules/ezmultiupload/function_definition.php). |
| `expMultiuploadService` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `extension/ezmultiupload/classes/expmultiuploadservice.php:20` | The server side of the upload page: checks before an upload (the target location, the content class each file becomes, the create permission for it t… |
| `expMultiuploadZipImporter` | class | – | `extension/ezmultiupload/classes/expmultiuploadzipimporter.php:29` | The ZIP import backend: inspect() lists what an archive holds, extract() writes the chosen files to a private directory, cleanup() removes that direc… |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Service` | class extends ModuleView | [Facade / service](facade.md), [Module view (Page Controller)](module-view.md) | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/service.php:23` | ezmultiupload/service: the checks and actions of the upload page, answered as JSON. |
| `Exponential\View\Extension\Ezmultiupload\Ezmultiupload\Upload` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezmultiupload/classes/runnable/views/ezmultiupload/upload.php:23` | The code of extension/ezmultiupload/modules/ezmultiupload/upload.php, moved into a class (#207 stage 1). |
| `eZMultiuploadHandler` | class | [Strategy / handler](strategy-handler.md) | `extension/ezmultiupload/classes/ezmultiuploadhandler.php:16` | eZMultiuploadHandler class |
| `eZMultiuploadHandlerInterface` | interface | [Interface (contract)](interface.md) | `extension/ezmultiupload/classes/ezmultiuploadhandlerinterface.php:12` | – |
| `ezmultiuploadInfo` | class | [Value object / struct](value-object.md) | `extension/ezmultiupload/ezinfo.php:7` | – |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezmultiupload/upload` | `upload.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:15` |
| `/ezmultiupload/service` | `service.php` | – | `extension/ezmultiupload/modules/ezmultiupload/module.php:20` |

## Fetch functions (3)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'ezmultiupload', 'can_upload' )` | `expMultiuploadFunctionCollection::fetchCanUpload()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:26` |
| `fetch( 'ezmultiupload', 'dashboard_target' )` | `expMultiuploadFunctionCollection::fetchDashboardTarget()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:36` |
| `fetch( 'ezmultiupload', 'quick_locations' )` | `expMultiuploadFunctionCollection::fetchQuickLocations()` | `extension/ezmultiupload/modules/ezmultiupload/function_definition.php:44` |
