---
title: "Module 3: Import and export tools"
audience: trainer, admin, developer
category: training
order: 30
summary: The tools module of the migration course - choosing the right import or export tool, importing with xrowextract with dry runs and remote id matching, and writing a custom import script on the kernel API. Three lessons, 2 hours 45 minutes.
keywords: migrating to exponential, import, export, xrowextract, ezcsvimport, packages, import script, ezexec, remote id, dry run, module
type: help
---
# Module 3: Import and export tools

With the mapping written, the content has to get in. This module teaches the tools Exponential already has, the way
of working that makes an import repeatable, and, for developers, the skeleton of a custom import script for sources
no tool reads.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 2 hours 45 minutes |
| Lessons | 3 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [3.1 Choosing the right import or export tool](choosing-the-right-tool.md) | 45 minutes | List classes and export columns with xrowextract; match five sources to their tools |
| [3.2 Importing with xrowextract: dry runs and repeatable imports](importing-with-xrowextract.md) | 60 minutes | Export a class, dry-run and apply an import, and prove that it does not duplicate |
| [3.3 A custom import script](a-custom-import-script.md) | 60 minutes | Run the documentation's skeleton with three items, twice, and see updates instead of duplicates |

## Preparing this module

- [ ] One training installation per learner with shell access; check that
      `php bin/php/console ext:xrowextract:csv --list-classes --node=2` works on it (xrowextract installed and
      active).
- [ ] Each learner has a course folder with at least one article, and knows its node id (lessons 3.2 and 3.3).
- [ ] A plain text editor on the training machines; no spreadsheet program for the CSV.
- [ ] Lesson 3.3 is for developers. Non-developers can do lesson 3.2 again with a second class, or read the
      [tools page](../../../../install/migration/exponential-tools.md) section 2 on exports.
- [ ] Run lesson 3.3 once yourself; note the locale code and the identifiers of the **Folder** class of your
      training site package.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 3.1 | 45 |
| Lesson 3.2 | 60 |
| Break | 15 |
| Lesson 3.3 | 60 |
| Total | 180 (2 hours 45 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Exponential's import and export tools](../../../../install/migration/exponential-tools.md): the page this module
  teaches from.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
