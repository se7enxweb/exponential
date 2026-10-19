---
title: What a design is
audience: designer
category: theme-design
order: 10
summary: The basic idea of the Exponential design system: a design is a named folder of templates, stylesheets, scripts and images; a siteaccess picks designs; the kernel looks for every file through a fallback chain.
keywords: design, theme, skin, look, template set, design folder, siteaccess, fallback, standard design, base design, what is a design
type: help
---
# What a design is

This page is for designers who are new to Exponential and want the one idea that everything else builds on. It
answers: what Exponential calls a design, what is inside one, how a site chooses its designs, and how a design
relates to the words "theme" and "template" that you may know from other systems.

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [What a design contains](#what-a-design-contains)
3. [Where designs live](#where-designs-live)
4. [How a site chooses its designs](#how-a-site-chooses-its-designs)
5. [Design, theme, template: the words](#design-theme-template-the-words)
6. [What a design does not contain](#what-a-design-does-not-contain)
7. [Next steps](#next-steps)

## In one paragraph

A **design** is a folder with a name, for example `media`, `starter` or `admin4`. Inside it are the templates that
produce the HTML of every page, and the stylesheets, scripts, fonts and images those pages use. A **siteaccess** (one
"site" of an installation: a host name, a path prefix or a port) says which design it shows. When the kernel needs a
file, say `pagelayout.tpl` or `stylesheets/main.css`, it does not look in one folder only: it walks a list of designs,
the **design cascade**, and takes the first copy it finds. That is why a new design can be small: it only contains
the files that differ from the designs below it.

## What a design contains

Every design folder follows the same layout. The folders are optional; a design has only what it needs.

| Folder | What goes in it | Read by |
|---|---|---|
| `templates/` | TPL templates: `pagelayout.tpl`, `node/view/full.tpl`, `content/datatype/view/ezstring.tpl` ... | the template engine (`eZTemplateDesignResource`) |
| `override/templates/` | templates that replace another template only under conditions (`override.ini`) | the override system |
| `stylesheets/` | CSS files | the `ezcss*` operators of ezjscore and the `ezdesign` operator |
| `javascript/` | JavaScript files | the `ezscript*` operators of ezjscore |
| `images/` | images of the design itself (logos, icons, backgrounds) | the `ezimage` and `ezdesign` operators |
| `fonts/` | web fonts | your stylesheets (`url()` in CSS) |

The full description, with every folder the kernel knows, is in [The design directory layout](design-directory-layout.md).

## Where designs live

Designs come from two places:

- **The kernel's `design/` folder**, for example `design/standard`, `design/base`, `design/admin4`, `design/admin4l`,
  `design/editor`.
- **Extensions**, in `extension/<extension>/design/<design>/`. An extension says it carries designs with
  `DesignExtensions[]` in its `settings/design.ini.append.php`. The theme extensions on this installation do exactly
  that: `extension/sevenx_themes_media/design/media`, `extension/sevenx_themes_simple/design/simple`,
  `extension/cjw_themes_jumper/design/starter`, `extension/sevenx_themes_super/design/sevenx_themes_super`.

A design of the same name can exist in several places at once: `design/standard` in the kernel and
`extension/explayouts/design/standard` in an extension, for example. The kernel treats them as one design and searches
the extension copies first (see [The design cascade](the-design-cascade.md)).

## How a site chooses its designs

Three settings in `site.ini [DesignSettings]` decide it (defaults in `settings/site.ini`):

```ini
[DesignSettings]
# The standard design, used as fallback design if the element is not in the sitedesign
StandardDesign=standard
# The design of the site
SiteDesign=admin
# Designs searched after SiteDesign and before StandardDesign
AdditionalSiteDesignList[]
```

Each siteaccess sets its own values in `settings/siteaccess/<siteaccess>/site.ini.append.php`. On this installation
the public siteaccess `site` uses:

```ini
[DesignSettings]
SiteDesign=media
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=ezwebin
AdditionalSiteDesignList[]=standard
AdditionalSiteDesignList[]=base
```

So a template is looked up in `media`, then `ezwebin`, then `standard`, then `base`.

## Design, theme, template: the words

| Word | Meaning in Exponential |
|---|---|
| **Design** | A named folder of templates and assets, as above. The kernel's own word. |
| **Theme** | Common speech for a design that gives a site its look, usually shipped as an extension (the `sevenx_themes_*` extensions, `cjw_themes_jumper`). A theme extension can carry more than one design and also settings, translations and classes. |
| **Template** | One `.tpl` file inside a design, written in the template language (TPL). |
| **Pagelayout** | The outermost template, `pagelayout.tpl`, which draws the frame of every page and puts the module's result in it. |
| **Override** | A template that replaces another one under conditions, configured in `override.ini`. |

## What a design does not contain

- **Content.** Pages, articles, images uploaded by editors live in the content repository, not in the design.
- **Content structure.** Content classes (the fields of an article) are defined in the administration. A design only
  decides how they look. If your design needs a new field, ask the developer who maintains the classes.
- **Settings of the whole installation.** A theme extension may ship settings (`settings/*.ini.append.php`), but the
  design folder itself holds no INI files.
- **Business logic.** Template operators and fetch functions give templates their data; new ones are PHP code in an
  extension (see the extension pages of this help).

## Next steps

- [Designs and siteaccesses](designs-and-siteaccesses.md): how a siteaccess is chosen and how it names its designs.
- [The design cascade](the-design-cascade.md): the exact search order.
- [The pagelayout](the-pagelayout.md): the template that frames every page.
- [Templates overview](../templates/templates-overview.md): the template language.

## Related pages

- [Designers: start here](../designers-start-here.md)
- [Design extensions](design-extensions.md)
- [Design glossary](design-glossary.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
