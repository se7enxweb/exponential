---
title: MariaDB for hosts
audience: hosting, admin
category: hosting
order: 100
summary: MariaDB as the database of an Exponential site, from a host's point of view: the shared mysqli driver, utf8mb4 and InnoDB, the grants a site's user needs, settings, installing, the connection character set, backups and the usual problems.
keywords: MariaDB, mysqli, ezmysqli, MySQL Improved, utf8mb4, utf8mb3, InnoDB, grants, CREATE DATABASE, mariadb-dump, mysqldump, single-transaction, UseSlaveServer, replicas, connection charset, panel databases, hosting
type: help
---
# MariaDB for hosts

This page is for system administrators and support staff at a hosting company who provide MariaDB databases for
Exponential sites, which is the usual database a hosting panel creates. It answers: how MariaDB is driven, how to
create a site's database and user, what to watch with character sets, and how to back it up. MariaDB is fourth in
the official order and the MySQL-compatible server the project prefers. Sources:
[Database support](../../database-support.md#4-mariadb) and
[chapter 9.3](../../../install/09-databases.md#93-mysql-and-mariadb).

## Contents

1. [One driver for MariaDB and MySQL](#one-driver-for-mariadb-and-mysql)
2. [Versions](#versions)
3. [Creating a database per site](#creating-a-database-per-site)
4. [Settings and install](#settings-and-install)
5. [Character sets](#character-sets)
6. [Backups](#backups)
7. [Problems and their causes](#problems-and-their-causes)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## One driver for MariaDB and MySQL

MariaDB runs on the same driver as MySQL, `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`), on the PHP extension
`mysqli`. Everything on [MySQL for hosts](mysql-for-hosts.md) applies; this page covers what a host needs to do, and
what differs.

- The setup wizard lists it as "MySQL Improved".
- `exp:install --db=mariadb` is the same as `--db=mysql`.
- The kickstarter takes `[database_choice] Type=mysqli` (`mysql` accepted).
- MariaDB is not in the project's CI; the MySQL 8.0 job exercises the same driver, and MariaDB 10.5 runs on the
  project's development server ([test coverage](../../database-support.md#test-coverage-ci)).

## Versions

The wizard's floor is 4.1.1 (the MySQL number in `eZSetupDatabaseMap()`), which is not a recommendation. Run a
release your vendor still supports with security fixes, with `utf8mb4` and InnoDB
([versions](../../database-support.md#versions-checked-against-the-code)).

## Creating a database per site

The installers do not create databases or users. No global privilege is needed:

```sql
CREATE DATABASE <customer> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<customer>'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON <customer>.* TO '<customer>'@'localhost';
```

`LOCK TABLES` and `CREATE TEMPORARY TABLES` are listed because the driver's `lock()` uses `LOCK TABLES` and the kernel
creates temporary tables for some operations. No `SUPER` and no right to list databases is needed.

> **Note**
> When a control panel creates the database, check its default character set and the privileges it grants against
> the statements above; check your panel's documentation for how to set them. The wizard refuses a database whose
> character set is not UTF-8 ([Databases and users in control panels](../panels/databases-and-users.md)).

## Settings and install

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=<customer>
Password=<the password>
Database=<customer>
# a Unix socket instead of host and port
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --apply --db=mariadb \
    --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
```

The installer creates InnoDB tables when the server offers InnoDB; transactions need it. For an older installation,
`php bin/php/ezconvertmysqltabletype.php --list` lists the table types and `--newtype=InnoDB` converts them.

**Read replicas.** `UseSlaveServer=enabled` with `SlaveServerArray[]` and the related keys sends reads to a replica
chosen at random per request; writes go to `Server`. MySQL and MariaDB are the only engines with built-in read
splitting ([chapter 9.3, Replicas](../../../install/09-databases.md#replicas)).

## Character sets

- Create the database in `utf8mb4`. The installer creates its tables without a character set of their own, so they
  take the database's default.
- The connection: `Charset=utf-8` (the default) is sent to the server as `utf8`, which MariaDB treats as the
  three-byte `utf8mb3`. Characters outside the Basic Multilingual Plane, such as emoji, cannot pass through the
  connection even when the tables are `utf8mb4`. Keep the database in `utf8mb4` anyway.
- If the connection character set cannot be set, the driver keeps running and logs "could not set the connection
  charset", but only when warnings are logged (`site.ini [DebugSettings] AlwaysLog[]=warning`). Text then comes back
  with `?` in place of characters.

The **System information** page warns when the connection's character set is not UTF-8.

## Backups

```bash
mariadb-dump --single-transaction -u <customer> -p <customer> > <customer>.sql
mariadb -u <customer> -p <customer> < <customer>.sql
```

`--single-transaction` gives a consistent dump of InnoDB tables without locking them; the older name `mysqldump`
works too. Commands with a password on the command line show it to other users of the machine; prefer the prompt or
an option file. A complete backup also contains `var/storage`, `settings/override` and `settings/siteaccess`.

## Problems and their causes

| Symptom | Cause |
|---|---|
| The "database cannot be reached" page | wrong host, user, password or database |
| The wizard refuses the database's character set | the database was created in `latin1` or another single-byte set; recreate it in `utf8mb4` |
| Text with `?` where accented letters were | the connection fell back to the server's default character set |
| An extension's tables are not InnoDB | convert with `ezconvertmysqltabletype.php --newtype=InnoDB` |
| Slow pages | `[DatabaseSettings] SQLOutput=enabled` with `QueryAnalysisOutput=enabled` shows the analysis in the debug output |

## Checklist

- [ ] A supported MariaDB release with InnoDB.
- [ ] One database per site in `utf8mb4`, one user with the grants above.
- [ ] `mysqli` in the web and the command line PHP.
- [ ] Panel defaults checked for character set and grants.
- [ ] `mariadb-dump --single-transaction` backups scheduled and a restore tested.

## Related pages

- [Databases in the official order](databases-in-the-official-order.md) and [MySQL for hosts](mysql-for-hosts.md)
- [Database support: MariaDB](../../database-support.md#4-mariadb) and
  [character sets](../../database-support.md#character-sets-and-collations)
- [Chapter 9.3: MySQL and MariaDB](../../../install/09-databases.md#93-mysql-and-mariadb)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
