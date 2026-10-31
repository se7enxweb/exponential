---
title: The Exponential Platform products
audience: web-framework-developer, developer
category: web-frameworks
order: 420
summary: The Symfony-based side of the Exponential family, for framework developers choosing a stack - Exponential Platform 3.2.9, Legacy, Nexus, DXP and the Nexus starter, which ones include the Exponential 6 kernel, which ship the layouts editor, GraphQL or SQLite, and how to verify a distribution before you promise a feature.
keywords: Exponential Platform, Platform Legacy, Nexus, DXP, Nexus starter, Symfony, Twig, GraphQL, dual kernel, legacy kernel, layouts, SQLite, create-project, skeleton, product family, web framework developer
type: help
---
# The Exponential Platform products

This page is for developers who know Symfony and ask whether there is an Exponential they can build as a normal
Symfony project. There is: next to Exponential 6, 7x maintains the Symfony-based **Exponential Platform** line that
descends from eZ Platform and Ibexa OSS. Some of its distributions include the Exponential 6 kernel, some do not. The
descriptions below come from each distribution's own documentation and `composer.json`, as summarized on
[The platform line](../who-is-7x.md#the-platform-line-exponential-platform-legacy-nexus-and-dxp).

## Contents

1. [The distributions](#the-distributions)
2. [What each one has](#what-each-one-has)
3. [Choosing](#choosing)
4. [Check before you promise](#check-before-you-promise)

## The distributions

| Distribution | What it is | Includes Exponential 6? |
|---|---|---|
| **Exponential Platform 3.2.9** | a CMS on the last free eZ Platform versions, "Symfony / New Stack Only" | No |
| **Exponential Platform Legacy** | the Symfony platform plus the legacy bridge and `se7enxweb/exponential` ("dual kernel") | Yes |
| **Exponential Platform Nexus** | platform, Exponential 6, Netgen Media Site and Exponential Layouts, with demo content | the 1.0.0.x to 1.2.0.x lines and Nexus 2.5 yes; 1.3.0.x (Platform v5) no |
| **Nexus starter** | the Platform v5 Nexus site as its own repository | No |
| **Exponential Platform DXP** | a Symfony-only digital experience platform, the continuation of the Ibexa DXP OSS line | No |

Per-line details: [Nexus starter](../../features/6.0/platform-nexus-starter.md),
[DXP skeleton](../../features/6.0/platform-dxp-skeleton.md),
[Legacy bridge](../../features/6.0/legacy-bridge.md).

## What each one has

| | Platform 3.2.9 | Legacy | Nexus | DXP |
|---|---|---|---|---|
| Symfony, Twig, platform REST | yes | yes | yes | yes |
| GraphQL | in its requirements | check the line's `composer.json` | yes, with the REST API | yes, with GraphiQL |
| Exponential 6 kernel and legacy admin | no | yes | lines up to 1.2.0.x and 2.5 | no |
| Exponential Layouts editor | no | not stated | yes | no (only `se7enxweb/layouts-core`) |
| Demo site | no | no | yes | no (clean skeleton) |

SQLite installs exist for several platform lines ([SQLite for the platform](../../features/6.0/platform-sqlite-install.md));
the Nexus v5 starter uses SQLite as its development default.

The DXP does **not** ship a layouts editor, an approval workflow, a scheduler or the legacy kernel; its requirements
contain none of them ([What Exponential is: the product family](../leadership/what-exponential-is.md#the-product-family)).

## Choosing

| You want | Start from |
|---|---|
| A PHP application with a content model, permissions and an administration, no Symfony | Exponential 6 |
| A new Symfony project with a CMS, REST and GraphQL, no legacy kernel | Exponential Platform DXP ([DXP skeleton](../../features/6.0/platform-dxp-skeleton.md)) |
| A finished demo site with Exponential Layouts on Symfony | Exponential Platform Nexus |
| An Exponential 6 site plus Symfony beside it | Exponential Platform Legacy ([Symfony legacy bridge](symfony-and-the-legacy-bridge.md)) |
| A team on eZ Platform 3 that wants current PHP | Exponential Platform 3.2.9 |

The rest of this audience's pages describe Exponential 6. The platform products follow Symfony conventions, so your
Symfony knowledge applies to them directly ([PHP 8.4 and 8.5 for the older Symfony stack](../../features/6.0/platform-php85-framework-forks.md),
[Platform administration interface](../../features/6.0/platform-admin-ui-fork.md),
[Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md),
[Site bundles](../../features/6.0/platform-site-bundles.md)).

## Check before you promise

- Read the distribution's `composer.json` for the version you will install; that is what you get.
- Name a version on `composer create-project`: some repositories carry upstream tags too, and five-part tags are not
  on Packagist ([Nexus starter](../../features/6.0/platform-nexus-starter.md)).
- The package map lists which fork replaces which upstream package
  ([Platform package map](../../specifications/6.0/platform-package-map.md)).

## Related pages

- [Who is the Exponential Foundation? The product family](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance)
- [When to choose Exponential](when-to-choose-exponential.md)
- [Platform console commands](../../specifications/6.0/platform-console-commands.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
