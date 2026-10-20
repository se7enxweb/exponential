---
title: Designs, siteaccesses and the design fallback chain
audience: website-creator, developer
category: website-creation
order: 110
summary: How Exponential finds a template, stylesheet or image - site design, additional designs, extension designs and the standard design - and how to read and change the chain for each siteaccess.
keywords: design, fallback, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, siteaccess, template lookup, ezdesign, ezimage, extension order, website creator
type: help
---
# Designs, siteaccesses and the design fallback chain

This page is for website creators who want to understand why a page uses one template and not another. Every
siteaccess has a chain of designs; Exponential walks it and takes the first file it finds. Once you can read the chain,
overrides, themes and extensions stop being a mystery.

## Contents

1. [The chain](#the-chain)
2. [Where designs live](#where-designs-live)
3. [Reading the chain of a siteaccess](#reading-the-chain-of-a-siteaccess)
4. [What uses the chain](#what-uses-the-chain)
5. [Changing the chain](#changing-the-chain)
6. [Common mistakes](#common-mistakes)

## The chain

For a template such as `node/view/full.tpl`, Exponential uses the first match in this order
([1. How a template is found](../../../guides/templates-and-design.md#1-how-a-template-is-found)):

1. the **override templates** whose rules in `override.ini` match the page, in `design/<design>/override/templates/`;
2. the plain `templates/` directory of the **site design** (`[DesignSettings] SiteDesign`);
3. the same directories of each **additional design** (`AdditionalSiteDesignList[]`, in order);
4. the **standard design** (`StandardDesign=standard` in `settings/site.ini`), the fallback you never edit.

Each design name can exist in several places: in `design/` of the installation and in every extension that declares
itself a design extension. Extensions are searched in their loading order, so an extension earlier in the list wins
over a later one with the same file.

## Where designs live

| Place | Declared by |
|---|---|
| `design/<design>/` of the installation | nothing; always searched |
| `extension/<ext>/design/<design>/` | `DesignExtensions[]=<ext>` in the extension's `settings/design.ini.append.php` |

A design directory holds `templates/`, `override/templates/`, `stylesheets/`, `images/` and `javascript/`.

## Reading the chain of a siteaccess

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign <siteaccess>
php bin/php/console exp:ini get site/DesignSettings/AdditionalSiteDesignList <siteaccess>
php bin/php/console exp:ini get design/ExtensionSettings/DesignExtensions <siteaccess>
```

`where` prints each file that sets the value and the one in effect; `get` prints the value
([The exp:ini command](../../../features/6.0/exp-ini-command.md)). To see which file answered on a real page, switch on
template path comments for the siteaccess, reload and search the page source for `START`
([Template path comments](../../../features/6.0/template-path-comments.md)).

## What uses the chain

| In a template | Finds |
|---|---|
| `{include uri='design:parts/header.tpl'}` | a template, through the chain |
| `{'logo.png'\|ezimage}` | an image in `images/` of the chain |
| `{'stylesheets/site.css'\|ezdesign}` | any file of the chain |
| `{ezcss_load(...)}`, `{ezscript_load(...)}` | stylesheets and scripts of the chain, packed by ezjscore ([Stylesheets and scripts](stylesheets-and-scripts.md)) |
| `{node_view_gui content_node=$node view='line'}` | `node/view/line.tpl`, or the override that matches |

## Changing the chain

One siteaccess, its own design first, the theme as fallback:

```ini
# settings/siteaccess/<site>/site.ini.append.php
[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=simple
```

The empty `AdditionalSiteDesignList[]` line clears what earlier files set, so the list is exactly yours. Then clear
the caches that remember template locations:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
```

To change which extension wins for the same design name, change the extension order
([Setup > Extensions: loading order](../../../features/6.0/extension-loading-order.md)).

## Common mistakes

| Symptom | Cause | Fix |
|---|---|---|
| A new template file is ignored | the override cache still holds the old lookup | clear `template-override` and `template` |
| A theme update wiped your change | you edited the theme in place | move the change into your own design extension |
| The admin shows your public design | you set `SiteDesign` in `settings/override/`, which applies to every siteaccess | set it in `settings/siteaccess/<site>/` |
| Your extension's file loses to the theme's | your extension loads after the theme | put it first in `ActiveAccessExtensions[]` or reorder in Setup > Extensions |
| A page is blank after adding a design | the pagelayout of the new design has an error | `php bin/php/eztemplatecheck.php` on the file ([5. Check the syntax](../../../guides/templates-and-design.md#5-check-the-syntax-before-you-look-at-the-browser)) |

## Related pages

- [Choosing a design or theme](choosing-a-design-or-theme.md)
- [Template overrides for site builders](template-overrides.md)
- [Additional extension directories](../../../features/6.0/additional-extension-directories.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
