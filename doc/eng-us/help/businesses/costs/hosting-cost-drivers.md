---
title: Hosting cost drivers
audience: businesses, project-manager, hosting, admin
category: business-solutions
order: 80
summary: Which technical choices make hosting an Exponential site cheaper or dearer - hosting model, database, engine, number of sites and servers, storage, caching, optional services, backups and staff time - with the facts from the hosting documentation and a worked monthly estimate from example assumptions.
keywords: hosting cost, cost drivers, hosting model, shared hosting, VPS, dedicated, managed hosting, SQLite, PostgreSQL, Velocity, storage, backups, Solr, Valkey, multisite, sizing, budget
type: help
---
# Hosting cost drivers

This page is for owners and managers who compare hosting offers, and for the project manager who must explain why
one offer costs more than another. It names the choices that move the hosting bill, with the facts behind each from
the hosting documentation, and shows a worked monthly estimate. The technical detail is in the
[hosting help](../../hosting/hosting-companies-start-here.md).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).

> **Note**
> The project publishes no hosting prices and no sites-per-server figures
> ([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md) says so). The estimate below uses
> only **Example assumptions** in a neutral currency unit. Use real quotes for your budget.

## Contents

1. [The drivers at a glance](#the-drivers-at-a-glance)
2. [Hosting model](#hosting-model)
3. [Database](#database)
4. [Engine and web server](#engine-and-web-server)
5. [Sites, servers and storage](#sites-servers-and-storage)
6. [Optional services](#optional-services)
7. [People: the hidden driver](#people-the-hidden-driver)
8. [A worked monthly estimate](#a-worked-monthly-estimate)
9. [Questions to put to a host](#questions-to-put-to-a-host)

## The drivers at a glance

| Driver | Usually cheaper | Usually dearer |
|---|---|---|
| Hosting model | Shared hosting, one small VPS | Managed hosting, several servers, Kubernetes |
| Database | SQLite 3 in a file, no server | A database server, or a cluster of them |
| Engine | One Velocity instance or one PHP-FPM pool | One instance per site for isolation, several web nodes |
| Sites | Several sites in one installation | One installation per site |
| Storage | Few media files, sensible backup retention | Large media library, long retention, copies off site |
| Services | Built-in search, file caches | Solr, Valkey or Redis, a CDN |
| People | Self-service with documented runbooks | 24-hour response, managed updates |

## Hosting model

The project compares shared hosting, VPS, dedicated servers, containers and Kubernetes without prices
([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)). For cost, the facts that matter:

- Shared hosting usually does not allow a long-running process, so Velocity is usually not possible there; Apache or
  LiteSpeed with `.htaccess` serve the site instead (`README.md`, "When a traditional server is still the right
  answer").
- A VPS or dedicated server allows Velocity and gives you root, but you or your host must run it.
- Managed hosting moves the operations work to the host; you pay for it in the plan instead of in staff time
  ([Managed hosting](../../hosting/models/managed-hosting.md)).

[Hosting models compared](../choosing/hosting-models-compared.md) puts this in business terms.

## Database

- SQLite 3 is the default and needs no database server (`README.md`, "Requirements"). It suits a site served from
  one machine ([Database support](../../database-support.md)).
- You need a database server, PostgreSQL first, "when more than one machine must write the database"
  ([Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)). A
  server adds a machine or a service to pay for, back up and patch.
- Several web nodes for one site need a database server and shared storage for `var/storage`
  ([Several web nodes](../../hosting/operations/several-web-nodes.md)).

## Engine and web server

- Velocity "needs nothing installed beyond PHP" (`README.md`, "Requirements") and serves HTTPS itself, including
  certificates from Let's Encrypt (`README.md`).
- One Velocity worker holds about 10 MB of private memory, as measured in the project
  ([Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md)).
- Cached pages cost little CPU: the README reports cached pages answered in 0.36 ms with no worker involved
  (`README.md`, "In short"). Rendered pages are where CPU goes, so good caching lets a smaller machine carry more.
- Strong isolation between several customers means "one Velocity instance per site behind a proxy, each as its own
  operating-system user" (`README.md`), which costs more memory than one shared process.

## Sites, servers and storage

- Multi-site support is built in: several sites can share one installation, one set of updates and one backup job
  ([One installation, many sites](../multisite/one-installation-many-sites.md)).
- Disk grows with media: images and files in `var/storage`, image variations generated on first use, logs and the
  audit trail ([Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md)).
- Backups need room for the database, uploaded files, settings, keys and the audit trail
  ([Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md)); retention multiplies that.

## Optional services

| Service | When you need it | Read |
|---|---|---|
| Solr through `ezfind` | Large sites with advanced search needs; the built-in search is the default | [Search backends](../../hosting/basics/search-backends.md) |
| Valkey or Redis | Shared caches across several web nodes (`sevenx_valkey_cache`) | [Valkey and Redis](../../hosting/performance/valkey-and-redis.md) |
| A CDN | Visitors far from the server, heavy media | [CDN](../../hosting/performance/cdn.md) |
| A mail delivery service | Many newsletters or strict delivery needs | [Mail delivery](../../hosting/operations/mail-delivery.md) |

Each is optional. Leave it out of the budget until a measured need appears.

## People: the hidden driver

The plan price is often smaller than the time someone spends running the site: updates, backups, restore tests,
watching logs. Count those hours (see [Total cost of ownership](total-cost-of-ownership.md)) or buy them as part of
a managed plan. The [Monthly operations checklist](../templates/monthly-operations-checklist.md) shows what the work
is.

## A worked monthly estimate

A single company site on a VPS with Velocity and SQLite. Every input is an **Example assumption**.

| Input | Value |
|---|---|
| Example assumption: VPS plan | 40 currency units a month |
| Example assumption: off-site backup storage | 10 currency units a month |
| Example assumption: external uptime monitoring | 5 currency units a month |
| Example assumption: administrator time | 3 hours a month at 50 currency units an hour |
| Database server | 0 (SQLite 3, fact) |
| TLS certificate | 0 if Velocity obtains one from Let's Encrypt (fact: `README.md`) |

Step by step:

1. Infrastructure: 40 + 10 + 5 = **55**.
2. Staff time: 3 x 50 = **150**.
3. Monthly total: 55 + 150 = **205**.
4. Yearly total: 205 x 12 = **2,460**.

Now change one driver: the site grows and needs two web nodes with a database server. Example assumptions: a second
VPS at 40, a managed database at 60, and 2 more hours of administration a month (100). New monthly total:
205 + 40 + 60 + 100 = **405**, about twice the first. That is why you should add servers only when measurements show
the need ([Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md#measure-do-not-guess)).

## Questions to put to a host

- [ ] Which PHP versions can I choose per site, and may I run a long-running process for Velocity?
- [ ] Is SQLite fine for my plan, or will you require a database server?
- [ ] What is included: backups, restore tests, monitoring, updates, TLS?
- [ ] How long are backups kept, and where?
- [ ] Which response times do you commit to, in writing?
- [ ] What does it cost to move away, and in which format do I get my data?

More questions: [Questions to ask a partner](../choosing/questions-to-ask-a-partner.md) and
[Hosting requirements for a website](../../creators/website-creators/hosting-for-a-website.md#questions-for-a-hosting-provider).

## Related pages

- [Choosing hosting](../choosing/choosing-hosting.md)
- [Designing hosting plans](../../hosting/business/designing-hosting-plans.md)
- [Velocity for businesses](../reliability/velocity-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
