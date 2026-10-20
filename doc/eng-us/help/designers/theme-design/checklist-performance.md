---
title: "Checklist: performance"
audience: designer
category: theme-design
order: 880
summary: A performance checklist for a design: requests and bundles, render blocking, fonts, images, layout shift, third parties, template fetches, view cache and cache-blocks, measured signed out with cold and warm caches.
keywords: performance checklist, speed checklist, page speed, cls, lcp, requests, bundles, image sizes, fetch limits, cache-block keys, design performance review
type: help
---
# Checklist: performance

This page is for designers who review a design's speed. Measure signed out, on the front page, an article and a list,
once right after clearing caches and once again.

## Contents

1. [Browser side](#browser-side)
2. [Server side](#server-side)
3. [Numbers to record](#numbers-to-record)

## Browser side

- [ ] One stylesheet request from the design (packed) ([The packer](the-packer.md)).
- [ ] At most two script requests; non-critical scripts deferred ([Loading JavaScript](loading-javascript.md)).
- [ ] No render-blocking script in the head except a tiny pre-paint guard.
- [ ] Fonts self-hosted, `font-display: swap`, few weights ([Fonts in a design](fonts-in-a-design.md)).
- [ ] Images in aliases near the displayed size, `srcset` where sizes vary ([Image performance](image-performance.md)).
- [ ] Images below the first screen lazy; the main image not lazy.
- [ ] Width and height on images and embeds; CLS at most 0.05 ([Measuring CLS and LCP](measuring-cls-and-lcp.md)).
- [ ] No third-party request before consent ([External resources and CDNs](external-resources-and-cdns.md)).
- [ ] Page-specific CSS and JS required only by the templates that need them.

## Server side

- [ ] Full views are view-cached; nothing user-specific in them ([Caching for designers](caching-for-designers.md)).
- [ ] Menus and footers in cache-blocks with correct, small keys.
- [ ] Every list fetch has a `limit`; no fetch inside a `foreach` ([Fetch performance](../templates/fetch-performance.md)).
- [ ] The current node fetched once in the pagelayout, reused by the parts.
- [ ] The debug output (on a test siteaccess) shows a reasonable number of database queries for the page.

## Numbers to record

| Page | Requests | Transfer size | LCP | CLS | Queries (cold) |
|---|---|---|---|---|---|
| front page | | | | | |
| article | | | | | |
| list | | | | | |

Record them at launch; compare after every design release.

## Related pages

- [Theme performance](theme-performance.md)
- [Checklist: theme launch](checklist-theme-launch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
