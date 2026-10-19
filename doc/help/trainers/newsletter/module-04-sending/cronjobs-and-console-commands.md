---
title: "Lesson 4.3: Cronjobs and console commands"
audience: trainer, admin
category: training
order: 43
summary: Administrators run the newsletter queue by hand with a dry run first, read the status command, and write the crontab lines for the newsletter parts on a training installation. 45 minutes.
keywords: newsletters with cjw newsletter, cronjob, runcronjobs, cjw_newsletter, cjw_newsletter_mailqueue_create, cjw_newsletter_mailqueue_process, cjw_newsletter_mailbox, ext:cjw_newsletter:queue, dry run, status, lock, crontab
type: help
---
# Lesson 4.3: Cronjobs and console commands

Editors press buttons; administrators make sure the work behind them runs. The newsletter's work is done by cronjob
parts and by console commands that run the same code. In this lesson administrators run a queue by hand, always with
a dry run first, and set up the crontab that a real installation needs.

> **Note**
> The queue command sends whatever is due through `TransportMethodCronjob`. Run it only on the training installation
> after checking that the method is `file` and every subscriber is at `example.invalid` (lesson 1.2).

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 4: Sending](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Administrators |
| Prerequisites | [Lesson 4.2](scheduling-and-sending-an-edition.md); shell access to the learner's own training installation |

## Learning objectives

After this lesson, learners can:

- name the four newsletter cronjob parts and what each does;
- run `ext:cjw_newsletter:queue` with `--dry-run`, then for real, and read its output;
- explain the run locks and what "Another queue run is active." means;
- write crontab lines for the newsletter parts and the bounce part;
- find when each part last ran.

## Trainer's notes

### How to present it

Sources: [Cronjob parts](../../../../specifications/6.0/cjw_newsletter.md#cronjob-parts),
[Console commands](../../../../specifications/6.0/cjw_newsletter.md#console-commands),
[10.5 Newsletters](../../../../install/10-after-installing.md#105-notifications-and-newsletters) and
[Cronjobs](../../../../guides/cronjobs.md).

1. **The parts** (5 minutes):

   | Part | What it does |
   |---|---|
   | `cjw_newsletter` | Creates the queue, then sends (both parts below) |
   | `cjw_newsletter_mailqueue_create` | Confirms waiting users, wakes due sends, builds the queue |
   | `cjw_newsletter_mailqueue_process` | Sends the mails that wait in the queue |
   | `cjw_newsletter_mailbox` | Reads the bounce mail accounts; not part of `cjw_newsletter` |

2. **The commands** (5 minutes). `ext:cjw_newsletter:queue` (`--dry-run`, `--create-only`, `--send-only`),
   `mailbox`, `import`, `repair` and `status`, each with `--help`. Exit code 0 is success, 1 is a failure or another
   run holding the lock. The cron parts, the commands and the dashboard buttons all call the same runner.
3. **Locks** (3 minutes). Each kind of run takes a file lock, so two queue runs never overlap, whoever starts them.
   A second run at the same time prints `Another queue run is active.` and exits with 1.
4. **Last runs** (2 minutes). The time, the user (`cron`, `console`, `job`) and the totals of each kind's last run
   are kept and shown on the dashboard and by `status`.
5. **The crontab** (5 minutes). Every ten minutes for `cjw_newsletter`, hourly for `cjw_newsletter_mailbox`, as in
   [10.5](../../../../install/10-after-installing.md#105-notifications-and-newsletters). The siteaccess given with
   `-s` must be the one the lists render with.

### Common questions

**Why `php bin/php/console` and not `./console`?** Both start the same console; the docs use either form. Use the one
your installation has.

**Running as root?** Scripts refuse to run as root unless `--allow-root-user` is given. Run them as the web server
user so the files they write have the right owner.

**Does a dry run write an audit event?** No. A real run writes one event per run when the audit is on
([Audit events](../../../../specifications/6.0/cjw_newsletter.md#audit-events)).

**What does `repair` do?** It removes subscriptions of removed users and mails that can never be sent; run it with
`--dry-run` first.

### Pitfalls

- **Running the queue as root** leaves root-owned files in `var/`, which the web server cannot write later.
- **The wrong siteaccess in the crontab** renders links for the wrong site.
- **Forgetting the mailbox part.** Bounces are never read; the dashboard reports it (module 5).
- **A real transport on a test installation.** Always read the transport line of `status` before a real run.

## Hands-on exercise

Use your own training installation, in its root directory, as the web server user (or add `--allow-root-user` if
your trainer tells you so). Replace `<name>` with your first name.

1. Check the transport and the outbox:

   ```bash
   php bin/php/console ext:cjw_newsletter:status
   ```

2. In the administration, schedule your edition `Tips for week 1` with **Send Newsletter** as in lesson 4.2
   (tracking **Off**). Do not press **Send now**.
3. Run the dry run and read what it would do:

   ```bash
   php bin/php/console ext:cjw_newsletter:queue --dry-run
   ```

4. Run the queue for real and read the two summary lines:

   ```bash
   php bin/php/console ext:cjw_newsletter:queue
   ```

5. Run `status` again and find the last run of the queue, its user and its totals.
6. Run the same work through the cron runner, to see that nothing is left:

   ```bash
   php runcronjobs.php cjw_newsletter
   ```

7. Read the help of the repair command and run it as a dry run:

   ```bash
   php bin/php/console ext:cjw_newsletter:repair --help
   php bin/php/console ext:cjw_newsletter:repair --dry-run
   ```

8. Write (do not install) the crontab lines for your installation's path and public siteaccess, following
   [10.5](../../../../install/10-after-installing.md#105-notifications-and-newsletters).

## Expected result

- The dry run reported the send and the number of items it would create, and changed nothing.
- The real run printed the queue line (sends and items created) and the sent line (mails sent, failed, sends
  finished); the outbox has the new files, all at `example.invalid`.
- `status` shows the last queue run by `console`.
- Each learner has two crontab lines with their path and siteaccess.
- The trainer checks one learner's terminal output and crontab lines.

## Quiz

1. Which cronjob part is not included in the part `cjw_newsletter`?
2. What does `ext:cjw_newsletter:queue --dry-run` change?
   A. it creates the queue only B. nothing C. it sends to test addresses D. it clears the outbox
3. A queue run prints `Another queue run is active.` and exits with 1. What happened?
4. Why should the queue not run as root?
5. Which command prints what the dashboard shows?

<details>
<summary>Answers</summary>

1. `cjw_newsletter_mailbox` ([Cronjob parts](../../../../specifications/6.0/cjw_newsletter.md#cronjob-parts)).
2. **B.** A dry run shows what a run would do and changes nothing ([specification](../../../../specifications/6.0/cjw_newsletter.md#example-send-a-newsletter-from-the-shell-and-look-at-the-result)).
3. Another run of the same kind held the lock ([specification](../../../../specifications/6.0/cjw_newsletter.md#cronjob-parts)).
4. Files it writes in `var/` would belong to root, and the web server user must be able to write them too ([10.5](../../../../install/10-after-installing.md#105-notifications-and-newsletters)).
5. `ext:cjw_newsletter:status` ([Console commands](../../../../specifications/6.0/cjw_newsletter.md#console-commands)).

</details>

## Further reading

- [Specification: the cjw_newsletter extension](../../../../specifications/6.0/cjw_newsletter.md#console-commands):
  every command and option.
- [Cronjobs](../../../../guides/cronjobs.md): how `runcronjobs.php` and parts work.
- [10.3 Cronjobs](../../../../install/10-after-installing.md#103-cronjobs): a complete crontab.

## Related pages

- [Module 4: Sending](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
