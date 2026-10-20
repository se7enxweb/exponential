---
title: "Lesson 3.4: Database backups and moving between engines"
audience: trainer, hosting, admin
category: training
order: 34
summary: Learners back up and check a database with the right tool for each engine, restore a SQLite database through its own locking, and plan a move of a site to another engine with the schema tools. 45 minutes.
keywords: hosting and operations, database backup, sqlite3 .backup, .restore, integrity_check, pg_dump, pg_restore, mariadb-dump, mysqldump, single-transaction, mongodump, datapump, ezsqldumpschema, ezsqlinsertschema, switching engines
type: help
---
# Lesson 3.4: Database backups and moving between engines

A database backup that cannot be restored is not a backup. This lesson teaches the consistent backup command for
each supported engine, how to check a copy, how to restore SQLite safely while the site keeps processes open, and how
a site moves to another engine. Whole-site backups, schedules and restore tests follow in Module 6.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 3: Databases](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 3.3](database-servers.md); a training installation on SQLite and the `sqlite3` program |

## Learning objectives

After this lesson, learners can:

- name the backup and restore command for each of the six supported engines;
- take a consistent SQLite backup while the site runs and check it with `PRAGMA integrity_check`;
- restore a SQLite database with `.restore` inside maintenance mode and clear the caches afterwards;
- explain why `--single-transaction` matters for MariaDB and MySQL dumps;
- outline the steps to move a site between SQL engines with `ezsqldumpschema.php` and `ezsqlinsertschema.php`.

## Trainer's notes

### How to present it

Source: [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md) and
[Chapter 9, section 9.9](../../../../install/09-databases.md#99-switching-an-existing-site-to-another-engine).

1. **The table** (4 minutes). Show *At a glance* from the backups page: `sqlite3 .backup`, `pg_dump -Fc`,
   `mongodump`, `mariadb-dump --single-transaction`, the `ezoracle` extension's `datapump` command or `expdp`,
   `mysqldump --single-transaction`. A database backup is only half of a site backup.
2. **SQLite** (5 minutes). Never copy a database file that is in use. `.backup` uses SQLite's online backup API;
   `VACUUM INTO` writes a compacted copy. Check with `PRAGMA integrity_check`, which prints `ok`. Restore with
   `.restore` in maintenance mode, then clear the kernel caches and Velocity's response cache: they still hold pages
   from before.
3. **Server engines** (4 minutes). `--single-transaction` gives a consistent dump of InnoDB tables without locking
   them. After a PostgreSQL restore into a fresh database, check the sequences. After a MongoDB restore on a new
   server, create the indexes from `bin/mongodb/create_indexes.js`.
4. **Moving engines** (7 minutes). Plan a maintenance window; no editor may publish between the dump and the switch.
   Dump with `ezsqldumpschema.php` into the engine-neutral `.dba` format, load with `ezsqlinsertschema.php`, set
   `DatabaseImplementation` and the connection keys, clear all caches, restart the workers. Count rows of large
   tables on both sides, then publish a test object. Schema updates later follow the engine the site runs on now.

### Common questions

**The restore printed nothing. Did it work?** `.restore` prints nothing when it succeeds. If it reports an error the
live database is unchanged; then stop every process that may hold the file and replace the file with its `-wal` and
`-shm` moved aside together.

**Does PostgreSQL give recovery to a moment between two dumps?** Only with its own replication and point-in-time
recovery, set up from the PostgreSQL documentation.

**Who backs up an Oracle site?** Coordinate with the customer's database administrator; most Oracle sites already have
a backup regime.

**Why do the schema tools show my password?** `--password=` is visible in the process list to other users of the
machine. Run the transfer on a machine you control, or with a temporary password changed afterwards.

### Pitfalls

- **Backups on the same disk only.** The SQLite backup needs local space first, but a copy that never leaves the
  machine is lost with it (Module 6).
- **Forgetting the caches after a restore.** Pages rendered from the old content keep appearing; it looks as if the
  restore failed.
- **A forgotten `DatabaseImplementation`** is the usual reason no page loads after an engine switch.

## Hands-on exercise

Use your own training installation on SQLite. Replace `exponential.db` with your file name. Keep the backup inside
the installation's `var/` for the exercise.

1. Make a backup directory: `mkdir -p var/training-backup`.
2. Take an online backup while the site runs:

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db ".backup 'var/training-backup/exponential-$(date +%F).db'"
   ```

3. Check it: `sqlite3 var/training-backup/exponential-<date>.db "PRAGMA integrity_check"`.
4. In the administration, rename one folder in your course area (for example add ` (changed)` to its name) and
   publish.
5. Switch maintenance mode on: `php bin/php/maintenance.php on --message="Restoring"`.
6. Restore the backup:

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db ".restore 'var/training-backup/exponential-<date>.db'"
   ```

7. Switch maintenance mode off: `php bin/php/maintenance.php off`.
8. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-all
   php bin/php/console exp:velocity cache clear
   ```

9. Open the folder in the administration and check its name.
10. Read the help of the schema dump tool and note the `--type` values it lists:
    `php bin/php/ezsqldumpschema.php --help`.

## Expected result

- Step 3 prints `ok`.
- While maintenance mode was on, the site showed the maintenance page.
- Step 9: the folder has its name from before step 4; the change is gone because the backup was older.
- The learner can name the types the schema tools accept and knows that `settings/dbschema.ini` lists more than the
  `--help` output does.
- The trainer checks one learner's folder name before and after the restore on the projector.

## Quiz

1. Why must you never copy a SQLite database file that is in use?
2. Which option makes a MariaDB or MySQL dump consistent without locking InnoDB tables?
   A. `--lock-all-tables` B. `--single-transaction` C. `--quick` D. `--no-data`
3. After restoring a database, the site still shows the old pages. What was forgotten?
4. What must be created after restoring MongoDB onto a new server?
5. Name the two scripts that move a site between SQL engines.

<details>
<summary>Answers</summary>

1. A copy taken during a write, or without its `-wal` file, can be inconsistent; use `.backup` or `VACUUM INTO`.
2. **B.** It gives a consistent dump of InnoDB tables without locking them.
3. Clearing the caches: the kernel caches and Velocity's response cache still held pages from before.
4. The indexes from `bin/mongodb/create_indexes.js`
   ([Database backups per engine](../../../hosting/operations/database-backups-per-engine.md#3-mongodb)).
5. `bin/php/ezsqldumpschema.php` and `bin/php/ezsqlinsertschema.php`.

</details>

## Further reading

- [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md): every command.
- [Changing the database engine](../../../hosting/migrations/changing-the-database-engine.md): the host's procedure.
- [Chapter 9, SQLite backup and restore](../../../../install/09-databases.md#sqlite-backup-and-restore): the details.
- [Database support, Backup and restore](../../../database-support.md#backup-and-restore): the overview.

## Related pages

- [Module 3: Databases](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
