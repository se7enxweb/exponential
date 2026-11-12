# Distributions of Exponential

This guide lists every distribution of Exponential that 7x publishes under the GitHub organization
[se7enxweb](https://github.com/se7enxweb): what each one is and who it is for, the PHP versions and databases it
supports, which kernel and administration it runs, what it ships (Exponential Layouts, the media site, demo content),
how to install it, its newest release and how to move on from it. It closes with the site packages that the
Exponential 6 installers offer, the PHP floor packages and the questions people ask most.

Everything here was read from each distribution's own repository: its `composer.json`, its README and its tags and
releases (as of 4 November 2026). When a distribution's own documentation is more detailed, this page links it instead
of repeating it. The installation you are reading this in runs [Exponential 6](#3-exponential-6); [Installing
Exponential 6.0](INSTALL.md) installs it.

> **Exponential and its history.** Exponential continues the code of eZ Publish, a content management system that its
> creator discontinued; the Symfony based lines continue the free eZ Platform and Ibexa Open Source lines. 7x maintains
> all of them for current PHP. "Legacy" in a name means the Exponential 6 kernel running next to a Symfony platform,
> not old software ([Who is the Exponential Foundation?](help/who-is-the-exponential-foundation.md#glossary)).

## Contents

1. [The quick start: which one should I choose?](#1-the-quick-start-which-one-should-i-choose)
2. [All distributions at a glance](#2-all-distributions-at-a-glance)
3. [Exponential 6](#3-exponential-6)
4. [Exponential Platform Legacy](#4-exponential-platform-legacy)
5. [Exponential Platform Nexus](#5-exponential-platform-nexus)
6. [Exponential Platform DXP](#6-exponential-platform-dxp)
7. [Exponential Platform 3.2.9 and Exponential Basic](#7-exponential-platform-329-and-exponential-basic)
8. [The site packages of Exponential 6](#8-the-site-packages-of-exponential-6)
9. [The PHP floor packages](#9-the-php-floor-packages)
10. [Moving between distributions](#10-moving-between-distributions)
11. [Demos and downloads](#11-demos-and-downloads)
12. [Questions and troubleshooting](#12-questions-and-troubleshooting)

## 1. The quick start: which one should I choose?

Answer two questions: do you want to work in PHP and Exponential's templates only, or in Symfony too? And do you want
a framework to build on, or a finished site with demo content to change?

| You want | Choose | Start with |
|---|---|---|
| A CMS in plain PHP, on any database, from PHP 8.0 to 8.5 | [Exponential 6](#3-exponential-6) | `composer create-project se7enxweb/exponential exponential` |
| An existing Exponential 6 site, with Symfony next to it | [Exponential Platform Legacy](#4-exponential-platform-legacy) | the line of the Symfony generation you want |
| A finished Symfony site with Exponential Layouts and demo content | [Exponential Platform Nexus](#5-exponential-platform-nexus) | the line of the platform generation you want, always with a version |
| A new project on Symfony only, without the Exponential 6 kernel | [Exponential Platform DXP](#6-exponential-platform-dxp) | `composer create-project se7enxweb/exponential-platform-dxp-skeleton` |
| To stay on eZ Platform 3.2 with current PHP | [Exponential Platform 3.2.9](#exponential-platform-329) | the `3.2` branch |
| A very small CMS for learning or a personal site | [Exponential Basic](#exponential-basic) | [basic.exponential.earth](https://basic.exponential.earth) |

If you are unsure, start with Exponential 6: it is the line this help center documents, it installs with two commands
on SQLite and it runs on every database Exponential supports.

## 2. All distributions at a glance

| Distribution | Package (type) | Kernel | Symfony | PHP | Layouts | Demo content | Newest release |
|---|---|---|---|---|---|---|---|
| Exponential 6 | `se7enxweb/exponential` (`ezpublish-legacy`) | Exponential 6 | none | 8.0 to 8.5; 7.4 usable without support | yes | through the site packages | `v6.0.14` (7 June 2026); 6.0.15 in development |
| Exponential Platform Legacy | `se7enxweb/exponential-platform-legacy` (`project`) | Symfony platform + Exponential 6 | 3.4, 5.4 or 7.4 by line | by line, 7.4 to 8.5 | on the 4.6 and 5.0 lines | platform installer | `v2.5.0.10`, `v3.3.44.14`, `v4.6.23.9`, `v5.0.3.7` |
| Exponential Platform Nexus | `se7enxweb/exponential-platform-nexus` (`project`) | Symfony platform, + Exponential 6 on the older lines | 3.4, 5.4 or 7.4 by line | by line, 8.0 to 8.5 | yes | yes (media site) | `1.0.0.11`, `v1.1.0.14`, `v1.2.0.5`, `1.3.0.11` |
| CJW Exponential Platform Nexus | `se7enxweb/cjw-exponential-platform-nexus` (`project`) | as Nexus 1.0.0.x | 3.4 | as Nexus 1.0.0.x | yes | yes (CJW) | `1.0.0.16` |
| Exponential Platform DXP | `se7enxweb/exponential-platform-dxp-skeleton` (`project`) on the metapackage `se7enxweb/exponential-platform-dxp` | Symfony platform v5 | 7.4 | 8.3 or later | no editor | platform installer | skeleton `v2.0.0.4`, metapackage `v0.0.0.4` |
| Exponential Platform 3.2.9 | `se7enxweb/exponential-platform` (`project`) | eZ Platform 3.2 kernel | 5.x | 7.3, 8.1 to 8.4 | no | platform installer | `v3.2.9` (28 September 2025) |
| Exponential Basic | `se7enxweb/exponentialbasic` (`project`) | Exponential Basic (2.x line) | none | 8.2 or later | no | its own | `v2.4.0.4` |

All of them are free software under the GNU GPL, version 2 or later. The DXP packages state "GPL-2.0-or-later or
proprietary", the license choice of their upstream.

## 3. Exponential 6

**What it is.** The content management system and application framework that this help center belongs to: a fully
customizable content model, versions and translations, roles and policies, multisite, a shop, workflows, a template
engine, REST and GraphQL, and the administration designs. The package is `se7enxweb/exponential`; the repository is
[se7enxweb/exponential](https://github.com/se7enxweb/exponential).

**Who it is for.** Organizations that run structured content, several sites or several languages from one
installation, hosting providers and agencies, and developers who build on the framework without Symfony.

| | |
|---|---|
| PHP | `composer.json` accepts 7.4 to 8.8; 8.0 is the oldest supported version, 7.4 is usable without support ([Why Exponential supports PHP 7.4](help/why-exponential-supports-php-7-4.md)). The continuous integration runs the tests on PHP 8.1 to 8.5 and checks the syntax and the boot on 8.0 and 7.4. |
| Databases | SQLite, MySQL and MariaDB, PostgreSQL, MongoDB and Oracle (through the [ezoracle](https://github.com/se7enxweb/ezoracle) extension) |
| Serving | Exponential Velocity, the PHP web server the README recommends for every stage, or Apache, nginx, LiteSpeed, Caddy or IIS with PHP-FPM ([Installing, section 8](INSTALL.md#8-serve-the-site-web-server-https-and-permissions)) |
| Administration | the admin4 design and the others, the editor siteaccess, and the AdminUI extension |
| Includes | the extensions the PHP floor package selects for the running PHP version: Exponential Layouts, Exponential Intelligence, the website toolbar expwt and the rest ([Extensions of a new installation](features/6.0/extensions-of-a-new-installation.md)) |
| Content and design | a site package chosen at install time ([section 8](#8-the-site-packages-of-exponential-6)) |
| Newest release | `v6.0.14` (7 June 2026). 6.0.15 is the line in development; the [release overview](release-overview) and the [branch overview](branch-overview.md) name the versions. |

**Install.**

```bash
composer create-project se7enxweb/exponential exponential
cd exponential
php bin/php/console exp:install --apply --random-password --url=http://localhost:8087
```

That installs the default site package `sevenx_multisite` on SQLite. Every option, the setup wizard and the
kickstarter are in [Installing Exponential 6.0](INSTALL.md); the settings afterwards in
[Configuring Exponential 6.0](CONFIGURING.md).

**Upgrade path.** Within the 6.0 line: [Updating Exponential 6.0](UPDATE.md). To add Symfony later without leaving the
installation: [Exponential Platform Legacy](#4-exponential-platform-legacy) and the [legacy bridge](features/6.0/legacy-bridge.md).

## 4. Exponential Platform Legacy

**What it is.** A Symfony platform with a full Exponential 6 inside: two kernels sharing one database. Its
`composer.json` requires `se7enxweb/exponential` (on the 2.5 line directly, on the newer lines through the legacy
bridge), so the Exponential administration and its templates run next to the platform's Symfony
administration (on the 2.5 line at `/legacy_admin/` and `/admin/`). Repository: [se7enxweb/exponential-platform-legacy](https://github.com/se7enxweb/exponential-platform-legacy).

**Who it is for.** Existing Exponential sites that want Symfony next to them and move forward step by step, and teams
that need both stacks at once.

There is one line per Symfony platform generation. You choose the line; each has its own branch and tags:

| Line | Branch | Platform kernel | Symfony | Bridge | PHP (`composer.json`) | Newest tag |
|---|---|---|---|---|---|---|
| 2.5 | `master` | eZ Platform 2.5 (`se7enxweb/ezpublish-kernel ~7.5`) | 3.4 LTS | `se7enxweb/legacy-bridge ^2.1` | `^7.1.3 \|\| ^8.0`; the README: 8.0 or later, 8.0 to 8.2 tested | `v2.5.0.10` |
| 3.3 | `3.x` | eZ Platform 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `se7enxweb/legacy-bridge ^3.0` | `^8.0` | `v3.3.44.14` |
| 4.6 | `4.6.x` | Ibexa Open Source 4.6 (`se7enxweb/exponential-platform-dxp` 4.6) | 5.x | `se7enxweb/legacy-bridge ^4.0` | 7.4 to 8.5; the release notes: installs on PHP 8.0 to 8.5, usable on 7.4 without support | `v4.6.23.9` |
| 5.0 | `5.x` | Platform v5 (`se7enxweb/exponential-platform-dxp`) | 7.4 | `se7enxweb/legacy-bridge ^5.0` | 8.3 or later | `v5.0.3.7` |

- **Exponential Layouts.** The 4.6 line requires it (`se7enxweb/layouts-core`, `netgen/layouts-ibexa`,
  `netgen/layouts-standard`). On the 5.0 line it is suggested and added by `composer create-project` on PHP 8.4 and
  later; `EXP_WITHOUT_NETGEN_LAYOUTS=1` leaves it out. The 2.5 and 3.3 lines do not ship it.
- **Databases** (README of the 2.5 line): MySQL 5.7 or later, MariaDB 10.0 or later, PostgreSQL 9.5 or later, and
  SQLite 3.35 or later for development and testing ([SQLite install of the platform](features/6.0/platform-sqlite-install.md)).
- **Install** (2.5 line, from its README): `composer create-project --ignore-platform-req=php+
  se7enxweb/exponential-platform-legacy:~2.5.0.8 exponential_website`, then the steps of the README. The other lines:
  the README and the installation guide of their branch.

## 5. Exponential Platform Nexus

**What it is.** A finished website project: the Symfony platform with the Netgen media site (its site bundle, designs
and demo content), Exponential Layouts, Tags and the Netgen site API, and on the older lines Exponential 6 next to it.
Repository: [se7enxweb/exponential-platform-nexus](https://github.com/se7enxweb/exponential-platform-nexus); its own book
(`doc/book/`) covers every line in detail, and [Exponential Platform Nexus: a finished site in minutes](features/6.0/platform-nexus-starter.md)
is the overview here.

**Who it is for.** Agencies and teams that want a complete website with demo content, a page builder and an
administration in minutes, and then change it into their own.

| Line | Platform generation | Symfony | PHP | Exponential 6 kernel | Newest tag |
|---|---|---|---|---|---|
| 1.0.0.x | eZ Platform 2.5 | 3.4 | 8.1 or later in practice (the kernel's minimum); Composer with `--ignore-platform-req=php+` | yes | `1.0.0.11` |
| 1.1.0.x | eZ Platform 3.3 | 5.4 | 8.0 or later, 8.5 recommended | yes | `v1.1.0.14` |
| 1.2.0.x | Ibexa Open Source 4.6 | 5.4 | 8.2 or later, 8.5 recommended | yes | `v1.2.0.5` |
| 1.3.0.x (current) | Platform v5 (DXP 5 OSS) with Exponential Layouts 2, SQLite as the zero-configuration default | 7.4 | 8.4 or later | no | `1.3.0.11` |

The default branch `master` carries upstream-style `v2.5.0.x` tags (up to `v2.5.0.9`); it is not a Nexus line and not
the `1.0.0.x` branch, so never install from it. Start new projects on 1.3.0.x.

- **Databases** (the book, chapter 2): MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ or SQLite 3.35+ on 1.1.0.x to 1.3.0.x;
  MySQL 5.7+ or MariaDB 10.2+, PostgreSQL and SQLite on 1.0.0.x (the eZ Platform 2.5 generation).
- **Install.** Clone the branch and check out the newest tag of the line, or use `composer create-project` and **always
  give a version** (for example `se7enxweb/exponential-platform-nexus:1.3.0.11`): without one, Composer installs the
  upstream Netgen media site, which Packagist lists under the same name.
- **Flavors.**
  - **CJW Exponential Platform Nexus** ([se7enxweb/cjw-exponential-platform-nexus](https://github.com/se7enxweb/cjw-exponential-platform-nexus),
    newest `1.0.0.16`): the 1.0.0.x stack with the CJW demo content and the Composer vendor directory included.
  - **CJW Exponential Platform Nexus Starter** ([se7enxweb/cjw-exponential-platform-nexus-starter](https://github.com/se7enxweb/cjw-exponential-platform-nexus-starter),
    newest tag `1.0.0.7`): a ready-to-run copy tuned for SQLite. Install it with `git clone`.
  - **Exponential Platform Nexus Starter** ([se7enxweb/exponential-platform-nexus-starter](https://github.com/se7enxweb/exponential-platform-nexus-starter),
    tag `1.0.0.0`): the Platform v5 site as a repository of its own, PHP 8.4 or later, "Currently In Public Beta" in its
    description. Install it with `git clone`.

## 6. Exponential Platform DXP

**What it is.** A Symfony-only digital experience platform: the free continuation of the eZ Platform and Ibexa Open
Source line, maintained by 7x. The skeleton
[se7enxweb/exponential-platform-dxp-skeleton](https://github.com/se7enxweb/exponential-platform-dxp-skeleton) is the
project you create; it requires the metapackage
[se7enxweb/exponential-platform-dxp](https://github.com/se7enxweb/exponential-platform-dxp). Overview here:
[the DXP skeleton](features/6.0/platform-dxp-skeleton.md).

**Who it is for.** New Symfony projects, and teams leaving eZ Platform or Ibexa Open Source who want current PHP.

- **Platform v5 (the default branch).** Symfony 7.4 LTS, PHP 8.3 or later (8.3 or 8.5 recommended), the admin UI at
  `/adminui/`, REST, GraphQL, the HTTP cache, Solr and core search. The skeleton's description says it provides Ibexa
  DXP OSS 5.0.6. Databases (README): MySQL 8.0+, MariaDB 10.3+, PostgreSQL 14+ and SQLite 3.35+ for development and
  testing.
- **4.6.** The branch `4.6` of the skeleton and of the metapackage: PHP 7.4 to 8.5 in `composer.json`, Symfony 5. It
  stays on Flysystem 2 (`se7enxweb/flysystem ^2.5`), a limit of that generation.
- **What it does not ship.** No layouts editor, no approval workflow or scheduler, and no Exponential 6 kernel. For
  Exponential 6 next to Symfony choose [Legacy](#4-exponential-platform-legacy) or [Nexus](#5-exponential-platform-nexus).
- **Install.** `composer create-project se7enxweb/exponential-platform-dxp-skeleton exponential_website`, then the
  steps of its README.

## 7. Exponential Platform 3.2.9 and Exponential Basic

### Exponential Platform 3.2.9

[se7enxweb/exponential-platform](https://github.com/se7enxweb/exponential-platform), branch `3.2`, newest release
`v3.2.9` (28 September 2025): a Symfony-only platform built on the last free eZ Platform 3.2 kernel
(`se7enxweb/ezplatform-kernel`), PHP 7.3 and 8.1 to 8.4. Its description states "No Exponential 6.x Legacy Support"
and points to Exponential Platform Legacy for that. For teams that run eZ Platform 3.2 and want to stay on it.

### Exponential Basic

[se7enxweb/exponentialbasic](https://github.com/se7enxweb/exponentialbasic), newest release `v2.4.0.4`: the small CMS
of the 2.x line, "for everyone, educational purposes, personal use" in its description, on PHP 8.2 or later with MySQL,
PostgreSQL, SQLite or Informix. It is a code base of its own, not a version of Exponential 6, and has its own
website, [basic.exponential.earth](https://basic.exponential.earth).

## 8. The site packages of Exponential 6

Exponential 6 itself ships no site; the installer installs one **site package** from the
[package server](https://exponential.packages.exponential.earth/): its content classes, demo content, design and
siteaccess settings. They belong to Exponential 6 only; the Symfony distributions have their own installers. Since
6.0.15 every site package only switches extensions on, and Composer installs the extensions themselves
([Site packages no longer install extension packages](bc/6.0/site-packages-no-extension-packages.md)).

| Site package | What you get | Without demo content |
|---|---|---|
| `sevenx_multisite` (the default) | 7x MultiSite: the official default design of Exponential 6.0.15, several sites in one installation | `sevenx_multisite_clean` |
| `sevenx_site` | 7x Simple Website Interface: one site | `sevenx_site_clean` |
| `cjw_multisite` | CJW MultiSite: the design of CJW Network | |
| `ezwebin_site` | Website Interface: the classic one-site design, redesigned for 6.0.15 | `ezwebin_site_clean` |
| `ezflow_site` | Flow: pages built from blocks | `ezflow_site_clean` |
| `ezdemo_site` | Exponential Demo Site | `ezdemo_site_clean` |
| `plain_site` | an almost empty site, for building everything yourself | |

All 6.0.15 site packages carry the version `6.0-15-stable`. Choose one with `exp:install --package=<name>`, the
kickstarter's `Site_package=` or the setup wizard's site package step ([Installing](INSTALL.md)). Their sources are
the `-ezpackage` repositories (`sevenx-ezpackage`, `cjw_multisite-ezpackage`, `ezwebin-ezpackage`, `ezflow-ezpackage`,
`ezdemo-ezpackage`), where an extension and its site package are released together.

## 9. The PHP floor packages

`se7enxweb/exponential-php-floor` is a metapackage that Exponential 6 requires instead of listing every extension
itself. Composer picks the line that fits the PHP version it runs on, so one `composer.json` installs on every
supported PHP version:

| Line | PHP | What it selects | Newest |
|---|---|---|---|
| 1.0 | 7.4 (usable without support) | the extension releases that still run on PHP 7.4 | `1.0.6` |
| 1.1 | 8.0 | every extension, including Exponential Layouts, Exponential Velocity and two-step sign-in | `1.1.8` |
| 1.2 | 8.1 to 8.3 | as 1.1 | `1.2.8` |
| 1.3 | 8.4 and later | as 1.2, with the newest releases where they need PHP 8.4 | `1.3.8` |

You never require it yourself: it comes with `se7enxweb/exponential`. Its README
([se7enxweb/exponential-php-floor](https://github.com/se7enxweb/exponential-php-floor)) lists what every release
changed.

## 10. Moving between distributions

| From | To | How |
|---|---|---|
| An older eZ Publish or Exponential site | Exponential 6 | [Upgrading from an older version](INSTALL.md#11-upgrading-from-an-older-version) and [Updating](UPDATE.md) |
| Exponential 6 | Exponential Platform Legacy | the same database and `ezpublish_legacy/`, connected through the [legacy bridge](features/6.0/legacy-bridge.md) |
| Legacy or Nexus, one line | the next line | one generation at a time, following the README of the target branch; for Nexus, chapter 11 of its book ("upgrading between lines") |
| Exponential Platform Legacy 5.0 or Nexus 1.3.0.x | Exponential Platform DXP v5 | the same Platform v5 kernel, without the Exponential 6 kernel |

A move never happens by itself: back up the database and `var/` first, and test on a copy.

## 11. Demos and downloads

| What | Where |
|---|---|
| Exponential 6, the newest code with the 7x design | [demo.exponential.earth](https://demo.exponential.earth/), administration at [edit.demo.exponential.earth](https://edit.demo.exponential.earth/) ([demo mode](help/demo-mode.md)) |
| Exponential 6, rebuilt every four hours from the main branch | [latest.demo.exponential.earth](https://latest.demo.exponential.earth/) |
| The CJW starter site | [site.cjw.demo.exponential.earth](https://site.cjw.demo.exponential.earth/) |
| Exponential Platform (Legacy, Nexus, DXP) | [platform.exponential.earth](https://platform.exponential.earth), demo at [platform.exponential.earth/demo](https://platform.exponential.earth/demo) |
| Exponential Basic | [basic.exponential.earth](https://basic.exponential.earth) |
| Packages | [Packagist, vendor se7enxweb](https://packagist.org/packages/se7enxweb/), the [package server](https://exponential.packages.exponential.earth/) for site packages |

## 12. Questions and troubleshooting

| Question or symptom | Answer |
|---|---|
| Does Exponential Platform DXP have a layouts editor? | No. It ships no layouts editor, approval workflow, scheduler or Exponential 6 kernel. Choose Nexus, or Legacy 4.6 or 5.0, for Exponential Layouts. |
| `composer create-project se7enxweb/exponential-platform-nexus` installed a project named `netgen/media-site` | It ran without a version and installed the upstream media site. Start again with a version, for example `:1.3.0.11`. |
| Composer refuses the 2.5 generation on a current PHP | Use `--ignore-platform-req=php+` as the README says; never switch off Composer's security blocking. |
| A Nexus or Legacy tag is missing on Packagist | Five-part tags (`v1.0.0.0.3`, `1.3.0.0.0`) exist only in Git. Install them from a clone. |
| Exponential Layouts is missing on Legacy 5.0 | It is added only on PHP 8.4 and later, and not when `EXP_WITHOUT_NETGEN_LAYOUTS=1` is set. |
| Nexus 1.3.0.x has no `/legacy_admin/` | That line runs Platform v5 without the Exponential 6 kernel. Use 1.2.0.x or earlier for both. |
| Which PHP for Exponential 6? | The newest you can run, 8.5 included; 8.0 is the oldest supported. Composer chooses the matching floor line by itself. |
| Can I use my site package on a Symfony distribution? | No. Site packages are for the Exponential 6 installers; the platforms install their content with their own commands. |
| Is "Legacy" old software? | No. It is the Exponential 6 kernel running next to a Symfony platform, and both are maintained. |

## Related pages

- [Installing Exponential 6.0](INSTALL.md), [Configuring Exponential 6.0](CONFIGURING.md), [Updating Exponential 6.0](UPDATE.md)
- [Who is the Exponential Foundation?](help/who-is-the-exponential-foundation.md): every product of the family, with its sources
- [Knowing the product family](help/advocates/knowing-the-product-family.md): one sentence per product
- [The Exponential platform ecosystem](history/ecosystem.md)
- [Exponential Competition](competition/README.md): Exponential compared with proprietary DXPs, SaaS and the systems in every language
