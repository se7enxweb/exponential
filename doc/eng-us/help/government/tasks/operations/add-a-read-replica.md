---
title: Add a read replica
audience: government, admin, hosting
category: public-sector
order: 270
summary: Step by step - send read queries of a MySQL or MariaDB site to one or more replicas with UseSlaveServer, after the database team has set up replication, and check the effect; with the limits (MySQL and MariaDB only, replication lag).
keywords: read replica, replication, UseSlaveServer, SlaveServerArray, MySQL, MariaDB, read scaling, database load, replication lag, DatabaseSettings
type: help
---
# Add a read replica

This task is for administrators of busy sites on MySQL or MariaDB. With `site.ini [DatabaseSettings]
UseSlaveServer=enabled`, reads go to a replica chosen at random per request and writes to `Server`. It is the only
engine with built-in read splitting ([Database support](../../../database-support.md); [chapter 9, Replicas](../../../../install/09-databases.md#replicas)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Limits](#limits)
5. [Related pages](#related-pages)

## Before you start

- The database team has set up replication with the vendor's tools; Exponential does not.
- A database user on the replica with read access.
- A change approved and tested on staging.

## Steps

1. In `settings/override/site.ini.append.php` (the keys really are spelled `Slaver...` for the user, password and
   database):

   ```ini
   [DatabaseSettings]
   UseSlaveServer=enabled
   SlaveServerArray[]=db-replica1.internal.example.gov
   SlaveServerPort[]=3306
   SlaverServerUser[]=<read user>
   SlaverServerPassword[]=<password>
   SlaverServerDatabase[]=<database>
   ```

2. Clear the INI cache and deploy:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

## Check it worked

- The site works; publishing still works (writes go to the primary).
- The replica's own query statistics show reads arriving.
- **Setup > System information**, Database connection, for the connection details as the page shows them.

## Limits

- **Replication lag**: a page may show content a few seconds old; an editor who just published may not see the
  change at once on a page read from the replica.
- **MySQL and MariaDB only**; other engines scale reads with their own tools.
- A replica is not a backup and not a failover: if the primary fails, writes fail
  ([Fail over to a standby database](fail-over-to-a-standby-database.md)).
- The password is in a settings file: keep `settings/override` out of version control or use your secrets process.

## Related pages
- [Read replicas](../../operations/availability/read-replicas.md)
- [Database high availability](../../operations/availability/database-high-availability.md)
- [Several web nodes (hosting)](../../../hosting/operations/several-web-nodes.md)
- [MariaDB for hosts](../../../hosting/basics/mariadb-for-hosts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
