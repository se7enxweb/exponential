---
title: Designing hosting plans
audience: hosting
category: hosting
order: 20
summary: How to build Exponential hosting plans from technical building blocks: engine, database, storage, backups, caching, support and services; plan templates for shared, VPS, dedicated and managed offers, with limits taken from your own measurements rather than invented figures.
keywords: hosting plans, packages, plan design, shared, VPS, dedicated, managed, limits, quotas, workers, database, storage, backups, staging, add-ons, product design
type: help
---
# Designing hosting plans

This page is for product managers and system administrators who together design the plans of an Exponential
hosting offer. It answers: what are the building blocks of a plan, which technical choices sit behind each block,
how do you set the limits of a plan, and what do typical plan families look like?

The page gives structure, not prices. Your costs, your hardware and your market decide the figures; the project's
sources publish none, and this help does not invent any.

## Contents

1. [The building blocks](#the-building-blocks)
2. [Technical choices behind each block](#technical-choices-behind-each-block)
3. [Setting limits from measurements](#setting-limits-from-measurements)
4. [Plan families](#plan-families)
5. [Add-ons](#add-ons)
6. [A plan sheet to copy](#a-plan-sheet-to-copy)
7. [Common mistakes](#common-mistakes)

## The building blocks

Every Exponential hosting plan is a combination of these blocks. Write down, for each plan, what it includes.

| Block | The question it answers | Options |
|---|---|---|
| Engine | What serves the site? | Exponential Velocity, FrankenPHP through Velocity, Apache or nginx with PHP-FPM |
| PHP | Which versions, who chooses? | the supported range 8.0 to 8.5 (Velocity included) |
| Database | Which engine, shared or dedicated? | in the official order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Storage | How much space for `var/storage` (uploads and image variations), caches and logs? | a quota per plan |
| Isolation | What separates this customer from others? | a system user and PHP-FPM pool per site, a VPS, a container, a dedicated machine |
| Backups | What is backed up, how often, kept how long, restored how? | database plus `var/storage`, `settings/override`, `settings/siteaccess` |
| Caching | Which layers are switched on and tuned? | Velocity's response cache, HTTP cache headers, reverse proxy, CDN, Valkey |
| Environments | Is there a staging copy? | production only; production plus staging; several |
| Updates | Who applies Exponential updates, and when? | customer; host on request; host on a schedule |
| Support | Who answers what, how fast? | see [Support tiers](support-tiers.md) |
| Services | What work is offered beyond hosting? | installation, migration, development, training |

## Technical choices behind each block

### Engine

[Chapter 8](../../../install/08-serving-the-site.md) recommends Exponential Velocity "for every stage" and calls
FrankenPHP a production-ready alternative. Velocity is a long-running process; on shared hosting that forbids
long-running processes, plans use Apache or nginx with PHP-FPM instead. A plan family can therefore split along this
line:

- **shared plans** on Apache or nginx with one PHP-FPM pool per customer;
- **VPS, dedicated and managed plans** on Velocity, with its own worker pool per site.

Details: [Choosing a web server](../servers/choosing-a-web-server.md), [Velocity for hosts](../servers/velocity-for-hosts.md).

### Database

SQLite 3 is the default and, with the driver Exponential ships, "a full production database for a site served from
one machine" ([Database support](../../database-support.md#1-sqlite-3-the-default)). It needs no database server, so
it costs a host nothing extra to provide. A plan that promises several web nodes needs a database server, PostgreSQL
first ([Databases in the official order](../basics/databases-in-the-official-order.md)).

### Storage and backups

A complete backup "is the database and the files": `var/storage`, `settings/override` and `settings/siteaccess`
([Database support](../../database-support.md#backup-and-restore)). Size the backup storage of a plan from those
directories, not from the whole installation; the code itself can be fetched again from Git or Composer.
See [What to back up](../operations/backups-what-to-back-up.md).

### Isolation

Plans that share a machine need operating-system isolation between customers. Chapter 8 names this as a reason to
keep a traditional web server: "when several customers on one machine need operating-system isolation from each
other". See [Multi-tenant isolation](../security/multi-tenant-isolation.md).

## Setting limits from measurements

The installation book gives few hard numbers and says so: "the documentation gives few hard numbers, and this book
does not invent others" ([2.10](../../../install/02-requirements.md#210-memory-cpu-and-disk)). What it does state:

| Item | Value | Source |
|---|---|---|
| PHP `memory_limit` | at least 64M | Chapter 2 |
| One Velocity worker | about 10 MB of private memory (measured) | Chapter 2, 2.10 |
| Velocity pool | `Workers=4` shipped | `settings/velocity.ini` |

Everything else comes from your own measurements. A method:

1. Install a reference site on the hardware the plan will use (a site package with demo content is a fair start, see
   [Site packages](../provisioning/site-packages.md)).
2. Measure it with the benchmark command ([Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md))
   with the caches cold and warm.
3. Watch memory per worker or per PHP-FPM child, database size and `var/` growth for a representative period.
4. Set each plan limit with headroom over what you measured, and write down how you measured it.
5. Measure again after each Exponential minor release and each PHP upgrade.

> **Tip**
> Put the measurement method into the plan sheet. When a customer asks why a plan has the limit it has, the answer
> is the measurement, not a guess.

## Plan families

Templates, not recommendations. Replace every `<...>` with your own figure.

| | Shared | VPS | Dedicated | Managed |
|---|---|---|---|---|
| Engine | Apache or nginx + PHP-FPM | Velocity (or traditional) | Velocity | Velocity, tuned by the host |
| Database | SQLite 3, or a database on a shared server | SQLite 3 or a server on the VPS | any of the six | chosen with the customer |
| Isolation | system user + PHP-FPM pool | virtual machine | machine | per the contract |
| Shell access | `<yes/no>` | yes | yes | for the host; customer `<yes/no>` |
| Storage quota | `<GB>` | `<GB>` | `<disk>` | `<GB>` |
| Backups | `<daily>`, kept `<n>` days | `<...>` | `<...>` | `<...>`, restore tested `<monthly>` |
| Staging copy | `<no>` | `<optional>` | `<optional>` | `<included>` |
| Updates | customer | customer, or host on request | customer | host, on a schedule |
| Support | `<tier>` | `<tier>` | `<tier>` | `<tier>` |

## Add-ons

Options a host can sell separately, each backed by a feature in the sources:

| Add-on | What it is based on |
|---|---|
| Staging environment | a second installation from the same Git checkout and a copy of the data; see [Deploying changes](../operations/deploying-changes.md) |
| Managed updates | the ezupdate extension and the upgrade chapter, see [Automated updates with ezupdate](../operations/automated-updates-with-ezupdate.md) |
| Extended backup retention | your backup system; see [Backup schedules and retention](../operations/backup-schedules-and-retention.md) |
| CDN | see [CDN](../performance/cdn.md) |
| Search server | see [Search backends](../basics/search-backends.md) |
| Migration service | see [Migrating customers to your hosting](../migrations/migrating-customers-to-your-hosting.md) |
| Security review | see [Security review checklist](../checklists/security-review.md) |

## A plan sheet to copy

```text
Plan name:            <name>
Audience:             <who it is for>
Engine:               <Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM>
PHP versions:         <list>; who chooses: <host | customer>
Database:             <engine, version>; dedicated or shared: <...>
Isolation:            <system user + pool | VM | container | machine>
Limits:               storage <...>, workers or FPM children <...>, database size <...>
  measured on:        <hardware>, <date>, method: <benchmark command, content set>
Backups:              <what>, <how often>, kept <how long>, stored <where>, restore tested <how often>
Environments:         <production | + staging>
Updates:              <who>, <when>, announced <how>
Support tier:         <tier name>  (see the support tiers sheet)
Add-ons available:    <list>
Not included:         <list, for example custom development>
```

## Common mistakes

- **Promising a capacity you have not measured.** Measure first; see above.
- **Leaving restore out of the plan.** A backup plan without a restore test is incomplete.
- **Offering Velocity on a platform that stops long-running processes.** Check before you sell it.
- **Mixing customers in one PHP-FPM pool.** Give each site its own system user and pool.
- **Calling a plan by the old product name.** The product is Exponential; see
  [Brand and naming rules](../../business/brand-and-naming-rules.md).

## Related pages

- [SLA options](sla-options.md)
- [Support tiers](support-tiers.md)
- [Billing and metering options](billing-and-metering-options.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md)
- [Sizing memory, CPU and disk](../basics/sizing-memory-cpu-and-disk.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
