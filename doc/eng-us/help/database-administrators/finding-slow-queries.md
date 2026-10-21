---
title: Finding slow queries
audience: dba
category: database-administration
order: 39
summary: The tools Exponential gives a database administrator to find slow and repeated SQL - SQLOutput and SlowQueriesOutput in the debug output, MySQL's EXPLAIN through QueryAnalysisOutput, DebugTransactions, the SQL profiler log, Oracle's slow query log, exp:benchmark - and how they fit with the engine's own slow query logs.
keywords: slow queries, SQLOutput, SlowQueriesOutput, QueryAnalysisOutput, EXPLAIN, DebugTransactions, debug output, DebugByIP, SQL profiler, sql_profile.on, sql_profile.log, oracle-slow.log, SlowQueryThreshold, exp:benchmark, slow query log, pg_stat_statements, performance
type: help
---
# Finding slow queries

This page is for database administrators who look for the SQL that makes an Exponential page slow. It answers: which
switches show the statements of a request, how to limit them to the slow ones, how to count statements per request
over time, what Oracle adds, and how this fits with your engine's own tools.

## Contents

1. [First: is it the database?](#first-is-it-the-database)
2. [Statements in the debug output](#statements-in-the-debug-output)
3. [EXPLAIN on MySQL and MariaDB](#explain-on-mysql-and-mariadb)
4. [Unbalanced transactions](#unbalanced-transactions)
5. [The SQL profiler](#the-sql-profiler)
6. [Oracle's slow query log](#oracles-slow-query-log)
7. [The engine's own tools](#the-engines-own-tools)
8. [Checklist](#checklist)

## First: is it the database?

Most requests are answered by caches before they reach the database
([Performance and scaling](performance-and-scaling.md)). Measure first:

```bash
php bin/php/console exp:benchmark --help
```

`exp:benchmark` measures page timings over HTTP, compares servers and runs kernel probes, among them the database
round trip; `--save` and `--baseline` catch a slowdown between two runs ([Benchmark](../../features/6.0/benchmark.md)).

## Statements in the debug output

Turn the debug output on for your own address only, never for everyone:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10

[DatabaseSettings]
SQLOutput=enabled
# only statements slower than this many milliseconds (0: all)
SlowQueriesOutput=50
```

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`), restart Velocity if it serves the
site, and load the page: the debug output lists the statements with their times, on every engine
([12.4 Debug output](../../install/12-troubleshooting.md#124-debug-output)). Switch it off again when you are done; the
output shows paths, queries and settings.

## EXPLAIN on MySQL and MariaDB

```ini
[DatabaseSettings]
SQLOutput=enabled
QueryAnalysisOutput=enabled
```

adds MySQL's `EXPLAIN` of each statement to the debug output. It works on MySQL and MariaDB only. On other engines
copy a statement from the debug output and run `EXPLAIN` (PostgreSQL, SQLite: `EXPLAIN QUERY PLAN`) or
`EXPLAIN PLAN FOR` (Oracle) yourself.

## Unbalanced transactions

`[DatabaseSettings] DebugTransactions=enabled` records a stack trace for every begin and commit, to find a transaction
that was opened and never closed. Use it only while you investigate.

## The SQL profiler

`eZDBInterface::profileSQL()` writes one line per request to `var/tmp/sql_profile.log`: statements, `SELECT`s, distinct
`SELECT`s, exact repeats, time in the database and in the repeats. It is off unless `var/tmp/sql_profile.on` exists;
the switch in **Setup > System information** (box **Database queries**, policy `setup/managecache`) creates and removes
that file. In 6.0.15 only the MySQL driver (`eZMySQLiDB`) calls it, so it counts statements on MySQL and MariaDB.

It counts the statements that **reached the database**: with the query cache on, a cached answer is not in it, which
makes it the tool to see what the cache leaves ([The SQL query cache](sql-query-cache.md)). Many exact repeats on one
page point at code that runs the same query in a loop.

## Oracle's slow query log

The `ezoracle` extension writes slow statements to `var/log/oracle-slow.log` when
`ezoracle.ini [LogSettings] SlowQueryThreshold` is set; `MaskLiterals` masks string literals so the log holds no
content. `[TraceSettings]` tags each session in `V$SESSION` with siteaccess, module and view, so AWR and ASH reports
show which page ran a statement ([Oracle: persistent connections, DRCP and tuning](oracle-connections-and-tuning.md)).
`ext:ezoracle:report --top-sql` lists the heaviest statements from `V$SQL`.

## The engine's own tools

The slow query log of MySQL and MariaDB, `pg_stat_statements` and `log_min_duration_statement` on PostgreSQL, the
profiler of MongoDB and Oracle's AWR are features of the servers, not of Exponential. They see every statement of every
process (web, Velocity, cron) without touching the site's settings, and are the right tools for production. Use the
debug output to connect a statement to a page.

## Checklist

- [ ] Measured with `exp:benchmark` before and after a change.
- [ ] Debug output limited to your address and switched off afterwards.
- [ ] The engine's own slow query log enabled with a sensible threshold.
- [ ] Repeated statements reported to whoever maintains the extension or template.

## Related pages

- [Performance and scaling](performance-and-scaling.md)
- [Benchmarking a hosted site](../hosting/performance/benchmarking-a-hosted-site.md)
- [Slow site runbook](../hosting/runbooks/slow-site.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
