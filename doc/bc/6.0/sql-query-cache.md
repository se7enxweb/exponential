# The SQL query cache

The rows a `SELECT` returned, answered again without asking the database until
a write to one of the tables it read makes them stale. For the SQL engines:
MySQL/MariaDB (`eZMySQLiDB`), PostgreSQL (`eZPostgreSQLDB`), SQLite
(`eZSQLite3DB`) and Oracle (`eZOracleDB` of the ezoracle extension, from its
query cache hooks on). The MongoDB driver does not use it and is not affected.

On Oracle, besides what is never cached anywhere, a statement that reads a
sequence (`<sequence>.NEXTVAL`, `.CURRVAL`: the driver reads every new row's id
that way), the clock (`SYSDATE`, `SYSTIMESTAMP`), the SCN, `SYS_GUID`,
`SYS_CONTEXT`/`USERENV` or `DBMS_RANDOM`, and anything that reads the catalogue
(`USER_*`, `ALL_*`, `DBA_*`, `V$*`), is always run. An anonymous PL/SQL block
(`DECLARE ...`, `BEGIN ... END;`) counts as a write whose tables cannot be read:
it makes every result stale.

Off by default: `settings/querycache.ini`, `Mode=off`.

It is the counterpart of the HTTP cache (`doc/bc/6.0/httpcache.md`). The HTTP
cache removes whole renders for the pages it can store; the query cache speeds
up **everything it cannot**: the first render after a purge, pages with query
strings, POST responses, the administration interface, signed-in pages with a
private permission context, cronjobs and scripts.

---

## What it gives (measured on alpha, 2026-09-27)

Every page runs the same SQL, word for word, on every request: across two
requests for the front page, 405 of its 405 distinct statements had the same
text, and a quarter (26 %) even repeat inside one request. In `shared` mode a
warm page sends **no statement at all** to the database.

### Plain Apache (php-fpm), rendered pages

The same pages back to back with `Mode=off` and `Mode=shared`, on
`https://alpha.se7enx.com/`. A query string keeps the HTTP cache out, so every
request is a full render. `ab -c 8`, 80 requests anonymous and 40 signed in;
"alone" is one request with nothing else running (median of 5). Signed in is an
administrator session.

| Page | Visitor | Off: pages/s | Shared: pages/s | Gain | Off: alone | Shared: alone | SQL off → shared |
|---|---|---|---|---|---|---|---|
| `/` (front page, layouts) | anonymous | 9.4–13.3 | 41.4–43.0 | **3.2–4.4×** | 485–608 ms | 164–177 ms | 545 → 0 |
| `/fitness` (section) | anonymous | 30.4–30.7 | 64.9–76.9 | **2.1–2.5×** | 294–299 ms | 101–104 ms | 273 → 0 |
| `/workout` (section) | anonymous | 28.5–28.8 | 60.3–68.9 | **2.1–2.4×** | 263–303 ms | 94–117 ms | 269 → 0 |
| `/` | signed in | 2.05 | 8.17 | **4.0×** | 582 ms | 191 ms | 544 → 0 |
| `/fitness` | signed in | 4.23 | 16.98 | **4.0×** | 263 ms | 111 ms | 272 → 0 |
| `/workout` | signed in | 4.60 | 18.61 | **4.0×** | 288 ms | 98 ms | 268 → 0 |

In short: a rendered page takes a **third of the time** (front page ~0.6 s →
~0.17 s alone), and the server renders **2–4 times as many pages per second**.
The database no longer works for these pages at all, which frees the MariaDB
CPU that bounded full renders on this machine (0.2–0.3 s per page).

Notes on reading the table:

- The ranges are two runs (2026-09-27, 02:3x and 02:4x); the signed-in rows one.
- Signed in, eight concurrent requests share **one** session, and PHP's
  session lock runs them one after another: p50 is about eight times "alone"
  (off 1.9 s, shared 0.45 s on `/fitness`). Different users do not wait for
  each other; "alone" is what one signed-in visitor feels.
- What is left of a render is templates and PHP. For the pages the HTTP cache
  can store, a hit (380–520 pages/s on Apache) is still far faster; the query
  cache is what makes the pages it cannot store faster: the first render after
  a purge, query strings, POSTs, private contexts, the administration interface,
  scripts.
- A cold cache (the first request after a clear or a write to the tables a
  page reads) runs every statement as with `Mode=off`, plus the lookups and
  stores; its overhead has not been measured separately.

### Correctness, checked on alpha

- the rendered HTML is byte-identical with the cache off, cold and warm;
- a write from a CLI script is seen by the web servers at once (the next
  front-page render re-ran the 125 statements that read the written table,
  and the one after that ran none);
- a transaction's writes count at `COMMIT` and are forgotten on `ROLLBACK`;
- publishing an object in the administration interface shows the new title
  in the next read;
- temporary tables (the shop's related-purchase list, search) are never cached.

---

## Settings: `settings/querycache.ini`

Override in `settings/override/querycache.ini.append.php` (or a siteaccess).

```ini
[QueryCacheSettings]
Mode=off          # off | request | shared
MaxAge=300        # longest life of a result, in seconds
MaxRows=5000      # results with more rows are not kept
ExcludeTables[]
ExcludeTables[]=ezsession
ExcludeTables[]=ezpending_actions
ExcludeTables[]=ezcollab_notification_rule
```

| Mode | What is kept | Where |
|---|---|---|
| `off` | nothing; the hooks return at once | — |
| `request` | a memo for one request: an identical `SELECT` is not run twice in it | PHP memory |
| `shared` | the memo, and results across requests | the memo, then APCu of the server process group |

Choosing:

- `request` is safe everywhere and needs nothing: it removes the in-request
  repeats (about a quarter of the statements on a layouts page).
- `shared` needs APCu (`apc.enabled=1`; for scripts also `apc.enable_cli=1`).
  Without APCu it works as `request`. This is the mode that removes the
  statements between requests.
- `MaxAge` bounds what the cache cannot see: a write made outside Exponential
  (another program, a SQL client, a restore). Within Exponential every write
  invalidates at once, whatever `MaxAge` is.
- `ExcludeTables` are tables whose reads are never cached — per-visitor or
  constantly written tables where a cached answer would never be reused.

After changing the file: clear the INI cache (`php bin/php/ezcache.php
--clear-tag=ini --allow-root-user`), reload PHP-FPM and restart Velocity
(`./console exp:velocity restart`).

---

## How it works

Class `eZDBQueryCache`, `lib/ezdb/classes/ezdbquerycache.php`. Each SQL driver
calls it in three places (a lookup lands on the stored entry first and uses the
tables stored with it; the statement's text is parsed only on a miss, since the
parsing was about a third of a rendered page's CPU):

- `arrayQuery()` asks `lookup()` before running a statement, and gets the rows
  or a ticket;
- `arrayQuery()` hands the rows to `store()` after a statement that returned;
- `query()` tells `noteWrite()` of every statement that succeeded.

`eZDBInterface::commit()` and `rollback()` call `afterCommit()` and
`afterRollback()`. `ezpKernelWeb` calls `resetRequest()` at the start of every
request, so a persistent worker (Velocity, FrankenPHP worker mode) starts each
one with an empty memo.

### Validity

A shared state file, `var/<site>/cache/querycache/state.ser`, holds

```
{ generation, genTime, tables: { table => time of its last write } }
```

Every process reads and writes it: Apache/php-fpm, Velocity, scripts and
cronjobs each have an APCu of their own, so the state cannot live there. It is
written under `flock` and replaced atomically; a process reads it again when
its copy is older than one second.

An entry is kept with the time its query **started**, the tables it read and
its rows. It is answered only while

- it was created after `genTime` (the last full clear),
- every table it read was last written before it was created, and
- it is younger than `MaxAge`.

A read that raced a write is therefore always stale, never trusted.

### What is never cached

- reads inside a transaction (they may see the transaction's own writes);
- statements whose tables cannot be read from the text (subqueries in `FROM`,
  anything that is not a plain `SELECT`);
- statements whose result is not a function of their tables: `NOW()`,
  `UNIX_TIMESTAMP()` with no argument, `CURRENT_DATE` and friends, `RAND()`,
  `UUID()`, `LAST_INSERT_ID()`, `FOUND_ROWS()`, locks (`FOR UPDATE`,
  `LOCK IN SHARE MODE`, `GET_LOCK()`), variables (`@x`, `@@x`), `SQL_NO_CACHE`,
  `INTO OUTFILE`;
- reads of temporary tables: those `CREATE TEMPORARY TABLE` made in this
  process, and those named like `eZDBInterface::generateUniqueTempTableName()`
  names them (`ezsearch_tmp_12`, `ezproductcoll_tmp_972`, `eznode_count_3`).
  A temporary table belongs to one connection and its name is reused, so
  another request's rows must never answer for it. Writes to temporary tables
  invalidate nothing and stay out of the state file;
- results with more than `MaxRows` rows;
- reads of the `ExcludeTables`.

### Writes

`INSERT`, `REPLACE`, `UPDATE` (multi-table too), `DELETE` (with `JOIN`s),
`TRUNCATE`, `CREATE`/`ALTER`/`DROP TABLE`, `CREATE`/`DROP INDEX` and
`RENAME TABLE` mark the tables they name as written now. A write whose tables
cannot be read from the text marks **everything** stale (a generation bump),
which is always safe. Inside a transaction the tables are collected and
marked at the outermost `COMMIT`; on `ROLLBACK` they are dropped.

### Keys

`md5` of the driver class, database name, server, the statement text and
`arrayQuery()`'s parameters (offset, limit, column). In APCu the entries are
`ezqc:<key>`, with `MaxAge` as their TTL, so APCu can evict them under memory
pressure. The counters are `ezqcstat:*`.

---

## In the administration interface

### Setup > Caches

Two rows at the top, with the other most-used buttons:

- **Clear query cache** (`ClearQueryCacheButton`) — a generation bump: every
  stored result is stale at once, on every server sharing `var/`, without
  walking APCu. The row shows the mode.
- **Clear HTTP cache** (`ClearHttpCacheButton`) — drops every cached page for
  every permission context, including Velocity's copies. Greyed out when the
  HTTP cache is off.

The query cache is also in the cache list below (`querycache`, tags `content`
and `ini`), so **Clear all caches**, **Clear content caches**, `ezcache.php
--clear-all`, `--clear-tag=content` and `--clear-id=querycache` clear it too.

### Setup > System information

The **Database queries** box shows, for the server answering the page:

- the mode, `MaxAge`, `MaxRows`, `ExcludeTables`;
- in `shared` mode, how many results this server holds in APCu and how much
  memory they take;
- the generation, when the cache was last cleared, and how many tables the
  state file tracks;
- this server's counters since they were last reset: requests, hits, misses,
  hit rate, statements that could not be cached, writes;
- the tables written most recently, and how long ago;
- **Clear the query cache** and **Reset the counters**.

Counters and APCu entries are **per server**: Apache/php-fpm and Velocity each
have their own APCu, so the numbers differ between `https://alpha…/admin` and
`https://alpha…:8080/admin`. The state (generation, tables) is shared.

Below it the SQL profile (next section) and its switch.

---

## The SQL profiler

`eZDBInterface::profileSQL()`, called by the MySQL driver after every
statement. Off unless `var/tmp/sql_profile.on` exists — one `file_exists()` per
request. The switch in Setup > System information creates and removes that
file. On, one line per request goes to `var/tmp/sql_profile.log`: statements,
`SELECT`s, distinct `SELECT`s, exact repeats, time in the database and in the
repeats.

It counts the statements that **reached the database**: with the query cache
on, a cached answer is not in it. That makes it the tool to see what the cache
leaves: a warm shared-cache page shows 0 statements.

Two further sentinels, for studies: `var/tmp/sql_profile.shapes` adds the most
repeated statements (values masked) to the log, and `var/tmp/sql_profile.hashes`
writes each request's statement hashes to their own file, to compare two
requests. Remove them when done; they write on every request.

---

## Support and maintenance

### Switching it on

1. Check APCu: Setup > System information, PHP section, or `php -m | grep apcu`
   (and the FPM pool's `php_admin_value[apc.enabled]`).
2. Set `Mode=request` in `settings/override/querycache.ini.append.php`; clear
   the INI cache, reload FPM, restart Velocity. Browse for a day.
3. Set `Mode=shared` the same way. Watch the hit rate in Setup > System
   information.

### Switching it off

`Mode=off`, then the same INI clear, reload and restart. Nothing needs
deleting: stale APCu entries expire within `MaxAge` and are never read while
the mode is off.

### What is normal

- A hit rate of 30–40 % right after a clear or restart, rising as pages are
  visited. On a quiet site with warm pages, most statements are hits.
- A burst of writes after a cache clear or Velocity restart: the warm-up
  renders pages, and some renders write (image aliases created on first view
  update `ezcontentobject_attribute`).
- `state.ser` a few KB; it lists every table ever written, one timestamp each.

### What to look at

| Symptom | Cause and fix |
|---|---|
| A page shows old data after a change made with a SQL client, `mysql < dump.sql` or another program | The cache cannot see writes outside Exponential. Clear it (Setup > Caches, or `php bin/php/ezcache.php --clear-id=querycache --allow-root-user`), or wait `MaxAge`. Do this after every restore. |
| `Class "eZDBQueryCache" not found` in `var/vc/qbix/log/error.log`, admin pages 500 on Velocity only | Velocity loaded the kernel autoload array before the class existed. Restart it: `./console exp:velocity restart --allow-root-user`. Any update that adds kernel classes needs this. |
| The hit rate stays low | A table the pages read is written on every request. Setup > System information lists the tables written last; find who writes (usually a view counter, a session-like table or a broken image alias regenerated on every view) and fix it, or add the table to `ExcludeTables`. |
| "APCu is not available to this server" in the box | `shared` falls back to `request`. Enable APCu for that PHP (FPM pool, or `apc.enable_cli=1` for Velocity and scripts). |
| APCu memory full, other caches evicted | Lower `MaxRows`, lower `MaxAge`, or raise `apc.shm_size`. Setup > System information shows what the query cache holds. |
| `state.ser` not writable (warnings in the log) | `var/<site>/cache/querycache/` must be writable by every user the site runs as. Files written by root are handed to the directory's owner. |
| A result looks wrong and you suspect the cache | Set `Mode=off` and compare. If the output is the same, it is not the cache. |

### Tests

**Unit tests, no database:** `tests/tests/lib/ezdb/eZDBQueryCacheTest.php`
(`@group querycache`, in the `lib` suite). The driver is a stand-in object and
the state lives in its own directory under `var/tmp` (`eZDBQueryCache::setStateDir()`),
so the tests never touch the site's cache.

| | What it proves |
|---|---|
| QC-01 | The tables a `SELECT` reads (joins, aliases, `db.table`, quoted names, table names inside strings ignored); not cacheable: not a plain `SELECT`, volatile functions, locks, `SQL_CALC_FOUND_ROWS`, a subquery in `FROM`, variables |
| QC-02 | The tables a write names, for every verb (`INSERT`, `INSERT IGNORE`, `REPLACE`, `UPDATE` multi-table, `DELETE` with `JOIN`, `TRUNCATE`, `CREATE`/`ALTER`/`DROP TABLE`, `CREATE INDEX`, `RENAME TABLE`); an unreadable write is null |
| QC-03 | A miss, a store, then a hit with the same rows, and the counters |
| QC-04 | A write to a table the result read makes it stale; a write to another table does not |
| QC-05 | An unreadable write and a clear make everything stale; a clear raises the generation |
| QC-06 | In a transaction reads are neither answered nor stored; writes count at `COMMIT` and are forgotten on `ROLLBACK` |
| QC-07 | Temporary tables, by name and by `CREATE TEMPORARY TABLE`: never cached, never in the state |
| QC-08 | `ExcludeTables`, `MaxRows`, `MaxAge` |
| QC-09 | Keys differ by statement, `arrayQuery()` parameters, database and server |
| QC-10 | A stored result is a copy: changing what a caller got changes nothing |
| QC-11 | `Mode=off` stores nothing, writes no state and counts nothing |
| QC-12 | A write by another process, seen through the state file, makes a result stale within a second, even one still in the request's memo; other tables stay current |
| QC-13 | A stored result is answered without parsing the statement again, but not once a table it read has been excluded |

```bash
php vendor/bin/phpunit --testsuite lib --filter eZDBQueryCacheTest
# OK (13 tests, 95 assertions), about 2 s (QC-08 and QC-12 wait a second each)
```

**On a running site** (MySQL, the real drivers), each checked on alpha
2026-09-27:

- the driver hooks, on the live database: temporary tables never cached and
  their writes kept out of the state, a normal table cached;
- transactions through `eZDB::begin()`/`commit()`/`rollback()`;
- a CLI write invalidating what the web servers hold;
- the rendered page byte-identical with the cache off, cold and warm;
- the timing table above: the same pages, `Mode=off` then `Mode=shared`,
  anonymous and signed in, with the statements per page from the SQL profiler.

To repeat the timing on another installation: switch the profiler on (Setup >
System information), and for each mode set it in
`settings/override/querycache.ini.append.php`, clear the INI cache and reload
PHP-FPM, request each page three times to warm it, then time it with
`ab -n 80 -c 8 'https://<host>/<page>?bench=1'` (the query string keeps the HTTP
cache out). The profiler's line for a probe request gives the statements; no
line means none reached the database.

### Files

| File | What it is |
|---|---|
| `lib/ezdb/classes/ezdbquerycache.php` | the cache |
| `lib/ezdb/classes/ezmysqlidb.php`, `ezpostgresqldb.php`, `ezsqlite3db.php` | the driver hooks |
| `lib/ezdb/classes/ezdbinterface.php` | `COMMIT`/`ROLLBACK` hooks, the SQL profiler |
| `kernel/private/classes/ezpkernelweb.php` | `resetRequest()` per request |
| `kernel/classes/ezcache.php` | the `querycache` entry in the cache list |
| `kernel/setup/cache.php`, `design/admin/templates/setup/cache.tpl` | Setup > Caches |
| `kernel/setup/info.php`, `design/admin/templates/setup/info.tpl` | Setup > System information |
| `settings/querycache.ini` | settings |
| `var/<site>/cache/querycache/state.ser` | the shared state |

See also (September 2026): [Behaviour changes, 16 to 30 September 2026](behaviour-changes-2026-09b.md#sql-query-cache-querycacheini) (lookup before parsing, `xxh128` keys, catalogue never cached), [Velocity](../../features/6.0/velocity-persistent-worker-server.md).

## See also (16 to 30 September 2026)

- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Behaviour changes, 16 to 30 September 2026](behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Cache clears that move directories aside](../../features/6.0/cache-clear-rename-aside.md)
- [Velocity response cache](../../features/6.0/velocity-response-cache.md)
