---
title: Migrating customers from eZ Platform and Ibexa
audience: hosting
category: hosting
order: 40
summary: For hosts whose customers run eZ Platform or Ibexa DXP/OSS: the two Exponential destinations (Exponential Platform Nexus and the Exponential 6.0 legacy kernel), what each means for the hosting stack, how to assess the effort, and where chapter 16 of the installation book has the steps.
keywords: migration, eZ Platform, Ibexa, DXP, OSS, Symfony, Exponential Platform Nexus, Exponential Platform Legacy, Exponential 6.0, legacy kernel, RichText, Page Builder, path A, path B, hosting stack
type: help
---
# Migrating customers from eZ Platform and Ibexa

This page is for hosting companies whose customers run a Symfony-based site on eZ Platform 1.x to 3.x or Ibexa DXP
or OSS 3.3 to 5.0, and want to move to an Exponential product on the host's platform. It answers: which destinations
exist, what each one needs from your servers, and how to assess the job before you offer it. The steps themselves
are in [Chapter 16](../../../install/16-migrating-from-ez-platform-and-ibexa.md).

## Contents

1. [Two destinations](#two-destinations)
2. [What each destination needs from the host](#what-each-destination-needs-from-the-host)
3. [Choosing with the customer](#choosing-with-the-customer)
4. [Assessment](#assessment)
5. [Phases](#phases)
6. [Hosting both during a transition](#hosting-both-during-a-transition)

## Two destinations

From [16.1](../../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations):

| | Path A: Exponential Platform Nexus | Path B: Exponential 6.0 (alone or as Exponential Platform Legacy) |
|---|---|---|
| What it is | "the like-for-like path: the same Symfony stack, the same database schema generation and the same PHP namespaces, with the packages swapped for the se7enxweb forks" | the legacy kernel of this repository; content moves to the INI and TPL world |
| Effort | the lowest risk: "same generation, a package swap" | the highest effort: "a data and template migration" |
| Custom Symfony bundles and Twig | kept | rewritten as legacy extensions and TPL templates |

Rules of thumb from [16.2](../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path): same
generation first; path B is "a rewrite of the presentation and of custom code, not of the content"; and do not mix the
paths in one cut-over.

## What each destination needs from the host

| | Path A (Symfony platform) | Path B (Exponential 6.0) |
|---|---|---|
| PHP | depends on the Nexus line chosen; chapter 16 lists the PHP of each | 8.0 to 8.5 (Velocity included) |
| Web server | a Symfony front controller under Apache or nginx with PHP-FPM | Velocity, FrankenPHP, or Apache/nginx with PHP-FPM ([Choosing a web server](../servers/choosing-a-web-server.md)) |
| Database | as the source platform used | the six of Exponential, in order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Search | as the source platform used | see [Search backends](../basics/search-backends.md) |
| Shell and Composer | required | recommended ([Composer and Git on servers](../basics/composer-and-git-on-servers.md)) |

> **Note**
> The hosting pages of this section describe Exponential 6.0. For a path A site, read the requirements of the
> Exponential Platform line chosen in chapter 16 and its repository; do not assume the 6.0 requirements apply.

## Choosing with the customer

Questions that decide the path, from chapter 16's table:

1. Does the site have custom Symfony bundles, Twig templates, REST or GraphQL clients the customer wants to keep?
   Then path A.
2. Does it use RichText content or Page Builder landing pages? Path A keeps RichText; Page Builder has no equivalent
   in either path and is rebuilt with Exponential Layouts, on Nexus (A) or in the legacy kernel (B).
3. Does the customer's team know INI, TPL and legacy extensions? Then path B is familiar ground.
4. Is lowest risk for a live site the priority? Then path A, same generation.

## Assessment

What to collect before an offer ([16.4](../../../install/16-migrating-from-ez-platform-and-ibexa.md#164-before-you-start-inventory-freeze-and-backups)):

| Item | Why |
|---|---|
| Source product and exact version | decides the generation and the Nexus line |
| `composer.json` and `composer.lock` | the packages to swap or replace |
| Custom bundles, templates and their size | the main effort driver |
| Field types in use, especially those without a counterpart on the target | data conversion work |
| Size of the content tables | decides the database and reindex time |
| Search engine in use | must be provided on your side |

Chapter 16 names the three numbers that drive the duration: "the number of custom bundles and templates, the number of
fields of types without a counterpart on the target, and the size of the content tables"
([16.10](../../../install/16-migrating-from-ez-platform-and-ibexa.md#1610-a-plan-and-its-phases)). Measure the last one
with a full dry run on a copy.

## Phases

| Phase | Host's part |
|---|---|
| 1. Inventory and decision | collect the items above; agree the path with the customer |
| 2. Source to the last release of its line | usually on the customer's current host |
| 3. Code | a staging server on your platform with the target's stack |
| 4. Data | the database copy on your staging server |
| 5. Timed dry run | your staging server; write down every duration |
| 6. Freeze, cut-over, verification | maintenance window; checks of [16.8](../../../install/16-migrating-from-ez-platform-and-ibexa.md#168-verify-the-migration) |
| 7. Decision window, then cleanup | keep the rollback ([16.9](../../../install/16-migrating-from-ez-platform-and-ibexa.md#169-rollback)) for the agreed time |

## Hosting both during a transition

Some Exponential Platform lines and Exponential Platform Legacy run the legacy kernel and a Symfony stack on one
database ([16.2](../../../install/16-migrating-from-ez-platform-and-ibexa.md#162-choosing-a-path), last row). For a
host that means one site with two front controllers and one database; size and monitor it as one site with the
needs of both.

## Related pages

- [Migrating customers to your hosting](migrating-customers-to-your-hosting.md)
- [From older Exponential versions](from-older-exponential-versions.md)
- [Chapter 17: Migration reference](../../../install/17-migration-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
