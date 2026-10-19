---
title: "Lesson 3.2: SQLite in production"
audience: trainer, hosting, admin
category: training
order: 32
summary: Learners explain WAL mode and the write queue that make SQLite fit an editorial site, monitor a SQLite database with the checks from chapter 9, and know when to choose another engine. 45 minutes.
keywords: hosting and operations, SQLite, WAL, write queue, BEGIN IMMEDIATE, writer-lock, SQLiteTransactionWait, integrity_check, ANALYZE, VACUUM, wal_checkpoint, database is busy
type: help
---
# Lesson 3.2: SQLite in production

SQLite is Exponential's default database and a production database for sites that run on one machine. Hosting staff
need to know how it behaves under load, which files belong to it, how to check it, and the honest limits that send a
site to a database server. This lesson teaches exactly that, from chapter 9.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 3: Databases](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 3.1](choosing-a-database.md); a training installation on SQLite and the `sqlite3` command-line program |

## Learning objectives

After this lesson, learners can:

- explain how WAL lets readers and one writer work side by side, and name the two extra files;
- describe the write queue (the `.writer-lock` file and `BEGIN IMMEDIATE`) and the setting that limits the wait;
- run the monitoring checks of chapter 9: lock timeouts, integrity, journal mode, WAL size, disk space, ownership;
- say when to run `ANALYZE` and `VACUUM`, and why `VACUUM` belongs in maintenance mode;
- name the situations in which another engine is the better choice.

## Trainer's notes

### How to present it

Source: [Chapter 9, section 9.2](../../../../install/09-databases.md#92-sqlite-3-a-production-database-in-one-file)
and [SQLite for hosts](../../../hosting/basics/sqlite-for-hosts.md).

1. **Why it fits** (3 minutes). Most requests of a running site do not touch the database: the cache layers answer
   them first. The driver adds WAL, connection settings for a web server and a write queue.
2. **WAL** (4 minutes). Writes go to `<database>-wal`; readers see a consistent snapshot. Readers never block the
   writer and the writer never blocks readers, but there is one writer at a time. `-wal` and `-shm` belong to the
   database; never delete them while any process has it open. All processes must be on one machine.
3. **The write queue** (5 minutes). Every transaction first takes an exclusive lock on
   `<database file>.writer-lock`, then runs `BEGIN IMMEDIATE`. Writers wait their turn instead of failing. The wait
   is limited by `[DatabaseSettings] SQLiteTransactionWait` (25 s), which must stay below the web server's request
   timeout (Velocity's `requestTimeout` is 30 s). A timeout reads "database is busy: ... nothing was written".
4. **Maintenance** (4 minutes). `ANALYZE` after an install or a large import; `PRAGMA optimize` weekly. `VACUUM` only
   when the file is much larger than its content, in maintenance mode, with twice the database size free on disk.
   A large `-wal` file shrinks with `journal_size_limit` or a `TRUNCATE` checkpoint.
5. **Limits** (4 minutes). Choose a server database when more than one machine must write, when many editors publish
   large changes all day, when you need replication or point-in-time recovery, or database users and grants. Size is
   rarely the reason.

### Common questions

**Can I put the database on NFS for two web servers?** No. WAL needs shared memory on one host.

**"database is busy" appears now and then. What do I do?** Look in `var/log/error.log` and the cron log for what held
the lock: a `VACUUM`, a large import, a script holding a transaction open. Raise the wait only together with the
request timeout.

**Who must own the files?** All processes (web, Velocity workers, cron) need write access; give them one common group
and group-writable files.

**Can I copy the database file for a backup?** Never while it is in use. Lesson 3.4 shows the online backup.

### Pitfalls

- **Deleting `-wal` or `-shm` to "clean up".** This loses committed data. Stop it at once in class if you see it.
- **Running `VACUUM` on a live site.** It holds the write lock for its whole run; publishes queue and may time out.
- **Root-owned database files** after a learner ran `sqlite3` as root. Check `ls -l var/storage/sqlite3/` at the
  end.

## Hands-on exercise

Use your own training installation on SQLite. Run the commands from the installation root; replace `exponential.db`
with the file name `ls` shows.

1. List the database directory: `ls -l var/storage/sqlite3/`. Identify the database, its `-wal` and `-shm` files and,
   if present, the `.writer-lock` file.
2. Check the journal mode:

   ```bash
   sqlite3 var/storage/sqlite3/exponential.db "PRAGMA journal_mode"
   ```

3. Run the fast integrity check: `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA quick_check"`.
4. Count lock timeouts: `grep -c "database is busy" var/log/error.log` (no file or `0` is fine).
5. Check the free disk space: `df -h var/storage/sqlite3`.
6. Read the wait setting:

   ```bash
   php bin/php/console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait
   ```

7. Update the planner statistics: `sqlite3 var/storage/sqlite3/exponential.db "ANALYZE"`.
8. Publish a small change in the administration (for example, edit a folder's name in your course area) to make a
   write, then repeat step 1 and compare the size of the `-wal` file.
9. Run a truncating checkpoint and read its three numbers:
   `sqlite3 var/storage/sqlite3/exponential.db "PRAGMA wal_checkpoint(TRUNCATE)"`.
10. Check that every file in `var/storage/sqlite3/` still has the same owner and group as before.

## Expected result

- Step 2 prints `wal`; step 3 prints `ok`.
- Step 4 prints `0` or reports no file.
- Step 6 prints `25` unless the installation overrides it.
- Step 9 prints three numbers, the first `0` when the checkpoint was not blocked; the `-wal` file is small afterwards.
- Step 10: ownership unchanged (run every command as the site's user, not as root).
- The trainer checks by asking each learner for the `quick_check` result and the `-wal` size before and after step 9.

## Quiz

1. Which two files sit next to a SQLite database in WAL mode and must never be deleted while it is open?
2. What does the driver do before `BEGIN IMMEDIATE`?
3. Why must `SQLiteTransactionWait` stay below the web server's request timeout?
4. When should `VACUUM` run, and how?
5. Name two situations in which a database server is the better choice than SQLite.

<details>
<summary>Answers</summary>

1. `<database>-wal` and `<database>-shm`.
2. It takes an exclusive `flock()` on `<database file>.writer-lock`, so writers queue in turn
   ([Queued writes](../../../../install/09-databases.md#queued-writes-begin-immediate-and-the-writer-lock)).
3. So a waiting request fails with a clear message instead of being killed by the server first.
4. Only when the file is much larger than its content, in maintenance mode, with about twice the database size free.
5. Any two of: more than one machine must write; many editors publish large changes all day; replication or
   point-in-time recovery needed; database users and grants needed.

</details>

## Further reading

- [Chapter 9, SQLite 3](../../../../install/09-databases.md#92-sqlite-3-a-production-database-in-one-file): the full
  explanation.
- [SQLite for hosts](../../../hosting/basics/sqlite-for-hosts.md): the host's summary.
- [SQLite database support](../../../../features/6.0/sqlite-database.md): the feature page.
- [Monitoring a SQLite site](../../../../install/09-databases.md#monitoring-a-sqlite-site): the checks table.

## Related pages

- [Module 3: Databases](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
