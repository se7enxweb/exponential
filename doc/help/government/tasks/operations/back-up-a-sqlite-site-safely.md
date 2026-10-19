---
title: Back up a SQLite site safely
audience: government, admin
category: public-sector
order: 50
summary: Step by step - back up an Exponential site that runs on SQLite 3 without stopping it, with the sqlite3 .backup command instead of a file copy, and schedule it.
keywords: SQLite, backup, sqlite3, .backup, WAL, database file, var/storage/sqlite3, online backup, cron, small sites
type: help
---
# Back up a SQLite site safely

This task is for administrators of sites on SQLite 3, the default database for a site served from one machine. At
the end you have a consistent copy of the database. A plain file copy of a database in use can be inconsistent; the
`.backup` command of the `sqlite3` tool is safe ([Operating a site, section 6](../../../../guides/operating-a-site.md#6-back-up-and-restore)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Schedule it](#schedule-it)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- The `sqlite3` command-line tool on the server.
- The database file name: `ls var/storage/sqlite3/` (`exponential.db` is the name `exp:install` uses by default).
- A target directory outside the installation, ideally on another volume.

## Steps

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/example/site-$(date +%F).db'"
tar czf /backup/example/files-$(date +%F).tgz var/storage settings/override settings/siteaccess
```

The database is several files (`.db`, `-wal`, `-shm`, `.writer-lock`); `.backup` reads it through SQLite and writes one
consistent file, so you do not copy the others.

## Schedule it

A nightly crontab line as the site's user (adapt paths):

```
30 2 * * * cd /path/to/installation && sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/example/site-nightly.db'"
```

Rotate the copies by your retention rule ([Backup schedules and retention](../../operations/backups-and-recovery/backup-schedules-and-retention.md)).

## Check it worked

```bash
sqlite3 /backup/example/site-nightly.db "PRAGMA integrity_check;"     # ok
```

Then restore it on a test server now and then ([Restore last night's backup to a test server](restore-last-nights-backup-to-a-test-server.md)).

## Related pages
- [Take a backup before a change](take-a-backup-before-a-change.md)
- [SQLite for hosts](../../../hosting/basics/sqlite-for-hosts.md)
- [SQLite database support](../../../../features/6.0/sqlite-database.md)
- [Backups per database engine](../../operations/backups-and-recovery/backups-per-database-engine.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
