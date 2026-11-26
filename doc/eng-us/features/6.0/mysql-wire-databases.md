# Databases that speak the MySQL protocol

This page is for administrators who want to run Exponential on a database that speaks the MySQL protocol but is not
MySQL or MariaDB, and for developers who work on its drivers: **TiDB**, **Vitess** and **PlanetScale**,
**SingleStore**, **MariaDB Xpand** and **Amazon Aurora MySQL**. From Exponential 6.0.15 the kernel has a driver for
each of them. Every one is the MySQL driver `eZMySQLiDB` with a profile of what its database does differently.

**Status.** TiDB, Vitess and SingleStore install and run a whole site (measured below). Xpand and Aurora MySQL have
their drivers and profiles, written from their vendors' documentation; they could not run here (Xpand has no public
container image, Aurora runs only at AWS), so they are not measured. The tables per database say what works and what
does not.

## Contents

1. [The drivers](#the-drivers)
2. [What works and what does not](#what-works-and-what-does-not)
3. [Settings](#settings)
4. [What the drivers do differently](#what-the-drivers-do-differently)
5. [Installing a site](#installing-a-site)
6. [Data table connections](#data-table-connections)
7. [Clustering](#clustering)
8. [Tests](#tests)

## The drivers

| Database | `DatabaseImplementation` | Driver class | Level reached | Measured on |
|---|---|---|---|---|
| TiDB | `tidb` | `expTiDBDB` | full site database | TiDB 8.5.1 (`pingcap/tidb:v8.5.1`) |
| Vitess, PlanetScale | `vitess`, `planetscale` | `expVitessDB` | full site database (an unsharded keyspace) | Vitess 25 `vttestserver` (MySQL 8.4 underneath) |
| SingleStore | `singlestore`, `memsql` | `expSingleStoreDB` | full site database | SingleStore 10.10 (`singlestoredb-dev`, no license key) |
| MariaDB Xpand | `xpand` | `expXpandDB` | profile, not measured | none: no public container image |
| Amazon Aurora MySQL | `aurora`, `auroramysql` | `expAuroraMySQLDB` | profile, not measured | none: a managed service only |

"Full site database" means what it means for the [SQL Server driver](sqlserver-database-driver.md): `exp:install`
installs the site package with its content, the site runs on it, and data table connections and imports reach it.

All drivers need PHP's `mysqli`. `databaseName()` is `mysql` for every one: the kernel's MySQL statements, the MySQL
schema handler (`eZMysqlSchema`), the MySQL DFS cluster backend and the portable SQL helpers (`sqlHelperDialect()`
`mysql`) work as they are, and the driver sends each statement the way its database takes it. The profiles are in
`expMySQLWireProfile` (`lib/ezdb/classes/expmysqlwireprofile.php`), a class of pure functions.

## What works and what does not

### TiDB

TiDB 6.5 and later, self-hosted or TiDB Cloud (with `SSL=verify`). Measured on TiDB 8.5.1.

| Works | Does not, or differently |
|---|---|
| a whole site: `exp:install --db=tidb`, the setup wizard by kickstarter (`Type=tidb`) | `BEGIN WORK` is refused: the driver sends `BEGIN` |
| foreign keys, enforced (TiDB 8.5) | `LOCK IN SHARE MODE` and `FOR SHARE` are refused: the driver sends `FOR UPDATE`, which locks more, never less |
| transactions, savepoints | isolation levels `READ UNCOMMITTED` and `SERIALIZABLE` are refused; `READ COMMITTED` and `REPEATABLE READ` (snapshot) work |
| `AUTO_INCREMENT` in MySQL's order: new tables get `AUTO_ID_CACHE=1` | without it, ids are unique but cached per TiDB server: not in insert order across servers |
| `GET_LOCK()`; table locks become named locks | `LOCK TABLES` does nothing unless the server runs with `enable-table-lock` |
| case-insensitive text: new tables get `COLLATE=utf8mb4_general_ci` | TiDB's default collation is `utf8mb4_bin` (case sensitive); clusters created with `new_collations_enabled_on_first_bootstrap=false` treat every collation as binary |
| `FULLTEXT` is accepted | no full-text index is built; the audit log console searches with `LIKE` |
| `ALTER TABLE` with several changes, `MODIFY`, `CHANGE`, `RENAME TABLE` | `OPTIMIZE TABLE` is refused: the driver sends `ANALYZE TABLE` |
| upsert, `GROUP_CONCAT`, timestamps past 2038, `utf8mb4` | `XA` transactions, `CHECKSUM TABLE`, `SQL_CALC_FOUND_ROWS` (the kernel uses none of them on a site) |
| the MySQL DFS cluster backend | dropping the primary key of a table with a clustered index |

### Vitess and PlanetScale

Vitess with one **unsharded** keyspace, and PlanetScale's MySQL databases (which are Vitess). Measured on a Vitess 25
`vttestserver` with `--foreign_key_mode=disallow`, as PlanetScale runs by default.

| Works | Does not, or differently |
|---|---|
| a whole site in an unsharded keyspace: `exp:install --db=vitess` | the keyspace is not made by `CREATE DATABASE`: create it in Vitess (`vtctldclient`) or in PlanetScale's console first; `--db-action=remove` drops its tables, never the keyspace |
| everything MySQL 8 does: MySQL runs underneath | foreign keys are refused with `foreign_key_mode=disallow`: the driver leaves them out of `CREATE TABLE` and `ALTER TABLE` (the kernel schema has none) |
| transactions, savepoints, every isolation level, `LOCK TABLES`, `GET_LOCK()` | **a transaction longer than the tablet's transaction timeout is killed** (vttablet's default 30 seconds, PlanetScale 20 seconds). The site package installs in one long transaction: raise `--queryserver-config-transaction-timeout` (600s here) for the install; on PlanetScale install elsewhere and import |
| `AUTO_INCREMENT` as MySQL in an unsharded keyspace | a **sharded** keyspace needs a vindex and a sequence table per table: not supported for a site; a data table connection can use a sharded table that is set up so |
| `FULLTEXT`, upsert, `GROUP_CONCAT`, timestamps past 2038 | `BEGIN WORK`, `XA` and `CHECKSUM TABLE` are refused: the driver sends `BEGIN` for the first |
| TLS (`SSL=verify`), which PlanetScale requires | PlanetScale's deploy requests change the schema of production branches: install on a development branch |

### SingleStore

SingleStore 8.5 and later, self-hosted or SingleStore Helios (with `SSL=verify`). Measured on SingleStore 10.10, the
free `singlestoredb-dev` container.

| Works | Does not, or differently |
|---|---|
| a whole site: `exp:install --db=singlestore` | a unique key must hold the shard key: the driver creates every table with a primary key as a **reference table** (`CREATE ROWSTORE REFERENCE TABLE`), copied to every leaf, and a table without one as a rowstore table. Right for a CMS; a very large table would rather be sharded by hand |
| `AUTO_INCREMENT` in insert order (reference tables, generated on the master aggregator) | foreign keys are refused: left out |
| transactions | isolation is `READ COMMITTED` whatever is set; `SAVEPOINT` is accepted and **does nothing** (a rollback to it rolls back nothing) |
| upsert, `GROUP_CONCAT`, `utf8mb4` | `FULLTEXT` on rowstore tables is refused: left out, the audit log console searches with `LIKE` |
| timestamps past 2038: `sqlUnixTimestamp()` and `sqlFromUnixTimestamp()` count with `TIMESTAMPDIFF` and `TIMESTAMPADD` | SingleStore's own `UNIX_TIMESTAMP()` and `FROM_UNIXTIME()` end in 2038 |
| case-insensitive text: new tables get `COLLATE=utf8mb4_general_ci` | the default collation is `utf8mb4_bin` |
| `LOCK TABLES` is accepted | `GET_LOCK()` does not exist; `RENAME TABLE` (the driver sends `ALTER TABLE ... RENAME TO`), `ALTER TABLE ... CHANGE` of a column's type (use `MODIFY`), `DELETE ... ORDER BY ... LIMIT`, `SHOW CREATE DATABASE` (the driver reads `information_schema`), `CHECKSUM TABLE`, `XA` |
| prefix indexes are accepted | the prefix length is ignored: the whole column is indexed |

### MariaDB Xpand

Not measured: MariaDB does not publish a container image of Xpand, and it is sold only as part of SkySQL and
Enterprise subscriptions. The profile follows Xpand 6's documentation.

| Expected to work | Does not, or differently (documented) |
|---|---|
| a whole site: `exp:install --db=xpand` (unverified) | `FULLTEXT` indexes are not supported: left out |
| foreign keys, transactions, savepoints, `READ COMMITTED`, `REPEATABLE READ`, `SERIALIZABLE` | `AUTO_INCREMENT` values are unique but not in insert order (allocated per node) |
| `GET_LOCK()`: table locks become named locks | `LOCK TABLES` is accepted and ignored; `XA` |

### Amazon Aurora MySQL

Not measured: Aurora runs only at AWS, and nothing was signed up for. Aurora MySQL 3 is MySQL 8.0, so the driver is the
MySQL driver with one addition.

| Expected to work | Does not, or differently (documented) |
|---|---|
| everything MySQL 8.0 does: `exp:install --db=aurora` (unverified) | **after a failover** the cluster endpoint can still lead to the old writer, now a reader, for some seconds: every write fails with 1290 or 1836. The driver checks `@@innodb_read_only` when it connects and connects again (`FailoverRetries`, `FailoverWait`) |
| the reader endpoint as the read replica (`UseSlaveServer`, `SlaveServerArray`) | the replica lag check reads MySQL's replica status, which Aurora's readers do not show: set `SlaveServerStatusCheck=disabled` |
| TLS (`SSL=verify` with Amazon's CA bundle as `SSLCA`) | IAM authentication tokens are not supported: use a database user and password |

## Settings

`[DatabaseSettings]` as for MySQL (`Server`, `Port`, `User`, `Password`, `Database`, `Socket`), with the driver's
`DatabaseImplementation`, and `[MySQLWireSettings]` in `site.ini`:

```ini
[DatabaseSettings]
DatabaseImplementation=tidb
Server=tidb.example.com
Port=4000
User=exponential
Password=...
Database=site

[MySQLWireSettings]
# empty: the driver's own profile; tidb, vitess, singlestore, xpand, aurora, mysql or mariadb
Profile=
# warning: each connection asks the server what it is and warns when it is not the profile's database
VerifyServer=disabled
# READ COMMITTED, REPEATABLE READ ... as the database keeps it; empty: the server's default
IsolationLevel=
# auto (the profile's), native (LOCK TABLES), named (GET_LOCK), none
TableLocks=auto
NamedLockTimeout=30
# TiDB: AUTO_ID_CACHE of new tables; 1 hands out ids in MySQL's order, 0 leaves TiDB's cache
TiDBAutoIdCache=1
# disabled, enabled (TLS, the certificate is not checked) or verify (checked against SSLCA, else the system's CAs)
SSL=disabled
SSLCA=
SSLCert=
SSLKey=
# Aurora: a primary that is read-only after connecting is connected to again
FailoverCheck=auto
FailoverRetries=5
FailoverWait=1
```

The installer writes `SSL` and `SSLCA` into `settings/override/site.ini.append.php` as well as into each siteaccess,
because the cluster file handler connects before a siteaccess is matched.

## What the drivers do differently

`expMySQLWireDB::query()` passes every statement through `expMySQLWireProfile::rewriteStatement()`, which returns the
statements to send: one in most cases, none when the database has no use for it, two for a `RENAME TABLE` of two
tables on SingleStore. Data statements (`INSERT`, `UPDATE`, `DELETE`, a `SELECT` without `SHARE`) are not even looked
at past their first word. The rules, all tested without a server:

| Statement | TiDB | Vitess | SingleStore | Xpand | Aurora |
|---|---|---|---|---|---|
| `BEGIN WORK` | `BEGIN` | `BEGIN` | `BEGIN` | `BEGIN` | `BEGIN` |
| `CREATE TABLE` | `COLLATE=utf8mb4_general_ci`, `AUTO_ID_CACHE=1` | foreign keys left out | `ROWSTORE REFERENCE` (with a primary key), foreign keys and `FULLTEXT` left out, `COLLATE=utf8mb4_general_ci` | `FULLTEXT` left out | as it is |
| `ALTER TABLE ... ADD FOREIGN KEY` | as it is | not sent | not sent | as it is | as it is |
| `ALTER TABLE ... ADD FULLTEXT`, `CREATE FULLTEXT INDEX` | as it is | as it is | not sent | not sent | as it is |
| `SELECT ... LOCK IN SHARE MODE` / `FOR SHARE` | `FOR UPDATE` | as it is | `LOCK IN SHARE MODE` | as it is | as it is |
| `RENAME TABLE a TO b, c TO d` | as it is | as it is | one `ALTER TABLE ... RENAME TO` each | as it is | as it is |
| `OPTIMIZE TABLE` | `ANALYZE TABLE` | as it is | as it is | as it is | as it is |

A collation is added only when the statement names none and its character set is `utf8mb4` or `utf8` (or none: the
database's). `CREATE TABLE ... LIKE` and temporary tables are not changed. The parser respects quotes and parentheses:
a default value `'FOREIGN KEY, (x)'` stays as it is.

Besides the statements:

- **Table locks** (`lock()`, used for the language mask, order statuses, the search log, identifiers): `named` takes
  one `GET_LOCK('exp:<database>.<table>')` per table, sorted, so two sessions cannot wait for each other, and
  `unlock()` releases them in reverse order.
- **Databases**: `createDatabase()` makes a TiDB database `utf8mb4_general_ci`. On Vitess it makes nothing: it checks
  that the keyspace exists, and `removeDatabase()` drops the keyspace's tables and views. The servers' own databases
  (`cluster`, `memsql`, `METRICS_SCHEMA`, `_vt` ...) are not listed.
- **Relations**: foreign keys are offered as a relation type only where they are kept (TiDB, Xpand, Aurora).
- **TLS**: `SSL=enabled` or `verify` opens the connection with `mysqli_ssl_set()` and `MYSQLI_CLIENT_SSL`.

## Installing a site

Create the database first, as for MySQL (on Vitess and PlanetScale the keyspace or database in their own tools; on
TiDB `CREATE DATABASE site CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci`). Then, with `mysqli` in the PHP of the
command line:

```bash
EXP_INSTALL_DB_PASSWORD=secret ./console exp:install --apply --db=tidb --db-host=tidb.example.com \
  --db-port=4000 --db-name=site --db-user=exponential
EXP_INSTALL_DB_PASSWORD=secret ./console exp:install --apply --db=planetscale --db-host=aws.connect.psdb.cloud \
  --db-port=3306 --db-name=site --db-user=... --db-ssl=verify
EXP_INSTALL_DB_PASSWORD=secret ./console exp:install --apply --db=singlestore --db-host=svc.singlestore.com \
  --db-port=3306 --db-name=site --db-user=admin --db-ssl=verify
```

`--db=` takes `tidb`, `vitess` (or `planetscale`), `singlestore` (or `memsql`), `xpand` and `aurora` (or
`aurora-mysql`); `--db-ssl` takes `enabled` or `verify`, `--db-ssl-ca` a CA file. The kickstarter takes the same names
as `[database_choice] Type`, and `SSL` and `SSLCA` in `[database_init]`. The setup wizard in the browser lists the
database types whose PHP extension it finds, which for all of these is MySQL's: choose them through the kickstarter or
`exp:install`.

Measured on 2026-11-26, the site package `sevenx_multisite` with its demo content, PHP 8.5, each database in a
container with two to four cores, on a host busy with other work (the times compare poorly). After each install the
front page and the administration's login page answered 200 over PHP's built-in server, `exp:cluster:enable --apply`
made a one-node DFS cluster (312 files, the metadata in the same database) and the front page answered 200 again:

| Database | Result | Steps | Errors | Checks | Package objects | Time |
|---|---|---|---|---|---|---|
| TiDB 8.5.1 | installed | 16 | 0 | 9 passed | 291 | 764 s |
| Vitess 25 (`vttestserver`, transaction timeout 600 s) | installed | 16 | 0 | 9 passed | 291 | 526 s |
| SingleStore 10.10 | installed | 16 | 0 | 9 passed | 291 | 279 s |

With vttablet's default transaction timeout of 30 seconds the Vitess install stopped in the demo content package
("transaction ... in use: for tx killer rollback"). The migrations that run after an install take no exact database
marker on these three (they refuse `CHECKSUM TABLE`), and go on without one, as on a database without a caption.

## Data table connections

A [data table connection](data-tables.md) names one of these databases as its engine (`tidb`, `vitess`, `planetscale`,
`singlestore`, `xpand`, `aurora`), or the engine `mysql` with the option `profile`. It is opened by the database's
driver, so the same rules apply; the options `ssl` and `ssl_ca` set TLS:

```ini
[Connection_analytics]
Engine=singlestore
Server=svc.singlestore.com
Port=3306
Database=analytics
User=reader
Secret=env:ANALYTICS_PASSWORD
Options[ssl]=verify
```

Without `mysqli` the connection falls back to PDO MySQL as a plain MySQL connection, without the profile.

## Clustering

`exp:cluster:enable` takes the MySQL DFS backend (`eZDFSFileHandlerMySQLiBackend`) for all of these: its schema goes
through the driver (`kernel/sql/mysql/cluster_dfs_schema.sql`, rewritten as above), and the backend's own statements
(`BEGIN`, `SELECT ... FOR UPDATE`) are taken by every one of them. Measured on TiDB, Vitess and SingleStore (above).
The backend opens its own `mysqli` connection, without TLS: on PlanetScale, TiDB Cloud and SingleStore Helios, which
require TLS, keep the DFS metadata in another database.

## Tests

`tests/tests/lib/ezdb/mysqlwire/` (DB-less, testsuite `mysqlwire`) and `.../mysqlwire/live/` (testsuite
`mysqlwire-live`; each database skips without `EXP_TEST_<TIDB|VITESS|SINGLESTORE|XPAND|AURORA>_HOST`, and `_PORT`,
`_USER`, `_PASSWORD` or `_PASSWORD_FILE`, `_DATABASE`). The GitHub job `mysqlwire` runs the live tests against TiDB
8.5.1, Vitess (`vttestserver`) and SingleStore (`singlestoredb-dev`) in containers; Xpand and Aurora skip.

| File | What |
|---|---|
| `expMySQLWireProfileTest` | the facts of every profile, recognising a server, every rewrite rule, the kernel and cluster schemas rewritten for each database |
| `expMySQLWireDBTest` | the driver over a recording connection: settings, statements in order, `BEGIN`, named and native table locks, relations, timestamps, databases and keyspaces, the character set, isolation levels, Aurora's failover check, the installer, the setup wizard, the cluster setup and data table connections |
| `live/expMySQLWireLiveTest` | against each server: it is recognised, MySQL's DDL with foreign keys, `FULLTEXT` and prefix indexes, case-insensitive text and emoji, ids after an explicit id, transactions, shared locks and table locks, upsert, `GROUP_CONCAT`, timestamps past 2038, schema changes and the schema read back |

The feature probe behind the tables above ran the same statements against each container, outside the test suite.
