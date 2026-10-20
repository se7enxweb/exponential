---
title: Databases and users
audience: hosting, admin
category: hosting
order: 90
summary: Creating the database and its user for an Exponential site, engine by engine in the official order (SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL), with the privileges each driver needs, the character set, how the connection is written into settings, and what a panel's database page must allow.
keywords: database, database user, privileges, grants, SQLite, PostgreSQL, pgcrypto, owner, MongoDB, readWrite, dbAdmin, MariaDB, MySQL, utf8mb4, InnoDB, Oracle, ora-grant, AL32UTF8, DatabaseSettings, panel, shared hosting
type: help
---
# Databases and users

This page is for administrators and support staff at hosting companies who create databases and database users for
Exponential sites, by hand or through a panel. It answers, for each of the six supported engines in the official
order, what must exist before the install, which privileges the site's user needs, and how the connection reaches the
site's settings.

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time. ([Database support](../../database-support.md))

## Contents

1. [Before the install](#before-the-install)
2. [1. SQLite 3](#1-sqlite-3)
3. [2. PostgreSQL](#2-postgresql)
4. [3. MongoDB](#3-mongodb)
5. [4. MariaDB](#4-mariadb)
6. [5. Oracle](#5-oracle)
7. [6. MySQL](#6-mysql)
8. [Microsoft SQL Server](#microsoft-sql-server)
9. [Where the connection is stored](#where-the-connection-is-stored)
10. [What a panel's database page must allow](#what-a-panels-database-page-must-allow)

## Before the install

"For a database server, create the database and a user that may create tables in it before you install; the
installers do not create databases or users." ([2.6 Databases](../../../install/02-requirements.md#26-databases))

| Engine | Create a database? | Create a user? | PHP extension |
|---|---|---|---|
| 1. SQLite 3 | no (one file, created by the installer) | no | `sqlite3` |
| 2. PostgreSQL | yes, owned by the site's user | yes | `pgsql` |
| 3. MongoDB | no (created by the first write) | yes, in the site's database | `mongodb` + library `mongodb/mongodb` |
| 4. MariaDB | yes, `utf8mb4` | yes | `mysqli` |
| 5. Oracle | a schema user | yes, with the extension's grant script | `oci8` |
| 6. MySQL | yes, `utf8mb4` | yes | `mysqli` |

Since 6.0.15 the setup wizard "works with your own database only": "You now need privileges only on your own
database" ([A clean installation that fits shared hosting](../../../features/6.0/clean-install-defaults.md)).

## 1. SQLite 3

No server, no user, no grants. "The database is one file under `var/storage/sqlite3/`." Access control is the file
system: the file and its directory belong to the site's user, and every process that writes (pool, Velocity workers,
cron) runs as that user or shares its group
([9.2 Files, ownership and mixed users](../../../install/09-databases.md#files-ownership-and-mixed-users)). On a panel
nothing has to be created; the installer writes the file.

## 2. PostgreSQL

From [9.4](../../../install/09-databases.md#94-postgresql):

```bash
sudo -u postgres createuser --pwprompt exponential
sudo -u postgres createdb --owner=exponential --encoding=UTF8 --template=template0 exponential
```

- "Making the user the **owner** of the database matters: from PostgreSQL 15 on, ordinary users may no longer create
  tables in the `public` schema of a database they do not own."
- The driver needs `pgcrypto`. "The installer runs `CREATE EXTENSION IF NOT EXISTS pgcrypto` when the server ships
  it; as a trusted extension the database owner may create it." Otherwise, as owner or superuser:

```bash
sudo -u postgres psql -d exponential -c 'CREATE EXTENSION IF NOT EXISTS pgcrypto;'
```

Practical minimum: PostgreSQL 13 (`pgcrypto` trusted from 13 on).

> **Note**
> If your panel creates PostgreSQL databases owned by another role than the site's user, the site user cannot create
> tables on PostgreSQL 15 and later. Check your panel's documentation for how it assigns ownership.

## 3. MongoDB

From [9.5](../../../install/09-databases.md#95-mongodb): create the user **in the site's database**, because "MongoDB
uses the database in the URI's path as the authentication database when no `authSource` is given":

```bash
mongosh --eval '
  db.getSiblingDB("exponential").createUser({
    user: "exponential",
    pwd: passwordPrompt(),
    roles: [ { role: "readWrite", db: "exponential" }, { role: "dbAdmin", db: "exponential" } ]
  })'
```

"The user is required, also on a test server that does not enforce access control." Requirements: PHP 8.2 or later,
server 6.0 or later, PHP `mongodb` extension 1.18 or later. Read the
[MongoDB limits](../../database-support.md#mongodb-limits) before offering it.

## 4. MariaDB

The same driver as MySQL (`mysqli`); from [9.3](../../../install/09-databases.md#93-mysql-and-mariadb):

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

- "The user needs no global privilege, no `SUPER` and no right to list databases." `LOCK TABLES` and
  `CREATE TEMPORARY TABLES` are needed by the driver and the kernel.
- Create the database in `utf8mb4`; otherwise the wizard stops with "The database [...] cannot be used ... the
  database has been created using character set [...]" ([2.6](../../../install/02-requirements.md#26-databases)).
- The installer creates InnoDB tables when the server offers it; transactions need it.

## 5. Oracle

Oracle 19c or later through the `ezoracle` extension, database character set AL32UTF8
([9.6](../../../install/09-databases.md#96-oracle-19c-and-later-ezoracle)). The extension prints the SQL for a DBA:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php exponential - USERS
```

"`-` takes the password from `ORACLE_NEW_PASSWORD`, so it does not appear in the process list; `USERS` is the
tablespace." The same is available as `php bin/php/console ext:ezoracle:ora-grant`. For the health and report
commands, also grant `SELECT_CATALOG_ROLE`.

## 6. MySQL

Exactly as 4. MariaDB: the same driver, the same `CREATE DATABASE ... CHARACTER SET utf8mb4`, the same per-database
grants. MySQL is "last in the order, not because it works less well, but because the project recommends the others
first for new sites" ([Database support](../../database-support.md#the-supported-databases-in-order)). It is tested in
CI on MySQL 8.0.

## Microsoft SQL Server

There is no driver in 6.0.x. It "is known to be possible with additional work at this time"; what would have to be
written is listed in [Database support](../../database-support.md#microsoft-sql-server-possible-with-additional-work).
Do not offer it as a database option for Exponential sites.

## Where the connection is stored

The installers write the connection into `settings/override/site.ini.append.php`, block `[DatabaseSettings]`. For
example, for 2. PostgreSQL ([The settings](../../database-support.md#the-settings-databasesettings-in-siteini)):

```ini
[DatabaseSettings]
DatabaseImplementation=ezpostgresql
Server=localhost
Port=5432
User=exponential
Password=<the password>
Database=exponential
```

- The file holds the password: keep `settings/override` at mode `0640` or stricter and out of version control and
  backups that others can read ([13.3](../../../install/13-security-hardening.md#133-secrets-in-settingsoverride)).
- When a panel changes a database password, change it there as well, then clear the INI cache and restart Velocity's
  workers if Velocity serves the site:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user
```

## What a panel's database page must allow

| Requirement | Engines |
|---|---|
| Create a database with a chosen character set (`utf8mb4`) | 4. MariaDB, 6. MySQL |
| The site's user owns the database, or may create tables and the `pgcrypto` extension | 2. PostgreSQL |
| A user defined in the site's own database with `readWrite` and `dbAdmin` | 3. MongoDB |
| Per-database privileges including `LOCK TABLES` and `CREATE TEMPORARY TABLES` | 4. MariaDB, 6. MySQL |
| Nothing (only writable `var/`) | 1. SQLite 3 |

If your panel cannot meet a row, that engine is not a fit for self-service on that panel; offer 1. SQLite 3, or create
the database for the customer.

## Related pages

- [Control panels: overview](README.md)
- [Databases in the official order](../basics/databases-in-the-official-order.md)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Chapter 9: Databases](../../../install/09-databases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
