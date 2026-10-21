---
title: "Template principles: page layout, content views, attributes"
audience: builder, designer, developer
category: books
order: 400
summary: How a page is assembled from templates, from pagelayout.tpl down to a single attribute; view modes, your own view, a print layout, and where styles and images go.
keywords: template, pagelayout, content view, view mode, full, line, embed, attribute template, include, stylesheet, design, layout set, Learning book
type: help
---
# Template principles: page layout, content views, attributes

The template system is hierarchical. A first-level template, `pagelayout.tpl`, controls the frame. Inside it, **content
view** templates draw the thing that was asked for. Inside those, **attribute** templates draw single fields. Almost
every template can be overridden by a more specific one, depending on the section, the class, the depth in the tree and
more ([overriding templates](overriding-templates.md)).

## The page layout

`pagelayout.tpl` is always called. It draws the head (styles, scripts), the top menu, navigation, a search box, the
footer, and puts the content view in the middle (`$module_result.content`). It can be overridden: the home page may have
another layout than a catalog of products.

Further layouts for a printer-friendly page or a pop-up are chosen with a URL part, `/layout/set/<name>/`, in front of
the address: `/layout/set/print/<address>` uses the layout named `print`. The layouts are defined in `layout.ini`, each
with the page layout template it uses:

```ini
[print]
PageLayout=print_pagelayout.tpl
```

The shipped file defines `fullscreen`, `popup` and `print`. (The book writes the URL as `/set/layout/...`; the module is
`layout`, view `set`.) An override can tell such a page apart with the `layout` key ([Templates and design](../../guides/templates-and-design.md)).

## Content views

A content view is the presentation of what the URL asked for. The classic address `/content/view/full/2` means: show node 2
in the **full** view. With nice addresses the view is implied: the full view. Other standard views you meet:

| View | Used for |
|---|---|
| `full` | the page of the node |
| `line` | one line in a list of children |
| `embed`, `embed-inline` | an object put inside a text |
| `sitemap`, `print`, `pdf` | a site map, a print page, a PDF |

The templates are in `design/<design>/templates/node/view/<view>.tpl`. To make **your own view mode**, put a file with
your name in that folder, for example `extended.tpl` with more detail than the full view (the audit trail, the last
modifier, related objects), and call it as `/content/view/extended/<node id>`, or ask for it with `fetch` and
`node_view_gui` in a template ([view modes for builders](../extension-development/part-2-builder/view-modes-for-builders.md)).

Content views can be overridden for classes, sections or one node. A class's own full view is the most common override
there is.

## Attribute templates

A full view does not usually print the fields itself: it asks each attribute to draw itself with `attribute_view_gui`, and
the datatype's **view template** does it (a text line as text, an image as an image of a given alias, rich text as HTML,
see [rich text tags](rich-text-tags.md)). These attribute templates can be overridden too, by class, node and section.

## Modular templates

Code that is used in several places goes in its own file and is brought in with `include`, with parameters:

```
{include uri='design:parts/footer.tpl' show_social=true()}
```

Typical parts: the head's style list, a calendar, navigators, menu blocks.

## Style sheets and images

Style belongs in CSS files, so the templates hold only markup and template code. In Exponential, style sheets are listed in
`design.ini` (`[StylesheetSettings]`) and bundled by the packer; they are not written into a template
([The design chain](../extension-development/part-2-builder/the-design-chain.md)). Images that are part of the design
(logos, icons) are in the design's `images/` folder. Content images come from `ezimage` attributes.

## Edit templates

On a collaborative site you may not want editors in the administration. Edit templates, together with a module that
uses them, can offer a tailored interface in the site's own design, under the normal siteaccess
([Admin pages in the admin4 style](../extension-development/part-3-extension-developer/admin-pages-in-admin4-style.md)).

## Caching

Template processing is costly. Compiled templates, the content (view) cache and cache blocks reduce the cost
([caching in templates](caching-in-templates.md)).

Next: [template syntax basics](template-syntax-basics.md).

## Source and license

Adapted from chapter 3, "Displaying Content with eZ publish Templates", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; syntax, operators, settings and examples updated to Exponential 6 (foreach, current settings, no inline style or script).
