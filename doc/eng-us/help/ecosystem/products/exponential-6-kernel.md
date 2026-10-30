---
title: The Exponential 6 kernel in the ecosystem
audience: ecosystem, developer, admin, partner
category: ecosystem
order: 11
summary: The Exponential 6 kernel as the center of the ecosystem - what it is, its main features as the README lists them, the 6.0 release line and its support, how it is installed with Composer, and what builds on it.
keywords: Exponential 6, kernel, CMS, CMF, content model, shop, multisite, multilingual, REST API, se7enxweb/exponential, 6.0.14, 6.0.15, Composer
type: help
---
# The Exponential 6 kernel in the ecosystem

## Contents

1. [What it is](#what-it-is)
2. [Main features](#main-features)
3. [The release line](#the-release-line)
4. [Installing it](#installing-it)
5. [What builds on it](#what-builds-on-it)

## What it is

"Exponential is a professional PHP application framework with advanced CMS (content management system)
functionality. As a CMS its most notable feature is its fully customizable and extendable content model"
(`README.md`). It is published as the Composer package `se7enxweb/exponential`. Its code was once the code of
eZ Publish, which its creator discontinued
([Exponential 6, the kernel](../../who-is-the-exponential-foundation.md#exponential-6-the-kernel)).

## Main features

The README's "Main Exponential features" include: user-defined content classes and objects, version control,
multilingual support, a built-in search engine, a role-based permission system, content approval and scheduled
publication, multi-site support, built-in e-commerce, workflows, a template engine, a headless REST API, a database
abstraction layer, clustering and a plugin API. A tour for buyers: [Feature tour](../../customers/value/feature-tour.md).

## The release line

| Fact | Source |
|---|---|
| 6.0.0 was released on 1 January 2024 | [Timeline](../../who-is-the-exponential-foundation.md#timeline) |
| 6.0.14 was released on 7 June 2026; 6.0.15 is in development on `main` | same |
| 6.0 is the maintained line; fixes are published with the next 6.0.x release | `SECURITY.md` |
| A published tag is never changed | `SECURITY.md` |
| PHP 8.0 to 8.5 (PHP 7.4 usable without support) are supported | `README.md`, [PHP versions](../php-stack/php-versions.md) |

The 6.0.15 line adds, among much else, Velocity, the admin4 design, an audit trail, Exponential Layouts, Exponential
Intelligence, content jobs, the LoveStack features of Mugo Web and the help system
([Exponential 6, the kernel](../../who-is-the-exponential-foundation.md#exponential-6-the-kernel)).

## Installing it

"Exponential 6.0.x specifically requires the php package manager called Composer to be installed and run the command
`composer install`" (`README.md`). The installation book covers every route:
[Introduction](../../../install/01-introduction.md), [Getting the code](../../../install/03-getting-the-code.md),
[Install in one command](../../../features/6.0/install-in-one-command.md).

## What builds on it

- The bundled extensions ([The bundled extensions](../packages/the-bundled-extensions.md)).
- Velocity, which serves it ([Exponential Velocity](exponential-velocity.md)).
- Exponential Platform Legacy and Nexus, which include it next to Symfony ([The Platform line](the-platform-line.md)).
- Sites, site packages, themes, apps and integrations built by the community and partners.

## Related pages

- [Products](README.md)
- [What Exponential is (project managers)](../../leadership/what-exponential-is.md)
- [Getting started](../../getting-started.md)
- [The history of Exponential](../../../history/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
