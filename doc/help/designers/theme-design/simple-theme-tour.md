---
title: A tour of the simple theme
audience: designer
category: theme-design
order: 110
summary: The simple design (extension sevenx_themes_simple), the light default public design: how its pagelayout uses ezwebin's page data and a cache-block, how it loads CSS and scripts from design.ini, its menus, and what to change when you build on it.
keywords: simple theme, sevenx_themes_simple, simple design, default design, ezpagedata, cache-block, CSSFileList, JavaScriptList, menu templates, dropdown menu, template_look, pagedesign
type: help
---
# A tour of the simple theme

This page is for designers who use or adapt the simple design of the extension `sevenx_themes_simple` (Composer
package `se7enxweb/sevenx_themes_simple`), the light and responsive public design of a default installation. It answers:
how the design is put together, how it loads its files, and what to watch when you build on it. The extension's own
page is [sevenx_themes_simple](../../../features/6.0/extensions/sevenx_themes_simple.md).

## Contents

1. [What it is built on](#what-it-is-built-on)
2. [The pagelayout](#the-pagelayout)
3. [Stylesheets and scripts from design.ini](#stylesheets-and-scripts-from-designini)
4. [Menus](#menus)
5. [Overrides](#overrides)
6. [Things to modernize when you build on it](#things-to-modernize-when-you-build-on-it)

## What it is built on

- The content classes of the ezwebin family (article, blog, folder, gallery, product, event ...), which the
  extension ships as `.ezpkg` packages in `packages/`.
- The `ezpagedata` operator of the `ezwebin` extension, which collects facts about the page (CSS classes, the
  "template look" object, the current node).
- `ezjscore` for packing and `ezjsc::jquery` for jQuery.

## The pagelayout

`design/simple/templates/pagelayout.tpl`:

1. defines the basket, the user's role hash and the root node from settings;
2. opens a `{cache-block}` keyed by the URI (with the error type for error pages), whether the basket is empty, the
   user's object id and an optional `$extra_cache_key`;
3. inside it, reads `ezpagedata()` and the `template_look` object (the "page design" object editors edit in the
   administration, with fields such as the logo and **Hide powered by**);
4. includes `page_head.tpl`, `page_head_style.tpl`, `page_head_script.tpl`, the header and menus, the content and
   the footer.

The cache-block makes the frame cheap: it is rendered once per URI and user and then read from the cache. See
[Cache-block](../templates/cache-block.md) for the rules about keys.

## Stylesheets and scripts from design.ini

Unlike the media design, simple reads its file lists from settings. `page_head_style.tpl`:

```tpl
{ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
```

and `page_head_script.tpl`:

```tpl
{ezscript_load( array( ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),
                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

with the lists in `extension/sevenx_themes_simple/settings/design.ini.append.php`:

```ini
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

So a design on top of simple adds a stylesheet with one line in its own `design.ini.append.php`:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=mytheme.css
```

It is printed after simple's files, so your rules win at equal specificity.

## Menus

`templates/menu/` holds several menu templates (`flat_top.tpl`, `double_top.tpl`, `dropdown.tpl`,
`multi-dropdown.tpl`, `topbar.tpl`, `social-media.tpl`). `page_header.tpl` includes `menu/dropdown.tpl`; the
other choices are there as commented-out includes, so switching menus is a one-line change in a copy of
`page_header.tpl` in your design. Menus fetch
the children of the root node; see Menus and navigation.

The folder also holds editor leftovers (`*.backup`, `*.save`, `#...#` files). They are not used by the design; do not
copy them.

## Overrides

`design/simple/override/templates/` has `embed/`, `full/`, `line/`, `view/` and `article_full_page.tpl`; the rules are
in `settings/siteaccess/site/override.ini.append.php` and `settings/siteaccess/sevenx_site_user/` of the extension.

## Things to modernize when you build on it

The simple design is older than the current rules for the administration. When you build a new public design on it,
consider:

- **External fonts.** The pagelayout loads Inter from `fonts.googleapis.com` with `preconnect` hints. A
  self-hosted copy in your design's `fonts/` is faster and needs no third-party request (the media design does this,
  see [Fonts in a design](fonts-in-a-design.md)).
- **Internet Explorer conditional comments.** The `<!--[if IE ...]>` body classes serve browsers that are no longer
  supported. A new design does not need them.
- **Font Awesome.** It ships a large icon font; an SVG icon set used only where needed is lighter
  ([Icons in a design](icons-in-a-design.md)).
- **Content Security Policy.** Check the design's templates for inline scripts and styles before turning a policy on
  for the site ([CSP-safe design](csp-safe-design.md)).

## Related pages

- [The themes that ship with Exponential](themes-on-alpha.md)
- [The ezwebin design as a base](ezwebin-as-a-base.md)
- [Loading stylesheets](loading-stylesheets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
