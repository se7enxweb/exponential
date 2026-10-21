---
title: "Designing for several brands in multisite"
audience: theme-designer
category: theme-design
order: 25
summary: How theme designers plan one design system for several brands served from one Exponential installation - what varies per brand and what stays shared, a brand matrix, brand tokens, logos, and how the design maps to a shared design plus a small brand design per site.
keywords: theme designer, multisite, multi-brand, brands, brand layer, shared design, brand tokens, white label, siteaccess, design cascade, brand matrix
type: help
---
# Designing for several brands in multisite

This page is for theme designers who design for an installation that serves several sites, each with its own brand:
the sites of a group of companies, regional sites, a product family. Exponential serves each site through its own
siteaccess, and each siteaccess has its own list of designs. The usual technical pattern is a **shared design** with all
templates, plus a **small brand design** per site that holds only what differs; it is explained with settings in
[Designs in a multisite installation](../designers/theme-design/designs-in-multisite.md#pattern-a-shared-design-plus-a-brand-layer)
and [Tutorial: two sites, two designs](../designers/theme-design/tutorial-two-sites-two-designs.md). This page is about
designing for that pattern.

## Contents

1. [Decide what may vary](#decide-what-may-vary)
2. [The brand matrix](#the-brand-matrix)
3. [Brand tokens](#brand-tokens)
4. [Logos and brand images](#logos-and-brand-images)
5. [When templates must differ](#when-templates-must-differ)
6. [Reviewing several brands](#reviewing-several-brands)
7. [Pitfalls](#pitfalls)

## Decide what may vary

The cheaper a brand difference is to build and maintain, the more freely it can vary:

| Difference | Cost | Where it lives |
|---|---|---|
| Colors, accent, fonts, radius | low | the brand design's token stylesheet |
| Logo, favicon, brand images | low | the brand design's images |
| Footer text, contact details | low to medium | content or a small template in the brand design |
| Navigation structure | medium | the content tree of each site |
| Component markup, page layouts | high | separate templates per brand |

Agree with the client, in writing, which rows may differ. "Everything may differ" means several themes, not one theme
with several brands.

## The brand matrix

List the brands in columns and the variable things in rows:

| | Brand North | Brand South | Shared |
|---|---|---|---|
| Accent | blue | green | |
| Font | brand font A | system stack | |
| Radius | | | one radius for all |
| Logo | north.svg, dark version | south.svg, dark version | |
| Header layout | | | shared |
| Footer links | own folder | own folder | template shared |

The matrix becomes the brand designs: one column, one small brand design.

## Brand tokens

The shared design defines every token with a neutral default; each brand design overrides only the tokens in its
column. Because the cascade looks in the brand design first, a brand stylesheet with the same file name replaces the
shared one for that site ([The design cascade](../designers/theme-design/the-design-cascade.md)). Rules:

- brands override **role** tokens (`--site-accent`), never component styles;
- every color a brand overrides gets its dark value in the same brand file;
- each brand's accent passes the contrast targets on its own surfaces, light and dark
  ([Color palettes and ramps](color-palettes-and-ramps.md#checking-before-coding)).

In your design tool, brands are usually a mode or a set of variables on top of the shared ones; keep the same token
names ([From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md)).

## Logos and brand images

Logos have different shapes: a wide wordmark, a square symbol. Design the header slot for the extremes of all brands,
with a fixed height and a maximum width, and ask each brand for light and dark versions of its logo as SVG
([SVG images](../designers/theme-design/svg-images.md)). Favicons and touch icons are per brand too
([Favicons and touch icons](../designers/theme-design/favicons-and-touch-icons.md)).

## When templates must differ

Sometimes one brand needs a different template: a different footer, a special front page. Put that one template in
the brand design, where the cascade finds it first. Keep the number of such templates small and list them in the
brand matrix; each is a copy that must be kept in step with the shared design at every change.

## Reviewing several brands

- Review every key screen in every brand, light and dark; a brand accent that works in light can fail in dark.
- Put the brands side by side on the [style guide page](building-a-style-guide-page.md), one per siteaccess.
- Changes to the shared design are reviewed in all brands before release.

## Pitfalls

- **Brand creep:** small one-off differences accumulate into separate themes. Check new requests against the matrix.
- **Per-brand component tweaks:** a brand file that restyles components instead of overriding tokens is hard to keep
  in step.
- **Shared caches:** the frame of each site is cached per siteaccess; a developer checks that brand content does not
  leak between sites ([Caching for designers](../designers/theme-design/caching-for-designers.md)).

## Related pages

- [Designs in a multisite installation](../designers/theme-design/designs-in-multisite.md)
- [Designs and siteaccesses](../designers/theme-design/designs-and-siteaccesses.md)
- [Customization and white-label work](../designers/sales/customization-and-white-label.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
