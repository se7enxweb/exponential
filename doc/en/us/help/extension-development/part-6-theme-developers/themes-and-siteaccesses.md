---
title: Themes and siteaccesses
audience: designer, developer
category: extension-development
order: 50
summary: Which site uses which theme - SiteDesign, AdditionalSiteDesignList and StandardDesign per siteaccess, themes that build on other themes, one theme for several sites, and how to see the chain a siteaccess really uses.
keywords: siteaccess, SiteDesign, AdditionalSiteDesignList, StandardDesign, design chain, theme inheritance, multisite, ActiveAccessExtensions, DesignExtensions
type: help
---
# Themes and siteaccesses

Part 6, chapter 5. A theme does nothing until a siteaccess uses it. This chapter shows the three settings that decide
which designs a siteaccess searches, how to build a theme on top of another one, and how to use one theme for several
sites with small differences.

## The three settings

In `site.ini [DesignSettings]`, per siteaccess:

| Setting | Searched | Typical value |
|---|---|---|
| `SiteDesign` | first | your theme's design: `mytheme` |
| `AdditionalSiteDesignList[]` | next, in order | the designs your theme builds on: `base` |
| `StandardDesign` | last | `standard` (the kernel's default) |

For each design name, the folders of the design extensions are searched first, in extension order, then the kernel's
own `design/<name>/`. The full rule, and a way to print the chain a siteaccess uses, are in
[The design chain](../part-2-builder/the-design-chain.md).

## Two switches that must both be on

A theme is used when:

1. **the extension is active** for the siteaccess: in `site.ini [ExtensionSettings] ActiveExtensions[]` (all
   siteaccesses) or the siteaccess's `ActiveAccessExtensions[]`; and
2. **the siteaccess names its design** in one of the three settings above.

`DesignExtensions[]` in the theme's own `design.ini` only makes its folder searchable. Forgetting the second switch is
the most common reason a new theme "does nothing"; the RAD wizard's generated README gets this wrong too
([A theme with the RAD wizard](a-theme-with-the-rad-wizard.md)).

## Where to write the siteaccess settings

| Where | When |
|---|---|
| `settings/siteaccess/<name>/site.ini.append.php` of the installation | the site decides which theme it uses (the usual case) |
| `settings/siteaccess/<name>/` inside the theme extension | the theme is made for a known siteaccess name and brings its settings |
| `./console exp:ini set ...` | from the command line, written to the installation's settings ([The exp:ini command](../part-2-builder/the-exp-ini-command.md)) |

```bash
./console exp:ini set site.ini/DesignSettings/SiteDesign mytheme siteaccess:mysite
./console exp:ini clear 'site.ini/DesignSettings/AdditionalSiteDesignList[]' siteaccess:mysite
./console exp:ini add 'site.ini/DesignSettings/AdditionalSiteDesignList[]' base siteaccess:mysite
```

## A theme on top of a theme

Because designs are searched in order, a theme can build on another theme: list the other theme's design in
`AdditionalSiteDesignList[]` after yours.

```ini
[DesignSettings]
SiteDesign=mytheme_red
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=mytheme
AdditionalSiteDesignList[]=base
```

`mytheme_red` then needs only the templates and stylesheets that differ: a header, a stylesheet with other colors.
Everything else comes from `mytheme`. This is the cleanest way to make variants, and the way to customize a theme
you did not write without forking it ([Extending without forking](../part-5-extend-fork-contribute/extending-without-forking.md)).

## One theme, several sites

An installation often has several public siteaccesses (languages, brands). They can share one theme:

| Differs per site | How |
|---|---|
| Nothing but the content | the same `SiteDesign` for all |
| Colors or logo | a small design per site on top of the shared one, as above |
| A few settings | per-siteaccess settings of the theme's own INI file, read in templates with `ezini()` |
| The menu's root node | a setting, never a node ID written into a template |

## Admin siteaccesses

Administration siteaccesses use the admin designs (`admin4`, `admin4l`, `admin`). A public theme does not belong in
their chain. If your theme's extension also contains templates for the administration (an edit template for a
datatype it adds), put them in `design/standard/` or `design/admin/` of the extension, where every chain finds them,
and keep the public look in the theme's own design.

## Checking the result

1. The design chain script of [The design chain](../part-2-builder/the-design-chain.md) prints the folders in order
   for a siteaccess.
2. With `ShowUsedTemplates=enabled` and the debug output on, each page lists the templates it used and the design
   each came from.
3. After changing design settings, clear the INI and template caches; a new template file also needs the
   template-override cache cleared.

## Related pages

- Previous: [A theme by hand](a-theme-by-hand.md)
- Next: [Theme assets, the packer and CSP](theme-assets-the-packer-and-csp.md)
- [Siteaccess settings](../part-2-builder/siteaccess-settings.md)
- [Settings scopes and their order](../part-2-builder/settings-scopes-and-their-order.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
