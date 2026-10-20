---
title: Backups per database engine
audience: government, hosting, admin
category: public-sector
order: 30
summary: For database and operations teams in public bodies. The backup and restore command of each database Exponential supports, in the official order, how to check a copy, and how to fit the engine's backup into the tooling your database team already runs.
keywords: database backup, SQLite .backup, pg_dump, pg_restore, mongodump, mariadb-dump, mysqldump, expdp, datapump, ezoracle, integrity check, restore, database team, enterprise backup
type: help
---
# Backups per database engine

This page is for database administrators and operations teams in public bodies who back up the database half of an
Exponential site. It gives the backup and restore command of each supported engine, how to check the copy, and how
the engine's tools fit into an organization's existing backup system. The complete hosting page is
[Database backups per engine](../../../hosting/operations/database-backups-per-engine.md).

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time.

## Contents

1. [At a glance](#at-a-glance)
2. [SQLite needs care](#sqlite-needs-care)
3. [Server engines](#server-engines)
4. [Fitting your backup system](#fitting-your-backup-system)
5. [Passwords on the command line](#passwords-on-the-command-line)
6. [Checklist](#checklist)

## At a glance

| Database | Backup | Restore |
|---|---|---|
| 1. SQLite 3 | `sqlite3 <db file> ".backup '/backup/exponential.db'"` | `sqlite3 <db file> ".restore '/backup/exponential.db'"` |
| 2. PostgreSQL | `pg_dump -U USER -Fc DATABASE > site.dump` | `pg_restore -U USER -d DATABASE --clean site.dump` |
| 3. MongoDB | `mongodump --uri="mongodb://USER@HOST:27017/DATABASE" --out=/backup/site` | `mongorestore --uri="..." --drop /backup/site` |
| 4. MariaDB | `mariadb-dump --single-transaction -u USER -p DATABASE > site.sql` | `mariadb -u USER -p DATABASE < site.sql` |
| 5. Oracle | `php bin/php/console ext:ezoracle:datapump` (see its `--help`), or Oracle's `expdp` | Oracle's `impdp` |
| 6. MySQL | `mysqldump --single-transaction -u USER -p DATABASE > site.sql` | `mysql -u USER -p DATABASE < site.sql` |

Find which engine a site uses with `grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php`.

## SQLite needs care

The whole database is one file under `var/storage/sqlite3/` (`exponential.db` is the default name; `ls
var/storage/sqlite3/` shows yours), with `-wal` and `-shm` files next to it. "Never copy a database file that is in
use" ([9.2 SQLite backup and restore](../../../../install/09-databases.md#sqlite-backup-and-restore)). Use the online
backup and check the copy:

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential-$(date +%F).db'"
sqlite3 /backup/exponential-<date>.db "PRAGMA integrity_check"     # prints ok
```

A file-level backup agent that copies `var/` while the site runs does not make a usable SQLite backup on its own.
Run the `.backup` command first, into a directory the agent then copies. Keep free disk for "the database twice over".

## Server engines

- **PostgreSQL**: `pg_dump` takes a consistent snapshot while the site runs; PostgreSQL has replication and
  point-in-time recovery of its own if your recovery point needs more than nightly dumps. After a restore into a fresh
  database, check the sequences named `<table>_<column>_seq` ([9.4](../../../../install/09-databases.md#sequences)).
- **MariaDB and MySQL**: `--single-transaction` gives a consistent dump of InnoDB tables without locking them; restore
  into a database created in `utf8mb4`.
- **MongoDB**: after a restore into a new server, create the indexes from `bin/mongodb/create_indexes.js`.
- **Oracle**: `ext:ezoracle:datapump` or your DBA's Data Pump jobs.

## Fitting your backup system

Most public bodies have an enterprise backup system and a database team with its own procedures. Practical points:

| Point | Advice |
|---|---|
| Who owns the database backup | the database team for server engines; the application team for SQLite, which has no server |
| Same moment as the files | schedule the dump and the file backup in the same window ([What to back up](what-to-back-up.md)) |
| Retention | agree it with records management ([Backup schedules and retention](backup-schedules-and-retention.md)) |
| Proof | a restore test per engine, not only a successful job log ([Restore testing](restore-testing.md)) |
| Before risky changes | an extra dump before upgrades, engine changes and restores |

## Passwords on the command line

`-p` makes the MySQL and MariaDB clients ask for the password; never write the password itself on the command line,
where it lands in the shell history and the process list ([Operating a site](../../../../guides/operating-a-site.md)).
For scheduled jobs use the client's option file or your secrets store, readable by the backup user only.

## Checklist

- [ ] The backup command of the site's engine is scheduled and owned by a named team.
- [ ] SQLite is backed up with `.backup`, never by copying the live file.
- [ ] The copy is checked (`PRAGMA integrity_check`, a test restore).
- [ ] No password appears on a command line or in a crontab.
- [ ] The dump and the file backup are taken in the same window.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Choosing a database in the official order](../hosting/database-choice-in-the-official-order.md)
- [Database support, backup and restore](../../../database-support.md#backup-and-restore)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
