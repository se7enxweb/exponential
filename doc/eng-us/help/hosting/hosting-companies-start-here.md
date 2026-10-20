---
title: Hosting companies: start here
audience: hosting
category: hosting
order: 1
summary: The landing page for hosting companies and service providers that offer Exponential hosting: what the platform needs, how this section is organized, a first-week plan for a new offer and links to every hosting page.
keywords: hosting, hosting company, provider, start here, landing, offer, requirements, PHP, database, Velocity, FrankenPHP, Apache, nginx, Plesk, cPanel, shared hosting, VPS, dedicated, containers, Kubernetes, backups, monitoring, security, installers, updates, SLA, support, migration, checklists, runbooks
featured: true
type: help
---
# Hosting companies: start here

This section of the help is for **hosting companies and service providers** that host Exponential sites for their
customers, or want to: product managers who design an "Exponential hosting" offer, system administrators who build
and run the servers, support staff who answer customers, and the people who market the offer. It answers: what an
Exponential site needs from a server, which hosting models and server stacks fit, how to install, update, back up,
monitor and secure customer sites, and how to package all of that as a product.

![Setup in the administration: content model, site and URLs, workflows, users and messages, system and updates, each as a card](../images/hosting/setup-overview.webp)

Every command and setting in this section was checked against the source code and the installation book of this
release. Where the sources give no figure (prices, response times, capacity per server), the pages give none either:
they show you how to measure and decide.

## Contents

1. [Exponential in one minute, for hosts](#exponential-in-one-minute-for-hosts)
2. [What a server must provide](#what-a-server-must-provide)
3. [How this section is organized](#how-this-section-is-organized)
4. [Your first week](#your-first-week)
5. [Reading paths by role](#reading-paths-by-role)
6. [The pages you will use most](#the-pages-you-will-use-most)
7. [Where to get help](#where-to-get-help)

## Exponential in one minute, for hosts

- **It is a PHP application.** Exponential is "a professional PHP application framework with advanced CMS (content
  management system) functionality" (`README.md`). It runs on PHP 8.0 to 8.5, on almost any operating system that
  runs PHP 8 ([Operating system support](../os-support.md)).
- **It is free software.** The code is licensed under the GPL v2 or later and costs nothing; you can install it for
  any number of customers. The documentation, including this page, is under the GNU FDL 1.3
  ([Free documentation](../free-documentation.md)).
- **It has a long history.** Exponential continues the code base of eZ Publish, a product its creator discontinued
  after almost 18 years; 7x and the Exponential Foundation maintain it today
  ([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).
- **It needs a database, a web server or engine, and writable storage.** That is the whole footprint; there is no
  separate application server to license.
- **It brings its own engine.** Exponential Velocity is a persistent-worker web server written in PHP, recommended
  for every stage; FrankenPHP is a production-ready alternative; Apache or nginx with PHP-FPM remain fully supported
  and are what shared hosting usually offers ([Chapter 8](../../install/08-serving-the-site.md)).

## What a server must provide

| Need | Minimum | Notes |
|---|---|---|
| PHP | 8.0 (Velocity included) | the newest 8.x your system offers; 8.5 is supported |
| Database | one of the six | in the official order: 1. SQLite 3 (default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work at this time. |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` set, `file_uploads` on | checked by the setup wizard |
| Web server | none required | Velocity serves HTTP and HTTPS itself; or Apache/nginx with PHP-FPM |
| Composer | 2.x | for installs and updates from source |
| Image conversion | GD or ImageMagick | both recommended |
| Writable directories | `var/` (cache, log, storage, autoload) and the `settings/` directories the setup writes | the setup wizard's `directory_permissions` check lists them; details in [Writable directories and permissions](basics/writable-directories-and-permissions.md) |

Sources: [Chapter 2: Requirements](../../install/02-requirements.md), [Database support](../database-support.md).
The full picture is on [Requirements at a glance](basics/requirements-at-a-glance.md).

## How this section is organized

| Section | What it covers |
|---|---|
| [Hosting basics](basics/README.md) | requirements, PHP, the six databases in order, operating systems, Composer and Git, permissions, sizing, media, search |
| [Hosting models](models/README.md) | shared, VPS, dedicated, containers, Kubernetes, platform as a service, managed hosting |
| [Control panels](panels/README.md) | Plesk (what the project's own development server runs), cPanel, DirectAdmin, ISPConfig, Webmin; PHP pools, cron, databases, certificates |
| [Web servers and engines](servers/README.md) | Apache, nginx, Caddy, LiteSpeed, IIS, Exponential Velocity, FrankenPHP |
| [Performance](performance/README.md) | PHP-FPM tuning, OPcache, the caching layers, Velocity's response cache, HTTP caching, reverse proxies, CDN, Valkey, static cache, benchmarking |
| [Operations](operations/README.md) | storage and clustering, backups and restore, monitoring and logs, cron jobs, mail, updates with ezupdate, deploying, maintenance mode |
| [Security](security/README.md) | hardening, TLS, Content Security Policy, request-shield, admin access, two-factor sign-in, audit trail, multi-tenant isolation, compliance |
| [Provisioning](provisioning/README.md) | one-click installers: `exp:install`, the kickstarter, the setup wizard; site packages; automation; multisite |
| [The hosting business](business/README.md) | plans, SLA and support options, billing options, marketing, partner programs, licensing |
| [Migrating customers](migrations/README.md) | bringing customer sites to your hosting, from older versions and other platforms |
| [Checklists](checklists/README.md) | new server, go-live, monthly operations, security review, offboarding |
| [Runbooks](runbooks/README.md) | site down, slow site, disk full, restore, compromised site, PHP upgrade, certificate expiry, Velocity will not start |
| [Glossary for hosts](glossary-for-hosts.md) | the terms used in this section |

## Your first week

A plan for a team that is about to add Exponential to its offer.

| Day | Do | Read |
|---|---|---|
| 1 | Decide which hosting model and stack your first offer uses | [Choosing a hosting model](models/choosing-a-hosting-model.md), [Choosing a web server](servers/choosing-a-web-server.md) |
| 2 | Build one test server from the checklist; install a site with `exp:install` | [New server checklist](checklists/new-server.md), [One-click installers](provisioning/one-click-installers.md) |
| 3 | Harden it and put the logs where your monitoring can read them | [Hardening for hosts](security/hardening-for-hosts.md), [Monitoring overview](operations/monitoring-overview.md) |
| 4 | Back it up, then restore it on a second machine | [What to back up](operations/backups-what-to-back-up.md), [Restore and restore tests](operations/restore-and-restore-tests.md) |
| 5 | Measure it, write down the limits of a plan, and draft the offer | [Benchmarking a hosted site](performance/benchmarking-a-hosted-site.md), [Designing hosting plans](business/designing-hosting-plans.md) |

> **Tip**
> Do day 4 for real. A backup that has never been restored is a hope, not a backup; the installation book's
> first-week checklist asks for a backup "taken **and restored** on another machine"
> ([10.15](../../install/10-after-installing.md#1015-a-checklist-for-the-first-week)).

## Reading paths by role

- **Product manager at the host:** this page, [Choosing a hosting model](models/choosing-a-hosting-model.md),
  [Designing hosting plans](business/designing-hosting-plans.md), [SLA options](business/sla-options.md),
  [Support tiers](business/support-tiers.md), [Marketing Exponential hosting](business/marketing-exponential-hosting.md).
- **System administrator:** [Requirements at a glance](basics/requirements-at-a-glance.md),
  [Databases in the official order](basics/databases-in-the-official-order.md),
  [Velocity for hosts](servers/velocity-for-hosts.md), [PHP-FPM tuning](performance/php-fpm-tuning.md),
  [Caching layers](performance/caching-layers.md), [Multi-tenant isolation](security/multi-tenant-isolation.md),
  the [checklists](checklists/README.md) and [runbooks](runbooks/README.md).
- **Support staff:** [Monitoring overview](operations/monitoring-overview.md),
  [Log viewer and rotation](operations/log-viewer-and-rotation.md), [Site down](runbooks/site-down.md),
  [Slow site](runbooks/slow-site.md), [When something goes wrong](../when-something-goes-wrong.md).
- **Migration team:** [Migrating customers to your hosting](migrations/migrating-customers-to-your-hosting.md) and
  the installation book's chapters [14](../../install/14-migrating-from-4x.md) to
  [17](../../install/17-migration-reference.md).

## The pages you will use most

- [Requirements at a glance](basics/requirements-at-a-glance.md)
- [Databases in the official order](basics/databases-in-the-official-order.md)
- [Choosing a hosting model](models/choosing-a-hosting-model.md)
- [Choosing a web server](servers/choosing-a-web-server.md)
- [Velocity for hosts](servers/velocity-for-hosts.md)
- [Caching layers](performance/caching-layers.md)
- [What to back up](operations/backups-what-to-back-up.md)
- [Hardening for hosts](security/hardening-for-hosts.md)
- [Multi-tenant isolation](security/multi-tenant-isolation.md)
- [One-click installers](provisioning/one-click-installers.md)
- [Go-live checklist for a customer site](checklists/new-customer-site-go-live.md)

## Where to get help

- **This help.** Search it from the box at the top of the help dashboard; filter by the Hosting audience.
- **Bugs and security issues.** Report a software bug as described on [When something goes wrong](../when-something-goes-wrong.md);
  report a security problem privately, as `SECURITY.md` in the repository describes, never in a public issue
  ([Security updates and disclosure](security/security-updates-and-disclosure.md)).
- **The project and its maintainers.** 7x and the Exponential Foundation are introduced on
  [Who is 7x?](../who-is-7x.md) and [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md);
  how hosts work with them is on [Working with the project and 7x](business/working-with-the-project-and-7x.md).
- **Other hosts and partners.** The Partners section of this help describes the partner programs; the community
  places are listed on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#community-places).

**Public demos.** Hosting a public demo of Exponential, with a published administrator sign-in? Run it in demo mode (`EXP_DEMO_MODE=1` for that site only): [Demo mode for hosts](security/demo-mode-for-hosts.md), [Recipe: run a public demo on LiteSpeed and Plesk](servers/run-a-public-demo-on-litespeed-and-plesk.md), [Demo mode problems](runbooks/demo-mode-problems.md).

## Related pages

- [Choosing hosting, database and operating system](../leadership/choosing-hosting-database-and-os.md) (for project managers)
- [Database support](../database-support.md)
- [Operating system support](../os-support.md)
- [Security hardening](../security-hardening.md)
- [The installation book](../../install/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
