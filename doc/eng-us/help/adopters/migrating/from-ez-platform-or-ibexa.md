---
title: From eZ Platform or Ibexa
audience: adopter, developer, hosting, project-manager
category: adoption
order: 42
summary: How an adopter running eZ Platform or Ibexa DXP/OSS moves into the Exponential family - the two destinations (Exponential Platform Nexus as a like-for-like package swap, or the Exponential 6 kernel as a data and template migration), the questions that decide, and where the details are.
keywords: eZ Platform, Ibexa, Ibexa OSS, migration, Nexus, Platform Legacy, Exponential 6, package swap, Symfony, Twig, chapter 16
type: help
---
# From eZ Platform or Ibexa

## Contents

1. [Two destinations](#two-destinations)
2. [Questions that decide](#questions-that-decide)
3. [Where the details are](#where-the-details-are)

## Two destinations

| | Path A: Exponential Platform Nexus | Path B: Exponential 6 (alone or as Platform Legacy) |
|---|---|---|
| What it is | "the like-for-like path: the same Symfony stack, the same database schema generation and the same PHP namespaces, with the packages swapped for the se7enxweb forks" | The legacy kernel |
| Effort | "the lowest risk: same generation, a package swap" | "the highest effort: a data and template migration" |
| Custom Symfony bundles and Twig | Kept | Rewritten as legacy extensions and TPL templates |

Source: [Migrating customers from eZ Platform and Ibexa](../../hosting/migrations/from-ez-platform-and-ibexa.md),
quoting chapter 16 of the installation book. Rules of thumb from the same chapter: same generation first; path B is "a
rewrite of the presentation and of custom code, not of the content"; do not mix the paths in one cut-over.

## Questions that decide

- Do you have custom Symfony bundles, Twig templates, REST or GraphQL clients you want to keep? Then path A.
- Do you want the Exponential 6 features: its template language, workflows, the built-in shop, Exponential Layouts?
  Then path B, or Legacy to run both.
- How much custom presentation code is there? Path B rewrites it.

See also [The Platform line](../../ecosystem/products/the-platform-line.md) and
[Exponential Platform DXP](../../ecosystem/products/platform-dxp.md) for what each Symfony distribution includes.

## Where the details are

- [Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md), chapter 16 of
  the installation book.
- The hosting view: [Migrating customers from eZ Platform and Ibexa](../../hosting/migrations/from-ez-platform-and-ibexa.md).
- Which fork replaces which package: [Package map](../../../specifications/6.0/platform-package-map.md).

## Related pages

- [Migrating](README.md)
- [Choosing a product](../../ecosystem/products/choosing-a-product.md)
- [Legacy bridge](../../../features/6.0/legacy-bridge.md)
- [Content migration checklist](content-migration-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
