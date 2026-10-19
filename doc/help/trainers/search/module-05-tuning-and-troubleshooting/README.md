---
title: "Module 5: Tuning and troubleshooting"
audience: trainer, admin
category: training
order: 50
summary: The fifth module of the Search course, tuning the search settings, search and languages, a checklist for missing results, and measuring search performance. Four lessons, 2 hours.
keywords: search course, module 5, tuning, troubleshooting, wildcards, stop words, languages, missing results, checklist, exp:benchmark, performance
type: help
---
# Module 5: Tuning and troubleshooting

The last module turns learners into the people others call when search misbehaves. They tune the settings per
siteaccess, understand how translations are searched, follow a checklist for missing results, and measure the
search page instead of guessing.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Duration | 2 hours (four lessons) |
| Lessons | 4 |
| Level | Intermediate to advanced |
| Format | Hands-on on the learner's own training installation; one case exercise prepared by the trainer |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [5.1 Tuning the search settings](tuning-the-search-settings.md) | 30 min | Switch wildcards on and off per siteaccess, read the stop word rule and the limits |
| [5.2 Search and languages](search-and-languages.md) | 25 min | Search translated content on two siteaccesses |
| [5.3 When results are missing](when-results-are-missing.md) | 35 min | Solve four prepared "not found" cases with the checklist |
| [5.4 Search performance and capacity](search-performance-and-capacity.md) | 30 min | Benchmark the search page and compare against a baseline |

## Preparing this module

- One training installation per learner with sample content, served (for example with `exp:velocity start`) so that
  `exp:benchmark` can reach it.
- For lesson 5.2: a second content language with its own public siteaccess on each installation, or plan the lesson
  as a demonstration.
- For lesson 5.3, on each installation, four articles with distinct words in their titles, each with one cause: one
  left as a draft, one in a section the anonymous role may not read, one whose word is only in a field you made not
  searchable before rebuilding the index, and one published while `DelayedIndexing` was enabled and the cronjob not
  run. Keep the list of words and causes for yourself.
- For lesson 5.4: ask learners to run benchmarks one row at a time on a shared machine.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 5.1 (30 min) |
| 0:30 | Lesson 5.2 (25 min) |
| 0:55 | Lesson 5.3 (35 min) |
| 1:30 | Lesson 5.4 (30 min) |
| 2:00 | End of module |

## Related pages

- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
