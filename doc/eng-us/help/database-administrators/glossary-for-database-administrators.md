---
title: Glossary for database administrators
audience: dba
category: database-administration
order: 48
summary: The Exponential terms a database administrator meets, explained in a sentence each with a link - from .dba, alias and cluster to siteaccess, update file, WAL and writer lock.
keywords: glossary, terms, definitions, dba format, DatabaseImplementation, driver, schema handler, siteaccess, override, update file, ezsite_data, DFS, WAL, writer lock, query cache, Velocity, ezoracle, adapter, maintenance mode
type: help
---
# Glossary for database administrators

This page is for database administrators who meet an Exponential term they do not know. Each entry is one or two
sentences with a link to the page that explains it.

## Contents

1. [A to D](#a-to-d)
2. [E to M](#e-to-m)
3. [N to S](#n-to-s)
4. [T to Z](#t-to-z)

## A to D

**Adapter (MongoDB)**: `expMongoDB`, the driver that translates the kernel's SQL-shaped calls into MongoDB operations;
it never runs SQL ([MongoDB: limits](mongodb-limits-and-operations.md)).

**Alias (driver alias)**: the value of `DatabaseImplementation`, such as `sqlite3` or `ezmysqli`, mapped to a driver
class by `ImplementationAlias[]` ([DatabaseSettings reference](databasesettings-reference.md#driver-aliases)).

**Audit tables**: `expaudit_cursor`, `expaudit_event`, `expaudit_file`, the index of the audit trail, created by the
6.0.15 update or `createaudittables.php` ([6.0.15 update](applying-the-6-0-15-database-update.md)).

**Cluster (DFS)**: several web servers sharing files on an NFS mount, with metadata in the table `ezdfsfile`
([Clustering](clustering-dfs-and-the-database.md)).

**Cronjob part**: a named list of scripts in `cronjob.ini`, run by `runcronjobs.php <part>`
([Housekeeping](housekeeping-cronjobs-and-queues.md)).

**.dba**: Exponential's engine-neutral schema and data format, a PHP array ([The .dba format](the-dba-schema-format.md)).

**DatabaseSettings**: the block of `site.ini` with the connection ([Reference](databasesettings-reference.md)).

**Delayed indexing**: publishing queues search indexing in `ezpending_actions` for a cronjob
([Search index tables](search-index-tables.md)).

**DRCP**: Oracle's Database Resident Connection Pooling, offered by `ezoracle` ([Oracle tuning](oracle-connections-and-tuning.md#drcp)).

**Driver**: the class that talks to one engine, implementing `eZDBInterface` ([The database layer](the-database-layer-in-one-page.md)).

## E to M

**eZDB**: the kernel's entry point to the database, `eZDB::instance()`; the name is a kernel identifier.

**ezoracle**: the extension that provides Oracle support, required by `composer.json` ([Setting up Oracle](oracle-setup.md)).

**ezsite_data**: the table holding the database's version rows `ezpublish-version` and `ezpublish-release`; the names
keep their historic spelling ([Upgrades](upgrades-and-sql-update-files.md)).

**Kickstarter**: an installer driven by `kickstart.ini`; its database type is `[database_choice] Type=`.

**Maintenance mode**: visitors get a 503 page while you work; `php bin/php/maintenance.php on|off|status`
([Maintenance mode](../../guides/maintenance-mode.md)).

**MONGO TODO**: the log marker of SQL the MongoDB adapter could not translate.

## N to S

**Null database (`eZNullDB`)**: what `eZDB::instance()` returns when the alias names no driver; every page then fails.

**Override**: a settings file in `settings/override/` (or a siteaccess) that changes a shipped value without editing
it ([Reading and changing settings](reading-and-changing-database-settings.md)).

**pgcrypto**: the PostgreSQL extension whose `digest()` the driver uses for MD5 ([Setting up PostgreSQL](postgresql-setup.md#pgcrypto)).

**Query cache**: the optional cache of `SELECT` results, `querycache.ini` ([The SQL query cache](sql-query-cache.md)).

**Schema handler**: the class that turns `.dba` into an engine's DDL and back, registered in `dbschema.ini`.

**Siteaccess**: one way into an installation (a site, the administration) with its own settings, possibly its own
database.

**Slave server**: Exponential's setting name for a MySQL or MariaDB read replica (`UseSlaveServer`)
([Read replicas](mysql-read-replicas.md)).

## T to Z

**Update file**: an SQL file in `update/database/<engine>/<version>/` that takes a database from one version to the
next ([Upgrades](upgrades-and-sql-update-files.md)).

**Upgrade check**: **Setup > Upgrade check**, which compares files and the database schema with the release
([Checking the schema](checking-the-schema-after-an-upgrade.md)).

**Velocity**: Exponential's own application server with persistent workers; each worker keeps its connection, so it
must be restarted after a database settings change.

**WAL**: SQLite's write-ahead log, the `-wal` file; readers and the writer work side by side
([SQLite for DBAs](sqlite-for-database-administrators.md#wal-readers-and-the-writer-side-by-side)).

**Writer lock**: the `.writer-lock` file with which the SQLite driver queues writers
([Write queue](sqlite-write-queue-and-locking.md)).

## Related pages

- [Glossary for hosts](../hosting/glossary-for-hosts.md)
- [Database FAQ](database-faq.md)
- [Database administrators: start here](database-administrators-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
