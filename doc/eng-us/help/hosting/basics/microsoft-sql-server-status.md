---
title: Microsoft SQL Server status
audience: hosting
category: hosting
order: 130
summary: The status of Microsoft SQL Server for Exponential 6.0.x as a host should state it: known to be possible with additional work at this time, with no driver, schema handler or installer entry in 6.0.x; what exists, what is missing, and what to tell a customer who asks.
keywords: Microsoft SQL Server, SQL Server, MSSQL, sqlsrv, pdo_sqlsrv, Windows hosting, database support, driver, eZDBInterface, schema handler, not supported, possible with additional work, customer question, hosting offer
type: help
---
# Microsoft SQL Server status

This page is for product managers, sales staff and support staff at a hosting company, especially one with a Windows
or Microsoft-centered offer, who are asked whether Exponential runs on Microsoft SQL Server. It answers: what the
project's statement is, what exists today, what is missing, and what a host can honestly offer instead. The source is
[Database support, Microsoft SQL Server](../../database-support.md#microsoft-sql-server-possible-with-additional-work).

## Contents

1. [The statement](#the-statement)
2. [What exists today](#what-exists-today)
3. [What is missing](#what-is-missing)
4. [What to tell a customer](#what-to-tell-a-customer)
5. [What a host can offer instead](#what-a-host-can-offer-instead)
6. [Related pages](#related-pages)

## The statement

> **Microsoft SQL Server is known to be possible with additional work at this time.**

It is not supported in Exponential 6.0.x: there is no driver, no schema handler and no installer entry for it. The
project's documentation says explicitly that nothing should be read as a promise of a release that has one. The
supported databases, in the official order, are 1. SQLite 3 (default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB ·
5. Oracle · 6. MySQL ([Databases in the official order](databases-in-the-official-order.md)).

> **Warning**
> Do not list SQL Server as an available database in a plan, an order form or marketing material for Exponential.

## What exists today

From [Database support](../../database-support.md#microsoft-sql-server-possible-with-additional-work):

| Piece | What it means |
|---|---|
| A small driver interface | Every database is reached through `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`); five existing drivers implement it, from a file database to a document store |
| An engine-neutral schema | `share/db_schema.dba` and `share/db_data.dba`, turned into an engine's DDL by a schema handler |
| Pluggable schema handlers | `settings/dbschema.ini` maps a type to a handler class |
| Pluggable driver aliases | `site.ini [DatabaseSettings] ImplementationAlias[]` is extended by an extension, the way `ezoracle` adds its aliases |
| Tools that read SQL Server | the database manager of the `sevenx_dse` extension ships an MS SQL driver for browsing tables; neither it nor any other tool is a way to run a site on SQL Server |
| PHP drivers | Microsoft publishes `sqlsrv` and `pdo_sqlsrv` for PHP; not tested with Exponential |

## What is missing

The work list, summarized from the same page:

| Piece | What it involves |
|---|---|
| A driver class | `eZDBInterface` on `sqlsrv`: connections, queries with offset and limit, transactions, identity columns, string and hash helpers |
| The `BIT_OR` aggregate | SQL Server has none; the content engine folds language masks with it |
| `UPDATE ... JOIN` | SQL Server writes it as `UPDATE ... FROM ... JOIN` |
| A schema handler | `.dba` to SQL Server DDL and back, for installs and the upgrade check |
| SQL files | the generated schema and the update files of every later release |
| Installer entries | the setup wizard, the extension check and the `exp:install` `--db` alias |
| Case and collation | a UTF-8 collation or `NVARCHAR` columns |
| Extensions | every extension with SQL of its own needs review |
| Tests and CI | a CI job with a SQL Server container |

How large that work is has not been measured, and the project gives no estimate. Anyone who wants to take it on is
invited to coordinate on the [software tracker](https://github.com/se7enxweb/exponential/issues), with the `ezoracle`
extension as the model of a driver shipped outside the kernel.

## What to tell a customer

A short answer support staff can use:

```text
Exponential 6.0.x does not run on Microsoft SQL Server today. The project states that it is
known to be possible with additional work, but there is no driver in the current release.
The supported databases, in the project's order, are SQLite 3 (the default), PostgreSQL,
MongoDB, MariaDB, Oracle and MySQL. We can host your site on <the databases of your plan>.
```

If the customer's reason is that their organization standardizes on SQL Server for its own data, a site database of
another engine next to it is usually acceptable; ask what the policy actually requires.

## What a host can offer instead

- **SQLite 3** needs no database server at all, which suits customers who mainly want to avoid running a second
  database product ([SQLite for hosts](sqlite-for-hosts.md)).
- **PostgreSQL** is the recommended database server ([PostgreSQL for hosts](postgresql-for-hosts.md)).
- **On Windows servers**, Windows is in tier 2 (known to work) with the official PHP builds for Windows and Apache
  or IIS with FastCGI; read what is missing there first
  ([Operating systems for hosts](operating-systems-for-hosts.md), [OS support: Windows](../../os-support.md#windows)).

## Related pages

- [Databases in the official order](databases-in-the-official-order.md)
- [Database support: Microsoft SQL Server](../../database-support.md#microsoft-sql-server-possible-with-additional-work)
- [Chapter 9.10: Microsoft SQL Server](../../../install/09-databases.md#910-microsoft-sql-server)
- [Working with the project and 7x](../business/working-with-the-project-and-7x.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
