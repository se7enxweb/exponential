---
title: Exponential Platform DXP in the ecosystem
audience: ecosystem, developer, partner, businesses
category: ecosystem
order: 18
summary: Exponential Platform DXP's place in the ecosystem - the Symfony-only Digital Experience Platform continuing the eZ Platform and Ibexa OSS line - what its meta package ships and what it does not (no layouts editor, workflows, scheduler or legacy kernel), what it adds, and how to start.
keywords: Exponential Platform DXP, DXP, Symfony, Ibexa OSS, eZ Platform, REST, GraphQL, SQLite, meta package, skeleton, no legacy kernel
type: help
---
# Exponential Platform DXP in the ecosystem

**"Exponential Platform DXP is a free, open-source Digital Experience Platform built on the Symfony framework, the
community continuation of the eZ Platform and Ibexa OSS line maintained by 7x"** (`README.md`).

## Contents

1. [What it ships](#what-it-ships)
2. [What it does not ship](#what-it-does-not-ship)
3. [What it adds](#what-it-adds)
4. [Who it is for](#who-it-is-for)
5. [How to start](#how-to-start)

## What it ships

Read from the requirements of the meta package `se7enxweb/exponential-platform-dxp`: "the admin UI, content forms,
REST, GraphQL and the GraphiQL console, HTTP cache, core and Solr search, the rich text, matrix and query field types,
notifications, messenger, cron, the standard design, Twig components, JWT authentication and
`se7enxweb/layouts-core`" ([Exponential Platform DXP](../../who-is-the-exponential-foundation.md#exponential-platform-dxp)).
The meta package describes itself as "Stable; Open Source", with the license "GPL-2.0-or-later or proprietary".

## What it does not ship

From the same source: "a layouts editor (no layouts UI or standard blocks), an approval workflow, a scheduler, or the
legacy kernel. For Exponential 6 beside Symfony, choose Legacy or Nexus." Do not describe the DXP with features of the
other distributions.

## What it adds

What the README says it adds over Ibexa DXP OSS:

- "The SQLite database driver and framework support";
- current PHP ("PHP 8.3 and later");
- "A path from Exponential 6" through the dual-kernel distributions;
- "A name of its own".

## Who it is for

"Organizations with a lot of structured content", "Teams leaving eZ Platform or Ibexa OSS", Exponential users "who
want a modern Symfony stack", and "Developers and agencies who prefer standard Symfony bundles, Composer and Twig"
(`README.md`, "Who it is for").

## How to start

- [DXP skeleton](../../../features/6.0/platform-dxp-skeleton.md): `composer create-project` and a documented install path.
- [SQLite for the platform](../../../features/6.0/platform-sqlite-install.md).
- Downloads: <https://platform.exponential.earth>.

## Related pages

- [Products](README.md)
- [The Platform line](the-platform-line.md)
- [Choosing a product](choosing-a-product.md)
- [Check a distribution before describing it](../ecosystem-checklists.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
