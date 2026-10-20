---
title: Styling layout blocks
audience: designer
category: theme-design
order: 760
summary: CSS for pages built with the layouts editor: the class names the shipped templates emit (slb, slb-zone, ngl-block, ngl-vt-*), the grid stylesheet, spacing between blocks, blocks that adapt to narrow zones, and dark mode for blocks.
keywords: style blocks, layouts css, slb, slb-zone, ngl-block, block classes, grid.css, explayouts css, container queries, block spacing, landing page css
type: help
---
# Styling layout blocks

This page is for designers who write the CSS of layouts pages. It answers: which classes the templates give you, how to
space and size blocks, and how to make one block look right in a wide and a narrow zone.

## Contents

1. [The classes you get](#the-classes-you-get)
2. [The grid stylesheet](#the-grid-stylesheet)
3. [Spacing between blocks](#spacing-between-blocks)
4. [Blocks in narrow zones](#blocks-in-narrow-zones)
5. [Light, dark and right to left](#light-dark-and-right-to-left)

## The classes you get

The shipped templates of `extension/explayouts/design/standard/templates/explayouts/` emit:

| Element | Classes and attributes |
|---|---|
| layout | `slb` (plus `slb-2-column` and the like in layout type templates), `data-layout-id` |
| zone | `slb-zone`, `data-zone-identifier`; in layout type templates also `sevenx-zone sevenx-zone-<name>` |
| block | `slb slb-<definition>` (`slb-title`, `slb-list`), `data-block-id` |
| container blocks | `ngl-block ngl-two-columns`, with `container`, `row` and `col-md-*` columns |

Designs that override the templates choose their own classes; the media and starter designs keep the class names of
the theme they were ported from. Look at the template path comments of a page to see which template drew a block
([Debugging a design](debugging-a-design.md)).

## The grid stylesheet

The media design loads `explayouts_standard/grid.css` (from the layouts extension's `design/standard/stylesheets/`)
before its own CSS, for the `container`, `row` and `col-*` classes. If your design draws containers with its own grid
(CSS grid or flex), you do not need it.

## Spacing between blocks

Give blocks vertical rhythm in one place:

```css
.slb-zone > * + * { margin-block-start: var(--site-space-5); }
```

instead of margins inside each block template. Editors then can stack any blocks without odd gaps.

## Blocks in narrow zones

The same list block may sit in a wide `main` zone and in a narrow `sidebar`. Use container queries on the zone:

```css
.slb-zone { container-type: inline-size; }
.slb-list .block-list { display: grid; gap: var(--site-space-3); }
@container (min-width: 40rem) {
  .slb-list .block-list { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
```

## Light, dark and right to left

- Colors from tokens only, so blocks follow dark mode ([Design tokens](design-tokens.md)).
- Logical properties, so two-column and image-with-text blocks mirror in right-to-left languages
  ([Logical CSS properties](logical-css-properties.md)).
- Hero blocks with text on images: check contrast in both themes ([Color contrast](color-contrast.md)).

## Related pages

- [Block view templates](block-view-templates.md)
- [Breakpoints and container widths](breakpoints-and-container-widths.md)
- [Tutorial: build a landing page with layouts](tutorial-build-a-landing-page-with-layouts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
