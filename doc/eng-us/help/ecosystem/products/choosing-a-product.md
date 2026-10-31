---
title: Choosing a product from the ecosystem
audience: ecosystem, project-manager, developer, businesses, partner
category: ecosystem
order: 19
summary: Which product of the Exponential ecosystem fits which need - Exponential 6 with SQLite, Velocity, Layouts, Intelligence, the Platform DXP, Nexus, Legacy or Exponential Basic - with the questions to ask and the page to read for each choice.
keywords: choosing, which product, decision, Exponential 6, DXP, Nexus, Legacy, Basic, Velocity, Layouts, Intelligence, Symfony, legacy kernel
type: help
---
# Choosing a product from the ecosystem

## Contents

1. [The short table](#the-short-table)
2. [Questions that decide](#questions-that-decide)
3. [Combinations](#combinations)

## The short table

| You want | Choose | Read |
|---|---|---|
| A CMS with a shop, multisite and many languages, on PHP only, no database server needed | Exponential 6 with SQLite | [Getting started](../../getting-started.md), [Database support](../../database-support.md) |
| The fastest way to serve it | Exponential Velocity | [Exponential Velocity](exponential-velocity.md) |
| Editors who build pages themselves | Exponential Layouts | [The layouts editor](../../layouts-editor.md) |
| Help writing and translating content | Exponential Intelligence | [Exponential Intelligence](exponential-intelligence.md) |
| A Symfony project with REST and GraphQL, no legacy kernel | Exponential Platform DXP | [Exponential Platform DXP](platform-dxp.md) |
| A complete demo site with layouts on the Symfony platform | Exponential Platform Nexus | [The Platform line](the-platform-line.md) |
| To keep an Exponential 6 site and add Symfony next to it | Exponential Platform Legacy | [Legacy bridge](../../../features/6.0/legacy-bridge.md) |
| A small, simple CMS to learn with | Exponential Basic | [Exponential Basic](exponential-basic.md) |

Source: [Which product is for you](../../who-is-the-exponential-foundation.md#which-product-is-for-you).

## Questions that decide

| Question | If yes |
|---|---|
| Do you have an existing Exponential 6 site? | Stay on Exponential 6; add Symfony with Legacy or Nexus only if you need it |
| Does your team work in Symfony and Twig already? | Look at DXP or Nexus |
| Do you need the built-in shop, workflows or the classic template language? | Exponential 6 (the DXP ships no workflow or scheduler) |
| Do you need a visual page builder? | Exponential 6 with Layouts, or Nexus; not the DXP alone |
| Do you want no database server at all? | Exponential 6 on SQLite; the DXP and platform also support SQLite |
| Is it for learning or a tiny site? | Exponential Basic or Exponential 6 on SQLite |

Check every claim against the distribution's own `composer.json` before you promise it
([Ecosystem checklists](../ecosystem-checklists.md)).

## Combinations

- Exponential 6 + Velocity + Layouts + Intelligence: the default 6.0.15 installation, since all are required in
  `composer.json` (Intelligence sends nothing until a provider key is added).
- Exponential 6 + Symfony: Legacy or Nexus.
- Nexus + Exponential 6 + Exponential Layouts: what Nexus ships.

## Related pages

- [Products](README.md)
- [Who Exponential is for](../../business/who-exponential-is-for.md)
- [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)
- [Adopters: start here](../../adopters/adopters-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
