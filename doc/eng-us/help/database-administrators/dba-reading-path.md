---
title: A reading path for database administrators
audience: dba
category: database-administration
order: 49
summary: The order in which a database administrator new to Exponential reads these pages - a first hour, a first day, then the pages for your engine and the pages for operations, upgrades and incidents - with the time each step takes.
keywords: reading path, learning path, onboarding, first day, order, where to start, dba, database administrator, curriculum
type: help
---
# A reading path for database administrators

This page is for database administrators who take over the database of an Exponential site and want to learn it in a
sensible order. Follow the steps in sequence; skip the engines you do not run.

## Contents

1. [The first hour](#the-first-hour)
2. [Your engine](#your-engine)
3. [The first day](#the-first-day)
4. [Before the first upgrade](#before-the-first-upgrade)
5. [Before the first incident](#before-the-first-incident)
6. [When the site grows](#when-the-site-grows)

## The first hour

1. [Database administrators: start here](database-administrators-start-here.md) (5 minutes).
2. [The database layer in one page](the-database-layer-in-one-page.md) (10 minutes).
3. [Supported engines and versions](supported-engines-and-versions.md) (10 minutes).
4. [The DatabaseSettings reference](databasesettings-reference.md) and
   [Reading and changing database settings](reading-and-changing-database-settings.md) (15 minutes).
5. Open **Setup > System information** on your site and find the Database card ([System information](../../guides/system-information.md)) (5 minutes).
6. [Securing the database](securing-the-database.md) (15 minutes).

## Your engine

| Engine | Read |
|---|---|
| SQLite 3 | [SQLite 3 for DBAs](sqlite-for-database-administrators.md), [Write queue](sqlite-write-queue-and-locking.md), [Maintenance](sqlite-maintenance.md), [Permissions](sqlite-files-ownership-and-permissions.md) |
| PostgreSQL | [Setting up PostgreSQL](postgresql-setup.md), [Operating PostgreSQL](postgresql-operations.md) |
| MongoDB | [Setting up MongoDB](mongodb-setup.md), [Limits and operations](mongodb-limits-and-operations.md) |
| MariaDB, MySQL | [Setting up](mysql-and-mariadb-setup.md), [utf8mb4 and InnoDB](mysql-and-mariadb-charset-and-innodb.md), [Read replicas](mysql-read-replicas.md) |
| Oracle | [Setting up Oracle](oracle-setup.md), [Connections and tuning](oracle-connections-and-tuning.md), [Maintenance commands](oracle-maintenance-commands.md) |

About an hour per engine.

## The first day

1. [Database accounts and privileges](database-accounts-and-privileges.md) and
   [Character sets and collations](character-sets-and-collations.md).
2. [Backups per engine](backups-per-engine.md) and [Restoring a database](restoring-a-database.md); then test a restore.
3. [Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md) and
   [Sessions in the database](sessions-in-the-database.md).
4. [Monitoring the database](monitoring-the-database.md); set up the alerts.
5. [The tables a database administrator should know](tables-you-should-know.md).

## Before the first upgrade

1. [Upgrades and the SQL update files](upgrades-and-sql-update-files.md).
2. [Applying the 6.0.15 database update](applying-the-6-0-15-database-update.md).
3. [Checking the schema after an upgrade](checking-the-schema-after-an-upgrade.md).
4. [The .dba schema format](the-dba-schema-format.md) and [Schema tools](schema-tools-dump-insert-diff.md).
5. The administrators' guide [Upgrading](../../guides/upgrading.md), to know what the rest of the team does.

## Before the first incident

1. [Troubleshooting database errors](troubleshooting-database-errors.md).
2. [Database error codes and messages](error-codes-and-messages.md).
3. [When something goes wrong](../when-something-goes-wrong.md).
4. [Checklists for database administrators](dba-checklists.md): print the restore list.

## When the site grows

1. [Performance and scaling](performance-and-scaling.md) and [Finding slow queries](finding-slow-queries.md).
2. [The SQL query cache](sql-query-cache.md) and [The built-in search index tables](search-index-tables.md).
3. [Choosing an engine](choosing-an-engine.md) and [Moving a site to another engine](moving-between-engines.md).
4. [Clustering: DFS and its metadata database](clustering-dfs-and-the-database.md).
5. [Reviewing extension SQL for every engine](portable-sql-for-extensions.md).

Keep [Database FAQ](database-faq.md) and the [Glossary](glossary-for-database-administrators.md) at hand.

## Related pages

- [Database administrators: start here](database-administrators-start-here.md)
- [Hosting companies: start here](../hosting/hosting-companies-start-here.md)
- [Database support](../database-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
