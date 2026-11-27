# MongoDB as the database

This page is for administrators whose organisation runs MongoDB already, or who want a document store behind a
mature content model. Exponential 6.0.14 (June 2026) can store the whole content repository in MongoDB instead of
MySQL, PostgreSQL or SQLite, and the setup wizard offers **MongoDB** next to the other database systems. Read the
limits below before you choose it for a production site.

This page is the entry point. The complete porting reference, with every kernel
class, the collection schema and a test plan, is
[MongoDB kernel support](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md); read
its section 24 for a step-by-step installation and section 26 for the known
limits.

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL (more databases from 6.0.15:
> [Choosing a database](../../help/choosing-a-database.md)). Details: [Database
> support](../../help/database-support.md).

## What was added

| Piece | File | Role |
|---|---|---|
| Database adapter | `lib/ezdb/classes/expmongodb.php` (`expMongoDB`, extends `eZDBInterface`) | Connects with the PHP `mongodb` extension and `MongoDB\Client`; turns the kernel's SQL-shaped calls into `find`, `aggregate`, `insert`, `upsert` and `deleteWhere` operations. |
| Schema handler | `lib/ezdbschema/classes/expmongoschema.php` (`expMongoSchema`) | Creates the collections of an installation. |
| Kernel compatibility layer | kernel classes and a few view scripts (the reference lists them in its section 5; the count was not re-verified) | Queries that used raw SQL (content tree, roles, URL aliases, user cache) were rewritten to portable calls. |
| Index script | `bin/mongodb/create_indexes.js` | Creates the indexes the kernel's queries rely on. |
| Conversion helpers | `bin/mongodb/export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`, `validate_ndjson.sh` | Move an existing MySQL site into MongoDB (see section 18 of the reference). |
| Wizard changes | `kernel/setup/` steps and `design/standard/templates/setup/init/database_init.tpl` | MongoDB as a choice; a **Database name** field, so you can authenticate against a specific database instead of only the `admin` database. |

## Settings

| File | Block | Key | Value | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `[DatabaseSettings]` | `DatabaseImplementation` | `mongodb` | siteaccess |
| `settings/site.ini` | `[DatabaseSettings]` | `ImplementationAlias[mongodb]` | `expMongoDB` | global |
| `settings/site.ini` | `[DatabaseSettings]` | `MongoSQLFallback`, `MongoSQLFallbackMaxRows`, `MongoSQLFallbackLifetime` | `enabled`, `200000`, `2` | global |
| `settings/dbschema.ini` | `[SchemaSettings]` | `SchemaHandlerClasses[mongo]` | `expMongoSchema` | global |
| `settings/setup.ini` | `[DatabaseSettings]` | `DefaultType` | `sqlite3`; choose `mongodb` in the wizard | global |

The wizard (`kernel/setup/ezsetupcommon.php`) lists the database system `mongodb` with required version 4.0 and
supports Unicode; the reference document (section 24.1) states MongoDB 6.0 as the practical minimum and 8.3 with PHP 8.5 as the tested combination.

## Install in outline

1. Prerequisites: PHP 8.2 or newer with the `mongodb` extension
   (`php -m | grep mongodb`), a MongoDB server, an application user with
   `readWrite` and `dbAdmin` on the database.
2. Give the setup wizard time: the configuration step can take one to three
   minutes. Raise PHP's `request_terminate_timeout` (PHP-FPM) before you begin.
3. Open the site; the setup wizard starts. Choose **MongoDB** on the database
   page and enter host, port, database name and credentials.
4. After the wizard, create the indexes (without them subtree queries and URL
   alias lookups scan whole collections):

   ```bash
   mongosh "mongodb://<user>:<password>@localhost:27017/<database>" --file bin/mongodb/create_indexes.js
   ```
5. Run the cronjobs once to build the search index (`php runcronjobs.php`).

## What works and what does not (6.0.15, November 2026)

Works: content editing and publishing, the content tree, URL aliases (also
after a move), roles and their lists, the admin interface, the front site,
`eztags`, search, the cronjob parts, the setup wizard, the kickstarter and
`exp:install --db=mongodb`.

Since 6.0.15 a SELECT the adapter cannot translate into MongoDB operations
(joins, `GROUP BY` with `HAVING`, `UNION`, sub-selects) is answered by the
**SQL fallback**: the collections it names are copied into an in-memory SQLite
database and the statement runs there, so hand-written SQL in the admin views,
reports, cronjobs and extensions gets the same rows as on the SQL engines
instead of an empty result. `site.ini [DatabaseSettings]` `MongoSQLFallback`
(`enabled`), `MongoSQLFallbackMaxRows` (`200000`) and
`MongoSQLFallbackLifetime` (`2` seconds) control it; details in
[MongoDB: limits and operations](../../help/database-administrators/mongodb-limits-and-operations.md).

Limits you must plan for:

- A SELECT over a collection with more documents than
  `MongoSQLFallbackMaxRows`, and a write that needs a join or a sub-select,
  are refused and logged (search `var/log/error.log` for `sqlFallback`).
- The SQL query cache does not apply, and there are no SQL update files.

The measured parity with the SQL engines (pages, admin views, write paths) is
in [Database support](../../help/database-support.md#engine-parity-measured).

## The same release made other engines better

The wizard work for MongoDB found places where code that was written for MySQL
leaked into other engines. Fixed in the same weeks (`e0d617f28e`, `93cdd0cf47`,
`7773850ade`):

- **SQLite and PostgreSQL**: during the MongoDB work several SQL paths had been
  gated to MySQL only, so those engines loaded no content. They now use the same
  SQL as MySQL, and only MongoDB takes its own path.
- **Shared-hosting MySQL**: a database user with ordinary per-database
  privileges (select, insert, update, delete, create, drop, index, alter,
  lock tables, create temporary tables) can
  finish the wizard; it no longer needs root, SUPER or the right to list
  databases.
- **PostgreSQL**: the wizard can be run again on a database that already has
  tables and sequences (`CREATE ... IF NOT EXISTS`, existing tables skipped),
  and the sequence correction no longer uses a column removed in PostgreSQL 12.
- **All engines**: `implode(null)` in the admin page layout and a missing
  `role_limitations` key in the user cache no longer crash or loop.

## Related pages

- [MongoDB kernel support (porting reference)](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md)
- [SQLite database support](sqlite-database.md), [specification of the SQLite driver](../../specifications/6.0/sqlite3-database-driver.md)
- [Database drivers and installers: SQLite, PostgreSQL, MySQL and Oracle, 16 to 30 September 2026](../../specifications/6.0/database-drivers-2026-09.md)
- [Package installer batching](package-installer-batching.md)
- [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md)
- [Chronicle: June 2026, first half](../../history/2026/2026-06a.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
