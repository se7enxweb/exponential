---
title: "DXP Developers: start here"
audience: dxp-developer
category: dxp-development
order: 1
summary: Where Symfony developers who build on Exponential Platform DXP start. What the DXP is and is not, which pages answer which task, the first hour with a new project, and the rules that keep claims and projects correct.
keywords: DXP, DXP developer, Exponential Platform DXP, Symfony, skeleton, metapackage, start here, overview, REST, GraphQL, Twig, admin UI, Ibexa OSS, eZ Platform, Platform v5
featured: true
type: help
---
# DXP Developers: start here

This section of the help is for **developers who build on Exponential Platform DXP**: Symfony developers starting a
new project, teams moving from eZ Platform or Ibexa OSS, and Exponential 6 developers who want to know whether the
DXP, Legacy or Nexus distribution fits their next project. It explains what the DXP ships, how a project is
installed, configured, built, deployed and updated, and where each part is documented in depth.

Every statement about the DXP on these pages was checked against the metapackage `se7enxweb/exponential-platform-dxp`
(its `composer.json`), the skeleton's installation guide (`INSTALL.md` of
`se7enxweb/exponential-platform-dxp-skeleton`), the documentation in this repository, or the installed packages of a
Platform v5 installation. Where a fact comes from an upstream package, the page says so.

## Contents

1. [What the DXP is, in five lines](#what-the-dxp-is-in-five-lines)
2. [What you find here](#what-you-find-here)
3. [Your first hour with the DXP](#your-first-hour-with-the-dxp)
4. [The rules for this section](#the-rules-for-this-section)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## What the DXP is, in five lines

- **Exponential Platform DXP** is "a free, open-source Digital Experience Platform built on the Symfony framework, the
  community continuation of the eZ Platform and Ibexa OSS line maintained by 7x"
  ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-dxp)).
- It runs on **PHP 8.3 or newer** and **Symfony 7.4**, with one kernel and one web entry point.
- It ships the administration interface at `/adminui/`, the REST API v2, GraphQL at `/graphql`, Twig
  rendering with the design engine, core and Solr search, HTTP cache, cron and messenger.
- It does **not** ship a layouts editor, an approval workflow, a scheduler or the Exponential 6 kernel
  ([What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md)).
- You start a project with `composer create-project se7enxweb/exponential-platform-dxp-skeleton`
  ([Installing with the skeleton](installing-with-the-skeleton.md)).

## What you find here

| Task | Page |
|---|---|
| Decide whether the DXP is the right product | [Choosing DXP, Legacy, Nexus or Exponential 6](choosing-dxp-legacy-nexus-or-exponential-6.md) |
| Know exactly what is installed | [Reading the metapackage](reading-the-metapackage.md), [The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md) |
| Install a project | [Versions you need](php-symfony-and-node-versions.md), [Installing with the skeleton](installing-with-the-skeleton.md), [Environment variables](environment-variables.md), [Databases](databases-mysql-postgresql-sqlite.md), [File permissions](file-permissions.md), [Web server setup](web-server-setup.md) |
| Find your way in the project | [The project directory layout](the-project-directory-layout.md), [Siteaccesses](siteaccesses-site-and-admin.md), [The admin UI](the-admin-ui.md) |
| Model content | [Content, content types and locations](content-types-and-locations.md), [The field types it ships](the-field-types-it-ships.md) |
| Render the site | [Twig and the design engine](twig-templates-and-the-design-engine.md), [The standard design and your theme](the-standard-design-and-your-theme.md), [Twig functions you use daily](twig-functions-you-use-daily.md), [Twig components](twig-components.md), [Image variations](image-variations.md), [Building assets](building-assets-with-yarn-and-encore.md) |
| Deliver content to other clients | [The REST API](the-rest-api.md), [JWT authentication](jwt-authentication.md), [GraphQL and GraphiQL](graphql-and-graphiql.md), [Headless with the DXP](headless-with-the-dxp.md) |
| Make it fast | [HTTP cache and Varnish](http-cache-and-varnish.md), [Application cache and Redis](application-cache-and-redis.md), [Search](search-legacy-and-solr.md), [Reindexing](reindexing.md) |
| Run it | [Cron](cron.md), [Messenger and notifications](messenger-and-notifications.md), [Console commands](console-commands-and-renames.md), [Doctrine migrations](doctrine-migrations.md), [Day-to-day commands](day-to-day-commands.md), [Deploying](deploying-dxp.md), [Updating](updating-dxp.md), [Troubleshooting](troubleshooting-dxp.md) |
| Move an existing project | [Coming from Ibexa OSS or eZ Platform](coming-from-ibexa-oss-or-ez-platform.md), [Coming from Exponential 6](coming-from-exponential-6.md), [The 4.6.x skeleton](the-v4x-skeleton.md) |
| Page building on the DXP | [Layouts core in the DXP](layouts-core-in-dxp.md) |
| Licensing, contributing, honesty | [Licensing](licensing.md), [Contributing to the forks](contributing-to-the-forks.md), [Claims to avoid](claims-to-avoid.md) |
| Quick answers | [FAQ](dxp-developer-faq.md), [Project checklist](dxp-project-checklist.md), [Glossary](glossary-for-dxp-developers.md), [Reading paths](reading-paths-for-dxp-developers.md) |

## Your first hour with the DXP

1. **Read what it is and is not** (10 minutes):
   [What Exponential Platform DXP is](what-exponential-platform-dxp-is.md) and
   [What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md).
2. **Check your machine** (5 minutes): PHP 8.3 or newer, Composer 2, Node.js 20 and Yarn 1.22
   ([Versions you need](php-symfony-and-node-versions.md)).
3. **Install on SQLite** (30 minutes): no database server needed
   ([Installing with the skeleton](installing-with-the-skeleton.md),
   [SQLite for development](sqlite-for-development.md)).
4. **Look around** (15 minutes): open `/adminui/`, create a content item, read it back through the REST API and
   `/graphql` ([The admin UI](the-admin-ui.md), [The REST API](the-rest-api.md)).

## The rules for this section

1. **Describe the DXP from its package list.** The skeleton's README carries a long, general feature list; only what
   the metapackage requires is installed. Check `composer.json` before you promise a feature
   ([Claims to avoid](claims-to-avoid.md)).
2. **Use the `exponential:` command names** in everything new; the `ibexa:` names are aliases
   ([Console commands](console-commands-and-renames.md)).
3. **Never commit `.env.local`, JWT keys or passwords.** The installer's default administrator account is publicly
   documented: change its password right after the install.
4. **Never install an upstream package next to its fork** ([The se7enxweb forks and `replace`](the-se7enxweb-forks-and-replace.md)).
5. **Use the right names.** The product is Exponential Platform DXP; 7x is the company that leads it; the Exponential
   Foundation is the nonprofit. It is not associated with eZ Systems or Ibexa AS
   ([Who is 7x?](../who-is-7x.md#in-short)).

## Pages from other audiences you will use

| Page | Why a DXP developer reads it |
|---|---|
| [Developers: the extension development book](../extension-development/README.md) | The developer audience's book for Exponential 6; read it when a project also runs the Exponential 6 kernel |
| [Reading paths for developers](../extension-development/reading-paths.md) | Where the developer audience starts, by goal |
| [Develop with Git](../develop-with-git.md) | The Git way of working the whole project uses |
| [Knowing the product family](../advocates/knowing-the-product-family.md) | Every product of the family in one sentence |
| [Headless with the REST API](../businesses/mobile-and-headless/headless-with-the-rest-api.md) | How organizations plan a headless project |
| [Migrating customers from eZ Platform and Ibexa](../hosting/migrations/from-ez-platform-and-ibexa.md) | The hosting side of a migration |
| [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#which-product-is-for-you) | Which product is for you |

## Finding these pages again

- In the Help center, use the audience filter **DXP Developers**, or open **All pages** and the folder
  `dxp-developers`.
- Every page has **Edit on GitHub**: if a command changed or a fact is out of date, fix it for every developer after
  you.

## Related pages

- [What Exponential Platform DXP is](what-exponential-platform-dxp-is.md)
- [DXP skeleton: create-project and a documented install](../../features/6.0/platform-dxp-skeleton.md)
- [Platform package map](../../specifications/6.0/platform-package-map.md)
- [Who is 7x?](../who-is-7x.md) and [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
