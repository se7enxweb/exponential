---
title: Hosting models compared
audience: businesses, project-manager, hosting
category: business-solutions
order: 20
summary: Shared hosting, VPS, dedicated servers, managed hosting, platform as a service, containers and Kubernetes compared for a business running Exponential - what each allows, who does the work, the risks and which kind of organization each suits - qualitatively, from the project's hosting documentation.
keywords: hosting models, shared hosting, VPS, dedicated server, managed hosting, platform as a service, PaaS, containers, Docker, Kubernetes, comparison, Velocity, SQLite, isolation, trade-offs
type: help
---
# Hosting models compared

This page is for owners and managers who see offers for very different kinds of hosting and must compare them. It
translates the project's technical comparison into business terms: what each model allows, who does the work, what
can go wrong, and which organizations it suits. It gives no prices; the project publishes none
([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).

## Contents

1. [The models in one table](#the-models-in-one-table)
2. [Each model in business terms](#each-model-in-business-terms)
3. [Who does the work](#who-does-the-work)
4. [Which model suits which organization](#which-model-suits-which-organization)
5. [Changing model later](#changing-model-later)

## The models in one table

Technical facts from [Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md#the-comparison-table):

| | Shared | VPS | Dedicated | Containers | Kubernetes |
|---|---|---|---|---|---|
| Your access | panel, often no shell | root or a sudo user | root | the image and its volumes | manifests, images, volumes |
| Velocity (long-running process) | usually not allowed | yes | yes | yes | yes |
| Usual way to serve | Apache or LiteSpeed with `.htaccess` | Velocity, or Apache/nginx with PHP-FPM | same as VPS | Velocity in the container | Velocity or PHP-FPM |
| SQLite 3 | yes, one site per file | yes | yes | one writing container only | one writing replica only |
| Several web nodes for one site | no | no | no | needs a database server and shared storage | same |

The project's own tier scale for these setups goes from 1 (tested) to 3 (expected to work); plan a test installation
before you rely on a tier 3 model ([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)).

## Each model in business terms

**Shared hosting.** Many customers on one server, managed by the host through a panel. Simple and usually the
smallest bill. Exponential works with Apache or LiteSpeed and the setup wizard; Velocity is usually not possible
because long-running processes are not allowed ([Shared hosting](../../hosting/models/shared-hosting.md)). Isolation
from other customers depends on how the host configures accounts and PHP pools.

**VPS (virtual private server).** Your own virtual machine. You can run Velocity and choose PHP versions. Someone must
run it: you, your partner, or the host as a managed service ([VPS hosting](../../hosting/models/vps-hosting.md)).

**Dedicated server.** A whole physical machine. More capacity and control, the same operating duties as a VPS
([Dedicated servers](../../hosting/models/dedicated-servers.md)).

**Managed hosting.** Not a separate technology but a service on top of one of the others: the host takes over updates,
backups, monitoring and more, using the tools Exponential provides (updates with `ezupdate`, the upgrade check,
maintenance mode, the log viewer, the audit trail)
([Managed hosting](../../hosting/models/managed-hosting.md)).

**Platform as a service.** A platform runs your code; tier 3 in the sources. A read-only or temporary file system
needs `var/` on persistent storage and a database server ([Platform as a service](../../hosting/models/platform-as-a-service.md)).

**Containers (Docker, Podman).** The site in a container image, tier 2 in the sources. Good for repeatable
deployments; volumes must keep the data ([Containers](../../hosting/models/containers-docker-and-podman.md)).

**Kubernetes.** Many containers orchestrated; tier 3. Several replicas need a database server, shared file storage,
shared sessions and care with cron ([Kubernetes](../../hosting/models/kubernetes.md)). Worth it when you already run
Kubernetes, rarely for one small site.

## Who does the work

| Task | Shared | VPS or dedicated | Managed | Containers or Kubernetes |
|---|---|---|---|---|
| Server and OS updates | host | you or your partner | host | your platform team |
| PHP updates | host (you choose a version) | you | host | in your image |
| Exponential updates | you | you | host, if agreed | you, in your image |
| Backups | host, check what is included | you | host | you, of volumes and database |
| Restore tests | ask | you | host, if agreed | you |
| Monitoring | ask | you | host | you |

Whatever the model, write down who does each task ([Contracts and SLA basics](contracts-and-slas-basics.md)).

## Which model suits which organization

| Organization | Often a good fit | Why |
|---|---|---|
| Small business, small budget, no technical staff | managed hosting, or shared hosting with a partner | someone else runs the machine |
| Small store with seasonal peaks | VPS with Velocity, managed or with a partner | Velocity's response cache handles cached pages without a worker (`README.md`) |
| Nonprofit or association with volunteers | managed or shared hosting | few duties left to volunteers |
| Company with its own IT team | VPS or dedicated | full control, Velocity, own procedures |
| Several brands or sites | one VPS or dedicated server with multisite | one installation, many sites ([Multisite](../multisite/README.md)) |
| Organization already running containers | containers or Kubernetes | fits existing operations |
| Sites that need strict separation between customers | one instance per site, each as its own user | "a single server process serving several sites is a routing convenience, not a security boundary" (`README.md`) |

These are qualitative suggestions, not rules. Check each against your own requirements and quotes.

## Changing model later

Moving a site between hosts or models is documented and does not change its version
([Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md)). Changing the database engine
is possible as well ([Changing the database engine](../../hosting/migrations/changing-the-database-engine.md)). So
start with the simplest model that meets today's needs, and move when measurements say so.

## Related pages

- [Choosing hosting](choosing-hosting.md)
- [Hosting cost drivers](../costs/hosting-cost-drivers.md)
- [Clustering and high availability](../reliability/clustering-and-high-availability.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
