---
title: High availability overview
audience: government, hosting, admin, project-manager
category: public-sector
order: 20
summary: For public bodies with continuity duties who need an Exponential site to survive the failure of a machine. What a single server can and cannot give, what several web servers require (database server, shared storage, sessions, one cron), and how to decide how much availability you need.
keywords: high availability, HA, continuity, redundancy, single point of failure, several web servers, load balancer, cluster, DFS, database server, failover, business continuity, public service availability
type: help
---
# High availability overview

This page is for IT architects and continuity planners in public bodies whose sites must stay reachable when one
machine fails, for example because they carry emergency information or statutory publications. It answers what a
single Exponential server gives you, what has to be in place before several servers can serve one site, and how to
decide how far to go. The details are on the hosting pages
[Several web nodes](../../../hosting/operations/several-web-nodes.md) and
[Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md).

## Contents

1. [Start with the duty, not the technology](#start-with-the-duty-not-the-technology)
2. [What one server gives you](#what-one-server-gives-you)
3. [What several web servers require](#what-several-web-servers-require)
4. [The single points of failure that remain](#the-single-points-of-failure-that-remain)
5. [A step-by-step path](#a-step-by-step-path)
6. [Checklist](#checklist)

## Start with the duty, not the technology

Ask first what the organization must deliver: how long the site may be unavailable, at what times, and how much
recent content may be lost after a failure. These are organizational decisions, described on
[Availability targets and service levels](availability-targets-and-service-levels.md) and
[RPO and RTO as decisions](../backups-and-recovery/rpo-and-rto-as-decisions.md). The project publishes no service
level and no availability figure for the software itself; the numbers you promise come from your own design and
tests.

## What one server gives you

"A single machine with enough CPU and memory is the simpler choice for most sites"
([Several web nodes](../../../hosting/operations/several-web-nodes.md)). On one machine Exponential offers:

| Feature | Helps with | Page |
|---|---|---|
| Velocity's watchdog | restarts the engine after a crash or an out-of-memory kill | [Velocity for public-sector teams](../hosting/velocity-for-public-sector-teams.md) |
| Response cache and HTTP cache | anonymous pages answered even under load spikes | [Velocity response cache and HTTP cache](velocity-response-cache-and-http-cache.md) |
| Maintenance mode | a clear 503 page with `Retry-After` during planned work | [Maintenance mode and planned downtime](maintenance-mode-and-planned-downtime.md) |
| Fast restore | a tested backup brings the site back on another machine | [Restore testing](../backups-and-recovery/restore-testing.md) |

What one server does not give you: survival of a hardware, disk, data center or network failure. A cold standby
machine with tested restores is often enough for a public body whose duty allows some hours of downtime.

## What several web servers require

From [Several web nodes](../../../hosting/operations/several-web-nodes.md):

| Part | Requirement | Page |
|---|---|---|
| Database | a database server every node reaches; not SQLite | [Database high availability](database-high-availability.md) |
| Uploaded files | shared through the DFS cluster file handler and an NFS mount | [Shared storage and the DFS cluster](shared-storage-and-the-dfs-cluster.md) |
| Sessions | in the database, or the balancer keeps each user on one node | [Sessions across servers](sessions-across-servers.md) |
| Load balancer | trusted by Exponential for the visitor's address and HTTPS | [Several web servers behind a load balancer](several-web-servers-behind-a-load-balancer.md) |
| Cron | on one node only | [Several web servers behind a load balancer](several-web-servers-behind-a-load-balancer.md) |
| Deploys, cache clears, maintenance | on every node | same page |

## The single points of failure that remain

Several web servers remove the web server as a single point of failure, but not everything:

- **The database server.** Every node depends on it. Its own replication or clustering is needed to remove it; see
  [Database high availability](database-high-availability.md).
- **The shared file storage.** "If the NFS mount goes away, every server fails to read files"
  ([Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md)).
- **The cluster metadata database**, which is on the path of every file read.
- **The load balancer**, unless it is redundant itself.
- **The data center**, unless nodes and data are in more than one.

Write these into your continuity plan with an owner and a recovery procedure each.

## A step-by-step path

| Step | What you add | You gain |
|---|---|---|
| 1 | one server, tested backups off site, a documented restore | recovery in the time your restore test measured |
| 2 | Velocity watchdog and external monitoring | automatic restart and fast alerting |
| 3 | a standby machine kept ready for restores | shorter recovery |
| 4 | a database server with its own replication | the database survives a machine failure |
| 5 | several web nodes, shared storage, a load balancer | the web tier survives a node failure |

Each step is a change of its own, with a test. Moving from SQLite to a database server is the documented way when a
second web server is needed ([Changing the database engine](../../../hosting/migrations/changing-the-database-engine.md)).

## Checklist

- [ ] Availability and recovery targets are decided by the organization and written down.
- [ ] The design is chosen to meet them, and its remaining single points of failure are listed.
- [ ] A restore has been tested and its time recorded.
- [ ] For several nodes: database server, shared storage, sessions, one cron and trusted proxies are in place.
- [ ] The continuity plan names an owner for each remaining single point of failure.

## Related pages

- [Availability](README.md) index of this folder
- [Disaster recovery plan](../backups-and-recovery/disaster-recovery-plan.md)
- [SLA options](../../../hosting/business/sla-options.md) for a provider's view of availability commitments
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
