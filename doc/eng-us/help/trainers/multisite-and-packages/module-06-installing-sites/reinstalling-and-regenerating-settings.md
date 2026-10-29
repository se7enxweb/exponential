---
title: "Lesson 6.4: Reinstalling, resetting and regenerating settings"
audience: trainer, admin, developer
category: training
order: 64
summary: Learners choose between DatabaseAction remove, ignore and skip, back up before a reinstall, reset a training site with the same kickstart.ini, regenerate the settings while keeping the data, and recover from a failed run and its maintenance page. 45 minutes.
keywords: multisite and site packages, DatabaseAction, remove, ignore, skip, --db-action, --force, reinstall, regenerate settings, backup, sqlite3 .backup, maintenance mode, maintenance.php off, kickstart.ini.before-exp-install, reset a lab
type: help
---
# Lesson 6.4: Reinstalling, resetting and regenerating settings

An install is not always the first one. Training labs are reset, test systems rebuilt, and sometimes only the
settings should be written again while the content stays. One key decides what happens to the data: `DatabaseAction`
(`--db-action` for `exp:install`). This lesson teaches the three values, the backup that must come before the
destructive one, and how to get out of a failed run.

> **Note**
> `DatabaseAction=remove` destroys every row in the database it names, and nothing asks again: `--force` is the only
> confirmation. Practise only on the installations of lessons 6.2 and 6.3.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 6: Installing sites with the kickstarter and exp:install](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Advanced |
| Audience | Administrators, developers and hosts |
| Prerequisites | [Lesson 6.3](the-kickstarter-and-kickstart-ini.md): an installation made with the kickstarter and its `kickstart.ini` kept aside |

## Learning objectives

After this lesson, learners can:

- explain `remove`, `ignore` and `skip` and choose the right one;
- back up a SQLite database consistently before a reinstall, outside the installation root;
- reset a site to its installed state by running the same `kickstart.ini` again;
- regenerate the settings without touching the data, and merge hand-made changes back;
- end the maintenance mode of a failed run after fixing its cause.

## Trainer's notes

### How to present it

Sources: [6.5 DatabaseAction](../../../../install/06-kickstarter.md#65-databaseaction-read-this-before-you-run),
[6.10 Re-running and resuming](../../../../install/06-kickstarter.md#610-re-running-and-resuming) and
[7.9 Examples](../../../../install/07-console-install.md#reinstall-over-an-existing-installation).

1. **The three values** (5 minutes):

   | Value | Database | Settings | Use it for |
   |---|---|---|---|
   | `remove` | existing tables dropped (SQLite: file emptied), then installed | written | a clean install, a reinstall |
   | `ignore` | added to what is there, tables that exist skipped | written | a new, empty database (the `ini --yes` default) |
   | `skip` | nothing inserted, the administrator untouched | written | regenerating `settings/siteaccess/` and `settings/override/` |

   A missing or misspelled value behaves like `ignore`, which fails on a database that is not empty. Write it out.
   `exp:install` defaults to `--db-action=remove`.
2. **The backup** (4 minutes). Into a directory outside the installation root: a dump inside the document root can be
   downloaded by anyone who guesses its name. For SQLite, `sqlite3 <file> ".backup '<target>'"` gives a consistent
   copy even while the site writes. Check that the backup is not empty before the run.
3. **Regenerating settings** (3 minutes). `skip` rewrites the settings from the file or the options. Hand-made
   changes in `settings/override/` and `settings/siteaccess/` are replaced, so copy them aside first and merge them
   back. Never restore old settings wholesale after a reinstall: node IDs in them may no longer fit.
4. **A failed run** (3 minutes). The site stays in maintenance mode ("The site stays in maintenance mode. After fixing
   the cause: run the kickstarter again, or php bin/php/maintenance.php off"). A window opened before the run stays
   open. After an `exp:install` killed before its clean-up, the earlier `kickstart.ini` is in
   `var/log/kickstart.ini.before-exp-install-<date>`.

### Common questions

**Can I resume a failed run at the failed step?** No; run the whole sequence again after fixing the cause. With
`remove`, a complete second run replaces whatever the failed one left.

**How do I reset a training lab fastest?** The same `kickstart.ini` with `DatabaseAction=remove` and `run --apply`,
or the same `exp:install --force` command. Train the trainer lesson 2.6 covers lab resets.

**I changed the database engine and the site still talks to the old one.** Settings in `settings/override/` win; set
`[DatabaseSettings] DatabaseImplementation` there to the new driver first, or remove the old value.

**Does `skip` keep my content and users?** Yes: nothing is inserted and the administrator is not touched.

### Pitfalls

- **The wrong database.** `--from-installed` or a copied file can point at a live database. Read `Database` twice.
- **A backup in `var/`.** That is inside the installation; use a directory outside it.
- **Settings merged blindly.** After `skip`, merge only the hand-made changes back, block by block.

## Hands-on exercise

Use the installation of lesson 6.3 (`kickstart-course`) and its `kickstart.ini`, which you moved aside.

1. Find the database file: `php bin/php/console exp:ini get site.ini/DatabaseSettings/Database`, then
   `ls -l var/storage/sqlite3/`.
2. In the administration of that site, create a folder `Before reset` and publish it.
3. Back up the database outside the installation (replace the paths):

   ```bash
   mkdir -p ~/course-backups
   sqlite3 var/storage/sqlite3/exponential.db ".backup '$HOME/course-backups/before-reset.sqlite'"
   ls -l ~/course-backups/
   ```

4. Put `kickstart.ini` back into the installation root and set `DatabaseAction=remove` in `[site_details]`.
5. Run `php bin/php/console exp:kickstarter run` (a dry run), then `run --apply`.
6. Check the content tree: the folder *Before reset* is gone; the site is as freshly installed. Note the new
   administrator password from the summary.
7. Add a hand-made setting: `php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Hand-made name" global`.
8. Regenerate the settings, keeping the data: set `DatabaseAction=skip`, and run `run --apply` again.
9. Run `exp:ini where site.ini/SiteSettings/SiteName`: the hand-made line is gone from the global override. Find the backup of the
   override in `var/backup/ini/` from step 7, and set the value again by hand.
10. Move `kickstart.ini` out of the installation root again.

## Expected result

- Step 3: a backup file of more than a few bytes outside the installation.
- Step 6: the content tree is the one of a fresh install, without *Before reset*; a new password was generated.
- Step 9: the hand-made `SiteName` is gone until it is set again; the content was not touched by the
  `skip` run.
- The trainer checks step 3 (size of the backup) before anyone runs step 5.

## Quiz

1. Which `DatabaseAction` empties the database first?
   A. `ignore` B. `skip` C. `remove` D. none
2. What is the default `--db-action` of `exp:install`?
3. Why must a backup not be kept inside the installation's document root?
4. Which value writes only the settings?
5. A run failed and the site shows the maintenance page. What two ways out are there after fixing the cause?

<details>
<summary>Answers</summary>

1. **C.**
2. `remove`.
3. Anyone who guesses its name could download it
   ([6.5](../../../../install/06-kickstarter.md#65-databaseaction-read-this-before-you-run)).
4. `skip`.
5. Run the kickstarter (or `exp:install`) again, or `php bin/php/maintenance.php off`.

</details>

## Further reading

- [6. The kickstarter](../../../../install/06-kickstarter.md): `DatabaseAction`, re-running, logs.
- [7. The console install](../../../../install/07-console-install.md#711-what-can-go-wrong).
- [Maintenance mode](../../../../features/6.0/maintenance-mode.md).
- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md).
- Train the trainer: [Resetting the lab between sessions](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md).

## Related pages

- [Module 6: Installing sites with the kickstarter and exp:install](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
