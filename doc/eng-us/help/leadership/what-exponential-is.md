---
title: What Exponential is: the product family
audience: project-manager, sales-marketing
category: leadership
order: 20
summary: Exponential and its product family in plain words: the CMS, CMF and shop kernel, the Platform distributions, Velocity, Intelligence, Layouts, extensions, licenses and the support commitment, all sourced.
keywords: Exponential, product family, overview, CMS, CMF, e-commerce, Exponential Platform, Legacy, Nexus, DXP, Exponential Basic, Velocity, Exponential Intelligence, expai, Exponential Layouts, AdminUI, extensions, license, GPL, support, 7x, Exponential Foundation
type: help
---
# What Exponential is: the product family

This page is for project managers and for sales and marketing colleagues who need to explain Exponential in their
own words: to a sponsor, a customer, a new team member or a steering committee. It answers what the product is, which
products make up the family and who each one is for, what it runs on, under which license, and what the project
commits to. Every statement comes from a file in this repository or from the two history pages of this help, and
names its source.

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [Where it comes from](#where-it-comes-from)
3. [Who stands behind it](#who-stands-behind-it)
4. [What the kernel does](#what-the-kernel-does)
5. [The product family](#the-product-family)
6. [Choosing a product for a project](#choosing-a-product-for-a-project)
7. [What it runs on](#what-it-runs-on)
8. [Languages and accessibility](#languages-and-accessibility)
9. [Licenses](#licenses)
10. [What the project commits to](#what-the-project-commits-to)
11. [Explaining it: three versions](#explaining-it-three-versions)
12. [Words you will hear](#words-you-will-hear)

## In one paragraph

Exponential is "a professional PHP application framework with advanced CMS (content management system)
functionality. As a CMS its most notable feature is its fully customizable and extendable content model"
(README, quoted on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-6-the-kernel)).
It is open source under the GNU General Public License, version 2 or later, it is installed with Composer from
Packagist, and its current line is **Exponential 6.0.x**. A content management system, an application framework
(CMF) and a built-in shop come in one package ([Who is 7x?](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one)).

## Where it comes from

- **Nearly thirty years of one code base.** The copyright notice reads "Copyright (C) 1998 - 2026 7x & Exponential
  Foundation" next to "Copyright (C) 1999 - 2014 eZ Systems AS" (`COPYRIGHT.md`). The oldest commit in the repository
  is from 2 September 2002 ([Who is 7x?](../who-is-7x.md#7x-in-numbers)).
- **A continuation, not a fork for its own sake.** Exponential continues the code base of eZ Publish, a product its
  creator discontinued after almost 18 years. 7x took over leadership of the project in 2023, released 6.0.0 on
  1 January 2024, and renamed the product Exponential in August 2025
  ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#timeline)).
- **Independent.** "This project is not associated with the original eZ Publish software or its original developer,
  eZ Systems or Ibexa" (README, quoted on [Who is 7x?](../who-is-7x.md#in-short)).
- **Every change since December 2023 is told** in [the history](../../history/README.md), and the full Git history is
  kept ([Develop with Git](../develop-with-git.md)).

## Who stands behind it

| | 7x | Exponential Foundation |
|---|---|---|
| What it is | A North American web design, support, development and hosting company | A 501(c)(3) nonprofit corporation founded in Seattle on 19 March 2026 |
| Role | Leads development, support, hosting and services; "the current leader in contributions" | Operates the project; funds and directs it |
| Read | [Who is 7x?](../who-is-7x.md) | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |

Both pages are sourced in detail. The sources do not name the Foundation's board, its governance or its budget;
the Foundation page says so plainly ([What the sources do not say](../who-is-the-exponential-foundation.md#what-the-sources-do-not-say)).

## What the kernel does

The README lists the main features of the kernel: user-defined content classes, version control, multilingual
support, a built-in search engine, role-based permissions, approval and scheduled publication, multisite support,
built-in e-commerce, workflows, a template engine, a headless REST API, a database abstraction layer, clustering,
and a plugin API ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-6-the-kernel)).

What that means for a project, with the page that explains each part:

| Area | What you get | Read |
|---|---|---|
| Content management | Content classes of your own, objects, versions, drafts, approval, sections, states, the content tree | [The content model and editing content](../../guides/content-model-and-editing.md) |
| Permissions | Roles made of policies, limited by section, class, subtree, language or siteaccess | [Roles, policies and unactivated users](../../guides/roles-and-policies.md) |
| Approval | Workflows that hold a version until it is approved | [Workflows](../../guides/workflows.md) |
| Page building | Exponential Layouts: layouts, zones, blocks and rules | [The layouts editor](../layouts-editor.md) |
| Writing assistance | Exponential Intelligence: drafts only, never publishes on its own | [Exponential Intelligence](../../features/6.0/extensions/expai.md) |
| Multisite | Several sites from one installation, each a siteaccess | [Planning multisite and multilingual projects](multisite-and-multilingual-planning.md) |
| Languages | 268 locale files as content languages; 32 administration translations, ten complete | [Languages in Exponential](../../languages.md) |
| E-commerce | Products, baskets, orders, VAT, currencies, PayPal | [Who is 7x?](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one) |
| Integration | REST API v1 and v2 | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |
| Security and audit | Content Security Policy, two-step sign-in, a tamper-evident audit trail | [Security hardening](../security-hardening.md), [Audit trail](../../features/6.0/audit-trail.md) |
| Help | This help: the repository's documentation inside the administration | [The help system](../../features/6.0/help-system.md) |

## The product family

The Foundation names "Exponential, Exponential Platform and Exponential Basic" as the products it stands for. Around
them 7x and the project maintain a web server, extensions and tools. The full descriptions, with sources and the
state of each product, are on
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance). In short:

| Product | What it is | Typical buyer or user |
|---|---|---|
| **Exponential 6** | The CMS, CMF and shop kernel this installation runs | Organizations with structured content, several sites or several languages; developers who build on its framework |
| **Exponential Platform 3.2.9** | Symfony-only CMS from the last free eZ Platform | Teams on the eZ Platform 3 stack who want current PHP |
| **Exponential Platform Legacy** | The Symfony platform plus Exponential 6 ("dual kernel") | Existing sites that keep Exponential 6 and add Symfony beside it |
| **Exponential Platform Nexus** | Platform plus Exponential 6, Netgen Layouts and Media Site, with demo content | Agencies and teams who want a complete site fast |
| **Exponential Platform DXP** | A Symfony-only digital experience platform, the continuation of the Ibexa DXP OSS line | New Symfony projects |
| **Exponential Basic** | The simple CMS of the eZ Publish 2 line | Education, personal use, small sites |
| **Exponential Velocity** | A web and application server written in PHP | Anyone who serves Exponential |
| **Exponential Intelligence** (`expai`) | A writing assistant in the administration | Editors, project managers and content writers |
| **Exponential Layouts** (`explayouts`) | A visual page builder | Editors and designers |
| **AdminUI** (`exp_adminui`) | An alternative administration interface | Editors who prefer it |
| **The extensions** | 66 `se7enxweb` packages required by the kernel | Site builders |

> **Warning**
> Exponential Platform DXP does **not** ship a layouts editor, an approval workflow, a scheduler or the legacy
> kernel; its requirements contain none of them. For Exponential 6 beside Symfony, the sources point to Legacy or
> Nexus ([Who is 7x?](../who-is-7x.md#the-platform-line-exponential-platform-legacy-nexus-and-dxp)). Check the
> distribution before you promise a feature to anyone.

## Choosing a product for a project

| The project needs | Start from | Read |
|---|---|---|
| A CMS with a shop, multisite and many languages, PHP only, no database server | Exponential 6 with SQLite | [Database support](../database-support.md) |
| Editors who build pages themselves | Exponential 6 with Exponential Layouts | [The layouts editor](../layouts-editor.md) |
| Help writing and translating content | Exponential Intelligence | [expai](../../features/6.0/extensions/expai.md) |
| The fastest way to serve the site | Exponential Velocity | [The persistent-worker server](../../features/6.0/velocity-persistent-worker-server.md) |
| A Symfony project with REST and GraphQL, no legacy kernel | Exponential Platform DXP | [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md) |
| A complete demo site with layouts on the Symfony platform | Exponential Platform Nexus | [Nexus starter](../../features/6.0/platform-nexus-starter.md) |
| To keep an Exponential 6 site and add Symfony next to it | Exponential Platform Legacy | [Legacy bridge](../../features/6.0/legacy-bridge.md) |
| A small, simple CMS to learn with | Exponential Basic | [Who is 7x?](../who-is-7x.md#exponential-basic) |

This table follows "Which product is for you" on
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#which-product-is-for-you). The rest of the
leadership pages are written for Exponential 6, the kernel this help belongs to.

## What it runs on

| Layer | Facts | Read |
|---|---|---|
| PHP | 8.0 to 8.5; Velocity needs 8.1 or later; 7.4 only up to 6.0.7 | [The support commitment](../who-is-the-exponential-foundation.md#php-versions) |
| Database | In order of preference: SQLite 3 (the default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Database support](../database-support.md) |
| Operating system | "almost any that runs PHP 8.0 or later", sorted into four tiers | [Operating system support](../os-support.md) |
| Web server | Exponential Velocity (recommended), FrankenPHP (production-ready alternative), Apache or nginx with PHP-FPM, and others | [Chapter 8: Serving the site](../../install/08-serving-the-site.md) |
| Installation | Composer packages, or Git clones with full history (the recommended way to build websites) | [Develop with Git](../develop-with-git.md) |

The decision between them is the subject of
[Choosing hosting, database and operating system](choosing-hosting-database-and-os.md).

## Languages and accessibility

- **268 locale files** (261 languages and countries, the test language Lorem Ipsum and 6 variations), in 32 scripts,
  40 of them written right to left, can be chosen as content languages; **32 administration translations** exist, ten
  of them complete ([Languages in Exponential](../../languages.md)).
- One installation holds at most 62 content languages at a time on 64-bit PHP
  ([Languages](../../languages.md#11-the-limit-of-62-content-languages)).
- The administration turns around for right-to-left interface languages, and content in a right-to-left language is
  marked as such ([Right to left](../../features/6.0/right-to-left.md)).
- The admin4 design is checked by tests for WCAG rules: color contrast of the tokens in light and dark mode and
  target sizes of at least 24 by 24 pixels ([Styling the administration](../../bc/6.0/admin4-styling.md)).

## Licenses

| What | License | Source |
|---|---|---|
| The software | GNU General Public License, version 2 or any later version. "Using Exponential under the terms of the GNU GPL is free of charge. The Professional License is no longer available." | README; `LICENSE` |
| Contributions | Released under the same GPL v2 or later | `CONTRIBUTING.md` |
| The documentation, including this page | GNU Free Documentation License 1.3 or later | [The license of this documentation](../../README-LICENSE.md) |
| Exponential Velocity | MIT | its `composer.json` ([Who is 7x?](../who-is-7x.md#open-source-licenses)) |

This is a summary, not legal advice. Buyers' questions are answered on
[Licensing explained for buyers](../business/licensing-for-buyers.md).

## What the project commits to

The project has no single "support policy" document. These commitments are each written down in a named place
([The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment)):

- **The maintained line** is Exponential 6.0. Security fixes are made on `main` and published with the next 6.0.x
  release; 5.x, 4.x, 3.x and older are not supported (`SECURITY.md`).
- **Security reports** go privately to the address in `SECURITY.md`, which promises a first answer within one
  business day.
- **Published versions never change.** A fix reaches you as the next release, never as a corrected copy of an old one
  (`SECURITY.md`).
- **Current PHP.** The continuous integration runs the test suite on PHP 8.1 to 8.5 and checks that PHP 8.0 still
  boots, on every push and pull request ([CI](../../bc/6.0/ci.md)).
- **Documentation lives with the code** and is shown in this help ([The help system](../../features/6.0/help-system.md)).

## Explaining it: three versions

Use these as a starting point. Each sentence is built only from the sources above.

**One sentence.**

> Exponential is an open source content management system, application framework and shop under the GPL, continuing
> a code base of nearly thirty years, led by 7x and operated by the nonprofit Exponential Foundation.

**Three sentences.**

> Exponential is an open source PHP content management system with a content model you define yourself, versions,
> approval workflows, multisite, many languages and a built-in shop. It is licensed under the GPL v2 or later, installs
> with Composer or as Git clones, and runs on PHP 8.0 to 8.5 with six supported databases, SQLite first. The company 7x
> leads its development and the nonprofit Exponential Foundation operates the project.

**For a steering committee.** Add, from this page: the product family table, the database order, the support
commitment and the license table. Point to [Who is 7x?](../who-is-7x.md) for history and to
[Risk management](risk-management.md) for the questions a committee will ask about longevity.

## Words you will hear

| Term | Meaning |
|---|---|
| **Siteaccess** | One way into an installation: a site, a language or the administration |
| **Content class** | A type of content (Article, Folder ...) with its attributes |
| **Object, node** | One piece of content; its place in the content tree |
| **Section** | A group of objects used for permissions and designs |
| **Role, policy** | A set of permissions; one permission |
| **Extension** | A package that adds modules, classes, templates or settings |
| **Dual kernel** | A distribution that runs the Symfony platform and Exponential 6 together |
| **Velocity** | Exponential's own PHP web and application server |

Every other term is in [the glossary](../../glossary.md) and in the glossary of
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#glossary).

## Related pages

- [Project managers: start here](project-managers-start-here.md)
- [Positioning and value proposition](../business/positioning-and-value-proposition.md)
- [Feature highlights mapped to customer needs](../business/feature-highlights-for-customers.md)
- [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
