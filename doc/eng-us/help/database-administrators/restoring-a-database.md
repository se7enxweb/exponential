---
title: Restoring a database
audience: dba
category: database-administration
order: 29
summary: The procedure for restoring the database of an Exponential site - maintenance mode, stopping Velocity, the restore command per engine, SQLite's .restore against replacing the file, the caches to clear afterwards (including the SQL query cache), sequences on PostgreSQL and Oracle, and the checks before reopening - plus how to test restores.
keywords: restore, database restore, recovery, sqlite3 .restore, pg_restore, mongorestore, mysql restore, impdp, datapump import, maintenance mode, Velocity stop, clear caches, querycache, sequences, restore test, disaster recovery
type: help
---
# Restoring a database

This page is for database administrators who restore an Exponential database, after a failure, a bad upgrade or for a
restore test. It answers: in which order to stop, restore and restart, which command restores each engine, what must
be cleared afterwards and what to check before the site reopens. Sources:
[10.9 Restore](../../install/10-after-installing.md#restore) and
[SQLite backup and restore](../../install/09-databases.md#sqlite-backup-and-restore).

## Contents

1. [Before you start](#before-you-start)
2. [The procedure](#the-procedure)
3. [The restore command per engine](#the-restore-command-per-engine)
4. [SQLite: .restore or replacing the file](#sqlite-restore-or-replacing-the-file)
5. [After the restore](#after-the-restore)
6. [Checks before reopening](#checks-before-reopening)
7. [Testing restores](#testing-restores)
8. [Checklist](#checklist)

## Before you start

- Know which backup you restore and that the files backup (`var/storage`, `settings/override`) is from the same
  moment.
- Keep a copy of the current, broken database: you may need something from it later.
- Tell the editors: anything published after the backup was taken is lost.

## The procedure

1. **Maintenance on**, so nobody writes:

   ```bash
   php bin/php/maintenance.php on --message="Restoring" --allow-root-user
   ```

2. **Under Velocity, stop the server**, so no worker holds the old database:

   ```bash
   php bin/php/console exp:velocity stop --allow-root-user
   ```

3. **Restore the database** (next section) and unpack the files over the installation; fix the owner and mode.
4. **Clear every cache and regenerate the autoloads**:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. **Start Velocity, clear its response cache**, switch maintenance off, open the front page and the administration:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --apply --allow-root-user
   php bin/php/maintenance.php off --allow-root-user
   ```

## The restore command per engine

| Engine | Restore |
|---|---|
| SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/exponential.db'"` |
| PostgreSQL | `pg_restore -U exponential -d exponential --clean exponential.dump` |
| MongoDB | `mongorestore --uri="mongodb://exponential@localhost:27017/exponential" --drop /backup/exponential` |
| MariaDB | `mariadb -u exponential -p exponential < exponential.sql` |
| Oracle | `php bin/php/console ext:ezoracle:datapump --import=db.dmp`, or Oracle's `impdp` |
| MySQL | `mysql -u exponential -p exponential < exponential.sql` |

## SQLite: .restore or replacing the file

Processes keep the database open: PHP-FPM workers while they run a request, Velocity workers for their whole life.
Restore through SQLite's own locking rather than by replacing the file under them. `.restore` copies the backup into
the live database page by page under a write lock and prints nothing when it succeeds.

If it reports an error (a writer held the lock, or the backup has another page size, which a database in WAL mode
cannot take over), the live database is unchanged. Then replace the file: stop every process that may have it open
(Velocity, PHP-FPM, cron), move the old database **and** its `-wal` and `-shm` files aside together, put the backup in
place, fix its owner and mode ([SQLite files, ownership and permissions](sqlite-files-ownership-and-permissions.md)),
and start the processes again.

## After the restore

- **Caches**: they still hold pages rendered from the content before the restore. `ezcache.php --clear-all` clears
  the kernel's caches, including the SQL query cache; to clear only that one after a write outside Exponential,
  `php bin/php/ezcache.php --clear-id=querycache --allow-root-user` ([The SQL query cache](sql-query-cache.md)).
- **Sequences**: a full `pg_restore` restores them with the data; after restoring selected tables, correct them
  ([Operating PostgreSQL](postgresql-operations.md#correcting-sequences)). On Oracle,
  `ext:ezoracle:sequence-sync --dry-run` reports sequences behind their tables.
- **Search index**: if the search index tables were not in the backup, or after restoring into another engine, rebuild
  it with `php bin/php/updatesearchindex.php --clean --allow-root-user` ([The built-in search index tables](search-index-tables.md)).
- **MongoDB**: run `bin/mongodb/create_indexes.js` if the indexes were not restored.

## Checks before reopening

- The version row matches the code:
  `SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';`
- The row counts of `ezcontentobject`, `ezcontentobject_tree` and `ezcontentobject_attribute` look as expected.
- Sign in to the administration, open a few pages, publish a test object (a publish exercises the writes a page view
  does not), and delete it.
- `var/log/error.log` shows nothing new.

## Testing restores

Test a restore on another machine at least once and after every major upgrade. A practical drill:

1. Take last night's backup to a staging machine.
2. Restore it with this procedure; time each step.
3. Run the checks above.
4. Write down the time it took and what was missing; that is your real recovery time.

Runbooks for hosting staff: [Restore and restore tests](../hosting/operations/restore-and-restore-tests.md) and
[Restore from backup](../hosting/runbooks/restore-from-backup.md).

## Checklist

- [ ] Maintenance on; Velocity stopped.
- [ ] Database and files restored from the same moment.
- [ ] All caches cleared, Velocity's response cache too.
- [ ] Sequences, search index and MongoDB indexes checked.
- [ ] Version row, row counts, sign-in and a test publish checked.
- [ ] Maintenance off.

## Related pages

- [Backups per engine](backups-per-engine.md)
- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
