---
title: Setting up MySQL and MariaDB
audience: dba
category: database-administration
order: 14
summary: How to prepare MySQL or MariaDB for an Exponential site - a utf8mb4 database, an account with per-database privileges only, the settings block with host, port or socket, the console install, and what differs between MariaDB and MySQL for Exponential.
keywords: MySQL, MariaDB, CREATE DATABASE, utf8mb4, utf8mb4_unicode_ci, CREATE USER, GRANT, LOCK TABLES, CREATE TEMPORARY TABLES, InnoDB, ezmysqli, mysqli, Socket, exp:install --db=mysql, --db=mariadb, MySQL Improved
type: help
---
# Setting up MySQL and MariaDB

This page is for database administrators who prepare MySQL or MariaDB for an Exponential site. Both run on the same
driver, `eZMySQLiDB` (`lib/ezdb/classes/ezmysqlidb.php`), on the PHP extension `mysqli`. The page answers: how to
create the database and the account, which privileges are needed and why, what the settings look like and how to run
the installer. Source: [9.3 MySQL and MariaDB](../../install/09-databases.md#93-mysql-and-mariadb).

## Contents

1. [Requirements](#requirements)
2. [Create the database and the account](#create-the-database-and-the-account)
3. [Why these privileges](#why-these-privileges)
4. [The settings](#the-settings)
5. [Install](#install)
6. [MariaDB and MySQL: what differs](#mariadb-and-mysql-what-differs)
7. [First checks](#first-checks)
8. [Checklist](#checklist)

## Requirements

- A MySQL or MariaDB release your vendor still supports with security fixes, with InnoDB and `utf8mb4`. The wizard's
  floor of 4.1.1 is historical. The project's CI runs MySQL 8.0; the development server runs MariaDB 10.5.
- The PHP extension `mysqli` (mysqlnd) in every PHP that uses the site.

## Create the database and the account

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

Use the web servers' host or network in place of `localhost` when the database is on another machine. None of the
installers creates the database or the account.

## Why these privileges

- `SELECT`, `INSERT`, `UPDATE`, `DELETE`: the site's normal work.
- `CREATE`, `DROP`, `INDEX`, `ALTER`: the installer and every later update file create and change tables and indexes.
- `LOCK TABLES`: the driver's `lock()` uses `LOCK TABLES`.
- `CREATE TEMPORARY TABLES`: the kernel creates temporary tables for some operations (names such as
  `ezsearch_tmp_12`).

No global privilege, no `SUPER` and no right to list databases is needed: the wizard works with per-database
privileges. If your policy separates a migration account from the run-time account, the run-time account still needs
`CREATE TEMPORARY TABLES` and `LOCK TABLES`; test the split on staging, because the project does not document or test
such a split ([Database accounts and privileges](database-accounts-and-privileges.md)).

## The settings

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=exponential
Password=<the password>
Database=exponential
# a Unix socket instead of host and port, e.g. /var/lib/mysql/mysql.sock
Socket=disabled
Charset=utf-8
# enabled: mysqli persistent connections (the driver prefixes the host with p:)
UsePersistentConnection=disabled
ConnectRetries=0
```

`ezmysqli`, `mysqli`, `ezmysql` and `mysql` all name this driver.

## Install

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --apply --db=mysql \
    --db-host=127.0.0.1 --db-name=exponential --db-user=exponential --allow-root-user
```

`--db=mariadb` is the same. In the setup wizard choose "MySQL Improved"; it checks that the database's default
character set is UTF-8 and refuses a `latin1` database. The kickstarter takes `[database_choice] Type=mysqli`
(`mysql` accepted). The installer creates InnoDB tables when the server offers InnoDB
(`kernel/setup/steps/ezstep_create_sites.php`).

## MariaDB and MySQL: what differs

| | MariaDB | MySQL |
|---|---|---|
| Place in the official order | 4 | 6 |
| In the project's CI | no (covered indirectly by the MySQL job) | yes, MySQL 8.0 |
| Dump tool | `mariadb-dump` (or the older name `mysqldump`) | `mysqldump` |
| Connection name `utf8` | means `utf8mb3` | means `utf8mb3` |
| `SET storage_engine` in old update files | removed in 12.0 | removed in 5.7.5 |

For Exponential there is no other difference: the same driver, the same settings, the same update files in
`update/database/mysql/`.

## First checks

```bash
mysql -u exponential -p exponential -e "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
mysql -u exponential -p exponential -e "SELECT ENGINE, COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='exponential' GROUP BY ENGINE;"
```

All tables should be InnoDB. Then open **Setup > System information**: the Database card shows the engine, the
server version and the character set.

## Checklist

- [ ] A supported MySQL or MariaDB release with InnoDB.
- [ ] The database is `utf8mb4`.
- [ ] The account has the privileges above on this database only, and is not `root`.
- [ ] The account is restricted to the web servers' hosts.
- [ ] Backups with `--single-transaction` are scheduled ([Backups per engine](backups-per-engine.md)).
- [ ] `AlwaysLog[]=warning` was enabled while you set up the site, to catch charset warnings.

## Related pages

- [MySQL and MariaDB: utf8mb4, the connection charset and InnoDB](mysql-and-mariadb-charset-and-innodb.md)
- [MySQL and MariaDB read replicas](mysql-read-replicas.md)
- [MySQL for hosts](../hosting/basics/mysql-for-hosts.md), [MariaDB for hosts](../hosting/basics/mariadb-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
