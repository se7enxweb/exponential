---
title: Budgeting for upgrades and maintenance
audience: businesses, project-manager, admin
category: business-solutions
order: 90
summary: How to plan a yearly budget for keeping an Exponential site current and safe - 6.0.x releases, PHP and server updates, extension updates, checks and restore tests - with what each kind of work involves, a calendar, and a worked budget built from example assumptions.
keywords: budget, upgrades, maintenance, updates, 6.0.x release, PHP upgrade, ezupdate, upgrade check, restore test, contingency, calendar, migration, release line
type: help
---
# Budgeting for upgrades and maintenance

This page is for managers who set the yearly budget of a website application and for the administrators or partners
who will do the work. It explains which maintenance work an Exponential site needs, what each kind involves, how
often it comes, and how to turn that into a budget with a reserve for surprises.

> **Note**
> Hours and rates in the worked budget are **Example assumptions** in a neutral currency unit. Ask whoever does the
> work for their own estimate, and measure the real hours in the first year.

## Contents

1. [The kinds of work](#the-kinds-of-work)
2. [What a release in the 6.0 line involves](#what-a-release-in-the-60-line-involves)
3. [PHP and server updates](#php-and-server-updates)
4. [Bigger moves: from older versions or other platforms](#bigger-moves-from-older-versions-or-other-platforms)
5. [A maintenance calendar](#a-maintenance-calendar)
6. [A worked yearly budget](#a-worked-yearly-budget)
7. [Keeping the budget small](#keeping-the-budget-small)

## The kinds of work

| Work | How often | Who | Source |
|---|---|---|---|
| Apply a new 6.0.x release | when one is published | administrator or partner | `SECURITY.md`; [Upgrading](../../../guides/upgrading.md) |
| Update extensions | when they publish releases | administrator | [Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md) |
| Update PHP | when your PHP version nears its end of support | host or administrator | [PHP upgrade runbook](../../hosting/runbooks/php-upgrade.md) |
| Update the operating system and database server | as the vendor publishes | host | [Security updates and disclosure](../../hosting/security/security-updates-and-disclosure.md) |
| Check files and database | before and after each update | administrator | [Upgrade check](../../../guides/upgrade-check.md) |
| Test a restore | at least a few times a year | administrator or host | [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md) |
| Review accounts and access | yearly | site owner | [Access reviews](../security/access-reviews.md) |

How often releases come: the 6.0 line went from 6.0.0 on 1 January 2024 to 6.0.14 on 7 June 2026, with 6.0.15 in
development ([Who is 7x?](../../who-is-7x.md#in-short)). Plan for several updates a year, not one large one.

## What a release in the 6.0 line involves

From [Upgrading, part C](../../../guides/upgrading.md#part-c-from-any-60x-to-today):

1. Back up first, always.
2. Put the new code in place (a checkout of the tag, or `composer update`).
3. Regenerate the class map and clear the caches.
4. Reload PHP-FPM, or redeploy Velocity.
5. Apply the database update file for the line, once, whole, in order.
6. Verify the upgrade.

Two facts keep this predictable. Security fixes come "with the next 6.0.x release", and "a published tag is never
changed" (`SECURITY.md`): a version you tested stays the version you run. The
[upgrade check](../../../guides/upgrade-check.md) compares files and database with the release before and after,
and changes nothing. `ezupdate` can check for updates and run a dry run from the administration
([Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md)).

Use maintenance mode for the minutes the site must be offline
([Maintenance mode](../../../features/6.0/maintenance-mode.md)), and see
[Upgrades without surprises](../reliability/upgrades-without-surprises.md) for the business view.

## PHP and server updates

- Exponential supports PHP 8.0 to 8.5; Velocity needs 8.1 or later (`README.md`, "What version of PHP is required").
- The test suite runs on PHP 8.1 to 8.5 and checks that 8.0 boots, on every push
  ([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#the-support-commitment)).
- The README recommends using "the latest version of PHP available on your OS".

Budget a PHP upgrade as its own small project with a test on a copy of the site: your own templates and extensions
must run on the new PHP as well.

## Bigger moves: from older versions or other platforms

A site still on 4.x or 5.x (from the eZ Publish years) or on eZ Platform or Ibexa is a migration, not an update.
Budget it separately, with a staging copy and a rollback plan. The installation book has the chapters:
[Migrating from 4.x](../../../install/14-migrating-from-4x.md),
[Migrating from 5.x legacy](../../../install/15-migrating-from-5x-legacy.md) and
[Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md).
[Switching to Exponential](../choosing/switching-to-exponential.md) explains it for decision makers.

## A maintenance calendar

| When | Task |
|---|---|
| Every month | Check for releases; read logs and the audit dashboard; follow the [monthly operations checklist](../templates/monthly-operations-checklist.md) |
| When a 6.0.x release appears | Read its changelog; test on staging; apply in a planned window |
| Every quarter | Test a restore; update extensions; check disk and backup retention |
| Every year | Plan PHP and OS upgrades; review accounts and roles; review this budget ([annual review](../templates/annual-review-checklist.md)) |

## A worked yearly budget

Every input is an **Example assumption**.

| Input | Value |
|---|---|
| Example assumption: 6.0.x releases applied per year | 6 |
| Example assumption: hours per release (staging test, update, checks) | 3 |
| Example assumption: one PHP upgrade with testing | 8 hours |
| Example assumption: monthly checks | 1 hour a month |
| Example assumption: restore tests | 4 a year at 2 hours each |
| Example assumption: hourly rate | 50 currency units |
| Example assumption: reserve for surprises | 20 % |

Step by step:

1. Releases: 6 x 3 = **18** hours.
2. PHP upgrade: **8** hours.
3. Monthly checks: 1 x 12 = **12** hours.
4. Restore tests: 4 x 2 = **8** hours.
5. Total hours: 18 + 8 + 12 + 8 = **46** hours.
6. Cost: 46 x 50 = **2,300**.
7. Reserve: 2,300 x 20 % = **460**.
8. Yearly maintenance budget: 2,300 + 460 = **2,760**.

If a partner does the work under a contract, replace steps 1 to 6 with the contract price, and keep the reserve for
work outside the contract.

## Keeping the budget small

- [ ] Keep custom changes in extensions and template overrides, not in kernel files; the upgrade check then finds
      no modified kernel files ([Upgrade check](../../../guides/upgrade-check.md)).
- [ ] Update often in small steps instead of skipping releases.
- [ ] Keep a staging copy that matches production.
- [ ] Follow releases on GitHub and the issue tracker named in `README.md`, "Issue tracker".
- [ ] Write down every update with its date and result; the audit trail records deploys and settings writes as
      well ([The audit trail](../../../features/6.0/audit-trail.md)).

## Related pages

- [Release line and support policy](../reliability/release-line-and-support-policy.md)
- [Upgrade check and file consistency](../reliability/upgrade-check-and-file-consistency.md)
- [Upgrading customer sites](../../hosting/operations/upgrading-customer-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
