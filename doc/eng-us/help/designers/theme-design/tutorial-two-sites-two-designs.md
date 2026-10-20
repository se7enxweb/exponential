---
title: "Tutorial: two sites, two designs"
audience: designer, admin
category: theme-design
order: 840
summary: Step by step, run two branded sites from one installation that share their templates: a shared design, a small brand design per site, the siteaccess design chains, brand tokens and logos, and the checks that keep the brands apart.
keywords: tutorial, two sites, multisite, brand design, shared templates, siteaccess design chain, brand tokens, two brands, sub brand, design per site
type: help
---
# Tutorial: two sites, two designs

This tutorial is for designers who style two brands that share one installation and the same page structure, for example
"North" and "South". You will build pattern A of [Designs in a multisite installation](designs-in-multisite.md): one
shared design and a small brand layer per site. Allow two hours.

## Contents

1. [Step 1: the shared design](#step-1-the-shared-design)
2. [Step 2: two brand designs](#step-2-two-brand-designs)
3. [Step 3: the design chains](#step-3-the-design-chains)
4. [Step 4: load the brand stylesheet](#step-4-load-the-brand-stylesheet)
5. [Step 5: check both](#step-5-check-both)

## Step 1: the shared design

Your theme extension has the design `corporate` with all templates, the base stylesheet and the scripts, built on tokens
([Design tokens](design-tokens.md)).

## Step 2: two brand designs

In the same extension:

```
extension/corporate_theme/design/brand_north/stylesheets/brand.css
extension/corporate_theme/design/brand_north/images/logo.svg
extension/corporate_theme/design/brand_south/stylesheets/brand.css
extension/corporate_theme/design/brand_south/images/logo.svg
```

`brand_north/stylesheets/brand.css`:

```css
:root {
  --site-accent: #0b5cad;
  --site-font: "Source Sans 3", system-ui, sans-serif;
}
:root[data-theme="dark"] { --site-accent: #7fb3f0; }
```

`brand_south/stylesheets/brand.css` has its own values for the same tokens, and no other rules.

## Step 3: the design chains

```ini
# settings/siteaccess/north/site.ini.append.php
[DesignSettings]
SiteDesign=brand_north
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=corporate
AdditionalSiteDesignList[]=standard
```

and the same for `south` with `brand_south`. The extension is registered once with `DesignExtensions[]`; all three
folders under its `design/` become design bases.

## Step 4: load the brand stylesheet

In the shared pagelayout of `corporate`:

```tpl
{ezcss_load( array( 'tokens.css', 'corporate.css', 'brand.css' ) )}
...
<a class="site-logo" href={'/'|ezurl}><img src={'logo.svg'|ezimage} alt="{$site.title|wash}" width="160" height="40"></a>
```

`brand.css` and `logo.svg` are found in the brand design first, because it is first in each chain. If a brand has no
`brand.css`, the cascade finds none and the packer skips it with a warning; give `corporate` a default `brand.css` so a
third site starts from sensible values.

## Step 5: check both

- [ ] Each site shows its own colors and logo, in light and dark.
- [ ] A template change in `corporate` shows on both.
- [ ] Caches: both siteaccesses are in each other's `RelatedSiteAccessList[]`, so publishing clears both.
- [ ] Brand-specific templates (if any) live only in the brand design, never in `corporate` with an `if` on the
      siteaccess name.

## Related pages

- [Designs in a multisite installation](designs-in-multisite.md)
- [The design cascade](the-design-cascade.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
