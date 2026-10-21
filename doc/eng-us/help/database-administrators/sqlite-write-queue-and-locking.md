---
title: "SQLite: the write queue and locking"
audience: dba
category: database-administration
order: 9
summary: How the SQLite driver of Exponential 6.0.15 queues concurrent writers with a lock file and BEGIN IMMEDIATE, how long a transaction waits (SQLiteTransactionWait) and how that relates to Velocity's and PHP-FPM's request timeouts, what a timeout looks like, and what to look for when timeouts repeat.
keywords: SQLite, locking, write queue, writer-lock, BEGIN IMMEDIATE, database is locked, database is busy, SQLiteTransactionWait, busy_timeout, SQLITE_BUSY, requestTimeout, request_terminate_timeout, flock, concurrency, publishing
type: help
---
# SQLite: the write queue and locking

This page is for database administrators who run Exponential on SQLite and need to understand what happens when two
editors publish at the same moment. It answers: why plain SQLite transactions fail with "database is locked", what
the driver does instead since 6.0.15, how long a write waits, what a timeout looks like and how to find its cause.
Sources: [Queued writes](../../install/09-databases.md#queued-writes-begin-immediate-and-the-writer-lock) and
[SQLite: transactions queue for the write lock](../../bc/6.0/sqlite-transactions.md).

## Contents

1. [The problem](#the-problem)
2. [What the driver does](#what-the-driver-does)
3. [How long a transaction waits](#how-long-a-transaction-waits)
4. [What a timeout looks like](#what-a-timeout-looks-like)
5. [When timeouts repeat](#when-timeouts-repeat)
6. [Forked processes and other users](#forked-processes-and-other-users)
7. [Measured behavior](#measured-behavior)
8. [Checklist](#checklist)

## The problem

A plain `BEGIN` in SQLite is deferred: the transaction reads first and asks for the write lock only at its first
write. If another connection committed in between, the snapshot it read is stale, and SQLite answers `SQLITE_BUSY`
immediately, without waiting, because waiting cannot make the snapshot current. Before 6.0.15 a publish could fail
with "database is locked" however long the busy timeout was.

## What the driver does

`eZSQLite3DB::beginQuery()` starts every transaction like this:

1. It opens, once per connection and again in every forked process, a lock file next to the database:
   `<database file>.writer-lock`, for example `var/storage/sqlite3/exponential.db.writer-lock`.
2. It takes an exclusive `flock()` on that file, retrying every 1 to 15 ms; writers that have waited more than a
   second try more often, which serves them roughly in arrival order.
3. With the lock held it runs `BEGIN IMMEDIATE`, which takes SQLite's write lock at the start, where the busy
   timeout applies. Writers outside the queue (a single statement, another program) are waited for by SQLite's busy
   handler within the time left.
4. At `COMMIT` or `ROLLBACK` it releases the lock file. If the process dies, the operating system releases it, so a
   crash never blocks the queue.

Waiting at the start is safe: before `BEGIN IMMEDIATE` succeeds the transaction has written nothing, and afterwards
nothing inside it can lose a lock race.

Every process that writes the database uses the same `.writer-lock`: PHP-FPM workers, Velocity workers, cronjobs and
console scripts queue against each other.

## How long a transaction waits

Up to `[DatabaseSettings] SQLiteTransactionWait` seconds (default `25`; values below 1 are raised to 1). Keep it below
the web server's request timeout, so a waiting request fails with a clear message instead of being killed:

| Server | Request timeout | Note |
|---|---|---|
| Velocity | `requestTimeout` (`Q.webserver.requestTimeout`), 30 s unless changed | the client gets a 504 when it is exceeded |
| PHP-FPM | the pool's `request_terminate_timeout` (off unless set) | `max_execution_time` does not count waiting time on Linux |

Releases before 6.0.15 shipped `60`; if your override still sets `60`, lower it to `25`. To wait longer, raise the
request timeout and `SQLiteTransactionWait` together, the wait always a few seconds below the timeout.

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait --allow-root-user
```

## What a timeout looks like

In `var/log/error.log` and on the error page:

```text
database is busy: the transaction could not start within 25 s, another write held the lock all that time; nothing was written
```

Any other failure at the start reads `the transaction could not start: <SQLite's message>; nothing was written`. In
both cases nothing was written; retry the action.

## When timeouts repeat

One write held the lock for the whole wait. Look at what was running at that time in `var/log/error.log` and in the
cron log:

- a `VACUUM` (it holds the write lock for its whole run);
- a large import or a script that opened a transaction and then waited for something else;
- a `sqlite3` shell left open inside `BEGIN`.

Count the timeouts with `grep -c "database is busy" var/log/error.log`. Raising `SQLiteTransactionWait` helps only when
the holder was legitimately long. If the queue is regularly longer than the wait during normal editing, a server
database with row-level locking serves the editors better ([Choosing an engine](choosing-an-engine.md)).

## Forked processes and other users

`flock()` belongs to an open file, so a forked process must not reuse its parent's handle. The driver remembers the
process id that opened the lock file and opens a new one in each fork; Velocity workers and the asynchronous
publisher's children queue correctly. The driver opens `.writer-lock` for writing and, failing that, read-only:
`flock()` works on a read-only handle, so a lock file created by another user still queues correctly.

## Measured behavior

From the change's own tests on the development server: 192 publishes from 64 processes at once all completed, where
the earlier start lost 6 of them; 48 transactions of 300 ms each, started together, finished in 14.7 seconds, about
the 14.4 seconds they take one after another. The queue costs almost nothing beyond the waiting itself.

## Checklist

- [ ] `SQLiteTransactionWait` is below Velocity's `requestTimeout` and PHP-FPM's `request_terminate_timeout`.
- [ ] No override still sets the old value `60`.
- [ ] `VACUUM` and large imports run in maintenance mode.
- [ ] `grep -c "database is busy" var/log/error.log` is 0 or explained.

## Related pages

- [SQLite 3 for database administrators](sqlite-for-database-administrators.md)
- [SQLite maintenance](sqlite-maintenance.md)
- [Troubleshooting database errors](troubleshooting-database-errors.md)
- [Velocity workers and pools](../hosting/servers/velocity-workers-and-pools.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
