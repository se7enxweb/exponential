---
title: "Module 1: Planning a migration"
audience: trainer, admin, developer, project-manager
category: training
order: 10
summary: The planning module of the migration course - the Exponential family and how to choose a path, the inventory and scope, the phases of the common checklist, and rehearsal, cut-over and rollback. Four lessons, 3 hours 15 minutes.
keywords: migrating to exponential, planning, migration path, inventory, scope, checklist, rehearsal, cut-over, rollback, module
type: help
---
# Module 1: Planning a migration

Before anyone writes an import script, a migration needs a destination, an inventory, a plan and a way back. This
module teaches the frame that every migration guide of the documentation shares, so that the later modules on
specific sources can build on it.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 3 hours 15 minutes |
| Lessons | 4 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [1.1 The Exponential family and choosing a path](choosing-a-path.md) | 45 minutes | Pick the right migration guide for five described sites |
| [1.2 Inventory and scope](inventory-and-scope.md) | 60 minutes | Take an inventory of their training installation with read-only queries and fill in a scope table |
| [1.3 The phases of a migration and the checklist](phases-and-the-checklist.md) | 45 minutes | Turn the common checklist into a plan with owners and done-when criteria |
| [1.4 Rehearsal, cut-over and rollback](rehearsal-cut-over-and-rollback.md) | 45 minutes | Use maintenance mode and write a cut-over runbook with a rollback section |

## Preparing this module

- [ ] One training installation per learner, installed with `php bin/php/console exp:install` (SQLite), with shell
      access ([The console install](../../../../install/07-console-install.md)).
- [ ] The `sqlite3` command line tool on the training machines (lesson 1.2).
- [ ] The migration hub and the checklist opened once on the projector, and the product versions in the hub checked
      against today's date (lesson 1.1).
- [ ] The five site descriptions of lesson 1.1 and the WordPress scenario of lesson 1.3 printed or in a shared
      document.
- [ ] Maintenance mode tried once on your own training installation (lesson 1.4); remember to switch it off.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 1.1 | 45 |
| Lesson 1.2 | 60 |
| Break | 15 |
| Lesson 1.3 | 45 |
| Lesson 1.4 | 45 |
| Total | 210 (3 hours 15 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Migrating to and from Exponential](../../../../install/migration/README.md): the migration hub this module teaches from.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
