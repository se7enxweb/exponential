---
title: Clustering and high availability
audience: businesses, project-manager, hosting, admin
category: business-solutions
order: 140
summary: When a business needs more than one web server for an Exponential site, what a multi-server setup requires (a database server, shared storage, sessions, a load balancer), what it costs in operations, and the simpler options to try first.
keywords: cluster, clustering, high availability, load balancer, several web nodes, DFS, NFS, shared storage, sessions, failover, redundancy, Velocity instances
type: help
---
# Clustering and high availability

This page is for owners and managers who wonder whether their site needs several servers, and for the project lead
who has to explain the cost. It describes what "high availability" means in practice for an Exponential site, what a
multi-server setup needs, what it adds to operations, and what to try first. The technical pages are
[Several web nodes](../../hosting/operations/several-web-nodes.md) and
[Clustering and shared storage](../../hosting/operations/clustering-and-shared-storage.md).

## Contents

1. [Two reasons for more servers](#two-reasons-for-more-servers)
2. [Try these first](#try-these-first)
3. [What several web servers need](#what-several-web-servers-need)
4. [What it adds to operations](#what-it-adds-to-operations)
5. [The parts that can still fail](#the-parts-that-can-still-fail)
6. [Questions for your hosting partner](#questions-for-your-hosting-partner)
7. [Decision checklist](#decision-checklist)

## Two reasons for more servers

The hosting documentation names two reasons: "Use several nodes when one machine is not enough or when the offer
promises that a node can fail without taking the site down"
([Several web nodes](../../hosting/operations/several-web-nodes.md)).

| Reason | Question to ask |
|---|---|
| Capacity | Have we measured that one machine, with caches on, cannot carry our peak? |
| Redundancy | Is the cost of the site being down while one server is repaired higher than the cost of running and maintaining several? |

If the answer to both is "no" or "we do not know", a multi-server setup is probably premature. Use the
Cost of downtime worksheet to answer the second question with your own
figures.

## Try these first

The same page advises: "A single machine with enough CPU and memory is the simpler choice for most sites". Before
adding servers:

1. **Caches on and warm** ([Caching for availability](caching-for-availability.md)).
2. **Measure** with `exp:benchmark` to know where the time goes ([Peak traffic planning](peak-traffic-planning.md#measure-before-you-guess)).
3. **Velocity**, where hosting allows it, raises what one machine can serve
   ([Velocity for businesses](velocity-for-businesses.md)). Several Velocity instances can share one port on one
   machine; `graceful` reloads them one at a time "so the port keeps answering"
   ([Velocity engines](../../../bc/6.0/velocity-engines.md#several-qbix-servers-on-one-port-instances)).
4. **A larger machine**: see [Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md).
5. **A fast restore**: for many small businesses a tested restore onto a standby server is a cheaper form of
   availability than a cluster ([Restore drills](restore-drills.md)).

> **Note**
> "A single server does not need a cluster file handler, whatever its size. Clustering is for a site that several
> machines serve at once. It adds a mount and a database to run, back up and monitor"
> ([Clustering and shared storage](../../hosting/operations/clustering-and-shared-storage.md#two-file-handlers)).

## What several web servers need

From [Several web nodes, what must be shared](../../hosting/operations/several-web-nodes.md#what-must-be-shared-what-stays-local):

| Part of the site | Across servers |
|---|---|
| Database | Shared: a database server all servers reach. SQLite is for one machine only |
| Uploaded files (`var/storage`) | Shared, through the DFS cluster file handler and an NFS mount |
| Code, settings, extensions, designs | Identical on every server, deployed the same way |
| Sessions | Shared through the database session handler, or each visitor kept on one server |
| Caches | Local per server, kept in step |
| Logs | Local per server; collect centrally for one view |
| Cronjobs | One server only |

Every supported database except SQLite supports several web servers on one database
([Several web nodes, the database](../../hosting/operations/several-web-nodes.md#the-database)). On MySQL and MariaDB,
read queries can be sent to replicas (`UseSlaveServer`, shipped `disabled`). A load balancer in front needs a health
check that takes a server out when it cannot render a page
([Health checks and uptime, load balancer health checks](../../hosting/operations/health-checks-and-uptime.md#load-balancer-health-checks)).

## What it adds to operations

Every routine task now happens on several machines
([Several web nodes, cron, deploys, maintenance and logs](../../hosting/operations/several-web-nodes.md#cron-deploys-maintenance-and-logs)):

- **Deploys on every server**, so all of them run the new code; take one out of the balancer while it is deployed if
  needed.
- **Maintenance mode on every server**: the state is a file in each server's `var/` directory.
- **Cron on one server only**, or the same jobs run twice.
- **Logs per server**: an error reference from a customer may be in another server's log.
- **Monitoring per server** as well as the shared address.
- **More to back up**: the database server, the shared storage, and the cluster's metadata.

Budget the staff time for this, not only the hardware; see Hosting cost drivers.

## The parts that can still fail

Several web servers protect against the loss of one web server. They do not by themselves protect against:

- the **database server** going down, unless the database has its own replication and failover;
- the **shared storage** (the NFS export) going down;
- the **load balancer** itself;
- a **bad deploy** pushed to every server at once;
- a **content mistake**, which is replicated everywhere instantly.

Each of these needs its own answer, from your hosting partner and from your own habits
([Graceful deploys](graceful-deploys.md), [Backups that restore](backups-that-restore.md)).

## Questions for your hosting partner

- [ ] Which parts are redundant: web servers, database, storage, load balancer, network?
- [ ] How are deploys and maintenance windows run on every server?
- [ ] Which server runs the cronjobs, and what happens if it fails?
- [ ] How are logs collected, so an error reference can be found on any server?
- [ ] How are the database and the shared storage backed up together?
- [ ] What has been tested: has a server been taken out on purpose to see the site keep running?

## Decision checklist

- [ ] We measured that one machine with caches on cannot carry our peak, **or** our downtime cost justifies redundancy.
- [ ] We tried the simpler options first.
- [ ] Our database is one that supports several web servers.
- [ ] Our team or partner can run deploys, maintenance, cron and logs across servers.
- [ ] The extra operating cost is in the budget.

## Related pages

- [Several web nodes](../../hosting/operations/several-web-nodes.md)
- [Database choice for reliability](database-choice-for-reliability.md)
- Contracts and SLAs basics
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
