---
title: "Designing for performance budgets"
audience: theme-designer
category: theme-design
order: 31
summary: The design decisions that decide how fast an Exponential theme is - agreeing a budget before drawing, the first screen, fonts, images, third-party embeds, page weight per template, and how a designer checks a page against the budget.
keywords: theme designer, performance, performance budget, page weight, fonts, images, LCP, CLS, first screen, third parties, embeds, design decisions, speed
type: help
---
# Designing for performance budgets

This page is for theme designers. Most of a page's speed is decided before any code is written: by how many fonts the
design uses, how large the first image is, whether a video plays in the header, and how many third-party widgets the
page carries. The technical levers are in [Theme performance](../designers/theme-design/theme-performance.md) and
[Checklist: performance](../designers/theme-design/checklist-performance.md); this page is about the design choices
behind them.

## Contents

1. [Agree the budget before drawing](#agree-the-budget-before-drawing)
2. [The first screen](#the-first-screen)
3. [Fonts](#fonts)
4. [Images and video](#images-and-video)
5. [Third parties](#third-parties)
6. [Stable layout](#stable-layout)
7. [Per-page extras](#per-page-extras)
8. [Checking a page against the budget](#checking-a-page-against-the-budget)

## Agree the budget before drawing

The designer help proposes a simple budget for a public content page: one stylesheet request and at most two script
requests from the design, no third-party request before consent, the largest first-screen image in an alias near its
displayed size, and layout shift (CLS) at most 0.05 ([Theme performance](../designers/theme-design/theme-performance.md#a-simple-budget)).
Agree this, or a stricter one, with the client at the start, and write it into the brief. A budget agreed after the
design is approved is a fight; a budget agreed before is a constraint you design with.

## The first screen

What appears without scrolling decides how fast the page feels. For each template, write down the largest element of
the first screen (usually the headline or the hero image): that is what the Largest Contentful Paint measures
([Measuring CLS and LCP](../designers/theme-design/measuring-cls-and-lcp.md)).

- A large hero image on every page is the most common cause of a slow first screen. Decide per class whether it is
  needed.
- Text-first designs are fast by default: a strong headline in a system or self-hosted font appears at once.
- Background videos and automatic sliders in the first screen are expensive; offer a still image with a play button.

## Fonts

Each font family and weight is a file. Two families and three or four weights in total is a reasonable ceiling. Fonts
are self-hosted in the design, never loaded from a font service ([Fonts in a design](../designers/theme-design/fonts-in-a-design.md)).
A system font stack costs nothing and can be a good design choice for body text. Choose variable fonts where they
replace several weights.

## Images and video

- Every image slot gets an alias near its displayed size, not the original
  ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)).
- Decorative images are a cost: prefer CSS shapes, gradients from tokens and small SVG files.
- Icons as one small set of SVG files, not an icon font with hundreds of glyphs ([Icons in a design](../designers/theme-design/icons-in-a-design.md)).
- Video: a poster image and a play button; the player loads on request.

## Third parties

Maps, video players, social feeds, chat widgets, analytics and captchas each load scripts and often fonts from other
hosts, which also affects privacy and the Content Security Policy
([External resources and CDNs](../designers/theme-design/external-resources-and-cdns.md)). Design a placeholder for each
embed that loads it only after the visitor asks (a static map image with "Show the map"), and list every third party in
the brief so the client sees the cost.

## Stable layout

Pages that jump while loading feel slow and cause mis-clicks. Designs that avoid it:

- every image and embed has a fixed ratio box in the design;
- banners, cookie notices and alerts do not push content down after it has appeared;
- fonts with similar metrics to their fallback, so text does not reflow when the font arrives.

## Per-page extras

Stylesheets and scripts that only one template needs (a gallery's lightbox, a form's date picker) can be required from
that template rather than loaded on every page ([Theme performance](../designers/theme-design/theme-performance.md#in-the-browser)).
Mark such features in the handoff, so the developer knows they are page-specific.

## Checking a page against the budget

1. Open the page on the development installation in a private window, with the browser's developer tools on the
   network tab and the cache disabled.
2. Count the requests by type and note any request to another host.
3. Run the browser's performance audit for the page at phone size, and note LCP and CLS.
4. Compare with the budget; anything over it goes into the [design QA checklist](design-qa-checklist.md) as an issue.

## Related pages

- [Theme performance](../designers/theme-design/theme-performance.md)
- [Image performance](../designers/theme-design/image-performance.md)
- [A performance audit for every release](../designers/lifecycle/performance-audit-per-release.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
