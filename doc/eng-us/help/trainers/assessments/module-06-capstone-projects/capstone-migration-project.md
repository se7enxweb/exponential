---
title: "Lesson 6.5: Capstone, a migration project"
audience: trainer, admin, developer, project-manager
category: training
order: 65
summary: A two-day capstone for migration teams, migrate an invented organization's sample site into a training installation, from inventory and mapping through two timed rehearsals with counts, redirects tested automatically, a cut-over and rollback plan, to a go or no-go recommendation, scored with a seven-criterion rubric. Two days plus presentations.
keywords: assessments and practical exercises, capstone, migration project, inventory, content model mapping, remote id, rehearsal, counts, redirects, cut-over, rollback, go no-go, rubric
type: help
---
# Lesson 6.5: Capstone, a migration project

A team migrates a sample site of an invented organization into a training installation as if it were a real project:
plan, import, rehearse twice, test the redirects, and recommend go or no-go with evidence. It extends the planning task
of [Lesson 5.4](../module-05-newsletter-and-migration/migration-practical-task.md) to a full project.

> **Note**
> This is the trainer's own assessment, not an official certification. The sample site is made by the trainer for
> class and holds no personal data; user accounts in it use addresses at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 6: Capstone projects](README.md) |
| Duration | Two days: 15 minutes briefing, about 10 hours work with three checkpoints, 15 minutes presentation per team, 1 hour scoring |
| Level | Advanced |
| Audience | Teams of two to four at the end of Migrating to Exponential, with Site administration or Developers |
| Prerequisites | Those courses; a training installation and a shell per team; the import tool of the course installed |

## Learning objectives

Teams that pass this capstone have shown that they can:

- run a migration from inventory to a go or no-go decision;
- build a repeatable import keyed on remote ids;
- rehearse with counts and timings and improve between rehearsals;
- test every redirect automatically;
- plan cut-over and rollback with triggers and an owner.

## Trainer's notes

### How to run it

1. **Before**: a sample site larger than in lesson 5.4 (40 to 80 pages, two content types with dates and authors, images,
   a few documents, a page in a second language, addresses with query strings, two orphan pages), as static files or
   as an export of the source system your course covered ([WordPress](../../../../install/migration/wordpress.md),
   [Drupal](../../../../install/migration/drupal.md), [Joomla](../../../../install/migration/joomla.md) or
   [static HTML](../../../../install/migration/static-html.md)).
2. **Checkpoints**: day 1 midday (inventory and mapping), end of day 1 (first rehearsal with counts), day 2 midday
   (second rehearsal, redirect test).
3. **A late change**: on day 2 morning, change two source pages and add one; the team must catch it with a delta run.
4. **Presentation**: the recommendation first, then the evidence.

### Pitfalls

- **No counts.** A rehearsal without counts proves nothing; insist on the comparison with the inventory.
- **Redirects tested by sampling.** The checklist asks for every row, automatically.

## Hands-on exercise

The brief, as handed to teams.

*The Northpoint Historical Society (an invented society) moves its website to Exponential. You are the migration
team.*

1. Inventory: every address (crawl or file list, not only the menu), every content type with counts per language,
   media with size, users, integrations, anything not to move.
2. Scope and owners: what moves, what does not, and who would decide each part in a real project.
3. Mapping: the table per type and field, tree placement, images and documents as objects, translations as
   translations of one object, remote id pattern.
4. Import: a repeatable import by remote id into a staging installation (your training installation).
5. Rehearsal 1: run it, time it, count per type and language, compare with the inventory, note every gap.
6. Fix and rehearsal 2: improve, run again from a clean target, time and count again.
7. Late change: catch the trainer's day 2 changes with a delta run without duplicates.
8. Redirects: generate the redirect table from the import; test every row automatically (status and destination) on
   the training installation; handle query string addresses in a web server rule you write down.
9. Cut-over and rollback plan: freeze, final run, DNS step, cache warming, log watching, rollback triggers, who
   decides, how long the old site stays read-only.
10. Recommendation: go or no-go, with the evidence and the remaining risks.

## Expected result

- The documents of steps 1 to 3 and 9, two rehearsal logs with timings and counts, a delta run log, the redirect test
  output, and a one-page recommendation.
- In the training installation: the imported content, no duplicates after the delta run.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Inventory and scope | Missing | Menu pages only | Complete, owners missing | Complete incl. orphans, scope with owners | Documents |
| Mapping | Missing | Types only | Fields mapped, media or languages undecided | Complete, media as objects, translations, remote ids | Mapping table |
| Repeatable import (must) | No import | Imports, duplicates on re-run | No duplicates, delta change missed | Re-runs update in place, delta caught | Installation, logs |
| Rehearsals | None | One, no counts | Two, counts without comparison | Two timed, counted, compared, improved | Rehearsal logs |
| Redirects | None | Table only | Tested by sample | Every row tested automatically, query strings handled | Test output |
| Cut-over and rollback | None | Steps only | Rollback without triggers or owner | Triggers, owner, read-only old site, timings from rehearsals | Plan |
| Recommendation | None | Opinion only | Evidence, risks missing | Go or no-go with evidence and remaining risks | Recommendation |

**Pass**: 14 of 21 points or more and **Repeatable import** at 2 or 3.

## Quiz

Debrief questions after the presentations.

1. What changed between rehearsal 1 and 2, and how did the counts show it?
2. How did your import avoid duplicates in the delta run?
3. Which rollback trigger would you agree with the society before the cut-over?
4. What would make you say no-go?

<details>
<summary>Answers</summary>

1. An open question; good answers compare counts with the inventory
   ([Checklist](../../../../install/migration/checklist.md#6-build-the-import-and-rehearse-it)).
2. Matching on remote ids and updating what exists
   ([Checklist](../../../../install/migration/checklist.md#3-map-the-content-model)).
3. For example a set share of key pages broken, or sign-in not working
   ([Checklist](../../../../install/migration/checklist.md#9-rollback)).
4. An open question; unexplained count differences, failing redirects or untested rollback are strong reasons.

</details>

## Further reading

- [The migration checklist](../../../../install/migration/checklist.md)
- [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md)
- [Exponential's import and export tools](../../../../install/migration/exponential-tools.md)

## Related pages

- [Module 6: Capstone projects](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
