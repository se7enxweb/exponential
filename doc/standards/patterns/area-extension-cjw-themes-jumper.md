---
title: Patterns in extension/cjw_themes_jumper
audience: developer
category: standards
order: 407
summary: Every class, view, fetch function, operator and plugin point of extension/cjw_themes_jumper with its design patterns.
keywords: extension/cjw_themes_jumper, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/cjw_themes_jumper

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/cjw_themes_jumper` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `cjwJumperFormHoneypot` | class | [Utility (static helper)](utility.md) | `extension/cjw_themes_jumper/classes/cjwjumperformhoneypot.php:20` | The honeypot of the feedback forms of the design starter (spam protection, together with the form token of ezformtoken). |
| `cjwJumperImagineImageFactory` | class extends eZImageFactory | [Factory and static factory methods](factory.md), [INI plugin point (extension point)](ini-plugin-point.md) | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagefactory.php:10` | Image handler factory of the converter cjwImagine (image.ini [cjwImagine] Handler=cjwJumperImagineImageFactory): produces cjwJumperImagineImageHandle… |
| `cjwJumperImagineImageHandler` | class extends eZImageHandler | [Factory and static factory methods](factory.md), [Strategy / handler](strategy-handler.md) | `extension/cjw_themes_jumper/classes/cjwjumperimagineimagehandler.php:28` | Image converter for the cjw_* aliases of the design starter: draws them exactly the way the Nexus v2 starter draws its own aliases (Imagine 1.x with… |
| `cjwJumperPagedCollection` | class | – | `extension/cjw_themes_jumper/classes/cjwjumperpagedcollection.php:23` | One page of a paged Layouts list or grid block of the design starter (v2: Netgen Layouts' ajax block route). |
| `cjwOsmTileProxy` | class | [Adapter / bridge / wrapper](adapter.md), [Utility (static helper)](utility.md) | `extension/cjw_themes_jumper/classes/cjwosmtileproxy.php:13` | The map tiles of the osm_proxy module: validation, the local tile cache and the fetch from the tile server. |
| `cjw_themes_jumperInfo` | class | [Value object / struct](value-object.md) | `extension/cjw_themes_jumper/ezinfo.php:7` | – |

## Module views (2)

| View | Script | Policy functions | Defined in |
|---|---|---|---|
| `/cjw_paging/page` | `page.php` | – | `extension/cjw_themes_jumper/modules/cjw_paging/module.php:16` |
| `/osm_proxy/tiles` | `tiles.php` | tiles | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:16` |

## Policy functions (1)

| Function | Limitations | Defined in |
|---|---|---|
| `osm_proxy/tiles` | – | `extension/cjw_themes_jumper/modules/osm_proxy/module.php:23` |

## INI plugin points (2)

| Setting | Class | Set in |
|---|---|---|
| `[cjwImagine] Handler` | `cjwJumperImagineImageFactory` | `extension/cjw_themes_jumper/settings/image.ini.append.php:40` |
| `[BlockDefinition_ezcontent_field] Handler` | `expLayoutsTitleBlockHandler` | `extension/cjw_themes_jumper/settings/siteaccess/cjw_starter/explayouts.ini.append.php:50` |
