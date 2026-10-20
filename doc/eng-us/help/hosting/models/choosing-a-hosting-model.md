---
title: Choosing a hosting model
audience: hosting
category: hosting
order: 20
summary: A comparison of shared hosting, VPS, dedicated servers, containers and Kubernetes for Exponential, built from what the project's sources say, with the questions that decide which model a hosting offer should use.
keywords: hosting model, comparison, shared hosting, VPS, dedicated server, containers, Docker, Podman, Kubernetes, Velocity, PHP-FPM, SQLite, PostgreSQL, isolation, shell access, long-running process, decision
featured: true
type: help
---
# Choosing a hosting model

This page is for product managers and system administrators at hosting companies who must decide which hosting
models to offer for Exponential. It answers: how shared hosting, VPS, dedicated servers, containers and Kubernetes
compare for an Exponential site, which facts in the project's sources drive the comparison, and which questions
decide the model for one offer or one customer. It gives no prices and no sites-per-server figures: the sources state
none, and the right numbers come from measuring your own machines.

## Contents

1. [The comparison table](#the-comparison-table)
2. [The five questions that decide](#the-five-questions-that-decide)
3. [Web server and engine per model](#web-server-and-engine-per-model)
4. [Database per model](#database-per-model)
5. [Isolation between customers](#isolation-between-customers)
6. [Mixing models](#mixing-models)
7. [A decision record](#a-decision-record)

## The comparison table

| | Shared hosting | VPS | Dedicated server | Containers (Docker, Podman) | Kubernetes |
|---|---|---|---|---|---|
| Typical customer access | panel, FTP, often no shell | root or a sudo user | root | the image and its volumes | manifests, images, volumes |
| Long-running process (Velocity, FrankenPHP) | usually not allowed | yes | yes | yes, the container's main process | yes |
| Usual way to serve | Apache or LiteSpeed with `.htaccess` | Velocity, or Apache/nginx with PHP-FPM | Velocity, or Apache/nginx with PHP-FPM | Velocity in the container | Velocity in the pods, or PHP-FPM behind a web server |
| Install method | the setup wizard in the browser | `exp:install`, the kickstarter or the wizard | as VPS | `exp:install` in the entry point | `exp:install` in a job or entry point |
| SQLite 3 (the default database) | yes, one site per file | yes | yes | one writing container only | one writing replica only |
| Several web nodes for one site | no | no (one machine) | no (one machine) | needs a database server and shared `var/storage` | needs a database server and shared `var/storage` |
| Isolation between customers | per account and PHP-FPM pool, as the host configures it | one machine per customer | one machine per customer | per container | per namespace and pod, as configured |
| Tier in the sources | through the panel: Plesk tier 1, cPanel and DirectAdmin tier 3 | by distribution: tier 2 or 3 | by distribution: tier 1 to 3 | tier 2 (Docker and Podman) | tier 3 |

Sources: [Operating system support](../../os-support.md) (tiers, "Containers, cloud and hosting panels"),
[Chapter 8](../../../install/08-serving-the-site.md#81-the-choice-in-one-table) ("Suits shared hosting"),
[Chapter 4](../../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation) (install methods)
and the root `README.md` ("When a traditional server is still the right answer").

> **Note**
> "Tier" here is the project's own scale: 1 tested here, 2 known to work, 3 expected to work, 4 not possible today.
> A tier 3 entry is "a reasoned expectation, not a test result"
> ([The four tiers](../../os-support.md#the-four-tiers)). Plan a test installation before you sell a tier 3 model.

## The five questions that decide

| Question | If the answer is ... | Then |
|---|---|---|
| May customers run a long-running process? | no | Apache or LiteSpeed with `.htaccess`, or nginx with PHP-FPM; Velocity is usually not possible ([Shared hosting](shared-hosting.md)) |
| Does the site need more than one machine to write the database? | yes | a database server, PostgreSQL first; not SQLite ([Database support](../../database-support.md#the-supported-databases-in-order)) |
| Is the machine's PHP older than 8.1? | yes, for example the stock PHP 8.0 of Red Hat Enterprise Linux 9 | either engine: Velocity runs on PHP 8.0 too ([Chapter 2](../../../install/02-requirements.md#22-php)) |
| Must customers be isolated at the operating-system level? | yes | one PHP-FPM pool or one Velocity instance per site, each as its own user ([Isolation](#isolation-between-customers)) |
| Will the customer have shell access? | no | the setup wizard in the browser installs the site ([Chapter 4](../../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation)) |

## Web server and engine per model

The project recommends Exponential Velocity "for every stage"; FrankenPHP is a production-ready alternative; Apache
and nginx with PHP-FPM are the traditional setups. Chapter 8 names the cases where a traditional server is the
better answer: "when your hosting does not allow long-running processes, when you already run a tuned Apache or
nginx you want to keep, when the machine only has PHP 8.0 ... or when several customers on one machine need
operating-system isolation from each other"
([8.1 The choice in one table](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)).

For a hosting company this translates into:

- **Shared hosting**: Apache (or LiteSpeed) with `.htaccess` and a PHP-FPM pool per account.
- **VPS and dedicated**: Velocity as the default; Apache or nginx when a panel manages the machine or the customer
  wants it. Both can run side by side on the same files during a migration.
- **Containers and Kubernetes**: Velocity as the container's process, since it needs no separate web server and
  serves HTTPS itself; or a web server container in front of PHP-FPM.

Details: [Choosing a web server](../servers/choosing-a-web-server.md) and
[Velocity for hosts](../servers/velocity-for-hosts.md).

## Database per model

The six supported databases, in the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB ·
4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at this time.

| Model | What fits | Why |
|---|---|---|
| Shared hosting | 1. SQLite 3 for small sites; otherwise the database server the panel offers (often 4. MariaDB or 6. MySQL) | SQLite needs no server and no account; it is "a full production database for a site served from one machine" |
| VPS, dedicated | any of the six; 1. SQLite 3 or 2. PostgreSQL first | one machine; the customer's or your standard decides |
| Containers | 1. SQLite 3 with one writing container, or a database server | "SQLite's WAL needs shared memory on one host" |
| Kubernetes with several replicas | a database server, 2. PostgreSQL first | several replicas cannot share one SQLite file over a network volume |

Sources: [Database support](../../database-support.md) and
[Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels). Read
[MongoDB limits](../../database-support.md#mongodb-limits) before you offer 3. MongoDB.

## Isolation between customers

The root `README.md` is explicit: "A single server process serving several sites is a routing convenience, not a
security boundary — the process that serves one site can read every file the others hold." For several customers on
one machine it recommends "one Velocity instance per site behind a proxy, each as its own operating-system user", or
a traditional server with one PHP-FPM pool per site
([Chapter 8](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)).

> **Warning**
> Several customer sites inside one Exponential installation (a multisite) share one code base, one set of
> processes and usually one database. That is a fine setup for one organization's sites, not a boundary between
> customers who must not see each other's data. See [Multi-tenant isolation](../security/multi-tenant-isolation.md).

## Mixing models

Most hosting companies end up offering more than one model. Common combinations, all covered by the sources:

- **Shared hosting for small sites, VPS for growth.** Moving a site is moving its files and its database
  ([Moving a site between hosts](../migrations/moving-a-site-between-hosts.md)).
- **Velocity beside a panel's Apache.** The project's own development server runs Plesk's Apache with PHP-FPM and
  Exponential Velocity on the same installation ([Tier 1](../../os-support.md#tier-1-tested-here)).
- **Containers for staging, a VM for production**, built from the same `exp:install` options or `kickstart.ini`.

## A decision record

```text
Hosting model decision for <offer or customer>       date: <date>     decided by: <names>

Model:              <shared | VPS | dedicated | containers | Kubernetes | PaaS | managed>
Operating system:   <name and version>, tier <1-4>
Panel:              <none | Plesk | other>, tier <1-4 or not placed>
PHP:                <version> web, <version> command line
Serving:            <Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM | other>
Database:           <1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL>
Customer access:    <panel | FTP | shell | none>
Isolation:          <one pool per site | one Velocity per site | one machine per customer>
Backups:            database + var/storage + settings/override + settings/siteaccess, restore tested on <date>
Risks accepted:     <for example a tier 3 system>
```

## Related pages

- [Hosting models: overview](README.md)
- [Shared hosting](shared-hosting.md), [VPS hosting](vps-hosting.md), [Dedicated servers](dedicated-servers.md),
  [Containers: Docker and Podman](containers-docker-and-podman.md), [Kubernetes](kubernetes.md)
- [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)
- [Designing hosting plans](../business/designing-hosting-plans.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
