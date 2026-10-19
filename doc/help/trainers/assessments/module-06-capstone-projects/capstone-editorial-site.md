---
title: "Lesson 6.2: Capstone, an editorial site"
audience: trainer, editor, website-creator
category: training
order: 62
summary: A one-day capstone for editorial teams, build the news section of an invented organization with folders, articles, images with alternative text, a translation, a landing page layout, an approval round and assistant-written drafts that were reviewed, then present it, scored with a seven-criterion rubric. One day (about 6 hours) plus presentations.
keywords: assessments and practical exercises, capstone, editorial site, editors, exponential layouts, exponential intelligence, articles, translation, landing page, approval, review, rubric
type: help
---
# Lesson 6.2: Capstone, an editorial site

A team of editors launches the news section of an invented organization in one day. The capstone joins Editors 101
and 201, Exponential Layouts and, where the course was taken, Exponential Intelligence.

> **Note**
> This is the trainer's own assessment, not an official certification. Use a training installation, an invented
> organization and test accounts at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 6: Capstone projects](README.md) |
| Duration | One day: 15 minutes briefing, 5 hours work with two checkpoints, 10 minutes presentation per team, 30 minutes scoring |
| Level | Intermediate |
| Audience | Editorial teams of two or three at the end of their editorial courses |
| Prerequisites | [Editors 101](../../editors-101/README.md); the Exponential Layouts course; optional: the Exponential Intelligence course |

## Learning objectives

Teams that pass this capstone have shown that they can:

- structure and publish a section with folders, articles and media;
- translate key pages;
- build and publish a landing page layout;
- take content through an approval;
- review assistant-written drafts before publishing (when the assistant is part of the course).

## Trainer's notes

### How to run it

1. **Before**: one training installation per team with a second content language, the layouts extensions, an approval
   workflow on one folder (as in [Workflows](../../../../guides/workflows.md#3-worked-example-approval-before-publishing)),
   one editor and one approver account per team at `example.invalid`, and, if used, the assistant switched on with
   limits. The workflow cronjob is run by the trainer at each checkpoint, not by a crontab.
2. **Checkpoint 1** (after 2 hours): the folder structure and at least three published articles.
3. **Checkpoint 2** (after 4 hours): the landing page layout as a draft or published; the approval round started.
4. **Presentation**: each team shows the deliverables on the installation in this order; each member answers one
   question about their part.

### Pitfalls

- **One person does all the layouts work.** Ask each member at the presentation to change one block.
- **Approval never completes.** If the workflow cronjob did not run, the approved version stays pending; run it at
  the checkpoints.

## Hands-on exercise

The brief, as handed to teams.

*The Riverside Rowing Club (an invented club) opens a news section on its website. You are its editorial team.*

1. Create a **News** folder with two subfolders, **Club news** and **Events**.
2. Publish six articles, three in each subfolder, with Title, Intro and Body. Every article has an image with a title
   and an alternative text.
3. Translate the two most important articles into the site's second language.
4. Build a landing page layout for **News** with a title block, a rich text introduction, and a list of the newest
   articles. Publish it. Show which mapping makes the **News** page use it (ask the trainer for the mapping if your
   role may not create one).
5. Put one article through the approval: the editor sends it for publishing, the approver approves it, and it is
   published after the workflow runs.
6. If the assistant is part of your course: let it write a teaser for one article, review it, and keep or discard it
   with a reason. Correct anything wrong before publishing.
7. Restore one article to an earlier version and one removed image from the trash, and say why you might need this.
8. Present: walk through the section on the public site, then in the administration.

## Expected result

- The section, articles, images, translations and landing page exist and are published on the team's installation.
- The approved article shows the approval history; the restored items are in place.
- The team explains each step and answers the individual questions.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Structure and articles | Missing | Under half done | All, minor field gaps | Folders and six complete articles | The tree |
| Images and alternative text | None | Images without text | Most with text | All with title and alternative text | The image items |
| Translations | None | One, unpublished | One published | Two published | Language badges |
| Landing page | None | Draft only | Published, list missing | Published with all blocks, mapping explained | The layout, **Rules** |
| Approval | Not attempted | Sent, not approved | Approved, not published | Published through the workflow | Version history |
| Review of assistant drafts (if used) | Unreviewed text published | Reviewed, error kept | Reviewed, reason vague | Reviewed, decision with a reason | The draft list, the article |
| Recovery and presentation (must) | Neither restore | One restore | Both, explanation thin | Both restores and a clear walk-through with individual answers | The installation, the presentation |

**Pass**: two thirds of the available points (14 of 21, or 12 of 18 without the assistant) and **Recovery and
presentation** at 2 or 3.

## Quiz

Debrief questions after the presentations.

1. Which deliverable took longest, and what would you prepare differently next time?
2. Why did the approved article not appear until the workflow ran?
3. Which mistake did the restore steps protect you from?
4. What did review of the assistant's text catch?

<details>
<summary>Answers</summary>

1. An open question; note the answers to improve the course.
2. The workflow cronjob carries out the held-back publish ([Workflows](../../../../guides/workflows.md#in-short)).
3. Accidental edits and removals ([Publishing, versions and the trash](../../../publishing-and-versions.md)).
4. An open question; any factual or tone error the team found counts.

</details>

## Further reading

- [Editors, practical task](../module-02-editorial-courses/editors-practical-task.md) and
  [Exponential Layouts, practical task](../module-02-editorial-courses/layouts-practical-task.md): the single tasks
  this capstone joins.
- [Planning capstone projects](planning-capstone-projects.md)

## Related pages

- [Module 6: Capstone projects](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
