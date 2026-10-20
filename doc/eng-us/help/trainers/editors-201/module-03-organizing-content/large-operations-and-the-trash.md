---
title: "Lesson 3.4: Large operations and the trash in depth"
audience: trainer, editor
category: training
order: 34
summary: Learners run a large remove, copy or move as a background content job, follow its progress, and use the trash's filters and restore to a new place. 45 minutes.
keywords: editors 201, content job, background, run now, large removal, progress, content jobs, trash, filter, trashed by, restore, original placement, empty trash
type: help
---
# Lesson 3.4: Large operations and the trash in depth

Removing an old campaign of 300 pages, or copying a whole site section, used to be risky in a browser. Exponential
runs such operations in the background as **content jobs**, in batches that survive interruptions. This lesson
shows editors the job pages, and then goes deeper into the trash than Editors 101 did.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 3: Organizing content](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors who reorganize larger parts of a site |
| Prerequisites | [Lesson 3.1](moving-and-copying.md); a folder with 50 or more items on the training site |

## Learning objectives

After this lesson, learners can:

- choose between **run now** and **in the background** on a confirmation page and explain the difference;
- follow a content job's progress and find all their jobs;
- filter the trash by **Trashed by**, **Type** and dates, and read **Original Placement**;
- restore an item whose original place is gone, and explain why **Remove selected** and **Empty trash** in the trash
  are final.

## Trainer's notes

### How to present it

Sources: [Content jobs](../../../../features/6.0/content-jobs.md) and
[The trash view](../../../../bc/6.0/trash.md).

1. **Content jobs** (6 minutes). Start a remove, copy, move, hide or section assignment on a large subtree. The
   confirmation page shows what the operation touches (counts, languages, objects that keep other locations) and a
   choice: **run now** or **in the background**. From 50 nodes on, "in the background" is offered first. The
   browser then follows the job's page: who started it, what it does, progress with time left, batches, and buttons
   to cancel, resume, or download the log. The list of jobs is at `content/jobs` (where your role allows).
2. **Why it matters** (2 minutes). A job works in batches and resumes where it stopped after an interruption. You can
   leave the page while it works.
3. **The trash, deeper** (5 minutes). *Trash (n)* lists **Name**, **Type**, **Section**, **Original Placement** and
   **Date trashed**. Filters: **Trashed by**, **Type**, **Trashed from** ... **to**, then **Filter**. **Restore** puts
   an item back where it was if that place still exists; if that place is in the trash too, restore the parent
   first; otherwise you choose a new place. **Remove selected** and **Empty trash** delete for good.

### Common questions

**Do I have to wait on the job page?** No. Leave it; the job continues. Come back through the jobs list.

**Can I cancel a job halfway?** Yes, with its cancel button. A partial copy can be removed from the job page.

**Why can I not see the jobs list?** It needs its own permission (`content/jobs`); ask an administrator.

### Pitfalls

- **Running a large operation "now" by habit.** On a busy site the request may time out. Prefer the background.
- **Emptying the trash.** Many editors' roles should not allow it. If yours does, think twice.
- **Restoring into the wrong place.** When the page asks for a new place, read the path before confirming.

## Hands-on exercise

Use the training installation. The trainer prepared, for each learner, a folder `Old campaign` with 50 or more items
(for example created with **Create multiple new** or a copy of a sample folder).

1. Open `Old campaign` and press **Remove**.
2. Read the confirmation page: the counts, and the choice between running now and in the background. Choose **in the
   background** and confirm.
3. Watch the job page: progress, batches and time left. Write down the job's number and how long it took.
4. Open the jobs list (`content/jobs` after the administration's address) if your role allows, and find your job.
5. Open **Trash**. Set **Trashed by** to yourself and **Type** to *Folder*, and press **Filter**.
6. Find `Old campaign` and read its **Original Placement**.
7. Remove your course folder's subfolder `Projects` (lesson 2.2) too. Then try to restore one article from inside
   `Projects` alone: read what the page says, and follow it (restore the parent first, or choose a new place).
8. **Restore** `Old campaign` and check that it is back with its items.

## Expected result

- The removal of `Old campaign` ran as a background job with visible progress.
- The filtered trash showed the learner's folders with their original placements.
- Step 7 showed that an item whose parent is in the trash needs the parent restored first or a new place.
- `Old campaign` is back with all its items.
- The trainer checks by opening one learner's job page or the jobs list on the projector.

![The trash with its filters by who removed the items, their type and the date](../../../images/trainers/e201-trash-filters.webp)

## Quiz

1. From how many nodes on is "in the background" offered first, with the shipped settings?
   A. 10 B. 50 C. 500 D. 1000
2. What can you do while a content job runs?
3. Name the three filters of the trash.
4. You restore an item whose original parent is in the trash too. What are your options?
5. True or false: **Empty trash** can be undone from the audit trail.

<details>
<summary>Answers</summary>

1. **B.** ([Content jobs](../../../../features/6.0/content-jobs.md#run-an-operation-as-a-job)).
2. Leave the page and keep working; the job continues in batches.
3. **Trashed by**, **Type** and the dates (**Trashed from** ... **to**).
4. Restore the parent first, or choose a new place.
5. **False.** **Remove selected** and **Empty trash** in the trash delete for good.

</details>

## Further reading

- [Content jobs](../../../../features/6.0/content-jobs.md): running large operations in the background.
- [The trash view](../../../../bc/6.0/trash.md): who trashed what, and where it was.
- [Publishing, versions and the trash](../../../publishing-and-versions.md#the-trash): the trash in short.

## Related pages

- [Module 3: Organizing content](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
