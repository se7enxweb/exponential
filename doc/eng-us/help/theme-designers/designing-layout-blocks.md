---
title: "Designing layout blocks"
audience: theme-designer
category: theme-design
order: 26
summary: How theme designers design for Exponential Layouts, where editors build pages from blocks - designing block types and view types instead of pages, any order and any zone width, containers, shared header and footer layouts, and a block design sheet to hand over.
keywords: theme designer, layouts, blocks, block types, view types, zones, layout types, landing page, page builder, containers, shared layouts, block design
type: help
---
# Designing layout blocks

This page is for theme designers whose site uses **Exponential Layouts**, the block editor in which editors arrange
pages themselves. On those pages you do not design a page; you design the **blocks** editors place, the **view types**
they choose for each block, and the **layout types** whose zones hold the blocks. The technical side is covered well in
the designer help: [Layouts for designers](../designers/theme-design/layouts-for-designers.md),
[Block definitions](../designers/theme-design/block-definitions.md), [Block view templates](../designers/theme-design/block-view-templates.md),
[Styling layout blocks](../designers/theme-design/styling-layout-blocks.md) and
[Tutorial: build a landing page with layouts](../designers/theme-design/tutorial-build-a-landing-page-with-layouts.md).

## Contents

1. [Design the parts, not the page](#design-the-parts-not-the-page)
2. [Block types and view types](#block-types-and-view-types)
3. [Any order, any width](#any-order-any-width)
4. [Containers](#containers)
5. [Layout types and zones](#layout-types-and-zones)
6. [Shared layouts](#shared-layouts)
7. [Naming view types for editors](#naming-view-types-for-editors)
8. [The block design sheet](#the-block-design-sheet)

## Design the parts, not the page

A landing page mockup is still useful, as an example of what editors could build. But what you hand over is a set of
blocks, each designed on its own and checked next to every other. Ask yourself for every block: does it look right
directly below a hero, directly above the footer, next to itself, and alone on a page?

## Block types and view types

A **block type** (title, text, list, gallery, hero ...) defines what a block holds and which settings editors fill in.
A **view type** is one of the looks a block type offers; editors pick one per block. Adding a view type to an existing
block type needs a template and a line of settings, no PHP, which makes it the cheapest way to give editors a new
look ([Block definitions](../designers/theme-design/block-definitions.md#adding-a-view-type-no-php)). Adding a block type
needs a developer.

So when a new look is requested, first ask whether it is a new view type of an existing block. A "news cards" look is
a view type of the list block, not a new block.

## Any order, any width

- **Order:** editors can put any block after any other. Spacing between blocks comes from one rule of the design, not
  from each block's margins ([Styling layout blocks](../designers/theme-design/styling-layout-blocks.md#spacing-between-blocks)).
- **Width:** a block may sit in a full-width zone, a column of a container, or a narrow side zone. Draw each view type
  at full width and at about 300 pixels ([Spacing and grid](spacing-and-grid.md#grids-inside-zones)).
- **Content:** a list block can show one item or fifty; a text block can be one line or a long article. Use the stress
  set ([Designing with real content](designing-with-real-content.md#the-stress-set)).
- **Empty:** a block with nothing to show should not leave a gap ([Designing empty and error states](designing-empty-and-error-states.md#empty-content)).

## Containers

Container blocks (two, three or four columns) hold other blocks in their placeholders. Design the columns' gap and the
point where they stack on small screens, and check blocks inside a column of a three-column container: that is the
narrowest place most blocks will ever be.

## Layout types and zones

A layout type is a grid of named zones (for example a main zone and a sidebar). Editors choose a layout type when they
create a layout, so each layout type is a page skeleton you design once
([Layout types and zones](../designers/theme-design/layout-types-and-zones.md)). Keep their number small: two or three
layout types usually cover a content site.

## Shared layouts

Some layouts are **shared**: their zones (often a header, a footer or a pre-footer area) are linked into many other
layouts, so a change appears everywhere. Design those blocks with the same care as the frame, since they appear on
many pages ([Shared layouts and linked zones](../designers/theme-design/shared-layouts-and-linked-zones.md)).

## Naming view types for editors

Editors choose view types from a list of names. Name each by what it looks like and where it fits:

| Poor name | Better name |
|---|---|
| `style2` | Cards, three columns |
| `dark` | Highlighted band |
| `zigzag` | Alternating image and text |

The identifier in settings uses your theme's prefix (`mytheme_cards`); the label editors see is translated
([Designing for editors](designing-for-editors.md#telling-editors-what-the-design-expects)).

## The block design sheet

For each block type and view type, hand over:

| Item | Content |
|---|---|
| Block and view type | list, `mytheme_cards` |
| Purpose | three to twelve items as cards |
| Settings used | number of items, show date yes or no |
| Item view | the card view mode ([Designing view modes](designing-view-modes.md)) |
| Widths | full, two-column, side zone |
| Edge cases | one item, no image, empty collection |
| Themes | light, dark, right to left |

The [style guide page](building-a-style-guide-page.md) can show every block type and view type with sample content, or
build a test layout with all of them on the development installation.

## Related pages

- [Layouts for designers](../designers/theme-design/layouts-for-designers.md)
- [The layouts editor](../layouts-editor.md)
- [Exponential Layouts course for trainers](../trainers/layouts/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
