---
title: "Lesson 1.3: The phases of a migration and the checklist"
audience: trainer, admin, developer, project-manager
category: training
order: 13
summary: Learners lay out a migration as the phases every path shares (inventory, scope, mapping, URLs, metadata, import, rehearsal, test, cut-over, rollback, after go-live), turn the common checklist into a project plan, and name the risks that recur. 45 minutes.
keywords: migrating to exponential, migration phases, project plan, checklist, risks, tracker, rehearsal, cut-over, go-live, timeline
type: help
---
# Lesson 1.3: The phases of a migration and the checklist

All migration guides of the documentation fit one frame: the common migration checklist. This lesson turns that
frame into a project plan learners can copy into their own tracker, and shows the risks that come back in almost
every project.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 1: Planning a migration](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Project managers, administrators and developers |
| Prerequisites | [Lesson 1.2](inventory-and-scope.md) |

## Learning objectives

After this lesson, learners can:

- name the eleven sections of the common migration checklist in their order;
- explain which phases can run in parallel and which must wait;
- turn the checklist into a plan with phases, owners and "done when" criteria;
- match three recurring risks to their counter-measures.

## Trainer's notes

### How to present it

Sources: [The common migration checklist](../../../../install/migration/checklist.md) and the plans in
[chapter 15.4](../../../../install/15-migrating-from-5x-legacy.md#154-plan-and-timeline) and
[chapter 16.10](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1610-a-plan-and-its-phases).

1. **The frame** (5 minutes). Show the contents list of the checklist: inventory, scope, mapping, URLs and redirects,
   search engine metadata, build and rehearse the import, test, cut-over, rollback, after go-live, risks. Every path
   guide adds what is specific to its source.
2. **"Done when"** (5 minutes). Show the timeline table of chapter 15.4. Each phase has a column "Done when". A plan
   without that column cannot be tracked.
3. **Parallel tracks** (4 minutes). Chapter 14.2 names three tracks that only meet at the end: the database, the
   files and settings, and the code. Different people can work on them at the same time.
4. **Recurring risks** (6 minutes). Section 11 of the checklist: lost addresses, broken rich text, duplicates after
   re-runs, users locked out, late changes lost, underestimated templates, personal data moved without need. Ask the
   room which of these they have seen before.

### Common questions

**How long does a migration take?** The documentation gives no fixed figure; it says to measure. Chapter 14.15 lists
what each step grows with, and chapter 16.10 names the three numbers that drive the duration. The rehearsal gives
the real time (lesson 1.4).

**Can templates wait until the content is in?** Start them early, against the content of the first rehearsal
(checklist, section 11: "underestimated templates").

**Where do we keep the checklist?** The checklist itself says: copy it into your project's tracker and tick it off.

### Pitfalls

- **One giant phase called "migration".** Break it down; every phase needs a done-when line.
- **Testing at the end only.** Counts and samples belong to every rehearsal, not only to the final run.
- **No after-go-live phase.** The 404 report, ranking and traffic, and switching off the old system are part of the
  project (checklist, section 10).

## Hands-on exercise

A planning exercise in groups of three, on paper or in a shared document. Use the scenario: *a site of 2 000
articles in two languages, 300 images, 40 editor accounts, one contact form, moving from a WordPress installation to
Exponential 6*.

1. Open [the common migration checklist](../../../../install/migration/checklist.md).
2. Make a table with the columns **Phase**, **What**, **Owner**, **Done when**.
3. Fill in one row per checklist section 1 to 10, in order.
4. For the phase "Build the import and rehearse it", write at least two "done when" criteria taken from section 6.
5. For the phase "Test", pick four tests from the table in section 7 and write how you would run each for this
   scenario.
6. Mark which rows can run in parallel with the import work.
7. From section 11, pick the three risks you think most likely for this scenario and write the counter-measure of
   each.
8. Present your plan to another group in three minutes.

## Expected result

- A table of ten rows, each with an owner and a measurable done-when criterion.
- The import phase names repeatable runs that match on remote id, and counts compared with the inventory.
- The test row names concrete checks, for example the counts per type and language, the 50 most visited pages side
  by side, and every redirect tested automatically.
- At least the risks "users locked out" (WordPress hashes) and "lost addresses" are among the three chosen.
- The trainer checks one plan per group against the checklist sections.

## Quiz

1. Put these in order: test, map the content model, cut-over, URLs and redirects.
2. What are the three tracks of chapter 14.2 that can be worked in parallel?
3. Which recurring risk does "remote ids and update-on-match" counter?
   A. Lost addresses B. Duplicates after re-runs C. Users locked out D. Broken rich text
4. Name two things that belong to the phase after go-live.
5. True or false: the documentation states how many days a migration of 2 000 articles takes.

<details>
<summary>Answers</summary>

1. Map the content model, URLs and redirects, test, cut-over
   ([checklist, contents](../../../../install/migration/checklist.md#contents)).
2. The database; the files and settings; the code
   ([chapter 14.2](../../../../install/14-migrating-from-4x.md#142-plan-the-migration-on-a-staging-copy)).
3. **B** ([checklist, section 11](../../../../install/migration/checklist.md#11-risks-that-recur)).
4. Any two of: the 404 report, search ranking and traffic, archiving and switching off the old system, documenting the
   mapping ([checklist, section 10](../../../../install/migration/checklist.md#10-after-go-live)).
5. **False.** It says to measure in the rehearsal
   ([chapter 14.15](../../../../install/14-migrating-from-4x.md#1415-how-long-it-takes)).

</details>

## Further reading

- [The common migration checklist](../../../../install/migration/checklist.md): every phase as a checklist.
- [Chapter 15.4: Plan and timeline](../../../../install/15-migrating-from-5x-legacy.md#154-plan-and-timeline): a plan
  with done-when criteria.
- [Chapter 16.10: A plan and its phases](../../../../install/16-migrating-from-ez-platform-and-ibexa.md#1610-a-plan-and-its-phases):
  the phases for the two platform paths.

## Related pages

- [Module 1: Planning a migration](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
