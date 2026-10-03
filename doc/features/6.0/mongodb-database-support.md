# MongoDB as the database

Exponential 6.0.14 (June 2026) can store the whole content repository in
MongoDB instead of MySQL, PostgreSQL or SQLite. If your organisation runs
MongoDB already, or you want a document store behind a mature content model, the
setup wizard now offers **MongoDB** next to the other database systems.

This page is the entry point. The complete porting reference, with every kernel
class, the collection schema and a test plan, is
[MongoDB kernel support](../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md); read
its section 24 for a step-by-step installation and section 26 for the known
limits.

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

## What works and what does not (June 2026)

Works: content editing and publishing, the content tree, URL aliases, roles
for access control, the admin interface, the front site, the setup wizard.

Limits you must plan for (full list in section 26 of the reference):

- Deep URL alias regeneration after moving subtrees is incomplete.
- Role and policy listing pages can be empty although permissions are enforced.
- Some cronjobs (static cache cleanup, notification, session garbage
  collection) are not ported.
- The `eztags` extension is not usable on MongoDB.
- The shop checkout and `ezflow` block scheduling are untested end to end.
- The adapter never runs real SQL: code that sends hand-written SQL through
  `arrayQuery()` returns nothing and logs a warning (search the logs for
  `MONGO TODO`).

## The same release made other engines better

The wizard work for MongoDB found places where code that was written for MySQL
leaked into other engines. Fixed in the same weeks (`e0d617f28e`, `93cdd0cf47`,
`7773850ade`):

- **SQLite and PostgreSQL**: during the MongoDB work several SQL paths had been
  gated to MySQL only, so those engines loaded no content. They now use the same
  SQL as MySQL, and only MongoDB takes its own path.
- **Shared-hosting MySQL**: a database user with ordinary per-database
  privileges (select, insert, update, delete, create, drop, index, alter) can
  finish the wizard; it no longer needs root, SUPER or the right to list
  databases.
- **PostgreSQL**: the wizard can be run again on a database that already has
  tables and sequences (`CREATE ... IF NOT EXISTS`, existing tables skipped),
  and the sequence correction no longer uses a column removed in PostgreSQL 12.
- **All engines**: `implode(null)` in the admin page layout and a missing
  `role_limitations` key in the user cache no longer crash or loop.

## Related

- [SQLite database support](sqlite-database.md),
  [specification of the SQLite driver](../../specifications/6.0/sqlite3-database-driver.md)
- [Package installer batching](package-installer-batching.md)
- [Chronicle: June 2026, first half](../../history/2026/2026-06a.md)
- [Changelog 6.0.14](../../changelogs/6.0/6.0.14.md)
