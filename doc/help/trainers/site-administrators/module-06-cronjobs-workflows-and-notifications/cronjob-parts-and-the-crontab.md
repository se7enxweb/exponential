---
title: "Lesson 6.1: Cronjob parts and the crontab"
audience: trainer, admin
category: training
order: 61
summary: Learners explain what a cronjob part and a script are, list every part of their installation from the shell, run one part by hand and write the crontab line that schedules it. 40 minutes.
keywords: site administration, cronjobs, cronjob part, runcronjobs.php, crontab, frequent, infrequent, global part, cron:frequent, scheduling
type: help
---
# Lesson 6.1: Cronjob parts and the crontab

Much of what a site does happens when nobody is looking: notifications go out, approved articles are published,
expired content is hidden, sitemaps are built. All of that is done by cronjobs. A site whose cronjobs do not run
looks fine for days and then fails in ways that are hard to trace. This lesson gives learners the model (parts and
scripts) and the shell commands that run and schedule them.

> **Note**
> Use a training installation, never a live site. Running a part on a live site sends real mail and publishes
> waiting content.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 6: Cronjobs, workflows and notifications](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | A training installation with shell access; basic command line |

## Learning objectives

After this lesson, learners can:

- explain the difference between a cronjob script and a cronjob part, and what the *global* part is;
- list every part and its scripts with `runcronjobs.php --list`;
- run one part and one single script for a chosen siteaccess;
- write a crontab line that runs the `frequent` part every five minutes from the installation root;
- name two consequences of a part that never runs.

## Trainer's notes

### How to present it

Source: [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md), sections 1, 12 and 13.

1. **Scripts and parts** (4 minutes). A *script* is a PHP file in a `cronjobs/` directory that does one job. A
   *part* groups scripts that run together on one schedule; it is a section `[CronjobPart-<name>]` with
   `Scripts[]` lines in `settings/cronjob.ini`. Show the shipped `frequent` part on the projector: it runs
   `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php` and `mailbounces.php`, in that order, in one
   process. Extensions add parts in their own `cronjob.ini.append.php`.
2. **The global part** (2 minutes). The scripts of `[CronjobSettings] Scripts[]` run when `runcronjobs.php` gets
   no part name. The administration calls this the **global** part. One of its scripts, `hide.php`, hides content
   whose date has passed.
3. **Running by hand** (4 minutes). Show `--list`, a part, and `--script=`. Explain `-s <siteaccess>`: a job reads
   the settings of the siteaccess it runs under, so notifications and sitemaps act on that site's content and URLs.
4. **Locks** (2 minutes). Every script takes a lock while it runs. A second run of the same script prints
   *Cronjob part locked by other process* and skips it. That is the lock working, not an error.
5. **Scheduling** (3 minutes). Show a crontab line that starts with `cd <installation root> &&`. Run it as the
   user the web server runs as, so the files a job writes stay writable for the site.

### Common questions

**How often should the frequent part run?** The notification guide recommends every five minutes as a good
default; a run with nothing to do is cheap.

**Is there a console command for each part?** Yes. Every part is also `cron:<part>` in the console, for example
`php bin/php/console cron:frequent -s <site>`. The global part has no `cron:` command; run it with
`runcronjobs.php` and no part name.

**Why does `--list` show the same script three times?** It prints each script once for every cronjob directory
it is found in. When several settings files append the same directory, the script repeats.

**Can a part be held back during a release?** Yes, with **Pause** on the cronjobs page; lesson 6.2 shows it.

### Pitfalls

- **Running as root.** As root every command needs `--allow-root-user`, and the files it writes may become
  unwritable for the web server. Prefer the site user on real servers; in class, say which user you are.
- **A symlinked path in the crontab.** The cronjobs page recognizes a line only when it contains the real path
  of the installation root. Use the real path after `cd`.
- **The wrong siteaccess.** Without `-s` the default siteaccess is used; on a multisite training installation that
  may not be the site learners look at.

## Hands-on exercise

Use your own training installation (for example one installed with `php bin/php/console exp:install`, see
[The console install](../../../../install/07-console-install.md)). Work in a terminal at the installation root.

1. Open `settings/cronjob.ini` read-only (`less settings/cronjob.ini`) and find `[CronjobPart-frequent]`. Write down
   its scripts.
2. Find `[CronjobSettings]` and its `Scripts[]` lines. These form the global part.
3. List every part and its scripts:

   ```bash
   php runcronjobs.php --list
   ```

4. Find the name of your public siteaccess: it is in `settings/override/site.ini.append.php` under
   `[SiteAccessSettings] AvailableSiteAccessList[]`. Use it as `<site>` below.
5. Run only the notification script:

   ```bash
   php runcronjobs.php -s <site> --script=notification.php
   ```

   Read the output: the siteaccess used, the script started and completed, and the elapsed time.
6. Run the whole `frequent` part the console way:

   ```bash
   php bin/php/console cron:frequent -s <site>
   ```

7. Show what the console knows about the part: `php bin/php/console help cron:frequent`.
8. Print your crontab without changing it: `php bin/php/console crontab:list` (or `crontab -l`).
9. On paper (do not install it yet), write the line that runs `frequent` every five minutes from your installation
   root and appends its output to `var/log/cron-frequent.log`.
10. Compare your line with your neighbor's and with the example in the notification guide.

## Expected result

- Step 3 printed a list that starts with the parts of the kernel, such as `CronjobPart-frequent:` followed by its
  scripts.
- Step 5 printed lines like *Running cronjobs/notification.php*, *Completing cronjobs/notification.php* and
  *Elapsed time*.
- Step 9 gave a line of this shape:

  ```cron
  */5 * * * * cd /path/to/installation && php runcronjobs.php -s <site> frequent >> var/log/cron-frequent.log 2>&1
  ```

- The trainer checks by asking two learners to read their line aloud and point out the `cd`, the siteaccess and the
  part name.

## Quiz

1. What is a cronjob part?
   A. One PHP script B. A named list of scripts in `cronjob.ini` C. A line in the crontab D. A database table
2. Which part does `php runcronjobs.php` run when you give no part name?
3. A run prints *Cronjob part locked by other process*. What does it mean, and what do you do?
4. Why should a crontab line start with `cd <installation root> &&`?
5. Name two things that stop happening on a site whose `frequent` part never runs.

<details>
<summary>Answers</summary>

1. **B.** A part is a `[CronjobPart-<name>]` section with `Scripts[]` lines
   ([Cronjobs, section 1](../../../../guides/cronjobs.md#1-parts-scripts-and-the-global-part)).
2. The scripts of `[CronjobSettings] Scripts[]`, which the administration shows as the **global** part.
3. The same script is running in another process right now (for example from the crontab). Wait; the lock keeps
   the two runs apart.
4. `runcronjobs.php` is run from the installation root, and the cronjobs page recognizes a line by the real path
   of that root.
5. Any two of: notifications are not sent, approved content is not published (workflow), content jobs do not run,
   the audit trail's spools are not processed, mail bounces are not read.

</details>

## Further reading

- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md): the full guide, including
  every setting.
- [After installing, 10.3 Cronjobs](../../../../install/10-after-installing.md#103-cronjobs): the cronjob step of
  the installation book.
- [Cron jobs for hosts](../../../hosting/operations/cron-jobs.md): scheduling on a hosting server.

## Related pages

- [Module 6: Cronjobs, workflows and notifications](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
