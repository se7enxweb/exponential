# Specification: SQLite and Oracle driver behaviour (October 2026)

Changes to the database drivers made on 2026-10-01 and 2026-10-02. Upgrade notes:
[doc/bc/6.0/sqlite-transactions.md](../../bc/6.0/sqlite-transactions.md).

## SQLite transactions

| Aspect | Behaviour since 6.0.15 |
|---|---|
| Start | `BEGIN IMMEDIATE`: the write lock is taken at the start, where the busy timeout applies (a deferred `BEGIN` made a transaction fail at its first write once another connection had committed since it read: "database is locked") |
| Waiting | the start waits up to `[DatabaseSettings] SQLiteTransactionWait` seconds, queued on a lock file next to the database that the operating system releases when a process ends; tries thin out as the wait grows; writers that waited longest go first |
| Failure | a start that still gets nothing is reported at once as a failed transaction with the reason, nothing written; before, the generic `begin()` counted it as started and later statements ran one by one, each committed on its own |
| Rollback | `ROLLBACK` is sent only when SQLite has a transaction open (no more "cannot rollback - no transaction is active") |
| Measured | 64 concurrent publishers: before 6 of 192 failed; after 192 of 192 complete and whole |

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `DatabaseSettings` | `SQLiteTransactionWait` | `60` (seconds; keep it below the web server's request timeout) | installation |

Check the setting: `./console exp:ini get site.ini/DatabaseSettings/SQLiteTransactionWait --allow-root-user` (the shipped value is in `settings/site.ini`, read in `lib/ezdb/classes/ezsqlite3db.php`).

Other SQLite driver fixes: `subString( s, n )` without a length returns the rest of the string, as on the other
engines (it stopped one character short, so every move dropped the last character of `path_string` and
`path_identification_string` of the moved subtree); a node's `path_identification_string` is rewritten from the value
stored for it, not from a stale copy held by the node object; the asynchronous publisher closes its database
connection before it forks for each publish (a connection must never cross a fork; an inherited MySQL connection
closed in the child ended the parent's too); the setup wizard's database field offers only real SQLite files
(`eZSQLite3DB::availableDatabasesIn()`).

(The 64-publisher measurement is from the change's test run and was not repeated for this page.)

## Oracle and the SQL query cache

The SQL query cache ([sql-query-cache.md](../../bc/6.0/sql-query-cache.md)) knows Oracle's statements, so the Oracle
driver (ezoracle 2.3.2 when this was written; `extension/ezoracle/ezinfo.php` says 2.3.3 at HEAD) can answer SELECTs from it:

- Oracle writes volatile values without parentheses, so `<sequence>.NEXTVAL` and `.CURRVAL` (the driver reads a
  new row's id with `SELECT <sequence>.currval FROM DUAL`), `SYSDATE`, `SYSTIMESTAMP`, the SCN, `SYS_GUID`,
  `SYS_CONTEXT`/`USERENV` and `DBMS_RANDOM` are never cached (a cached answer would hand out an id twice).
- Oracle's catalogue (`USER_*`, `ALL_*`, `DBA_*`, `CDB_*`, `V$*`) counts as a system table.
- An anonymous PL/SQL block (`DECLARE ... BEGIN ... END;`) is a write whose tables cannot be read and makes every
  result stale; a bare `BEGIN` is still the start of a transaction.
- The cache directory is left to the site user (a root process used to leave it owned by root so the site user's
  cron could not write `state.ser`), and statements with long string literals are read correctly.

Also on Oracle (PHP 8.5 notices fixed by reading every admin page's debug report): a preference without a name is
neither stored nor read; `createGroupedDataMap()` reads an empty class attribute category as a string (Oracle returns
`''` as NULL); `ezpExtension::getInfo`, `eZTemplateCompiler`, `eZHTTPTool::redirect()`, the INI setting datatype,
the `ristring` operator and `{if}` without a file placement no longer pass null where PHP 8.5 deprecates it.

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [SQLite3 database driver](sqlite3-database-driver.md), [SQL query cache](../../bc/6.0/sql-query-cache.md).
