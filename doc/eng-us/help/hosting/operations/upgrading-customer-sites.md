---
title: Upgrading customer sites
audience: hosting, admin
category: hosting
order: 190
summary: How a host brings customer installations to a newer Exponential release - taking inventory of versions, planning windows, the code, database and cache steps of an upgrade within the 6.0 line, the checks that prove it worked, and what to do when it does not.
keywords: upgrade, upgrading, Exponential 6.0, dbupdate, database update, version, ezsite_data, upgrade check, checkdbfiles, checkclasses, maintenance window, staging, PHP version, customer communication
type: help
---
# Upgrading customer sites

This page is for system administrators and support managers at a host who keep many Exponential installations on
current releases. It answers: how to find which release each site runs, how to plan upgrades across customers, which
steps an upgrade within the 6.0 line takes, how to check the result, and how to recover when a step fails.

![Setup, Upgrade check: compare the files and the database with this release](../../images/hosting/upgrade-check.webp)

![Git Manager: the Git checkout of the installation, its branch, the commits waiting and the buttons to update](../../images/hosting/git-manager.webp)

The procedure is the installation book's [chapter 11, Upgrading](../../../install/11-upgrading.md) and the guide
[Upgrading](../../../guides/upgrading.md); this page arranges it for a host. Customers coming from older major versions
or other products are on [Migrating customers](../migrations/README.md).

## Contents

1. [Rules before anything else](#rules-before-anything-else)
2. [Take inventory](#take-inventory)
3. [Plan the upgrade per customer](#plan-the-upgrade-per-customer)
4. [The upgrade within the 6.0 line](#the-upgrade-within-the-60-line)
5. [Check the result](#check-the-result)
6. [When something goes wrong](#when-something-goes-wrong)
7. [Checklist](#checklist)

## Rules before anything else

- **Never run an installer over an existing site.** The setup wizard, the kickstarter and `exp:install` create a new
  database. "An upgrade replaces the code and updates the existing database in place." `exp:install` refuses to run
  where an installation exists; "Do not add `--force` to get past either on a live site"
  ([11.1](../../../install/11-upgrading.md#111-before-you-start)).
- **Back up first, every time.** "The database step is the one that cannot be undone by putting files back."
- **Run as the site's user.** Files created by root in `var/` break the site later.
- **A published release cannot be changed.** "The way to get a fix is the next version." Plan to move forward, not to
  patch a release in place.

## Take inventory

For each installation, record the release, the PHP version, the database engine and the web server or engine:

- In the administration: **Setup > System information** names the version, PHP and the server that answered.
- On the command line: `./console exp:systeminfo --json`.
- In the database, the version row (its name keeps the historic spelling on purpose):

  ```bash
  mysql -u USER -p DATABASE -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
  ```

  Use the equivalent client for PostgreSQL or SQLite.

Which part of the guide applies follows from the version ([Which part do I read?](../../../guides/upgrading.md#which-part-do-i-read)):

| The site runs | Path |
|---|---|
| 3.10 up to 5.3 | the old update chain, then 5.4 to 6.0, then the 6.0 line ([Part A](../../../guides/upgrading.md#part-a-from-310-up-to-53-the-old-chain)) |
| 5.4 or 5.90 | 5.4 to 6.0.0, then the 6.0 line ([Part B](../../../guides/upgrading.md#part-b-from-54-or-590-to-exponential-600)) |
| an earlier 6.0.x | the 6.0 line ([Part C](../../../guides/upgrading.md#part-c-from-any-60x-to-today)) |
| LoveStack | `exp:lovestack check`, then Part C, then `exp:lovestack migrate` ([Coming from LoveStack](../../../guides/upgrading.md#coming-from-lovestack)) |

## Plan the upgrade per customer

| Step | What to decide |
|---|---|
| PHP | Exponential 6.0 needs PHP 8.0 or newer on the current line, for Velocity too; a site that must stay on PHP 7.4 stays on 6.0.7 ([PHP versions and extensions](../basics/php-versions-and-extensions.md)) |
| Customizations | own extensions and template overrides: test them on a copy first |
| Staging | restore the latest backup on a staging copy, upgrade it there, let the customer check it |
| Window | a maintenance window for the database step, announced to the customer |
| Rollback | the backup taken right before the window, and the time a restore takes |

> **Tip**
> Group customers by their current release and by how customized they are. Uncustomized sites on the same release
> can follow one tested runbook; customized ones get a staging copy each.

## The upgrade within the 6.0 line

From [11.6 From any 6.0.x to today](../../../install/11-upgrading.md#116-from-any-60x-to-today) and
[Part C](../../../guides/upgrading.md#part-c-from-any-60x-to-today):

1. **Maintenance on** and a fresh backup:

   ```bash
   php bin/php/maintenance.php on --message="Upgrade in progress" --until=30m
   ```

2. **Put the new code in place**: a checkout of the new tag, or `composer update` of `se7enxweb/exponential` in the
   project's `composer.json`. Keep `settings/override/`, `settings/siteaccess/`, your extensions and designs out of any
   command that replaces files. Then:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

3. **Apply the database update** of the line, once, whole, in order:
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. Files exist for MySQL (used for MariaDB too), PostgreSQL
   and SQLite; Oracle's files come with the `ezoracle` extension, and MongoDB needs no SQL file
   ([11.3 By engine](../../../install/11-upgrading.md#by-engine)).

   ```bash
   mysql -u USER -p DATABASE < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
   ```

4. **Run the data scripts** of the line once (once per var directory, on every web server of a cluster):

   ```bash
   php update/common/scripts/6.0/createaudittables.php
   php update/common/scripts/6.0/movetrashrecords.php --remove-file
   ```

5. **Unique logins.** List accounts that share a login, and add the unique index when there are none:

   ```bash
   ./console exp:user-login-check --allow-root-user
   ./console exp:user-login-check --fix-index --allow-root-user
   ```

6. **Read the checklist of every period you pass** (changelogs and behavior-change notes, listed in
   [C3](../../../guides/upgrading.md#c3-read-the-checklist-of-each-period-you-pass)). The changes people meet most:
   accounts without an `ezuser_setting` row cannot sign in; overrides follow `Priority`; security headers, cookie
   flags and a stricter upload check are on by default.
7. **Check own extensions**: `./console exp:checkclasses --allow-root-user` reports classes PHP refuses to load.
8. **Deploy**: reload the PHP-FPM that serves the site, or run `./console exp:velocity deploy` on Velocity
   ([Deploying changes](deploying-changes.md)). Clear the content cache and Velocity's response cache after the reload.
9. **Maintenance off** after the checks below.

## Check the result

```bash
php bin/php/ezcache.php --clear-all --allow-root-user     # ends without an error
./console exp:checkdbfiles --allow-root-user               # no file marked missing for your line
```

Then in the browser: the front page loads; sign in to the administration; **Setup > System information** shows the new
version; publish a test item and see it on the site. **Setup > Upgrade check** and **Check file consistency** list files
of the code that differ from the release; files changed on purpose are expected there
([The upgrade check](../../../guides/upgrade-check.md)).

## When something goes wrong

| Symptom | What to do |
|---|---|
| A white page after the code change | `var/log/error.log`; run step 2 again; `vendor/` must match the new `composer.json` |
| `Class ... not found` | `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches |
| Libraries missing after a failed Composer run | [Repairing an installation](../../../bc/6.0/repair.md) |
| A SQL file failed half way | restore the backup and apply the file again from the start; "never repair by hand and re-run" |
| Someone cannot sign in | the `ezuser_setting` item of the [behavior changes of July and August 2026](../../../bc/6.0/behaviour-changes-2026-07-08.md) |

Source: [If something goes wrong](../../../guides/upgrading.md#if-something-goes-wrong).

## Checklist

- [ ] Inventory: release, PHP, database engine and server recorded per site.
- [ ] Customized sites tested on a staging copy restored from a backup.
- [ ] Backup taken right before the window; restore time known.
- [ ] Code, autoloads, caches, database file, data scripts and login check done in order.
- [ ] The checklists of every period passed have been read.
- [ ] PHP-FPM reloaded or Velocity deployed; content and response caches cleared afterwards.
- [ ] `exp:checkdbfiles`, the upgrade check and a test publish pass; the customer is told.

## Related pages

- [PHP upgrade](../runbooks/php-upgrade.md) (runbook)
- [From older Exponential versions](../migrations/from-older-exponential-versions.md)
- [Automated updates with ezupdate](automated-updates-with-ezupdate.md)
- [Maintenance mode](maintenance-mode.md), [Deploying changes](deploying-changes.md)
- [Chapter 11: Upgrading](../../../install/11-upgrading.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
