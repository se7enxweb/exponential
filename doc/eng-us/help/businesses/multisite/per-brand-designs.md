---
title: A design per brand
audience: businesses, sales-marketing, website-creator
category: business-solutions
order: 40
summary: How each brand on one Exponential installation keeps its own look - a design per siteaccess, the design fallback chain, adapting a shipped theme safely, per-brand names, logos and meta tags, and what to budget.
keywords: design, theme, brand, per-brand design, SiteDesign, AdditionalSiteDesignList, fallback chain, logo, favicon, SiteName, sevenx_themes_simple, business
type: help
---
# A design per brand

This page is for brand and marketing leads, and for the managers who budget the design work, when several brands run
on one Exponential installation. It explains how each brand gets its own look while the brands share the code
underneath, which parts a brand can change without a developer, and how to keep designs maintainable over years of
updates.

## Contents

1. [How a brand gets its own look](#how-a-brand-gets-its-own-look)
2. [The fallback chain in business terms](#the-fallback-chain-in-business-terms)
3. [Three ways to build a brand's design](#three-ways-to-build-a-brands-design)
4. [What editors can change without a developer](#what-editors-can-change-without-a-developer)
5. [Keeping designs safe through updates](#keeping-designs-safe-through-updates)
6. [Brand design checklist](#brand-design-checklist)

## How a brand gets its own look

Each siteaccess names its design in `site.ini [DesignSettings]`: a `SiteDesign` that is searched first, then the
designs in `AdditionalSiteDesignList[]`, then the `standard` design
([Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md#pointing-a-siteaccess-at-a-design)).
Two brands are therefore simply two siteaccesses with different design names. They can show the same content
completely differently, or each show their own folder.

## The fallback chain in business terms

For each template a page needs, Exponential uses the first match in a fixed order: override templates for the page,
the brand's own design, each additional design, and finally the standard design
([Designs, siteaccesses and the design fallback chain](../../creators/website-creators/designs-and-the-fallback-chain.md#the-chain)).

What that means for your budget:

| Situation | Effect |
|---|---|
| Brand B differs from brand A only in colors, logo and a few templates | Brand B's design holds only those files; everything else falls back to brand A's or the shared theme |
| Every brand is visually distinct | Each brand has its own pagelayout and templates; shared parts can still live in one common design listed behind them |
| A new brand is a variation of an existing one | Start with the existing design as the fallback and override only what differs |

Smaller per-brand designs mean less code to test on every update.

## Three ways to build a brand's design

From [Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md#three-ways):

| Way | Work | Upgrades | Choose when |
|---|---|---|---|
| Use a shipped theme as it is | least: settings only | you get the theme's updates with Composer | the theme's look and classes fit |
| Adapt a theme in your own extension | some: overrides of the templates you change | the theme updates underneath; your overrides stay | you want the theme's structure with your brand |
| Build your own design | most: every template you need | only your own code | the look is specific, or you build on the layouts editor |

The default public design, `sevenx_themes_simple`, is described as "the light, responsive public design of the
default installation" ([sevenx_themes_simple](../../../features/6.0/extensions/sevenx_themes_simple.md)). The same
page lists the other shipped themes.

> **Tip**
> If brands differ mainly in page composition, look at Exponential Layouts: editors compose pages from blocks, and
> shared layouts carry common parts such as the footer
> ([Landing pages with the layouts editor](../../creators/website-creators/landing-pages-with-layouts.md)).

## What editors can change without a developer

In the ezwebin-based designs, the shipped simple theme included:

- **The logo** comes from a page design object; editors change it by editing that object in the administration,
  including its alternative text
  ([Branding: site name, logo, favicon and meta tags](../../creators/website-creators/branding-your-site.md#the-logo-and-the-page-design-object)).
- **The "Powered by Exponential" notice** in the footer is hidden by an attribute of the same object, with no template
  change.

What needs a developer or administrator, per brand siteaccess:

- `SiteName`, `SiteURL` and `SiteDescription` in `site.ini [SiteSettings]`, used for titles, mail subjects, feeds,
  sitemaps and the description meta tag.
- Favicons and the web manifest, which live in the design's `images/favicon/` directory.
- Stylesheets, page templates and the pagelayout.

## Keeping designs safe through updates

- **Never edit a Composer-installed theme in place**: it is replaced on every update. Put changes in your own design
  extension listed before the theme
  ([Adapting a theme the safe way](../../creators/website-creators/choosing-a-design-or-theme.md#adapting-a-theme-the-safe-way)).
- **Keep each brand's design in version control**, in an extension of its own. A brand that lives in its own extension
  can also carry its own settings
  ([Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)).
- **Check designs for right to left and accessibility** if a brand serves such markets
  ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md),
  [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).
- **Test every brand after an update**, not only the main one. One installation means one update for all brands.

## Brand design checklist

- [ ] Each brand's siteaccess names its design, with a shared fallback where brands overlap.
- [ ] Site name, address and description are set per brand.
- [ ] Logo, favicon and social sharing image exist per brand.
- [ ] No theme file was edited in place; changes are in your own design extension.
- [ ] Each brand's design is in version control with a named owner.
- [ ] Every brand was checked on a phone and a desktop ([Responsive sites](../mobile-and-headless/responsive-sites.md)).
- [ ] Every brand passes your accessibility review.

## Related pages

- [Sharing content across brands](shared-content-across-brands.md)
- [Designs, siteaccesses and the design fallback chain](../../creators/website-creators/designs-and-the-fallback-chain.md)
- [Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md)
- [Branding: site name, logo, favicon and meta tags](../../creators/website-creators/branding-your-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
