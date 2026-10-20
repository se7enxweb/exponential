---
title: Patterns in lib/ezimage
audience: developer
category: standards
order: 533
summary: Every class, view, fetch function, operator and plugin point of lib/ezimage with its design patterns.
keywords: lib/ezimage, design patterns, classes, extension points
type: catalogue
---
# Patterns in lib/ezimage

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `lib/ezimage` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (16)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expSvgSanitizer` | class | [Utility (static helper)](utility.md) | `lib/ezimage/classes/expsvgsanitizer.php:51` | Makes an SVG image safe to store and serve from the site's own origin, by allow-list. |
| `eZEXIFImageAnalyzer` | class | – | `lib/ezimage/classes/ezexifimageanalyzer.php:19` | – |
| `eZGIFImageAnalyzer` | class | – | `lib/ezimage/classes/ezgifimageanalyzer.php:19` | – |
| `eZImageAnalyzer` | class | [Factory and static factory methods](factory.md) | `lib/ezimage/classes/ezimageanalyzer.php:23` | – |
| `eZImageFactory` | class | [Factory and static factory methods](factory.md) | `lib/ezimage/classes/ezimagefactory.php:21` | – |
| `eZImageFont` | class | – | `lib/ezimage/classes/ezimagefont.php:30` | – |
| `eZImageGDFactory` | class extends eZImageFactory | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezimage/classes/ezimagegdfactory.php:12` | – |
| `eZImageGDHandler` | class extends eZImageHandler | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `lib/ezimage/classes/ezimagegdhandler.php:24` | – |
| `eZImageHandler` | class | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `lib/ezimage/classes/ezimagehandler.php:19` | – |
| `eZImageInterface` | class | [Factory and static factory methods](factory.md), [Registry](registry.md) | `lib/ezimage/classes/ezimageinterface.php:20` | – |
| `eZImageLayer` | class extends eZImageInterface | [Factory and static factory methods](factory.md) | `lib/ezimage/classes/ezimagelayer.php:19` | – |
| `eZImageManager` | class | [Facade / service](facade.md), [Factory and static factory methods](factory.md), [Singleton / instance()](singleton.md) | `lib/ezimage/classes/ezimagemanager.php:85` | – |
| `eZImageObject` | class extends eZImageInterface | – | `lib/ezimage/classes/ezimageobject.php:19` | – |
| `eZImageShellFactory` | class extends eZImageFactory | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `lib/ezimage/classes/ezimageshellfactory.php:12` | – |
| `eZImageShellHandler` | class extends eZImageHandler | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `lib/ezimage/classes/ezimageshellhandler.php:19` | – |
| `eZImageTextLayer` | class extends eZImageLayer | [Factory and static factory methods](factory.md) | `lib/ezimage/classes/ezimagetextlayer.php:19` | – |

## Event hooks (1)

| Event | Kind | Called in |
|---|---|---|
| `image/alias` | notify | `lib/ezimage/classes/ezimagemanager.php:989` |
