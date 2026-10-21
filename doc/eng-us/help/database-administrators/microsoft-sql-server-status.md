---
title: "Microsoft SQL Server: status for DBAs"
audience: dba
category: database-administration
order: 22
summary: Where Microsoft SQL Server stands for Exponential 6.0.x - not supported, known to be possible with additional work - what exists that such work could build on (the driver interface, the .dba format, pluggable schema handlers, the old xrowmssql extension), what is missing, and what a DBA should answer when asked to run a site on SQL Server.
keywords: Microsoft SQL Server, MSSQL, SQL Server, sqlsrv, pdo_sqlsrv, xrowmssql, not supported, possible with additional work, eZDBInterface, schema handler, BIT_OR, OFFSET FETCH, SCOPE_IDENTITY, NVARCHAR
type: help
---
# Microsoft SQL Server: status for DBAs

This page is for database administrators in organizations standardized on Microsoft SQL Server who are asked whether
an Exponential site can run on it. The short answer: **not in Exponential 6.0.x**. SQL Server is known to be possible
with additional work at this time; there is no driver, no schema handler and no installer entry for it, and nothing
here is a promise of a release that has one. This page summarizes what exists, what is missing and what to do
meanwhile. The full statement and work list are in
[Database support](../database-support.md#microsoft-sql-server-possible-with-additional-work).

## Contents

1. [The statement](#the-statement)
2. [What exists to build on](#what-exists-to-build-on)
3. [The xrowmssql extension](#the-xrowmssql-extension)
4. [What is missing](#what-is-missing)
5. [What to do meanwhile](#what-to-do-meanwhile)
6. [Contributing the work](#contributing-the-work)

## The statement

> Microsoft SQL Server is known to be possible with additional work at this time.

Do not plan a production site on SQL Server with Exponential 6.0.x. Do not install a third-party driver found online
on a production site without a full test: no SQL Server driver has been run on Exponential 6 by the project.

## What exists to build on

- **A small driver interface.** Every database is reached through `eZDBInterface`
  (`lib/ezdb/classes/ezdbinterface.php`); five drivers already implement it for a file database, three SQL servers
  and a document store ([The database layer in one page](the-database-layer-in-one-page.md)).
- **An engine-neutral schema.** `share/db_schema.dba` and `share/db_data.dba` are turned into each engine's DDL by a
  schema handler ([The .dba schema format](the-dba-schema-format.md)).
- **Pluggable schema handlers and aliases.** `settings/dbschema.ini` (`SchemaPaths[]`, `SchemaHandlerClasses[]`) and
  `site.ini [DatabaseSettings] ImplementationAlias[]` can be extended from an extension, the way `ezoracle` does it.
- **PHP drivers.** Microsoft publishes `sqlsrv` and `pdo_sqlsrv` for PHP on Linux and Windows; check Microsoft's
  support matrix for PHP versions. Not tested with Exponential.
- **Tools that read SQL Server** (not ways to run a site): the database manager of the `sevenx_dse` extension ships an
  MS SQL driver for browsing tables.

## The xrowmssql extension

[se7enxweb/xrowmssql](https://github.com/se7enxweb/xrowmssql) is a SQL Server extension written for the 4.x line and
SQL Server 2005/2008, copied from `xrow/xrowmssql` in October 2013. It has a driver (`eZMSSQLDB`), a schema handler
(`eZMssqlSchema`), settings and install notes. It has not been run on Exponential 6. To work there it needs at least:
a PHP 8 constructor, the `sqlsrv` back end on Linux (the old `mssql_*` functions are gone since PHP 7), real offset
and limit (`OFFSET ... FETCH`, SQL Server 2012 and later), `SCOPE_IDENTITY()` instead of `@@identity`, and
`NVARCHAR(MAX)` instead of `ntext`. Its `extension.xml` says GPL v2 while its `LICENSE` file is GPL v3; check both
before reusing code.

## What is missing

| Piece | What it involves |
|---|---|
| A driver | `eZDBInterface` on `sqlsrv`: connect, query, offset and limit, transactions, `lastSerialID()`, escaping, string helpers, `md5()` via `HASHBYTES` |
| The `BIT_OR` aggregate | SQL Server has none; the content engine folds language masks with it |
| `UPDATE ... JOIN` | Written `UPDATE t SET ... FROM t JOIN ...` on SQL Server |
| A schema handler | `.dba` to SQL Server DDL and back, for the installer and the upgrade check |
| SQL and update files | `kernel/sql/<engine>/`, `update/database/<engine>/` |
| Installer entries | the wizard, `settings/setup.ini [database_extensions]`, `exp:install --db` |
| Session statements, case and collation | ignore MySQL-only `SET`; choose a UTF-8 collation (2019 and later) or `NVARCHAR` |
| Extensions | review every extension with SQL of its own |
| Tests and CI | a CI job with a SQL Server container |

How large this work is has not been measured, and no estimate is given.

## What to do meanwhile

- Run the site on one of the six supported engines. PostgreSQL is the recommended server
  ([Choosing an engine](choosing-an-engine.md)).
- If your organization requires reporting from SQL Server, replicate or export data from the supported engine into
  SQL Server with your usual integration tools; the site itself keeps its own database.
- Treat any claim of SQL Server support you meet as unverified until it names a released, tested driver.

## Contributing the work

Anyone who wants to take it on is welcome: start from xrowmssql for the SQL Server specifics and from `ezoracle` as
the model of a current driver shipped outside the kernel, and open an issue on the
[software tracker](https://github.com/se7enxweb/exponential/issues) to coordinate.

## Related pages

- [Database support: Microsoft SQL Server](../database-support.md#microsoft-sql-server-possible-with-additional-work)
- [Microsoft SQL Server status](../hosting/basics/microsoft-sql-server-status.md) (for hosting companies)
- [Supported engines and versions](supported-engines-and-versions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
