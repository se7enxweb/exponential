---
title: The anatomy of a design extension
audience: designer, developer
category: extension-development
order: 20
summary: Every folder and file of a theme extension - templates, override templates, stylesheets, scripts, images, the settings that register it and its override rules - shown on the public sevenx_themes_simple theme.
keywords: design extension, anatomy, templates, override templates, stylesheets, javascript, images, design.ini, override.ini, siteaccess settings, pagelayout.tpl
type: help
---
# The anatomy of a design extension

Part 6, chapter 2. This chapter opens a real theme, se7enxweb/sevenx_themes_simple (GPL 2 or later, version 1.0.24 at
the time of writing), and explains each folder and file. Read it with the theme's repository open beside you.

## The design folder

```text
design/simple/
  templates/                 templates found by name through the design chain
    pagelayout.tpl           the frame of every page
    page_head.tpl            <head>: title, meta tags
    page_head_style.tpl      stylesheets
    page_head_script.tpl     scripts
    page_header.tpl          the site header
    page_footer.tpl          the site footer
    node/view/               the kernel's node view templates, replaced
    content/  menu/  parts/  user/  shop/  error/  ...
  override/templates/        templates used through override rules
    full/  line/  embed/  view/
  stylesheets/
  javascript/
  images/
```

### templates/

A template here replaces the template of the same path in designs further down the chain (the kernel's `standard`
design, for example). `pagelayout.tpl` is the one every page is drawn in; the theme splits it into `page_head*.tpl`,
`page_header.tpl` and `page_footer.tpl`, which it includes. Splitting keeps each file short and lets a site override
only the header.

### override/templates/

Templates that are not found by name but chosen by an override rule: "for a node of class `video` in the full view,
use `full/video.tpl`". The folder name `override/templates` is the convention the kernel's own designs use; the rule
names the subfolder. See [Template overrides](../part-2-builder/template-overrides-basics.md).

### stylesheets/, javascript/, images/

Files found through the design chain by the `ezcss_load`, `ezscript_load` and `ezimage` template functions and
operators. The theme keeps third-party libraries in a `libs/` subfolder of `stylesheets/`, with each library's own
license (chapter [Distributing a theme](distributing-a-theme.md) says why).

## The settings

### settings/design.ini.append.php

```ini
[ExtensionSettings]
DesignExtensions[]=sevenx_themes_simple

[StylesheetSettings]
CSSFileList[]=websitetoolbar.css
CSSFileList[]=libs/fontawesome/css/all.min.css
CSSFileList[]=magnific-popup.css
CSSFileList[]=main.css
CSSFileList[]=main.res.css

[JavaScriptSettings]
JavaScriptList[]=ezjsc::jquery
JavaScriptList[]=jquery.magnific-popup.js
JavaScriptList[]=main.js
```

(Shortened from the real file.) `DesignExtensions[]` registers the extension's `design/` folder. The stylesheet and
script lists are what `page_head_style.tpl` and `page_head_script.tpl` load. The kernel's `design.ini` marks
`CSSFileList[]` and `JavaScriptList[]` as deprecated in favor of `FrontendCSSFileList[]` and
`FrontendJavaScriptList[]`; the theme's head templates load both the old and the new lists, so either works. A new
theme should use the Frontend lists ([Theme assets, the packer and CSP](theme-assets-the-packer-and-csp.md)).

### settings/site.ini.append.php

```ini
[RegionalSettings]
TranslationExtensions[]=sevenx_themes_simple
```

This makes the theme's `translations/` folder part of the site's translations, so its interface texts can be
translated.

### settings/siteaccess/ (one folder per siteaccess)

The theme ships settings for the siteaccesses it is meant for (`site` and `sevenx_site_user` in this theme), among
them `override.ini.append.php` with its override rules:

```ini
[full_video]
Source=node/view/full.tpl
MatchFile=full/video.tpl
Subdir=templates
Match[class_identifier]=video
```

Settings in an extension's `settings/siteaccess/<name>/` apply only to the siteaccess of that name. The siteaccess
must also have the extension active (`ActiveAccessExtensions[]` or the global `ActiveExtensions[]`).

## packages/

The theme's templates expect content classes such as `article`, `blog_post` or `event`. The theme ships them as
packages (`.ezpkg` files) so a new site can import them, instead of assuming they exist. See
[Packages and the package creator](../part-7-releasing/packages-ezpkg.md).

## What to watch for in your own theme

- **Node IDs in override rules.** A rule with `Match[node]=67` works on the site it was written for. In a theme
  meant for others, prefer class identifiers, sections or a setting.
- **Editor leftovers.** Backup files (`*.save`, `*~`) and minified copies next to the source should be either all
  intentional or none. Add a `.gitignore` for backups.
- **One design name per look.** If the theme has two looks, make them two designs (`simple` and `simple_dark`), not
  conditions all over the templates.

## Related pages

- Previous: [Themes are extensions](themes-are-extensions.md)
- Next: [A theme with the RAD wizard](a-theme-with-the-rad-wizard.md)
- [The extension directory layout](../part-3-extension-developer/extension-directory-layout.md)
- [Override conditions](../part-2-builder/override-conditions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
