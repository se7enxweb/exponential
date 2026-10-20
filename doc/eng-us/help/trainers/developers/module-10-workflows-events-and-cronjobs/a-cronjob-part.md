---
title: "Lesson 10.3: A cronjob part"
audience: trainer, developer
category: training
order: 103
summary: Learners write a cronjob script in their extension, add it to a part of its own in cronjob.ini, run it with runcronjobs.php and through the console as cron:<part>, and find it on Setup > Cronjobs. 45 minutes.
keywords: developers course, cronjob, cronjob part, cronjob.ini, CronjobSettings, ExtensionDirectories, CronjobPart, Scripts, runcronjobs.php, cron: namespace, isQuiet, cli, @description, Setup Cronjobs, crontab, lock
type: help
---
# Lesson 10.3: A cronjob part

Work that should happen regularly, without a visitor, runs as a cronjob: clean-ups, imports, reports. In this
lesson learners write a cronjob script that removes old rows of the greeting table from lesson 9.3, put it in a part
of its own, run it from the shell and through the console, and find it on the administration's cronjobs page.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 10: Workflows, events and cronjobs](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 9.3](../module-09-the-console-and-persistent-objects/persistent-objects.md): the table and class `mysiteGreeting` |

## Learning objectives

After this lesson, learners can:

- write a cronjob script that uses `$cli` and `$isQuiet` set by the runner;
- add a part with `[CronjobPart-<name>] Scripts[]` and register the extension in `[CronjobSettings]
  ExtensionDirectories[]`;
- run a part with `php runcronjobs.php -s <sa> <part>`, one script with `--script=`, and through the console;
- describe the script with `@description` for the console and the cronjobs page;
- explain the lock each script takes while it runs.

## Trainer's notes

### How to present it

Sources: [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md) and
[Exponential Console, cronjob parts](../../../../bc/6.0/console.md#cronjob-parts-and-the-crontab-added-june-2026).

1. **Scripts and parts** (4 minutes). A script is a PHP file in `cronjobs/` of the installation or of an extension in
   `[CronjobSettings] ExtensionDirectories[]`. A part groups scripts on one schedule: `[CronjobPart-frequent]` runs
   `notification.php`, `workflow.php` and more. The scripts of `[CronjobSettings] Scripts[]` are the default part,
   shown as **global**.
2. **The script** (3 minutes). The runner prepares `$cli`, `$isQuiet` and the database; the script does its job and
   prints only when not quiet. A `@description` in the docblock names it on the console and the cronjobs page.
3. **Running** (3 minutes). `php runcronjobs.php -s <sa> <part>` runs a part; `--script=<file>` runs one script;
   `php bin/php/console cron:<part>` does the same through the console.
4. **Locks** (2 minutes). Each script takes a lock of its own while it runs; a second run prints "Cronjob part locked
   by other process" and skips it.
5. **Scheduling** (3 minutes). **Setup** > **Cronjobs** shows the parts, runs them, and proposes crontab lines;
   `php bin/php/console crontab:list` shows what is scheduled.

### Common questions

**Which siteaccess does a cronjob use?** The one given with `-s`; without it, the default siteaccess.

**Can a cronjob run a long job?** Yes, but keep each run short and resumable: work in batches and let the next run
continue, as the content jobs do.

**How do I test a script before scheduling it?** Run it with `--script=` from the shell or with the page's run
button, and read the output and the logs.

### Pitfalls

- **Running as root.** Files a cronjob writes as root may not be writable for the site. Run as the web server's user.
- **A part nobody schedules.** A part that is not in the crontab never runs. Check **Setup** > **Cronjobs**.
- **Deleting without a limit.** A clean-up deletes in batches with a clear rule (here: older than 30 days).

## Hands-on exercise

On the development installation, in `extension/mysite_hello`, with the table `mysite_greeting`.

1. Create `cronjobs/greeting_cleanup.php`:

   ```php
   <?php
   /**
    * Cronjob greeting_cleanup.php (part "mysite")
    *
    * @description Remove stored greetings older than 30 days
    */
   $limit = time() - 30 * 86400;
   $db = eZDB::instance();
   $rows = $db->arrayQuery( 'SELECT COUNT(*) AS total FROM mysite_greeting WHERE created < ' . (int)$limit );
   $db->query( 'DELETE FROM mysite_greeting WHERE created < ' . (int)$limit );
   if ( !$isQuiet )
   {
       $cli->output( 'Removed ' . (int)$rows[0]['total'] . ' greetings older than 30 days' );
   }
   ```

2. Register the extension's cronjobs and the part:

   ```bash
   php bin/php/console exp:ini add cronjob.ini/CronjobSettings/ExtensionDirectories mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add cronjob.ini/CronjobPart-mysite/Scripts greeting_cleanup.php extension:mysite_hello
   ```

3. Check the file with `php -l`.
4. List the cronjob parts: `php bin/php/console list cron`. Find `cron:mysite`.
5. Run the part: `php runcronjobs.php -s <sa> mysite`.
6. Run only the script: `php runcronjobs.php -s <sa> --script=greeting_cleanup.php`.
7. Run it through the console: `php bin/php/console cron:mysite -s <sa>`.
8. Open **Setup** > **Cronjobs** in the administration and find the part **mysite** and its script.
9. Commit the script and the settings file.

## Expected result

- Step 4 lists `cron:mysite` with "Remove stored greetings older than 30 days".
- Steps 5 to 7 print "Removed 0 greetings older than 30 days" (the greetings of lesson 9.3 are new).
- Step 8: the part's card on the cronjobs page, not yet scheduled.
- The trainer asks each learner how the part would be scheduled.

## Quiz

1. Which setting makes the kernel look for cronjob scripts in an extension?
2. How do you run only one script of a part?
   A. `runcronjobs.php <script>` B. `runcronjobs.php --script=<script>` C. `exp:ezexec <script>` D. `cron:<script>`
3. Which variables does the runner give a cronjob script for output?
4. What happens when the same script is started while it still runs?
5. Where do you see whether a part is scheduled?

<details>
<summary>Answers</summary>

1. `cronjob.ini [CronjobSettings] ExtensionDirectories[]`.
2. **B.** `php runcronjobs.php -s <sa> --script=<script>`.
3. `$cli` for output and `$isQuiet` to stay silent when asked.
4. The second run finds the script's lock, prints "Cronjob part locked by other process" and skips it.
5. On **Setup** > **Cronjobs**, or with `php bin/php/console crontab:list`.

</details>

## Further reading

- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md).
- [The cronjobs console](../../../../features/6.0/cronjobs-console.md) and
  [Pausing cronjobs](../../../../features/6.0/cronjob-pause.md).
- [Content jobs](../../../../features/6.0/content-jobs.md): large jobs in batches.

## Related pages

- [Module 10: Workflows, events and cronjobs](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
