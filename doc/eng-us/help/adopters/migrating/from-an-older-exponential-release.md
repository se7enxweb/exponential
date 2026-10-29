---
title: From an older Exponential release
audience: adopter, admin, developer, hosting, customer
category: adoption
order: 41
summary: How an adopter with an older Exponential site moves to the current 6.0 line - from an older 6.0.x release, from 5.4 or the 4.x and 3.x lines of the eZ Publish years, and from 5.x on the Symfony stack - why to move now, what drives the effort, and the chapters of the installation book.
keywords: upgrade, older release, 6.0.x, 5.x, 4.x, 3.x, eZ Publish years, migration, installation book, security fixes, PHP 8
type: help
---
# From an older Exponential release

Sites built when the software was still called eZ Publish run on the same code base Exponential continues. Moving them
to 6.0 keeps most of the content model and database ([Switching to Exponential](../../businesses/choosing/switching-to-exponential.md)).

## Contents

1. [Why move now](#why-move-now)
2. [Your starting point](#your-starting-point)
3. [What drives the effort](#what-drives-the-effort)
4. [Before you start](#before-you-start)

## Why move now

- "5.x, 4.x, 3.x and older" get no security fixes; 6.0 is the maintained line (`SECURITY.md`).
- Exponential supports PHP 8.0 to 8.5 (PHP 7.4 usable without support) (`README.md`), so the site can move to a supported PHP as well.
- No license fee after the switch (`README.md`).

## Your starting point

| You run | Read |
|---|---|
| An older 6.0.x release | [Upgrading, 11.6 From any 6.0.x to today](../../../install/11-upgrading.md#116-from-any-60x-to-today) |
| 5.4 (or 5.90) | [Upgrading, 11.5 From 5.4 to 6.0.0](../../../install/11-upgrading.md#115-from-54-or-590-to-600) |
| 3.10 to 5.3 | [Upgrading, 11.4 From 3.10 to 5.3](../../../install/11-upgrading.md#114-from-310-to-53-the-old-chain) |
| 4.x | [Migrating from 4.x](../../../install/14-migrating-from-4x.md) |
| 5.x with the legacy kernel | [Migrating from 5.x legacy](../../../install/15-migrating-from-5x-legacy.md) |

The hosting view: [Migrating customers from older versions](../../hosting/migrations/from-older-exponential-versions.md).

## What drives the effort

From [Switching to Exponential](../../businesses/choosing/switching-to-exponential.md#what-drives-the-effort):
the size of the content tables, the number of stored files, your own extensions and PHP code ("porting them to PHP 8
grows with lines of your own code, not with database size"), templates and design, search reindexing, URL aliases per
language, and integrations.

## Before you start

- Find out which version you run ([Upgrading, 11.2](../../../install/11-upgrading.md#112-which-version-do-you-run)).
- List your own extensions and check them for PHP 8 ([Part 4: Upgrading](../../extension-development/part-4-upgrading/README.md)).
- Make and test a backup ([Restore test](../../customers/healthy/restore-test.md)).
- Plan a rehearsal on a copy ([Content migration checklist](content-migration-checklist.md)).

## Related pages

- [Migrating](README.md)
- [Upgrading](../../../install/11-upgrading.md)
- [Upgrade day checklist](../../customers/healthy/upgrade-day-checklist.md)
- [Taking over a site](../../customers/partners-and-hosts/taking-over-a-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
