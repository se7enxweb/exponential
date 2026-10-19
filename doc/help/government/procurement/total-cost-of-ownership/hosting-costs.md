---
title: Estimating hosting costs for an Exponential site
audience: government, project-manager, hosting, admin
category: public-sector
order: 50
summary: The factors that decide the hosting cost of an Exponential site - hosting model, engine, database, search, storage, backups, availability and environments - with the sizing facts the repository states and a method to estimate with your own quotes. No prices are given.
keywords: hosting cost, infrastructure cost, server sizing, cloud, VPS, dedicated server, containers, database server, backups, availability, environments, staging, estimate
type: help
---
# Estimating hosting costs for an Exponential site

This page is for project managers who must estimate the hosting part of a total cost of ownership, and for the
IT staff who help them. It lists what drives the cost, the sizing figures the repository actually states, and a
method to turn quotes into an annual figure. It contains no prices; use quotes from your hosts or your own
internal rates.

## Contents
1. [What drives the hosting cost](#what-drives-the-hosting-cost)
2. [Sizing facts from the repository](#sizing-facts-from-the-repository)
3. [Environments](#environments)
4. [Availability and backups](#availability-and-backups)
5. [A method to estimate](#a-method-to-estimate)
6. [Questions for hosting quotes](#questions-for-hosting-quotes)

## What drives the hosting cost

| Driver | Low-cost choice | Higher-cost choice | Documentation |
|---|---|---|---|
| Hosting model | shared hosting or one VPS | dedicated servers, several web nodes, Kubernetes | [Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md) |
| Database | SQLite 3, no server | a database server, replicated | [Requirements](../../../../install/02-requirements.md), [Databases in the official order](../../../hosting/basics/databases-in-the-official-order.md) |
| Search | built-in search | Solr through ezfind | [Search backends](../../../hosting/basics/search-backends.md) |
| Engine | Apache or nginx with PHP-FPM, or Velocity | several Velocity instances, load balancer | [Choosing a web server](../../../hosting/servers/choosing-a-web-server.md) |
| Storage | small media library | large media library, shared storage for several nodes | [Storage layout](../../../hosting/operations/storage-layout.md) |
| Availability | single server, restore from backup | several nodes, monitored around the clock | [Several web nodes](../../../hosting/operations/several-web-nodes.md) |
| Management | your own staff | managed hosting | [Managed hosting](../../../hosting/models/managed-hosting.md) |

Several web nodes for one site need a database server and shared `var/storage`; SQLite fits one writing machine only
([Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md)).

## Sizing facts from the repository

From [Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md):

- PHP `memory_limit`: the health check fails below 128M and warns below 256M.
- One Velocity worker holds about 10 MB of private memory (measured); the shipped pool is 4 workers.
- OPcache and APCu reserve shared memory (256 MB each in the shipped Velocity settings).
- Free disk: the health check fails under 1 GB or 5 %, warns under 5 GB or 10 %.
- Cached pages cost little CPU; rendered pages (signed-in editors, first views, search) are where CPU goes.

The page's own advice is to measure a real site rather than guess: run the proof of concept with your content and
measure with `exp:benchmark` ([Benchmarking](../../../../guides/benchmarking.md)).

## Environments

| Environment | Purpose | Often sized as |
|---|---|---|
| Production | the public site | full size |
| Staging or acceptance | testing releases and content before production | smaller copy |
| Development | supplier's work | supplier's own machines, or small |
| Training | editor courses with invented content | small; see [Lab quick card](../../../trainers/lab-quick-card.md) |

Count every environment you pay for. A staging copy must never hold more personal data than it needs.

## Availability and backups

- Backups cover the database and files together; settings and generated keys must be in them
  ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)).
- Backup storage grows with retention and with `var/storage`.
- Restore time is only known after a restore test ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)).
- An availability target above what one server gives multiplies servers, storage and operations work.

## A method to estimate

1. Decide the hosting model and environments with your IT team.
2. Measure the proof of concept with your content: memory, disk, page render times.
3. Get at least two quotes per model, with the same specification and service level.
4. Add internal costs: your staff's time for monitoring, updates and backups if you operate it yourself.
5. Multiply recurring costs over the horizon, add one-off setup.
6. Enter the result in the [TCO worksheet template](tco-worksheet-template.md).

## Questions for hosting quotes

- Which PHP version, database and engine will run the site? Are they supported
  ([Requirements](../../../../install/02-requirements.md))?
- Where is the data stored (country, provider), and where are backups stored?
- How often are backups taken, how long kept, and when was a restore last tested?
- How fast are released Exponential security fixes applied ([SLA options](../../../hosting/business/sla-options.md))?
- What is included: TLS certificates, monitoring, staging, support hours?
- What does the host hand over at the end of the contract ([Customer offboarding](../../../hosting/checklists/customer-offboarding.md))?

## Related pages
- [Total cost of ownership: overview](README.md)
- [On-premise or cloud](on-premise-vs-cloud.md)
- [Hosting companies: start here](../../../hosting/hosting-companies-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
