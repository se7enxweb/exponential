---
title: Layouts for designers
audience: designer
category: theme-design
order: 700
summary: The layouts editor from a designer's side: layouts, zones, blocks, collections and rules, how the pagelayout resolves and draws a layout, which templates draw what, and how a design takes over their markup.
keywords: layouts, layouts editor, page builder, landing pages, zones, blocks, layout types, explayouts, resolve_layout, layout.tpl, zone.tpl, block templates, design layouts
featured: true
type: help
---
# Layouts for designers

This page is for designers whose sites use the layouts editor, the page builder in which editors compose pages from
blocks. Editors decide **what** goes on a page; designers decide **how every block looks**. The page explains the
concepts, the rendering chain and where your templates plug in. Editors' side: [The layouts editor](../../layouts-editor.md);
the full reference: [Exponential Layouts](../../../bc/6.0/LAYOUTS.md).

## Contents

1. [Five ideas](#five-ideas)
2. [The rendering chain](#the-rendering-chain)
3. [The templates](#the-templates)
4. [Taking over the markup in your design](#taking-over-the-markup-in-your-design)
5. [What to read next](#what-to-read-next)

## Five ideas

| Idea | In one line | Designer's concern |
|---|---|---|
| **Layout** | a named arrangement based on a layout type | the grid of its zones |
| **Zone** | a named region of a layout (`main`, `left`, `footer`) | where blocks sit and how wide they are |
| **Block** | one item in a zone: text, image, list, hero ... | the block's markup and CSS per view type |
| **Collection** | the content a list-like block shows (picked or queried) | how items are drawn |
| **Rule** | which pages get which layout | none, but it explains which layout you see |

## The rendering chain

1. The pagelayout asks for a layout: `fetch( 'explayouts', 'resolve_layout', hash() )` (or
   `resolve_layout_for_node`).
2. The resolver walks the enabled rules by priority and returns the first published layout that matches.
3. `explayouts/layout.tpl` draws the layout: with the layout type's own template (`[LayoutType_<type>] Template=`) or
   the zones one under another.
4. `explayouts/zone.tpl` (or `zone_blocks.tpl` inside a layout type template) draws each top-level block of the zone.
5. Each block is drawn by `explayouts/block/<definition identifier>.tpl`, which can switch on `$block.view_type`.

If no rule matches, the pagelayout falls back to `{$module_result.content}`.

## The templates

In `extension/explayouts/design/standard/templates/explayouts/`:

| Template | Draws |
|---|---|
| `layout.tpl` | a layout: picks the layout type template or lists zones |
| `layouts/<type>.tpl` | the grid of one layout type (`1_column`, `2_column`, `hero`, `sidebar_left` ...) |
| `zone.tpl`, `zone_blocks.tpl` | the top-level blocks of a zone |
| `block/<definition>.tpl` | one block definition (`title.tpl`, `list.tpl`, `two_columns.tpl`, `full_view.tpl` ...) |
| `tpl_block/<name>.tpl` | the "template block": a part of the page drawn by a template the block names |

A block template receives `$block` (with `id`, `definition_identifier`, `view_type`, `name`, `values`, and for
children `parent_id` and `placeholder`), `$zone` and `$module_result`. The `full_view` block prints
`{$module_result.content}`: the page's own content inside the layout.

## Taking over the markup in your design

The layouts templates live in the `standard` design of the extension, so any design above `standard` in your cascade
replaces them by path. The media design has `templates/explayouts/layout.tpl`, `zone.tpl`, `block/...` and
`tpl_block/...` of its own; the starter design too. Copy only the block templates whose markup you want to change.
See [Block view templates](block-view-templates.md).

## What to read next

- [Layout types and zones](layout-types-and-zones.md)
- [Block definitions](block-definitions.md)
- [Shared layouts and linked zones](shared-layouts-and-linked-zones.md)
- [Layout rules](layout-rules.md)
- [Styling layout blocks](styling-layout-blocks.md)
- [Tutorial: build a landing page with layouts](tutorial-build-a-landing-page-with-layouts.md)

## Related pages

- [The pagelayout](the-pagelayout.md)
- [Fetching a layout in templates](../templates/layouts-template-fetch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
