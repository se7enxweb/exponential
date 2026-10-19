---
title: "Tutorial: port a static HTML design"
audience: designer
category: theme-design
order: 830
summary: Turn a static HTML/CSS mockup or an agency's templates into an Exponential design: split the page into pagelayout and parts, move assets into the design folders, replace fixed texts and links with template code, map page types to view templates, and remove inline code on the way.
keywords: tutorial, port html, static html, mockup to theme, convert html template, agency design, html to tpl, design port, pagelayout from html, page types
type: help
---
# Tutorial: port a static HTML design

This tutorial is for designers who received static HTML pages (from an agency, a style guide or a purchased template)
and must turn them into a working Exponential design. Allow a day for a typical site of five page types.

## Contents

1. [Step 1: inventory](#step-1-inventory)
2. [Step 2: assets into the design](#step-2-assets-into-the-design)
3. [Step 3: the frame becomes the pagelayout](#step-3-the-frame-becomes-the-pagelayout)
4. [Step 4: page types become view templates](#step-4-page-types-become-view-templates)
5. [Step 5: fixed content becomes template code](#step-5-fixed-content-becomes-template-code)
6. [Step 6: clean up on the way](#step-6-clean-up-on-the-way)

## Step 1: inventory

List the page types of the mockup and map each to Exponential:

| Mockup page | Becomes |
|---|---|
| frame (header, menu, footer) | `pagelayout.tpl` and parts |
| home page | full view of the front page class, or a layout ([Layouts for designers](layouts-for-designers.md)) |
| article | full view override for class `article` |
| list of articles | full view of `folder` with a line view per item |
| contact form | full view of the feedback form class |
| search results | `content/search.tpl` |

## Step 2: assets into the design

```
mockup/css/*.css     ->  design/mytheme/stylesheets/
mockup/js/*.js       ->  design/mytheme/javascript/
mockup/img/*         ->  design/mytheme/images/
mockup/fonts/*       ->  design/mytheme/fonts/
```

Fix relative `url()` paths in the CSS (`../images/...`, `../fonts/...`). Load the CSS with one `ezcss_load` and the scripts
with `ezscript_load` ([Loading stylesheets](loading-stylesheets.md)). Replace a CDN link to a library with a local copy
([External resources and CDNs](external-resources-and-cdns.md)).

## Step 3: the frame becomes the pagelayout

Take the mockup's common frame; replace the content area with `{$module_result.content}`; split header, footer and menu
into `pagelayout/*.tpl` parts ([The pagelayout](the-pagelayout.md)). Replace:

| Mockup | Template |
|---|---|
| `<html lang="en">` | `<html lang="{$site.http_equiv.Content-language\|wash}">` |
| `<title>Site</title>` | a title from the page's path and `$site.title` |
| `href="index.html"` | `href={'/'\|ezurl}` |
| `src="img/logo.svg"` | `src={'logo.svg'\|ezimage}` |
| a fixed menu | a fetch of the root's children ([Menus and navigation](../templates/menus-and-navigation.md)) |

## Step 4: page types become view templates

For each content page type, an override with its own template ([Tutorial: override a full view](tutorial-override-a-full-view.md)).
Paste the mockup's markup and replace the dummy texts with fields:

```tpl
<h1>{$node.name|wash}</h1>
<div class="lead">{attribute_view_gui attribute=$node.data_map.intro}</div>
```

## Step 5: fixed content becomes template code

- Lorem ipsum becomes fields of the content.
- Repeated items (cards, teasers) become a `{foreach}` over a fetch and a line view.
- Interface texts ("Read more", "Search") go through `i18n`.
- Dates through `l10n`.

## Step 6: clean up on the way

Static templates often carry habits that do not survive in Exponential:

- inline `<style>`, `style=""`, `onclick=""` and inline scripts: move them into files ([CSP-safe design](csp-safe-design.md));
- missing `alt`, skipped headings, a `div` used as a button: fix them now ([Accessibility basics for themes](accessibility-basics-for-themes.md));
- fixed colors: turn them into tokens, which gives you dark mode later ([Design tokens](design-tokens.md));
- `left`/`right` in CSS: logical properties, for right-to-left languages.

## Related pages

- [Tutorial: create a theme extension](tutorial-create-a-theme-extension.md)
- [Choosing a starting theme](choosing-a-starting-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
