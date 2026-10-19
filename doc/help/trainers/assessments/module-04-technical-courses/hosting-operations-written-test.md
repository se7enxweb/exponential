---
title: "Lesson 4.5: Hosting and operations, written test"
audience: trainer, hosting, admin
category: training
order: 45
summary: A fourteen-question written test for the end of the Hosting and operations course, on writable directories and users, backups and restores, deploying a PHP change, maintenance and monitoring, Velocity health checks, disk-full incidents and mail on test copies, with an answer key that cites the hosting pages. 30 minutes for learners.
keywords: assessments and practical exercises, hosting and operations, written test, writable directories, file permissions, backup, restore, restore test, deploy, exp:velocity deploy, maintenance, 503, health check, watchdog, disk full, file transport
type: help
---
# Lesson 4.5: Hosting and operations, written test

This test closes the Hosting and operations course. It checks the judgment that keeps hosted sites safe: which user
writes which files, what a backup must contain, the order of a deploy, how to tell maintenance from an outage, and how
to keep a restored copy from mailing real people.

> **Note**
> This is the trainer's own assessment, not an official certification.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 4: Assessing the technical courses](README.md) |
| Duration | 45 minutes (5 briefing, 30 test, 10 going through the answers) |
| Level | Intermediate to advanced |
| Audience | Learners at the end of the Hosting and operations course |
| Prerequisites | The Hosting and operations course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- say which directories must be writable and by whom;
- list what a backup contains and how to make database and files agree;
- explain the order of a deploy and why it matters;
- monitor a site correctly during maintenance and with Velocity;
- act safely when a disk is full or a backup is restored for a test.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 30 minutes.
2. Go through the answers with the hosting pages on the projector.

### Common questions

**Our hosts do not run Velocity.** Drop questions 9 and 10 and lower the pass mark by two.

### Pitfalls

- **Learners who reach for `rm -rf var/`.** Question 12 is there for a reason; discuss it.

## The test

1. Which directory must always stay writable, and why?
2. What is the recommended ownership setup for an installation?
3. Why does running a cache clear as root break a site later?
4. A backup consists of two halves. Name them, and the condition they must meet.
5. Which directory of a backup is as sensitive as the server itself, and why?
6. How do you back up a SQLite database of a running site?
7. A PHP class was changed. Which single command deploys it, and how do you see what it would do first?
8. Why are rendered-output caches cleared only after PHP-FPM and Velocity run the new code?
9. Which address answers a health check of a Velocity server, and what does it not prove?
10. What does the Velocity watchdog do, and what does it never do?
11. Your monitor alerts on a 503 during a planned maintenance window. How should the check behave?
12. The disk is full. Name one thing you may remove and one you must never remove.
13. You restore a backup on a test machine. Name two things you do so it cannot act like the live site.
14. Why do you test a restore on another machine, and how often?

<details>
<summary>Answers</summary>

1. `var/` and everything below: caches, compiled templates, logs, uploads, image variations, autoloads, the SQLite
   database ([Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md#what-must-be-writable)).
2. Run everything as the site's user: PHP-FPM pool, Velocity workers, cron, console and Composer
   ([Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md#setup-1-one-user)).
3. Files written by root cannot be replaced by the site user afterwards
   ([Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md#setup-1-one-user)).
4. The database and the files, taken at the same moment
   ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#a-backup-is-two-halves)).
5. `settings/override/`: it holds the database password and other credentials
   ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#the-files-to-back-up)).
6. With SQLite's online backup, `sqlite3 <db file> ".backup '<target>'"`, never by copying a busy file
   ([Database backups per engine](../../../hosting/operations/database-backups-per-engine.md#at-a-glance)).
7. `./console exp:velocity deploy --allow-root-user`; `--dry-run` first
   ([Deploying changes](../../../hosting/operations/deploying-changes.md#one-command-for-a-php-change)).
8. Otherwise pages requested in between are rendered by the old code and cached again
   ([Deploying changes](../../../hosting/operations/deploying-changes.md#the-steps-and-their-order)).
9. `/Q/health` (for example `http://127.0.0.1:8088/Q/health`); it shows the server process answers, not that a page of
   the site renders ([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md#velocity-health-endpoint-and-watchdog)).
10. It starts the default engine again when it is down and nobody meant it to be; it never touches a server that runs
    ([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md#velocity-health-endpoint-and-watchdog)).
11. Pause the check for the window, or accept a 503 with `Retry-After` while maintenance is on; alert if the window
    lasts longer than announced
    ([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md#planned-maintenance-is-not-an-outage)).
12. May: caches through the cache command, rotated log archives through `exp:logs --prune`. Never: `var/storage`,
    `settings/`, or the SQLite files by hand ([Disk full](../../../hosting/runbooks/disk-full.md#what-may-be-removed-and-what-never)).
13. Two of: set the file transport (or `DebugSending`), do not install the crontab, restrict access, remove the copy
    afterwards ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).
14. It proves the backup, the procedure and the time; at least once and after every major upgrade, and on your own
    schedule ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).

</details>

## Scoring

- One point per question; questions 4, 12 and 13 need both parts.
- Pass: 10 of 14 points or more, with questions 5 and 13 correct: both protect other people's data.

## Further reading

- [Hosting companies: start here](../../../hosting/hosting-companies-start-here.md)
- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) and
  [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)
- [Deploying changes](../../../hosting/operations/deploying-changes.md)

## Related pages

- [Lesson 4.6: Hosting and operations, practical task](hosting-operations-practical-task.md)
- [Module 4: Assessing the technical courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
