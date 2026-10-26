---
title: The product family for reporters
audience: press
category: press-media
order: 13
summary: Every product of the Exponential family in one line a reporter can print - Exponential 6, Basic, the four Exponential Platform distributions, Velocity, Exponential Intelligence, Exponential Layouts, AdminUI, the help and the extensions - with its license, its state on 18 October 2026, and the mix-ups to avoid.
keywords: product family, products, Exponential 6, Exponential Basic, Exponential Platform, Legacy, Nexus, DXP, Velocity, Exponential Intelligence, Exponential Layouts, AdminUI, extensions, which product
type: help
---
# The product family for reporters

This page is for journalists who need to name the right product. "Exponential" usually means the kernel,
Exponential 6, but the family has more members, and several stories have confused them. The full descriptions are on
[The products one by one](../who-is-the-exponential-foundation.md#the-products-one-by-one).

## Contents

1. [The family in one table](#the-family-in-one-table)
2. [The two lines: PHP and Symfony](#the-two-lines-php-and-symfony)
3. [State on 18 October 2026](#state-on-18-october-2026)
4. [Mix-ups to avoid](#mix-ups-to-avoid)
5. [Writing the names](#writing-the-names)

## The family in one table

| Product | One line you can print | License |
|---|---|---|
| **Exponential 6** | The PHP content management system, framework and shop; what "Exponential" usually means | GPL v2 or later |
| **Exponential Basic** | The small, simple CMS of the 2.x line, "for everyone, educational purposes, personal use" | GPL v2 or later |
| **Exponential Platform 3.2.9** | A Symfony-only CMS based on the last free versions of eZ Platform | per its package |
| **Exponential Platform Legacy** | The Symfony platform with Exponential 6 included, a "dual kernel" | per its package |
| **Exponential Platform Nexus** | Platform, Exponential 6, Netgen Layouts and Media Site with demo content | per its package |
| **Exponential Platform DXP** | A free, open source Symfony digital experience platform, continuing the eZ Platform and Ibexa OSS line | "GPL-2.0-or-later or proprietary" (its composer.json) |
| **Exponential Velocity** | A web and application server written in PHP that keeps the application loaded between requests | MIT |
| **Exponential Intelligence** | A writing assistant in the administration that writes drafts and never publishes on its own | (package `se7enxweb/expai`) |
| **Exponential Layouts** | A visual page builder: layouts, zones, blocks and rules | (packages `explayouts*`) |
| **AdminUI** | An alternative administration, a port of the Netgen AdminUI | (package `exp_adminui`) |
| **The help system** | The documentation inside the administration | GFDL 1.3 for the pages |
| **The extensions** | 66 se7enxweb packages required by the kernel on 18 October 2026, 69 on 25 October 2026 | each its own |

Sources: [The product family at a glance](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance),
[Open source licenses](../who-is-7x.md#open-source-licenses). Where a cell names a package instead of a license, check
the package's own `composer.json` before you print a license.

## The two lines: PHP and Symfony

```text
PHP line (Exponential 6)                    Symfony line (Exponential Platform)
  Exponential 6  <- the kernel               Platform 3.2.9   (Symfony only)
  + Velocity, Layouts, Intelligence,         DXP              (Symfony only)
    AdminUI, help, extensions                Legacy           (Symfony + Exponential 6)
  Exponential Basic (2.x, small)             Nexus            (Symfony + Exponential 6 + layouts + demo)
```

"Legacy" and "Nexus" run Exponential 6 beside Symfony; "Platform 3.2.9" and "DXP" do not
([The platform line](../who-is-7x.md#the-platform-line-exponential-platform-legacy-nexus-and-dxp)). One exception:
the Nexus 1.3.0.x line for Platform v5, and the v5 starter, have no legacy kernel
([Nexus starter](../../features/6.0/platform-nexus-starter.md)).

## State on 18 October 2026

| Product | State | Source |
|---|---|---|
| Exponential 6 | 6.0.14 released 7 June 2026; 6.0.15 in development | [release overview](../../release-overview) |
| Exponential Basic | v2.4.0.3 | repository description |
| Velocity | Arrived in Exponential on 22 September 2026 as the recommended engine; part of Exponential, required by `composer.json` at `~0.0.4.54`, as of 6.0.15 (20 October 2026) | [Velocity chronicle](../../history/velocity/README.md), [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#20-october-2026-exponential-velocity-is-part-of-exponential) |
| Intelligence, Layouts, AdminUI | Required by the kernel since the 6.0.15 line | composer.json |
| Platform DXP | "Stable; Open Source" | its composer.json |
| Nexus starter | "Currently In Public Beta" | its repository description |

## Mix-ups to avoid

| Mix-up | Correct |
|---|---|
| "Exponential Platform DXP includes the page builder." | DXP does not ship a layouts editor; Nexus does. |
| "Legacy means the old version." | Legacy is Exponential 6 running beside a Symfony platform; Exponential 6 itself is the maintained line. |
| "Velocity is Qbix." | Velocity began as a fork of the Qbix web server engine; the product is Exponential Velocity. |
| "The AI publishes articles." | Exponential Intelligence writes drafts only. |
| "Exponential is a Symfony CMS." | Exponential 6 is a PHP framework of its own; the Symfony line is Exponential Platform. |

## Writing the names

Exact spellings and short forms: [Naming quick reference for news editors](naming-quick-reference-for-editors.md).

## Related pages

- [Knowing the product family](../advocates/knowing-the-product-family.md)
- [Feature stories: index](feature-stories-index.md)
- [Story: Exponential Platform DXP](story-exponential-platform-dxp.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
