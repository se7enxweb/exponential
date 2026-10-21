---
title: Choosing DXP, Legacy, Nexus or Exponential 6
audience: dxp-developer
category: dxp-development
order: 4
summary: A decision guide for developers - which product of the Exponential family fits a project, with the questions to ask, a comparison table read from each distribution's packages, and the traps (Nexus v5 has no legacy kernel, the DXP has no layouts editor).
keywords: choose, comparison, DXP, Legacy, Nexus, Exponential 6, dual kernel, legacy bridge, Symfony, layouts, which product, decision
type: help
---
# Choosing DXP, Legacy, Nexus or Exponential 6

This page is for developers and technical leads who must pick a product before a project starts. It compares the
four candidates on facts read from their packages and documentation, and gives the questions that decide.

## Contents

1. [Three questions first](#three-questions-first)
2. [The comparison](#the-comparison)
3. [Decisions by situation](#decisions-by-situation)
4. [Traps](#traps)
5. [Changing your mind later](#changing-your-mind-later)

## Three questions first

1. **Symfony or PHP only?** If the team wants Symfony controllers, Twig and Doctrine, the platform line fits. If the
   team wants a CMS kernel with no framework underneath, Exponential 6 fits.
2. **Is there an Exponential 6 site to keep?** Then the dual-kernel distributions (Legacy, the earlier Nexus lines)
   keep it running beside Symfony.
3. **Do editors need to arrange pages with blocks?** The DXP ships only the Layouts core, not an editor. Nexus ships
   the Netgen Layouts editor; Exponential 6 has Exponential Layouts.

## The comparison

| | Exponential Platform DXP | Exponential Platform Legacy | Exponential Platform Nexus | Exponential 6 |
|---|---|---|---|---|
| Kernel | Platform v5 | Platform plus the Exponential 6 kernel | Platform; the 1.0 to 1.2 and 2.5 lines also the Exponential 6 kernel | Exponential 6 |
| Framework | Symfony 7.4 | Symfony (per line) | Symfony (per line; 7.4 for 1.3 and the v5 starter) | none |
| PHP | 8.3 or newer | per line | 8.4 or newer for 1.3 and the v5 starter | see [Getting started](../getting-started.md) |
| Templates | Twig | Twig and `.tpl` | Twig (and `.tpl` on the dual-kernel lines) | `.tpl` |
| Page builder | Layouts core only, no editor | per line | Netgen Layouts with its editor | Exponential Layouts |
| Demo content | none | per line | yes | site packages |
| Start with | [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md) | [Legacy bridge](../../features/6.0/legacy-bridge.md) | [Nexus starter](../../features/6.0/platform-nexus-starter.md) | [Getting started](../getting-started.md) |

Sources: the metapackage requirements, [Exponential Platform Nexus: a finished site in minutes](../../features/6.0/platform-nexus-starter.md#what-it-is)
(the table of lines), [Legacy bridge](../../features/6.0/legacy-bridge.md#which-version-for-which-platform) and
[Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you).

## Decisions by situation

| Situation | Choose | Why |
|---|---|---|
| New project, Symfony team, content through APIs and Twig | DXP | Clean single kernel, REST and GraphQL included |
| New project, editors must build pages from blocks, Symfony team | Nexus (1.3 or the v5 starter) | Ships the Netgen Layouts editor and a demo site |
| An Exponential 6 site that must keep running while a Symfony front end grows | Legacy | The legacy bridge serves both kernels from one database |
| A site on eZ Platform 3.3 or Ibexa OSS 4.6 to modernize | DXP (v5) or the [4.6.x skeleton](the-v4x-skeleton.md) | Same content model lineage ([Coming from Ibexa OSS or eZ Platform](coming-from-ibexa-oss-or-ez-platform.md)) |
| A CMS with a shop, workflows and multisite on PHP only | Exponential 6 | Those features are in its kernel |
| Learning, a tiny site | Exponential Basic | [Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you) |

## Traps

- **"Nexus means legacy."** Not on v5: Nexus 1.3.0.x and the v5 starter run on Platform v5 with no legacy kernel. Only
  the 1.0, 1.1, 1.2 and 2.5 lines include it ([Nexus starter](../../features/6.0/platform-nexus-starter.md#what-it-is)).
- **"The DXP has a layouts editor."** It does not. `se7enxweb/layouts-core` is the model and rendering core
  ([Layouts core in the DXP](layouts-core-in-dxp.md)).
- **"Legacy means old."** "Legacy" is the Exponential 6 kernel running beside a Symfony platform; Exponential 6 itself
  is the maintained line ([Knowing the product family](../advocates/knowing-the-product-family.md#common-mix-ups)).
- **Version numbers.** Always name a version when you create a Nexus project; without one, Composer can pick an
  upstream tag ([Nexus starter](../../features/6.0/platform-nexus-starter.md#what-it-is)).

## Changing your mind later

- DXP to Nexus: both are Platform v5 projects; adding the Netgen Layouts packages a Nexus project uses is a Composer
  change, but test it on a copy, because Layouts 2.0 needs PHP 8.4
  ([Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md#limits)).
- Exponential 6 to the platform: plan a content migration, or run both kernels with the legacy bridge during the
  move ([Coming from Exponential 6](coming-from-exponential-6.md)).

## Related pages

- [What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md)
- [Knowing the product family](../advocates/knowing-the-product-family.md)
- [Migrating customers from eZ Platform and Ibexa](../hosting/migrations/from-ez-platform-and-ibexa.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
