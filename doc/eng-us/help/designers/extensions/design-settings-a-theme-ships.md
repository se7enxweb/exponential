---
title: "The design settings a theme ships"
audience: designer, developer
category: theme-extensions
order: 130
summary: Which design settings belong in the theme extension and which belong to the installation - SiteDesign, AdditionalSiteDesignList[], StandardDesign and the stylesheet and script lists - with a recommended split.
keywords: SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignSettings, FrontendCSSFileList, FrontendJavaScriptList, StylesheetSettings, design.ini, site.ini
type: help
---
# The design settings a theme ships

This page is for designers deciding which settings to put in their theme and which to leave to the person who
installs it. How designs fall back on each other is explained in [The design cascade](../theme-design/the-design-cascade.md);
here the question is only **where each setting lives**.

## The settings involved

| Setting | File and block | Default in the kernel | Meaning |
|---|---|---|---|
| `SiteDesign` | `site.ini [DesignSettings]` | `admin` (the global default; siteaccesses set their own) | The design a siteaccess uses first |
| `AdditionalSiteDesignList[]` | `site.ini [DesignSettings]` | empty | Further designs searched before the standard one |
| `StandardDesign` | `site.ini [DesignSettings]` | `standard` | The last fallback |
| `DesignExtensions[]` | `design.ini [ExtensionSettings]` | empty | Extensions searched for designs |
| `FrontendCSSFileList[]`, `FrontendJavaScriptList[]` | `design.ini [StylesheetSettings]`, `[JavaScriptSettings]` | empty | Stylesheets and scripts the public pagelayout loads ([Loading stylesheets](../theme-design/loading-stylesheets.md)) |

## The recommended split

| In the theme extension | In the installation (siteaccess or override) |
|---|---|
| `DesignExtensions[]=<extension>` | `SiteDesign=<design>` for the siteaccess that should use the theme |
| The theme's own stylesheet and script lists | `AdditionalSiteDesignList[]` when the installation combines several themes |
| Defaults of the theme's own settings file | Site name, URLs, mail and database settings |

The reason: a theme can be installed on an installation with several siteaccesses. If the theme set `SiteDesign`
globally, it would change the administration and every other site too.

## When the theme ships a siteaccess

Some themes come with a siteaccess of their own (`cjw_themes_jumper` has settings for `cjw_starter`). Then
`SiteDesign` may live in `extension/<theme>/settings/siteaccess/<siteaccess>/site.ini.append.php`, because it only
applies while that siteaccess runs ([Siteaccess settings inside a theme](siteaccess-settings-in-an-extension.md)).

## Step by step

1. Put `DesignExtensions[]` and the stylesheet lists in the theme.
2. Document the `SiteDesign` line in the README instead of setting it.
3. On a test installation, set it for one siteaccess:

   ```bash
   ./console exp:ini set site.ini/DesignSettings/SiteDesign mytheme siteaccess:site --dry-run --allow-root-user
   ```

   Read the printed file, then run it again without `--dry-run`.
4. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
5. Check that the administration still uses its own design.

## Related pages

- [Designs and siteaccesses](../theme-design/designs-and-siteaccesses.md)
- [Registering the design](registering-the-design.md)
- [Settings files in an extension](settings-files-in-an-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
