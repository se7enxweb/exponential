---
title: "Lesson 10.1: Planning and running an upgrade"
audience: trainer, admin
category: training
order: 101
summary: Learners find the version an installation runs, choose the part of the upgrade guide that applies, read the checklists of the periods they pass, and rehearse the steps of an upgrade within the 6.0 line on a training installation, ending with the verification checks. 50 minutes.
keywords: site administration, upgrade, upgrading, version, ezsite_data, Part C, dbupdate, autoloads, clear caches, exp:checkclasses, exp:checkdbfiles, changelog, behavior changes, Velocity deploy
type: help
---
# Lesson 10.1: Planning and running an upgrade

An upgrade is routine when it is planned: know the version you run, take a backup, put the new code in place, apply
the database update once, read what changed in the periods you pass, and verify. This lesson teaches the plan and
rehearses its steps on a training installation, so that the first real upgrade is not the first time anyone runs them.

> **Note**
> Rehearse on a training installation or a copy. A published release cannot be changed: the fix for a problem in a
> release is the next release, never a changed one.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 10: Upgrades, extensions and packages](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Intermediate to advanced |
| Audience | Site administrators, support staff |
| Prerequisites | [Module 9](../module-09-backups-and-maintenance/README.md); a training installation with shell access |

## Learning objectives

After this lesson, learners can:

- find the version an installation runs, in the administration and from the database;
- choose the part of the upgrading guide that applies to a version;
- list the steps of an upgrade within the 6.0 line in the right order;
- find and read the checklists of the periods between two versions;
- verify an upgrade with the commands and checks of the guide.

## Trainer's notes

### How to present it

Sources: [Upgrading: from 4.x, 5.x or an earlier 6.0.x to today](../../../../guides/upgrading.md) and
[Chapter 11: Upgrading](../../../../install/11-upgrading.md).

1. **Which version** (3 minutes). **Setup > System information** names it. In the database, the rows
   `ezpublish-version` and `ezpublish-release` of `ezsite_data` keep their historic spelling on purpose; they belong to
   the old name of the product, which was discontinued by its creator after almost 18 years.
2. **Which part** (3 minutes). Part A for 3.10 up to 5.3, then Part B; Part B for 5.4 or 5.90; Part C for any 6.0.x.
   Sites coming from LoveStack have a read-only check, `exp:lovestack check`, first.
3. **Part C in order** (6 minutes). Step 1: back up, always. C1: put the new code in place, regenerate the autoloads,
   clear the caches, reload the PHP-FPM that serves the site or deploy Velocity. C2: apply
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` once, whole, in order; then the audit tables and the trash
   records scripts. C3: read the checklist of each period you pass. C4: check your own extensions with
   `exp:checkclasses`.
4. **The three changes people most often meet** (3 minutes). Accounts without an `ezuser_setting` row cannot sign in;
   overrides follow `Priority`; security headers, cookie settings and a stricter upload check are on by default.
5. **Verify** (3 minutes). Clear caches, `exp:checkdbfiles`, the front page, a sign-in, **Setup > System
   information**, a test publish, and **Setup > Upgrade check** (lesson 10.2). Clear the content cache and Velocity's
   response cache after the reload.
6. **When it goes wrong** (2 minutes). A SQL file that failed half way: restore the backup and apply it again from the
   start. Never repair by hand and re-run.

### Common questions

**How long does an upgrade take?** The guide plans twenty minutes for a small site. The risky step is the database,
which is why the backup comes first.

**Do we upgrade straight from 5.x to the newest 6.0?** Part B takes the site to 6.0.0, then Part C to today. Read every
period checklist in between.

**Where are the release notes of the extensions?** In the extensions' own changelogs; each extension carries its
version in `ezinfo.php` and `extension.xml`, shown on **Setup > Extensions**.

### Pitfalls

- **Skipping the backup** because "it is only a minor version".
- **Applying the update file twice.** Apply it once; a statement that fails because a column exists is skipped alone.
- **Forgetting the PHP processes.** New code is not live until the PHP-FPM that serves the site is reloaded or
  Velocity is deployed; a page rendered by the old code may then sit in a cache.
- **`ezpgenerateautoloads.php -k` with extra working copies of the code inside the installation root.** Exclude them,
  or kernel classes are mapped into the copies.

## Hands-on exercise

Use your own training installation, in a terminal at the installation root. The exercise rehearses the steps on the
version you already run; nothing is upgraded.

1. Open **Setup > System information** and write down the version.
2. Read the same from the database, read only (SQLite example; replace the file name with yours):

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
   ```

3. In [Upgrading](../../../../guides/upgrading.md#which-part-do-i-read), decide which part applies to a site on
   6.0.12 and to one on 5.4.0. Write both answers down.
4. Open the table of [C3](../../../../guides/upgrading.md#c3-read-the-checklist-of-each-period-you-pass) and list the
   documents a site on 6.0.12 must read.
5. Step 1, back up: repeat steps 2 to 5 of lesson 9.3 (maintenance on, database, files, maintenance off).
6. C1, as after a code change:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

   If the site runs on Velocity, preview the deploy: `php bin/php/console exp:velocity deploy --dry-run`.
7. C4, check the classes: `php bin/php/console exp:checkclasses`.
8. Verify: `php bin/php/console exp:checkdbfiles`; open the front page; sign in; publish a test item and see it on
   the site.
9. Read the "If something goes wrong" table of the guide and explain to your neighbor what you would do if C2 failed
   half way.

## Expected result

- Step 2 shows a version row such as `6.0.15stable` (or the version of your release).
- Step 3: Part C for 6.0.12; Part B, then Part C, for 5.4.0.
- Step 4 lists the changelog and behavior change documents of every period after 6.0.12.
- Steps 6 to 8 run without errors; the test item appears on the site.
- The trainer checks by asking one learner to say the steps of Part C in order, from memory.

![Updates and packages: packages that need attention and when updates were last checked](../../../images/trainers/sa-updates.webp)

## Quiz

1. Where in the administration do you read the version an installation runs?
2. Put these in order: apply the database update, back up, put the new code in place, read the period checklists,
   verify.
3. What do you do when the database update file fails half way?
   A. Fix the failed statement by hand and continue B. Restore the backup and apply the file again from the start
   C. Apply the file a second time D. Skip the file
4. Which command shows a PHP incompatibility in an extension's classes?
5. Why clear the content and response caches after reloading PHP, and not before?

<details>
<summary>Answers</summary>

1. **Setup > System information**.
2. Back up, put the new code in place, apply the database update, read the period checklists, verify
   ([Upgrading, Part C](../../../../guides/upgrading.md#part-c-from-any-60x-to-today)).
3. **B.** Never repair by hand and re-run.
4. `exp:checkclasses`, which loads every declared class and reports those PHP refuses.
5. A page rendered by the old code between the clear and the reload would be cached again and served as if the
   upgrade had not worked.

</details>

## Further reading

- [Upgrading](../../../../guides/upgrading.md): every part, the checks and the troubleshooting table.
- [Chapter 11: Upgrading](../../../../install/11-upgrading.md): the update files per engine.
- [Upgrading customer sites](../../../hosting/operations/upgrading-customer-sites.md): upgrades at a host.
- [Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md): what the current line changes.

## Related pages

- [Module 10: Upgrades, extensions and packages](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
