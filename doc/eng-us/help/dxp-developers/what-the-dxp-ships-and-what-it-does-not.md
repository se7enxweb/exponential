---
title: What the DXP ships and what it does not
audience: dxp-developer
category: dxp-development
order: 3
summary: The definitive list of what Exponential Platform DXP installs, read from the metapackage's requirements, and what it does not include - no layouts editor, no approval workflow, no scheduler, no Exponential 6 kernel - with what to choose instead.
keywords: DXP features, what is included, metapackage, composer.json, layouts editor, workflow, scheduler, legacy kernel, admin UI, REST, GraphQL, Solr, HTTP cache, field types, not included
type: help
---
# What the DXP ships and what it does not

This page is for developers, architects and anyone who writes a proposal for a DXP project. It lists what Exponential
Platform DXP installs, read package by package from the requirements of `se7enxweb/exponential-platform-dxp`, and
what it does not install. When a feature is not on this page, assume it is not part of the DXP until you have found
the package that provides it.

## Contents

1. [How this list was made](#how-this-list-was-made)
2. [What it ships](#what-it-ships)
3. [What it does not ship](#what-it-does-not-ship)
4. [If you need what it does not ship](#if-you-need-what-it-does-not-ship)
5. [Check it yourself](#check-it-yourself)

## How this list was made

The metapackage's `composer.json` (`"type": "metapackage"`, description "A meta package for installing 7x Exponential
Platform DXP (Stable; Open Source)") lists every package a DXP project gets. This page groups them. The same grouping
is in [Who is the Exponential Foundation?, Exponential Platform DXP](../who-is-the-exponential-foundation.md#exponential-platform-dxp).

## What it ships

| Area | Packages in the metapackage | What you get |
|---|---|---|
| Content kernel | `se7enxweb/exponential-platform-dxp-core`, `ibexa/core-persistence`, `ibexa/doctrine-schema` | Content repository: content types, content items, locations, versions, translations, sections, users, roles and policies |
| Administration | `se7enxweb/admin-ui`, `se7enxweb/admin-ui-assets`, `se7enxweb/system-info` | The administration interface at `/adminui/`, with a system information view |
| Editing forms | `ibexa/content-forms` | Symfony forms to create and edit content and users |
| Users | `ibexa/user` | Login, registration, forgotten and changed passwords, user settings |
| Field types | in the core, plus `se7enxweb/fieldtype-richtext`, `ibexa/fieldtype-matrix`, `ibexa/fieldtype-query` | Text, number, date, image, file, relation and more; rich text; matrix; content query ([list](the-field-types-it-ships.md)) |
| Rendering | `ibexa/design-engine`, `ibexa/standard-design`, `ibexa/twig-components`, `symfony/twig-bundle`, `twig/extra-bundle` | Twig templates with themes and fallback, the `standard` design, Twig component groups |
| Assets | `symfony/webpack-encore-bundle`, `symfony/asset` | Webpack Encore builds for the site and the admin |
| APIs | `ibexa/rest`, `ibexa/graphql`, `overblog/graphiql-bundle`, `lexik/jwt-authentication-bundle`, `friendsofsymfony/jsrouting-bundle` | REST API v2, GraphQL with the GraphiQL explorer, JWT tokens |
| Search | `ibexa/core-search`, `ibexa/search`, `ibexa/solr` | The default database search engine and an optional Solr engine |
| Cache | `ibexa/http-cache`, `symfony/cache` | HTTP cache with purging, tag-aware application cache |
| Background work | `ibexa/cron`, `ibexa/messenger`, `ibexa/notifications`, `symfony/mailer` | A cron runner, message queues, system notifications, mail |
| Languages | `ibexa/i18n`, `symfony/translation` | Translations of the interface |
| Install | `ibexa/post-install`, `doctrine/doctrine-migrations-bundle` | Post-install steps and database migrations |
| Page building core | `se7enxweb/layouts-core` | The Layouts model and rendering core, without an editor ([Layouts core in the DXP](layouts-core-in-dxp.md)) |
| Framework | Symfony 7.4 components and bundles, `doctrine/doctrine-bundle`, `monolog/monolog`, `knplabs/knp-menu-bundle` | The application framework |

Requirements of the metapackage itself: `"php": ">=8.3"`, `ext-ctype`, `ext-iconv`; the skeleton adds `ext-gd`,
`ext-curl`, `ext-json`, `ext-xsl`, `ext-xml`, `ext-intl` and `ext-mbstring`.

## What it does not ship

| Not included | Why it matters | Source |
|---|---|---|
| A layouts editor | Only `se7enxweb/layouts-core` is required: no layouts UI, no standard blocks, no Ibexa integration package of Netgen Layouts | Metapackage requirements |
| An approval workflow | No workflow package is required | Metapackage requirements |
| A scheduler for publishing | No scheduler package is required | Metapackage requirements |
| The Exponential 6 kernel | No legacy bridge, no `se7enxweb/exponential` | Skeleton README: "No LegacyBridge, no classic legacy kernel" |
| Demo site content and design | The skeleton is "a clean Symfony project for Exponential Platform with no demo design" | [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md#what-it-is) |
| A shop | No commerce package is required | Metapackage requirements |

The skeleton's README also has a general list of "Main Exponential Platform DXP Features" that mentions content
approval, scheduled publication and a workflow system. Those lines describe the family's heritage, not the packages
the metapackage installs. Go by the package list ([Claims to avoid](claims-to-avoid.md)).

## If you need what it does not ship

| You need | Choose |
|---|---|
| Editors who arrange pages with blocks, on Symfony | Exponential Platform Nexus ([Nexus starter](../../features/6.0/platform-nexus-starter.md)) |
| The Exponential 6 kernel beside Symfony | Exponential Platform Legacy ([Legacy bridge](../../features/6.0/legacy-bridge.md)) |
| Workflows, scheduled publishing, a shop and a layouts editor without Symfony | Exponential 6 ([Getting started](../getting-started.md)) |
| Something only for your project | Write a Symfony bundle in `src/` or require a package; check it against PHP 8.3 and Symfony 7.4 first |

## Check it yourself

```bash
composer show se7enxweb/exponential-platform-dxp --tree | head -60   # what the metapackage pulls in
composer show | grep -E "^(ibexa|se7enxweb|netgen)/"                  # the content packages in your project
composer why netgen/layouts-ui                                        # prints nothing when it is not installed
```

## Related pages

- [Reading the metapackage](reading-the-metapackage.md)
- [Choosing DXP, Legacy, Nexus or Exponential 6](choosing-dxp-legacy-nexus-or-exponential-6.md)
- [Knowing the product family, Common mix-ups](../advocates/knowing-the-product-family.md#common-mix-ups)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
