---
title: Read replicas
audience: government, hosting, admin
category: public-sector
order: 60
summary: For database teams in public bodies running Exponential on MySQL or MariaDB. How Exponential sends read queries to replicas with UseSlaveServer, the exact settings, what replication lag means for editors, and when replicas help and when they do not.
keywords: read replica, replica, slave, UseSlaveServer, SlaveServerArray, SlaverServerUser, replication lag, MySQL, MariaDB, read scaling, database load
type: help
---
# Read replicas

This page is for database administrators in public bodies whose Exponential site runs on MySQL or MariaDB and who
want to move read load off the primary server. It answers how Exponential uses replicas, which settings switch it on,
what replication lag means for editors and visitors, and when replicas are worth it. The source is
[9.3 MySQL and MariaDB, Replicas](../../../../install/09-databases.md#replicas).

## Contents

1. [What Exponential does with replicas](#what-exponential-does-with-replicas)
2. [The settings](#the-settings)
3. [Replication lag](#replication-lag)
4. [When replicas help](#when-replicas-help)
5. [Checklist](#checklist)

## What Exponential does with replicas

With `UseSlaveServer=enabled`, Exponential sends read queries to a replica chosen at random per request; writes go to
`Server`. This is the only engine with built-in read splitting ([Database support](../../../database-support.md)).
PostgreSQL, Oracle, MongoDB and SQLite sites do not use replicas for reads through this setting.

Replicas are a performance feature, not a failover: if the primary fails, writes fail. For availability see
[Database high availability](database-high-availability.md).

## The settings

In `settings/override/site.ini.append.php`, block `[DatabaseSettings]`. The keys really are spelled `Slaver...` for
user, password and database:

```ini
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=db-replica-1.example.gov
SlaveServerPort[]
SlaveServerPort[]=3306
SlaverServerUser[]
SlaverServerUser[]=<read user>
SlaverServerPassword[]
SlaverServerPassword[]=<password>
SlaverServerDatabase[]
SlaverServerDatabase[]=<database>
```

The host name and placeholders are examples. The shipped `settings/site.ini` lists these keys commented out, with
`UseSlaveServer=disabled`. After the change clear the INI cache and make the engine read it:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

Give the replica user read rights only; it never needs to write. Keep the password out of version control
([Configuration under version control](../hosting/configuration-under-version-control.md)).

## Replication lag

"Replication lag means a page may show content a few seconds old"
([9.3](../../../../install/09-databases.md#replicas)). Tell editors what to expect: right after publishing, a page
read from a replica can still show the old version for a moment. Monitor the lag with your database's tools, and treat
a growing lag as an incident, because visitors then see old information, which matters for notices with legal or
safety weight.

## When replicas help

| Situation | Replicas help? |
|---|---|
| Many anonymous visitors | usually not much: Velocity's response cache and the HTTP cache answer them without the database ([Velocity response cache and HTTP cache](velocity-response-cache-and-http-cache.md)) |
| Many signed-in users, searches, uncached pages | yes, if the database is the bottleneck |
| The primary is slow because of writes | no; replicas only take reads |
| You need a standby for continuity | not by themselves; see [Database high availability](database-high-availability.md) |

Measure before you add a replica: `exp:benchmark` and the slow query output
(`[DatabaseSettings] SlowQueriesOutput` with `SQLOutput=enabled`) show where time goes
([Capacity planning](capacity-planning.md)).

## Checklist

- [ ] The site runs on MySQL or MariaDB.
- [ ] The replica user has read rights only; its password is not in version control.
- [ ] Settings are in `settings/override`, the INI cache cleared and the engine reloaded.
- [ ] Replication lag is monitored, and editors know about the short delay.
- [ ] A measurement shows the database was the bottleneck.

## Related pages

- [Availability](README.md) index of this folder
- [MySQL for hosts](../../../hosting/basics/mysql-for-hosts.md), [MariaDB for hosts](../../../hosting/basics/mariadb-for-hosts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
