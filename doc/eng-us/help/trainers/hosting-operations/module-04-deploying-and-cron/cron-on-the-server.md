---
title: "Lesson 4.2: Cron on the server"
audience: trainer, hosting, admin
category: training
order: 42
summary: Learners explain cronjob parts and scripts, write a crontab for a customer site under the site's user with the right PHP binary, and diagnose jobs that do not run. 45 minutes.
keywords: hosting and operations, cron, crontab, runcronjobs.php, cronjob parts, frequent, infrequent, cache_cleanup, session_gc, siteaccess, lock, cronjob.ini, notifications not sent
type: help
---
# Lesson 4.2: Cron on the server

An Exponential site depends on scheduled jobs: notifications, workflows, scheduled publishing, delayed search
indexing and cleanup. When cron is missing, nothing breaks loudly; things simply stop happening. This lesson teaches
hosting staff which parts a site needs, how to install them correctly and how to find out why a job did not run.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 4: Deploying and cron](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 4.1](deploying-a-change.md); a training installation and a shell as the site's user |

## Learning objectives

After this lesson, learners can:

- explain the difference between a cronjob script and a cronjob part, and list the scripts of a part;
- name the parts every hosted site needs (`frequent`, the default part, `infrequent`) and what each does;
- write crontab lines that run in the installation root, as the site's user, with the PHP binary the site uses and
  the customer's public siteaccess;
- diagnose the usual failures: notifications not sent, a part always locked, root-owned files.

## Trainer's notes

### How to present it

Source: [Cron jobs](../../../hosting/operations/cron-jobs.md) and
[Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md).

1. **Parts and scripts** (4 minutes). A script is a PHP file in a cronjob directory; a part is a named list of scripts
   in `settings/cronjob.ini` that run together. `php runcronjobs.php <part>` runs a part; without a name it runs the
   default part, which the administration calls **Global**.
2. **What a site needs** (5 minutes). `frequent` always (notifications, workflows, content jobs, the audit trail);
   the default part always (scheduled unpublish and hide, delayed indexing); `infrequent` (link check, e-mail
   preference retention); `cache_cleanup` for busy sites; `session_gc.php` where the operating system's session
   cleanup does not run; `logs` when the kernel manages logs.
3. **The crontab** (5 minutes). Show the example of *Cron jobs*: `cd` into the installation root,
   `php runcronjobs.php -q -s <siteaccess> <part>`, output to a log file. `-s` names the siteaccess whose content and
   URLs the jobs act on.
4. **User and PHP** (3 minutes). Install the lines in the site user's crontab, with the full path of the PHP binary
   the site runs on. As root, scripts refuse to start without `--allow-root-user`, and their files belong to root.
5. **Hosts that limit cron, maintenance, several nodes** (3 minutes). One `frequent` line is the minimum. Maintenance
   mode does not stop cron. On several nodes, one crontab on one node.

### Common questions

**Can the Velocity scheduler replace cron?** No. It does not run an arbitrary command-line PHP script; keep the
system's cron.

**What does "Cronjob part locked by other process" mean?** A previous run of the same script still runs, or was
killed. A lock older than `[CronjobSettings] MaxScriptExecutionTime` (43200 seconds) is taken over.

**Where do I see every part?** `php runcronjobs.php --list`, or `./console list cron`.

**Is there a ready-made file?** The repository ships `exponential.cron`, with a variable for the installation root and
the PHP binary.

### Pitfalls

- **A crontab under root.** The jobs then write files the site cannot replace. Always `crontab -e -u <site user>`.
- **The wrong PHP.** `php -v` in cron may differ from the shell's on a host with several versions. Use the full path.
- **Forgetting `-s`.** Jobs then run in the default siteaccess, which may not be the customer's public one.
- **Log files that grow forever.** Rotate the `cron-*.log` files with the system's logrotate.

## Hands-on exercise

Use your own training installation and a shell as the user that owns its files.

1. In the installation root, list every part and its scripts: `php runcronjobs.php --list`.
2. Find the scripts of `frequent` and of the default part and note them.
3. Find the full path of your PHP binary: `command -v php` and `php -v | head -1`.
4. Find the name of your public siteaccess: in the administration, open **Setup**, **System information** and read
   the section **Siteaccesses** (or read the site addresses that `php bin/php/console exp:velocity status` lists).

5. Run the `frequent` part once by hand, quietly, and look at the exit status:

   ```bash
   php runcronjobs.php -q -s <siteaccess> frequent; echo "exit $?"
   ```

6. Start the same command twice at once (append `&` to the first) and read the message of the second run.
7. Open your crontab with `crontab -e` and add, with your paths:

   ```text
   */5  * * * *  cd /path/to/installation && /path/to/php runcronjobs.php -q -s <siteaccess> frequent >> var/log/cron-frequent.log 2>&1
   */15 * * * *  cd /path/to/installation && /path/to/php runcronjobs.php -q -s <siteaccess> >> var/log/cron-default.log 2>&1
   ```

8. Wait for the next five-minute mark and check `var/log/cron-frequent.log` and its owner with `ls -l var/log/`.
9. List new files under `var/` with `find var -newer var/log/cron-frequent.log -user root` and check that nothing
   belongs to root.

## Expected result

- Step 1 lists the parts with their scripts, including `frequent` and the default part.
- Step 5 ends with `exit 0`.
- Step 6: one run reports that the part or script is locked by another process, or finishes after the first.
- Step 8: the log file exists and belongs to the site's user.
- Step 9 prints nothing.
- The trainer checks one learner's `crontab -l` on the projector.

## Quiz

1. What is the difference between a cronjob script and a cronjob part?
2. Notifications are never sent. Which part is most likely missing from the crontab?
   A. `infrequent` B. `cache_cleanup` C. `frequent` D. `logs`
3. What does `-s <siteaccess>` decide?
4. Why must the crontab belong to the site's user?
5. True or false: maintenance mode stops cronjobs.

<details>
<summary>Answers</summary>

1. A script is one PHP file; a part is a named list of scripts in `settings/cronjob.ini` that run together on one
   schedule.
2. **C.** Without `frequent`, notifications are not sent and the audit's alerts never fire
   ([Cron jobs](../../../hosting/operations/cron-jobs.md#what-a-hosted-site-needs)).
3. The siteaccess the jobs run in: indexing, sitemaps and notifications act on its content and URLs.
4. So the files the jobs write belong to it; root-owned files break the site later.
5. **False.** Cronjobs run in a shell and are not affected; pause parts on **Setup**, **Cronjobs** instead.

</details>

## Further reading

- [Cron jobs](../../../hosting/operations/cron-jobs.md): parts, crontab, user, limits.
- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md): the full guide.
- [Cron and shell access](../../../hosting/panels/cron-and-shell-access.md): cron in hosting panels.
- [Chapter 10, Cronjobs](../../../../install/10-after-installing.md#103-cronjobs): the installation book's version.

## Related pages

- [Module 4: Deploying and cron](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
