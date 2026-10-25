---
title: Patterns in extension/sevenx_themes_media
audience: developer
category: standards
order: 474
summary: Every class, view, fetch function, operator and plugin point of extension/sevenx_themes_media with its design patterns.
keywords: extension/sevenx_themes_media, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/sevenx_themes_media

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/sevenx_themes_media` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (7)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Submit` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/sevenx_themes_media/classes/runnable/views/info-collection/submit.php:10` | The code of extension/sevenx_themes_media/modules/info-collection/submit.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\SevenxThemesMedia\InfoCollection\Viewmodal` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/sevenx_themes_media/classes/runnable/views/info-collection/viewmodal.php:10` | The code of extension/sevenx_themes_media/modules/info-collection/viewmodal.php, moved into a class (#207 stage 1). |
| `sevenxThemesMediaField` | class implements ArrayAccess | [Iterator](iterator.md) | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:3` | – |
| `sevenxThemesMediaOperators` | class | [Template operator (Interpreter plugin)](template-operator.md) | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:58` | – |
| `sevenxThemesMediaXHTMLXMLOutput` | class extends eZXHTMLXMLOutput | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/sevenx_themes_media/classes/sevenxthemesmediaxhtmlxmloutput.php:11` | Extends eZXHTMLXMLOutput to register the legacy XML tag names that the imported content uses (title, itemizedlist, listitem, orderedlist, literallayo… |
| `sevenxThemesMediaYouTube` | class | [Utility (static helper)](utility.md) | `extension/sevenx_themes_media/classes/sevenxthemesmediayoutube.php:22` | YouTube without contacting Google before the visitor chooses to play. |
| `sevenx_themes_mediaInfo` | class | [Value object / struct](value-object.md) | `extension/sevenx_themes_media/ezinfo.php:13` | – |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/info-collection/view-modal` | `viewmodal.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:6` |
| `/info-collection/submit` | `submit.php` | read | `extension/sevenx_themes_media/modules/info-collection/module.php:13` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `info-collection/read` | – | `extension/sevenx_themes_media/modules/info-collection/module.php:21` |

## INI plugin points (3)

| Setting | Class | Set in |
|---|---|---|
| `[BlockDefinition_product_highlight] Handler` | `expLayoutsListBlockHandler` | `extension/sevenx_themes_media/settings/explayouts.ini.append.php:16` |
| `[OutputSettings] HandlerClass` | `sevenxThemesMediaXHTMLXMLOutput` | `extension/sevenx_themes_media/settings/ezxml.ini.append.php:4` |
| `[ezjscServer_expajaxloadmore] Class` | `expLayoutsAjaxLoadMoreServer` | `extension/sevenx_themes_media/settings/siteaccess/site/ezjscore.ini.append.php:15` |
