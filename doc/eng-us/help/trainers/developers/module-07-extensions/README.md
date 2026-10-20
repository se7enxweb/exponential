---
title: "Module 7: Extensions"
audience: trainer, developer
category: training
order: 70
summary: The seventh module of the Developers course. Learners map the anatomy of an extension, give theirs metadata and a license, regenerate autoloads and activate extensions, declare dependencies for the loading order, and write a template operator. 3 hours 55 minutes.
keywords: developers course, module 7, extension, ezinfo.php, extension.xml, license, autoloads, ezpgenerateautoloads, ActiveExtensions, Setup Extensions, loading order, dependencies, template operator, eztemplateautoload
type: help
---
# Module 7: Extensions

Since module 1 learners have built `mysite_hello` one piece at a time. This module turns it into a proper
extension: a map of what it holds, its identity and license, the autoloads and activation that make its code
reachable, its place in the loading order, and a template operator as the last piece.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 3 hours 55 minutes (with one 10-minute break) |
| Lessons | 5 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 7.1: The anatomy of an extension](the-anatomy-of-an-extension.md) | 40 minutes | Map every file of `mysite_hello` to its extension point and setting |
| [Lesson 7.2: extension.xml, ezinfo.php and a license](extension-xml-and-ezinfo.md) | 45 minutes | Write the metadata, add a license, check the About page |
| [Lesson 7.3: Autoloads and activation](autoloads-and-activation.md) | 45 minutes | `ezpgenerateautoloads.php -e`, **Setup** > **Extensions**, class not found |
| [Lesson 7.4: Loading order and dependencies](loading-order-and-dependencies.md) | 40 minutes | Read the order, declare `<uses>`, see the real loading position |
| [Lesson 7.5: A template operator](a-template-operator.md) | 55 minutes | Write, register and use `mysite_initials` |

## Preparing this module

- Each learner has `mysite_hello` with the pieces of modules 3 to 6 committed in its own Git repository.
- `xmllint` is installed on the learners' machines (lesson 7.2); check with `xmllint --version`.
- An administrator account for the **Extensions** page and the About page.
- Rehearse lesson 7.3 step 3 (the class not found on purpose) so you can show the debug output on the projector.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 7.1 | 40 |
| Lesson 7.2 | 45 |
| Lesson 7.3 | 45 |
| Break | 10 |
| Lesson 7.4 | 40 |
| Lesson 7.5 | 55 |
| **Total** | **235** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
