---
title: Upgrade overview for the public sector
audience: government, admin, project-manager
category: public-sector
order: 20
summary: The upgrade of an Exponential site as a public-sector project - which upgrade path applies, the steps from backup to verification, what to put before the change board, and the people to involve - built on the upgrading guide and chapter 11 of the installation book.
keywords: upgrade, upgrading, version, 6.0.x, database update, dbupdate, upgrade path, project plan, change board, backup, verification, upgrade check, public sector
type: help
---
# Upgrade overview for the public sector

This page is for service owners, project managers and administrators planning an upgrade of a public website. The
technical procedure is in [Upgrading](../../../../guides/upgrading.md) and
[chapter 11 of the installation book](../../../../install/11-upgrading.md); this page frames it as a project with
approvals, a window and evidence.

## Contents
1. [Find your path](#find-your-path)
2. [The steps](#the-steps)
3. [Who to involve](#who-to-involve)
4. [What the change board wants to see](#what-the-change-board-wants-to-see)
5. [Common surprises](#common-surprises)
6. [Related pages](#related-pages)

## Find your path

**Setup > System information** names the version; so does the database row `ezpublish-version` in `ezsite_data`
(the name keeps its historic spelling).

| You run | Path (from the upgrading guide) |
|---|---|
| 3.10 up to 5.3 | Part A (the old chain of update files, one per step), then Part B |
| 5.4 or 5.90 | Part B: to 6.0.0, PHP 8 |
| 6.0.0 up to 6.0.14 | Part C: new code, `dbupdate-6.0.0-6.0.15.sql`, the checklists of each period |
| LoveStack | `./console exp:lovestack check`, then Part C, then `exp:lovestack migrate` |

Exponential continues the code base of eZ Publish, which its creator discontinued after almost 18 years; sites on
those old versions follow Part A. Migrations from other systems are in [chapters 14 to 17](../../../../install/README.md).

## The steps

1. **Back up** database and files, and test the restore on a scratch machine
   ([Upgrading, step 1](../../../../guides/upgrading.md#step-1-back-up-always)).
2. **Rehearse on staging** with a copy of production ([Staging and approval](staging-and-approval.md)).
3. **Before evidence**: **Setup > Upgrade check** (file and database consistency), system information report
   ([Upgrade check before and after](upgrade-check-before-and-after.md)).
4. **Open the window** ([Maintenance windows](maintenance-windows.md)).
5. **New code**, then:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

6. **Database update** for your engine, once, whole, in order, for example
   `update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql`; then the scripts the guide lists
   (`createaudittables.php`, `movetrashrecords.php`).
7. **Extensions**: `./console exp:checkclasses --allow-root-user` reports classes PHP refuses to load.
8. **Reload PHP-FPM or deploy Velocity**; clear the response caches after the reload.
9. **After evidence** and smoke tests: front page, sign-in, publish a test item, upgrade check again.
10. **Close the window** and record the version now running.

## Who to involve

| Who | Why |
|---|---|
| Service owner | accepts the risk and the window |
| Change manager | the change record and the board |
| Web team and key editors | test editing and publishing on staging |
| Security officer | security changes in the release notes, new defaults |
| Hosting provider or infrastructure team | PHP, database server, backups, maintenance window |
| Data protection officer | when staging gets a copy of production data |

## What the change board wants to see

The release notes you read ([Reading release notes](reading-release-notes.md)), the staging result, the plan with
times, the rollback plan ([Rollback planning](rollback-planning.md)), the communication to the public, and the
evidence you will collect. The template is on
[Change management and advisory boards](change-management-and-advisory-boards.md#a-change-record-template).

## Common surprises

The upgrading guide names the three changes people most often meet: accounts without an `ezuser_setting` row cannot
sign in (August 2026); overrides follow `Priority` (July 2026); security headers, `HttpOnly`/`SameSite` cookies and a
stricter upload check are on by default (September and October 2026), which matters if another host frames your
administration. Also: a unique index on `ezuser.login` is not added on upgrade; check with
`./console exp:user-login-check --allow-root-user`.

## Related pages
- [Upgrading customer sites (hosting)](../../../hosting/operations/upgrading-customer-sites.md)
- [PHP and database upgrades](php-and-database-upgrades.md)
- [Check upgrade consistency](../../tasks/operations/check-upgrade-consistency.md)
- [Site administrators course, upgrades module](../../../trainers/site-administrators/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
