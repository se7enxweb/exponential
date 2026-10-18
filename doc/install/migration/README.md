---
title: Migrating to and from Exponential
audience: admin, developer, project-manager
category: install-upgrade
order: 5
summary: The migration hub. A verified table of every Exponential product and its versions, a decision guide, and one guide per path within the Exponential family and from or to other systems (WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, site builders, static sites, JavaScript apps), with the real import and export tools.
keywords: migration, migrate, move, switch, import, export, Exponential Platform, Nexus, DXP, Exponential Basic, Platform Legacy, WordPress, Drupal, Joomla, Magento, Shopify, Contentful, AEM, Wix, Squarespace, GoDaddy, Next.js, static site, redirects, URL aliases, cutover, rollback
type: install
featured: true
---
# Migrating to and from Exponential

This is the hub for moving a website **to** Exponential 6, **from** Exponential 6 to another product of the
Exponential family, and **away** from Exponential to another system. It starts with a table of the Exponential
products as they really are published today, then helps you choose a path, and links to one detailed guide per path.
Every guide has the same parts: what you can export, how it maps to Exponential's content model, the tools that
exist, where you need your own script, URLs and redirects, search engine metadata, testing, cut-over, rollback and a
checklist.

[Installation book contents](../README.md) · Start with: [The common migration checklist](checklist.md)

## Contents

- [1. The Exponential products and their versions (verified)](#1-the-exponential-products-and-their-versions-verified)
- [2. Which path fits?](#2-which-path-fits)
- [3. Migrations within the Exponential family](#3-migrations-within-the-exponential-family)
- [4. Migrations from and to other systems](#4-migrations-from-and-to-other-systems)
- [5. Shared references](#5-shared-references)
- [6. How the facts on these pages were checked](#6-how-the-facts-on-these-pages-were-checked)

## 1. The Exponential products and their versions (verified)

Exponential is a family of products maintained by 7x. Their names are similar, and their version numbers do not
always say which platform generation they carry, so check this table before you plan anything. Every row was read on
2026-10-18 from the product's GitHub repository under `github.com/se7enxweb`: the default branch, the branches, the
releases (`gh release list`), the tags, and the `composer.json` of the default branch or of the release tag named.
Where a fact could not be verified it says so.

### 1.1 The products

| Product | Repository and Composer package | What it is, according to its `composer.json` | Latest releases (GitHub) | Contains the Exponential 6 legacy kernel |
|---|---|---|---|---|
| **Exponential 6** (the kernel this help belongs to) | `se7enxweb/exponential`, type `ezpublish-legacy` | The legacy PHP kernel: INI settings, TPL templates, the admin designs, five database engines, PHP `^8.0` to `^8.5` | `v6.0.14` (latest release, 2026-06-07); `v6.0.0` to `v6.0.14` monthly since 2024-01-01. The default branch `main` is the 6.0.15 line (`lib/version.php`) | It *is* the kernel |
| **Exponential Starter** | `se7enxweb/exponential-starter` (its `composer.json` names the package `se7enxweb/exponential`) | A ready-to-run Exponential 6 with the vendor directory included, tuned for SQLite (its description says 6.0.13) | `1.0.0.0` to `1.0.0.5` (2026-06-17) | Yes |
| **Exponential Platform** | `se7enxweb/exponential-platform` (default branch `3.2`) | "Symfony / New Stack Only": eZ Platform 3.2 forks (`se7enxweb/ezplatform-kernel ~1.3.42`, `ezplatform-admin-ui ~2.3.39`), Symfony 5, PHP `^7.3 \|\| ^8.1` to `^8.4`; no legacy bridge | `v3.2.9` (2025-09-28), the only GitHub release; tags `v3.0.x` to `v3.2.9`; branches also `1.7`, `1.13`, `2.5`, `3.0`, `3.1` | No |
| **Exponential Platform OSS** | `se7enxweb/oss` | The open source metapackage. Branch `3.3` (releases `v3.3.0.2` to `v3.3.0.5`, 2026-04-12), branches `4.0` to `4.6`; `master` requires the `ibexa/*` 5.0 packages and Symfony 7.4 | `v3.3.0.5` | No |
| **Exponential Platform DXP** (metapackage) | `se7enxweb/exponential-platform-dxp` | Platform v5 on `master`: PHP `>=8.3`, Symfony 7.4, `se7enxweb/exponential-platform-dxp-core ^5.0.1.0`, `se7enxweb/admin-ui ^5.0.5.0`, REST, GraphQL, HTTP cache, Solr and core search, RichText, Matrix and Query field types, notifications, messenger, cron, standard design, JWT, and `se7enxweb/layouts-core` only. Branches `3.3`, `4.0` to `4.6`, `4.6.x-LB`, `5.x-LB` | `0.0.0.1`, `v0.0.0.2` (2026-04-06) | No (the `-LB` branches are what Platform Legacy builds on) |
| **Exponential Platform DXP core** | repository `se7enxweb/core`, package `se7enxweb/exponential-platform-dxp-core` (`replace: ibexa/core`) | The repository kernel of Platform v4 and v5 | `v5.0.9` (2026-10-05); tags `v4.6.15` to `v4.6.24` for the v4 line | No |
| **Exponential Platform DXP 2.x** (skeleton, Platform v5) | `se7enxweb/exponential-platform-dxp-skeleton` | A project skeleton: `se7enxweb/exponential-platform-dxp dev-master`, PHP `>=8.3`. Its description: "Provides Ibexa DXP OSS 5.0.6" | `v2.0.0.0` to `v2.0.0.2` (2026-04-11); before that `0.0.0.1`, `0.0.0.2` | No |
| **Exponential Platform DXP 1.x** (skeleton, Platform v4) | `se7enxweb/exponential-platform-v4x-dxp-skeleton` | A project skeleton: `se7enxweb/exponential-platform-dxp 4.6.x-dev`, PHP `^7.4` to `^8.5`. Its description: "Provides Ibexa DXP OSS v4.6.x" | `v1.0.0.0` to `v1.0.0.2` (2026-04-11) | No |
| **Exponential Platform DXP Starter** | `se7enxweb/exponential-platform-dxp-starter` (package name in its `composer.json`: `se7enxweb/exponential-platform-dxp-skeleton`) | The DXP 2.x skeleton ready to run, vendor directory included, SQLite | `1.0.0.0` (2026-04-26) | No |
| **Exponential Platform Nexus** | `se7enxweb/exponential-platform-nexus` (default branch `master`) | A complete site distribution: Netgen Layouts, the Netgen media site, Tags, Site API and an admin on an Exponential Platform generation. One release line per generation, see [1.2](#12-the-release-lines-of-nexus-and-platform-legacy) | `1.3.0.6` (marked Latest), `v1.2.0.1`, `v1.1.0.8`, `1.0.0.11`, `v2.5.0.7` (all 2026-10-06) | Lines `1.0.0.x`, `v2.5.0.x`, `1.1.0.x`, `1.2.0.x`: yes. Line `1.3.0.x`: no |
| **Exponential Platform Nexus Starter** | `se7enxweb/exponential-platform-nexus-starter` | Nexus on Platform v5 ready to run: `se7enxweb/exponential-platform-dxp dev-master`, Symfony 7.4, PHP `>=8.4` (its description says "Exponential 6.0.13"; its `composer.json` has no legacy kernel) | `1.0.0.0` (2026-04-26) | No, by its `composer.json` |
| **CJW Exponential Platform Nexus** | `se7enxweb/cjw-exponential-platform-nexus` | The CJW variant of Nexus on the 2.5 generation: `se7enxweb/exponential ^6.0.12`, `se7enxweb/legacy-bridge ^2.1`, Netgen Layouts 1.4, vendor files included | `1.0.0.6` to `1.0.0.12` (2026-10-06) | Yes |
| **CJW Exponential Platform Nexus Starter** | `se7enxweb/cjw-exponential-platform-nexus-starter` (package name `se7enxweb/cjw-exponential-platform-nexus`) | The CJW Nexus ready to run, SQLite | `1.0.0.0` to `1.0.0.3` (2026-07-04) | Yes |
| **Exponential Platform Legacy** | `se7enxweb/exponential-platform-legacy` (default branch `master`) | The hybrid: an Exponential Platform generation plus the Exponential 6 kernel on one database, joined by `se7enxweb/legacy-bridge`. One release line per generation, see [1.2](#12-the-release-lines-of-nexus-and-platform-legacy) | `v5.0.3.1` (Latest, 2026-10-05), `v4.6.23.3`, `v3.3.44.8`, `v2.5.0.4` (all 2026-10-05) | Yes, every line |
| **Exponential Platform Legacy Starter** | `se7enxweb/exponential-platform-legacy-starter` | Platform Legacy v5 ready to run: `exponential-platform-dxp dev-5.x-LB`, `legacy-bridge ^5.0.0.0`, PHP `>=8.3` | `1.0.0.0` (2026-04-26) | Yes |
| **Exponential Basic** | `se7enxweb/exponentialbasic` (default branch `main`) | The modernized continuation of the classic Exponential **2.x** CMS on PHP 8 (`php ^8.2`): its own kernel modules (`ezarticle`, `ezimagecatalogue`, `ezforum`, `eztrade`, `ezcalendar`, `ezpoll` and more). It is a different code base and data model from Exponential 6 | `v2.4.0.3` (2026-08-18), `v2.4.0.2`, `v2.4.0.1`, `v2.4.0.0`; branches `2.4.0.0` to `2.4.0.2`, `main`, `master` | No |
| **Exponential Basic Platform** | `se7enxweb/exponential-basic-platform` | Described by its repository as "Beta / Incomplete / Unstable" | none | No |
| **Exponential Velocity** | `se7enxweb/exponential-velocity` | The PHP application server recommended for Exponential 6; not a content product. See [Serving the site](../08-serving-the-site.md) | (not needed for migration planning) | n/a |
| `exponential7` | `se7enxweb/exponential7` | "private development branch": no branches, tags or releases are visible | none | **Not verifiable**; do not plan with it |

The names you may have heard and what they mean:

| You heard | It is |
|---|---|
| "Nexus 2.5", "Nexus 4.x", "Nexus 5.x" | Platform **generations** inside Nexus, not Nexus version numbers. The Nexus versions are `1.0.0.x` and `v2.5.0.x` (generation 2.5), `1.1.0.x` (3.3), `1.2.0.x` (4.6) and `1.3.0.x` (v5) |
| "Platform Legacy 2.5 / 4.x / 5.x" | Correct: its versions carry the generation: `v2.5.0.x`, `v3.3.44.x` (earlier `v3.0.0.x`), `v4.6.23.x`, `v5.0.x` |
| "DXP 1.x / 2.x" | The two DXP **skeletons**: `v1.0.0.x` is `exponential-platform-v4x-dxp-skeleton` (generation 4.6), `v2.0.0.x` is `exponential-platform-dxp-skeleton` (generation v5) |
| "Exponential Platform" without a suffix | `se7enxweb/exponential-platform` `v3.2.9`, the Symfony-only distribution |
| "Exponential", "Exponential 6", "Exponential Legacy" | `se7enxweb/exponential`, this kernel |

### 1.2 The release lines of Nexus and Platform Legacy

Read from the `composer.json` of each release tag:

| Product and line | Latest release | Platform generation | Symfony | PHP | Legacy kernel and bridge |
|---|---|---|---|---|---|
| Nexus `1.0.0.x` | `1.0.0.11` | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5.40`) | 3.4 | `^7.1.3` to `^8.6` | `se7enxweb/exponential ^6.0.12`, `legacy-bridge ^2.1` |
| Nexus `v2.5.0.x` (from `master`) | `v2.5.0.7` | eZ Platform 2.5 (`~7.5.40`) | 3.4 | as above | as above |
| Nexus `1.1.0.x` | `v1.1.0.8` | 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `^8.0` | `legacy-bridge ^3.0`, `site-legacy-bundle ^2.0` |
| Nexus `1.2.0.x` | `v1.2.0.1` | 4.6 (`se7enxweb/oss ~4.6.0`) | 5.4 | `>=8.2` | `site-legacy-bundle v2.0.0` with the legacy installer (`ezpublish_legacy/`) |
| Nexus `1.3.0.x` | `1.3.0.6` | v5 (`se7enxweb/exponential-platform-dxp dev-master`) | 7.4 | `>=8.4` | none in `composer.json` |
| Platform Legacy `v2.5.0.x` | `v2.5.0.4` | eZ Platform 2.5 (`~7.5.33`) | 3.4 | `^7.1.3 \|\| ^8.1 \|\| ^8.2` | `se7enxweb/exponential ^6.0.12`, `legacy-bridge ^2.1` |
| Platform Legacy `v3.3.44.x` | `v3.3.44.8` | 3.3 (`se7enxweb/oss ~3.3.0.0`, `ezplatform-kernel ~1.3.43`) | 5.4 | `^8.0` | `legacy-bridge ^3.0.0.35` |
| Platform Legacy `v4.6.23.x` | `v4.6.23.3` | 4.6 (`exponential-platform-dxp 4.6.x-LB-dev`) | 5.4 | `^7.4` to `^8.5` | `legacy-bridge ^4.0.0.0` |
| Platform Legacy `v5.0.x` | `v5.0.3.1` | v5 (`exponential-platform-dxp dev-5.x-LB`) | 7.4 | `>=8.3` | `legacy-bridge ^5.0.0.0` |

> **Warning**
> Two version traps. **Platform Legacy `v5.0.3` is not a v5 release**: its `composer.json` is the 2.5 line's
> (`ezpublish-kernel ~7.5.33`, `legacy-bridge ^2.1`). Its release notes call `v5.0.3.1` "the first correct 5.x release
> since v5.0.2". Ask Composer for `v5.0.3.1` (or `v5.0.2`), never for a bare `^5.0`. And **always name a version for
> Nexus**: the repository keeps the tags of the Netgen media site it was forked from (`3.1.4` to `3.1.6`), so a
> `composer create-project` without a version can pick one of those. Details: [chapter 16.1](../16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations).

### 1.3 What each product has, in the terms of a migration

| | Exponential 6 | Platform 3.2 | DXP 1.x / 2.x | Nexus | Platform Legacy | Basic 2.4 |
|---|---|---|---|---|---|---|
| Content model | classes, attributes, locations | content types, fields, locations (same tables) | as Platform | as its generation | both, one database | its own modules (articles, image catalogue, products) |
| Templates | TPL | Twig | Twig | Twig (and TPL on the legacy lines) | both | its own templates |
| Visual page building | Exponential Layouts (`se7enxweb/explayouts`) | none | `layouts-core` only (no editor UI in `composer.json`) | Netgen Layouts with its editor | through the legacy kernel or Nexus | none |
| Legacy workflows, shop, newsletters, information collection | yes | no | no workflow or scheduler package in `composer.json` | on the legacy lines, through the legacy kernel | yes | its own shop (`eztrade`) |
| REST | v1/v2 (`ezprestapi`), `expservices` | REST v2 | REST v2, GraphQL | as its generation | both | XML-RPC (`index_xmlrpc.php`) |
| Database engines | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | as the platform (MySQL/MariaDB, PostgreSQL) | as the platform; SQLite in the starters | as its generation; SQLite in the starters | shared | MySQL, PostgreSQL, SQLite, Informix (README) |

The column "Platform" lists what the `composer.json` files require. Anything not listed there is not part of the
product, even where the upstream product it was forked from had it.

## 2. Which path fits?

Answer the questions in order; the first "yes" gives your guide.

| Question | If yes |
|---|---|
| Is your site on an older release of this kernel (eZ Publish 3.x, 4.x or 5.x, community 2013.x/2014.x, the 5.90 legacy-only line, or LoveStack)? | [Older releases of this kernel](older-releases.md) |
| Is it on Exponential 6 and you want the Symfony stack (Twig, REST v2/GraphQL) **and** to keep the legacy admin or TPL pages for a while? | [Exponential Platform Legacy](exponential-platform-legacy.md) |
| Do you want a finished site with a visual page builder on the Symfony stack? | [Exponential Platform Nexus](exponential-platform-nexus.md) |
| Do you want a clean, current Symfony project to build on (PHP 8.3+, Symfony 7.4)? | [Exponential Platform DXP](exponential-platform-dxp.md) |
| Are you on Exponential Platform 3.2.x (Symfony only) and want to move to or from it? | [Exponential Platform (Symfony only)](exponential-platform.md) |
| Are you on eZ Platform or Ibexa (any version)? | [Chapter 16](../16-migrating-from-ez-platform-and-ibexa.md), then the Exponential family guide of your target |
| Are you on Exponential Basic (the 2.x line)? | [Exponential Basic](exponential-basic.md) |
| Is the site on WordPress, Drupal, Joomla or Magento? | [WordPress](wordpress.md), [Drupal](drupal.md), [Joomla](joomla.md), [Magento](magento.md) |
| Is it on a hosted builder or shop (Shopify, Wix, Squarespace, GoDaddy)? | [Shopify](shopify.md), [Site builders](site-builders.md) |
| Is it on an enterprise or headless system (Contentful, Adobe AEM)? | [Contentful](contentful.md), [Adobe Experience Manager](adobe-aem.md) |
| Is it hand-made HTML, a static site generator, or a custom application? | [Static HTML](static-html.md), [JavaScript and Node.js apps](javascript-apps.md), [Symfony applications](symfony-apps.md), [ColdFusion applications](coldfusion-apps.md), [Other applications and databases](other-applications.md) |
| Are you leaving Exponential? | [Migrating away from Exponential](leaving-exponential.md) |

Rules of thumb that hold for every path:

- **Content first, presentation second.** Move the content into the target's content model, keep the old site
  running, and build the new templates against the migrated content on a staging copy.
- **One change at a time.** Do not change product, PHP version, database engine and design in one cut-over. Move to
  the closest target first, let it settle, then change the next thing.
- **Keep every old address working.** A migration that loses addresses loses search ranking and links. Every guide
  has a URL section; the [checklist](checklist.md#4-urls-and-redirects) has the steps.
- **Rehearse.** Run the whole migration at least twice on copies before the real cut-over, and time it.

## 3. Migrations within the Exponential family

| Guide | Directions | In short |
|---|---|---|
| [Exponential Platform Legacy](exponential-platform-legacy.md) | 6 to Platform Legacy, and back | The gentlest path: the same database, both kernels side by side |
| [Exponential Platform Nexus](exponential-platform-nexus.md) | 6 to Nexus, and back | Pick the Nexus line by generation; Layouts data needs its own step |
| [Exponential Platform DXP](exponential-platform-dxp.md) | 6 to DXP 1.x or 2.x, and back | Symfony only; XmlText to RichText; templates are rewritten |
| [Exponential Platform (Symfony only)](exponential-platform.md) | 6 to Platform 3.2.9, and back | An older generation; prefer DXP 1.x or Platform Legacy 3.3 for new work |
| [Exponential Basic](exponential-basic.md) | Basic 2.4 to 6, and back | Different data models: a scripted content conversion |
| [Older releases of this kernel](older-releases.md) | eZ Publish 3.x to 5.x, 2013.x to 2017.x, LoveStack to 6 | The database update chain in place, plus PHP 8 porting |

## 4. Migrations from and to other systems

| Guide | Covers |
|---|---|
| [WordPress](wordpress.md) | WXR export, the REST API, WP-CLI; posts, pages, media, categories and tags, authors, comments, redirects |
| [Drupal](drupal.md) | JSON:API, the Migrate API; nodes, taxonomy, media, paths |
| [Joomla](joomla.md) | The Web Services API; articles, categories, tags, menus |
| [Magento and Adobe Commerce](magento.md) | Data Transfer CSV export, REST and GraphQL; catalog content, CMS pages, URL rewrites |
| [Shopify](shopify.md) | Product CSV, the Admin GraphQL API, URL redirects |
| [Wix, Squarespace and GoDaddy](site-builders.md) | What each builder can export, and what has to be read off the live site |
| [Contentful](contentful.md) | `contentful space export`, the Content Delivery and Management APIs |
| [Adobe Experience Manager](adobe-aem.md) | Content packages, the JSON of the Sling GET servlet, the Assets HTTP API |
| [Static HTML sites](static-html.md) | Crawling or reading files, HTML to XmlText, keeping `.html` addresses |
| [JavaScript and Node.js apps](javascript-apps.md) | Next.js and other headless front ends, Markdown/MDX content, using Exponential as the content source |
| [Symfony applications](symfony-apps.md) | Doctrine entities to content classes, and running a Symfony app next to Exponential |
| [ColdFusion applications](coldfusion-apps.md) | Reading the application's database and files |
| [Other applications and databases](other-applications.md) | Spreadsheets, custom PHP, SharePoint-style intranets, any SQL database |
| [Migrating away from Exponential](leaving-exponential.md) | Every export Exponential has, and how to hand content to another system |

## 5. Shared references

| Page | Use it for |
|---|---|
| [The common migration checklist](checklist.md) | Inventory, freeze, mapping, URLs, SEO, testing, cut-over, rollback, sign-off |
| [Exponential's import and export tools](exponential-tools.md) | Every tool that exists, with its real options, and the skeleton of a custom import script |
| [Mapping content to Exponential](content-model-mapping.md) | Classes, datatypes, locations, users, media, translations, URLs and metadata, for any source |
| [Chapter 17: Migration reference](../17-migration-reference.md) | Tables, datatypes against field types, INI against YAML, TPL against Twig, password hashes |

## 6. How the facts on these pages were checked

- **Exponential products**: GitHub repositories of `se7enxweb`, read on 2026-10-18 (default branch, branches,
  releases, tags, `composer.json` of the default branch and of the release tags). Versions move on; check the
  repository again before you pin one.
- **Exponential tools**: the source of this repository and of the extensions it requires in `composer.json`; each
  tool page names the file it was read from.
- **Other systems**: the vendors' own public documentation, linked from each guide. Where a vendor's documentation
  did not confirm a feature, the guide says "not verified" and does not describe it.
- **History**: eZ Publish was the product this kernel descends from; its creator discontinued it after almost 18
  years. The old name appears on these pages only to identify the releases you migrate from.

This page is part of the Exponential documentation and is free documentation under the GNU Free Documentation License
1.3 or any later version ([the license of this documentation](../../README-LICENSE.md)).
