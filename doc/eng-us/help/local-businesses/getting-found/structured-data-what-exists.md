---
title: "Structured data: what exists"
audience: local-business, businesses, customer, website-creator
category: business-solutions
order: 25
summary: What schema.org structured data (JSON-LD) Exponential and its shipped themes print today - breadcrumbs, articles, recipes, the content license - and what a local business would have to add for a LocalBusiness block with address and opening hours, as template work by a builder.
keywords: structured data, schema.org, JSON-LD, LocalBusiness, opening hours specification, breadcrumbs, rich results, xrowmetadata, sevenx_themes_media, local business
type: help
---
# Structured data: what exists

This page is for owners and builders of local business sites who have heard that search engines read **structured
data**: a block of machine-readable facts (in the JSON-LD format, using the schema.org vocabulary) in the page head.
It says plainly what ships and what does not.

## Contents

1. [What structured data is for](#what-structured-data-is-for)
2. [What ships today](#what-ships-today)
3. [What does not ship](#what-does-not-ship)
4. [Adding a LocalBusiness block](#adding-a-localbusiness-block)
5. [Checklist](#checklist)

## What structured data is for

Structured data repeats facts that are already on the page (name, address, opening hours, price) in a form machines
read without guessing. Search engines may use it for richer results. It does not replace a correct page or an accurate
business listing on map platforms ([Business listings outside Exponential](business-listings-outside-exponential.md)).

## What ships today

Searched in `extension/` on 20 October 2026:

| Structured data | Where | Applies to |
|---|---|---|
| `BreadcrumbList` on every page | `extension/sevenx_themes_media/design/media/templates/pagelayout/head/meta.tpl` | sites using the media theme |
| `Article` with publisher `Organization` | the media theme's full views for articles, news, blog posts | those content types |
| `Recipe` | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php` (class `ng_recipe`) | recipe pages of the media theme |
| a job posting with hiring `Organization` | `.../content/views/full/ng_job_position.tpl` | job pages of the media theme |
| schema.org `license` of the content | `xrowmetadata` (`xrowmetadata/license_head.tpl`, version 1.4.9 and later) | sites that set a content license |

So whether your site prints any structured data depends on the **design** it uses. The default public design
(`sevenx_themes_simple`) was not found to print JSON-LD.

## What does not ship

- No `LocalBusiness` (or `Restaurant`, `Store`, `HairSalon` and so on) block.
- No `OpeningHoursSpecification`; there is also no opening-hours datatype to read it from
  ([Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md)).
- No `Event` structured data for the `event` class of `ezwebin`.
- No `Product` or `Offer` structured data for shop products.
- No `AggregateRating` from `ezstarrating`.

Each can be added by a builder in a template. None needs a new extension.

## Adding a LocalBusiness block

This is template work for your builder, usually an hour or two once the content model is clear. The idea:

1. Keep name, address, phone and hours in one content object
   ([Planning your pages](../getting-started/planning-your-pages.md#write-each-fact-once)); with structured hours
   fields, as described in [Opening hours without a special datatype](../maps-and-hours/opening-hours-without-a-special-datatype.md#option-2-a-small-opening-hours-class).
2. In the pagelayout, or only on the front page and contact page, print a JSON-LD block built from those fields. The
   media theme's `meta.tpl` shows how the project prints JSON-LD safely with the `json_ld` operator.
3. Use the coordinates of an `ezgmaplocation` field for `geo`, if the site has one
   ([Showing your location on a map](../maps-and-hours/showing-your-location-on-a-map.md)).
4. Test the page with the search engines' own structured data testing tools (outside Exponential).
5. Remember the holiday hours: if structured data says "open" and the door is closed, visitors are annoyed. Either
   include special dates or keep the block to the regular hours and announce exceptions on the page.

## Checklist

- [ ] You know which design your site uses and whether it prints any JSON-LD.
- [ ] Name, address, phone and hours live in one object.
- [ ] If a LocalBusiness block is wanted, it is in the builder's quote.
- [ ] The block is tested and matches the visible page.
- [ ] Holiday hours are handled.

## Related pages

- [Local SEO basics](local-seo-basics.md)
- [Site packages and the themes that ship](../../creators/website-creators/site-packages-and-shipped-themes.md)
- [Choosing a design or theme](../../creators/website-creators/choosing-a-design-or-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
