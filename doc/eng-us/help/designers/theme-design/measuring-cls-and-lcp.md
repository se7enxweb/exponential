---
title: Measuring CLS and LCP
audience: designer
category: theme-design
order: 670
summary: Two numbers that tell how a page feels: Cumulative Layout Shift (target 0.05 or less) and Largest Contentful Paint; what causes bad values in Exponential designs and how to measure them in the browser.
keywords: CLS, cumulative layout shift, LCP, largest contentful paint, Core Web Vitals, layout shift, web vitals, performance measurement, lighthouse, jank
type: help
---
# Measuring CLS and LCP

This page is for designers who check how a page loads. **Cumulative Layout Shift (CLS)** measures how much content jumps
while the page loads; **Largest Contentful Paint (LCP)** measures when the biggest visible element (usually the main image
or heading) is drawn.

## Contents

1. [Targets](#targets)
2. [What causes layout shift](#what-causes-layout-shift)
3. [What delays the largest paint](#what-delays-the-largest-paint)
4. [How to measure](#how-to-measure)

## Targets

- CLS at most **0.05**: the target the project uses for its own pages (stricter than the commonly cited 0.1).
- LCP as early as possible; a common goal is under 2.5 seconds on a mid-range phone.

## What causes layout shift

| Cause | Fix |
|---|---|
| images without width and height | print the alias's `width` and `height`, or set `aspect-ratio` |
| web fonts swapping with very different metrics | a fallback stack close to the web font; few weights |
| banners and consent bars pushed in above content | overlay them (fixed position) or reserve their space |
| embeds (videos, maps) without a box | a placeholder with the final aspect ratio, as the media design's video consent placeholder does |
| a theme set after the first paint | set it in the head before stylesheets ([The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md)) |
| side columns that open after scripts run | draw the remembered state from the first paint (admin4 uses a `a4-preload` class until its column script has run) |

## What delays the largest paint

- the main image loaded lazily or in a size far larger than shown;
- render-blocking scripts in the head;
- many stylesheets or third-party font CSS before any text can be drawn;
- a slow server response: an uncached full view with many fetches ([Fetch performance](../templates/fetch-performance.md)).

## How to measure

1. Open the page in a private window, signed out.
2. Developer tools, Performance or Lighthouse panel: record a page load; read CLS and LCP and the elements behind them.
3. Repeat at 375 px (phone emulation, throttled network) and at 1440 px.
4. Repeat after clearing the caches once: the first visitor after a publish gets the slow path.

## Related pages

- [Theme performance](theme-performance.md)
- [Image performance](image-performance.md)
- [Checklist: performance](checklist-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
