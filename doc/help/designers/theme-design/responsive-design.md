---
title: Responsive design
audience: designer
category: theme-design
order: 400
summary: Making an Exponential design work from phones to wide screens and at high zoom: the viewport tag, fluid layout, breakpoints, responsive images through aliases, touch targets, and the widths the project tests at.
keywords: responsive, mobile, phone, tablet, viewport, breakpoints, fluid layout, media queries, container queries, touch targets, pointer coarse, zoom, 375px, 960px, 1440px
type: help
---
# Responsive design

This page is for designers who make a design work on every screen. It answers: what the pagelayout needs, how to plan
breakpoints, and which widths and zoom levels to test.

## Contents

1. [The basics](#the-basics)
2. [Layout that flows](#layout-that-flows)
3. [Breakpoints](#breakpoints)
4. [Images](#images)
5. [Touch](#touch)
6. [What to test](#what-to-test)

## The basics

- The viewport tag in the head of the pagelayout (every shipped design has it):

  ```html
  <meta name="viewport" content="width=device-width, initial-scale=1">
  ```

  Never add `maximum-scale=1` or `user-scalable=no`: visitors must be able to zoom.
- One HTML for every device; CSS decides the layout. Exponential has a mobile device detection
  (`$module_result.is_mobile_device`), but templates that send different markup per device make caching and testing
  harder. Prefer CSS.

## Layout that flows

```css
.site-main {
  max-width: 72rem;
  margin-inline: auto;
  padding-inline: clamp(1rem, 3vw, 2rem);
}
.card-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr));
  gap: 1.5rem;
}
```

Grids with `auto-fill`/`minmax`, `flex-wrap`, `clamp()` for sizes and logical properties (`margin-inline`) cover most
layouts with few media queries, and stay correct in right-to-left languages.

## Breakpoints

See [Breakpoints and container widths](breakpoints-and-container-widths.md) for the values the shipped designs use and
how to choose your own. Write them in `rem` or `em` where you can, so they follow the visitor's font size.

## Images

- `max-width: 100%; height: auto;` on content images.
- Several aliases with `srcset` so phones download small files ([Image aliases](image-aliases.md)).
- `width` and `height` attributes so the layout does not jump ([Measuring CLS and LCP](measuring-cls-and-lcp.md)).

## Touch

- Tap targets at least 24 x 24 CSS pixels, better 44 x 44 for primary controls. The media design's
  `exp-mobile-layout.css` collects such fixes.
- `@media (pointer: coarse)` can enlarge controls for touch only; admin4 uses it.
- Do not rely on hover to reveal content: touch screens have no hover.

## What to test

The project checks its pages at these sizes:

| Width | Scale | Stands for |
|---|---|---|
| 1440 px | 1 | a desktop |
| 960 px | 2 | a desktop at 200% browser zoom |
| 375 px | 2 | a phone |

At each: no horizontal scroll of the page, no overlapping text, every control reachable, text readable. See
[Testing at zoom levels](testing-at-zoom-levels.md).

## Related pages

- [Breakpoints and container widths](breakpoints-and-container-widths.md)
- [Accessibility basics for themes](accessibility-basics-for-themes.md)
- [Logical CSS properties](logical-css-properties.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
