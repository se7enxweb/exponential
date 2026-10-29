---
title: Database unreachable
audience: government, admin, hosting
category: public-sector
order: 40
summary: Runbook for "We'll be right back" pages caused by a database Exponential cannot reach - check the server, the credentials, connections and disk per engine, switch to a standby if you run one, and recover with caches cleared.
keywords: database down, database unreachable, We'll be right back, 503, MySQL, MariaDB, PostgreSQL, SQLite, database is busy, connection refused, credentials, standby, failover, runbook
type: help
---
# Database unreachable

This runbook is for administrators when visitors get "We'll be right back" (503) because Exponential cannot reach its
database. Exponential has no automatic database failover; the database server and its replication are run with the
vendor's tools ([Database high availability](../availability/database-high-availability.md)).

## Contents
1. [Confirm](#confirm)
2. [Which database?](#which-database)
3. [Server databases](#server-databases)
4. [SQLite](#sqlite)
5. [Recover](#recover)
6. [Related pages](#related-pages)

## Confirm

```bash
./console exp:logs --tail=var/log/error.log --lines=30
./console exp:logs --search='database' --scope=kernel --level=error
```

The log line holds the driver's message (connection refused, access denied, too many connections, database is busy).
The front page may still be served from a cache while the administration fails; check both.

## Which database?

```bash
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini
```

Values: `ezmysqli` (MySQL, MariaDB), `ezpostgresql` or `pgsql` (PostgreSQL), `sqlite3`, `mongodb`; Oracle comes with
its extension ([Operating a site, section 7](../../../../guides/operating-a-site.md#7-know-your-database-driver)).

## Server databases

1. Is the server running and reachable from the web server? Use the vendor's client with the host, port and user of
   `[DatabaseSettings]` in `settings/override/site.ini.append.php` (the client asks for the password).
2. **Access denied**: the password changed or the user was removed; fix it on the database or in the settings, then
   clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) and reload PHP.
3. **Too many connections**: look at the server's connection limit and at persistent connections
   (`UsePersistentConnection`); under Velocity each worker keeps its connection.
4. **Disk full on the database server**: free space with the database team.
5. **A standby exists**: if the primary will not come back soon, promote the standby with the vendor's tools and
   point Exponential at it ([Fail over to a standby database](../../tasks/operations/fail-over-to-a-standby-database.md)).

`ConnectRetries` (default `0`) makes the driver try again on a failed first connection; it does not replace a
standby.

## SQLite

- "Database is busy": writers queue for the single write lock; the wait is `SQLiteTransactionWait` (25 seconds). Many
  simultaneous publishers are a reason to move to a server database.
- Every process (web server's PHP, Velocity's workers, cron, console) must be able to write the database directory
  and its `-wal`, `-shm` and `.writer-lock` files; mixed file owners after a root-run command are a common cause.
- SQLite cannot be shared over a network file system ([Database support](../../../database-support.md)).

## Recover

1. Confirm the administration loads and a page renders from the database (not only from cache).
2. Clear the caches that may hold error states, after PHP and Velocity run normally:
   `php bin/php/ezcache.php --clear-tag=content --allow-root-user`; under Velocity
   `./console exp:velocity cache clear --apply --allow-root-user`.
3. Check that cronjobs run again (they failed during the outage) and run the frequent part once by hand.
4. Record the cause; if data may have been lost (a failover to a lagging standby), tell content owners what to check.

## Related pages
- [Site down](site-down.md)
- [Read replicas](../availability/read-replicas.md)
- [Backups per database engine](../backups-and-recovery/backups-per-database-engine.md)
- [Databases chapter](../../../../install/09-databases.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
