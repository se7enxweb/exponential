---
title: Patterns in extension/expimageeditor
audience: developer
category: standards
order: 413
summary: Every class, view, fetch function, operator and plugin point of extension/expimageeditor with its design patterns.
keywords: extension/expimageeditor, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/expimageeditor

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/expimageeditor` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (5)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expImageEditorException` | class extends RuntimeException | [Exception hierarchy](exception.md) | `extension/expimageeditor/classes/expimageeditorexception.php:15` | A request the image editor refuses: the message (English, translated when shown) and the HTTP status to answer with. |
| `expImageEditorFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md) | `extension/expimageeditor/classes/expimageeditorfunctioncollection.php:15` | The fetch functions of the expimageeditor module. |
| `expimageeditorInfo` | class | [Value object / struct](value-object.md) | `extension/expimageeditor/ezinfo.php:9` | – |
| `expImageEditorStore` | class | [Utility (static helper)](utility.md) | `extension/expimageeditor/classes/expimageeditorstore.php:31` | What the save view of the image editor does on the server: the editor works in the browser and posts the finished image; this class checks who posts… |
| `Exponential\View\Extension\Expimageeditor\Expimageeditor\Save` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/expimageeditor/classes/runnable/views/expimageeditor/save.php:20` | expimageeditor/save//: takes the edited image (POST, field "image", with the form token in the X-CSRF-Token header or the ezxform_token field), store… |

## Module views (1)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/expimageeditor/save` | `save.php` | edit | `extension/expimageeditor/modules/expimageeditor/module.php:15` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `expimageeditor/edit` | – | `extension/expimageeditor/modules/expimageeditor/module.php:20` |

## Fetch functions (1)

| Fetch | Implemented by | Defined in |
|---|---|---|
| `fetch( 'expimageeditor', 'aspect_ratios' )` | `expImageEditorFunctionCollection::fetchAspectRatios()` | `extension/expimageeditor/modules/expimageeditor/function_definition.php:13` |
