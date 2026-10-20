---
title: "Lesson 6.3: Capstone, running a site"
audience: trainer, admin, hosting
category: training
order: 63
summary: A two-day capstone for administrators and hosting staff, take over an invented organization's training site, set up roles and sections, cron and search, a backup and a tested restore, a deploy and a maintenance window, and handle two incidents the trainer injects, then hand over a runbook, scored with a seven-criterion rubric. Two days plus presentations.
keywords: assessments and practical exercises, capstone, site operations, site administration, hosting, roles, sections, cronjobs, delayed indexing, backup, restore test, deploy, maintenance, incident, runbook, rubric
type: help
---
# Lesson 6.3: Capstone, running a site

For two days the learner is the administrator of an invented organization's site, on a training installation. They
set it up for daily work, prove the backups, and handle two incidents the trainer injects. The final deliverable is a
runbook another administrator could follow.

> **Note**
> This is the trainer's own assessment, not an official certification. Every incident is injected on a training
> installation by the trainer. Mail transport `file`; test users at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 6: Capstone projects](README.md) |
| Duration | Two days: 15 minutes briefing, about 10 hours work with three checkpoints, 15 minutes presentation per learner, 1 hour scoring |
| Level | Intermediate to advanced |
| Audience | Learners at the end of Site administration and Hosting and operations (Search optional) |
| Prerequisites | Those courses; a training installation on SQLite and a shell per learner |

## Learning objectives

Learners who pass this capstone have shown that they can:

- give a team limited, tested access to its part of a site;
- schedule cron and keep search current;
- back up consistently and prove a restore on a safe copy;
- deploy a change in the right order and run a maintenance window;
- diagnose and fix an incident and write it down.

## Trainer's notes

### How to run it

1. **Before**: one SQLite training installation per learner with content, no crontab installed, `Transport=file`.
   Prepare two incidents to inject on day 2, each reversible, for example: delayed indexing switched on with cron not
   running (new content not found), and a file under `var/` made unwritable for the site's user (a cache cannot be
   written). Write down how you injected each, so you can undo it.
2. **Checkpoints**: end of day 1 morning (roles and sections), end of day 1 (cron, search, backup), day 2 midday
   (restore test done, first incident).
3. **Incidents**: inject one in the morning and one after lunch of day 2, with a short "user report" in plain words
   ("New articles do not show up in search since yesterday").
4. **Presentation**: the learner walks through the runbook and fixes one problem live that the trainer names.

### Pitfalls

- **Incidents that cannot be undone.** Never inject anything that deletes data. Keep it to settings and permissions.
- **Learners working as root.** Expect it on day 1; the permissions incident teaches why not.

## Hands-on exercise

The brief, as handed to learners.

*You are the new administrator of the Hillside Community Library website (an invented library). The previous
administrator left no notes.*

1. Take stock: siteaccesses, active extensions, roles, sections, cron, mail transport, search engine. Write it down.
2. The library's events team needs to edit only the **Events** area: create the section, the role, the group and one
   test user at `example.invalid`, and prove the limit.
3. Write the crontab lines the site needs (default, frequent and infrequent parts) for the site's user. Do not install
   them on the training machine; run each part once by hand and note the result.
4. Back up the database and the files in one maintenance window; restore them into a second directory as a safe test
   copy; check it; write the one-line restore record.
5. Deploy a small change your trainer gives you (a settings change, or a template), in the documented order, and show
   that it is live.
6. Run a planned maintenance window of 15 minutes with a message, and end it.
7. Handle incident 1 and incident 2 when they are reported: find the cause, fix it, and write an incident note
   (symptom, cause, fix, prevention).
8. Write the runbook: daily, weekly and monthly checks; how to back up and restore; how to deploy; who may do what.

## Expected result

- The stock-taking notes, the limited role proven by the test user, the crontab lines with by-hand runs, a backup,
  a tested restore with a record, a deploy, a maintenance window in the recent changes, two incident notes, and a
  runbook of a few pages.
- The trainer checks the installation, the copy, and the notes, and watches the live fix.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Stock-taking | None | A few items | Most items | All seven areas recorded | Notes |
| Limited access | None | Role without test | Tested, limit leaky | Tested and limited to the section | Role page, test user |
| Cron and search | None | Lines written, not run | Run, search not checked | Lines for the site's user, each part run, search current | Notes, search |
| Backup and restore (must) | None | Backup only | Restored, copy not made safe | Consistent backup, safe copy, checks, record | Copy, notes |
| Deploy and maintenance | None | One of them | Both, order or message wrong | Both, in the documented order, maintenance ended | Notes, maintenance recent changes |
| Incidents | Neither fixed | One fixed, no note | Both fixed, notes thin | Both fixed with cause and prevention | Incident notes, the installation |
| Runbook | None | Fragments | Usable, gaps | Another administrator could follow it | The runbook |

**Pass**: 14 of 21 points or more and **Backup and restore** at 2 or 3.

## Quiz

Debrief questions after the presentations.

1. Which incident took longest, and which log or page found the cause?
2. Why are the crontab lines written for the site's user, not root?
3. What would you tell the library about its backups, with which evidence?
4. Which part of your runbook would you test first with a colleague?

<details>
<summary>Answers</summary>

1. An open question; good answers name the page or log, for example the search backends page or `var/log/error.log`.
2. Files written by root cannot be replaced by the site's user later
   ([Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md#setup-1-one-user)).
3. That they work, with the restore record: date, backup, time to restore, result, who ran it
   ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).
4. An open question; the restore section is a good answer.

</details>

## Further reading

- [Site administrators, practical task](../module-03-administration-courses/site-administrators-practical-task.md)
  and [Hosting and operations, practical task](../module-04-technical-courses/hosting-operations-practical-task.md)
- [Runbooks](../../../hosting/runbooks/README.md): models for incident notes.
- [Monthly operations checklist](../../../hosting/checklists/monthly-operations.md)

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
