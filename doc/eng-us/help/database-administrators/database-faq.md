---
title: Database FAQ
audience: dba
category: database-administration
order: 46
summary: Short answers to the questions database administrators ask most about Exponential - which engine, is SQLite really enough, which privileges, where the password is, how to back up and upgrade, replicas, clustering, SQL Server, MongoDB limits, writing to the database by hand and more - each with a link to the full page.
keywords: FAQ, questions, database, SQLite production, which database, privileges, root, password, backup, upgrade, replicas, cluster, SQL Server, MongoDB, Oracle, utf8mb4, emoji, query cache, hand edits, version
type: help
---
# Database FAQ

This page is for database administrators who want a quick answer before they read a full page. Each answer links to
the page with the details.

## Contents

1. [Choosing and installing](#choosing-and-installing)
2. [Accounts and security](#accounts-and-security)
3. [Running the database](#running-the-database)
4. [Upgrades and moves](#upgrades-and-moves)
5. [Scaling](#scaling)
6. [Engine-specific questions](#engine-specific-questions)

## Choosing and installing

**Which databases are supported?** Six, in this order of preference: SQLite 3 (the default), PostgreSQL, MongoDB,
MariaDB, Oracle, MySQL. Microsoft SQL Server is known to be possible with additional work
([Supported engines and versions](supported-engines-and-versions.md)).

**Is SQLite really fit for production?** For a site served from one machine, yes: the driver adds WAL, connection
settings for a web server and a write queue, and caches answer most page views. Choose a server engine for several
web servers, heavy concurrent publishing, replication or database grants
([Choosing an engine](choosing-an-engine.md)).

**Which minimum versions?** SQLite library 3.33, PostgreSQL 13, MongoDB 6.0 with PHP 8.2, Oracle 19c; MySQL and
MariaDB a release your vendor supports, with InnoDB and `utf8mb4` ([Versions](supported-engines-and-versions.md#the-version-table)).

**Does the installer create the database and the user?** No. Create them first; the installers create the tables
([Setting up PostgreSQL](postgresql-setup.md), [MySQL and MariaDB](mysql-and-mariadb-setup.md)).

## Accounts and security

**Which privileges does the site need?** Full rights on its own database and nothing else; on MySQL the list in
[Database accounts and privileges](database-accounts-and-privileges.md), on PostgreSQL ownership of the database.

**Can the site run without DDL rights?** The project does not document or test that split; the same account applies
the update files. Test it on staging if your policy requires it.

**Where is the database password?** In `settings/override/site.ini.append.php`. Keep it out of version control,
mode 640 ([Securing the database](securing-the-database.md)).

**Does the administration show the password?** No. Secrets are masked everywhere, and the database user is always
hidden in the System information report.

## Running the database

**How do I back it up?** With the engine's tool: `.backup` for SQLite, `pg_dump`, `mysqldump --single-transaction`,
`mongodump`, Data Pump. Plus `var/storage` and `settings/override` ([Backups per engine](backups-per-engine.md)).

**Can I copy the SQLite file?** Not while it is in use. Use `.backup` or `VACUUM INTO`.

**May I change data with SQL by hand?** Avoid it; use the administration or the scripts. If you must, clear the caches
afterwards, the SQL query cache included ([The SQL query cache](sql-query-cache.md#writes-outside-exponential)).

**Which cronjobs touch the database?** The default part, `frequent` and `infrequent`; `session_gc.php` is not in any
shipped part ([Housekeeping](housekeeping-cronjobs-and-queues.md)).

**Are sessions in the database?** Only with `[Session] Handler=ezpSessionHandlerDB`
([Sessions in the database](sessions-in-the-database.md)).

## Upgrades and moves

**Which version is my database at?** `SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';`
([Upgrades](upgrades-and-sql-update-files.md#which-version-is-the-database-at)).

**Which update files do I apply?** Those of your engine and your path, in order, each once; never `6.12/`, `7.2/`,
`7.3/` or `unstable/` ([Upgrades and the SQL update files](upgrades-and-sql-update-files.md)).

**How do I check the schema afterwards?** **Setup > Upgrade check**, "Check database consistency", or `ezsqldiff.php`
([Checking the schema](checking-the-schema-after-an-upgrade.md)).

**Can I move from MySQL to PostgreSQL?** Yes, through a `.dba` dump and insert in a maintenance window
([Moving a site to another engine](moving-between-engines.md)).

## Scaling

**Does Exponential support read replicas?** On MySQL and MariaDB, with `UseSlaveServer`; on the other engines
replication is the server's own ([Read replicas](mysql-read-replicas.md)).

**Can several web servers share one database?** Yes, on every engine except SQLite; files are then shared with the DFS
cluster ([Clustering](clustering-dfs-and-the-database.md)).

**How do I find slow queries?** `SQLOutput` and `SlowQueriesOutput` in the debug output for your address, the SQL
profiler, and your engine's slow query log ([Finding slow queries](finding-slow-queries.md)).

**Should I turn on the query cache?** Try `request`, then `shared`, and measure; remember to clear it after writes
outside Exponential ([The SQL query cache](sql-query-cache.md)).

## Engine-specific questions

**Why are emoji lost on MySQL?** The connection uses `utf8`, which is three-byte `utf8mb3`
([utf8mb4](mysql-and-mariadb-charset-and-innodb.md#the-connection-charset)).

**Why does PostgreSQL need pgcrypto?** The driver computes MD5 with `digest()` ([Setting up PostgreSQL](postgresql-setup.md#pgcrypto)).

**What does MongoDB not support?** Some features, listed on [MongoDB: limits and operations](mongodb-limits-and-operations.md);
untranslated SQL returns empty results and logs `MONGO TODO`.

**Is Oracle supported out of the box?** Through the `ezoracle` extension, present in every installation and activated
for Oracle sites, from 6.0.15 on ([Setting up Oracle](oracle-setup.md)).

**Can I use Microsoft SQL Server?** Not in 6.0.x ([Microsoft SQL Server: status](microsoft-sql-server-status.md)).

## Related pages

- [Database administrators: start here](database-administrators-start-here.md)
- [Glossary for database administrators](glossary-for-database-administrators.md)
- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
