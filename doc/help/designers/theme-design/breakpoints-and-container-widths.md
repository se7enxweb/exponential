---
title: Breakpoints and container widths
audience: designer
category: theme-design
order: 410
summary: The breakpoints and content widths admin4 uses (576, 1024, 1280 px, --a4-content-max) as a worked example, how to choose your own for a public design, container queries, and keeping breakpoints in one place.
keywords: breakpoints, media queries, container width, max-width, content width, 576px, 1024px, 1280px, container queries, mobile first, a4-content-max, layout widths
type: help
---
# Breakpoints and container widths

This page is for designers who decide where a layout changes. It answers: which breakpoints the administration uses
and why, how to pick yours, and how to keep them consistent.

## Contents

1. [admin4 as an example](#admin4-as-an-example)
2. [Choosing breakpoints for a public design](#choosing-breakpoints-for-a-public-design)
3. [Mobile first](#mobile-first)
4. [Container queries](#container-queries)
5. [One place for the values](#one-place-for-the-values)

## admin4 as an example

From the admin styling guide (`doc/bc/6.0/admin4-styling.md`, "Window widths"):

| Width | The admin frame |
|---|---|
| below 576 px | phone: the top menu is a panel behind a menu button, one header row of 56 px |
| from 576 px | two header rows; tabs that do not fit go into "More" |
| from 1024 px | the left column sits beside the content (below: a drawer) |
| from 1280 px | one header row; the right column sits beside the content (below: a drawer) |
| any | the content column stops at `--a4-content-max` (1680 px) |

The admin is tested at 390, 820, 1180, 1440, 1920 and 2560 px (`AdminResponsiveTest`). Note that 960 px, a 1920 px
screen at 200% zoom, falls in the tablet range: zoomed desktops get the tablet layout.

## Choosing breakpoints for a public design

- **Let the content decide.** Narrow the browser until the layout breaks; that width is a breakpoint. Device widths
  change every year; your content does not.
- **Few breakpoints.** Two or three cover most sites: one where a single column becomes two, one where a side column
  appears, one where the content stops growing.
- **A maximum line length.** Running text reads best at about 60 to 80 characters per line; cap text columns with
  `max-width: 70ch` rather than letting them follow the screen.
- **Think in zoom.** A visitor at 200% zoom on a desktop sees your tablet layout. It must be complete, not a cut-down
  version ([Testing at zoom levels](testing-at-zoom-levels.md)).

## Mobile first

Write the narrow layout as the default and add width with `min-width` queries:

```css
.page { display: grid; gap: 1.5rem; }
@media (min-width: 64em) {
  .page { grid-template-columns: minmax(0, 1fr) 20rem; }
}
```

Phones then download and apply the fewest rules, and a failed query leaves a usable single column.

## Container queries

A block that appears in a wide main zone and in a narrow side zone (common with the layouts editor) should adapt to its
container, not the window:

```css
.slb-zone { container-type: inline-size; }
@container (min-width: 40rem) {
  .teaser { display: grid; grid-template-columns: 12rem 1fr; }
}
```

`.slb-zone` is the class the layouts extension's `zone.tpl` puts on each zone.

## One place for the values

CSS custom properties cannot be used inside media queries, so keep breakpoints in one commented block at the top of
your main stylesheet (or in your SCSS variables if you build with Sass, as the media design's `sass/` sources do), and
use the same numbers everywhere.

## Related pages

- [Responsive design](responsive-design.md)
- [Styling layout blocks](styling-layout-blocks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
