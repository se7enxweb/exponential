---
title: "Visual regression checks: light, dark, zoom"
audience: designer
category: theme-lifecycle
order: 160
summary: Catch unintended visual changes between releases - screenshots of the page list at fixed widths and modes before and after, compared side by side or by tool - with the widths the Exponential project uses.
keywords: visual regression, screenshots, compare, light mode, dark mode, zoom, 1440, 960, 375, before after
type: help
---
# Visual regression checks: light, dark, zoom

This page is for designers who want to know what a release changed visually, including what they did not intend.

## The matrix

| Width | Scale | Modes |
|---|---|---|
| 1440 px | 1 | light, dark |
| 960 px | 2 (200% zoom) | light, dark |
| 375 px | 2 | light |

These are the widths the Exponential project uses to check its own pages.

## Step by step

1. On the test installation with the **previous** release, take screenshots of every page of the page list in every
   cell of the matrix. Name them `<page>-<width>-<mode>.png`.
2. Update to the **new** release and take the same set.
3. Compare: side by side, or with an image-diff tool that highlights changed pixels.
4. For every difference, decide: intended (note it in the changelog) or a regression (fix it).
5. Keep the new set as the baseline for the next release, outside the theme's published files if large.

## Typical regressions

- Dark mode: a white card on a dark background, text with too little contrast.
- Zoom: a header that overlaps content, horizontal scrolling.
- Phone: a menu that does not open, images overflowing.

## Related pages

- [Screenshots for a theme](../distribution/screenshots-for-a-theme.md)
- Checklist: dark mode
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
