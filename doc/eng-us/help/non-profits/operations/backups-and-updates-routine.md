---
title: Backups and updates routine
audience: non-profit, admin, hosting, businesses
category: business-solutions
order: 50
summary: The short routine that keeps a non-profit's Exponential site from expensive failures - backups of the database and storage with a tested restore (sqlite3 .backup for SQLite), applying Exponential, extension and PHP updates on a copy first, cron, logs, the upgrade check, and a calendar with a name next to each task.
keywords: backups, restore test, sqlite3 .backup, storage, updates, upgrades, PHP updates, security fixes, cron, logs, log viewer, upgrade check, maintenance mode, routine, calendar, non-profit
type: help
---
# Backups and updates routine

This page is for whoever looks after the site: a technical volunteer, a host or a partner, and the staff member who
checks that it is done. "Most expensive incidents on small sites come from missing backups, unapplied updates and
nobody watching" ([Low-budget operations](../../businesses/nonprofits/low-budget-operations.md#the-routine-that-prevents-expensive-problems)).
The routine below prevents all three.

## Contents

1. [Backups](#backups)
2. [The restore test](#the-restore-test)
3. [Updates](#updates)
4. [Cron and logs](#cron-and-logs)
5. [The calendar](#the-calendar)
6. [If you have a host or partner](#if-you-have-a-host-or-partner)

## Backups

A backup has two parts: the **database** and the **storage directory** (uploaded images and files). Both, or the
backup is useless.

| Part | How | Source |
|---|---|---|
| SQLite database | `sqlite3 <file> ".backup <target>"`, not a file copy while the site is busy | [SQLite database support](../../../features/6.0/sqlite-database.md#limits-and-good-practice) |
| Other databases | The database's own dump tool | [Backups and restores](../../creators/website-creators/backups-and-restores.md) |
| Storage | Copy of `var/<site>/storage` | same |
| Settings | `settings/override/` including the audit keys | [The audit trail](../../../features/6.0/audit-trail.md) |

Keep at least one copy away from the server, and protect it: backups hold the same personal data as the site
([Donor and volunteer data protection](donor-and-volunteer-data-protection.md)).

Owner's view: [Backups](../../customers/healthy/backups.md).

## The restore test

A backup that has never been restored is a hope, not a backup. Every quarter:

1. Restore the latest backup to a test location (a laptop or a test host).
2. Open the site; sign in; open a page with images and a file.
3. Write down the date, how long it took, and what went wrong.

Template: [Restore test](../../customers/healthy/restore-test.md).

## Updates

| What | Why | Read |
|---|---|---|
| Exponential releases | Fixes, including security fixes; a published version never changes, a fix comes as the next release | [How updates work](../../customers/healthy/how-updates-work.md), [Release line and support](../../customers/healthy/release-line-and-support.md) |
| Extensions | They are updated with Exponential through Composer | [Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md) |
| PHP and the server | The host's job, or yours on a virtual server | [PHP versions and extensions](../../hosting/basics/php-versions-and-extensions.md) |

Good practice:

- Test an update on a copy first; then apply it to the live site.
- Use maintenance mode for longer work ([Maintenance mode and planned work](../../customers/healthy/maintenance-mode-and-planned-work.md)).
- After an update, test the key paths: home, Donate, volunteer form, newsletter sign-up, shop checkout if used.
- The administration's upgrade check helps see what changed ([Upgrade check](../../../guides/upgrade-check.md)).
- Checklist: [Upgrade day checklist](../../customers/healthy/upgrade-day-checklist.md).

## Cron and logs

- Cron runs newsletters, scheduled publishing, approval publishing, view counts and the audit trail's rotation. Check
  monthly that it runs ([Cronjobs](../../../guides/cronjobs.md)).
- Glance at the logs monthly in the log viewer ([Log viewer](../../../features/6.0/log-viewer.md),
  [Logs for owners](../../customers/healthy/logs-for-owners.md)).

## The calendar

| How often | Task | Who (name) |
|---|---|---|
| Daily (automatic) | Backup runs | |
| Monthly | Backup exists and is recent; updates checked; cron ran; logs glanced at | |
| Quarterly | Restore test; access review | |
| On each release | Update tested on a copy, applied, key paths tested | |
| Yearly | Review host, partner and budget; `exp:audit verify` | |

Put this in the shared calendar. The [Monthly checklist](../checklists/monthly-checklist.md) and
[Annual checklist](../checklists/annual-checklist.md) carry the same tasks.

## If you have a host or partner

Ask them, in writing ([What to ask your host](../../customers/healthy/what-to-ask-your-host.md),
[What to ask your partner](../../customers/healthy/what-to-ask-your-partner.md)):

- [ ] How often are backups made, and where are they kept?
- [ ] When did you last test a restore of our site? Can we see the result?
- [ ] How soon after a release are updates applied?
- [ ] Who tells us when something was done?

A maintenance agreement makes it explicit ([Maintenance agreement template](../../customers/partners-and-hosts/maintenance-agreement-template.md)).

## Related pages

- [Operations for non-profits](README.md)
- [Maintaining your website](../../creators/website-creators/maintaining-your-website.md)
- [Backups that restore](../../businesses/reliability/backups-that-restore.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
