---
title: Setting up Oracle with ezoracle
audience: dba
category: database-administration
order: 19
summary: How to prepare Oracle Database for an Exponential site with the ezoracle extension - requirements (19c or later, AL32UTF8, oci8 and an Oracle client), the grants printed by ora-grant.php, the connect string forms, the console install, the site.ini block, and Oracle's own behaviors (empty string as NULL, case-sensitive comparison, no AS before a table alias).
keywords: Oracle, ezoracle, eZOracleDB, eZOracleSchema, 19c, AL32UTF8, oci8, Instant Client, ora-grant.php, SELECT_CATALOG_ROLE, Easy Connect, TNS alias, connect string, FREEPDB1, exp:install --db=oracle, OracleEmptyStringForNull, OracleCaseInsensitive, ORA-00933
type: help
---
# Setting up Oracle with ezoracle

This page is for Oracle database administrators who prepare a schema for an Exponential site. Oracle support comes
from the `ezoracle` extension (version 2.3.7, required by `composer.json` as `~2.3.7`, so it is in
`extension/ezoracle` of every installation and activated only for sites that use Oracle). It provides the driver
`eZOracleDB`, the schema handler `eZOracleSchema`, console commands and cronjobs. This page answers: what is
required, which grants the account needs, how to connect and install, and which Oracle behaviors matter. Sources:
[9.6 Oracle 19c and later](../../install/09-databases.md#96-oracle-19c-and-later-ezoracle) and the extension's own
`README.md` and `INSTALL` in `extension/ezoracle/`.

## Contents

1. [Requirements](#requirements)
2. [Create the account](#create-the-account)
3. [Connect strings](#connect-strings)
4. [Install](#install)
5. [The settings](#the-settings)
6. [Oracle's own behaviors](#oracles-own-behaviors)
7. [Checklist](#checklist)

## Requirements

- **Exponential 6.0.15 or later**; earlier versions do not offer Oracle in the installers.
- **Oracle Database 19c or later**; the wizard checks 19.0. The driver uses `STANDARD_HASH` and `OFFSET`/`FETCH`.
  Tested with Oracle AI Database 26ai Free (23.26). The project's support statement names 19c as the minimum
  ([Database support](../database-support.md#5-oracle)); the extension's `README.md` describes a fallback path for
  11.2 and 12.1 that is covered by its unit tests only. Plan with 19c.
- Database character set **AL32UTF8**. The driver connects in AL32UTF8; `NLS_LANG` is not needed.
- PHP 8.0 to 8.5 with **oci8** (PECL oci8 3.4 for PHP 8.4 and 8.5, 3.2 for 8.1 to 8.3), built against an Oracle
  client; the Instant Client is enough ([PHP extensions per engine](php-extensions-per-engine.md#oracle-oci8-and-the-client)).
- Oracle is not in the project's CI; Oracle-only kernel tests skip on other engines. Test upgrades on a staging copy.

## Create the account

The account may create sessions, tables, views, triggers, sequences and procedures. The extension prints the SQL for
a DBA to run:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php exponential - USERS
```

`-` takes the password from `ORACLE_NEW_PASSWORD`, so it does not appear in the process list; `USERS` is the
tablespace. The same is available as `php bin/php/console ext:ezoracle:ora-grant`. Read the printed SQL, then run it
as a DBA. For the `health` and `report` commands, also grant `SELECT_CATALOG_ROLE`: they read `V$` views, and without
the role the affected checks say so.

## Connect strings

`site.ini [DatabaseSettings] Database` is the connect string; `Server` and `Port` are not used by this driver. It may
be:

- Easy Connect: `127.0.0.1:1521/FREEPDB1`;
- Easy Connect Plus: `tcp://host:port/service?connect_timeout=5`;
- a TNS alias (with `TNS_ADMIN` set for every process);
- a full `(DESCRIPTION=...)` descriptor.

`ezoracle.ini [ConnectionSettings]` can build a descriptor over several listeners instead
([Oracle: persistent connections, DRCP and tuning](oracle-connections-and-tuning.md)).

## Install

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --apply --db=oracle \
    --db-host=127.0.0.1 --db-port=1521 --db-name=FREEPDB1 --db-user=exponential --allow-root-user
```

`--db-name` is the service name (default `FREEPDB1`); `host:port/service` is built from it. A full connect string
or `@<tns alias>` is used as given. `--db=oci8` and `--db=ezoracle` are accepted too. The installers activate the
extension for the new site. In the setup wizard Oracle appears when `oci8` is loaded and `extension/ezoracle` is
present. For the kickstarter set `[database_choice] Type=oci8` and the connect string as `Database`.

## The settings

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=exponential
Password=<the password>
Database=127.0.0.1:1521/FREEPDB1
Charset=utf-8
# Oracle stores '' as NULL; enabled returns '' for NULL in text columns
OracleEmptyStringForNull=disabled
# enabled: ALTER SESSION SET NLS_COMP=LINGUISTIC NLS_SORT=<sort>
OracleCaseInsensitive=disabled
OracleCaseInsensitiveSort=BINARY_CI

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

## Oracle's own behaviors

| Behavior | Effect on Exponential | What to do |
|---|---|---|
| `''` is stored as NULL | An empty string comes back as NULL | `OracleEmptyStringForNull=enabled` returns `''` for text columns |
| Strings compare binary (with case) | A login or lookup written for MySQL finds nothing in another case | `OracleCaseInsensitive=enabled`, then create linguistic indexes with `ext:ezoracle:ci-indexes --create` |
| `AS` is allowed only before a column alias | `ORA-00933` in an extension's SQL that writes `AS` before a table alias | Fix the extension ([Reviewing extension SQL](portable-sql-for-extensions.md)) |

The driver runs one `ALTER SESSION SET` on every connect (number format, length semantics, `CURSOR_SHARING`, and the
NLS settings when `OracleCaseInsensitive` is on); see [The SQL query cache](sql-query-cache.md) for how it treats
that statement.

## Checklist

- [ ] Oracle 19c or later, character set `AL32UTF8`.
- [ ] `oci8` loaded in every PHP the site uses; client libraries found by PHP-FPM, Velocity and cron.
- [ ] The account has the grants from `ora-grant.php`, plus `SELECT_CATALOG_ROLE` for health reports.
- [ ] `ezoracle` is in `ActiveExtensions[]`.
- [ ] Decisions taken on `OracleEmptyStringForNull` and `OracleCaseInsensitive`.
- [ ] Data Pump backups scheduled ([Backups per engine](backups-per-engine.md)).

## Related pages

- [Oracle: persistent connections, DRCP and tuning](oracle-connections-and-tuning.md)
- [Oracle maintenance commands and cronjobs](oracle-maintenance-commands.md)
- [Oracle for hosts](../hosting/basics/oracle-for-hosts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
