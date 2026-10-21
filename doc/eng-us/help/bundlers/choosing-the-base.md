---
title: Choosing the base of a bundle
audience: bundler, partner, developer
category: bundling
order: 30
summary: How to choose what your bundle is built on - Exponential 6 on its own, or a Platform distribution that carries it (Legacy, Nexus) - with the facts that decide it - PHP range, the legacy kernel, the layouts editor, the installer, the database - and a decision table.
keywords: base, choose, Exponential 6, Legacy distribution, Nexus, Platform, DXP, legacy kernel, Symfony, layouts editor, PHP version, decision
type: help
---
# Choosing the base of a bundle

This page is for bundlers deciding what their bundle starts from. Exponential 6 ships in two shapes: on its own
(the package `se7enxweb/exponential`), and inside the Exponential Platform distributions **Legacy** and **Nexus**,
which add a Symfony stack beside it. Each shape has its own installer, PHP range and update path, so the choice
shapes every later page of your bundle.

## Contents

1. [The candidates](#the-candidates)
2. [The facts that decide it](#the-facts-that-decide-it)
3. [Decision table](#decision-table)
4. [A note on Exponential Platform DXP](#a-note-on-exponential-platform-dxp)
5. [Record your choice](#record-your-choice)

## The candidates

| Base | What it is | Source |
|---|---|---|
| **Exponential 6** | The CMS kernel, its required extensions and designs; installed with the setup wizard, the kickstarter or `exp:install` | `composer.json` of `se7enxweb/exponential`; [Exponential 6 as a base](exponential-6-as-a-base.md) |
| **Exponential Platform Legacy** | "Dual Kernel Symfony CMS which also includes a full copy of Exponential 6" (README); lines 2.5.x to 5.0.x | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-legacy) |
| **Exponential Platform Nexus** | A ready-made website project: Platform, Netgen Layouts, demo content; the 1.0.0.x to 1.2.0.x lines and the 2.5 tags carry the legacy kernel, the 1.3.0.x line (Platform v5) does not | [Exponential Platform Nexus](../../features/6.0/platform-nexus-starter.md) |

## The facts that decide it

| Fact | Exponential 6 | Legacy | Nexus |
|---|---|---|---|
| Exponential 6 kernel inside | yes | yes (`se7enxweb/exponential` and `se7enxweb/legacy-bridge`) | 1.0.0.x to 1.2.0.x and 2.5: yes; 1.3.0.x and the v5 starter: no |
| Symfony stack | no | yes | yes |
| Installer | setup wizard, kickstarter, `exp:install` | the distribution's own; see its README | `exponential:install` (types such as `exponential-media`) |
| PHP | 8.0 to 8.5 for 6.0.15 (`composer.json`) | per line, see its README | per line: 1.3.0.x needs PHP 8.4 or newer |
| Databases | SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | per line | the v5 starter: SQLite, MySQL, MariaDB, PostgreSQL |
| Layouts page builder | Exponential Layouts (required since 6.0.15) | per line | Netgen Layouts |
| Demo content | site packages with or without demo content | per line | the Nexus demo (`exponential-media`) |

Nexus facts: [Exponential Platform Nexus](../../features/6.0/platform-nexus-starter.md). Exponential 6 facts:
[Database support](../database-support.md), [Chapter 2: Requirements](../../install/02-requirements.md).

> **Note**
> Always name a version when you install Nexus with Composer: `composer create-project
> se7enxweb/exponential-platform-nexus` without a version picks the upstream `3.1.6`, which "is the upstream
> `netgen/media-site`, not a Nexus release" ([Nexus](../../features/6.0/platform-nexus-starter.md#what-it-is)).

## Decision table

| Your bundle | Base |
|---|---|
| A hosting one-click install, a container, a server image of "the CMS" | Exponential 6 |
| A bundle for shared hosting without a shell | Exponential 6 (the setup wizard needs only a browser) |
| A bundle that must run on PHP 8.0 to 8.3 | Exponential 6 (6.0.15 line), or a Legacy or Nexus line that supports that PHP |
| A bundle for teams that want Symfony and Twig beside Exponential 6 | Legacy |
| A complete demo website with layouts and content, for agencies | Nexus (choose the line by the platform generation you want) |
| A bundle for an existing Exponential site that must keep running | Exponential 6, or Legacy when Symfony is wanted next to it |

## A note on Exponential Platform DXP

Exponential Platform DXP is a separate, Symfony-only product. It has **no legacy kernel**, no layouts editor, no
approval workflow and no scheduler ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-dxp)).
The pages of this section are about bundles that carry Exponential 6; if you bundle DXP, take its facts from its own
repository and do not apply these pages to it unchecked.

## Record your choice

```text
Base:                 Exponential 6 / Legacy <line> / Nexus <line>
Exact version:        <tag>
PHP range we support: <from> to <to>
Database(s):          <list>
Installer we call:    exp:install / kickstarter / exponential:install
Why this base:        <one sentence>
```

Copy it into the [Bundle worksheet](bundle-worksheet.md).

## Related pages

- [Exponential 6 as a base](exponential-6-as-a-base.md)
- [Platform distributions as a base](platform-distributions-as-a-base.md)
- [Versions, tags and pinning](versions-tags-and-pinning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
