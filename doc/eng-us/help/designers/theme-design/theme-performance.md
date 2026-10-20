---
title: Theme performance
audience: designer
category: theme-design
order: 650
summary: What makes a design fast or slow: request count and size of CSS and JS, render-blocking files, fonts, images, fetches in templates, cache-blocks and the view cache, with a short budget and how to measure.
keywords: performance, fast theme, page speed, render blocking, critical css, defer scripts, fonts, images, fetch performance, cache-block, Core Web Vitals, LCP, CLS, budget
type: help
---
# Theme performance

This page is for designers who want a design that feels instant. Performance is decided in two places: the browser
(what it has to download and draw) and the server (what templates fetch and render). The page lists the levers on both
sides.

## Contents

1. [In the browser](#in-the-browser)
2. [On the server](#on-the-server)
3. [A simple budget](#a-simple-budget)
4. [Measuring](#measuring)

## In the browser

| Lever | How |
|---|---|
| Fewer requests | one `ezcss_load` and one or two script bundles, packed ([The packer](the-packer.md)) |
| Less render blocking | stylesheets in the head, scripts at the end of the body or with `defer` (the media design splits critical and deferred scripts) |
| Fonts | self-hosted, `font-display: swap`, few weights, `unicode-range` subsets ([Fonts in a design](fonts-in-a-design.md)) |
| Images | aliases at the displayed size, `srcset`, `loading="lazy"` below the fold, width and height set ([Image performance](image-performance.md)) |
| No third parties by default | [External resources and CDNs](external-resources-and-cdns.md) |
| Page-specific CSS and JS only where used | `ezcss_require` / `ezscript_require` from the view template |
| Stable layout | reserved space for images, ads, embeds ([Measuring CLS and LCP](measuring-cls-and-lcp.md)) |

## On the server

| Lever | How |
|---|---|
| The view cache | keep full views cacheable; nothing user-specific in them ([Caching for designers](caching-for-designers.md)) |
| Cache-blocks | wrap menus and footers with the right keys |
| Fewer fetches | fetch once in the pagelayout, pass the result to parts; prefer `content_info` values ([Fetch performance](../templates/fetch-performance.md)) |
| Limits | always a `limit` on list fetches; paginate |
| No fetch in a loop | fetch the children once, not a fetch per item |

## A simple budget

For a public content page, aim for:

- one stylesheet request and at most two script requests from the design;
- no request to a third-party host before consent;
- the largest image of the first screen served in an alias near its displayed size;
- layout shift (CLS) at most 0.05, the target the project uses for its own pages.

## Measuring

- The browser's network panel: count requests, check sizes, look for render-blocking files.
- The browser's performance or Lighthouse panel for Largest Contentful Paint and Cumulative Layout Shift.
- On the server, the debug output (`DebugOutput=enabled` for your address) shows timing and the number of database
  queries of a page; templates with many fetches show up there.
- Measure signed out and with a cold and a warm cache: the first visitor after a publish sees the uncached page.

## Related pages

- [Image performance](image-performance.md)
- [Measuring CLS and LCP](measuring-cls-and-lcp.md)
- [Checklist: performance](checklist-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
