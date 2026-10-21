---
title: What Exponential Platform DXP is
audience: dxp-developer
category: dxp-development
order: 2
summary: The DXP in one page for developers - its architecture, the single Symfony kernel, the entry points, the packages it is made of, who maintains it, and where it sits in the Exponential product family.
keywords: Exponential Platform DXP, what is, architecture, Symfony 7.4, PHP 8.3, single kernel, Platform v5, Ibexa OSS, eZ Platform, product family, 7x
type: help
---
# What Exponential Platform DXP is

This page is for developers who meet Exponential Platform DXP for the first time. It describes the product as its
own packages describe it, shows its architecture, and places it next to the other products of the family.

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [The architecture](#the-architecture)
3. [What a project is made of](#what-a-project-is-made-of)
4. [Who maintains it](#who-maintains-it)
5. [Where it sits in the family](#where-it-sits-in-the-family)
6. [What the name means](#what-the-name-means)

## In one paragraph

Exponential Platform DXP is "a free, open-source Digital Experience Platform built on the Symfony framework, the
community continuation of the eZ Platform and Ibexa OSS line maintained by 7x" (README, quoted in
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-dxp)). The skeleton
calls Platform v5 "the standard single-kernel release": the Platform v5 content kernel on **Symfony 7.4 LTS** with
**PHP 8.3+**, with "no LegacyBridge, no classic legacy kernel" (skeleton `README.md` and `INSTALL.md`).

## The architecture

The skeleton's installation guide (section 2) draws the request path like this:

```text
Browser request
  -> web server (Apache or Nginx, with PHP-FPM)
    -> public/index.php (the Symfony entry point)
      -> Symfony kernel (Platform v5, Symfony 7.4 LTS)
           |- administration interface        /adminui/
           |- REST API v2                     /api/ibexa/v2/ (the guide shows /api/ezp/v2/)
           |- GraphQL API                     /graphql
           '- Symfony and Twig site controllers  /
```

One Symfony application, one kernel, one web entry point. Content is stored in a relational database through
Doctrine DBAL (MySQL, MariaDB, PostgreSQL, or SQLite for development) and rendered through Twig templates.

| Layer | What it is in the DXP | Read |
|---|---|---|
| Content repository | The Platform v5 kernel `se7enxweb/exponential-platform-dxp-core` (the fork of the upstream core) | [Content, content types and locations](content-types-and-locations.md) |
| Administration | `se7enxweb/admin-ui` at `/adminui/` | [The admin UI](the-admin-ui.md) |
| Front end | Twig with `ibexa/design-engine` and `ibexa/standard-design` | [Twig and the design engine](twig-templates-and-the-design-engine.md) |
| APIs | `ibexa/rest`, `ibexa/graphql`, JWT through `lexik/jwt-authentication-bundle` | [The REST API](the-rest-api.md), [GraphQL](graphql-and-graphiql.md) |
| Search | `ibexa/core-search`, `ibexa/search`, `ibexa/solr` | [Search](search-legacy-and-solr.md) |
| Operations | `ibexa/cron`, `ibexa/messenger`, `ibexa/http-cache`, Doctrine migrations | [Cron](cron.md), [HTTP cache](http-cache-and-varnish.md) |

## What a project is made of

A DXP project is a Symfony project created from the skeleton `se7enxweb/exponential-platform-dxp-skeleton`. The
skeleton's own `composer.json` requires little more than the metapackage `se7enxweb/exponential-platform-dxp`,
`symfony/flex` and `symfony/runtime`; the metapackage brings in everything else
([Reading the metapackage](reading-the-metapackage.md)). Symfony Flex recipes write the configuration under
`config/` when the packages are installed ([The project directory layout](the-project-directory-layout.md)).

## Who maintains it

The DXP is maintained by **7x** and the community. The skeleton's README opens with a project notice: the project is
not associated with the original software of eZ Systems or with eZ Systems itself. Many of its packages are se7enxweb forks of
upstream packages that declare the upstream names under `replace`
([The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md)); others are required from upstream as they
are (`ibexa/rest`, `ibexa/graphql` and more).

## Where it sits in the family

| Product | Kernel | Use it when |
|---|---|---|
| **Exponential Platform DXP** | Platform v5 only, Symfony 7.4 | A new Symfony project, no Exponential 6 kernel |
| Exponential Platform Legacy | Symfony platform plus the Exponential 6 kernel (dual kernel) | You keep an Exponential 6 site and add Symfony beside it |
| Exponential Platform Nexus | A finished site with Netgen Layouts; the v5 lines without the legacy kernel, the earlier lines with it | You want a complete demo site fast |
| Exponential 6 | The PHP CMS kernel itself, no Symfony | A CMS on PHP only |

The full comparison is on [Choosing DXP, Legacy, Nexus or Exponential 6](choosing-dxp-legacy-nexus-or-exponential-6.md).

## What the name means

"DXP" stands for Digital Experience Platform, the term the upstream line used for a content platform with APIs for
several channels. In this family it names one distribution with a defined package list. It is not a promise of every
feature that other products sold under the DXP label offer ([Claims to avoid](claims-to-avoid.md)).

## Related pages

- [What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md)
- [DXP skeleton: create-project and a documented install](../../features/6.0/platform-dxp-skeleton.md)
- [Knowing the product family](../advocates/knowing-the-product-family.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
