---
title: Checklists for database administrators
audience: dba
category: database-administration
order: 47
summary: Printable checklists for the database of an Exponential site - before an install, go-live, daily, weekly and monthly operations, before and after an upgrade, a restore, an engine move and a security review - each item linked to the page that explains it.
keywords: checklist, checklists, go-live, install, daily, weekly, monthly, upgrade, restore, migration, security review, operations, dba
type: help
---
# Checklists for database administrators

This page is for database administrators who want the steps of each routine on one page. Copy a list into your
ticket or runbook; each item links to the page that explains it.

## Contents

1. [Before an install](#before-an-install)
2. [Go-live](#go-live)
3. [Daily](#daily)
4. [Weekly](#weekly)
5. [Monthly and quarterly](#monthly-and-quarterly)
6. [Before and after an upgrade](#before-and-after-an-upgrade)
7. [Restore](#restore)
8. [Engine move](#engine-move)
9. [Security review](#security-review)

## Before an install

- [ ] Engine chosen ([Choosing an engine](choosing-an-engine.md)); version at or above the practical minimum ([Versions](supported-engines-and-versions.md)).
- [ ] PHP extension loaded in the web PHP, the command line PHP and Velocity's PHP ([PHP extensions](php-extensions-per-engine.md)).
- [ ] Database created in UTF-8 ([Character sets](character-sets-and-collations.md)).
- [ ] Account created with the least privileges that work ([Accounts](database-accounts-and-privileges.md)).
- [ ] PostgreSQL: `pgcrypto` available. MongoDB: library installed. Oracle: `oci8` and the client found.
- [ ] Network access only from the web servers ([Securing](securing-the-database.md#network-exposure)).

## Go-live

- [ ] **Setup > System information** shows the expected engine, version and UTF-8.
- [ ] No `root` or superuser in `settings/override/site.ini.append.php`; the file is mode 640 and not in version control.
- [ ] SQLite: ownership set up, the file not downloadable ([SQLite permissions](sqlite-files-ownership-and-permissions.md)).
- [ ] MongoDB: `create_indexes.js` run ([Setting up MongoDB](mongodb-setup.md)).
- [ ] Backups scheduled and one restore tested ([Backups](backups-per-engine.md), [Restoring](restoring-a-database.md)).
- [ ] Cronjobs scheduled; `session_gc.php` added with database sessions ([Housekeeping](housekeeping-cronjobs-and-queues.md)).
- [ ] Monitoring and alerts in place ([Monitoring](monitoring-the-database.md)).
- [ ] Debug output off for the public.

## Daily

- [ ] Backup ran; its size is plausible; SQLite backup passes `integrity_check`.
- [ ] `var/log/error.log` has no new database errors.
- [ ] `ezpending_actions` is not growing.
- [ ] MongoDB: no new `MONGO TODO`.

## Weekly

- [ ] SQLite: `PRAGMA optimize`, `quick_check`, `-wal` size ([SQLite maintenance](sqlite-maintenance.md)).
- [ ] Engine statistics current (PostgreSQL autovacuum, Oracle `gather-stats`).
- [ ] Disk space for database, logs and backups.
- [ ] **Setup > Cronjobs** shows runs without issues.

## Monthly and quarterly

- [ ] Table sizes reviewed ([Tables](tables-you-should-know.md#which-tables-grow)); old versions pruned.
- [ ] Engine and PHP extension still in vendor security support.
- [ ] Restore tested on another machine (quarterly).
- [ ] Accounts and network rules reviewed.

## Before and after an upgrade

- [ ] Version row read ([Upgrades](upgrades-and-sql-update-files.md#which-version-is-the-database-at)).
- [ ] Backup taken; maintenance on.
- [ ] Update files of your engine and path applied in order, each once; no `6.12/`, `7.2/`, `7.3/`, `unstable/`.
- [ ] Cluster update files where the cluster is used.
- [ ] 6.0.15: `createaudittables.php`, `movetrashrecords.php` ([6.0.15 update](applying-the-6-0-15-database-update.md)).
- [ ] `exp:checkdbfiles` prints nothing; upgrade check lists no missing table ([Checking the schema](checking-the-schema-after-an-upgrade.md)).
- [ ] Caches cleared after the PHP-FPM reload or Velocity restart; test publish done; maintenance off.

## Restore

- [ ] Maintenance on; Velocity stopped.
- [ ] Database and files from the same moment restored.
- [ ] All caches cleared, query cache and Velocity's response cache included.
- [ ] Sequences, search index, MongoDB indexes checked.
- [ ] Version row, row counts, sign-in and test publish checked; maintenance off.

## Engine move

- [ ] Rehearsal on a copy; window agreed ([Moving engines](moving-between-engines.md)).
- [ ] Extensions reviewed for the target engine ([Extension SQL](portable-sql-for-extensions.md)).
- [ ] Dump and insert completed; settings switched; caches cleared; workers restarted.
- [ ] Row counts match; consistency check clean; search index rebuilt.
- [ ] Backups and monitoring switched to the new engine.

## Security review

- [ ] One account per site, no administrative accounts ([Accounts](database-accounts-and-privileges.md)).
- [ ] Credentials only in `settings/override/` (and the cluster files); not in history, crontabs or scripts.
- [ ] Database port reachable from the web servers only; TLS where it crosses machines.
- [ ] Backups encrypted and access-controlled.
- [ ] Debug output limited to administrators' addresses.

## Related pages

- [Database administrators: start here](database-administrators-start-here.md)
- [Monthly operations](../hosting/checklists/monthly-operations.md), [Security review](../hosting/checklists/security-review.md) (for hosting companies)
- [Database FAQ](database-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
