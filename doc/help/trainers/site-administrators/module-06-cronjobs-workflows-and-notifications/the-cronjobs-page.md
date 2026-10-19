---
title: "Lesson 6.2: The cronjobs page"
audience: trainer, admin
category: training
order: 62
summary: Learners read Setup > Cronjobs, run a part and a single script from the browser, follow the output, pause and resume a part, and copy the crontab line the page proposes. 45 minutes.
keywords: site administration, cronjobs page, Setup Cronjobs, Run part, Pause, Resume, Stop running job, Clear logs, Output, Recent runs, crontab, managecronjobs
type: help
---
# Lesson 6.2: The cronjobs page

**Setup > Cronjobs** shows every cronjob part of the installation on one page: whether the crontab runs it, its
schedule in words, its next and last run, and the exact shell command behind it. Administrators can run a part or one
script for a chosen site, follow the output while it works and pause a part during a release. This lesson teaches the
page as the daily tool and keeps the shell from lesson 6.1 as the fallback.

> **Note**
> Every button on this page acts at once on the installation. Practice on your own training installation only.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 6: Cronjobs, workflows and notifications](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | [Lesson 6.1](cronjob-parts-and-the-crontab.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- find a part on **Setup > Cronjobs** and read its status, schedule, next run and last run;
- run one part and one script for a chosen site, and follow the **Output**;
- pause and resume a part with a note, and say what a paused part means for the crontab;
- copy the crontab line the page proposes for a part nothing runs;
- name the policy a role needs to open the page.

## Trainer's notes

### How to present it

Source: [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md), sections 3 to 12, and
[Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md). The page described is the one of the admin4
design.

1. **The way in** (2 minutes). **Setup** in the top menu, then **Cronjobs** in the left column (`/setup/cronjobs`).
   Only roles with the policy `setup/managecronjobs` see it; a new installation gives it to the Administrator role.
2. **Top to bottom** (5 minutes). The page has **Now** (idle or what runs, with **Stop running job**, **Clear logs**
   and **Pause all parts**), **Overview**, **Run and filter**, **Output**, **Cronjob parts**, **Available but not
   activated**, **Recent runs**, **Pauses** and **Crontab**. The list of parts switches between **Compact** (one row
   per part, the default) and **Detailed** (cards).
3. **Running** (4 minutes). Choose the site in **Run for site**, then run a part. The run is a process of its own,
   so it keeps going when the page is closed. Only one job started from the page runs at a time. Show the Output
   following the job live.
4. **Pause** (2 minutes). A paused part is left out by every run, the crontab's too, until it is resumed. A note
   typed in **Note for a pause or resume** goes with the next button pressed and appears under **Pauses**.
5. **Crontab** (2 minutes). The page reads the crontab of the user the web server runs as. For parts nothing runs
   it proposes a line; **Crontab** at the bottom lists them ready to paste.

### Common questions

**Why are two parts marked as needing attention on a new installation?** `unlock` and `cluster_maintenance` are in
`[AdminSettings] ForbiddenParts[]`: they are destructive and belong to an administrator in a shell. Their cards say
*Blocked*.

**I pressed Stop running job and the script went on for a moment.** The page sends `SIGTERM`, not `SIGKILL`, so the
script can release its lock and end cleanly.

**Does Clear logs delete the history?** No. It empties the shared output and error logs; the per-part logs and
**Recent runs** stay.

**The crontab runs a part, yet the page says Not scheduled.** The line probably uses a symlinked path, or it is in
another user's crontab than the web server's. See section 17 of the guide.

### Pitfalls

- **No PHP command line binary.** If the page says in red that no binary was found, every **Run** is disabled. Set
  `cronjob.ini [AdminSettings] PhpCliPath` to the CLI binary of the site's PHP version.
- **Apache and Velocity disagree.** Each web server reads the crontab of the user it runs as, so the *Scheduled*
  figures can differ. Expected; say so before a learner asks.
- **A run that seems to do nothing.** If the crontab runs the same script at that moment, the lock skips it. Read
  the Output for *Cronjob part locked by other process*.

## Hands-on exercise

Use your own training installation, signed in to the administration as an administrator.

1. Open **Setup** in the top menu and choose **Cronjobs** in the left column.
2. Under **Overview**, write down how many parts there are, how many are scheduled and how many need attention.
3. Switch the list to **Detailed**. Find the card of the **Frequent** part. Read its badges, **Schedule** or
   **Suggested schedule**, **Next run** and **Last run from here**.
4. Open the card's scripts list and read what each script does.
5. In **Run for site**, choose your public siteaccess.
6. On the **Frequent** card, press **Run part**. Watch **Now** turn to *Running* and the **Output** follow the job.
7. When it has finished, open **Recent runs** and find your run with its duration and result.
8. Open the scripts of **Frequent** again and press **Run** next to `notification.php` only. Compare the Output with
   step 6.
9. Type `Training: pause test` in **Note for a pause or resume** and press **Pause** on the **Frequent** part.
   Read the card and the **Pauses** section.
10. In a terminal at the installation root, run `php runcronjobs.php -s <site> frequent` and read the notice it
    prints.
11. Back on the page, press **Resume** on the **Frequent** part.
12. Open **Crontab** at the bottom and copy the line proposed for **Frequent** into your notes. Do not install it
    unless your trainer asks you to.

## Expected result

- Step 6: a message that the part was started for the chosen site as a process, and the Output showing a header
  line with the part and the siteaccess, then the scripts.
- Step 7: the run is listed under **Recent runs** with *OK* or a number of issues.
- Step 10: `runcronjobs.php` printed a notice that the part is paused, with the note from step 9, and ran nothing.
- After step 11 the card no longer says *Paused*.
- The trainer checks by opening one learner's **Pauses** section on the projector: it shows the pause and the
  resume with the note.

## Quiz

1. Which policy does a role need to open **Setup > Cronjobs**?
2. True or false: a part paused on the page is still run by the crontab.
3. What does **Stop running job** send to the job?
   A. `SIGKILL` B. `SIGTERM` C. `SIGHUP` D. Nothing; it only hides the job
4. Why can you not run the `unlock` part from the page?
5. You change **Run for site**. What else changes on the page?

<details>
<summary>Answers</summary>

1. `setup/managecronjobs` ([Cronjobs, section 2](../../../../guides/cronjobs.md#2-before-you-start)).
2. **False.** A paused part is left out by every run, whoever starts it, until it is resumed
   ([Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md)).
3. **B.** So the script can release its lock and end cleanly.
4. It is in `[AdminSettings] ForbiddenParts[]` because it is destructive; run it from a shell if you must.
5. The siteaccess in every shell command shown on the page, so a copied command matches what **Run** would do.

</details>

## Further reading

- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md): every area of the page and a
  troubleshooting table.
- [Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md): how pauses are stored and reported.
- [The cronjobs console](../../../../features/6.0/cronjobs-console.md): the feature reference.
- [The cronjob console for hosts](../../../hosting/operations/cronjob-console.md): the same page from a host's view.

## Related pages

- [Module 6: Cronjobs, workflows and notifications](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
