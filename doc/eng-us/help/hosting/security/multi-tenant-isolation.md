---
title: Multi-tenant isolation
audience: hosting
category: hosting
order: 110
summary: How far one customer's Exponential site is kept from another's on shared infrastructure: what Exponential isolates and what it leaves to the operating system, the boundary each setup gives (shared process, one pool or Velocity per site, containers, separate machines), multi-site installations, shared paths such as TemporaryDir, databases, caches and logs, and a decision table.
keywords: multi-tenant, isolation, tenant, customer separation, shared hosting, PHP-FPM pool, Velocity instance, operating-system user, containers, multi-site, siteaccess, TemporaryDir, open_basedir, database user, sessions, caches, logs, security boundary
featured: true
type: help
---
# Multi-tenant isolation

This page is for the product managers, architects and security staff of a host who decide how customers share
machines. The question it answers: if one customer's site is compromised, or simply badly written, what else can it
reach? The short answer from the project's own README: **a single server process serving several sites is a routing
convenience, not a security boundary.** The boundary comes from the operating system: separate users, separate
processes, separate databases.

## Contents

1. [What Exponential isolates, and what it does not](#what-exponential-isolates-and-what-it-does-not)
2. [The setups and their boundaries](#the-setups-and-their-boundaries)
3. [Several sites on one installation](#several-sites-on-one-installation)
4. [Shared paths to watch](#shared-paths-to-watch)
5. [Databases](#databases)
6. [Caches, sessions and logs](#caches-sessions-and-logs)
7. [Choosing](#choosing)
8. [Checklist](#checklist)

## What Exponential isolates, and what it does not

Inside one installation, Exponential separates **users** with roles and policies, **siteaccesses** with their own
settings, and can give each siteaccess its own session cookie (`SessionNameHandler=custom`). Those are application
boundaries: they hold as long as the PHP code is not compromised.

Between **installations**, Exponential provides nothing of its own and needs nothing special: each installation is a
directory, a database and a set of processes. Whether one customer can read another's files depends only on how the
host runs those processes. The README says it plainly: if several customers must not be able to read each other's
data, run one Velocity instance per site behind a proxy, each as its own operating-system user, because the process
that serves one site can read every file the others hold.

And from [Chapter 8](../../../install/08-serving-the-site.md#81-the-choice-in-one-table): when "several customers on
one machine need operating-system isolation from each other (run one Velocity per site, each as its own user, or a
traditional server with one PHP-FPM pool per site)".

## The setups and their boundaries

| Setup | Boundary between customers | Notes |
|---|---|---|
| One installation, several customers as siteaccesses | **none** at the operating-system level: one code base, one PHP user, usually one database | only for sites of one organization ([Several sites on one installation](#several-sites-on-one-installation)) |
| Several installations, one shared PHP user or one shared Velocity | **none**: the shared process can read every installation | avoid for different customers |
| One PHP-FPM pool per site, each as its own user | operating-system user and file permissions | the classic shared-hosting model; [Per-customer users and pools](per-customer-users-and-pools.md) |
| One Velocity instance per site, each as its own user | operating-system user; a separate process tree | behind a proxy that routes by host name; [Velocity beside Apache or nginx](../servers/velocity-beside-apache-or-nginx.md) |
| One container per site | the container runtime's isolation, plus its own user | [Containers](../models/containers-docker-and-podman.md), [Kubernetes](../models/kubernetes.md) |
| One virtual or physical machine per customer | the hypervisor or hardware | [VPS hosting](../models/vps-hosting.md), [Dedicated servers](../models/dedicated-servers.md) |

Velocity's own process model inside one site: the parent may stay root to bind ports and read certificates, and every
worker gives up root before any application code runs; `[ServerSettings] User` and `Group` decide whom it becomes,
and root workers are refused unless `AllowRootWorkers=enabled`
([8.3.5](../../../install/08-serving-the-site.md#835-address-ports-user-and-group)). Persistent workers serve many
requests of the **same** site; `ForkPerRequest=enabled` gives one request per worker, "the isolation of PHP-FPM"
([8.3.7](../../../install/08-serving-the-site.md#837-persistent-workers-and-keepglobals)). Neither setting separates
customers; separate instances do.

## Several sites on one installation

Exponential can serve many sites from one installation: each site a siteaccess or a site extension with its own
`VarDir`, its own logs (`UseGlobalLogDir=disabled`, `LogVarDir`), caches (`CacheVarDir`) and INI cache
(`INICacheDir=site`) ([Logs, INI cache and expiry timestamps per site](../../../features/6.0/multi-site-log-and-cache-paths.md)).
That keeps the sites' data apart for operations, not for security: one PHP user writes all of them, and an
administrator with `setup/*` policies on one siteaccess works on the shared installation. Use it for the sites of one
customer, or one organization, not to separate unrelated customers. Setup:
[Multisite on one installation](../provisioning/multisite-on-one-installation.md).

## Shared paths to watch

Even with one user per site, a few defaults point to places every user of the machine shares:

| Path or setting | Default | What to do |
|---|---|---|
| `site.ini [FileSettings] TemporaryDir` | `/tmp/` | a directory of the site's own, below `var/` ([13.11](../../../install/13-security-hardening.md#1311-file-permissions-and-ownership)) |
| `[FileSettings]` permissions | `0777` directories, `0666` files | `0770` / `0660` with the site's group |
| PHP's session storage | `session.save_handler` of `php.ini` (files) when `[Session] Handler` is empty | a session directory per pool, or the database session handler |
| PHP's `upload_tmp_dir` | the system default | per pool, inside the site's area |
| Velocity's log and cache directories | `var/vc/qbix/log` (`0640` files, `0750` directory) | inside each installation already |
| Velocity configuration | `/etc/vc/sites-available/<site>.conf`, mode `0600`, one per installation | keep the mode; it holds certificate paths and tokens |

Panels often also set `open_basedir` per account. The setup wizard only warns about it ("open_basedir is in use and can
give problems running Exponential due to bugs in some PHP versions"); test a site under your panel's setting before
relying on it ([Shared hosting](../models/shared-hosting.md)).

## Databases

Give each installation its own database and its own database user with rights on that database only. The connection,
including the password, is in the installation's `settings/override/site.ini.append.php`; keep that file `0640` or
stricter so other users cannot read it ([Databases and users](../panels/databases-and-users.md)). The supported
databases, in the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle ·
6. MySQL; Microsoft SQL Server is known to be possible with additional work at this time
([Database support](../../database-support.md)). With SQLite 3 the database is a file below the installation
(`var/storage/sqlite3/` by default), so file permissions are the whole boundary.

## Caches, sessions and logs

- **Caches** are per installation (`var/`); with a shared reverse proxy or CDN in front, make sure keys include the
  host name and that signed-in pages are never stored (13.10, "Caching proxies").
- **Sessions**: one cookie per siteaccess with `SessionNameHandler=custom`; different installations on different host
  names never share cookies unless a cookie domain is set wider than the host.
- **Logs** name visitors. Keep each site's logs inside its own tree and readable only by its user; a central log
  platform that collects them needs its own access rules per customer.

## Choosing

| Customer need | Suggested model |
|---|---|
| Small sites, price-sensitive, no shell | one PHP-FPM pool and user per site, shared machine |
| Velocity performance, still on a shared machine | one Velocity instance per site, each as its own user, behind a proxy |
| Regulated data, strict separation demanded by contract | own container or own virtual machine, own database server or instance |
| Many sites of one organization | one installation with several sites is possible; separate installations if departments must not see each other |

These are options; the host chooses and states the model in its plans
([Choosing a hosting model](../models/choosing-a-hosting-model.md)).

## Checklist

- [ ] Every customer's installation runs as its own operating-system user, never a shared PHP user or a shared
      Velocity.
- [ ] Code read-only to that user; `var/` and `settings/` writable only by it and its group.
- [ ] No world-writable permissions; `TemporaryDir`, sessions and uploads in per-site directories.
- [ ] One database and one database user per installation; `settings/override` mode `0640` or stricter.
- [ ] Multi-site installations used only within one organization.
- [ ] Shared proxies and CDNs never cache signed-in pages.
- [ ] The isolation model written into the plan description.

## Related pages

- [Security for hosts: overview](README.md)
- [Per-customer users and pools](per-customer-users-and-pools.md)
- [Choosing a hosting model](../models/choosing-a-hosting-model.md)
- [Shared hosting](../models/shared-hosting.md)
- [Chapter 8: File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
