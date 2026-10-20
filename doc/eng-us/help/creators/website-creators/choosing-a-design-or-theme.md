---
title: Choosing a design or theme
audience: website-creator, admin
category: website-creation
order: 90
summary: Three ways to get a design for your site - use a shipped theme, adapt one in your own extension, or build your own - with what each costs in work and upgrades, and how to switch a siteaccess to a design.
keywords: design, theme, look and feel, sevenx_themes_simple, ezwebin, ezdemo, media theme, own design, design extension, SiteDesign, website creator
type: help
---
# Choosing a design or theme

This page is for website creators who decide how their site will look. In Exponential a **design** is a directory of
templates, stylesheets, images and scripts; a **theme** is a design packaged as an extension. You can use a theme as
it is, adapt it, or build your own. This page compares the three and shows how a siteaccess is pointed at a design.

## Contents

1. [Three ways](#three-ways)
2. [The themes that ship](#the-themes-that-ship)
3. [Adapting a theme the safe way](#adapting-a-theme-the-safe-way)
4. [Your own design](#your-own-design)
5. [Pointing a siteaccess at a design](#pointing-a-siteaccess-at-a-design)
6. [Decision checklist](#decision-checklist)

## Three ways

| Way | Work | Upgrades | Choose when |
|---|---|---|---|
| Use a shipped theme as it is | least: settings only | you get the theme's updates with Composer | the theme's look and classes fit |
| Adapt a theme in your own extension | some: overrides of the templates you change | the theme updates underneath; your overrides stay | you want the theme's structure with your brand |
| Build your own design | most: every template you need | only your own code | the look is specific, or you build on the layouts editor |

## The themes that ship

| Theme (extension) | Design name | What it is | Read |
|---|---|---|---|
| `sevenx_themes_simple` | `simple` | the light, responsive public design of the default installation; header with multi-level menu and cart, article, blog, gallery, product and basket templates | [sevenx_themes_simple](../../../features/6.0/extensions/sevenx_themes_simple.md) |
| `ezwebin` | `ezwebin` | the classic Website Interface: articles, blogs, events, forums, galleries, products, RSS and shop pages | [ezwebin](../../../features/6.0/extensions/ezwebin.md) |
| `ezdemo` | `ezdemo` | the design with demo content | [ezdemo](../../../features/6.0/extensions/ezdemo.md) |
| `sevenx_themes_media` | `media` | the media site design used with the layouts editor | [Layouts](../../../bc/6.0/LAYOUTS.md) |
| `ezflow` | `ezflow` | block-based pages of the classic flow extension | [ezflow](../../../features/6.0/extensions/ezflow.md) |

`sevenx_themes_simple` and `ezwebin` share the ezwebin classes, so content moves between them without changes.

## Adapting a theme the safe way

A theme installed by Composer is replaced on every update. Never edit its files in place
([sevenx_themes_simple, "Use it"](../../../features/6.0/extensions/sevenx_themes_simple.md#use-it)). Instead:

1. Create your own design extension, for example `extension/mysite_design/` with
   `design/simple/templates/` (the same design name as the theme) and `settings/design.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?
   [ExtensionSettings]
   DesignExtensions[]=mysite_design
   */ ?>
   ```

2. Activate it before the theme, so its files are found first:

   ```ini
   # settings/override/site.ini.append.php
   [ExtensionSettings]
   ActiveAccessExtensions[]=mysite_design
   ActiveAccessExtensions[]=sevenx_themes_simple
   ```

3. Copy only the templates you change into your extension, keeping their paths, and change them there.
4. Add your stylesheet to `[StylesheetSettings] CSSFileList[]` in your extension's `design.ini.append.php`
   ([Stylesheets and scripts for your site](stylesheets-and-scripts.md)).
5. Clear the caches for new template paths
   (`php bin/php/ezcache.php --clear-id=template-override --allow-root-user` and `--clear-tag=template`).

Why it works: designs are searched in extension order, and the first file found wins
([Designs, siteaccesses and the design fallback chain](designs-and-the-fallback-chain.md),
[Extension loading order](../../../features/6.0/extension-loading-order.md)).

## Your own design

Start from a design with nothing in it and let the `standard` design answer everything you have not written yet:

```text
extension/mysite_design/
├── design/mysite/
│   ├── templates/pagelayout.tpl        the frame of every page
│   ├── override/templates/full/        templates per class, chosen by override rules
│   ├── stylesheets/
│   ├── images/
│   └── javascript/
└── settings/
    ├── design.ini.append.php           DesignExtensions[]=mysite_design, CSSFileList[], JavaScriptList[]
    └── siteaccess/<site>/override.ini.append.php
```

Write the pagelayout first ([The pagelayout: your site's frame](the-pagelayout.md)), then one full and one line
template per class ([Template overrides for site builders](template-overrides.md)).

## Pointing a siteaccess at a design

```ini
# settings/siteaccess/<site>/site.ini.append.php
[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
```

`SiteDesign` is searched first, then each `AdditionalSiteDesignList[]` entry, then `standard` (`StandardDesign` in
`settings/site.ini`). Check what is in effect:

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign <site>
```

## Decision checklist

- [ ] Do the shipped theme's classes fit the content model? If not, plan your own templates.
- [ ] Is right-to-left needed? Check the theme's stylesheets ([Right-to-left sites](right-to-left-sites.md)).
- [ ] Does the theme meet your accessibility target? ([Accessibility for site builders](accessibility-for-site-builders.md))
- [ ] Who updates the theme, and how do you test an update before it goes live?
- [ ] Is the design in Git, in your own extension, not in `design/` of the installation?

## Related pages

- [Site packages and the themes that ship](site-packages-and-shipped-themes.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Branding: site name, logo, favicon and meta tags](branding-your-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
