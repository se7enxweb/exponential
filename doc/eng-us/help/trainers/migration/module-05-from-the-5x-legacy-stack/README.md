---
title: "Module 5: From the 5.x legacy stack"
audience: trainer, admin, developer
category: training
order: 50
summary: The module on moving a site of the 5.x era (the dual-kernel stack with Symfony and the legacy kernel) to Exponential 6 - recognising the variant, extracting the legacy part, writing the injected configuration as INI, and handling field types, image variations and Twig templates. Three lessons, 2 hours 45 minutes.
keywords: migrating to exponential, 5.x, dual kernel, ezpublish_legacy, extraction, ezpublish.yml, INI, siteaccess, ezrichtext, image variations, Twig to TPL, module
type: help
---
# Module 5: From the 5.x legacy stack

Sites of the 5.x era ran a Symfony stack next to the legacy kernel, on one database. Exponential continues the
legacy kernel, so this migration takes the legacy part out of the Symfony application and writes down what the
Symfony side used to provide. The module follows chapter 15 of the installation book; the database steps are those
of module 4.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 2 hours 45 minutes |
| Lessons | 3 |
| Level | Advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [5.1 Which 5.x variant, and what carries over](which-variant-and-what-carries-over.md) | 45 minutes | Identify a variant from its files; list Exponential's own tables; decide the fate of three Symfony bundles |
| [5.2 Extracting the legacy part and writing the configuration as INI](extracting-legacy-and-writing-ini.md) | 60 minutes | Read where settings come from with `exp:ini`; translate YAML siteaccess matching and parameters into INI |
| [5.3 Field types, image aliases and Twig templates](field-types-images-and-templates.md) | 60 minutes | Compare datatypes; write an image alias and an override block; rewrite Twig as TPL; convert a DocBook fragment |

## Preparing this module

- [ ] Module 4 taught first, or at least lesson 4.2: the database chain is the same from 5.4 on.
- [ ] One training installation per learner with shell access; `sqlite3` installed.
- [ ] The YAML snippets of lessons 5.2 and 5.3 printed or in a shared document.
- [ ] If you have one, the root listing of an old 5.x project (without credentials) to show on the projector in
      lesson 5.1.
- [ ] Lesson 5.3 is for developers and theme designers; administrators can work on the INI part of lesson 5.2 again
      for a second siteaccess instead.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 5.1 | 45 |
| Lesson 5.2 | 60 |
| Break | 15 |
| Lesson 5.3 | 60 |
| Total | 180 (2 hours 45 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Chapter 15: Migrating from the 5.x legacy stack](../../../../install/15-migrating-from-5x-legacy.md): the chapter
  this module teaches from.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
