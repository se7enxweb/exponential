---
title: Microsoft SQL Server status
audience: hosting
category: hosting
order: 130
summary: The status of Microsoft SQL Server for Exponential as a host should state it - supported from 6.0.15 on the kernel's own driver expSQLServerDB (SQL Server 2017 to 2025, Azure SQL Database and Managed Instance), not in 6.0.14 and older - what a host needs to offer it, what it does not do yet, and what to tell a customer who asks.
keywords: Microsoft SQL Server, SQL Server, MSSQL, Azure SQL, sqlsrv, pdo_sqlsrv, pdo_odbc, ODBC Driver 18, Windows hosting, LocalDB, database support, expSQLServerDB, DFS cluster, Always On, customer question, hosting offer
type: help
---
# Microsoft SQL Server status

This page is for product managers, sales staff and support staff at a hosting company, especially one with a Windows
or Microsoft-centred customer base, who are asked whether Exponential runs on Microsoft SQL Server.

## Contents

1. [The statement](#the-statement)
2. [What a host needs](#what-a-host-needs)
3. [What it does not do yet](#what-it-does-not-do-yet)
4. [What to tell a customer](#what-to-tell-a-customer)
5. [Related pages](#related-pages)

## The statement

> **Microsoft SQL Server and Azure SQL are supported from Exponential 6.0.15**, the seventh database in the project's
> official order: 1. SQLite 3 · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL
> Server and Azure SQL.

The kernel has a driver of its own, `expSQLServerDB`, written anew under the GPL, with a schema handler, the kernel
schema for SQL Server, installer entries and a DFS cluster backend. Exponential 6.0.14 and older have no SQL Server
driver: a customer on an older release upgrades first.

| | |
|---|---|
| Servers | SQL Server 2017, 2019, 2022 and 2025; Azure SQL Database and Azure SQL Managed Instance. Not SQL Server 2016 and older, not Azure SQL Edge (retired by Microsoft) |
| PHP layers | Microsoft's `sqlsrv` (recommended) or `pdo_sqlsrv`, PHP's `pdo_odbc` with ODBC Driver 17 or 18, or `pdo_dblib` over FreeTDS |
| Operating systems | Linux and Windows; SQL Server Express and LocalDB work for development |
| Installers | the kickstarter (`Type=sqlserver`) and `exp:install --db=sqlserver`; the setup wizard in the browser does not offer it |
| Several web servers | yes: the DFS cluster backend `expDFSFileHandlerSQLServerBackend`; read replicas through Always On |
| Tests | on every push against SQL Server 2017, 2019, 2022 and 2025 on three PHP layers, PHP 8.1 to 8.5, and LocalDB on Windows |

## What a host needs

- **An existing database.** The installer does not create it on SQL Server. Create it with a UTF-8 collation
  (`Latin1_General_100_CI_AS_SC_UTF8`, SQL Server 2019 and later) and `READ_COMMITTED_SNAPSHOT ON`.
- **The PHP extension** in the PHP of the site and of the command line: `sqlsrv` or `pdo_sqlsrv` from Microsoft
  (PECL on Linux, DLLs on Windows), or `pdo_odbc` with Microsoft's ODBC Driver 18. ODBC Driver 18 encrypts by
  default: a server with a self-signed certificate needs `--db-trust-server-certificate`.
- **Connection pooling off** (the default, `[SQLServerSettings] ConnectionPooling=disabled`), and on Linux
  `odbcinst.ini`'s pooling off as well: a pooled connection would be shared by forked workers.
- **Your own backups**: the upgrade's built-in safety copy has no dump for SQL Server. Use `BACKUP DATABASE` or the
  service's backups and run upgrades with `--no-backup`.

## What it does not do yet

- The setup wizard in the browser lists no SQL Server entry: install with the kickstarter or `exp:install`.
- No upgrade from an older release has been tested on SQL Server yet: 6.0.15 is the first release with the driver.
- No `update/database/` files for SQL Server: the schema is `kernel/sql/sqlserver/`.
- The DFS backend stores file names of at most 830 characters.

## What to tell a customer

> Exponential 6.0.15 runs on Microsoft SQL Server 2017 to 2025 and on Azure SQL, on Linux and on Windows, with its own
> driver and cluster backend, tested on every push. The database must exist before the installation; use a UTF-8
> collation and read-committed snapshot.

Do not offer SQL Server for a site that has to stay on 6.0.14 or older.

## Related pages

- [Databases in the official order](databases-in-the-official-order.md)
- [Choosing a database](../../choosing-a-database.md) and [Database support: Microsoft SQL Server](../../database-support.md#7-microsoft-sql-server-and-azure-sql)
- [Microsoft SQL Server driver](../../../features/6.0/sqlserver-database-driver.md) and
  [Clustering on SQL Server](../../../features/6.0/sqlserver-dfs-cluster.md)
- [Exponential on Windows](../../exponential-on-windows.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
