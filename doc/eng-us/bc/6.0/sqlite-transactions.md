# SQLite: transactions queue for the write lock

Read this page if your installation runs on SQLite (the default of a quick local install). It explains what
changed in how SQLite transactions start, whether an earlier subtree move damaged your data, and the one setting
you can tune. The change was introduced on 2026-10-01 and completed on 2026-10-02. MySQL, PostgreSQL, Oracle and
MongoDB installations are not affected.

## In short

| | |
|---|---|
| What changed | Transactions start with `BEGIN IMMEDIATE` and wait for the write lock instead of failing with "database is locked". A node move no longer cuts one character off the moved paths. |
| Who is affected | SQLite installations only. |
| How to check | `grep -n "SQLiteTransactionWait" settings/site.ini`, then open a few nodes you moved on SQLite earlier. |
| How to fix | Nothing to change for new work. Move a damaged subtree again. |

## What changed

- **Transactions start with `BEGIN IMMEDIATE`.** Publishing and editing next to other writers (asynchronous
  publishing, content jobs, cronjobs) no longer fail with "database is locked".
- **A transaction start waits its turn.** It waits for the writers ahead of it for up to
  `[DatabaseSettings] SQLiteTransactionWait` seconds (default 25), instead of failing after SQLite's 5 second busy
  timeout. A start that never gets the lock fails at once with the reason and writes nothing: either the whole
  transaction commits or nothing does.
- **`subString()` without a length returns the rest of the string.** Before, a node move on SQLite dropped the last
  character of the moved subtree's `path_string` and `path_identification_string`.

## Waiting on the command line and for single statements (5 November 2026)

- **Cronjobs and scripts wait longer.** On the command line a transaction start waits
  `[DatabaseSettings] SQLiteCLITransactionWait` seconds (default 300), not the 25 s of a web request. Before, a
  cronjob (`runcronjobs.php frequent`, `global`) behind an import's long write stopped with "A database transaction
  in Exponential failed".
- **A single write outside a transaction queues like a transaction.** It takes its turn on `<database>.writer-lock`
  and waits up to `SQLiteBusyTimeout` ms in a web request (default 5000) or `SQLiteCLIBusyTimeout` ms on the command
  line (default 30000). Before, it lost every race against a loop of short transactions and failed with "database
  is locked": an external link saved in the online editor could lose its URL id while an import ran.
- **A lock SQLite reports at once is tried again** within the same wait, with a short growing pause, for reads,
  single writes and the start of a transaction.
- **The error says what happened**: "database is busy: the statement waited 5.0 s for another write to end (limit
  5 s, [DatabaseSettings] SQLiteBusyTimeout) and was not run; nothing was written".
- `SQLitePragmas[]=busy_timeout=<ms>`, where an installation set it, still wins over the two new settings.

## How to check

1. Confirm the setting is present:

   ```bash
   grep -n "SQLiteTransactionWait" settings/site.ini
   ```

   Expected output:

   ```
   81:SQLiteTransactionWait=25
   ```

2. If you moved subtrees on SQLite with an earlier version, open a few of the moved nodes by their URL alias. Every
   descendant of such a subtree had its `path_string` and `path_identification_string` cut by one character. A
   node whose URL alias does not resolve was affected.

## How to fix

- **Damaged subtree:** move the subtree again. The move rebuilds the paths with the corrected `subString()`.
- **Long-running writers:** if a transaction start still gives up, raise the wait. Keep it below the web server's
  request timeout (Velocity: `Q.webserver.requestTimeout`, 30 s unless changed; PHP-FPM: `request_terminate_timeout`) and raise both together.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `DatabaseSettings` | `SQLiteTransactionWait` | `25` (seconds) | installation |
| `settings/site.ini` | `DatabaseSettings` | `SQLiteCLITransactionWait` | `300` (seconds) | command line |
| `settings/site.ini` | `DatabaseSettings` | `SQLiteBusyTimeout` | `5000` (ms) | web requests |
| `settings/site.ini` | `DatabaseSettings` | `SQLiteCLIBusyTimeout` | `30000` (ms) | command line |

Override it in `settings/override/site.ini.append.php`, then clear the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

## Related pages

- [Database support](../../help/database-support.md): the supported databases in their official order, SQLite first
- [Specification: database drivers (SQLite and Oracle)](../../specifications/6.0/database-drivers-sqlite-oracle.md)
- [SQLite database support](../../features/6.0/sqlite-database.md)
- [Specification: the SQLite3 database driver](../../specifications/6.0/sqlite3-database-driver.md)
- [SQLite for Exponential Platform: no database server needed](../../features/6.0/platform-sqlite-install.md)
- [Platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md)
- History: [January 2024, 1 to 15](../../history/2024/2024-01a.md), [January 2024, 16 to 31](../../history/2024/2024-01b.md),
  [April 2026](../../history/2026/2026-04.md), [June 2026, 16 to 30](../../history/2026/2026-06b.md)
