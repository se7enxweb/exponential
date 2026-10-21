---
title: "Spacing and grid"
audience: theme-designer
category: theme-design
order: 14
summary: How theme designers define spacing and a layout grid for an Exponential theme - one spacing scale as tokens, space inside and between components, a page grid that also works inside narrow layout zones, logical properties, and what to hand over.
keywords: theme designer, spacing, spacing scale, grid, layout grid, gutters, columns, container, rhythm, tokens, logical properties, layout zones
type: help
---
# Spacing and grid

This page is for theme designers who decide the space between things and the columns things sit in. In an
Exponential theme both become tokens and a small number of layout classes. The breakpoints and container widths
themselves are covered in [Breakpoints and container widths](../designers/theme-design/breakpoints-and-container-widths.md),
and the spacing between layout blocks in [Styling layout blocks](../designers/theme-design/styling-layout-blocks.md).

## Contents

1. [One spacing scale](#one-spacing-scale)
2. [Space inside and space between](#space-inside-and-space-between)
3. [The page grid](#the-page-grid)
4. [Grids inside zones](#grids-inside-zones)
5. [Logical properties](#logical-properties)
6. [The scale as tokens](#the-scale-as-tokens)
7. [What to hand over](#what-to-hand-over)

## One spacing scale

Pick five to seven steps and use nothing else. A scale where each step is about 1.5 to 2 times the one before (for
example 4, 8, 16, 24, 40 pixels, written in `rem`) gives clear differences between "related" and "separate". Values in
between are not allowed in the mockup either: in most design tools you can set the scale as spacing variables, so
nudging by 3 pixels is visible as a mistake.

## Space inside and space between

Decide two rules and write them next to the scale:

- **Inside** a component (padding of a card, gap between a label and its field): small steps.
- **Between** components and sections: larger steps, and larger still between unrelated sections.

The space between two elements belongs to their container, not to either element. Give stacks a `gap` (in flex or
grid) rather than a bottom margin on every child; editors' content then never produces a double margin or a missing
one at the end of a list.

Rich text is the exception: paragraphs, lists and headings inside a text field need vertical margins, because there
is no container per element. Set them once for the rich text area, in steps of the scale.

## The page grid

A content site usually needs only:

- a **container** with a maximum width and side padding that grows at larger widths;
- a **content column** limited to a readable measure ([Type scale](type-scale.md#line-height-and-measure));
- a **two-column** layout (content and sidebar) that becomes one column on small screens.

A twelve-column grid in the design tool is a fine drawing aid, but you do not need twelve column classes in the CSS.
Describe the layouts by their intent ("content and sidebar, 2 to 1, stacked below 60em"), and let CSS grid express
them.

## Grids inside zones

With Exponential Layouts, editors put blocks into zones, and a zone may be the full width, a column of a two-column
container block, or a narrow sidebar. A card grid designed for 1200 pixels will also appear in a 300 pixel zone.
Therefore:

- design every block view type at full width and at sidebar width ([Designing layout blocks](designing-layout-blocks.md));
- in CSS, size grids by the space available (`repeat( auto-fill, minmax( 16rem, 1fr ) )`) or with container queries,
  not by the viewport ([Breakpoints and container widths](../designers/theme-design/breakpoints-and-container-widths.md#container-queries)).

## Logical properties

Write spacing with logical properties (`margin-block`, `padding-inline-start`, `inset-inline-end`) rather than
left/right/top/bottom. They follow the writing direction, so a right-to-left language gets a mirrored layout with no
second stylesheet ([Logical CSS properties](../designers/theme-design/logical-css-properties.md)). Mark in the mockup
which spacing is direction-dependent (an icon before a label) and which is not.

## The scale as tokens

The project's starter token set defines the scale as `--site-space-1` to `--site-space-5`
([Design tokens](../designers/theme-design/design-tokens.md#a-starter-set)). Use the same names in your design tool,
so a value read from a mockup is already the name the developer types:

```css
.mytheme-stack       { display: flex; flex-direction: column; gap: var(--site-space-3); }
.mytheme-section + .mytheme-section { margin-block-start: var(--site-space-5); }
.mytheme-card        { padding: var(--site-space-4); }
```

Spacing tokens have the same value in light and dark, and are defined once.

## What to hand over

- The spacing scale with names and values.
- The inside and between rules, in two sentences.
- The page layouts by intent, with the width at which each changes.
- Each block view type at full and sidebar width.
- A note on direction-dependent spacing for right-to-left languages.

## Related pages

- [Responsive design](../designers/theme-design/responsive-design.md)
- [Layout types and zones](../designers/theme-design/layout-types-and-zones.md)
- [Component inventory](component-inventory.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
