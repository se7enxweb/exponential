---
title: Design extensions
audience: designer, developer
category: theme-design
order: 40
summary: How an extension carries a design: the DesignExtensions[] setting, the folder it points to, activating the extension for all or some siteaccesses, and what else a theme extension usually ships.
keywords: design extension, theme extension, DesignExtensions, design.ini, ActiveExtensions, ActiveAccessExtensions, extension design folder, theme package, sevenx_themes_media, cjw_themes_jumper
type: help
---
# Design extensions

This page is for designers who want their design to live in an extension (the normal way) instead of in the
kernel's `design/` folder. It answers: what makes an extension a design extension, how it is switched on, and what
the theme extensions of this installation ship besides their templates.

## Contents

1. [Why a design belongs in an extension](#why-a-design-belongs-in-an-extension)
2. [The one setting: DesignExtensions](#the-one-setting-designextensions)
3. [Activating the extension](#activating-the-extension)
4. [What a theme extension usually ships](#what-a-theme-extension-usually-ships)
5. [The theme extensions of this installation](#the-theme-extensions-of-this-installation)
6. [One extension, several designs](#one-extension-several-designs)
7. [Checks after activating](#checks-after-activating)

## Why a design belongs in an extension

- **Upgrades never touch it.** The kernel's `design/` folder belongs to the product; an upgrade replaces it.
- **It travels as one unit.** Templates, stylesheets, settings and translations go together in one folder, one Git
  repository, one Composer package.
- **It can be switched on and off.** Activating or deactivating the extension adds or removes the design without
  moving files.

## The one setting: DesignExtensions

An extension's designs are found only when the extension is listed in `design.ini [ExtensionSettings]
DesignExtensions[]`. The extension adds itself, in its own `settings/design.ini.append.php`. The starter theme's file,
`extension/cjw_themes_jumper/settings/design.ini.append.php`, is the whole example:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
# The design "starter" (design/starter/) of this extension
DesignExtensions[]=cjw_themes_jumper
*/ ?>
```

With that line, every folder `extension/cjw_themes_jumper/design/<name>/` becomes a design base for the design
`<name>`. The kernel builds the list in `eZTemplateDesignResource::designExtensions()`
(`kernel/common/eztemplatedesignresource.php`).

> **Note**
> The `.append.php` form with the `<?php /* ... */ ?>` wrapper is the usual way to write extension settings: the
> wrapper keeps the file from being shown as text if a web server ever served it.

## Activating the extension

An extension is read only when it is active. Two lists in `site.ini [ExtensionSettings]` decide that:

| Setting | Effect |
|---|---|
| `ActiveExtensions[]` | active for every siteaccess, read before the siteaccess is chosen |
| `ActiveAccessExtensions[]` | active only for the siteaccesses whose own `site.ini.append.php` lists it, read after the siteaccess is chosen |

A theme that only one site uses belongs in `ActiveAccessExtensions[]` of that siteaccess, so the other sites never
search its folders. Activation lines go into the installation's `settings/override/site.ini.append.php` or a
siteaccess's `site.ini.append.php`, which an administrator edits.

After activating a new extension, regenerate the autoload array if the extension has PHP classes, and clear caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

On this installation, `./console exp:velocity deploy` runs these steps and reloads PHP.

## What a theme extension usually ships

| Path in the extension | Purpose |
|---|---|
| `design/<design>/templates/`, `override/templates/`, `stylesheets/`, `javascript/`, `images/`, `fonts/` | the design |
| `settings/design.ini.append.php` | `DesignExtensions[]`, stylesheet and script lists |
| `settings/siteaccess/<siteaccess>/site.ini.append.php` | the design lines for a siteaccess of a known name |
| `settings/siteaccess/<siteaccess>/override.ini.append.php` | the override rules of the design |
| `settings/image.ini.append.php`, `content.ini.append.php` | image aliases and view settings the templates rely on |
| `translations/<locale>/translation.ts` | the design's own texts in other languages |
| `autoloads/eztemplateautoload.php`, `classes/` | template operators of its own, if any |
| `ezinfo.php`, `extension.xml`, `composer.json`, `LICENSE` | name, version, license and packaging |

The metadata files, packaging and release steps are covered in the theme extension pages of this help
([Theme extensions: start here](../extensions/theme-extensions-start-here.md)).

## The theme extensions of this installation

| Extension | Designs it carries | Notes |
|---|---|---|
| `sevenx_themes_media` | `media` | the public `site` and `bold` siteaccesses; overrides per siteaccess in `settings/siteaccess/site/` and `bold/` |
| `sevenx_themes_simple` | `simple` | lists its CSS and scripts in `CSSFileList[]` / `JavaScriptList[]` |
| `cjw_themes_jumper` | `starter`, `standard` | the `starter` siteaccesses, settings under `settings/siteaccess/cjw_starter/` |
| `sevenx_themes_super` | `sevenx_themes_super` | a small design with `site.css` and `site.js` |
| `ezwebin` | `ezwebin` | the classic website interface design, used as a fallback by `site` and `bold` |

Each is described on its own page: [The media theme](media-theme-tour.md), [The simple theme](simple-theme-tour.md),
[The starter theme](starter-theme-tour.md), [The super theme](super-theme-tour.md),
[The ezwebin design as a base](ezwebin-as-a-base.md).

## One extension, several designs

A design extension can carry any number of designs: `cjw_themes_jumper` has `design/starter` for its own look and
`design/standard` for templates that should be found by every siteaccess whose cascade reaches `standard`. Use a
`standard` folder in an extension with care: it is searched by **every** siteaccess that has the extension active and
falls back to `standard`, the administration included.

## Checks after activating

- [ ] The extension is active: the administration lists it under Setup > Extensions (or ask your administrator).
- [ ] A template of the design shows up: enable `ShowTemplatePathComments` on a test siteaccess and look at the page
      source ([Debugging a design](debugging-a-design.md)).
- [ ] A stylesheet of the design is in the packed CSS: the browser's network panel shows one packed file from
      `var/<site>/cache/public/stylesheets/` that contains your rules.
- [ ] No other siteaccess changed look: open the administration and the other public sites once.

## Related pages

- [The design cascade](the-design-cascade.md)
- [The design directory layout](design-directory-layout.md)
- [Tutorial: create a theme extension](tutorial-create-a-theme-extension.md)
- [Extension loading order](../../../features/6.0/extension-loading-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
