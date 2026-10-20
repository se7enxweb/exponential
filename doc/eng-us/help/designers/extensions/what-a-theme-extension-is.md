---
title: "What a theme extension is"
audience: designer
category: theme-extensions
order: 20
summary: A theme extension is one folder under extension/ that carries a design and everything it needs; why designs belong there and not in the installation's own design/ folder, with three real themes as examples.
keywords: theme extension, design extension, design folder, extension folder, upgrade safe, sevenx_themes_simple, cjw_themes_jumper, sevenx_themes_media
type: help
---
# What a theme extension is

This page is for designers who have built templates and stylesheets and wonder where to keep them.

## In short

- An **extension** is a folder under `extension/` (the folder is set by `site.ini [ExtensionSettings]
  ExtensionDirectory`, default `extension`). It does nothing until it is switched on.
- A **theme extension** is an extension whose main job is a design: a `design/<name>/` folder with templates,
  stylesheets, scripts and images, plus the settings that tell the installation to use it.
- Because it is a separate folder, a theme can be versioned, released, installed with Composer, packed as an
  `.ezpkg`, and upgraded without touching the kernel.

## Why not edit the installation's own design folders

The installation ships designs of its own (`design/standard`, the administration designs). Changing files there
looks quick, but:

| Editing `design/` of the installation | Using a theme extension |
|---|---|
| The next upgrade of Exponential overwrites your files | Upgrades leave `extension/<yourtheme>/` alone |
| Nobody can tell your changes from the product's | Your theme is one folder with its own history |
| It cannot be shared or sold separately | It can be published as a package |
| The file consistency check lists every changed file | The check of the product stays clean |

## What is inside one

A minimal theme extension has:

- `design/<design name>/templates/` with at least `pagelayout.tpl` (see
  [The pagelayout](../theme-design/the-pagelayout.md));
- `design/<design name>/stylesheets/`, `javascript/`, `images/`;
- `settings/design.ini.append.php` with `DesignExtensions[]=<extension name>`
  ([Registering the design](registering-the-design.md));
- `ezinfo.php` and `extension.xml` ([ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md));
- a `LICENSE` and a `README.md`.

## Three real examples

The themes 7x maintains are public repositories you can read:

| Theme | Design folder(s) | Notes |
|---|---|---|
| `sevenx_themes_simple` | `design/simple` | Ships content class packages in `packages/` ([Bundling content class packages](bundling-content-class-packages.md)) |
| `cjw_themes_jumper` | `design/starter`, `design/standard` | Its own settings file `cjw_jumper.ini`, siteaccess settings for `cjw_starter`, two small modules |
| `sevenx_themes_media` | the `media` design | Template operators, data types, a `share/` folder with seed data |

Each of these is a GitHub repository under `se7enxweb/` and a Composer package on Packagist; look at
`https://github.com/se7enxweb/cjw_themes_jumper` for a compact one.

## A design is not always a whole theme

Some extensions carry a design only as a side job: a module's templates in `design/standard/templates/<module>/`,
or an icon set. Everything in this section applies to them too; the word "theme" just means the design is the point.

## Next steps

- [Naming your theme extension](naming-your-theme-extension.md)
- [The folder structure of a theme extension](extension-folder-structure.md)
- [The design extension wizard](design-extension-wizard.md)

## Related pages

- [Design extensions](../theme-design/design-extensions.md)
- [Extensions: find, install, configure, build and release](../../../guides/extensions.md)
- [Theme extensions: start here](theme-extensions-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
