---
title: Character sets and collations
audience: dba
category: database-administration
order: 23
summary: How Exponential stores text as UTF-8 on each of the six engines, which character set and collation to create, how the MySQL and MariaDB connection differs from the tables, how case-sensitive and case-insensitive comparison differ between engines, and how to check a running site.
keywords: character set, charset, collation, UTF-8, UTF8, utf8mb4, utf8mb3, AL32UTF8, BSON, template0, case sensitive, case insensitive, LIKE, lower, NLS_COMP, OracleCaseInsensitive, latin1, System information
type: help
---
# Character sets and collations

This page is for database administrators who create the database of an Exponential site or check an existing one
for encoding problems. Exponential stores and serves text as UTF-8; each engine names that differently and compares
strings differently. This page answers: what to create on each engine, where the connection can differ from the
tables, and how case affects lookups. Source:
[Database support, character sets and collations](../database-support.md#character-sets-and-collations).

## Contents

1. [What to create, per engine](#what-to-create-per-engine)
2. [The MySQL and MariaDB connection](#the-mysql-and-mariadb-connection)
3. [Case and comparison](#case-and-comparison)
4. [Checking a running site](#checking-a-running-site)
5. [Converting an old database](#converting-an-old-database)
6. [Checklist](#checklist)

## What to create, per engine

| Engine | Create the database with | Notes |
|---|---|---|
| SQLite 3 | nothing to choose | SQLite stores text as UTF-8 |
| PostgreSQL | `--encoding=UTF8 --template=template0` | `template0` lets you choose the encoding regardless of the server's default |
| MongoDB | nothing to choose | BSON strings are UTF-8 |
| MariaDB | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | The wizard refuses a database whose default character set is not UTF-8 |
| Oracle | database character set `AL32UTF8` | The driver connects in AL32UTF8; `NLS_LANG` is not needed |
| MySQL | `CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` | As MariaDB |

The Oracle character set is chosen when the database is created; it is a property of the whole database, so a
schema for Exponential belongs in an `AL32UTF8` database.

## The MySQL and MariaDB connection

`site.ini [DatabaseSettings] Charset=utf-8` (the default) is sent to the server as `utf8`, which both servers treat
as the three-byte `utf8mb3`. Characters outside the Basic Multilingual Plane, such as emoji, cannot pass through the
connection even when the tables are `utf8mb4`. Keep the database in `utf8mb4` anyway.

If the connection character set cannot be set, the driver keeps running and logs "could not set the connection
charset"; text then comes back with `?` in place of the missing characters. The warning is logged only when
warnings are (`site.ini [DebugSettings] AlwaysLog[]=warning`). Details:
[MySQL and MariaDB: utf8mb4, the connection charset and InnoDB](mysql-and-mariadb-charset-and-innodb.md).

## Case and comparison

| Engine | `=` | `LIKE` | How to change |
|---|---|---|---|
| SQLite 3 | with case | ignores case for ASCII letters only | |
| PostgreSQL | with case | with case (`ILIKE` ignores it) | compare `lower(...)` |
| MongoDB | with case | | a collation on the query or index |
| MariaDB, MySQL | default collations ignore case | ignore case | a binary or `_cs` collation |
| Oracle | with case (binary) | with case | `OracleCaseInsensitive=enabled` (`NLS_COMP=LINGUISTIC`, `NLS_SORT`), plus linguistic indexes from `ext:ezoracle:ci-indexes --create` |

The consequence: a login or identifier lookup written against MySQL's case-insensitive collations may find nothing
on PostgreSQL, Oracle or MongoDB. Exponential's own code handles this; extensions and your own report queries must
compare `lower(...)` on both sides where case must not matter
([Reviewing extension SQL for every engine](portable-sql-for-extensions.md)).

User logins are a special case: `exp:userlogincheck` lists logins that more than one user account has (for example
the same login in two cases), and `--fix-index` adds the unique index on `ezuser.login` once there are none
(`php bin/php/console list` describes it).

## Checking a running site

- **Setup > System information**, Database card: the character set of the connection; the page warns when it is not
  UTF-8 ([System information](../../guides/system-information.md)).
- MySQL and MariaDB:

  ```sql
  SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
    FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = 'exponential';
  SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES
   WHERE TABLE_SCHEMA = 'exponential' AND TABLE_COLLATION NOT LIKE 'utf8%';
  ```

- PostgreSQL: `psql -l` shows the encoding of each database.
- Oracle: `SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET';`

## Converting an old database

A database from an old release in a single-byte character set is converted with `bin/php/ezconvertdbcharset.php`
(MySQL, PostgreSQL and Oracle only). Pass the collation explicitly; its default is `utf8_general_ci`. Run it on a copy
after a backup ([MySQL and MariaDB: utf8mb4](mysql-and-mariadb-charset-and-innodb.md#converting-an-old-database-to-utf-8)).

## Checklist

- [ ] The database was created in the engine's UTF-8 character set.
- [ ] MySQL and MariaDB: every table is `utf8mb4`; no charset warning in `var/log/warning.log`.
- [ ] Oracle: `NLS_CHARACTERSET` is `AL32UTF8`.
- [ ] Extensions and reports compare case-insensitively where they must.

## Related pages

- [Setting up MySQL and MariaDB](mysql-and-mariadb-setup.md), [Setting up PostgreSQL](postgresql-setup.md), [Setting up Oracle](oracle-setup.md)
- [9.8 Cross-engine notes](../../install/09-databases.md#98-cross-engine-notes)
- [Languages](../languages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
