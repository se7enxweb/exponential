---
title: "Housekeeping: cronjobs and queue tables"
audience: dba
category: database-administration
order: 37
summary: The cronjobs that keep an Exponential database tidy and its queues moving - the parts of settings/cronjob.ini and what each script does to the database, the scripts no shipped part runs (session_gc.php, trashpurge.php, old_drafts_cleanup.php and others), the queue tables to watch, and how to schedule and verify them.
keywords: cronjobs, runcronjobs.php, cronjob.ini, CronjobPart, frequent, infrequent, cache_cleanup, cluster_maintenance, indexcontent, internal_drafts_cleanup, basket_cleanup, session_gc, trashpurge, old_drafts_cleanup, ezpending_actions, ezworkflow_process, cleanupversions, queue, housekeeping
type: help
---
# Housekeeping: cronjobs and queue tables

This page is for database administrators who want to know which scheduled jobs write to the Exponential database,
which queues pile up when the jobs stop, and how to check them. The cronjob system itself is described in
[Cronjobs](../../guides/cronjobs.md); this page looks at it from the database's side. Source: `settings/cronjob.ini`
and the scripts in `cronjobs/`.

## Contents

1. [How cronjobs run](#how-cronjobs-run)
2. [The default part](#the-default-part)
3. [The other shipped parts](#the-other-shipped-parts)
4. [Scripts that no shipped part runs](#scripts-that-no-shipped-part-runs)
5. [Queue tables to watch](#queue-tables-to-watch)
6. [Scripts outside the cron system](#scripts-outside-the-cron-system)
7. [A crontab](#a-crontab)
8. [Checklist](#checklist)

## How cronjobs run

`php runcronjobs.php [-s <siteaccess>] [<part>]` runs the scripts of one part; without a part name it runs the
default list in `[CronjobSettings] Scripts[]`. Only one instance of a script runs at a time
(`MaxScriptExecutionTime`, 12 hours by default). Parts can be paused at **Setup > Cronjobs**
([Cronjob console](../hosting/operations/cronjob-console.md)).

## The default part

`[CronjobSettings] Scripts[]` in the shipped file:

| Script | Effect on the database |
|---|---|
| `unpublish.php` | unpublishes objects whose unpublish date passed |
| `rssimport.php` | imports configured RSS feeds as content |
| `indexcontent.php` | indexes content queued in `ezpending_actions` (delayed indexing) |
| `hide.php` | hides objects whose hide date passed |
| `internal_drafts_cleanup.php` | removes old internal drafts |

## The other shipped parts

| Part | Scripts | Notes |
|---|---|---|
| `frequent` | `notification.php`, `workflow.php`, `contentjobs.php`, `audit.php`, `mailbounces.php` | workflows waiting in `ezworkflow_process`, notifications, content jobs, the audit index |
| `infrequent` | `basket_cleanup.php`, `linkcheck.php`, `mailpreferences.php` | removes old shop baskets; checks `ezurl` links; e-mail preference retention |
| `cache_cleanup` | `cachecleanup.php`, `httpcache_cleanup.php`, `querycache_cleanup.php` | files only; `querycache_cleanup.php` removes old result files of the SQL query cache |
| `cluster_maintenance` | `clusterpurge.php` | purges expired DFS cluster entries; in `ForbiddenParts[]`, so it cannot be launched from the browser |
| `unlock` | `unlock.php` | releases locks; also forbidden from the browser |
| `contentcheck`, `contentjobs`, `audit`, `logs`, `notification`, `mailpreferences`, `mailbounces`, `cleanuprss` | one job each | run a single job on its own schedule |

## Scripts that no shipped part runs

These scripts exist in `cronjobs/` but are not in any part of the shipped `cronjob.ini` (some are listed there as
comments). Add the ones you need to a part in `settings/override/cronjob.ini.append.php`:

| Script | Purpose |
|---|---|
| `session_gc.php` | removes expired rows from `ezsession` (needed with `ezpSessionHandlerDB`) |
| `trashpurge.php` | permanently deletes objects in the trash (all, or as old as `[TrashSettings] KeepItemsForDays` says) |
| `old_drafts_cleanup.php` | removes user drafts older than the configured age limit |
| `subtreeexpirycleanup.php` | cleans subtree expiry entries (commented out in the default list since 4.6) |
| `staticcache_cleanup.php` | for the static cache, which stays disabled |
| `updateviewcount.php` | flushes the buffered view counts to the database (`ezview_counter`) |

Read each script's header and its documentation link at **Setup > Cronjobs** before enabling it.

```ini
# settings/override/cronjob.ini.append.php
[CronjobPart-infrequent]
Scripts[]=session_gc.php
```

## Queue tables to watch

| Table | Grows when | Check |
|---|---|---|
| `ezpending_actions` | `indexcontent.php` does not run | `SELECT action, COUNT(*), MIN(created) FROM ezpending_actions GROUP BY action;` |
| `ezworkflow_process` | `workflow.php` does not run | rows of waiting processes |
| `ezsession` | expired rows are not removed | rows with `expiration_time` in the past |
| `ezbasket`, `ezproductcollection*` | `basket_cleanup.php` does not run | old baskets |
| `ezcontentobject_version` | old versions are never pruned | `bin/php/cleanupversions.php` |

`MIN(created)` is a Unix timestamp; a value hours old on a site that publishes means the queue is stuck.

## Scripts outside the cron system

| Script | When |
|---|---|
| `bin/php/cleanupversions.php` | remove archived versions beyond the `VersionManagement` limits |
| `bin/php/ezsessiongc.php` | remove expired sessions by hand |
| `bin/php/updatesearchindex.php --clean` | rebuild the search index |

## A crontab

```text
*/5 * * * *  cd /path/to/installation && umask 002 && php runcronjobs.php -q frequent
15 * * * *   cd /path/to/installation && umask 002 && php runcronjobs.php -q
30 3 * * *   cd /path/to/installation && umask 002 && php runcronjobs.php -q infrequent
45 3 * * *   cd /path/to/installation && umask 002 && php runcronjobs.php -q -s <siteaccess> cache_cleanup
```

Adapt the times; **Setup > Cronjobs** proposes lines for your installation
([Cronjobs, scheduling](../../guides/cronjobs.md#12-scheduling-with-the-crontab)). Run cron as the site's user, with
the same umask as the site when the database is SQLite.

## Checklist

- [ ] The default part, `frequent` and `infrequent` are scheduled.
- [ ] With database sessions, `session_gc.php` runs from a part.
- [ ] `ezpending_actions` and `ezworkflow_process` do not grow over days.
- [ ] Old versions are pruned on schedule.
- [ ] **Setup > Cronjobs** shows recent runs without issues.

## Related pages

- [Cronjobs](../../guides/cronjobs.md)
- [Cron jobs](../hosting/operations/cron-jobs.md) (for hosting companies)
- [Sessions in the database](sessions-in-the-database.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
