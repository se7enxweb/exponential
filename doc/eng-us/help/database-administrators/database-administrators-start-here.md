---
title: "Database administrators: start here"
audience: dba
category: database-administration
order: 1
summary: Where database administrators start with Exponential. The six supported engines in one paragraph, a first fifteen minutes, the rules every DBA keeps, and a map of every page for database administrators, from engine setup, accounts and character sets to backups, upgrades, performance, monitoring and troubleshooting.
keywords: database administrator, DBA, database, start here, overview, index, SQLite, PostgreSQL, MongoDB, MariaDB, MySQL, Oracle, backups, upgrades, performance, monitoring, security
featured: true
type: help
---
# Database administrators: start here

This section of the help is for **database administrators**: the people who create, secure, back up, upgrade, tune and
monitor the database of an Exponential site, whether they run one site or many, on SQLite, PostgreSQL, MongoDB,
MariaDB, Oracle or MySQL. It describes the database layer exactly as the 6.0.x kernel implements it, with the
settings, scripts and files you will use, and says plainly where something needs an extension or does not exist.

Every fact on these pages was checked against the code, the settings files and the project's documentation, chiefly
[Database support](../database-support.md) and [chapter 9 of the installation guide](../../install/09-databases.md).
Each page names its sources.

## Contents

1. [Exponential's database in one paragraph](#exponentials-database-in-one-paragraph)
2. [Your first fifteen minutes](#your-first-fifteen-minutes)
3. [Six rules for every database](#six-rules-for-every-database)
4. [Map of the database administrator pages](#map-of-the-database-administrator-pages)
5. [Pages from other audiences you will use](#pages-from-other-audiences-you-will-use)
6. [Finding these pages again](#finding-these-pages-again)

## Exponential's database in one paragraph

**Exponential** is a free and open source PHP content management system; its content repository lives in one
database reached through the kernel class `eZDB` and one of five drivers. Six databases are supported, in this order
of preference: **1. SQLite 3** (the default, one file, for a site on one machine) · **2. PostgreSQL** (the recommended
server) · **3. MongoDB** (through an adapter, with documented limits) · **4. MariaDB** · **5. Oracle** (19c and later,
through the `ezoracle` extension) · **6. MySQL**. Microsoft SQL Server is known to be possible with additional work at
this time. The schema is kept in an engine-neutral `.dba` format and changed by per-engine update files. Uploaded
files, settings and caches are not in the database.

## Your first fifteen minutes

1. **Find the engine** (2 minutes): **Setup > System information**, Database card, or
   `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user`.
2. **Read the layer** (5 minutes): [The database layer in one page](the-database-layer-in-one-page.md).
3. **Read your engine's setup page** (5 minutes): from the map below.
4. **Check the backups** (3 minutes): [Backups per engine](backups-per-engine.md).

Then follow the [reading path](dba-reading-path.md).

## Six rules for every database

1. **Never edit shipped files.** Settings go into `settings/override/`; schema changes come from update files.
2. **One account per site, least privilege, never `root`.** ([Accounts](database-accounts-and-privileges.md))
3. **UTF-8 everywhere**: `utf8mb4`, `UTF8`, `AL32UTF8`. ([Character sets](character-sets-and-collations.md))
4. **Back up the database and the files together, and test the restore.** ([Backups](backups-per-engine.md))
5. **After any write outside Exponential, clear the caches**, the SQL query cache included.
   ([Query cache](sql-query-cache.md))
6. **After a settings change, clear the INI cache and restart Velocity's workers.**
   ([Changing settings](reading-and-changing-database-settings.md))

## Map of the database administrator pages

### Orientation

| Page | What it helps you do |
|---|---|
| [The database layer in one page](the-database-layer-in-one-page.md) | Understand eZDB, the drivers, the schema handlers and what is stored where |
| [Supported engines and versions](supported-engines-and-versions.md) | State supported versions and know what CI tests |
| [Choosing an engine](choosing-an-engine.md) | Pick the right database for a site |
| [PHP extensions per engine](php-extensions-per-engine.md) | Make sure every PHP has the driver extension |
| [The DatabaseSettings reference](databasesettings-reference.md) | Look up every connection setting |
| [Reading and changing database settings](reading-and-changing-database-settings.md) | Read the value in effect and change it safely |

### Engines

| Page | What it helps you do |
|---|---|
| [SQLite 3 for database administrators](sqlite-for-database-administrators.md) | Understand the driver, its files, PRAGMAs and WAL |
| [SQLite: the write queue and locking](sqlite-write-queue-and-locking.md) | Handle concurrent publishing and lock timeouts |
| [SQLite maintenance](sqlite-maintenance.md) | Run ANALYZE, VACUUM, WAL checkpoints and integrity checks |
| [SQLite files, ownership and permissions](sqlite-files-ownership-and-permissions.md) | Set up the files so every process can write them |
| [Setting up PostgreSQL](postgresql-setup.md), [Operating PostgreSQL](postgresql-operations.md) | Create the database, pgcrypto, sequences and case |
| [Setting up MySQL and MariaDB](mysql-and-mariadb-setup.md) | Create the database and the account |
| [MySQL and MariaDB: utf8mb4, the connection charset and InnoDB](mysql-and-mariadb-charset-and-innodb.md) | Get character sets and table types right |
| [MySQL and MariaDB read replicas](mysql-read-replicas.md) | Send reads to replicas |
| [Setting up MongoDB](mongodb-setup.md), [MongoDB: limits and operations](mongodb-limits-and-operations.md) | Run MongoDB and know what the adapter cannot do |
| [Setting up Oracle with ezoracle](oracle-setup.md) | Prepare an Oracle schema |
| [Oracle: persistent connections, DRCP and tuning](oracle-connections-and-tuning.md) | Pool and tune Oracle sessions |
| [Oracle maintenance commands and cronjobs](oracle-maintenance-commands.md) | Use the ext:ezoracle commands and cronjobs |
| [Microsoft SQL Server: status for DBAs](microsoft-sql-server-status.md) | Answer the SQL Server question correctly |

### Security and schema

| Page | What it helps you do |
|---|---|
| [Character sets and collations](character-sets-and-collations.md) | Create UTF-8 databases and handle case |
| [Database accounts and privileges](database-accounts-and-privileges.md) | Grant the least privileges that work |
| [Securing the database](securing-the-database.md) | Protect credentials, network access and backups |
| [The .dba schema format and the shipped schema files](the-dba-schema-format.md) | Know where the schema comes from |
| [Schema tools: dump, insert and diff](schema-tools-dump-insert-diff.md) | Use ezsqldumpschema, ezsqlinsertschema and ezsqldiff |
| [The tables a database administrator should know](tables-you-should-know.md) | Find your way in the kernel's tables |

### Backups, upgrades and moves

| Page | What it helps you do |
|---|---|
| [Backups per engine](backups-per-engine.md) | Back up each engine consistently |
| [Restoring a database](restoring-a-database.md) | Restore and test restores |
| [Upgrades and the SQL update files](upgrades-and-sql-update-files.md) | Apply the right update files in the right order |
| [Applying the 6.0.15 database update](applying-the-6-0-15-database-update.md) | Bring a 6.0.x database to 6.0.15 |
| [Checking the schema after an upgrade](checking-the-schema-after-an-upgrade.md) | Verify the schema with the upgrade check and ezsqldiff |
| [Moving a site to another engine](moving-between-engines.md) | Change the database engine of a running site |

### Operations and performance

| Page | What it helps you do |
|---|---|
| [Sessions in the database](sessions-in-the-database.md) | Decide where sessions live and clean them up |
| [The built-in search index tables](search-index-tables.md) | Look after the search index and its queue |
| [Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md) | Keep queues moving and tables tidy |
| [The SQL query cache](sql-query-cache.md) | Use the query cache safely |
| [Finding slow queries](finding-slow-queries.md) | Find the statements that make a page slow |
| [Performance and scaling](performance-and-scaling.md) | Apply the levers in the right order |
| [Clustering: DFS and its metadata database](clustering-dfs-and-the-database.md) | Run the database side of a multi-server cluster |
| [Monitoring the database](monitoring-the-database.md) | Set up checks and alerts |

### Troubleshooting and reference

| Page | What it helps you do |
|---|---|
| [Troubleshooting database errors](troubleshooting-database-errors.md) | Go from a symptom to its fix |
| [Database error codes and messages](error-codes-and-messages.md) | Read error numbers and messages |
| [Reviewing extension SQL for every engine](portable-sql-for-extensions.md) | Approve an extension for a non-MySQL site |
| [Database FAQ](database-faq.md) | Get quick answers |
| [Checklists for database administrators](dba-checklists.md) | Follow a routine step by step |
| [Glossary for database administrators](glossary-for-database-administrators.md) | Look up a term |
| [A reading path for database administrators](dba-reading-path.md) | Learn the pages in a sensible order |

## Pages from other audiences you will use

| Page | Why a database administrator reads it |
|---|---|
| [Database support](../database-support.md) | The project's support statement for every engine |
| [Chapter 9, Databases](../../install/09-databases.md) | The operator's reference behind these pages |
| [Admin tour](../admin-tour.md), [Logging into the admin](../logging-into-the-admin.md) | Find Setup > System information, Upgrade check, Sessions and Cronjobs |
| [Security hardening](../security-hardening.md) | The security of the whole site |
| [Operating a site](../../guides/operating-a-site.md), [Upgrading](../../guides/upgrading.md), [Upgrade check](../../guides/upgrade-check.md), [System information](../../guides/system-information.md), [Sessions](../../guides/sessions.md), [Cronjobs](../../guides/cronjobs.md), [Caches](../../guides/caches.md) | The administrators' guides your work touches |
| [Hosting companies: start here](../hosting/hosting-companies-start-here.md) | The hosting side: servers, panels, backups, runbooks |
| [Databases in the official order](../hosting/basics/databases-in-the-official-order.md), [Database backups per engine](../hosting/operations/database-backups-per-engine.md), [Changing the database engine](../hosting/migrations/changing-the-database-engine.md), [Clustering and shared storage](../hosting/operations/clustering-and-shared-storage.md) | The same topics from a host's point of view |
| [When something goes wrong](../when-something-goes-wrong.md) | Where to get help |
| [Free documentation](../free-documentation.md) | What you may do with these pages |

## Finding these pages again

- In the help, filter by the audience **Database administrators** or the category **Database administration**, or
  open **All pages** and the folder `database-administrators`.
- Each page has **Edit on GitHub**. If a fact here is wrong or out of date, tell us there or on the
  [software tracker](https://github.com/se7enxweb/exponential/issues).

## Related pages

- [The database layer in one page](the-database-layer-in-one-page.md)
- [A reading path for database administrators](dba-reading-path.md)
- [Database FAQ](database-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
