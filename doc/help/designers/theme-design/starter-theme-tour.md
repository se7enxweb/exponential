---
title: A tour of the starter theme
audience: designer
category: theme-design
order: 120
summary: The starter design (extension cjw_themes_jumper) as an example of a theme that finds content by remote id, keeps behavior settings per siteaccess, draws its chrome with layouts and ships its own image aliases and settings file.
keywords: starter theme, cjw_themes_jumper, starter design, remote id, ExtensionSettingsSiteAccess, cjw_jumper.ini, layouts chrome, offcanvas menu, image aliases, global and siteaccess settings
type: help
---
# A tour of the starter theme

This page is for designers who want to see how a theme can be made portable between installations. The extension
`cjw_themes_jumper` (Composer package `se7enxweb/cjw_themes_jumper`) carries the design `starter`, used by the
`starter` and `starter_ger` siteaccesses of the demonstration installation. It answers: how the theme splits its
settings, how it finds content without node ids, and how it draws the page with the layouts engine.

## Contents

1. [What it brings](#what-it-brings)
2. [Global settings and siteaccess settings](#global-settings-and-siteaccess-settings)
3. [Content by remote id, never by node id](#content-by-remote-id-never-by-node-id)
4. [A settings file of its own](#a-settings-file-of-its-own)
5. [The pagelayout draws a layout](#the-pagelayout-draws-a-layout)
6. [Stylesheets](#stylesheets)
7. [Ideas worth copying](#ideas-worth-copying)

## What it brings

By its README: the design `starter` (pagelayout, header, offcanvas menu, breadcrumb, pre-footer and footer; full,
line, list, tile, teaser, slide, gallery and embed views), templates for the layouts blocks and their view types,
custom XML tags with plain renderings for every other design (in `design/standard/`), image aliases `cjw_i30` to
`cjw_i1920`, a map tile proxy module (`osm_proxy`) so visitors never contact the tile server, and German design
strings.

## Global settings and siteaccess settings

The extension is active for every siteaccess (`ActiveExtensions[]`), but only a few settings are global:

- **global** (`settings/`): `design.ini` (the design), `content.ini` (the custom XML tags, so every siteaccess can
  open and save the content without losing tags), `image.ini` (aliases), `cjw_jumper.ini` and `osm_proxy.ini`;
- **per siteaccess** (`settings/siteaccess/cjw_starter/`): `site.ini`, `override.ini`, `module.ini`, `collect.ini`,
  `explayouts.ini`, everything that changes how a site behaves.

The `starter` siteaccesses pull the per-siteaccess part in with one line:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=cjw_starter
```

Result: the administration and other sites are not affected by the theme's overrides, and a second language
siteaccess needs no copies.

## Content by remote id, never by node id

Node ids differ between installations. The theme therefore names content by **remote id**, which stays the same when the
content package is installed elsewhere:

- in `override.ini`: `Match[remote_id]=cjw-starter-o-root` for the root page's full view;
- in `cjw_jumper.ini`: `[SiteInfo] NodeRemoteID=cjw-starter-n-65`;
- in templates: `fetch( 'content', 'node', hash( 'remote_id', ezini( 'SiteInfo', 'NodeRemoteID', 'cjw_jumper.ini' ) ) )`.

Its settings file says: "Anything not found is left out of the page; the chrome then builds itself from the content
tree alone." That is a good rule for any theme.

## A settings file of its own

`cjw_jumper.ini` holds the design's choices: `[Header]` (the classes of header images, fallback images, landing and
large-header classes, logo images), `[Menu]`, `[LayoutSettings]` (which layout types and name prefixes belong to the
design, which module views are drawn without a layout). The templates read them with `ezini( ..., 'cjw_jumper.ini' )`.
A theme setting in its own INI file can be changed per siteaccess without touching templates. See
[Design extensions](design-extensions.md).

## The pagelayout draws a layout

`design/starter/templates/pagelayout.tpl` documents its variables at the top (all prefixed `pl_`), then:

1. fetches the current node from `$module_result.content_info.node_id`;
2. for a full view, resolves the layout of the node with
   `fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', ... ) )` and keeps it only when its type and name
   belong to this design (`[LayoutSettings]` of `cjw_jumper.ini`);
3. otherwise builds a "chrome" layout in the template itself: header zone, main zone with a `full_view` block, footer
   zone with a two-column pre-footer;
4. draws it with `{include uri='design:explayouts/layout.tpl' layout=$pl_layout}`, or, for the module views listed as
   plain, draws header, `#main` with `{$module_result.content}` and footer directly.

So the same block templates draw editor-built landing pages and ordinary pages. See
[Layouts for designers](layouts-for-designers.md).

## Stylesheets

`pagelayout/head/style.tpl` loads its files in one `ezcss_load` call at pack level 1 (merged, not minified), with the
order kept as in the original site ("the cascade order is the pixel contract: never reorder"). It also prints a short
`<style>` block that hides content blocks of other languages. On a site with an enforced Content Security Policy
such a block would need to move into a stylesheet (for example one rule per language in a packed file, switched by a
class on `<html>`); see [No inline CSS](no-inline-css.md).

## Ideas worth copying

- Remote ids instead of node ids, in settings, overrides and templates.
- Behavior settings per siteaccess through `ExtensionSettingsSiteAccess`, global settings only where every
  siteaccess needs them.
- A settings file of the theme's own for every design choice.
- Page variables defined once at the top of the pagelayout, documented in a comment.
- A plain fallback for every missing piece of content.

## Related pages

- [The themes that ship with Exponential](themes-on-alpha.md)
- [Designs and siteaccesses](designs-and-siteaccesses.md)
- Override a single node
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
