---
title: "Lesson 4.6: Hosting and operations, practical task"
audience: trainer, hosting, admin
category: training
order: 46
summary: A 75-minute practical task for the end of the Hosting and operations course, back up a SQLite training site consistently, restore it into a second directory as a safe test copy with the file transport and no crontab, check it, find an error by its reference in the log, and write the one-line restore record, scored with a six-criterion rubric. 100 minutes with scoring.
keywords: assessments and practical exercises, hosting and operations, practical task, rubric, backup, sqlite3 .backup, restore, restore test, maintenance, file transport, crontab, ownership, error reference, log, restore record
type: help
---
# Lesson 4.6: Hosting and operations, practical task

"Do your backups work?" is the question every host must answer with evidence. In this task the learner takes a
consistent backup of a training site, restores it as a test copy that cannot reach anyone, checks it, and writes the
record that answers the question.

> **Note**
> This is the trainer's own assessment, not an official certification. Use training installations on SQLite only,
> with test accounts at `example.invalid`. Never use a copy of a customer's site for an assessment.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 4: Assessing the technical courses](README.md) |
| Duration | 100 minutes (5 briefing, 75 task, 20 scoring) |
| Level | Intermediate to advanced |
| Audience | Learners at the end of the Hosting and operations course |
| Prerequisites | [Lesson 4.5](hosting-operations-written-test.md); a shell on a training machine with one SQLite training installation per learner |

## Learning objectives

Learners who pass this task have shown that they can:

- take database and files of a running site in one window;
- restore a site into a separate directory with correct ownership;
- make a restored copy safe: no real mail, no cron, no public access;
- check a restored site and measure the time;
- find a logged error by its reference.

## Trainer's notes

### How to run it

1. **Before**: each learner has a SQLite training installation in a directory they own, served on a local port, and
   a second empty directory for the copy. The `sqlite3` command line tool is installed. Produce one error page on
   each installation (for example open a module that does not exist) and note its reference.
2. **Briefing**: hand out the steps and the rubric; 75 minutes. Learners work as the installation's user, not as root.
3. **Scoring**: read the notes and timings, check the copy's settings, `crontab -l` of the learner's user, and the
   ownership of the copy's `var/`.

### Common questions

**How do we serve the copy?** On another local port, as in
[Serving the lab sites](../../train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md); the copy is never
exposed beyond the machine.

**The copy comes back in maintenance.** If `var/` was copied while maintenance was on, it carries
`var/maintenance.json`; `php bin/php/maintenance.php off` ends it. Learners who find this themselves earn the point
for checking.

### Pitfalls

- **Copying the busy database file.** Scored as a fault under **Consistent backup**.
- **Running as root.** Files owned by root in the copy's `var/` break it later; scored under **Restore**.

## Hands-on exercise

The task, as handed to learners. Work as your installation's user; write down every command and the time each part
took.

1. Switch maintenance on for your training site with a message:
   `php bin/php/maintenance.php on --message="Backup" --until=30m --allow-admin`.
2. Back up the database with SQLite's online backup into a backup directory of your own:

   ```bash
   sqlite3 <path to the database file> ".backup '<backup dir>/site.db'"
   ```

   (the database lives in `var/storage/sqlite3/` on a default SQLite install).
3. In the same window, archive the files: `var/storage/`, `settings/override/`, `settings/siteaccess/` and any
   extension or design of your own. Note what you left out on purpose and why.
4. Switch maintenance off and check the site.
5. Prepare the copy: put the same release of the code into the second directory (as in the course), unpack your
   files archive there and restore the database file into its `var/storage/sqlite3/`.
6. Before the copy runs, make it safe: set `[MailSettings] Transport=file` in the copy's
   `settings/override/site.ini.append.php`, confirm that no crontab line runs the copy (`crontab -l`), and serve it on a
   local port only.
7. Fix ownership of the copy's writable directories, clear every cache and regenerate the autoloads in the copy.
8. Start the copy and check it: the front page, sign-in to the administration with a test account, one image, and the
   newest item you know exists on the original.
9. On the original site, find the error your trainer produced: take its reference from the error page and find the
   matching lines in `var/log/error.log` (or **Setup > Log files**). Copy the lines into your notes without any
   personal data.
10. Write the one-line restore record: date, site, backup used, time to restore, result, who ran it.
11. Stop the copy's server. Leave the copy for the trainer to score; it is removed after scoring.

## Expected result

- A backup directory with `site.db` and a files archive, taken while maintenance was on.
- A copy that answers on its own local port, with `Transport=file`, no crontab line, ownership by the learner's user,
  and the newest item present.
- Notes with the error's log lines, the timings and the restore record.
- The trainer checks the copy's settings, `crontab -l` and `ls -l var/` of the copy.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Consistent backup | Database file copied while busy | Online backup, files missing | Both, outside one window | Online backup and files in one maintenance window | Notes, the backup directory |
| Restore | Copy does not run | Runs, ownership wrong (root files in `var/`) | Runs, caches or autoloads skipped | Runs, ownership, caches, autoloads right | `ls -l var/`, the copy |
| Safe copy (must) | Mail transport unchanged or cron runs it | One of mail or cron handled | Both handled, served beyond localhost | File transport, no cron, local port only | Copy's settings, `crontab -l` |
| Checks | None | Front page only | Three of four checks | Front page, sign-in, image, newest item | Notes |
| Error by reference | Not found | Wrong lines | Right lines, personal data copied | Right lines, nothing personal | Notes, the log |
| Record and timing | None | Timing without record | Record incomplete | Complete one-line record with times | Notes |

**Pass**: 12 of 18 points or more and **Safe copy** at 2 or 3.

## Quiz

Debrief questions to ask after scoring.

1. Why was maintenance on during steps 2 and 3?
2. What would happen if the copy's crontab ran with the default mail transport?
3. Which part of your backup would you encrypt, and why?
4. What does your restore record prove to a customer or an auditor?

<details>
<summary>Answers</summary>

1. So the database and the files describe the same moment
   ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#a-backup-is-two-halves)).
2. Notifications, newsletters and jobs would run against real addresses
   ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).
3. The archive with `settings/override/`, which holds credentials
   ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md#the-files-to-back-up)).
4. That backups work, with evidence of when, how long and by whom
   ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).

</details>

## Further reading

- [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md): the procedure in full.
- [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md): commands for every engine.
- [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md).

## Related pages

- [Lesson 4.5: Hosting and operations, written test](hosting-operations-written-test.md)
- [Module 4: Assessing the technical courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
