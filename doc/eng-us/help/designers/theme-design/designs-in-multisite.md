---
title: Designs in a multisite installation
audience: designer, admin
category: theme-design
order: 180
summary: Patterns for several sites in one installation: one shared design with a small brand design per site, one design per site, per-language siteaccesses that share a design, and site extensions that carry their own settings.
keywords: multisite, multi-site, several sites, brand design, shared design, per site design, site extension, language siteaccess, ExtensionSettingsSiteAccess, settings override in extension, design per brand
type: help
---
# Designs in a multisite installation

This page is for designers who style several sites that run from one installation: brands of one company, a site per
country, a campaign site next to the main one. It answers: how to share what is common, keep apart what differs, and
avoid copying templates.

## Contents

1. [Three patterns](#three-patterns)
2. [Pattern A: shared design plus a brand layer](#pattern-a-shared-design-plus-a-brand-layer)
3. [Pattern B: one design per site](#pattern-b-one-design-per-site)
4. [Pattern C: language siteaccesses of one site](#pattern-c-language-siteaccesses-of-one-site)
5. [Sites that live in their own extension](#sites-that-live-in-their-own-extension)
6. [Pitfalls](#pitfalls)

## Three patterns

| Pattern | When | Cost |
|---|---|---|
| A: shared design + brand layer | the sites share structure and differ in color, logo, fonts | lowest: one set of templates |
| B: one design per site | the sites look and behave differently | one set of templates per site |
| C: language siteaccesses of one site | the same site in several languages | none: settings only |

## Pattern A: shared design plus a brand layer

Put every template in a shared design, say `corporate`. Give each site a tiny design of its own that holds only what
differs: a stylesheet with the brand's tokens, the logo image, maybe the footer template.

```
extension/corporate_theme/design/corporate/      templates, base CSS, scripts
extension/corporate_theme/design/brand_north/    stylesheets/brand.css, images/logo.svg
extension/corporate_theme/design/brand_south/    stylesheets/brand.css, images/logo.svg
```

```ini
# settings/siteaccess/north/site.ini.append.php
[DesignSettings]
SiteDesign=brand_north
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=corporate
AdditionalSiteDesignList[]=standard
```

The shared pagelayout loads `brand.css` after its base stylesheet; the cascade finds the right `brand.css` for each
site, because each brand design is searched first. With design tokens the brand file can be a dozen lines
([Design tokens](design-tokens.md)):

```css
/* brand_north/stylesheets/brand.css */
:root {
  --site-accent: #0b5cad;
  --site-font: "Source Sans 3", system-ui, sans-serif;
}
```

## Pattern B: one design per site

Each site gets a full design, possibly in its own extension. Shared parts (a cookie banner, a search form) can still
live in a common design listed in every site's `AdditionalSiteDesignList[]`.

## Pattern C: language siteaccesses of one site

`site_eng` and `site_ger` show the same site in two languages. They use the same `SiteDesign`; only
`[RegionalSettings]` differs. To avoid copying the siteaccess settings of the theme, the second siteaccess names the
first in `site.ini [SiteAccessSettings] ExtensionSettingsSiteAccess` (the `starter_ger` siteaccess of the
demonstration installation does this with `cjw_starter`). See
[Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md).

## Sites that live in their own extension

Since January 2026 an extension that holds a whole site can carry settings that apply to all of its siteaccesses, in
`extension/<site extension>/settings/override/`, and to a group of them in `settings/override__<group>/`; these are
read only while one of the extension's siteaccesses runs ([Per-site settings inside extensions](../../../features/6.0/multi-site-ini-overrides.md)).
A site's design lines can live there:

```ini
# extension/site_example/settings/override/site.ini.append.php
<?php /*
[DesignSettings]
SiteDesign=example
*/ ?>
```

## Pitfalls

- **A `standard` design folder in an extension** is searched by every siteaccess whose chain ends in `standard`,
  including other brands and the administration. Keep brand templates in brand-named designs.
- **Caches are per siteaccess.** A change to one brand's template needs its caches cleared; list related siteaccesses
  in `RelatedSiteAccessList[]` so content changes clear the view cache of all of them.
- **Images and links in shared templates.** Use `ezimage`, `ezdesign` and `ezurl`, never paths with a design or
  siteaccess name in them, so the same template works for every site.
- **Overrides by node id** differ per site tree. Prefer class identifiers, sections or remote ids
  ([Override match keys](../templates/override-match-keys.md)).

## Related pages

- [Designs and siteaccesses](designs-and-siteaccesses.md)
- [Tutorial: two sites, two designs](tutorial-two-sites-two-designs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
