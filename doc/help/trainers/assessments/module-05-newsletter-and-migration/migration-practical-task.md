---
title: "Lesson 5.4: Migrating to Exponential, practical task"
audience: trainer, admin, developer
category: training
order: 54
summary: A 90-minute practical planning task for the end of the migration course, from a small sample site supplied by the trainer produce an inventory, a content model mapping table, a redirect table, a rehearsal and cut-over plan with timings and rollback triggers, and import three pages into a training installation with remote ids, scored with a six-criterion rubric. 115 minutes with scoring.
keywords: assessments and practical exercises, migration, practical task, rubric, inventory, content model mapping, remote id, redirect table, URL alias, URL wildcard, rehearsal, cut-over, rollback, static HTML
type: help
---
# Lesson 5.4: Migrating to Exponential, practical task

A migration is mostly planning. In this task each learner receives the same small sample site and produces the
documents a real project needs before writing import code, then proves the most important idea, the remote id, by
importing three pages twice without making duplicates.

> **Note**
> This is the trainer's own assessment, not an official certification. The sample site is made by the trainer for
> class and contains no personal data. Imports go into the learner's own training installation only.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 5: Assessing the newsletter and migration courses](README.md) |
| Duration | 115 minutes (5 briefing, 90 task, 20 scoring) |
| Level | Advanced |
| Audience | Learners at the end of the Migrating to Exponential course |
| Prerequisites | [Lesson 5.3](migration-written-test.md); a training installation and a shell per learner |

## Learning objectives

Learners who pass this task have shown that they can:

- take inventory of a source site, including its addresses;
- map source types and fields to classes, attributes, datatypes and locations;
- build a redirect table with a decision per address;
- plan rehearsals, cut-over and rollback with triggers and owners;
- import content repeatably by remote id.

## Trainer's notes

### How to run it

1. **Before**: prepare a sample site of 10 to 20 static HTML pages: a home page, news items with dates, a few
   "about" pages, two images per news item, one page with a query string address in its links (for example
   `news.php?id=3`), one page nobody links to. Give every learner a copy as a folder. Have the import tool your course
   taught installed on the training installations
   ([Exponential's import and export tools](../../../../install/migration/exponential-tools.md)).
2. **Briefing**: hand out the steps and the rubric; 90 minutes. The migration chapters may be open.
3. **Scoring**: read the documents; in the training installation, count the imported items and check their remote
   ids.

### Common questions

**May learners write their own import script?** Yes, if it matches on remote id. The checklist says to use existing
tools first and write a script only for what they cannot do.

**How detailed must the plan be?** One page each for inventory, mapping, redirects and plan. Real projects have more;
the rubric looks for the decisions, not the length.

### Pitfalls

- **Skipping the orphan page.** A sitemap or the menu misses it; a crawl or the file listing finds it. It is in the
  sample on purpose.
- **Query string addresses.** They cannot be URL aliases; learners who map them to aliases lose a point.

## Hands-on exercise

The task, as handed to learners. Use the sample site folder and your own training installation.

1. **Inventory.** List every page of the sample site with its address, type (news, about, home), language and the
   images it uses. Include pages no menu links to. Count pages per type.
2. **Scope.** Decide what moves and what does not, and write who would own each part in a real project (content,
   design, addresses, users) as in the checklist.
3. **Mapping.** Write the mapping table: source type, source field, Exponential class, attribute identifier, datatype,
   conversion, notes. Decide where each type goes in the content tree and how images become objects.
4. **Remote ids.** Define the remote id pattern for each type, for example `static-news-<file name>`.
5. **Redirects.** Write the redirect table: old address, decision (same address, 301 to the new address, or 410/404),
   new address, and which of the three Exponential ways (or the web server) handles it.
6. **Plan.** Write the rehearsal, cut-over and rollback plan: how many rehearsals, what is counted after each, the
   freeze, the DNS step, the rollback triggers and who decides.
7. **Import.** Import three news pages into your training installation with their remote ids, using the tool from the
   course, below a folder `Migration test <your name>`.
8. **Repeat.** Change one word in one source page and run the same import again. Check that there are still exactly
   three items, and that the changed one has a new version.
9. **Count.** Write down the counts of step 1 and of your import, and what a full rehearsal would compare.

## Expected result

- Four short documents: inventory (with the orphan page), mapping table, redirect table (the query string address
  handled in the web server), plan with rollback triggers and owners.
- In the training installation: three news items with remote ids following the pattern; after step 8 still three,
  one with two versions.
- The trainer compares the inventory with the sample's file list and checks the remote ids of the imported items.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Inventory | Missing | Menu pages only | All pages, images or counts missing | All pages incl. the orphan, images, counts per type | The inventory |
| Mapping | Missing | Types only | Fields mapped, conversions or images undecided | Every field mapped or dropped on purpose, images as objects, tree placement | The mapping table |
| Redirects | Missing | Some addresses | All, query string mapped to an alias | All, a decision each, query string in the web server | The redirect table |
| Plan and rollback | Missing | Steps without timing | Rehearsals and cut-over, no rollback trigger | Two or more timed rehearsals, counts, freeze, rollback triggers and owner | The plan |
| Repeatable import (must) | No import | Imported without remote ids | Remote ids, re-run duplicated one | Re-run updated in place, three items, one with a new version | The training installation |
| Scope and data care | Not addressed | Scope only | Owners named, personal data not considered | Scope, owners, and "move only what you need" stated | The scope note |

**Pass**: 12 of 18 points or more and **Repeatable import** at 2 or 3.

## Quiz

Debrief questions to ask after scoring.

1. Why did the orphan page matter?
2. What would have happened in step 8 without remote ids?
3. Why do redirects stay in place for at least a year?
4. Which single document would you show a customer first, and why?

<details>
<summary>Answers</summary>

1. A sitemap or menu alone misses orphan pages; their addresses may still have links and traffic
   ([Checklist](../../../../install/migration/checklist.md#1-inventory-know-what-you-have)).
2. Duplicates: the import would create new items instead of updating
   ([Checklist](../../../../install/migration/checklist.md#11-risks-that-recur)).
3. Search engines and old links need them that long
   ([Checklist](../../../../install/migration/checklist.md#4-urls-and-redirects)).
4. An open question; a good answer is the scope with owners, because it decides everything else
   ([Checklist](../../../../install/migration/checklist.md#2-decide-the-scope-and-the-owner-of-each-part)).

</details>

## Further reading

- [Migrating static HTML](../../../../install/migration/static-html.md): the guide for the sample site.
- [Exponential's import and export tools](../../../../install/migration/exponential-tools.md): which tool for which
  job.
- [The migration checklist](../../../../install/migration/checklist.md): every step of the plan.

## Related pages

- [Lesson 5.3: Migrating to Exponential, written test](migration-written-test.md)
- [Module 5: Assessing the newsletter and migration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
