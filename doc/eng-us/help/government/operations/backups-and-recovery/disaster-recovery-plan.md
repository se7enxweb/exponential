---
title: A disaster recovery plan
audience: government, hosting, admin, project-manager
category: public-sector
order: 100
summary: For public bodies that must document how a website comes back after a disaster: loss of a server, a data center, the database or the data itself. The scenarios, the parts of a plan, an outline you can copy, and the Exponential steps each scenario needs.
keywords: disaster recovery, DR plan, continuity plan, business continuity, data center loss, server loss, ransomware, compromise, restore, standby, contacts, scenario, exercise
type: help
---
# A disaster recovery plan

This page is for continuity planners, IT managers and operations teams in public bodies who write the disaster
recovery plan of a website. It lists the scenarios worth planning for, the parts every plan needs, an outline to copy,
and the Exponential steps behind each scenario. Your organization's continuity framework decides the format; this page
fills in the technical part.

## Contents

1. [Scenarios](#scenarios)
2. [What every plan needs](#what-every-plan-needs)
3. [An outline to copy](#an-outline-to-copy)
4. [The technical recovery](#the-technical-recovery)
5. [Exercising the plan](#exercising-the-plan)
6. [Checklist](#checklist)

## Scenarios

| Scenario | Recovery | Pages |
|---|---|---|
| A server fails, data intact elsewhere | restore onto a new or standby machine | [Restoring to a test server](restoring-to-a-test-server.md) (same steps, production host) |
| The database server fails | switch to a standby, or restore the database | [Database high availability](../availability/database-high-availability.md) |
| The data center is lost | rebuild in a second location from off-site backups | [Offsite and encrypted copies](offsite-and-encrypted-copies.md) |
| Data destroyed or encrypted by an attacker | restore from a backup taken before the attack; investigate first | [Compromised site](../../../hosting/runbooks/compromised-site.md) |
| A large content error by editors | restore content from the trash or older versions, or a selective restore | [Content history](../../../../guides/content-history.md) |
| The shared storage of a cluster fails | restore the NFS export and its metadata database | [Shared storage and the DFS cluster](../availability/shared-storage-and-the-dfs-cluster.md) |

## What every plan needs

- **Objectives**: recovery point and recovery time, decided by the organization ([RPO and RTO as decisions](rpo-and-rto-as-decisions.md)).
- **Roles**: who declares a disaster, who restores, who communicates; team names with on-call contacts kept in a
  separate, current list.
- **Locations**: where backups, keys and the plan itself are kept, reachable when the main site and network are down.
- **Procedures**: step-by-step, tested, one per scenario.
- **Communication**: what the public is told and where, while the site is down (a status page on another platform,
  social channels, a phone line).
- **Dependencies**: DNS, certificates, mail relay, central proxy, identity provider.

## An outline to copy

1. Purpose and scope: which sites, which installations.
2. Objectives per site: recovery point, recovery time, priority order.
3. Roles and contacts (reference to the contact list).
4. Inventory: hosting decision record, data location inventory, backup locations
   ([A hosting decision record](../hosting/hosting-decision-record.md),
   [Data location and what is stored where](../hosting/data-location-and-what-is-stored-where.md)).
5. Scenarios and their procedures (table above).
6. Communication templates.
7. Return to normal: checks, closing the incident, post-incident review.
8. Exercise schedule and history.

## The technical recovery

Whatever the scenario, the Exponential side follows the same core:

```bash
php bin/php/maintenance.php on --message="Restoring the service" --until=2h --allow-root-user
php bin/php/console exp:velocity stop --allow-root-user
# restore the database with the engine's tool, unpack the files and key files
php bin/php/ezcache.php --clear-all --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
php bin/php/console exp:velocity start --allow-root-user
php bin/php/console exp:velocity cache clear --allow-root-user
./console exp:audit verify --allow-root-user
php bin/php/maintenance.php off --allow-root-user
```

From [Restore from backup](../../../hosting/runbooks/restore-from-backup.md). Before overwriting anything after an
attack, keep a copy of the damaged state for the investigation. After the restore, rebuild the search index if it
looks incomplete (`php bin/php/updatesearchindex.php`) and republish or re-enter what was lost since the backup.

## Exercising the plan

A plan that was never exercised is a draft. Exercise it on a schedule you choose: a table-top walk-through with the
roles, and a technical restore into a second location. Record the measured time and every gap found, and update the
plan. The restore test reports from [Restore testing](restore-testing.md) are part of the evidence.

## Checklist

- [ ] Each site has objectives, a priority and a scenario table.
- [ ] Backups, keys and the plan are reachable when the main site and network are down.
- [ ] Public communication during an outage is prepared.
- [ ] The plan was exercised, and the measured times are in it.
- [ ] It is reviewed after every incident and every major change.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [High availability overview](../availability/high-availability-overview.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
