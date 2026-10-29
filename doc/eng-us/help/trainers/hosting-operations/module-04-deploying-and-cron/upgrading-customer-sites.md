---
title: "Lesson 4.4: Upgrading customer sites"
audience: trainer, hosting, admin
category: training
order: 44
summary: Learners take an inventory of installations, plan an upgrade per customer, walk through the steps of an upgrade within the 6.0 line on a training copy, and check the result with the upgrade check. 45 minutes.
keywords: hosting and operations, upgrade, upgrading customer sites, inventory, exp:systeminfo, dbupdate, data scripts, exp:user-login-check, exp:checkclasses, exp:checkdbfiles, upgrade check, maintenance mode, staging, rollback
type: help
---
# Lesson 4.4: Upgrading customer sites

A host keeps many installations on current releases. Doing that safely is a routine: know what every site runs, test
customized sites on a copy, take a backup right before the window, follow the steps in order and check the result.
This lesson teaches the routine for upgrades within the 6.0 line, as chapter 11 and the upgrading guide describe it.

> **Note**
> Practise on a copy of a training installation. Never run an installer over an existing site, and never upgrade a
> customer site in class.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 4: Deploying and cron](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate to advanced |
| Audience | System administrators and support managers |
| Prerequisites | [Lesson 4.3](the-cronjob-console.md); [Module 3](../module-03-databases/README.md) for backups |

## Learning objectives

After this lesson, learners can:

- state the rules that come before any upgrade: no installer over a site, a backup first, the site's user, forward
  only;
- record a site's release, PHP version, database engine and server;
- choose the part of the upgrading guide that applies to a site's version;
- list the steps of an upgrade within the 6.0 line in order;
- check the result with `exp:checkdbfiles`, **System information** and the **Upgrade check** page.

## Trainer's notes

### How to present it

Source: [Upgrading customer sites](../../../hosting/operations/upgrading-customer-sites.md),
[Upgrading](../../../../guides/upgrading.md) and [Chapter 11: Upgrading](../../../../install/11-upgrading.md).

1. **Rules first** (4 minutes). The setup wizard, the kickstarter and `exp:install` create a new database; an upgrade
   replaces the code and updates the existing database in place. Never add `--force` on a live site. The database step
   cannot be undone by putting files back, so back up first, every time. A published release cannot be changed; the
   way to get a fix is the next version.
2. **Inventory** (3 minutes). **Setup**, **System information**, or `./console exp:systeminfo --json`. Which part of
   the guide applies follows from the version: Part A from 3.10 to 5.3, Part B from 5.4 or 5.90, Part C from an
   earlier 6.0.x.
3. **Plan** (3 minutes). PHP version, customizations, a staging copy restored from the latest backup, an announced
   window and a rollback plan. Group customers by release and by how customized they are.
4. **The steps** (6 minutes). Maintenance on and a fresh backup; new code; autoloads and caches; the database update
   file of the line, once, whole, in order (`update/database/<engine>/6.0/`); the data scripts once per var directory;
   the unique-login check; the checklists of every period passed; `exp:checkclasses`; deploy; maintenance off.
5. **Check** (4 minutes). Caches clear without error, `exp:checkdbfiles` marks nothing missing, the front page loads,
   **System information** shows the new version, a test publish works, **Upgrade check** lists only intended
   differences.

### Common questions

**A SQL file failed half way. Can I fix it by hand?** No. Restore the backup and apply the file again from the start.

**What about MongoDB and Oracle?** MongoDB needs no SQL update file; Oracle's files come with the `ezoracle` extension.

**Can a site on PHP 7.4 upgrade?** Yes, from 6.0.15: the current line runs on PHP 7.4, without Velocity, Layouts
and a few extensions that need PHP 8 ([Why Exponential supports PHP 7.4](../../../why-exponential-supports-php-7-4.md)).

**Is there automation?** The `ezupdate` extension can apply updates; see
[Automated updates with ezupdate](../../../hosting/operations/automated-updates-with-ezupdate.md).

### Pitfalls

- **Overwriting settings.** Keep `settings/override/`, `settings/siteaccess/`, extensions and designs out of any
  command that replaces files.
- **Running as root.** Files created by root in `var/` break the site later.
- **Skipping the period checklists.** The changes people meet most (sign-in without an `ezuser_setting` row, override
  priority, security headers) are listed there.

## Hands-on exercise

Use a copy of your training installation (for example a second install made with `exp:install`), never a customer
site.

1. Record the inventory: `php bin/php/console exp:systeminfo --json | head -40`. Note the release, PHP version,
   database engine and server.
2. In the administration, open **Setup**, **System information** and compare with step 1.
3. Open [Upgrading, Which part do I read?](../../../../guides/upgrading.md#which-part-do-i-read) and note which part
   applies to your training site.
4. Switch maintenance mode on with a message and a duration:

   ```bash
   php bin/php/maintenance.php on --message="Upgrade practice" --until=30m
   ```

5. Take a database backup as in lesson 3.4.
6. List the database update files for your engine: `ls update/database/sqlite/6.0/` (or `mysql`, `postgresql`).
7. Check accounts that share a login: `php bin/php/console exp:user-login-check`.
8. Check own extensions: `php bin/php/console exp:checkclasses`.
9. Check the update chain: `php bin/php/console exp:checkdbfiles`.
10. Open **Setup**, **Upgrade check** and read the two checks it offers (**File consistency check** and **Database
    consistency check**); run them on your training copy only.
11. Switch maintenance mode off: `php bin/php/maintenance.php off`.
12. Write a one-page upgrade plan for an imagined customer site: release, path, staging, window, backup, rollback time.

## Expected result

- Steps 1 and 2 agree on the release and the PHP version.
- Step 6 lists the update file of the line for the engine.
- Steps 7 to 9 report no problem on a clean training installation, or name exactly what to fix.
- Step 10: the checks list only files changed on purpose, if any.
- Step 12: the plan names every item of the checklist in *Upgrading customer sites*.
- The trainer checks two plans against that checklist.

![The upgrade check of a customer site](../../../images/trainers/ho-upgrade.webp)

## Quiz

1. Why must you never run `exp:install` over an existing site to upgrade it?
2. Which step cannot be undone by putting files back?
   A. new code B. clearing caches C. the database update D. maintenance mode
3. A site runs 5.4. Which part of the upgrading guide applies?
4. A database update file stopped half way with an error. What do you do?
5. Name two checks that prove an upgrade worked.

<details>
<summary>Answers</summary>

1. The installers create a new database; an upgrade updates the existing database in place.
2. **C.** That is why the backup comes first, every time.
3. Part B (5.4 or 5.90 to 6.0.0), then the 6.0 line.
4. Restore the backup and apply the file again from the start; never repair by hand and re-run
   ([Upgrading customer sites](../../../hosting/operations/upgrading-customer-sites.md#when-something-goes-wrong)).
5. Any two of: caches clear without error; `exp:checkdbfiles` marks nothing missing; **System information** shows the
   new version; a test publish works; the **Upgrade check** lists only intended differences.

</details>

## Further reading

- [Upgrading customer sites](../../../hosting/operations/upgrading-customer-sites.md): the routine for hosts.
- [Upgrading](../../../../guides/upgrading.md): every path, with the period checklists.
- [The upgrade check](../../../../guides/upgrade-check.md): what the two checks compare.
- [PHP upgrade](../../../hosting/runbooks/php-upgrade.md): the runbook when PHP changes too.

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
