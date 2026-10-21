---
title: "SQLite maintenance: ANALYZE, VACUUM and the WAL file"
audience: dba
category: database-administration
order: 10
summary: The routine maintenance of an Exponential SQLite database - statistics with ANALYZE and PRAGMA optimize, compaction with VACUUM in maintenance mode, keeping the WAL file in check with journal_size_limit and a TRUNCATE checkpoint, and integrity checks - with the commands and their expected output.
keywords: SQLite, maintenance, ANALYZE, PRAGMA optimize, VACUUM, VACUUM INTO, WAL, wal_checkpoint, TRUNCATE, journal_size_limit, integrity_check, quick_check, journal_mode, disk space, cron
type: help
---
# SQLite maintenance: ANALYZE, VACUUM and the WAL file

This page is for database administrators who look after an Exponential site on SQLite. It answers: which maintenance
SQLite needs, when to run each task, how to run it without disturbing editors, and what healthy output looks like.
Source: [ANALYZE, VACUUM and the WAL file](../../install/09-databases.md#analyze-vacuum-and-the-wal-file) and
[Monitoring a SQLite site](../../install/09-databases.md#monitoring-a-sqlite-site). The `sqlite3` command-line program
is optional for the site but needed for these tasks (`sqlite3 --version`).

## Contents

1. [Statistics: ANALYZE](#statistics-analyze)
2. [Compaction: VACUUM](#compaction-vacuum)
3. [The WAL file](#the-wal-file)
4. [Integrity checks](#integrity-checks)
5. [A maintenance schedule](#a-maintenance-schedule)
6. [Checklist](#checklist)

The examples use `var/storage/sqlite3/exponential.db`; use the file your `Database` setting names.

## Statistics: ANALYZE

SQLite's query planner chooses indexes better with statistics. Run `ANALYZE` once after an install or a large
import, and again after the content has grown a lot:

```bash
sqlite3 var/storage/sqlite3/exponential.db "ANALYZE"
```

A site that is slow right after an install or an import usually lacks them
([12.10](../../install/12-troubleshooting.md#1210-databases)). `PRAGMA optimize` runs only the analysis that is
needed and is cheap enough for a weekly cron entry:

```bash
sqlite3 var/storage/sqlite3/exponential.db "PRAGMA optimize"
```

## Compaction: VACUUM

Deleted content leaves free pages in the file. SQLite reuses them but does not give them back to the file system.
`VACUUM` rebuilds the file compactly. It needs about twice the database's size in free disk space and holds the write
lock for its whole run, so writers queue behind it and may time out after `SQLiteTransactionWait`. Run it in
maintenance mode, and only when the file has become much larger than its content (after removing a large part of
the site):

```bash
php bin/php/maintenance.php on --allow-root-user
sqlite3 var/storage/sqlite3/exponential.db "VACUUM"
php bin/php/maintenance.php off --allow-root-user
```

`VACUUM INTO '<file>'` writes a compacted copy instead and is a good backup method
([Backups per engine](backups-per-engine.md)).

## The WAL file

After a checkpoint SQLite reuses the WAL file from its start but does not shrink it. A burst of heavy writing can
leave a large `-wal` file behind; in the project's load tests it grew well over a hundred megabytes and was
checkpointed back afterwards. Two remedies:

- In `settings/override/site.ini.append.php`, `SQLitePragmas[]=journal_size_limit=67108864` truncates the WAL to
  64 MB after each checkpoint that empties it.
- A manual checkpoint that truncates the file:

  ```bash
  sqlite3 var/storage/sqlite3/exponential.db "PRAGMA wal_checkpoint(TRUNCATE)"
  ```

  It prints three numbers; a first number of `0` means the checkpoint was not blocked.

Never delete the `-wal` or `-shm` file while any process has the database open: they are part of the database.

## Integrity checks

| Check | Command | Healthy |
|---|---|---|
| Quick | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA quick_check"` | `ok` |
| Thorough | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA integrity_check"` | `ok` |
| Journal mode | `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA journal_mode"` | `wal` |
| Disk space | `df -h var/storage/sqlite3` | room for the database twice over |

Run the same integrity check on every backup before you rely on it.

## A maintenance schedule

| When | Task |
|---|---|
| After install, large import, engine move | `ANALYZE` |
| Weekly (cron, off-peak) | `PRAGMA optimize`; `PRAGMA quick_check`; check the `-wal` size |
| After removing a large part of the site | `VACUUM` in maintenance mode |
| After a burst of writes left a large WAL | `PRAGMA wal_checkpoint(TRUNCATE)`, or set `journal_size_limit` |
| Daily | online backup and `PRAGMA integrity_check` of the backup |

Run cron entries with the same group and a umask of `0002` as the site, so files they create stay group-writable
([SQLite files, ownership and permissions](sqlite-files-ownership-and-permissions.md)). `exp:benchmark kernel`
measures the database round trip among its probes; with `--save` and `--baseline` it catches a slowdown between two
runs ([Benchmark](../../features/6.0/benchmark.md)).

## Checklist

- [ ] `ANALYZE` was run after the install and after large imports.
- [ ] A weekly `PRAGMA optimize` and `quick_check` are scheduled.
- [ ] `VACUUM` runs only in maintenance mode, with enough free disk.
- [ ] The `-wal` file does not keep growing after busy periods.

## Related pages

- [SQLite 3 for database administrators](sqlite-for-database-administrators.md)
- [SQLite: the write queue and locking](sqlite-write-queue-and-locking.md)
- [Monitoring the database](monitoring-the-database.md)
- [Maintenance mode](../../guides/maintenance-mode.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
