---
title: A tour of the media theme
audience: designer
category: theme-design
order: 100
summary: The media design (extension sevenx_themes_media) as a worked example of a large, modern Exponential theme: its pagelayout parts, layouts block templates, packed stylesheets and scripts, self-hosted fonts, zoom and mobile fixes, settings and overrides.
keywords: media theme, sevenx_themes_media, media design, example theme, pagelayout parts, block templates, packed css, self-hosted fonts, zoom support, override rules, image aliases
type: help
---
# A tour of the media theme

This page is for designers who learn best from a real, complete theme. The media design, from the extension
`sevenx_themes_media` (Composer package `se7enxweb/sevenx-themes-media`), draws the public `site` and `bold`
siteaccesses of the demonstration installation. It answers: where its parts live, what it does that you may want to
copy, and what to keep in mind when you build on it.

## Contents

1. [Size and shape](#size-and-shape)
2. [The pagelayout and its parts](#the-pagelayout-and-its-parts)
3. [Stylesheets: one packed file](#stylesheets-one-packed-file)
4. [Scripts: two bundles, one deferred](#scripts-two-bundles-one-deferred)
5. [Fonts served from the design](#fonts-served-from-the-design)
6. [Zoom and phones](#zoom-and-phones)
7. [Block templates for the layouts editor](#block-templates-for-the-layouts-editor)
8. [Settings and overrides](#settings-and-overrides)
9. [Template operators of its own](#template-operators-of-its-own)
10. [What to copy, what to leave](#what-to-copy-what-to-leave)

## Size and shape

- Extension `extension/sevenx_themes_media/`, design `design/media/`.
- About 440 templates under `design/media/templates/` and more under `override/templates/` (`content`, `edit`,
  `embed`, `full`, `line`, `node`, `standard`, `view`).
- Stylesheets, scripts and fonts in `stylesheets/`, `javascript/`, `fonts/`; SCSS sources under `sass/`.
- Its README states that the templates are hand-maintained source: edit them directly.

![The media design on the public site: the Fit and Healthy front page drawn by layouts](../../images/designers/site-front-page.webp)

## The pagelayout and its parts

`templates/pagelayout.tpl` prepares a few variables (the current node, the breadcrumb path), includes
`pagelayout/variables.tpl`, then draws one of three frames:

- `payload` view mode: only `{$module_result.content}`, no frame;
- `layout_preview` view mode: a reduced frame for previews;
- every other page: the full frame.

The full frame's head is five includes (`pagelayout/head/title.tpl`, `meta.tpl`, `link.tpl`, `style.tpl`,
`script.tpl`) plus Open Graph and tag manager parts; the body starts with `pagelayout/accessibility_links.tpl`, a
skip link to `#main`. Header, breadcrumbs, footer, cookie control and bottom scripts are parts too.

## Stylesheets: one packed file

`pagelayout/head/style.tpl` loads every stylesheet with **one** `ezcss_load` call: `inter.css` (fonts) first, the
layouts grid, `index.css`, then the theme's own additions (`account.css`, `error-page.css`, `zoom-support.css`,
`exp-header-menu.css`, `exp-page-parts.css`, `exp-mobile-layout.css` ...). The packer merges them into one file in
`var/<site>/cache/public/stylesheets/`.

Lessons the file's own comment records:

- `ezcss_load`, not `ezcss`: `_load` also prints the stylesheets that content templates asked for with
  `ezcss_require` during the render.
- Order is preserved: the theme's base styles come first, required files after.
- Paths are design-relative and go through the cascade: `'explayouts_standard/grid.css'` is found in the layouts
  extension's `design/standard`.
- `exp-page-parts.css` holds rules that used to be `<style>` blocks and `style=""` attributes of page templates.

Note that `settings/design.ini.append.php` of the extension also lists `CSSFileList[]` entries; the media
pagelayout does not read that list, it names its files itself. Designs differ here: check the head template of the
design you build on ([Loading stylesheets](loading-stylesheets.md)).

## Scripts: two bundles, one deferred

`pagelayout/head/script.tpl` packs the render-blocking scripts (`runtime.js`, `index.js`) with `ezscript_load` at pack
level 2, and the rest (`index-noncritical.js`, `ajax-load-more.js`, the consent and header scripts) with
`ezscriptfiles( ..., 2, true() )`, which returns URLs so the template can write `<script defer src=...>` itself.
Configuration a script needs (texts in the page's language, a privacy page URL) is printed as
`<script type="application/json" id="exp-yt-config">` data, built with `json_encode` and `i18n`. A JSON data block
is not executed, so it needs no CSP nonce.

## Fonts served from the design

The Inter web font is served from `design/media/fonts/` and declared in `stylesheets/inter.css`, which is packed
with the rest. The comment in `style.tpl` explains why: a third-party font host costs extra DNS, TCP and TLS round trips
before any text can paint. See [Fonts in a design](fonts-in-a-design.md).

## Zoom and phones

- `zoom-support.css`: browser zoom shrinks the viewport in CSS pixels (a 1920 px screen at 200% is a 960 px
  viewport). Its rules give back controls the original theme hid below a breakpoint.
- `exp-mobile-layout.css`: tap targets, input sizes and image fixes for phones, tablets and 200% zoom.

Both are hand-maintained and loaded after the build output. See [Testing at zoom levels](testing-at-zoom-levels.md).

## Block templates for the layouts editor

`templates/explayouts/` holds the media versions of `layout.tpl`, `zone.tpl` and the block templates: `block/list.tpl`
dispatches by view type to `block/list/<view_type>.tpl` (`grid.tpl`, `numbered.tpl`, `zigzag.tpl`, `accordion.tpl` ...),
and `tpl_block/` holds the template blocks (`article_header.tpl`, `article_body.tpl`, `article_media.tpl` ...). They
replace the layouts extension's own templates through the cascade, because `media` is searched before `standard`.
See [Block view templates](block-view-templates.md).

## Settings and overrides

| File | Holds |
|---|---|
| `settings/design.ini.append.php` | `DesignExtensions[]=sevenx_themes_media` |
| `settings/site.ini.append.php` | `[TemplateSettings] ExtensionAutoloadPath[]=sevenx_themes_media`, which registers its template operators |
| `settings/siteaccess/site/site.ini.append.php` (and `bold/`) | the design chain and `TranslationExtensions[]` |
| `settings/siteaccess/site/override.ini.append.php` (and `bold/`) | the override rules of the full, line and embed views |
| `settings/image.ini.append.php` | the image aliases its templates ask for |
| `settings/explayouts.ini.append.php` | layouts settings of the theme |
| `translations/<locale>/translation.ts` | the theme's strings in eleven catalogues (`eng-US`, `ger-DE`, `fre-FR`, `heb-IL` ...) |

## Template operators of its own

The extension registers template operators of its own (`autoloads/eztemplateautoload.php`), for example `intro`,
`title`, `path`, `content_link`, `image`, `ng_image_alias`, `site_url`, `json_ld`, `explayouts_render_zone`. They come
from the theme's history as a port of a Twig theme. Templates of another design can use them only when this extension
is active. Prefer kernel operators in a design you want to be portable.

## What to copy, what to leave

**Copy:** the split pagelayout, one `ezcss_load` call, two script bundles with `defer`, JSON configuration blocks
instead of inline scripts, self-hosted fonts, a zoom stylesheet, a skip link.

**Leave:** the media site's content classes (`ng_*`) and the operators tied to them, unless your site uses that content
model.

## Related pages

- [The themes that ship with Exponential](themes-on-alpha.md)
- [The packer](the-packer.md)
- [Layouts for designers](layouts-for-designers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
