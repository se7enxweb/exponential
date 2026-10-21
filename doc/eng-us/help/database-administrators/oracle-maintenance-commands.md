---
title: Oracle maintenance commands and cronjobs
audience: dba
category: database-administration
order: 21
summary: The maintenance tools the ezoracle extension gives an Oracle DBA - the ext:ezoracle console commands (health, gather-stats, recompile, purge-recyclebin, sequence-sync, schema-diff, indexes, report, datapump, ci-indexes), the scripts in extension/ezoracle/bin/php, and the ezoracle and ezoraclehealth cronjob parts with their switches.
keywords: Oracle, ezoracle, ext:ezoracle, health, gather-stats, DBMS_STATS, recompile, purge-recyclebin, sequence-sync, schema-diff, indexes, report, datapump, DBMS_DATAPUMP, expdp, impdp, ci-indexes, ora-update-seqs, mysql2oracle, oracleclusterbinarypurge, cronjob, ezoraclehealth, CronjobSettings
type: help
---
# Oracle maintenance commands and cronjobs

This page is for Oracle database administrators who keep an Exponential schema healthy. The `ezoracle` extension ships
console commands that wrap the usual Oracle maintenance, and cronjobs that run them on a schedule. This page answers:
which commands exist, what each one does, how to schedule them and where they report. Source: the "Console commands"
and "Cronjobs" sections of `extension/ezoracle/INSTALL` (version 2.3.7) and
[9.6 Maintenance](../../install/09-databases.md#maintenance).

## Contents

1. [How the commands behave](#how-the-commands-behave)
2. [The console commands](#the-console-commands)
3. [Scripts in bin/php](#scripts-in-binphp)
4. [Cronjobs](#cronjobs)
5. [A routine for an Oracle site](#a-routine-for-an-oracle-site)
6. [Checklist](#checklist)

## How the commands behave

```bash
php bin/php/console ext:ezoracle:<command> [-s <siteaccess>] [options]
```

Every command has `--help`, prints one PASS, WARN, FAIL or INFO line per check, and ends with exit status 1 when a
check failed, so it can guard a script. Commands that change something take `--dry-run`. `V$` and DBA views need
`SELECT_CATALOG_ROLE`; without it the affected checks say so.

## The console commands

| Command | What it does |
|---|---|
| `health` | Connection, versions, character set, invalid objects, tablespaces, sessions, blocking locks, recycle bin, sequences, statistics |
| `gather-stats` | `DBMS_STATS.GATHER_SCHEMA_STATS` (`[StatisticsSettings]`) |
| `recompile` | `DBMS_UTILITY.COMPILE_SCHEMA` for invalid objects |
| `purge-recyclebin` | `PURGE RECYCLEBIN` |
| `sequence-sync` | Sequences behind their tables' ids; `--fix` moves them |
| `schema-diff` | The database against `share/db_schema.dba` (`--file=<dba>`), with the upgrade SQL |
| `indexes` | Index report; `--rebuild [--index=<name>] [--online]`; `--monitor-on`, `--monitor-off`, `--usage` |
| `report` | `--sizes` (tables with index and LOB space), `--top-sql` (from `V$SQL`) |
| `datapump` | `--export=<file.dmp>` and `--import=<file.dmp>` through `DBMS_DATAPUMP` into the directory object of `[DataPumpSettings] Directory` (default `DATA_PUMP_DIR`; the user needs READ and WRITE on it); `--table-exists-action`, `--remap-schema` |
| `ci-indexes` | Linguistic indexes for `OracleCaseInsensitive` (`--create`, `--drop`) |

The server tools work as well: `expdp <user>@<db> schemas=<user> directory=DATA_PUMP_DIR dumpfile=<file.dmp>` and
`impdp` for the restore ([Backups per engine](backups-per-engine.md)).

## Scripts in bin/php

`extension/ezoracle/bin/php/` holds the scripts behind the commands and a few more:

| Script | Use |
|---|---|
| `ora-grant.php` | Prints the grants for a new account ([Setting up Oracle](oracle-setup.md#create-the-account)) |
| `ora-update-seqs.php` | Moves sequences from a login string, like `sequence-sync --fix` |
| `mysql2oracle-schema.php`, `mysql2oracle-data.php` | Move a MySQL site into Oracle; also `ext:ezoracle:mysql2oracle-schema` and `-data` ([Moving a site to another engine](moving-between-engines.md)) |
| `ora-drop-schema.php` | Drops the schema's objects; destructive, read its `--help` first |
| `oracleclusterbinarypurge.php` | Purges expired files of the DFS cluster when the Oracle backend is used ([Clustering](clustering-dfs-and-the-database.md)) |

The extension's `INSTALL` also describes `extension/ezoracle/bin/shell/ora-initialize.sh`, an older automated
initialization path. The current installers (`exp:install --db=oracle`, the wizard, the kickstarter) are the
documented way for a new site.

## Cronjobs

The extension adds cronjob parts in its own `settings/cronjob.ini.append.php`:

| Part | Scripts |
|---|---|
| `ezoracle` | statistics, recycle bin, sequences, health |
| `ezoraclehealth` | health alone |
| `ezoracle_statistics`, `ezoracle_recyclebin`, `ezoracle_sequences` | one job each |

Each job does nothing until it is switched on in `settings/override/ezoracle.ini.append.php`:

```ini
[CronjobSettings]
GatherStatistics=enabled
HealthMonitor=enabled
HealthMailReceivers[]=dba@example.com
PurgeRecycleBin=enabled
SequenceCheck=enabled
SequenceFix=disabled
```

and in the crontab, for example:

```text
15 3 * * *  cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoracle
0 * * * *   cd /path/to/installation && php runcronjobs.php -q -s <admin siteaccess> ezoraclehealth
```

Reports go to `var/log/ezoracle-<job>.log`; the health monitor mails its report to `HealthMailReceivers[]` when a
check warns or fails. Use a team address, not a personal one.

## A routine for an Oracle site

| When | What |
|---|---|
| Hourly | `ezoraclehealth` |
| Nightly | `ezoracle` (statistics, recycle bin, sequence check) |
| After an upgrade | `schema-diff`, `recompile`, `sequence-sync` |
| After an import or a restore | `sequence-sync --fix --dry-run`, then without `--dry-run` |
| Monthly | `report --sizes`, `indexes --usage` |

## Checklist

- [ ] `SELECT_CATALOG_ROLE` granted, so `health` and `report` see everything.
- [ ] Cron switches enabled in the override; both crontab lines installed.
- [ ] `HealthMailReceivers[]` is a team mailbox.
- [ ] Data Pump directory object exists and the account can read and write it.

## Related pages

- [Setting up Oracle with ezoracle](oracle-setup.md)
- [Oracle: persistent connections, DRCP and tuning](oracle-connections-and-tuning.md)
- [Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
