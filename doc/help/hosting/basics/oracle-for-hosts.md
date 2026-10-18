---
title: Oracle for hosts
audience: hosting, admin
category: hosting
order: 110
summary: Oracle Database 19c and later as the database of an Exponential site, from a host's point of view: the ezoracle extension, oci8 and the Instant Client, the user and its grants, the connect string, persistent connections and DRCP, maintenance commands, backups and Oracle's own behavior.
keywords: Oracle, Oracle 19c, ezoracle, oci8, Instant Client, AL32UTF8, LD_LIBRARY_PATH, TNS_ADMIN, connect string, Easy Connect, DRCP, persistent connections, ora-grant, datapump, expdp, impdp, ezoracle.ini, ActiveExtensions, hosting
type: help
---
# Oracle for hosts

This page is for system administrators at a hosting company whose customers run Exponential on Oracle, usually on a
database the customer already owns. It answers: what the web servers need (PHP `oci8` and an Oracle client), what the
customer's DBA must provide, how the connection is configured, which settings reduce connection cost, and how the
database is maintained and backed up. Oracle is fifth in the official order. Sources:
[Database support](../../database-support.md#5-oracle) and
[chapter 9.6](../../../install/09-databases.md#96-oracle-19c-and-later-ezoracle); the complete reference is the
extension's own `README.md` and `INSTALL` in `extension/ezoracle/`.

## Contents

1. [What it is](#what-it-is)
2. [Requirements](#requirements)
3. [What the DBA provides](#what-the-dba-provides)
4. [Settings and install](#settings-and-install)
5. [Persistent connections and DRCP](#persistent-connections-and-drcp)
6. [Maintenance and backups](#maintenance-and-backups)
7. [Oracle's own behavior](#oracles-own-behavior)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## What it is

Oracle support comes from the **`ezoracle`** extension, which `composer.json` requires (`~2.3.7`), so it is in
`extension/ezoracle` in every installation. It provides the driver `eZOracleDB`, the schema handler
`eZOracleSchema`, console commands (`ext:ezoracle:<name>`) and cronjobs. It is activated only for sites that use
Oracle; the installers activate it for the new site.

## Requirements

| Item | Value |
|---|---|
| Exponential | 6.0.15 or later (earlier versions do not offer Oracle in the installers) |
| Oracle Database | **19c or later** (the wizard checks 19.0); 11g and older are not supported |
| Character set | `AL32UTF8`; `NLS_LANG` is not needed |
| PHP | 8.0 to 8.5 with `oci8` (PECL oci8 3.4 for PHP 8.4 and 8.5, 3.2 for 8.1 to 8.3) |
| Oracle client | built against by `oci8`; the Instant Client is enough, `ORACLE_HOME` is not needed |
| Seen working | Oracle AI Database 26ai Free (23.26), oci8 3.4.1, Instant Client 23.26, PHP 8.5 |

`oci8` itself comes from your vendor, EPEL or Remi. When the client is outside the system library path, set
`LD_LIBRARY_PATH` (and `TNS_ADMIN` for TNS aliases) **in every place PHP runs**: the PHP-FPM pool (`env[...]`),
Velocity's environment and the cron environment.

> **Warning**
> A PHP that loads `oci8` on the command line but not in the PHP-FPM pool (or the other way round) is the most common
> Oracle problem on a host. Check `php -m` and the web PHP's information page for `oci8` on every server.

## What the DBA provides

An Oracle user that may create sessions, tables, views, triggers, sequences and procedures. The extension prints the
SQL for the DBA:

```bash
ORACLE_NEW_PASSWORD='<a strong password>' php extension/ezoracle/bin/php/ora-grant.php <customer> - USERS
```

(`-` takes the password from `ORACLE_NEW_PASSWORD`, so it is not in the process list; `USERS` is the tablespace.)
The same is available as `php bin/php/console ext:ezoracle:ora-grant`. For the health and report commands, also
grant `SELECT_CATALOG_ROLE`.

## Settings and install

```ini
[DatabaseSettings]
DatabaseImplementation=ezoracle
User=<customer>
Password=<the password>
# Easy Connect host:port/service_name, a TNS alias or a full descriptor;
# Server and Port are not used by this driver
Database=db.example.com:1521/<service>
Charset=utf-8

[ExtensionSettings]
ActiveExtensions[]=ezoracle
```

```bash
EXP_INSTALL_DB_PASSWORD='<the password>' php bin/php/console exp:install --db=oracle \
    --db-host=db.example.com --db-port=1521 --db-name=<service> --db-user=<customer> --allow-root-user
```

`--db-name` is the **service name** (default `FREEPDB1`); a full connect string or `@<tns alias>` is used as given.
For the kickstarter set `[database_choice] Type=oci8` (`oracle` accepted).

## Persistent connections and DRCP

A new dedicated Oracle session costs tens of milliseconds per request. Everything beyond the plain connection is in
`ezoracle.ini`, overridden in `settings/override/ezoracle.ini.append.php`; every feature is off by default:

```ini
[ConnectionSettings]
Persistent=enabled
DRCP=enabled
ConnectionClass=EXPONENTIAL
KeepAliveInterval=60
```

- **Persistent connections** live as long as the PHP process; `oci8.persistent_timeout`, `oci8.max_persistent` and
  `oci8.ping_interval` in `php.ini` govern them.
- **DRCP** (Database Resident Connection Pooling) shares a pool of server processes between all PHP processes, so
  hundreds of workers do not need hundreds of dedicated server processes. The DBA starts the pool once:
  `EXECUTE DBMS_CONNECTION_POOL.START_POOL;`.
- **Extension order matters.** When the settings come from a site extension rather than `settings/override/`, list
  that extension **before** `ezoracle` in `ActiveExtensions[]`
  ([chapter 9.6](../../../install/09-databases.md#persistent-connections-and-drcp)).

Check the effective value with `php bin/php/console exp:ini get ezoracle.ini/ConnectionSettings/DRCP --allow-root-user`.

## Maintenance and backups

Console commands (`php bin/php/console ext:ezoracle:<name>`): `health`, `gather-stats`, `recompile`,
`purge-recyclebin`, `sequence-sync`, `schema-diff`, `indexes`, `report`, `datapump`, `ci-indexes`. Each prints one
PASS, WARN, FAIL or INFO line per check. The cronjob parts `ezoracle` (nightly) and `ezoraclehealth` (hourly) run
them once enabled in `ezoracle.ini [CronjobSettings]`:

```text
15 3 * * *  cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoracle
0 * * * *   cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoraclehealth
```

**Backups**: `php bin/php/console ext:ezoracle:datapump` (see its `--help`) or Oracle's `expdp`; restore with
Oracle's `impdp`. Slow statements go to `var/log/oracle-slow.log` when `[LogSettings] SlowQueryThreshold` is set.

## Oracle's own behavior

| Behavior | Setting |
|---|---|
| Oracle stores `''` as NULL | `OracleEmptyStringForNull=enabled` returns `''` for text columns |
| Strings compare with case | `OracleCaseInsensitive=enabled`, then `ext:ezoracle:ci-indexes --create` |
| `AS` before a table alias is rejected (`ORA-00933`) | fix the extension's SQL |

Oracle is not in the project's CI; Oracle-only tests in the kernel suite skip on other engines
([test coverage](../../database-support.md#test-coverage-ci)).

## Checklist

- [ ] Exponential 6.0.15 or later.
- [ ] Oracle 19c or later in `AL32UTF8`.
- [ ] `oci8` loaded in the web, command line and Velocity PHP, with the client libraries found in each.
- [ ] The user created from `ora-grant.php` output; `SELECT_CATALOG_ROLE` for health checks.
- [ ] `ezoracle` active for the site; persistent connections and DRCP considered.
- [ ] Backups agreed with the customer's DBA.

## Related pages

- [Databases in the official order](databases-in-the-official-order.md)
- [Database support: Oracle](../../database-support.md#5-oracle)
- [Chapter 9.6: Oracle 19c and later](../../../install/09-databases.md#96-oracle-19c-and-later-ezoracle)
- [SQLite and Oracle drivers](../../../specifications/6.0/database-drivers-sqlite-oracle.md)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
