---
title: "Module 8: Licensing and releasing"
audience: trainer, designer, developer
category: training
order: 80
summary: The last module of Theme design. Learners license every part of their theme, practice the release routine (version number, bump before tag, annotated tag) in a local repository, and finish with a changelog, release notes and the publishing checklists. 2 hours 40 minutes.
keywords: theme design, module 8, license, GPL, GFDL, Creative Commons, third-party assets, version number, permanent tag, annotated tag, CHANGELOG.md, release notes, distribution checklist
type: help
---
# Module 8: Licensing and releasing

A theme that others use needs clear terms and releases they can trust. This module covers the license of every part of
a theme, the release routine that never moves a published tag, and the changelog, release notes and checklists that
make a release usable. All release exercises happen in a local repository without a remote, so nothing can be
published by accident. The module ends the course.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Duration | 2 hours 40 minutes (with one 10-minute break) |
| Lessons | 3 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 8.1: Licensing a theme](licensing-a-theme.md) | 45 minutes | Take a license inventory of their theme and write the licenses into every place |
| [Lesson 8.2: Versions, and why a pushed tag is permanent](versions-and-permanent-tags.md) | 45 minutes | Sort tags, choose the next number, bump, commit, make an annotated tag locally |
| [Lesson 8.3: Changelog, release notes and the release checklist](changelog-notes-and-checklist.md) | 60 minutes | Write `CHANGELOG.md` and release notes, walk the checklists, name their open items |

## Preparing this module

- Learners' `training_theme` in a local Git repository without a remote (lesson 7.4). Check with `git remote -v`
  before lesson 8.2: if a learner added a remote, remove it for the exercise.
- State at the start of lesson 8.1 that the lesson explains licensing choices from the documentation and is not legal
  advice.
- For lesson 8.3, the Exponential version of the lab (from the About page, `/ezinfo/about`) on the board.
- Leave 10 minutes at the end of lesson 8.3 for the course feedback
  ([Collecting feedback](../../train-the-trainer/module-06-assessment-and-follow-up/collecting-feedback.md)).

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 8.1 | 45 |
| Lesson 8.2 | 45 |
| Break | 10 |
| Lesson 8.3 | 60 |
| **Total** | **160** |

## Related pages

- [Module 7: Packaging a theme](../module-07-packaging-a-theme/README.md)
- [Distributing a theme: start here](../../../designers/distribution/distribution-start-here.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
