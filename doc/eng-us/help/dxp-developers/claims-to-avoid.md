---
title: Claims to avoid
audience: dxp-developer
category: dxp-development
order: 47
summary: Statements about Exponential Platform DXP that sound right but are not true of its package list or its installed code - layouts editor, workflows, scheduling, legacy kernel, URLs from older guides, "Exponential" Twig functions - with the correct statement and its source.
keywords: claims, accuracy, proposals, marketing, layouts editor, workflow, scheduler, legacy kernel, REST prefix, GraphiQL, SOAP, Oracle, feature list, honest
type: help
---
# Claims to avoid

This page is for developers, architects and anyone who writes a proposal, a README or a talk about Exponential Platform
DXP. Each row is a claim that has been made or is easy to make, with the correct statement.

## Contents

1. [Why this page exists](#why-this-page-exists)
2. [Features](#features)
3. [Technical details](#technical-details)
4. [Names and relationships](#names-and-relationships)
5. [How to check a claim](#how-to-check-a-claim)

## Why this page exists

The DXP is defined by the packages its metapackage requires. The skeleton's README also has a long, general list of
"Main Exponential Platform DXP Features" that the family has offered over the years; some items on it describe
Exponential 6 or other distributions, not the DXP's package list. A proposal built on that list promises things the
customer will not get.

## Features

| Avoid | Say instead | Source |
|---|---|---|
| "Editors build pages visually with blocks." | The DXP ships the Layouts core only, no editor; Nexus and Exponential 6 have page builders | [Layouts core in the DXP](layouts-core-in-dxp.md) |
| "Content approval workflow and scheduled publication are included." | No workflow or scheduler package is in the DXP | [What the DXP ships](what-the-dxp-ships-and-what-it-does-not.md#what-it-does-not-ship) |
| "Runs your Exponential 6 extensions." | No legacy kernel; use Legacy or a Nexus line with the legacy kernel | [Choosing](choosing-dxp-legacy-nexus-or-exponential-6.md) |
| "Includes a shop." | No commerce package is in the DXP | Metapackage requirements |
| "SOAP library" or "Oracle database support" | Not part of the DXP's packages; Oracle appears in the guide only as an export target | [Databases](databases-mysql-postgresql-sqlite.md#which-engine) |
| "Comes with a demo site." | The skeleton has no demo design; Nexus does | [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md#what-it-is) |
| "Production-ready on SQLite." | SQLite is for development, tests, demos and offline installations | [SQLite for development](sqlite-for-development.md#limits) |

## Technical details

| Avoid | Say instead |
|---|---|
| "The REST API is at `/api/ezp/v2/`." | The prefix is the setting `ibexa.rest.path_prefix`; the installed v5 package defaults to `/api/ibexa/v2` ([The REST API](the-rest-api.md#where-it-lives)) |
| "GraphiQL is at `/graphql/explorer`." | In an installed v5 project the dev route is `/graphiql`; check with `debug:router` ([GraphQL](graphql-and-graphiql.md#the-explorer)) |
| "Templates use the `@ezdesign` namespace." | On v5 it is `@ibexadesign` ([Twig and the design engine](twig-templates-and-the-design-engine.md#the-ibexadesign-namespace)) |
| "There are `exponential_*` Twig functions." | The Twig functions are the generation's `ibexa_*` functions |
| "Every command is `exponential:`." | `ibexa:cron:run` and `ibexa:graphql:generate-schema` keep their names ([Console commands](console-commands-and-renames.md)) |
| "Works with Node.js 22." | The DXP skeleton's guide supports Node.js 20 only; the Nexus v5 starter uses 22 |
| "Runs on PHP 8.2." | The metapackage requires PHP 8.3 or newer |

## Names and relationships

| Avoid | Say instead |
|---|---|
| "A product of Ibexa" or "by eZ Systems" | Maintained by 7x and the community; not associated with eZ Systems ([Who is 7x?](../who-is-7x.md#in-short)) |
| "Exponential 6 on Symfony" | The DXP is the Symfony platform line; Exponential 6 is a separate kernel |
| "Legacy means outdated" | "Legacy" is the Exponential 6 kernel beside a Symfony platform |

## How to check a claim

1. Find the package that provides the feature in the metapackage's `composer.json`.
2. Find the code or configuration in an installed project (`vendor/`, `config/`).
3. If you cannot find either, leave the claim out, or write "not verified".

## Related pages

- [What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md)
- [Claims you must not make (for advocates)](../advocates/claims-you-must-not-make.md)
- [Comparing Exponential factually](../business/comparing-exponential-factually.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
