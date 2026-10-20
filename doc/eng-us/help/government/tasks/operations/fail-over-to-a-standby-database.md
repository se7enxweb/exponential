---
title: Fail over to a standby database
audience: government, admin, hosting
category: public-sector
order: 280
summary: Step by step - when the primary database is lost and the database team has promoted a standby, point Exponential at it with a settings change, clear the INI cache, reload, and check; Exponential has no automatic failover of its own.
keywords: failover, standby database, promote, high availability, DatabaseSettings Server, database down, disaster recovery, PostgreSQL, MariaDB, MySQL, manual failover
type: help
---
# Fail over to a standby database

This task is for administrators during a database outage when a standby exists. **Exponential has no automatic
database failover** for PostgreSQL, MariaDB or MySQL: the standby is promoted with the database vendor's tools, and
Exponential is then pointed at it. Background: [Database high availability](../../operations/availability/database-high-availability.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Afterwards](#afterwards)
5. [Related pages](#related-pages)

## Before you start

- The incident lead decided to fail over ([Database unreachable](../../operations/incidents/database-unreachable.md)).
- The database team promoted the standby and gave you its host name; the site's database user exists there with the
  same rights.
- Know how far behind the standby was: content edited after that point is lost.

## Steps

1. Maintenance mode, so nothing writes during the switch:

   ```bash
   php bin/php/maintenance.php on --message="This service is temporarily unavailable." --allow-admin --allow-root-user
   ```

2. Point the site at the new primary in `settings/override/site.ini.append.php`:

   ```ini
   [DatabaseSettings]
   Server=db-standby.internal.example.gov
   ```

   If replicas are configured (`UseSlaveServer`), check they follow the new primary, or switch read splitting off.
   With the DFS cluster, the cluster's metadata database (`file.ini [eZDFSClusteringSettings]`) needs the same
   attention.
3. Clear the INI cache and make PHP read it:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   On several web servers, do steps 2 and 3 on each, or deploy the settings to all.

## Check it worked

- From an allowed address: the administration loads, a page renders from the database, a test publish works.
- `./console exp:logs --tail=var/log/error.log --lines=20` shows no connection errors.
- Switch maintenance off and clear the page caches ([Bring the site back online](bring-the-site-back-online.md)).

## Afterwards

- Tell content owners which period may be lost and ask them to check recent changes.
- Rebuild a new standby with the database team; the settings change goes into version control.
- Record the failover and its duration for your continuity reporting.

## Related pages
- [Add a read replica](add-a-read-replica.md)
- [Disaster recovery plan](../../operations/backups-and-recovery/disaster-recovery-plan.md)
- [Databases in the official order (hosting)](../../../hosting/basics/databases-in-the-official-order.md)
- [PostgreSQL for hosts](../../../hosting/basics/postgresql-for-hosts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
