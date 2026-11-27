---
title: "Microsoft SQL Server: status for DBAs"
audience: dba
category: database-administration
order: 22
summary: Where Microsoft SQL Server stands for Exponential - supported from 6.0.15 on the kernel's own driver expSQLServerDB (SQL Server 2017 to 2025, Azure SQL), not in 6.0.14 and older - with what a DBA prepares (collation, read committed snapshot, logins), the PHP layers, encryption, pooling, read replicas, the DFS cluster, backups and what is not there yet.
keywords: Microsoft SQL Server, MSSQL, SQL Server, Azure SQL, expSQLServerDB, expSQLServerSchema, sqlsrv, pdo_sqlsrv, pdo_odbc, pdo_dblib, ODBC Driver 18, READ_COMMITTED_SNAPSHOT, UTF-8 collation, NVARCHAR, Always On, ApplicationIntent, DFS cluster, BACKUP DATABASE, xrowmssql
type: help
---
# Microsoft SQL Server: status for DBAs

This page is for database administrators in organizations standardized on Microsoft SQL Server who are asked to run
an Exponential site on it. The short answer: **yes, from Exponential 6.0.15**. The kernel has a driver of its own,
`expSQLServerDB`, with the schema handler `expSQLServerSchema`, the kernel schema in `kernel/sql/sqlserver/`,
installer entries and a DFS cluster backend. Exponential 6.0.14 and older have none of it. The full reference is the
[Microsoft SQL Server driver](../../features/6.0/sqlserver-database-driver.md) page.

## Contents

1. [Servers and PHP layers](#servers-and-php-layers)
2. [Preparing the database](#preparing-the-database)
3. [Encryption, pooling and sessions](#encryption-pooling-and-sessions)
4. [Read replicas and the cluster](#read-replicas-and-the-cluster)
5. [Backups and upgrades](#backups-and-upgrades)
6. [What is not there yet](#what-is-not-there-yet)
7. [The old xrowmssql extension](#the-old-xrowmssql-extension)
8. [Related pages](#related-pages)

## Servers and PHP layers

| Server | Status |
|---|---|
| SQL Server 2017 (14.x) | supported; the floor (`STRING_AGG`, `TRIM`, `CONCAT_WS`) |
| SQL Server 2019, 2022, 2025 | supported; 2022 is the main CI target, all four run in CI |
| Azure SQL Database, Managed Instance | supported; transient errors are retried |
| Azure SQL Edge, SQL Server 2016 and older | not supported |

PHP reaches it through `sqlsrv` (recommended), `pdo_sqlsrv`, `pdo_odbc` with Microsoft's ODBC Driver 17 or 18, or
`pdo_dblib` over FreeTDS; `[SQLServerSettings] Layer=auto` takes the first one loaded. Measured: `sqlsrv` and
`pdo_sqlsrv` 5.11 to 5.13 build and pass on PHP 8.0 to 8.5 (5.11 on PHP 8.0 reads `TrustServerCertificate=yes` as
false with `pdo_sqlsrv`).

## Preparing the database

The installers do not create the database on SQL Server. Create it, and the login and user of the site, first:

```sql
CREATE DATABASE [site] COLLATE Latin1_General_100_CI_AS_SC_UTF8;
ALTER DATABASE [site] SET READ_COMMITTED_SNAPSHOT ON;
```

- **Read committed snapshot** lets readers never wait for writers, as the kernel expects from MySQL and PostgreSQL;
  the driver notes it in the debug output when it is off.
- **A UTF-8 collation** (2019 and later) or `NVARCHAR` columns keep every character; `VARCHAR` in a Latin collation
  loses them, and PDO_ODBC reads `VARCHAR` in the client code page.
- **Index keys**: SQL Server indexes at most 1,700 bytes and no `NVARCHAR(MAX)`; the schema handler makes the eight
  kernel text columns in an index key `NVARCHAR(840)`.

Then `exp:install --apply --db=sqlserver --db-host=... --db-name=site --db-user=...` or the kickstarter's
`Type=sqlserver` ([installing a site](../../features/6.0/sqlserver-database-driver.md#installing-a-site-on-sql-server)).

## Encryption, pooling and sessions

- `--db-encrypt` / `[SQLServerSettings] Encrypt`: `yes` (the default), `no` or `strict` (TDS 8.0, SQL Server 2022 and
  later); `TrustServerCertificate` accepts a self-signed certificate on a trusted network.
- `ConnectionPooling=disabled` (the default): a pooled `sqlsrv` connection would be inherited by forked workers. Keep
  `odbcinst.ini`'s pooling off on Linux as well.
- Each session sets `NOCOUNT`, `XACT_ABORT`, the ANSI options, `DATEFORMAT ymd`, `LOCK_TIMEOUT` and `READ COMMITTED`.
  Deadlocks (1205) and lock timeouts (1222) are told apart; a single statement outside a transaction is retried.

## Read replicas and the cluster

- `UseSlaveServer=enabled` with the Always On listener or a secondary: anonymous reads go there with
  `ApplicationIntent=ReadOnly`; a replica further behind than `SlaveServerMaxLag` is not used. Across subnets set
  `MultiSubnetFailover=enabled`.
- The DFS cluster backend `expDFSFileHandlerSQLServerBackend` keeps the file metadata in the same database for any
  number of web servers; `exp:cluster:enable --backend=sqlserver` makes a one-node cluster
  ([Clustering on SQL Server](../../features/6.0/sqlserver-dfs-cluster.md)). File names are at most 830 characters.

## Backups and upgrades

The safety copy `exp:upgrade` makes before it writes has no built-in dump for SQL Server. Back up with
`BACKUP DATABASE` (or the service's backups on Azure) and run the upgrade with `--no-backup`. No upgrade from an older
release has been tested on SQL Server: 6.0.15 is the first release with the driver.

## What is not there yet

- The setup wizard in the browser lists SQL Server only when its PHP has `sqlsrv` or `pdo_sqlsrv`; with `pdo_odbc` or
  `pdo_dblib` alone, use the kickstarter or `exp:install`.
- No `update/database/` files for SQL Server; the schema is `kernel/sql/sqlserver/kernel_schema.sql`.
- `pdo_dblib` is covered by the tests without a server only.

## The old xrowmssql extension

The driver is not derived from xrowmssql. That extension was read for its problems only, and each of them is now a
test ([the traceability table](../../features/6.0/sqlserver-database-driver.md#traceability-the-old-extensions-problems)).
Do not install xrowmssql on 6.0.15.

## Related pages

- [Database support: Microsoft SQL Server](../database-support.md#7-microsoft-sql-server-and-azure-sql),
  [Choosing a database](../choosing-a-database.md)
- [Microsoft SQL Server status](../hosting/basics/microsoft-sql-server-status.md) (for hosting companies)
- [Supported engines and versions](supported-engines-and-versions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
