# MariaDB and MySQL database support

This page is for administrators and database administrators who run an Exponential site on MariaDB or MySQL. Both
run on the same driver, so they share one page: the support level, what the server and PHP need, the settings, an
install, the limits and the versions that were tested. The deeper chapters are linked where they say more.

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL (more databases from 6.0.15:
> [Choosing a database](../../help/choosing-a-database.md)). Details: [Database
> support](../../help/database-support.md#support-levels-at-a-glance).

MariaDB is the MySQL-compatible server the project prefers; MySQL is last in the order, not because it works less
well, but because the project recommends the others first for new sites. In the setup wizard both are one entry,
**MariaDB / MySQL Improved**.

## Contents

1. [Support level](#support-level)
2. [Requirements](#requirements)
3. [PHP extension](#php-extension)
4. [Settings](#settings)
5. [Installing a site](#installing-a-site)
6. [Limits](#limits)
7. [Tested versions](#tested-versions)
8. [Databases that speak MySQL's protocol](#databases-that-speak-mysqls-protocol)
9. [Related pages](#related-pages)

## Support level

| | Data tables | Full site | DFS | Multi-server cluster | Setup wizard | `exp:install` / kickstarter | xne upgrade tested |
|---|---|---|---|---|---|---|---|
| MariaDB | yes (`Engine=mysql`) | yes | yes, in the kernel (the default backend) | yes, with read replicas | yes, as MariaDB / MySQL Improved (`mysqli`) | `--db=mariadb` | yes: 6.0.14 to 6.0.15 and an exact rollback, also of a failed step |
| MySQL | yes (`Engine=mysql`) | yes | yes, in the kernel (the default backend) | yes, with read replicas | yes, as MariaDB / MySQL Improved (`mysqli`) | `--db=mysql` | the same driver and path, tested on MariaDB |

## Requirements

- **A release your vendor still supports**, with InnoDB (transactions need it) and the character set `utf8mb4`.
- **A database and a user** with rights on that database only; no global privilege is needed:

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'exponential'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON exponential.* TO 'exponential'@'localhost';
```

The step-by-step preparation is in [MySQL and MariaDB setup](../../help/database-administrators/mysql-and-mariadb-setup.md).

## PHP extension

`mysqli`, bundled with PHP through mysqlnd (`php-mysqlnd`, `php<version>-mysql`). The setup wizard offers MariaDB /
MySQL Improved, and the databases of the MySQL protocol, whenever the PHP running it has `mysqli`. A data table
connection or an import source over PDO uses `pdo_mysql`.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=ezmysqli
Server=localhost
# empty: 3306
Port=
User=exponential
Password=<the password>
Database=exponential
# a Unix socket instead of host and port
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
```

`ezmysqli`, `mysqli`, `ezmysql` and `mysql` all name the driver `eZMySQLiDB`. Put the block in
`settings/override/site.ini.append.php` (the installers write it for you), then clear the INI cache. Read replicas:
`UseSlaveServer=enabled` with `SlaveServerArray[]`; only plain SELECTs of anonymous GET and HEAD requests go to a
replica, and only to one that is current ([MySQL read replicas](../../help/database-administrators/mysql-read-replicas.md)).

## Installing a site

```bash
export EXP_INSTALL_DB_PASSWORD='<the password>'
php bin/php/console exp:install --db=mariadb --db-host=localhost --db-user=exponential --db-name=exponential
```

`--db=mariadb` and `--db=mysql` are the same. In the browser, choose **MariaDB / MySQL Improved** on the setup
wizard's database page; the kickstarter takes `Type=mysqli` in `[database_choice]`
([console install](../../install/07-console-install.md)).

## Limits

- The connection character set `utf-8` maps to the server's `utf8`, which is the three-byte `utf8mb3`: characters
  outside the Basic Multilingual Plane, such as emoji, do not pass through the connection even when the tables are
  `utf8mb4` ([character sets and InnoDB](../../help/database-administrators/mysql-and-mariadb-charset-and-innodb.md)).
- Tables of an old installation that are still MyISAM have no transactions:
  `php bin/php/ezconvertmysqltabletype.php --list` lists them, `--newtype=InnoDB` converts them.
- MySQL and MariaDB sort NULL first in ascending order, where PostgreSQL and Oracle sort it last.

## Tested versions

MySQL 8.0 in CI on every push; MariaDB through the same driver, and MariaDB 10.5 on the development server. xne's
integration suite upgrades a real 6.0.14 site on MariaDB to the current code and rolls it back exactly, also after a
database step that was made to fail.

## Databases that speak MySQL's protocol

TiDB, Vitess and PlanetScale, SingleStore, MariaDB Xpand and Amazon Aurora MySQL install a whole site on the same
extension with a profile each: [Databases that speak the MySQL protocol](mysql-wire-databases.md). Managed MariaDB and
MySQL services such as Amazon RDS are plain MariaDB or MySQL to the driver.

## Related pages

- [Database support](../../help/database-support.md) and [Choosing a database](../../help/choosing-a-database.md).
- [9.3 MySQL and MariaDB](../../install/09-databases.md#93-mysql-and-mariadb), the operator's chapter.
- [MySQL and MariaDB setup](../../help/database-administrators/mysql-and-mariadb-setup.md),
  [MySQL read replicas](../../help/database-administrators/mysql-read-replicas.md).
- [Clustering in Exponential 6.0](clustering-overview.md).
