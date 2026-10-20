---
title: "Module 5: Fetch functions"
audience: trainer, developer
category: training
order: 50
summary: The fifth module of the Developers course. Learners fetch nodes and lists, filter, sort and page them, use the fetches of the user, section and relation functions, and write a fetch function of their own. 3 hours 30 minutes.
keywords: developers course, module 5, fetch, content list, tree, list_count, attribute_filter, class_filter, sort_by, paging, navigator, has_access_to, current_user, function_definition.php, function collection
type: help
---
# Module 5: Fetch functions

Templates get their data through fetch functions. This module goes from the first `fetch( 'content', 'node', ... )`
to a paged, filtered list on a real page, through the fetches of other kernel modules, and ends with learners
writing a fetch function in their own extension.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 3 hours 30 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 5.1: Fetching nodes and lists](fetching-nodes-and-lists.md) | 45 minutes | `node`, `list`, `list_count`, reading `function_definition.php` |
| [Lesson 5.2: Filters, sorting and paging](filters-sorting-and-paging.md) | 55 minutes | Class and attribute filters, `tree` and `depth`, `sort_by`, the navigator |
| [Lesson 5.3: Fetches of other modules](fetches-of-other-modules.md) | 40 minutes | `current_user`, `has_access_to`, sections, relations, site data |
| [Lesson 5.4: A fetch function of your own](a-fetch-function-of-your-own.md) | 60 minutes | A module, `function_definition.php` and a function collection class |

## Preparing this module

- The training installation needs enough content for filters and paging to show something: a folder with at least
  six articles or notes, and a few folders below node 2.
- `mysite_hello` is active on each installation (lesson 3.1) and the override of lesson 3.2 is in place for the
  paging exercise.
- For lesson 5.3, each learner has a site account (not an administrator) to compare signed in and signed out.
- Rehearse lesson 5.4 once; keep a finished copy of the three files to hand out to learners who fall behind.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 5.1 | 45 |
| Lesson 5.2 | 55 |
| Break | 10 |
| Lesson 5.3 | 40 |
| Lesson 5.4 | 60 |
| **Total** | **210** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
