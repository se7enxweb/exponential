---
title: Your own design extension
audience: designer, developer, website-creator
category: build-two-ways
order: 500
summary: Set up a small design extension (here "mysite") next to a theme, activate it in the right order, and override any template of the theme or of Layouts without touching their files. Every step tested on a copy of the Fit & Healthy site.
keywords: design extension, template override, mysite, DesignExtensions, ActiveExtensions, extension order, design.ini, theme, sevenx_themes_media, override templates, clear cache
type: help
---
# Your own design extension

The template way of building starts with an extension of your own. It holds your templates, settings and stylesheets,
lives in its own git repository, and changes the site without editing the theme (`sevenx_themes_media`) or the
Layouts extensions, so their updates never overwrite your work. These pages call it `mysite`.

## Contents

1. [The files](#the-files)
2. [Activate it, in the right place](#activate-it-in-the-right-place)
3. [Override a template](#override-a-template)
4. [Clear the caches](#clear-the-caches)
5. [Check which file is used](#check-which-file-is-used)

## The files

```text
extension/mysite/
    settings/
        design.ini.append.php                      registers the design extension
        menu.ini.append.php, explayouts.ini.append.php ...   your settings
    design/
        media/                                     the design the site uses (SiteDesign of the siteaccess)
            templates/                             same paths as the templates you replace
            stylesheets/                           your CSS
```

`settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite

*/ ?>
```

The folder under `design/` must be the design the siteaccess uses: `media` for the Fit & Healthy site. Templates in
`design/standard/` would be found for every design, after the site's own design.

## Activate it, in the right place

Add `mysite` to `[ExtensionSettings] ActiveExtensions[]` of `site.ini` (Setup > Ini settings, or
`./console exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] mysite global`). **The position matters.** What
was measured on the test copy:

| `mysite` in `ActiveExtensions[]` | `DesignExtensions[]` in effect | Result |
|---|---|---|
| appended at the end (what `exp:ini add` does) | `mysite` listed before `sevenx_themes_media` | the theme's templates win; the override is ignored |
| placed before `sevenx_themes_media` | `mysite` listed after the theme | your templates win |
| placed first | `mysite` listed last | your templates win, and your `explayouts.ini` is read after Layouts' own (needed for new block types) |

The rule behind it: the extensions are read in the reverse order of `ActiveExtensions[]`, and in
`DesignExtensions[]` the last one listed wins. So an extension listed **earlier** in `ActiveExtensions[]` wins over
the ones after it. `exp:ini add` appends; to put the line first, edit the list in **Setup > Ini settings** (one
`=name` per line, in order) or in the file.

## Override a template

Copy the template you want to change from the theme to the same path in your extension, then change your copy. For the
header menu, for example:

```text
extension/sevenx_themes_media/design/media/templates/pagelayout/header/main_menu.tpl     (the theme's)
extension/mysite/design/media/templates/pagelayout/header/main_menu.tpl                  (yours, wins)
```

The same works for every block template of Layouts, for example
`extension/mysite/design/media/templates/explayouts/block/list/grid.tpl` replaces the List block's grid view
([How a block maps to its templates](block-to-template-map.md)).

## Clear the caches

A new template file needs the override list rebuilt: clear **Template & content**
(`./console exp:cache clear --tag=template,content`). After changing `ActiveExtensions[]`, clear the INI caches too
(`--tag=template,content,ini`).

## Check which file is used

```text
./console exp:ini get design.ini/ExtensionSettings/DesignExtensions -s site     # mysite must come after the theme
```

In the page's HTML your markup shows. The administration's **Quick settings** (right column) have **List of used
templates** and **Template debug** for a closer look.

## Related pages

- [A header menu that follows the tree](header-menu-from-the-tree.md)
- [Add a view type](add-a-view-type.md)
- [Add a block type](add-a-block-type.md)
- [Your own template blocks](template-blocks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
