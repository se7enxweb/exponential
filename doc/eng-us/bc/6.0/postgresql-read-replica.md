# Read replicas: PostgreSQL, MySQL and MariaDB, Oracle (6.0.15)

Read this page if you want a site's pages to read from a replica close to the web server, for example a hot standby
on the same machine as one node of a cluster, while every write still goes to the primary. The same rule applies to
PostgreSQL, MySQL and MariaDB, and Oracle (the ezoracle extension). SQLite has no replica (see below).

## In short

| | |
|---|---|
| What changed | `eZPostgreSQLDB` honours `[DatabaseSettings] UseSlaveServer` and the `SlaveServerArray[]` keys (before, it sent every statement to `Server`). `eZMySQLiDB` already did, but sent every `SELECT` outside a transaction to the replica, so a request could read its own edit back stale; it now follows the same safe rule. ezoracle's `eZOracleDB` reads from a read-only standby with the same rule. |
| Who is affected | Nobody until `UseSlaveServer=enabled` is set. The shipped `settings/site.ini` has `UseSlaveServer=disabled`. **MySQL and MariaDB sites that already use `UseSlaveServer=enabled` read the replica less often from 6.0.15 on, and never stale: see [MySQL and MariaDB](#mysql-and-mariadb).** |
| What to do | Set the replica in `settings/override/site.ini.append.php` (per engine below), clear the INI cache and deploy. |

## Which statements read from the replica

A statement runs on the replica only when **all** of these hold. Anything else runs on the primary, as before.

1. It is a plain `SELECT`, which writes nothing and takes no lock. Not `WITH` (a data-modifying CTE writes), no
   `SELECT ... INTO`, no `FOR UPDATE` / `FOR SHARE` (MySQL: `LOCK IN SHARE MODE`), no sequence function, no
   advisory or named lock. Each driver names its own dialect's forms (below). The test reads the text, so a literal
   that merely contains one of these words also goes to the primary: a round trip, never a wrong answer.
2. No transaction is open.
3. The request has written nothing yet. The first `INSERT`, `UPDATE`, `DELETE`, `BEGIN`, `LOCK`, `SET` or any other
   statement that is not such a `SELECT` pins the rest of the request to the primary, so a request reads what it wrote.
4. The request is a web request (PHP-FPM, Apache or a Velocity worker) with the method `GET` or `HEAD`. Cronjobs,
   scripts and console commands always use the primary, and so does every `POST`.
5. The visitor is not signed in (no `is_logged_in` cookie). Editors always read the primary, so they see their own
   work at once.
6. The replica is current. Each engine checks this differently (below), with the first statement of the request that
   could use the replica. If the replica does not answer, or is behind, the whole request reads the primary.

Any error on the replica (no connection, a statement cancelled by a recovery conflict, a server that went away) runs
the same statement on the primary, without an error to the caller, and turns the replica off for the rest of the
request. The connection is opened on first use, with a 2 s timeout and no retries. A node where nothing listens on the
replica's address simply reads the primary, so one set of settings can serve every node of a cluster when only some
nodes have a local replica.

The decision and the pin are per request: `eZDBInterface::resetReplicaRequest()` is the reset hook `dbreplica` of
`expKernelServiceScope` and runs at the start of every request for every driver, also in a persistent Velocity worker.
An explicit `eZDBInterface::SERVER_MASTER` always reads the primary; `SERVER_SLAVE` (passed by the search and by
permission checks with temporary tables) is only a wish, and the rule above decides.

The DFS cluster backend (`file.ini`, `config.cluster.php`) has its own connection and keeps reading the primary.

## Settings for every engine

```ini
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=127.0.0.1
SlaveServerPort[]
SlaveServerPort[]=5413
SlaverServerUser[]
SlaverServerUser[]=<site login>
SlaverServerPassword[]
SlaverServerPassword[]=<its password>
SlaverServerDatabase[]
SlaverServerDatabase[]=<site database>
```

With more than one `SlaveServerArray[]` entry each request picks one at random. For MySQL, MariaDB and Oracle these
settings say how current the replica must be (PostgreSQL uses synchronous apply instead, see below):

| Setting | Default | Meaning |
|---|---|---|
| `SlaveServerStatusCheck` | `enabled` | Ask the replica for its replication status. `disabled` trusts the replica without asking (for a login that may not read the status). |
| `SlaveServerMaxLag` | `0` | The most seconds the replica may be behind its primary to be read. |
| `SlaveServerCatchUp` | `enabled` | MySQL/MariaDB: the replica must have applied the primary's GTID position of the start of the request. Oracle: `STANDBY_MAX_DATA_DELAY` on the standby session. |
| `SlaveServerCatchUpWait` | `100` | MariaDB: how long (ms) the first statement waits for the replica to reach that position before the request reads the primary. |

Then:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

## PostgreSQL

Driver-specific statements kept on the primary: `nextval()`, `currval()`, `setval()`, `lastval()`, `pg_advisory*`,
`txid_current*`, `pg_current_*`, `pg_notify()`, `set_config()`, large-object and dblink functions.

**Current:** the first statement of the request asks the replica `pg_is_in_recovery()` and
`pg_stat_wal_receiver.status`. A standby whose stream is broken is not read. A server that is not in recovery (a
primary) is accepted. The site's database login needs `pg_read_all_stats` to see the receiver's status; without it the
replica is never used.

A physical standby carries the primary's logins and passwords, so the site's own login works on it.

### Visitors after an edit: synchronous apply

Rules 3 and 5 cover the editor's own request and the editor's later pages. An anonymous visitor who asks for the page
a moment after an editor published it is served by another request, perhaps on another node. With asynchronous
replication that request can still see the old version on the replica, and the view cache would keep it.

To close that window make the primary wait until the standby has applied each commit of the site's database:

```sql
-- on the primary
ALTER SYSTEM SET synchronous_commit = 'local';   -- only the site's database waits, not every database
ALTER SYSTEM SET synchronous_standby_names = '<standby application_name>';
SELECT pg_reload_conf();
ALTER DATABASE <site database> SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO <site login>;
```

A commit then returns only after the standby shows it. The cost is one network round trip per commit. If the standby
goes away, commits in that database wait until it is back or until `synchronous_standby_names` is emptied, so watch the
standby and empty the setting automatically when it stops streaming (a systemd timer is enough).

## MySQL and MariaDB

Driver-specific statements kept on the primary: `LOCK IN SHARE MODE`, `GET_LOCK()` and the other named-lock
functions, `LAST_INSERT_ID()`, the sequence functions (`NEXTVAL()`, `NEXT VALUE FOR`), the replication wait functions,
`LOAD_FILE()`. `SQL_CALC_FOUND_ROWS` and the `FOUND_ROWS()` after it stay together on whichever server the request
reads.

**Current**, checked with the first statement of the request that could use the replica:

1. **Status** (`SlaveServerStatusCheck`): `SHOW REPLICA STATUS` (`SHOW SLAVE STATUS` on MySQL before 8.0.22 and
   MariaDB before 10.5.1). Both replication threads must run, and `Seconds_Behind_Master` (`Seconds_Behind_Source`)
   must be known and at most `SlaveServerMaxLag`. A server with no replication status at all (a primary) is read as it
   is. The replica login needs `REPLICATION CLIENT` (MySQL) or `REPLICA MONITOR` (MariaDB 10.5.9 and later); without
   it the replica is not read, unless `SlaveServerStatusCheck=disabled`.
2. **Catch-up** (`SlaveServerCatchUp`): before it asks the replica, the driver reads the primary's GTID position
   (`@@gtid_binlog_pos` on MariaDB, `@@gtid_executed` on MySQL). The replica must have applied it: MariaDB waits up to
   `SlaveServerCatchUpWait` ms (`MASTER_GTID_WAIT()`), MySQL compares at once (`GTID_SUBSET()`). Whatever the primary
   had committed when the request started, on any node, the request sees, so a visitor right after an edit is never
   served the old page (and the view cache never keeps one). It costs one small statement on the primary per
   anonymous request instead of every statement of the page. Without a GTID position (no binary log, MySQL with
   `gtid_mode=OFF`) only the lag decides.

The replica connection uses the primary's character set; a replica that cannot is not read. A replica connection kept
from an earlier request (persistent connections, a Velocity worker) that broke meanwhile, for example because the
replica was restarted, is opened once more on the next request.

### Behaviour changes for sites that already used UseSlaveServer

The setting and its keys are the same; what changes from 6.0.15 on:

| Before | From 6.0.15 |
|---|---|
| The replica connection opened in the constructor, and a replica that was down stopped the site (`eZDBNoConnectionException`). | Opened on first use; a replica that is down means the request reads the primary. |
| Every `SELECT` outside a transaction went to the replica, also right after a write in the same request, also for signed-in editors, cronjobs and `POST` requests. | Only the requests and statements of the rule above. |
| No check of the replica's lag or threads. | `SlaveServerStatusCheck`, `SlaveServerMaxLag`, `SlaveServerCatchUp`. **Grant the replica login `REPLICATION CLIENT` (MySQL) or `REPLICA MONITOR` (MariaDB), or set `SlaveServerStatusCheck=disabled` to keep trusting the replica.** |
| `SERVER_SLAVE` forced the replica; temporary tables of the search and of permission checks were created there. | The rule decides; a temporary table is created on the primary and pins the request. |
| `eZTableList( SERVER_SLAVE )` listed the replica's tables, and `$db->DBConnection` was the replica connection. | Both are the primary; the replica connection is `$db->ReplicaConnection`. |

Most pages of an anonymous visitor read the replica as before; editors, forms and scripts read the primary.

## Oracle (ezoracle)

The ezoracle extension's `eZOracleDB` reads from a read-only standby (Active Data Guard) with the same rule.
`SlaveServerArray[]` is the standby's connect string (Easy Connect or a TNS name, as `Database` is for the primary),
with `SlaverServerUser[]` and `SlaverServerPassword[]`; `SlaveServerPort[]` and `SlaverServerDatabase[]` are not used.
The standby session gets the same session settings as the primary's (NLS, `CURSOR_SHARING`).

Driver-specific statements kept on the primary: `WITH`, `seq.NEXTVAL` and `seq.CURRVAL`, `INTO`, `DBMS_*` and `UTL_*`
calls, `SYS_GUID()`; PL/SQL blocks are not `SELECT`s.

**Current:** the standby's `v$database`. `PRIMARY` (a read-only user of the primary standing in) is read as it is. A
`PHYSICAL STANDBY` is read only when it is open `READ ONLY WITH APPLY` and its `apply lag` in `v$dataguard_stats` is at
most `SlaveServerMaxLag`. With `SlaveServerCatchUp=enabled` the standby session sets
`STANDBY_MAX_DATA_DELAY = <SlaveServerMaxLag>`, so Oracle itself refuses a read that would be older (`ORA-03172`,
and the statement runs on the primary). `STANDBY_MAX_DATA_DELAY = 0` needs SYNC redo transport; with ASYNC set
`SlaveServerMaxLag` above 0. The standby login needs:

```sql
GRANT SELECT ON sys.v_$database TO <standby login>;
GRANT SELECT ON sys.v_$dataguard_stats TO <standby login>;
```

Without them the standby is not read, unless `SlaveServerStatusCheck=disabled`. A standby login that is not the schema
owner also needs `SELECT` on the site's tables and synonyms for them. Details: ezoracle's `INSTALL`, "7. Read-only
standby".

## SQLite: not applicable

An SQLite database is one file on one server, which every statement reads and writes. `eZSQLite3DB` ignores
`UseSlaveServer` and the `SlaveServerArray[]` keys (it turns the setting off when it connects), so settings copied
from a MySQL or PostgreSQL site work unchanged. The shared query cache and the HTTP cache work on SQLite as on the
other engines.

MongoDB (`expMongoDB`) does not read `UseSlaveServer` either; a MongoDB replica set has its own read preferences.

## How to check

- Tests (no database needed): `tests/tests/lib/ezdb/eZPostgreSQLDBReadReplicaTest.php`,
  `eZMySQLiDBReadReplicaTest.php` and `eZSQLite3DBReadReplicaTest.php`; ezoracle's `tests/eZOracleReadReplicaTest.php`.
- PostgreSQL: on the standby, `pg_stat_activity` shows the site's login while anonymous pages are served; on the
  primary, `pg_stat_replication` shows the standby `streaming` with `sync_state = sync` when synchronous apply is on.
- MySQL/MariaDB: on the replica, `SHOW PROCESSLIST` shows the site's login while anonymous pages are served; a
  statement `SELECT @@server_id` from a page tells which server answered.
- Oracle: `SYS_CONTEXT('USERENV', 'SESSION_USER')` or `v$session` on the standby.

## Related pages

- [The kernel service container](kernel-services.md) (the reset hooks)
- [The SQL query cache](sql-query-cache.md)
- [Read replicas](../../help/government/operations/availability/read-replicas.md)
