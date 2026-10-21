---
title: MySQL and MariaDB read replicas
audience: dba
category: database-administration
order: 16
summary: How Exponential sends read queries to MySQL or MariaDB replicas with UseSlaveServer and the SlaveServer and SlaverServer keys, how a replica is chosen, what replication lag means for editors and visitors, and what the other engines offer instead.
keywords: replica, read replica, replication, UseSlaveServer, SlaveServerArray, SlaveServerPort, SlaverServerUser, SlaverServerPassword, SlaverServerDatabase, read splitting, replication lag, MySQL, MariaDB, scaling reads
type: help
---
# MySQL and MariaDB read replicas

This page is for database administrators who run MySQL or MariaDB with replicas and want Exponential to use them. It
answers: which settings send reads to a replica, how the replica is chosen, what lag means for the site, and how the
other engines compare. MySQL and MariaDB are the only engines with built-in read splitting in Exponential. Source:
[Replicas](../../install/09-databases.md#replicas) and `settings/site.ini`.

## Contents

1. [The settings](#the-settings)
2. [How a replica is chosen](#how-a-replica-is-chosen)
3. [Replication lag](#replication-lag)
4. [Accounts on the replicas](#accounts-on-the-replicas)
5. [Do you need replicas at all](#do-you-need-replicas-at-all)
6. [Other engines](#other-engines)
7. [Checklist](#checklist)

## The settings

The keys are in `site.ini [DatabaseSettings]`; the shipped file has them commented out:

```ini
# settings/override/site.ini.append.php
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=replica1.example.internal
SlaveServerArray[]=replica2.example.internal
SlaveServerPort[]
SlaveServerPort[]=3306
SlaveServerPort[]=3306
SlaverServerUser[]
SlaverServerUser[]=exponential_ro
SlaverServerUser[]=exponential_ro
SlaverServerPassword[]
SlaverServerPassword[]=<the password>
SlaverServerPassword[]=<the password>
SlaverServerDatabase[]
SlaverServerDatabase[]=exponential
SlaverServerDatabase[]=exponential
```

The keys for user, password and database really are spelled `Slaver...`; that is the name the code reads. The lists
are parallel: the second entry of each list belongs to the second replica. The empty `Key[]` line first resets the
list, so that only the entries of this file count.

Clear the INI cache, reload PHP-FPM and restart Velocity after a change
([Reading and changing database settings](reading-and-changing-database-settings.md)).

## How a replica is chosen

With `UseSlaveServer=enabled`, `eZDB::instance()` picks one replica at random (`mt_rand()` in `lib/ezdb/classes/ezdb.php`) when it creates the connection: per request under PHP-FPM. Reads go to that replica; writes always go to
`Server`. **Setup > System information**, part **Database connection**, shows the read replica in use
([System information](../../guides/system-information.md)).

## Replication lag

A page may show content a few seconds old while a replica catches up. In practice:

- an editor who publishes and immediately views the page may see the previous version for as long as the lag lasts;
- the caches (view cache, Velocity's response cache) may then keep that older rendering until their next refresh.

Keep lag low and monitor it on the replicas with your usual tools (for example `SHOW REPLICA STATUS` on MySQL 8.0.22
and later, `SHOW SLAVE STATUS` on older releases and MariaDB). Exponential itself does not measure lag or take a
lagging replica out of rotation; that is the job of your replication monitoring or a proxy in front of the replicas.

## Accounts on the replicas

A replica account needs read privileges; the kernel creates temporary tables for some read operations, so give it
`CREATE TEMPORARY TABLES` as well, or test the read paths you rely on (search, lists) with a strict read-only
account on staging. Keep the replica password in `settings/override/` like the main one.

## Do you need replicas at all

Most page views do not reach the database: Velocity's response cache, the content view cache and the template-block
cache answer first, and the SQL query cache can answer repeated `SELECT`s
([Performance and scaling](performance-and-scaling.md)). Measure first with `exp:benchmark` and the SQL profiler
([Finding slow queries](finding-slow-queries.md)). Replicas are worth their operation when uncached reads (signed-in
editors, search, the first views after a clear) really load the primary.

## Other engines

| Engine | Read scaling |
|---|---|
| PostgreSQL | Replication is PostgreSQL's own; Exponential has no read splitting for it. A proxy in front of PostgreSQL is a server-side choice. |
| MongoDB | Replica sets are MongoDB's own. |
| Oracle | External (Data Guard); `ezoracle.ini` offers failover over several listeners. |
| SQLite | No replication; one host. |

Source: [9.7 Comparison](../../install/09-databases.md#97-comparison).

## Checklist

- [ ] The lists of hosts, ports, users, passwords and databases have the same length.
- [ ] The replica account can read every table and create temporary tables.
- [ ] Lag is monitored outside Exponential.
- [ ] Editors know that a fresh publish may show with a short delay.

## Related pages

- [Setting up MySQL and MariaDB](mysql-and-mariadb-setup.md)
- [Several web nodes](../hosting/operations/several-web-nodes.md)
- [The DatabaseSettings reference](databasesettings-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
