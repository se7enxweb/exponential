---
title: Themes are extensions
audience: designer, developer
category: extension-development
order: 10
summary: Why an Exponential theme is packaged as an extension with a design folder, what that gives you over editing the kernel's design folders, and how a theme relates to designs, siteaccesses and the design chain.
keywords: theme, design extension, design, siteaccess, design chain, DesignExtensions, packaging, upgrade-safe
type: help
---
# Themes are extensions

Part 6, chapter 1. Exponential has no separate "theme" mechanism. A theme is an extension whose main content is a
design folder: `extension/<name>/design/<design name>/` with templates, stylesheets, scripts and images. This
chapter explains why that is a good thing, and introduces the three words this part uses all the time.

## Three words

| Word | Meaning |
|---|---|
| **design** | a named set of templates and files, for example `simple`, `standard`, `admin4`. A design name is a folder name under some `design/` folder |
| **design extension** | an extension that contributes design folders, registered in `design.ini [ExtensionSettings] DesignExtensions[]` |
| **siteaccess** | one entry point of the installation (a public site, the admin, an editor site), which chooses the designs it uses in `site.ini [DesignSettings]` |

A theme, then, is a design extension that provides a design for one or more siteaccesses.

## Why not edit the kernel's design folders?

The kernel has its own `design/standard/`, `design/admin/` and others. You could put your templates there. Do not:

- **Updates overwrite them.** The kernel's folders belong to the kernel's releases.
- **You cannot tell your files from the kernel's.** A year later nobody knows which template you changed.
- **You cannot reuse the theme.** It is mixed into one installation.

In an extension, the theme has its own folder, its own Git repository, its own version and license, and can be
installed into any number of sites with Composer.

## A real one

se7enxweb/sevenx_themes_simple is a public theme, and a good one to read. At the top level it has exactly what this
book's Part 3 describes for any extension:

```text
sevenx_themes_simple/
  composer.json  extension.xml  ezinfo.php  LICENSE
  design/simple/            the design "simple"
  settings/                 design.ini and site.ini additions, and per-siteaccess settings
  packages/                 content class packages the theme's templates expect
  translations/             the theme's interface texts
```

Its `settings/design.ini.append.php` starts with the line that makes it a design extension:

```ini
[ExtensionSettings]
DesignExtensions[]=sevenx_themes_simple
```

The next chapter goes through the rest: [The anatomy of a design extension](design-extension-anatomy.md).

## What a theme can and cannot do

| A theme can | A theme should not |
|---|---|
| replace any template, from the pagelayout to one attribute's view | contain PHP business logic (put that in a separate extension) |
| add view modes and override rules | change kernel settings unrelated to looks |
| bring stylesheets, scripts, fonts and images | ship content; ship content classes as packages instead (sevenx_themes_simple does) |
| add template operators it needs (in its own `autoloads/`) | depend on one particular installation's node IDs |

The last point matters when a theme is shared: a template that fetches node 254 works on your site and nowhere else.
Use settings for such values, with a sensible default.

## Related pages

- Previous: [Part 6: Theme developers](README.md)
- Next: [The anatomy of a design extension](design-extension-anatomy.md)
- [The design chain](../part-2-builder/the-design-chain.md)
- [A design inside an extension](../part-3-extension-developer/design-in-an-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
