---
title: "Lesson 4.3: The cronjob console"
audience: trainer, hosting, admin
category: training
order: 43
summary: Learners use Setup, Cronjobs to see what the crontab runs, run a part and follow its output, pause and resume parts with a note, and decide what a hosting plan lets customers do there. 40 minutes.
keywords: hosting and operations, cronjob console, Setup Cronjobs, run part, pause, resume, pause all parts, stop running job, PhpCliPath, proc_open, ForbiddenParts, managecronjobs, crontab suggestions
type: help
---
# Lesson 4.3: The cronjob console

Support staff often have no shell on a customer's server. The administration page **Setup**, **Cronjobs** shows what
the crontab runs, runs parts, follows their output and pauses them during a maintenance window. This lesson teaches
the page, what it needs from the hosting platform's PHP, and how to decide who may use it.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 4: Deploying and cron](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Hosting support staff and site administrators |
| Prerequisites | [Lesson 4.2](cron-on-the-server.md); an administrator account on the learner's training installation |

## Learning objectives

After this lesson, learners can:

- read the page's sections and tell whether a part is scheduled, paused or not activated;
- run a part for a chosen site and follow its output;
- pause and resume a part with a note, and explain who is affected by a pause;
- name what the page needs from the platform (a PHP command-line binary, `proc_open`, `exec`, `posix_kill`);
- explain why some parts are blocked and who should get the `setup/managecronjobs` policy.

## Trainer's notes

### How to present it

Source: [The cronjob console](../../../hosting/operations/cronjob-console.md),
[Cronjobs console](../../../../features/6.0/cronjobs-console.md) and
[Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md).

1. **A tour** (5 minutes). On the admin4 design the page **Cronjobs** has the sections **Overview**, **Now**,
   **Run and filter**, **Cronjob parts**, **Available but not activated**, **Recent runs**, **Pauses**, **Crontab** and
   **Output**. The **Crontab** section shows what the crontab holds and suggests lines for parts nothing runs.
2. **Running** (3 minutes). **Run** starts a part or a script for the site chosen in the site list next to it; **Output**
   follows it live; **Stop running job** sends `SIGTERM` so the script can release its lock. Only one job started from
   the page runs at a time.
3. **Pausing** (4 minutes). A paused part is left out by `runcronjobs.php` whoever starts it: the crontab, the console
   commands or the page. The notice names since when, by whom and why. Skipped runs are logged in
   `cronjob_skipped.log`. On several servers the pause files must be on shared storage.
4. **Platform and permissions** (3 minutes). The page needs a PHP CLI binary (`[AdminSettings] PhpCliPath` on hosts
   with several PHP versions), `proc_open`, `exec` and `posix_kill`, and says what is missing instead of failing.
   `cluster_maintenance` and `unlock` are in `ForbiddenParts[]`. The policy `setup/managecronjobs` belongs to the
   Administrator role in a new installation; decide per plan who else gets it.

### Common questions

**A part shows Not scheduled although it runs.** The page reads the crontab of the user the web server runs as; a
crontab under root or a deploy user is not shown. Move the crontab to the site's user.

**Does maintenance mode pause cron?** No. Pause the parts that must not run during the window.

**Stop running job fails with "could not be signalled".** The job runs as a different system user than the web
server that answered, for example Velocity workers and PHP-FPM as different users. Stop it from a shell.

**Do queue workers obey a pause?** Queue workers that run as a systemd service are not cronjobs and keep working.

### Pitfalls

- **Running heavy parts on a shared training server.** Each learner runs parts on their own installation only.
- **Forgetting to resume.** End the exercise with every part resumed and check the **Pauses** section.
- **Labels differ by design.** This lesson describes the admin4 design; on other designs check the labels first.

## Hands-on exercise

Use your own training installation, signed in to its administration as an administrator.

1. Open **Setup**, then **Cronjobs**.
2. In **Overview** and **Cronjob parts**, find the parts **Frequent** and **Global** and note whether each is scheduled
   and when it last ran.
3. Open the section **Crontab** and compare it with the crontab you installed in lesson 4.2.
4. Under **Run and filter**, choose your public site in the site list and press **Run** for the part **Frequent**.
5. Follow the job in **Output** until it ends, then find it in **Recent runs** with its result and duration.
6. Press **Pause** for **Frequent**, and write a note such as `Training: pause test` in
   **Note for a pause or resume (optional)**.
7. In a terminal, run `php runcronjobs.php -s <siteaccess> frequent` and read the notice it prints.
8. Find the skipped run in the log directory's `cronjob_skipped.log` (for example `ls var/log/` and `tail` the file).
9. Back on the page, resume **Frequent** and check that **Pauses** lists no paused part.
10. Find a part listed under **Available but not activated** and read what its scripts do.

## Expected result

- Step 5: the run appears in **Recent runs** with its result.
- Step 7 prints a notice that the part is paused, with the time, the user and the note.
- Step 8: `cronjob_skipped.log` has a line for the skipped run.
- Step 9: no part is paused.
- The trainer checks by asking each learner to read the notice of step 7 aloud.

## Quiz

1. Which runs does a pause on the cronjob page hold back?
   A. only runs started from the page B. only crontab runs C. every run of `runcronjobs.php`, whoever starts it
   D. only the queue workers
2. A part runs from root's crontab, but the page says it is not scheduled. Why?
3. Which PHP functions must the platform allow for the page to start and stop jobs?
4. Why are `cluster_maintenance` and `unlock` blocked on the page?
5. Which policy gives access to the page?

<details>
<summary>Answers</summary>

1. **C.** A paused part is left out whoever starts it ([Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md)).
2. The page reads the crontab of the user the web server runs as, not root's.
3. `proc_open` to start, `exec` to read the crontab, `posix_kill` to stop.
4. Both are destructive and belong to an administrator on a shell, not to a button (`ForbiddenParts[]`).
5. `setup/managecronjobs`.

</details>

## Further reading

- [The cronjob console](../../../hosting/operations/cronjob-console.md): platform needs and support scenarios.
- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md): the user guide.
- [Pausing cronjob parts](../../../../features/6.0/cronjob-pause.md): how pauses are stored and shared.
- [Maintenance mode](../../../hosting/operations/maintenance-mode.md): windows and cron together.

## Related pages

- [Module 4: Deploying and cron](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
