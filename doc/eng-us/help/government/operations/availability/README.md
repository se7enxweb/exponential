---
title: Availability for public-sector sites
audience: government, hosting, admin, project-manager
category: public-sector
order: 10
summary: Index of the availability pages for public bodies: high availability, several web servers, shared storage, database availability and read replicas, the caches in front of PHP, cache warming, maintenance mode, sessions, capacity planning and service levels.
keywords: availability, high availability, continuity, load balancer, cluster, DFS, database, replicas, static cache, response cache, HTTP cache, cache warming, maintenance mode, sessions, capacity, service level
type: help
---
# Availability for public-sector sites

This folder is for IT teams, architects and service owners in public bodies who must keep a site reachable: during
peaks, through planned maintenance and when a machine fails. It covers what one server gives you, what several web
servers require, database availability, the caches in front of PHP, maintenance windows, sessions, capacity planning
and how to set availability targets. The detailed server procedures are in the hosting section for hosting companies,
especially [Several web nodes](../../../hosting/operations/several-web-nodes.md) and
[Clustering and shared storage](../../../hosting/operations/clustering-and-shared-storage.md); these pages add the
public-sector view: continuity duties, central network teams, change calendars and decisions owned by the
organization.

## Pages

| Page | Answers |
|---|---|
| [High availability overview](high-availability-overview.md) | What one server gives, what several require, and which single points of failure remain. |
| [Several web servers behind a load balancer](several-web-servers-behind-a-load-balancer.md) | What is shared, trusted proxies, cron on one node, deploys and clears on every node. |
| [Shared storage and the DFS cluster](shared-storage-and-the-dfs-cluster.md) | How several servers share uploaded files, and what the storage and database teams provide. |
| [Database high availability](database-high-availability.md) | What Exponential does for database availability, and how to switch to a standby. |
| [Read replicas](read-replicas.md) | How MySQL and MariaDB sites send reads to replicas, and when that helps. |
| [The static cache](static-cache.md) | Why it ships off, and the rare cases where it still fits. |
| [Velocity response cache and HTTP cache](velocity-response-cache-and-http-cache.md) | The caches that answer before rendering, their freshness and how to clear them. |
| [Cache warming after changes](cache-warming-after-changes.md) | How to warm the site after a deploy, a restore or before an announcement. |
| [Maintenance mode and planned downtime](maintenance-mode-and-planned-downtime.md) | What visitors and monitors see, who gets in, and planning a window. |
| [Sessions across servers](sessions-across-servers.md) | Keeping editors signed in across servers, and what a failed node means. |
| [Capacity planning](capacity-planning.md) | The figures the repository gives and how to measure your own site. |
| [Availability targets and service levels](availability-targets-and-service-levels.md) | Which commitments you can define, what they depend on and how to measure them. |

## Reading order

1. Decide what you need: [Availability targets and service levels](availability-targets-and-service-levels.md), then
   [High availability overview](high-availability-overview.md).
2. Keep one server fast and predictable: the [caches](velocity-response-cache-and-http-cache.md),
   [cache warming](cache-warming-after-changes.md), [capacity planning](capacity-planning.md) and
   [maintenance mode](maintenance-mode-and-planned-downtime.md).
3. Only when the targets require it, several servers: [load balancer](several-web-servers-behind-a-load-balancer.md),
   [shared storage](shared-storage-and-the-dfs-cluster.md), [sessions](sessions-across-servers.md),
   [database availability](database-high-availability.md) and [read replicas](read-replicas.md).
4. Read [The static cache](static-cache.md) only if someone asks for it.

Continue with [Backups and recovery](../backups-and-recovery/README.md); hosting choices are in [Hosting](../hosting/README.md).

## Related pages

- Up: [Operations](../README.md)
- Start of the section: [Governments: start here](../../government-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
