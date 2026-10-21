---
title: The database layer in one page
audience: dba
category: database-administration
order: 2
summary: How Exponential reaches its database, explained for database administrators - the eZDB entry point, the driver aliases, the five driver classes, the schema handlers, what is stored in the database and what is not, and where each piece lives in the code.
keywords: eZDB, eZDBInterface, driver, database layer, DatabaseImplementation, ImplementationAlias, eZNullDB, schema handler, dbschema.ini, lib/ezdb, lib/ezdbschema, architecture, what is in the database
type: help
---
# The database layer in one page

This page is for database administrators who meet Exponential for the first time and want to know how the
application talks to the database before they touch a server. It answers: which code opens the connection, how the
driver is chosen, what the drivers do differently, which tools turn the engine-neutral schema into DDL, and what is
kept in the database as opposed to files. Every class and file named here exists in the 6.0.x kernel.

## Contents

1. [One entry point: eZDB](#one-entry-point-ezdb)
2. [Drivers and their aliases](#drivers-and-their-aliases)
3. [Schema handlers](#schema-handlers)
4. [What is in the database, and what is not](#what-is-in-the-database-and-what-is-not)
5. [Caches between the site and the database](#caches-between-the-site-and-the-database)
6. [Where the pieces live](#where-the-pieces-live)
7. [What this means for your work](#what-this-means-for-your-work)

## One entry point: eZDB

Every part of Exponential reaches the database through `eZDB::instance()` (`lib/ezdb/classes/ezdb.php`). It reads
`site.ini [DatabaseSettings] DatabaseImplementation`, looks the value up in `ImplementationAlias[]`, creates the
driver object and keeps it for the rest of the request.

All drivers implement one interface, `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`): connect, `query()`,
`arrayQuery()` with offset and limit, transactions, `lastSerialID()`, `escapeString()` and helpers for what differs
between engines (`subString()`, `concatString()`, `md5()`, `bitAnd()`, `bitOr()`).

If the alias names no class, the request does not stop at once. `eZDB::instance()` returns a null database
(`eZNullDB`, `lib/ezdb/classes/eznulldb.php`) and writes "Database implementation not supported" to
`var/log/error.log`. Every page that needs content then fails, so a typing error looks like a database that is down
([9.1](../../install/09-databases.md#91-how-exponential-chooses-a-database-driver)).

## Drivers and their aliases

| Engine | Alias in `DatabaseImplementation` | Class | File |
|---|---|---|---|
| SQLite 3 | `sqlite3` | `eZSQLite3DB` | `lib/ezdb/classes/ezsqlite3db.php` |
| PostgreSQL | `ezpostgresql`, `postgresql`, `pgsql` | `eZPostgreSQLDB` | `lib/ezdb/classes/ezpostgresqldb.php` |
| MongoDB | `mongodb` | `expMongoDB` | `lib/ezdb/classes/expmongodb.php` |
| MariaDB, MySQL | `ezmysqli`, `mysqli`, `ezmysql`, `mysql` | `eZMySQLiDB` | `lib/ezdb/classes/ezmysqlidb.php` |
| Oracle | `ezoracle`, `oracle` | `eZOracleDB` | the `ezoracle` extension |

The kernel aliases are in `settings/site.ini`; the Oracle aliases are added by the extension's own
`site.ini.append.php` when it is active. Code can ask which engine it runs on: `eZDB::instance()->databaseName()`
returns `sqlite`, `postgresql`, `mongo`, `mysql` or `oracle` (and `null` for the null database).

Two drivers do more than pass SQL through:

- **SQLite** registers the functions SQLite lacks (`md5()`, the `BIT_OR` and `BIT_AND` aggregates), rewrites MySQL's
  `UPDATE ... JOIN`, ignores MySQL-only `SET` statements and queues writers
  ([SQLite 3 for database administrators](sqlite-for-database-administrators.md)).
- **MongoDB** never runs SQL: the adapter translates the kernel's SQL-shaped calls into MongoDB operations
  ([MongoDB: limits and operations](mongodb-limits-and-operations.md)).

## Schema handlers

The schema is kept in an engine-neutral format, `.dba` (`share/db_schema.dba`, `share/db_data.dba`). A schema
handler turns it into the DDL of one engine and reads a live database back into the same format. The handlers are
registered in `settings/dbschema.ini [SchemaSettings]`:

| Type | Class | File |
|---|---|---|
| `mysql`, `mysqli` | `eZMysqlSchema` | `lib/ezdbschema/classes/ezmysqlschema.php` |
| `postgresql` | `eZPgsqlSchema` | `lib/ezdbschema/classes/ezpgsqlschema.php` |
| `sqlite`, `sqlite3` | `eZSQLiteSchema` | `lib/ezdbschema/classes/ezsqliteschema.php` |
| `mongo` | `expMongoSchema` | `lib/ezdbschema/classes/expmongoschema.php` |
| `oracle` | `eZOracleSchema` | the `ezoracle` extension |

The installers, the schema tools (`ezsqldumpschema.php`, `ezsqlinsertschema.php`) and the upgrade check all go
through these handlers. See [The .dba schema format](the-dba-schema-format.md).

## What is in the database, and what is not

| In the database | Not in the database |
|---|---|
| Content objects, versions, fields, the node tree, URL aliases | Uploaded images and files: `var/storage` (and `var/<site>/storage`) |
| Content classes, sections, object states | Settings: INI files in `settings/` and the extensions |
| Users, roles, policies, user visits | Caches: files under `var/cache` and `var/<site>/cache` |
| The built-in search index | Logs: `var/log/` |
| Sessions, when `ezpSessionHandlerDB` is used | Sessions with the default PHP handler (PHP's own storage) |
| Queues (`ezpending_actions`), workflows, the shop | Templates and designs |
| The installed version (`ezsite_data`) | The maintenance switch (`var/maintenance.json`) |

A database backup alone is therefore not a backup of the site; see [Backups per engine](backups-per-engine.md).

## Caches between the site and the database

Most page views never reach the database: Velocity's response cache, the content view cache and the template-block
cache answer first, and the optional SQL query cache (`settings/querycache.ini`, `Mode=off` by default) answers
repeated `SELECT` statements on every engine except MongoDB. A write made outside Exponential, with a SQL client or a
restore, is not seen by these caches; clear them afterwards ([The SQL query cache](sql-query-cache.md),
[Performance and scaling](performance-and-scaling.md)).

## Where the pieces live

| What | Where |
|---|---|
| Connection settings | `settings/site.ini [DatabaseSettings]`, overridden in `settings/override/site.ini.append.php` |
| Drivers | `lib/ezdb/classes/` |
| Schema handlers | `lib/ezdbschema/classes/`, registered in `settings/dbschema.ini` |
| Shipped schema and data | `share/db_schema.dba`, `share/db_data.dba`, `kernel/sql/<engine>/` |
| Update files | `update/database/<engine>/<version>/` |
| Schema tools | `bin/php/ezsqldumpschema.php`, `ezsqlinsertschema.php`, `ezsqldiff.php` |
| MongoDB helpers | `bin/mongodb/` |

## What this means for your work

- The database account is the only credential the application needs; it lives in one override file
  ([Securing the database](securing-the-database.md)).
- Schema changes come from update files and the schema tools, never from hand edits
  ([Upgrades and the SQL update files](upgrades-and-sql-update-files.md)).
- After any write outside the application, clear the caches.

## Related pages

- [Database administrators: start here](database-administrators-start-here.md)
- [The DatabaseSettings reference](databasesettings-reference.md)
- [Supported engines and versions](supported-engines-and-versions.md)
- [Database support](../database-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
