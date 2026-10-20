---
title: "Module 1: How search works"
audience: trainer, admin
category: training
order: 10
summary: The first module of the Search course, where people search, the built-in engine and its limits, Solr through ezfind at a glance, and choosing a backend. Four lessons, 1 hour 45 minutes.
keywords: search course, module 1, how search works, eZSearchEngine, Solr, ezfind, search backend, quick search, content finder
type: help
---
# Module 1: How search works

This module gives learners the map they need for the rest of the course: the places where people search, the
built-in engine that answers most of them, the optional Solr server through the ezfind extension, and a simple method
to choose between the two.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Duration | 1 hour 45 minutes (four lessons) |
| Lessons | 4 |
| Level | Intermediate |
| Format | Short presentations, exercises on the learner's training installation and one pair exercise on paper |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [1.1 Where people search in Exponential](where-people-search.md) | 25 min | Try the quick search, advanced search, content finder, site search and help search |
| [1.2 The built-in search engine](the-built-in-search-engine.md) | 30 min | Read the search settings with `exp:ini` and test words, accents and phrases |
| [1.3 Solr and ezfind at a glance](solr-and-ezfind-at-a-glance.md) | 25 min | Run `exp:solr status` and check which engine the installation uses |
| [1.4 Choosing a search backend](choosing-a-search-backend.md) | 25 min | Recommend a backend for three described sites |

## Preparing this module

- One training installation per learner with sample content (for example installed with `exp:install`, see
  [Installing from the command line](../../../../install/07-console-install.md)), an administrator account and a
  terminal in the installation root.
- Check that the public site of the training installation has a search box; if its design has none, show the
  `content/search` page directly in lesson 1.1.
- Run `php bin/php/console exp:solr status` once on the training installation so you know its message.
- Print the three site descriptions of lesson 1.4, one copy per pair.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 1.1 (25 min) |
| 0:25 | Lesson 1.2 (30 min) |
| 0:55 | Lesson 1.3 (25 min) |
| 1:20 | Lesson 1.4 (25 min) |
| 1:45 | End of module |

## Related pages

- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
