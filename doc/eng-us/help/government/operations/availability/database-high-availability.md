---
title: Database high availability
audience: government, hosting, admin
category: public-sector
order: 50
summary: For public-sector database and infrastructure teams. What Exponential itself offers for database availability (connection retries, read replicas on MySQL and MariaDB, Oracle failover settings), what is left to the database's own replication, and how to switch a site to a standby database.
keywords: database high availability, replication, standby, failover, point-in-time recovery, PostgreSQL, MariaDB, MySQL, Oracle, Failover, ConnectRetries, UseSlaveServer, SQLite limits, continuity
type: help
---
# Database high availability

This page is for database administrators and infrastructure teams in public bodies who must keep the database of an
Exponential site available when a database machine fails. It answers what Exponential does on its own, what it leaves
to the database engine, and what switching to a standby database involves. The references are
[9 Databases](../../../../install/09-databases.md) and [Database support](../../../database-support.md).

## Contents

1. [What Exponential does and does not do](#what-exponential-does-and-does-not-do)
2. [Per engine](#per-engine)
3. [Switching to a standby database](#switching-to-a-standby-database)
4. [Testing it](#testing-it)
5. [Checklist](#checklist)

## What Exponential does and does not do

Exponential connects to one database server per site, named in `site.ini [DatabaseSettings] Server`. It has:

| Feature | Setting | Engines |
|---|---|---|
| Retries when the first connection fails | `[DatabaseSettings] ConnectRetries` (shipped `0`) | MySQL, PostgreSQL, SQLite |
| Reads sent to replicas | `UseSlaveServer` with `SlaveServerArray[]` (shipped `disabled`) | MySQL and MariaDB only |
| A descriptor over several listeners, failover, retries | `ezoracle.ini [ConnectionSettings] Hosts[]`, `Failover`, `RetryCount` | Oracle |

It has **no built-in automatic failover** for PostgreSQL, MariaDB or MySQL: if the server named in `Server` fails, the
site cannot reach its database until that address works again. Database availability is therefore the job of the
database platform: its replication, a virtual IP or DNS name that moves to the standby, or a proxy in front of the
database. Choose what your database team already runs.

## Per engine

| Engine | Availability options | Notes |
|---|---|---|
| 1. SQLite 3 | none of its own | one host; no replication; recover from backups ([9.2](../../../../install/09-databases.md#limits-and-when-to-choose-another-engine)) |
| 2. PostgreSQL | its own replication and point-in-time recovery | set up with the PostgreSQL documentation |
| 3. MongoDB | see the vendor's documentation | read the [MongoDB limits](../../../database-support.md#mongodb-limits) first |
| 4. MariaDB, 6. MySQL | the server's replication; Exponential can read from replicas | [Read replicas](read-replicas.md) |
| 5. Oracle | `[ConnectionSettings]` failover over several listeners, DRCP, retries | every feature is off by default ([9.6](../../../../install/09-databases.md#96-oracle-19c-and-later-ezoracle)) |

For a public body with a continuity duty, SQLite is the wrong engine once the duty asks for a standby database; plan
the move to a database server ([Changing the database engine](../../../hosting/migrations/changing-the-database-engine.md)).

## Switching to a standby database

When the platform has no moving address and you switch by hand, the change on the Exponential side is one setting.
The database steps (promoting the standby) follow your engine's documentation.

1. Put the site into maintenance, so nobody writes during the switch:

   ```bash
   php bin/php/maintenance.php on --message="Switching database" --until=30m --allow-root-user
   ```

2. Promote the standby with the database's own tools.
3. Change `Server` (and `Port` if needed) in `settings/override/site.ini.append.php`, `[DatabaseSettings]`; with a
   cluster, also the cluster's database settings and `CLUSTER_STORAGE_HOST`.
4. Clear the INI cache and make the engine read the new value:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Under PHP-FPM without Velocity, reload the PHP-FPM that serves the site.
5. Check the site, then end maintenance:

   ```bash
   php bin/php/maintenance.php off --allow-root-user
   ```

A DNS name or virtual address that the database team moves makes step 3 unnecessary, and is the better design.

## Testing it

A standby that was never switched to is a hope. Test the switch on a test environment
([Environments for development, test and production](../hosting/environments-dev-test-prod.md)), measure how long it
takes, and record the time with your recovery objectives. After a real switch, check content published just before
the failure: replication lag can lose the last writes.

## Checklist

- [ ] The engine supports the continuity duty (not SQLite when a standby is required).
- [ ] The database team runs replication and owns the promotion procedure.
- [ ] The site reaches the database through an address that can move, or the manual switch is documented.
- [ ] `ConnectRetries` is decided.
- [ ] The switch was tested and its time recorded.

## Related pages

- [Availability](README.md) index of this folder
- [Choosing a database in the official order](../hosting/database-choice-in-the-official-order.md)
- [Backups per database engine](../backups-and-recovery/backups-per-database-engine.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
