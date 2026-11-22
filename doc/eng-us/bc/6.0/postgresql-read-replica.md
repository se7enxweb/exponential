# PostgreSQL read replica (6.0.15)

Read this page if your site runs on PostgreSQL and you want its pages to read from a replica close to the web server,
for example a hot standby on the same machine as one node of a cluster, while every write still goes to the primary.

## In short

| | |
|---|---|
| What changed | `eZPostgreSQLDB` honours `[DatabaseSettings] UseSlaveServer` and the `SlaveServerArray[]` keys. Before, only the MySQLi driver did; PostgreSQL sent every statement to `Server`. |
| Who is affected | Nobody until `UseSlaveServer=enabled` is set for a PostgreSQL site. The shipped `settings/site.ini` has `UseSlaveServer=disabled`. |
| What to do | Set the replica in `settings/override/site.ini.append.php` (below), clear the INI cache and deploy. |

## Which statements read from the replica

A statement runs on the replica only when **all** of these hold. Anything else runs on the primary, as before.

1. It is a plain `SELECT`: not `WITH` (a data-modifying CTE writes), no `nextval()`, `currval()`, `setval()`,
   `lastval()`, no `FOR UPDATE` / `FOR SHARE`, no advisory lock, no `SELECT ... INTO`. The test reads the text, so a
   literal that merely contains one of these words also goes to the primary (a round trip, never a wrong answer).
2. No transaction is open.
3. The request has written nothing yet. The first `INSERT`, `UPDATE`, `DELETE`, `BEGIN`, `LOCK` or any other
   statement that is not such a `SELECT` pins the rest of the request to the primary, so a request reads what it wrote.
4. The request is a web request (PHP-FPM, Apache or a Velocity worker) with the method `GET` or `HEAD`. Cronjobs,
   scripts and console commands always use the primary, and so does every `POST`.
5. The visitor is not signed in (no `is_logged_in` cookie). Editors always read the primary, so they see their own
   work at once.
6. The replica answers and streams from its primary: the first statement of the request asks it
   `pg_is_in_recovery()` and `pg_stat_wal_receiver.status`. A standby whose stream is broken is not read. A server
   that is not in recovery (a primary) is accepted. The site's database login needs `pg_read_all_stats` to see the
   receiver's status; without it the replica is never used.

Any error on the replica (no connection, a statement cancelled by a recovery conflict) runs the same statement on the
primary and turns the replica off for the rest of the request. The connection is opened on first use, with a 2 s
timeout and no retries. A node where nothing listens on the replica's address simply reads the primary, so one set of
settings can serve every node of a cluster when only some nodes have a local replica.

The decision and the pin are per request: `eZPostgreSQLDB::resetRequest()` is a reset hook of
`expKernelServiceScope` and runs at the start of every request, also in a persistent Velocity worker.

The DFS cluster backend (`file.ini`, `config.cluster.php`) has its own connection and keeps reading the primary.

## Visitors after an edit: synchronous apply

Rules 3 and 5 cover the editor's own request and the editor's later pages. An anonymous visitor who asks for the page
a moment after an editor published it is served by another request, perhaps on another node. With asynchronous
replication that request can still see the old version on the replica, and the view cache would keep it.

To close that window make the primary wait until the standby has applied each commit of the site's database:

```sql
-- on the primary
ALTER SYSTEM SET synchronous_standby_names = '<standby application_name>';
SELECT pg_reload_conf();
ALTER DATABASE <site database> SET synchronous_commit = remote_apply;
GRANT pg_read_all_stats TO <site login>;
```

A commit then returns only after the standby shows it. The cost is one network round trip per commit. If the standby
goes away, commits in that database wait until it is back or until `synchronous_standby_names` is emptied, so watch the
standby and empty the setting automatically when it stops streaming (a systemd timer is enough).

## Settings

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

A physical standby carries the primary's logins and passwords, so the site's own login works on it. Then:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

## How to check

- `tests/tests/lib/ezdb/eZPostgreSQLDBReadReplicaTest.php` (no database needed): which statements and requests may use
  the replica, the pin after a write, the reset hook.
- On the standby, `pg_stat_activity` shows the site's login while anonymous pages are served; on the primary,
  `pg_stat_replication` shows the standby `streaming` with `sync_state = sync` when synchronous apply is on.

## Related pages

- [The kernel service container](kernel-services.md) (the reset hooks)
- [Read replicas](../../help/government/operations/availability/read-replicas.md)
