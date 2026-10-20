---
title: MySQL for hosts
audience: hosting, admin
category: hosting
order: 120
summary: MySQL as the database of an Exponential site, from a host's point of view: where it stands in the official order, versions, the grants a site's user needs, InnoDB and utf8mb4, settings, read replicas, backups and the usual problems.
keywords: MySQL, mysqli, ezmysqli, MySQL 8.0, utf8mb4, InnoDB, grants, CREATE USER, mysqldump, single-transaction, UseSlaveServer, SlaveServerArray, replicas, persistent connections, QueryAnalysisOutput, hosting
type: help
---
# MySQL for hosts

This page is for system administrators at a hosting company who provide MySQL databases for Exponential sites. It
answers: where MySQL stands, how to create a site's database and user, what the driver needs from the server, how to
use replicas, and how to back it up. MySQL is sixth in the official order: supported and tested in the project's CI,
last only because the project recommends the others first for new sites. Sources:
[Database support](../../database-support.md#6-mysql) and
[chapter 9.3](../../../install/09-databases.md#93-mysql-and-mariadb).

## Contents

1. [Where MySQL stands](#where-mysql-stands)
2. [Versions and extension](#versions-and-extension)
3. [Creating a database per site](#creating-a-database-per-site)
4. [Settings and install](#settings-and-install)
5. [Replicas and persistent connections](#replicas-and-persistent-connections)
6. [Backups](#backups)
7. [Problems and their causes](#problems-and-their-causes)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## Where MySQL stands

- The driver `eZMySQLiDB` on `mysqli` is the one most third-party extensions were originally written against, so
  MySQL is the safe choice for a customer with older extensions that contain SQL of their own.
- It shares the driver with MariaDB ([MariaDB for hosts](mariadb-for-hosts.md)); for a host, the two are provisioned
  the same way.
- The project's CI runs its database tests against MySQL 8.0 on every push
  ([test coverage](../../database-support.md#test-coverage-ci)).

## Versions and extension

| Item | Value |
|---|---|
| Wizard floor | 4.1.1 (not a recommendation) |
| Practical minimum | a release your vendor still supports, with `utf8mb4` and InnoDB |
| Seen working | 8.0 in CI |
| PHP extension | `mysqli` (`php-mysqlnd` on the RHEL family, `php<version>-mysql` on Debian and Ubuntu) |
| Installer names | wizard "MySQL Improved"; `exp:install --db=mysql` (also `mysqli`); kickstarter `Type=mysqli` |

## Creating a database per site

```sql
CREATE DATABASE <customer> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<customer>'@'localhost' IDENTIFIED BY '<a strong password>';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER,
      LOCK TABLES, CREATE TEMPORARY TABLES
   ON <customer>.* TO '<customer>'@'localhost';
```

No global privilege is needed. The installer creates InnoDB tables when the server offers InnoDB, and transactions
need it. The MySQL update files of an upgrade start with `SET default_storage_engine=InnoDB;`
([chapter 9.3](../../../install/09-databases.md#character-set-and-storage-engine)).

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
# a Unix socket instead of host and port, e.g. /var/lib/mysql/mysql.sock
Socket=disabled
Charset=utf-8
UsePersistentConnection=disabled
ConnectRetries=0
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=mysql \
    --db-host=127.0.0.1 --db-name=<customer> --db-user=<customer> --allow-root-user
```

> **Note**
> The connection character set `utf-8` is sent as MySQL's `utf8`, three bytes per character, so characters outside the
> Basic Multilingual Plane, such as emoji, cannot pass through the connection even when the tables are `utf8mb4`.
> Customers who need them should know this before they choose MySQL or MariaDB
> ([character sets](../../database-support.md#character-sets-and-collations)).

## Replicas and persistent connections

- **Read replicas.** `UseSlaveServer=enabled` with `SlaveServerArray[]`, `SlaveServerPort[]`, `SlaverServerUser[]`,
  `SlaverServerPassword[]` and `SlaverServerDatabase[]` (the keys really are spelled `Slaver...`) sends reads to a
  replica chosen at random per request; writes go to `Server`. Replication lag means a page may show content a few
  seconds old.
- **Persistent connections.** `UsePersistentConnection=enabled` keeps one connection per PHP process (the driver
  prefixes the host with `p:`). Under Velocity's persistent workers each worker keeps its connection anyway. Size the
  server's connection limit for the number of PHP processes of every site that uses it.

## Backups

```bash
mysqldump --single-transaction -u <customer> -p <customer> > <customer>.sql
mysql -u <customer> -p <customer> < <customer>.sql
```

`--single-transaction` gives a consistent dump of InnoDB tables without locking them. A complete backup also contains
`var/storage`, `settings/override` and `settings/siteaccess`
([Backup and restore](../../database-support.md#backup-and-restore)).

## Problems and their causes

| Symptom | Cause |
|---|---|
| The "database cannot be reached" page | wrong host, user, password or database; since PHP 8.1 `mysqli` throws and the driver turns it into `eZDBNoConnectionException` |
| Text with `?` where accented letters were | the connection fell back to the server's default character set; set `AlwaysLog[]=warning` and look for "could not set the connection charset" in `var/log/warning.log` |
| The wizard refuses the database's character set | the database is not UTF-8; recreate it in `utf8mb4` |
| Slow queries | `[DatabaseSettings] QueryAnalysisOutput=enabled` with `SQLOutput=enabled` shows MySQL's analysis in the debug output |

## Checklist

- [ ] A supported MySQL release with InnoDB.
- [ ] One database per site in `utf8mb4`, one user with the grants above.
- [ ] `mysqli` in the web and the command line PHP.
- [ ] The server's connection limit sized for the PHP processes of every site on the server.
- [ ] `mysqldump --single-transaction` backups scheduled and a restore tested.

## Related pages

- [Databases in the official order](databases-in-the-official-order.md) and [MariaDB for hosts](mariadb-for-hosts.md)
- [Database support: MySQL](../../database-support.md#6-mysql)
- [Chapter 9.3: MySQL and MariaDB](../../../install/09-databases.md#93-mysql-and-mariadb)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
