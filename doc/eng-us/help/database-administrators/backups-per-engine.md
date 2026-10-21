---
title: Backups per engine
audience: dba
category: database-administration
order: 28
summary: How to back up the database of an Exponential site on each engine (sqlite3 .backup or VACUUM INTO, pg_dump, mongodump, mariadb-dump and mysqldump with --single-transaction, Data Pump for Oracle), what else belongs in a complete backup, when to use maintenance mode, and how to verify a backup.
keywords: backup, database backup, sqlite3 .backup, VACUUM INTO, integrity_check, pg_dump, mongodump, mysqldump, mariadb-dump, --single-transaction, expdp, datapump, ext:ezoracle:datapump, maintenance mode, var/storage, settings/override, retention, portable dump, dba
type: help
---
# Backups per engine

This page is for database administrators who set up or review the backups of an Exponential site. It answers: which
command backs up each engine consistently, what must be backed up with the database, when maintenance mode is needed,
and how to check a backup. Sources: [Database support, backup and restore](../database-support.md#backup-and-restore)
and [10.9 Backups and restore](../../install/10-after-installing.md#109-backups-and-restore).

## Contents

1. [A complete backup](#a-complete-backup)
2. [The database, per engine](#the-database-per-engine)
3. [Consistency and maintenance mode](#consistency-and-maintenance-mode)
4. [A portable second format](#a-portable-second-format)
5. [Verifying a backup](#verifying-a-backup)
6. [Schedules and retention](#schedules-and-retention)
7. [Checklist](#checklist)

## A complete backup

A complete backup is the database **and** the files, taken at the same moment:

| Path | Why |
|---|---|
| the database | content, users, settings stored in tables |
| `var/storage` and `var/<site>/storage` | uploaded images and files |
| `settings/override`, `settings/siteaccess` | the installation's settings, with the database password |
| your own extensions and designs | if they are not in a repository |
| `var/storage/sqlite3/` | the database, on SQLite (taken with SQLite's tools, below) |

Caches (`var/cache`, `var/<site>/cache`) need no backup; they are regenerated. A database backup alone does not
restore a site: content refers to files in `var/storage`.

## The database, per engine

| Engine | Backup | Note |
|---|---|---|
| SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".backup '/backup/exponential.db'"` | online and consistent while the site runs; or `"VACUUM INTO '/backup/exponential.db'"` (SQLite 3.27+) for a compacted copy. **Never copy a database file that is in use** |
| PostgreSQL | `pg_dump -U exponential -Fc exponential > exponential.dump` | the custom format restores selectively |
| MongoDB | `mongodump --uri="mongodb://exponential@localhost:27017/exponential" --out=/backup/exponential` | the tool asks for the password |
| MariaDB | `mariadb-dump --single-transaction -u exponential -p exponential > exponential.sql` | `mysqldump` is the older name |
| Oracle | `php bin/php/console ext:ezoracle:datapump --export=db.dmp` | or Oracle's `expdp` |
| MySQL | `mysqldump --single-transaction -u exponential -p exponential > exponential.sql` | |

`--single-transaction` gives a consistent dump of InnoDB tables without locking them. `-p` without a value makes the
client ask for the password; never write it on the command line, prefer the tools' prompts or option files.
Point-in-time recovery (PostgreSQL WAL archiving, MySQL binary logs, MongoDB oplog, Oracle RMAN) is the engine's own
feature and works with Exponential as with any application; configure it on the server.

## Consistency and maintenance mode

On SQLite and on MySQL or MariaDB with InnoDB the database backup is consistent without maintenance mode. Maintenance
mode makes the database and the files agree, because nothing is uploaded between the two:

```bash
php bin/php/maintenance.php on --message="Back soon" --until=30m --allow-root-user
# database backup, then:
tar czf /backup/files-$(date +%F).tar.gz var/storage settings/override settings/siteaccess
php bin/php/maintenance.php off --allow-root-user
```

`--allow-admin` keeps the administration reachable, `--allow-ip` lets given addresses through
([Maintenance mode](../../guides/maintenance-mode.md)). For a nightly backup of a busy site, many operators accept a
few minutes of difference between files and database instead; then take the database first and the files right after.

## A portable second format

Next to the engine's native dump, a `.dba` dump is independent of the engine and can be loaded into another SQL
engine:

```bash
php bin/php/ezsqldumpschema.php --type=mysql --user=exponential --password='<password>' \
    --format=generic --output-array --output-types=all exponential var/backup/site.dba --allow-root-user
```

It is slower and larger than a native dump and takes the password on the command line
([Schema tools](schema-tools-dump-insert-diff.md)). It is not available for MongoDB.

## Verifying a backup

| Engine | Check |
|---|---|
| SQLite | `sqlite3 /backup/exponential.db "PRAGMA integrity_check"` prints `ok` |
| PostgreSQL | `pg_restore --list exponential.dump` lists the objects without error |
| MySQL, MariaDB | the dump ends with `-- Dump completed`; the file is not empty |
| MongoDB | `mongorestore --dryRun` on the directory |
| Oracle | the Data Pump log reports success |

The real check is a restore on another machine ([Restoring a database](restoring-a-database.md)). A backup that was
never restored is a hope, not a backup.

## Schedules and retention

Keep backups away from the server and keep several generations. Backups hold e-mail addresses and password hashes:
encrypt them and delete old generations on schedule. Schedules for hosting companies are on
[Backup schedules and retention](../hosting/operations/backup-schedules-and-retention.md).

## Checklist

- [ ] The database command of your engine runs nightly and is consistent.
- [ ] `var/storage`, `settings/override` and `settings/siteaccess` are backed up with it.
- [ ] SQLite is backed up with `.backup` or `VACUUM INTO`, never with `cp`.
- [ ] No password on a command line or in a crontab.
- [ ] Backups are verified, encrypted and kept off the server.
- [ ] A restore was tested this quarter.

## Related pages

- [Restoring a database](restoring-a-database.md)
- [Database backups per engine](../hosting/operations/database-backups-per-engine.md) (for hosting companies)
- [Operating a site, back up and restore](../../guides/operating-a-site.md#6-back-up-and-restore)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
