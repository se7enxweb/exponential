---
title: The Exponential Platform line
audience: ecosystem, developer, partner, hosting
category: ecosystem
order: 17
summary: The Symfony-based Exponential Platform line in the ecosystem - Platform 3.2.9, the dual-kernel Legacy and Nexus distributions with Exponential 6 included, the starters and skeletons, the legacy bridge, and the framework forks that keep them on current PHP.
keywords: Exponential Platform, Platform Legacy, Platform Nexus, dual kernel, Symfony, legacy bridge, Nexus starter, eZ Platform, Netgen, Media Site, forks, distributions
type: help
---
# The Exponential Platform line

"Exponential Platform is the direct continuation of the software stack known as eZ Platform" and "currently comes in
nine different distributions" (`README.md`, "What is Exponential Platform?"). The DXP has a page of its own:
[Exponential Platform DXP](platform-dxp.md).

## Contents

1. [The distributions](#the-distributions)
2. [Dual kernel: Legacy and Nexus](#dual-kernel-legacy-and-nexus)
3. [Starters and skeletons](#starters-and-skeletons)
4. [The forks underneath](#the-forks-underneath)
5. [Where to talk about it](#where-to-talk-about-it)

## The distributions

| Distribution | What the README says |
|---|---|
| Exponential Platform Legacy 5.0.x, 4.6.x, 3.3.x, 2.5.x | "Dual Kernel Symfony CMS which also includes a full copy of Exponential 6" |
| Exponential Platform 3.2.9 | "Symfony only CMS" |
| Exponential Platform Nexus 1.3.0.x | "Platform v5.x Dual Kernel Symfony 7.4+ CMS which also includes a full copy of Exponential 6.x+" |
| Exponential Platform Nexus 1.2.0.x, 1.1.0.x, 1.0.0.x | Platform v4.x, v3.x and v2.5.x dual kernel lines, each with a full copy of Exponential 6 |

## Dual kernel: Legacy and Nexus

- **Legacy**: "Its `composer.json` requires `se7enxweb/exponential` and `se7enxweb/legacy-bridge`, so Exponential 6
  runs next to the Symfony platform" ([Exponential Platform Legacy](../../who-is-the-exponential-foundation.md#exponential-platform-legacy)).
  For sites that "want Symfony beside them and move forward step by step" ([Legacy bridge](../../../features/6.0/legacy-bridge.md)).
- **Nexus**: "Platform v2/v3/v4/v5 + Exponential v6.x (Legacy) + Netgen Media Site + NG Layouts"; it ships the layouts
  editor, with a CJW flavor and a starter ([Exponential Platform Nexus](../../who-is-the-exponential-foundation.md#exponential-platform-nexus)).
  For "Agencies and teams who want a complete website with demo content, layouts and an administration in minutes".

## Starters and skeletons

| You want | Use | Read |
|---|---|---|
| A Symfony 7.4 / PHP 8.4+ site with layouts, REST and GraphQL | `exponential-platform-nexus-starter` or `exponential-platform-dxp-skeleton` | [Nexus starter](../../../features/6.0/platform-nexus-starter.md), [DXP skeleton](../../../features/6.0/platform-dxp-skeleton.md) |
| To keep an Exponential 6 site and add Symfony | `se7enxweb/legacy-bridge` | [Legacy bridge](../../../features/6.0/legacy-bridge.md) |
| A platform database without a server | SQLite install | [SQLite for the platform](../../../features/6.0/platform-sqlite-install.md) |

Source: [The Exponential platform ecosystem](../../../history/ecosystem.md), "How to choose".

## The forks underneath

The line runs on current PHP because 7x maintains forks of the packages it depends on: the content repository
kernels, the admin UIs, field types, search, HTTP cache, GraphQL, the legacy bridge, layouts-core, and the Symfony
3.4, Twig 2.x and Doctrine forks for PHP 8.4 and 8.5. Each has a page in
[The Exponential platform ecosystem](../../../history/ecosystem.md); which fork replaces which upstream package is in
the [package map](../../../specifications/6.0/platform-package-map.md).

## Where to talk about it

- Website: <https://platform.exponential.earth>.
- Forum: <https://share.exponential.earth/forums/exponential-platform> (`README.md`).

## Related pages

- [Products](README.md)
- [Exponential Platform DXP](platform-dxp.md)
- [Migrating from eZ Platform and Ibexa](../../hosting/migrations/from-ez-platform-and-ibexa.md)
- [Upstream projects and forks](../php-stack/upstream-projects-and-forks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
