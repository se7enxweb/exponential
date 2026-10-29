---
title: "Editors 201: course overview"
audience: trainer, editor
category: training
order: 1
summary: The intermediate course for editors who publish every week, 8 modules and 23 lessons, about 16 hours of lessons, covering translations, relations and locations, organizing content and sections, object states, approvals, URL aliases, feeds and PDF, tags, content checks and accessible content.
keywords: editors 201, intermediate editor training, translations, languages, relations, locations, sections, object states, approval, collaboration, url alias, rss, pdf export, tags, eztags, content checks, accessibility, course overview
type: help
---
# Editors 201: course overview

Editors 201 is for editors who already publish confidently and now work on a larger, multilingual or team-run site.
It covers what Editors 101 left out: translations, relations and locations, reorganizing content, object states,
approvals, addresses, feeds, PDF exports, tags, content checks and accessible writing. It ends with a capstone that
combines them.

## Who it is for

- Editors who completed Editors 101, or have used Exponential for some months.
- Chief editors and reviewers who approve content.
- Translators working on multilingual sites.
- Editors who look after a part of a site: its structure, its tags, its quality.

## Outcome

After the course, learners can translate items and fix content in the wrong language, read and use relations and
locations, move, copy and sort content and predict its section, run large operations as background jobs, set object
states, send and approve content through a workflow, work as a team without overwriting each other, add URL aliases,
check their content in feeds and PDF exports, tag content and maintain tags, run the content checks, and write
accessible content.

## Format and length

| | |
|---|---|
| Lessons | 23 lessons in 8 modules |
| Lesson time | 16 hours 15 minutes (presenting and exercises) |
| With breaks | about 17 hours 25 minutes, as in the modules' timing plans |
| Format | Classroom with a projector, or online with screen sharing; many exercises in pairs |
| Group size | Up to about 12 learners per trainer, an even number for the pair exercises |
| Level | Intermediate |

Modules 4 (object states) and 7 (tags) depend on features your learners' site may not use; leave them out when it
does not.

## Prerequisites

- Learners: Editors 101 or equivalent experience (signing in, the tree, the edit form, the online editor, images,
  versions and the trash).
- Trainer: experience as an editor and some administrator rights on the training installation, because several
  modules need roles, a workflow, a second language, a state group or a tags field prepared
  ([Train the trainer: course overview](../train-the-trainer/README.md)).

## Lab setup

- A training installation reachable from every learner's computer (for example installed with
  `php bin/php/console exp:install --apply`, [7. The console install](../../../install/07-console-install.md)); how to build and
  reset it: [Module 2: Building the lab](../train-the-trainer/module-02-building-the-lab/README.md) of Train the
  trainer.
- One Editor account per learner, plus approver and reviewer accounts for the pair exercises; e-mail addresses at
  `example.invalid`; no real mail.
- Each learner's course folder below a folder **Training** (from Editors 101, or created anew).
- Per module, the preparation listed in its overview: a second content language (module 1), a section learners may
  assign and a large folder per learner (module 3), a state group and roles (module 4), an approval workflow and a
  running workflow cronjob (module 5), an RSS export and the `pdf/edit` policy (module 6), a tags field and tags
  (module 7), the content check policies and prepared problems (module 8).

## Modules

| Module | Lessons | Duration | Topics |
|---|---|---|---|
| [Module 1: Translations and languages](module-01-translations-and-languages/README.md) | 3 | 2 h 10 min | Content languages, translating, main language, removing a translation |
| [Module 2: Relations, locations and embedding](module-02-relations-locations-and-embedding/README.md) | 3 | 2 h 10 min | Relations both ways, several locations, checking before removing |
| [Module 3: Organizing content](module-03-organizing-content/README.md) | 4 | 3 h 05 min | Move and copy, sorting, sections, content jobs, the trash in depth |
| [Module 4: Object states](module-04-object-states/README.md) | 2 | 1 h 15 min | States and groups, setting states, a review flow |
| [Module 5: Approvals and collaboration](module-05-approvals-and-collaboration/README.md) | 3 | 2 h 10 min | Pending items, approving and denying, edit conflicts, team habits |
| [Module 6: Addresses, feeds and PDF](module-06-addresses-feeds-and-pdf/README.md) | 3 | 2 h 10 min | URL aliases, RSS feeds, PDF exports |
| [Module 7: Tags](module-07-tags/README.md) | 2 | 1 h 30 min | Tagging content, the tags dashboard |
| [Module 8: Quality and accessibility](module-08-quality-and-accessibility/README.md) | 3 | 2 h 55 min | Content checks, accessible content, the capstone |

## Reading path

For a trainer preparing the course, in this order:

1. [Languages](../../languages.md) and [Content languages](../../../guides/content-languages.md) (sections 1 to 9).
2. [Creating and editing content](../../editing-content.md) and
   [Publishing, versions and the trash](../../publishing-and-versions.md), as a refresher.
3. [Removing and hiding: the check of reverse relations](../../../features/6.0/reverse-relation-check.md) and
   [The content model and editing content](../../../guides/content-model-and-editing.md) (sections 8 and 10).
4. [Sections](../../../guides/sections.md), [Content jobs](../../../features/6.0/content-jobs.md) and
   [The trash view](../../../bc/6.0/trash.md).
5. [Object states](../../../guides/object-states.md) and [Workflows](../../../guides/workflows.md), with
   [My drafts and my pending items](../../../guides/drafts-and-pending.md).
6. [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md),
   [RSS feeds](../../../guides/rss-feeds.md) and [PDF exports](../../../guides/pdf-exports.md).
7. [eztags](../../../features/6.0/extensions/eztags.md), [The content checks](../../../features/6.0/content-checks.md)
   and [Accessibility standards](../../../standards/accessibility.md).
8. [Roles and policies](../../../guides/roles-and-policies.md): you will prepare several roles.
9. Then the modules in order: each module's overview first (its preparation list and timing plan), then its lessons.
   Rehearse every exercise, including the pair exercises with two browsers.

## Suggested schedules

Lesson times add up to 16 hours 15 minutes; the schedules add breaks.

### Three days

| Day | Morning | Afternoon |
|---|---|---|
| 1 | Module 1 | Module 2, Module 4 |
| 2 | Module 3 | Module 5 |
| 3 | Module 6, Module 7 | Module 8 |

### Two long days without the optional modules

Leave out modules 4 and 7 when the site uses neither object states nor tags (13 hours 30 minutes of lessons):

| Day | Morning | Afternoon |
|---|---|---|
| 1 | Module 1, Module 2 | Module 3 |
| 2 | Module 5, Module 6 | Module 8 |

### Weekly sessions

One module per week, in order, with the capstone (lesson 8.3) as the last session. Between sessions, learners apply
the module on their own site and bring one question.

## Assessing learners

- Each lesson's quiz and expected result check understanding during the course.
- Lesson 8.3 (the capstone) has an eight-point checklist for a final practical check.
- The assessments course (Assessments and practical exercises, in this section of the help) has further material.
  All assessments are the trainer's own material, not an official certification; the partner program's
  certifications are separate and their details are not published
  ([Certifications](../../partners/certifications.md)).

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
