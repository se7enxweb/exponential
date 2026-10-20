---
title: "Module 6: From the Symfony-based platforms"
audience: trainer, admin, developer
category: training
order: 60
summary: The module on sites that run eZ Platform or Ibexa - the source generations and the two destinations, path A to Exponential Platform Nexus of the same generation, and path B down to the Exponential 6 legacy kernel or Platform Legacy. Three lessons, 2 hours 30 minutes.
keywords: migrating to exponential, eZ Platform, Ibexa, Symfony, Exponential Platform Nexus, Exponential Platform Legacy, path A, path B, package swap, ezsqldiff, legacy bridge, module
type: help
---
# Module 6: From the Symfony-based platforms

Sites on eZ Platform or Ibexa have two destinations in the Exponential family. This module teaches how to recognise
the source generation, how to choose between the Exponential Platform distributions of the same generation and the
Exponential 6 legacy kernel, and how each path is planned. It follows chapter 16 of the installation book.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 2 hours 30 minutes |
| Lessons | 3 |
| Level | Advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [6.1 From eZ Platform or Ibexa: two destinations and how to choose](two-destinations-and-choosing.md) | 45 minutes | Identify generations; find matching Nexus and Platform Legacy lines; choose a path for two sites |
| [6.2 Path A: to Exponential Platform Nexus](path-a-to-nexus.md) | 45 minutes | Plan a 4.6 move: package swap, command names, landing pages, verification |
| [6.3 Path B: down to the Exponential 6 legacy kernel](path-b-down-to-the-legacy-kernel.md) | 60 minutes | Decide field types; write the six database steps with `ezsqldiff.php` in the right direction |

## Preparing this module

- [ ] Read chapter 16 once in full; check the release lines of chapter 16.1 against the migration hub on the day.
- [ ] One training installation per learner with shell access, for the read-only commands of lesson 6.3.
- [ ] The scenarios of lessons 6.2 and 6.3 printed or in a shared document.
- [ ] Learners should know Composer and Symfony basics for lesson 6.2; mixed groups pair a Symfony developer with an
      administrator.
- [ ] No Nexus or Platform installation is needed: the module is a planning module. If you want to show one, use a
      starter on a separate machine and never point it at a real database.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 6.1 | 45 |
| Lesson 6.2 | 45 |
| Break | 15 |
| Lesson 6.3 | 60 |
| Total | 165 (2 hours 30 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Chapter 16: Migrating from eZ Platform and Ibexa](../../../../install/16-migrating-from-ez-platform-and-ibexa.md):
  the chapter this module teaches from.
- [Chapter 17: Migration reference](../../../../install/17-migration-reference.md): the lookup tables for both paths.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
