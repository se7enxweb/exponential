---
title: Patterns in extension/ezodf
audience: developer
category: standards
order: 446
summary: Every class, view, fetch function, operator and plugin point of extension/ezodf with its design patterns.
keywords: extension/ezodf, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezodf

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezodf` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (8)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\Ezodf\Ezodf\Export` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezodf/classes/runnable/views/ezodf/export.php:71` | The code of extension/ezodf/modules/ezodf/export.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezodf\Ezodf\Import` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezodf/classes/runnable/views/ezodf/import.php:44` | The code of extension/ezodf/modules/ezodf/import.php, moved into a class (#207 stage 1). |
| `ezodfInfo` | class | [Value object / struct](value-object.md) | `extension/ezodf/ezinfo.php:12` | – |
| `eZOOConverter` | class | [Strategy / handler](strategy-handler.md) | `extension/ezodf/classes/ezooconverter.php:41` | – |
| `eZOOGenerator` | class | [Builder](builder.md) | `extension/ezodf/classes/ezoogenerator.php:41` | – |
| `eZOOImport` | class | – | `extension/ezodf/classes/ezooimport.php:41` | – |
| `eZOpenofficeUploadHandler` | class extends eZContentUploadHandler | [Strategy / handler](strategy-handler.md) | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php:41` | – |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `extension/ezodf/classes/ezrestodfhandler.php:46` | – |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/ezodf/import` | `import.php` | import | `extension/ezodf/modules/ezodf/module.php:34` |
| `/ezodf/export` | `export.php` | export | `extension/ezodf/modules/ezodf/module.php:42` |

## Policy functions (2)

| Function | Limitations | Defined in |
|---|---|---|
| `ezodf/import` | – | `extension/ezodf/modules/ezodf/module.php:52` |
| `ezodf/export` | – | `extension/ezodf/modules/ezodf/module.php:53` |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[RESTSettings] HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/settings/ezrest.ini.append.php:4` |
