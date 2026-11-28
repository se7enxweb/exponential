# Oracle database support

This page is for administrators and database administrators who run an Exponential site on Oracle Database. It is the
one page for Oracle that every other database has: the support level, what the server and PHP need, the settings, an
install, the limits and the versions that were tested. The deeper chapters are linked where they say more.

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL · 7. Microsoft SQL Server and Azure SQL (more databases from 6.0.15:
> [Choosing a database](../../help/choosing-a-database.md)). Details: [Database
> support](../../help/database-support.md#support-levels-at-a-glance).

The driver `eZOracleDB`, the schema handler `eZOracleSchema`, console commands (`ext:ezoracle:<name>`) and cronjobs
come with the `ezoracle` extension. `composer.json` requires it, so it is in `extension/ezoracle` of every
installation; it is activated only for sites that use Oracle.

## Contents

1. [Support level](#support-level)
2. [Requirements](#requirements)
3. [PHP extension](#php-extension)
4. [Settings](#settings)
5. [Installing a site](#installing-a-site)
6. [Limits](#limits)
7. [Tested versions](#tested-versions)
8. [Related pages](#related-pages)

## Support level

| Data tables | Full site | DFS | Multi-server cluster | Setup wizard | `exp:install` / kickstarter | xne upgrade tested |
|---|---|---|---|---|---|---|
| yes (`Engine=oracle`) | yes | yes, in the kernel (`expDFSFileHandlerOracleBackend`, Oracle 12.1 and later) | yes, with read replicas | yes, with `oci8` | `--db=oracle` | yes: one full upgrade of a kickstarted site and an exact rollback |

## Requirements

- **Oracle Database 19c or later** in the character set `AL32UTF8`. The driver uses `STANDARD_HASH` and
  `OFFSET`/`FETCH`; 11g and older are not supported.
- **A user** that may create sessions, tables, views, triggers, sequences and procedures.
  `php extension/ezoracle/bin/php/ora-grant.php` prints the grants for a DBA to run.

The step-by-step preparation is in [Oracle setup](../../help/database-administrators/oracle-setup.md).

## PHP extension

`oci8` (PECL), built against an Oracle client; the Instant Client is enough, `ORACLE_HOME` is not needed with it.
oci8 3.4 for PHP 8.4 and 8.5, 3.2 for PHP 8.1 to 8.3, from EPEL, Remi or your vendor. The setup wizard offers Oracle
whenever the PHP running it has `oci8`.

## Settings

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<the password>
# the connect string: Easy Connect host:port/service_name, a TNS alias or a full descriptor;
# Server and Port are not used by this driver
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

`ezoracle.ini [ConnectionSettings]` adds persistent connections, Database Resident Connection Pooling (DRCP, so
hundreds of PHP workers share a pool of server processes), retries, failover, call timeouts and session tracing; every
one is off by default ([Oracle connections and tuning](../../help/database-administrators/oracle-connections-and-tuning.md)).
When these settings come from a site extension instead of `settings/override/`, list that extension **before**
`ezoracle` in `ActiveExtensions[]`.

## Installing a site

```bash
export EXP_INSTALL_DB_PASSWORD='<the password>'
php bin/php/console exp:install --db=oracle --db-host=127.0.0.1 --db-user=exponential --db-name=FREEPDB1
```

A service name in `--db-name` becomes the Easy Connect string `<db-host>:<db-port>/<service>`; a value with `/` or `(`
is used as it is, and `@alias` names a TNS alias. In the browser, choose Oracle on the setup wizard's database page;
the kickstarter takes `Type=oci8` in `[database_choice]` ([console install](../../install/07-console-install.md)).

## Limits

- Oracle stores `''` as NULL: `OracleEmptyStringForNull=enabled` returns `''` for text columns, and since 6.0.15 a
  comparison with the empty string is sent as `IS NULL` / `IS NOT NULL`.
- Oracle compares strings case-sensitively: `OracleCaseInsensitive=enabled`, with linguistic indexes from
  `ext:ezoracle:ci-indexes --create`.
- Oracle sorts NULL after every value in ascending order, where MySQL and SQLite sort it first.
- The upgrade command has no built-in safety copy for Oracle: back up with `expdp` or `ext:ezoracle:datapump` and run
  the upgrade with `--no-backup`. xne makes its own verified backup.
- Exponential 6.0.15 or later is needed for the installers; earlier versions do not offer Oracle there.

## Tested versions

Oracle is not in the project's CI for the whole database suite; the DFS cluster backend is tested on Oracle Free 23.
The engine parity check and the installs run by hand against Oracle AI Database 26ai Free (23.26) with oci8 3.4.1 and
Instant Client 23.26 on PHP 8.5. xne was proven on Oracle once: a full upgrade of a kickstarted site through
`ezoracle` (the kernel's upgrade applied five steps, the health check passed) and an exact rollback of every file and
table.

## Related pages

- [Database support](../../help/database-support.md) and [Choosing a database](../../help/choosing-a-database.md).
- [9.6 Oracle 19c and later](../../install/09-databases.md#96-oracle-19c-and-later-ezoracle), the operator's chapter,
  and the extension's `README.md` and `INSTALL` in `extension/ezoracle/`.
- [Oracle setup](../../help/database-administrators/oracle-setup.md),
  [Oracle connections and tuning](../../help/database-administrators/oracle-connections-and-tuning.md).
- [Clustering on Oracle](oracle-dfs-cluster.md) and [Clustering in Exponential 6.0](clustering-overview.md).
