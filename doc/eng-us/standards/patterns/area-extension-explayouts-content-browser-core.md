---
title: Patterns in extension/explayouts_content_browser_core
audience: developer
category: standards
order: 418
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_content_browser_core with its design patterns.
keywords: extension/explayouts_content_browser_core, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_content_browser_core

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_content_browser_core` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (3)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsContentBrowserCoreBackend` | class | [Strategy / handler](strategy-handler.md) | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercorebackend.php:2` | – |
| `expLayoutsContentBrowserCoreSearchFilter` | class | [Decorator / filter](decorator.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/explayouts_content_browser_core/classes/explayoutscontentbrowsercoresearchfilter.php:10` | The extended attribute filter of the content browser's search (extendedattributefilter.ini [explayouts_content_browser_search]): each word must be in… |
| `explayouts_content_browser_coreInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_content_browser_core/ezinfo.php:12` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[explayouts_content_browser_search] ClassName` | `expLayoutsContentBrowserCoreSearchFilter` | `extension/explayouts_content_browser_core/settings/extendedattributefilter.ini.append.php:7` |
