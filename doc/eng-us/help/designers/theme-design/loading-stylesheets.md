---
title: Loading stylesheets
audience: designer
category: theme-design
order: 200
summary: How a design's CSS reaches the page: the ezcss_load operator, the design.ini lists (CSSFileList, FrontendCSSFileList, BackendCSSFileList, AdminComponentCSSFileList), stylesheets a content template requires, order, media and the packer.
keywords: stylesheets, css, load css, ezcss_load, ezcss_require, ezcss, CSSFileList, FrontendCSSFileList, BackendCSSFileList, AdminComponentCSSFileList, design.ini, StylesheetSettings, css order
featured: true
type: help
---
# Loading stylesheets

This page is for designers who add a stylesheet to a design, or wonder why one is not on the page. It answers: which
operator prints stylesheets, where the file lists live, in which order files arrive, and how a single content
template can ask for an extra stylesheet.

## Contents

1. [The short answer](#the-short-answer)
2. [ezcss_load in the pagelayout](#ezcss_load-in-the-pagelayout)
3. [File lists in design.ini](#file-lists-in-designini)
4. [A stylesheet for one kind of page only](#a-stylesheet-for-one-kind-of-page-only)
5. [Order and specificity](#order-and-specificity)
6. [Media and print](#media-and-print)
7. [What not to do](#what-not-to-do)

## The short answer

1. Put the file in `design/<yourdesign>/stylesheets/` of your design extension.
2. Name it in the `ezcss_load( array( ... ) )` call of your pagelayout's head, or in a `design.ini` list your
   pagelayout reads.
3. Clear caches and hard-reload. The file reaches the browser packed with the others ([The packer](the-packer.md)).

## ezcss_load in the pagelayout

The ezjscore extension provides the operators (`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`):

| Operator | Does |
|---|---|
| `ezcss_load( files, media, type, rel, charset, pack_level )` | prints `<link>` tags for the files given **plus** every file queued with `ezcss_require` so far |
| `ezcss_require( files )` | queues files for the next `ezcss_load`; after it has run, prints them at once |
| `ezcss( files, ... )` | prints tags for the files given, nothing else |
| `ezcssfiles( files, pack_level, ignore_loaded )` | returns the packed URLs instead of tags |

Default `media` is `all` and default `pack_level` 3. Paths are relative to `stylesheets/` and found through the design
cascade, so `'mytheme.css'` finds `design/<any design in the chain>/stylesheets/mytheme.css`, and
`'explayouts_standard/grid.css'` finds a file in a subfolder.

The media design's head (`pagelayout/head/style.tpl`):

```tpl
{ezcss_load( array( 'inter.css',
                    'explayouts_standard/grid.css',
                    'index.css',
                    'shop.css',
                    'exp-mobile-layout.css' ) )}
```

(shortened). Use `ezcss_load`, not `ezcss`, in the pagelayout: only `_load` prints what other templates required.

## File lists in design.ini

`settings/design.ini [StylesheetSettings]` defines lists a pagelayout can read with `ezini()`:

| Setting | Meant for |
|---|---|
| `FrontendCSSFileList[]` | public designs (an extension adds its stylesheet here) |
| `BackendCSSFileList[]` | administration designs |
| `AdminComponentCSSFileList[]` | the admin's shared components and module stylesheets (`components.css`, `modules/setup.css` ...) |
| `CSSFileList[]` | the older list for both, kept for compatibility (DEPRECATED in favor of the two above) |

A list only matters if the design's head template reads it. The kernel's `design/standard/templates/pagelayout.tpl`
and the simple design read `CSSFileList` and `FrontendCSSFileList`; admin4's `page_head_style.tpl` reads
`AdminComponentCSSFileList` and `BackendCSSFileList`; the media design names its files itself. If you want other
extensions to be able to add stylesheets to your design, read the lists:

```tpl
{ezcss_load( array( 'mytheme.css',
                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
```

and an extension then adds one line in its own `settings/design.ini.append.php`:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=myextension.css
```

## A stylesheet for one kind of page only

A full view template that needs extra CSS asks for it; the pagelayout's `ezcss_load` prints it:

```tpl
{* override/templates/full/event.tpl *}
{ezcss_require( array( 'event-calendar.css' ) )}
<article class="event">...</article>
```

This works because the view runs before the pagelayout. The list travels in the persistent variable, so it also
survives the view cache. The visitor downloads the file only on pages that need it; on all other pages it costs nothing.

## Order and specificity

- Files come out in the order of the array; files queued with `ezcss_require` come after the array.
- Later files win at equal specificity. Put base styles first, then components, then page-specific files, then fixes.
- Duplicates are dropped: a file required twice is printed once.

## Media and print

The second parameter is the `media` attribute:

```tpl
{ezcss_load( array( 'mytheme.css' ) )}
{ezcss( array( 'print.css' ), 'print' )}
```

A print stylesheet is often simpler inside the main file, with `@media print { ... }` ([Print stylesheets](print-stylesheets.md)).

## What not to do

- **No `<style>` blocks or `style=""`** in templates ([No inline CSS](no-inline-css.md)).
- **No hand-written `<link>` to `/extension/.../stylesheets/x.css`**: it bypasses the cascade and the packer.
- **No `?v=` query strings** to force reloads ([No version queries on asset links](no-version-queries-on-asset-links.md)).
- **No CDN stylesheets** without a reason: third-party hosts slow the first paint and must be allowed by the site's
  Content Security Policy ([External resources and CDNs](external-resources-and-cdns.md)).

## Related pages

- [Loading JavaScript](loading-javascript.md)
- [The ezcss and ezscript operators](ezcss-and-ezscript-operators.md)
- [The packer](the-packer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
