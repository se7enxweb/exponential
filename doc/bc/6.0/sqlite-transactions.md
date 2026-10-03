# SQLite: transactions queue for the write lock

Applies to installations on SQLite (the default of a quick local install). Introduced 2026-10-01 and completed
2026-10-02. Technical details: [database drivers specification](../../specifications/6.0/database-drivers-sqlite-oracle.md).

## What changed

- Transactions start with `BEGIN IMMEDIATE`. Publishing and editing next to other writers (asynchronous
  publishing, content jobs, cronjobs) no longer fail with "database is locked".
- A transaction start waits for the writers ahead of it, up to `[DatabaseSettings] SQLiteTransactionWait` seconds
  (default 60), instead of failing after SQLite's 5 s busy timeout. A start that gets nothing fails at once with the
  reason and writes nothing: either the whole transaction commits or nothing does.
- `subString()` without a length returns the rest of the string; a node move on SQLite no longer drops the last
  character of the moved subtree's `path_string` and `path_identification_string`.

## Check

```bash
grep -n "SQLiteTransactionWait" settings/site.ini
```

A subtree moved on SQLite before this fix had every descendant's `path_string` and `path_identification_string`
cut by one character. If you moved subtrees on SQLite with an earlier version, open a few of the moved nodes by
their URL alias; one that does not resolve was affected and needs the subtree moved again.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `DatabaseSettings` | `SQLiteTransactionWait` | `60` | installation |

Keep it below the web server's request timeout. Override in `settings/override/site.ini.append.php`
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user` afterwards).

## No action needed

MySQL, PostgreSQL, Oracle and MongoDB installations are not affected.
