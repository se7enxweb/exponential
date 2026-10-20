---
title: "Search: course overview"
audience: trainer, admin, developer
category: training
order: 1
summary: The course for administrators and developers who run search on an Exponential site, how search works, indexing, searching in practice, Solr through ezfind, tuning and troubleshooting. Five modules, 20 lessons, about 10 hours of lessons.
keywords: search course, course overview, reading path, search, indexing, solr, ezfind, search statistics, content finder, troubleshooting search
type: help
---
# Search: course overview

This course teaches administrators and developers how search works on an Exponential site and how to keep it
working: what the built-in search engine does, when Solr through the ezfind extension is the better choice, how
content gets into the index, what editors and visitors see when they search, and what to check when a result is
missing or search is slow.

## Who it is for

- Site administrators who answer "why can't I find my article?" questions.
- Developers who set up search for a site, or move a site from the built-in engine to Solr.
- Hosting staff who run the Solr server next to an installation.

**Outcome:** after the course, a learner can explain how an item reaches the search index, rebuild the index, choose
and set up a search backend, read the search statistics and work through a checklist when results are missing.

## Format and duration

| | |
|---|---|
| Duration | 20 lessons, about 10 hours of lessons (one and a half to two days with breaks) |
| Format | Classroom or online; hands-on |
| Level | Intermediate; Module 4 is advanced |
| Prerequisites | [Site administration](../site-administrators/README.md) (at least the modules on caches and cronjobs); a terminal |
| Lab | One training installation per learner, installed with `php bin/php/console exp:install` and served with `exp:velocity start`; for Module 4 an installation on MySQL or PostgreSQL, a Java runtime and the ezfind package (see the module's preparation list) |

## Modules

| Module | Lessons | Duration | Outcome |
|---|---|---|---|
| [1. How search works](module-01-how-search-works/README.md) | 4 | 1 h 45 min | Knows where people search, the built-in engine, Solr through ezfind, and how to choose |
| [2. Indexing](module-02-indexing/README.md) | 4 | 2 h | Follows an item into the index, uses delayed indexing, rebuilds the index, picks searchable fields |
| [3. Searching in practice](module-03-searching-in-practice/README.md) | 4 | 2 h | Uses the admin search, advanced search and content finder; checks the site search; reads statistics |
| [4. Solr and ezfind](module-04-solr-and-ezfind/README.md) | 4 | 2 h 10 min | Installs ezfind with a Solr server, uses exp:solr, indexes with Solr, works with relevance and facets |
| [5. Tuning and troubleshooting](module-05-tuning-and-troubleshooting/README.md) | 4 | 2 h | Tunes the search settings, handles languages, finds missing results, measures performance |

## Reading path

For a trainer preparing to give this course, in this order:

1. [Trainers: start here](../trainers-start-here.md) and the [Course catalogue](../course-catalogue.md).
2. [Searching](../../searching.md): search as editors and visitors meet it.
3. [Search backends](../../hosting/basics/search-backends.md): the built-in engine and Solr from a host's view.
4. [Web server and Solr commands](../../../features/6.0/web-server-and-solr-commands.md): the console commands used in
   Module 4.
5. [Cronjobs](../../../guides/cronjobs.md): delayed indexing depends on them.
6. [Caches](../../../guides/caches.md): stale results are often a cache question.
7. [Installing from the command line](../../../install/07-console-install.md): the lab, including the database
   choice Module 4 needs.
8. Modules 1 to 5 of this course, in order. Rehearse Module 4 completely on one lab machine with your own Java
   runtime before the class; it is the module most likely to fail on the day.

## Suggested schedules

| Schedule | Plan |
|---|---|
| Two days | Day 1: Modules 1, 2 and 3. Day 2: Modules 4 and 5, then a troubleshooting exercise on a learner's own installation |
| One and a half days, built-in search only | Day 1: Modules 1, 2 and 3. Day 2 morning: Module 5; skip Module 4 |
| Five half days | One module per half day, in order |
| Self-study | One module per week, with the exercises done on your own installation |

## Assessing learners

Each module ends its lessons with a quiz and an expected result the trainer can check. As a final practical task,
the trainer hides one item from search on a learner's installation (for example by leaving an article as a draft,
hiding it, or turning on delayed indexing without running the cronjob) and the learner finds the cause with the checklist of
lesson 5.3. This is the trainer's own assessment, not an official certification; the partner program's
certifications are separate ([Certifications](../../partners/certifications.md)). The Assessments and practical
exercises course in the catalogue has more material.

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Searching](../../searching.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
