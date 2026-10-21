---
title: Monitoring the database
audience: dba
category: database-administration
order: 42
summary: What to monitor on the database of an Exponential site and where to read it - the Database card of Setup > System information, exp:system:info for scripts and alerts, the log files and the patterns that matter, the queue tables, per-engine health checks (SQLite PRAGMAs, Oracle's health cronjob) and the engine's own monitoring.
keywords: monitoring, health check, System information, exp:system:info, --json, --sizes, exit code, error.log, warning.log, exp:logs, database is busy, Database implementation not supported, MONGO TODO, could not set the connection charset, ezoraclehealth, quick_check, uptime, alerting
type: help
---
# Monitoring the database

This page is for database administrators who set up monitoring and alerting for the database of an Exponential site.
It answers: what the application itself reports about its database, how to read it from a script, which log lines
deserve an alert, and which checks belong to the engine. The application's view and the server's view complement each
other: the first tells you whether the site can use the database, the second how the server is doing.

## Contents

1. [Setup > System information](#setup--system-information)
2. [From a script: exp:system:info](#from-a-script-expsysteminfo)
3. [Log files and patterns](#log-files-and-patterns)
4. [Queues](#queues)
5. [Per engine](#per-engine)
6. [Checklist](#checklist)

## Setup > System information

The page needs the `setup/system_info` policy and only reads ([System information](../../guides/system-information.md)):

- **Database card**: engine and server version, driver class, the file (SQLite) or server and database name, character
  set, number of tables, size (SQLite always; MySQL and PostgreSQL with **Measure sizes**).
- **Database connection** part: type, server, socket, database name, character set, read replica.
- **Database queries** box: the SQL query cache mode, hit rate and the SQL profiler switch.
- **Health checks** first, failures and warnings before notes; a warning when the connection character set is not
  UTF-8.
- **Download report** (text or JSON) for a support request; passwords and the database user are never in it.

Open it through each server that serves the site (Apache with PHP-FPM, Velocity's port): each has its own PHP and may
report differently.

## From a script: exp:system:info

```bash
./console exp:system:info --allow-root-user            # the text report
./console exp:system:info --json --allow-root-user     # JSON for your monitoring system
./console exp:system:info --sizes --allow-root-user    # with directory and database sizes
```

It reports the command-line PHP and **exits 1 when a check fails**, so it can feed an alert or guard a deploy.
`exp:expfetch` (the system card) shows version, engine and database on one screen with hints.

## Log files and patterns

The logs are in `var/log/` (`error.log`; `warning.log` only with `[DebugSettings] AlwaysLog[]=warning`). `exp:logs`
lists, follows and searches them ([Log viewer and rotation](../hosting/operations/log-viewer-and-rotation.md)).

| Pattern | Meaning | Alert |
|---|---|---|
| `Database implementation not supported` | mistyped `DatabaseImplementation`; every page fails | immediately |
| `eZDBNoConnectionException`, "database cannot be reached" | wrong host, credentials, or the server is down | immediately |
| `database is busy: the transaction could not start` | SQLite write queue timed out ([Write queue](sqlite-write-queue-and-locking.md)) | when repeated |
| `attempt to write a readonly database` | SQLite file ownership ([Permissions](sqlite-files-ownership-and-permissions.md)) | immediately |
| `could not set the connection charset` | MySQL connection charset fell back | at setup |
| `MONGO TODO` | SQL the MongoDB adapter could not translate | review daily |
| `ORA-` | an Oracle error | by number |

```bash
grep -c "database is busy" var/log/error.log
grep -rn "MONGO TODO" var/log/ | tail
```

## Queues

| Check | Healthy |
|---|---|
| `SELECT action, COUNT(*), MIN(created) FROM ezpending_actions GROUP BY action;` | small counts, recent `MIN(created)` |
| expired rows in `ezsession` (database sessions) | few |
| **Setup > Cronjobs**, recent runs | no issues, last run recent |

See [Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md).

## Per engine

| Engine | From Exponential | From the engine |
|---|---|---|
| SQLite | `PRAGMA quick_check`, `PRAGMA journal_mode` (`wal`), size of `-wal`, disk space, file ownership ([SQLite maintenance](sqlite-maintenance.md#integrity-checks)) | |
| PostgreSQL | System information | connections, locks, replication lag, autovacuum (`pg_stat_*` views) |
| MySQL, MariaDB | System information; replica in use | connections, InnoDB status, replica lag |
| MongoDB | `MONGO TODO` count | server status, replica set health |
| Oracle | `ext:ezoracle:health`, the hourly `ezoraclehealth` cronjob, which mails `HealthMailReceivers[]` on warnings ([Oracle maintenance commands](oracle-maintenance-commands.md)) | AWR, OEM |

Also monitor the backups themselves: their age, size and the last restore test ([Backups per engine](backups-per-engine.md)).

## Checklist

- [ ] `exp:system:info --json` polled; exit code 1 alerts.
- [ ] Alerts on the immediate log patterns above.
- [ ] Queue tables checked daily.
- [ ] Engine-side monitoring for connections, locks, replication and disk.
- [ ] Backup age monitored.

## Related pages

- [Monitoring overview](../hosting/operations/monitoring-overview.md), [Health checks and uptime](../hosting/operations/health-checks-and-uptime.md)
- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [System information](../../guides/system-information.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
