---
title: "Module 2: Mapping content to Exponential"
audience: trainer, admin, developer
category: training
order: 20
summary: The mapping module of the migration course - classes, datatypes and the content tree; users, roles and passwords; URLs, redirects and search engine metadata. Three lessons, 2 hours 45 minutes.
keywords: migrating to exponential, content model mapping, classes, datatypes, remote id, users, passwords, url alias, redirects, url wildcard, seo, module
type: help
---
# Module 2: Mapping content to Exponential

Every source ends in the same building blocks. This module teaches learners to write the mapping before the code:
which class and datatype each field becomes, where each item sits in the tree, what happens to users and their
passwords, and how every old address keeps working.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Duration | 2 hours 45 minutes |
| Lessons | 3 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [2.1 Classes, datatypes and the content tree](classes-datatypes-and-the-tree.md) | 60 minutes | Read a class in the admin and write a mapping table for a WordPress post |
| [2.2 Users, roles and passwords](users-roles-and-passwords.md) | 45 minutes | Read hash types with read-only queries and decide between import and reset for four sources |
| [2.3 URLs, redirects and search engine metadata](urls-redirects-and-seo.md) | 60 minutes | Create, try and remove a URL wildcard; write and test a redirect table |

## Preparing this module

- [ ] One training installation per learner with an administrator account and shell access; the `sqlite3` tool
      installed.
- [ ] The class **Article** (or a similar class) present on the training site package; check its attributes once.
- [ ] The training site answers on a known address, so learners can request it with `curl` (lesson 2.3).
- [ ] The WordPress mapping and address tables of [the WordPress guide](../../../../install/migration/wordpress.md)
      available to learners.
- [ ] At the end of the module, check that every learner removed the wildcard of lesson 2.3.

## Timing plan

| Block | Minutes |
|---|---|
| Lesson 2.1 | 60 |
| Lesson 2.2 | 45 |
| Break | 15 |
| Lesson 2.3 | 60 |
| Total | 180 (2 hours 45 minutes of lessons, plus a 15-minute break) |

## Related pages

- [Migrating to Exponential: course overview](../README.md)
- [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md): the page this module
  teaches from.
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
