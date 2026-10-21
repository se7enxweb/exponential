---
title: "Databases: MySQL, MariaDB, PostgreSQL and SQLite"
audience: dxp-developer
category: dxp-development
order: 10
summary: Which databases a DXP project runs on, how to create each one with the right character set, which to choose for development and production, and how the skeleton's guide converts a running project from one engine to another.
keywords: database, MySQL, MariaDB, PostgreSQL, SQLite, utf8mb4, collation, create database, conversion, pgloader, mysql2sqlite, Doctrine DBAL, Oracle
type: help
---
# Databases: MySQL, MariaDB, PostgreSQL and SQLite

This page is for developers who choose and prepare the database of a DXP project. The DXP stores content through
Doctrine DBAL; the skeleton's guide documents four engines.

## Contents

1. [Which engine](#which-engine)
2. [Creating the database](#creating-the-database)
3. [Installing the schema](#installing-the-schema)
4. [Converting between engines](#converting-between-engines)
5. [After a conversion](#after-a-conversion)

## Which engine

| Engine | Minimum | Use it for | Notes |
|---|---|---|---|
| MySQL | 8.0 | Production | `utf8mb4` with `utf8mb4_unicode_520_ci` |
| MariaDB | 10.3 (10.6 recommended) | Production | Same character set |
| PostgreSQL | 14 (16 recommended) | Production | `UTF8` encoding |
| SQLite | 3.35 | Development, tests, demos, offline installations | No server; not for concurrent public use ([SQLite for development](sqlite-for-development.md)) |

The guide's export path to Oracle is "export only"; Oracle is not an engine you install the DXP on.

## Creating the database

MySQL or MariaDB:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
GRANT ALL PRIVILEGES ON exponential.* TO 'your_db_user'@'localhost';
FLUSH PRIVILEGES;
```

PostgreSQL:

```bash
psql -U postgres -c "CREATE DATABASE exponential ENCODING 'UTF8';"
```

Doctrine can also create it from your settings: `php bin/console doctrine:database:create`. SQLite needs no step:
the install command creates the file.

Then set the database variables in `.env.local` ([Environment variables](environment-variables.md#database)).

## Installing the schema

```bash
php bin/console exponential:install exponential-oss
```

On later updates, apply database changes with Doctrine migrations, not with the install command
([Doctrine migrations](doctrine-migrations.md)).

## Converting between engines

The skeleton's guide (section 21) converts a running project with free tools only:

| From | To | Tool in the guide |
|---|---|---|
| MySQL or MariaDB | SQLite | `mysql2sqlite` (MIT) piped from `mysqldump` |
| PostgreSQL | SQLite | `pgloader` |
| SQLite | MySQL or MariaDB | `sqlite3-to-mysql` (Python, MIT) |
| SQLite | PostgreSQL | `pgloader` |
| MySQL or MariaDB | PostgreSQL | `pgloader` |
| PostgreSQL | MySQL or MariaDB | CSV export per table, schema with `pgloader`, import |
| Any | Oracle | export only |

Example from the guide, MySQL to SQLite:

```bash
mysqldump --no-tablespaces --skip-extended-insert --compact \
  -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" -h "$DATABASE_HOST" "$DATABASE_NAME" \
  | ./mysql2sqlite - | sqlite3 var/data_dev.db
```

Back up the database, `var/` and `.env.local` before you start, and do it in a maintenance window without traffic.

## After a conversion

The guide's checklist (section 21g):

1. Update `.env.local` with the new database settings.
2. `php bin/console cache:clear` (the container caches the connection).
3. `php bin/console doctrine:schema:validate`.
4. `php bin/console doctrine:migration:migrate --allow-no-migration`.
5. `php bin/console exponential:reindex`.
6. Smoke-test the site and `/adminui/`.
7. For SQLite as the target, fix the file permissions.

| Symptom | Cause | Fix |
|---|---|---|
| "Base table not found" | A table was not transferred | Validate the schema; read the converter's log |
| Garbled text | Character set mismatch at export | Export with `--default-character-set=utf8mb4` or `CLIENT_ENCODING=UTF8` |
| Image variations return 404 | Rows missing in the field table | Compare row counts between source and target |

## Related pages

- [SQLite for development](sqlite-for-development.md)
- [SQLite for Exponential Platform](../../features/6.0/platform-sqlite-install.md)
- [AdminNeo database manager](../../features/6.0/adminneo-database-manager.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
