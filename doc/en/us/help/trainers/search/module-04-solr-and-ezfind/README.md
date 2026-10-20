---
title: "Module 4: Solr and ezfind"
audience: trainer, admin, developer, hosting
category: training
order: 40
summary: The fourth module of the Search course, installing ezfind and a Solr server, exp:solr and the Solr settings, indexing with Solr, and relevance, elevation and facets. Four lessons, 2 hours 10 minutes.
keywords: search course, module 4, Solr, ezfind, exp:solr, solr.ini, SolrBase, updatesearchindexsolr, boost, elevation, facets
type: help
---
# Module 4: Solr and ezfind

This module moves a training installation from the built-in engine to Solr through the ezfind extension, and teaches
what changes: the installation steps, the two Solr settings blocks and the `exp:solr` command, indexing and commits,
and the levers that decide what comes first in the results.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Duration | 2 hours 10 minutes (four lessons) |
| Lessons | 4 |
| Level | Advanced |
| Format | Hands-on; two terminals and the administration on the learner's own training installation |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [4.1 Installing ezfind and a Solr server](installing-ezfind-and-solr.md) | 35 min | Add, activate and set up ezfind; start the bundled Solr |
| [4.2 exp:solr and the Solr settings](exp-solr-and-the-solr-settings.md) | 30 min | Configure `[SolrSettings]`, compare it with `[SolrBase]`, check with `status` and `ping` |
| [4.3 Indexing with Solr](indexing-with-solr.md) | 30 min | Build the Solr index, read commit settings, find the extension's cronjob parts |
| [4.4 Relevance, elevation and facets](relevance-elevation-and-facets.md) | 35 min | Boost a class, elevate an object for a query, undo both |

## Preparing this module

- One training installation per learner **on MySQL or PostgreSQL** (`exp:install --db=mysql` or `--db=pgsql`, see
  [Installing from the command line](../../../../install/07-console-install.md)): the extension ships no SQLite
  schema file for its table.
- Composer and network access to Packagist and GitHub on every machine, or the `se7enxweb/ezfind` package prepared in
  advance.
- A Java runtime that starts the Solr bundled in `extension/ezfind/java`. The extension's installation notes name the
  Java version they were written for; rehearse the start on one lab machine with your Java before the class, and
  have a shared Solr ready as a fallback (then learners set `SearchServerURI` and `Url` to it).
- Enough memory for a Solr process per machine.
- Rehearse lesson 4.2's `exp:solr ping` against your lab's Solr.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lesson 4.1 (35 min) |
| 0:35 | Lesson 4.2 (30 min) |
| 1:05 | Lesson 4.3 (30 min) |
| 1:35 | Lesson 4.4 (35 min) |
| 2:10 | End of module |

## Related pages

- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
