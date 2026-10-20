---
title: Hosting models: overview
audience: hosting
category: hosting
order: 10
summary: The overview of the hosting models section: which ways of hosting Exponential the sources cover (shared, VPS, dedicated, containers, Kubernetes, platform as a service, managed) and one line on each page.
keywords: hosting models, shared hosting, VPS, virtual server, dedicated server, containers, Docker, Podman, Kubernetes, platform as a service, PaaS, managed hosting, overview
type: help
---
# Hosting models: overview

This section is for product managers and system administrators at hosting companies who decide **in which form**
they offer Exponential: on a shared server, on a virtual or dedicated machine per customer, in containers, on a
platform as a service, or as a managed service. It answers what each model means for an Exponential site, what the
project's own sources say about it, and which questions to settle before a model becomes a product.

Back to [Hosting companies: start here](../hosting-companies-start-here.md).

## Contents

1. [The pages of this section](#the-pages-of-this-section)
2. [What is the same in every model](#what-is-the-same-in-every-model)
3. [What changes between models](#what-changes-between-models)
4. [Where to read next](#where-to-read-next)

## The pages of this section

| Page | What it answers |
|---|---|
| [Choosing a hosting model](choosing-a-hosting-model.md) | One table comparing shared, VPS, dedicated, containers and Kubernetes, with the decisions behind it |
| [Shared hosting](shared-hosting.md) | Running many customer sites on one machine without shell access or long-running processes for the customer |
| [VPS hosting](vps-hosting.md) | One virtual machine per customer or per small group of sites, with root access |
| [Dedicated servers](dedicated-servers.md) | A whole physical machine for one customer or one large site, and when that pays off |
| [Containers: Docker and Podman](containers-docker-and-podman.md) | Images, volumes and the one-writer rule for SQLite |
| [Kubernetes](kubernetes.md) | Several replicas, shared storage, sessions and a database server |
| [Platform as a service](platform-as-a-service.md) | Offerings with a fixed runtime, and what a read-only or ephemeral file system means |
| [Managed hosting](managed-hosting.md) | Taking over updates, backups, monitoring and security for the customer |

## What is the same in every model

Whatever the model, an Exponential site needs the same few things
([Chapter 2: Requirements](../../../install/02-requirements.md)):

- **PHP 8.0 or later** (for Exponential Velocity too) with the required extensions;
- **one database** of the six supported ones, in the official order: 1. SQLite 3 (the default) · 2. PostgreSQL ·
  3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
  at this time; it has no driver in 6.0.x ([Database support](../../database-support.md));
- **writable storage** for `var/` and the `settings/` directories the setup writes;
- **a way to serve HTTP**: Exponential Velocity, FrankenPHP through Velocity, or a traditional web server such as
  Apache or nginx with PHP-FPM ([Chapter 8](../../../install/08-serving-the-site.md));
- **cron**, or another scheduler, for `runcronjobs.php`
  ([Chapter 10, Cronjobs](../../../install/10-after-installing.md#103-cronjobs)).

A complete backup in every model is the database **and** the files `var/storage`, `settings/override` and
`settings/siteaccess` ([Backup and restore](../../database-support.md#backup-and-restore)).

## What changes between models

| Question | Why it matters for Exponential |
|---|---|
| May the customer run a long-running process? | Velocity and FrankenPHP need one; Apache or nginx with PHP-FPM do not. "Shared hosting usually will not" allow one (`README.md`). |
| Is there shell access? | Without it the setup wizard in the browser is the installer ([Chapter 4](../../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation)). |
| Is the file system persistent? | `var/` holds uploads, caches, logs and, with SQLite, the database itself. |
| Do several machines serve one site? | Then SQLite is out and a database server, shared storage for `var/storage` and shared sessions are needed ([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels)). |
| Must customers be isolated from each other? | One PHP-FPM pool or one Velocity instance per site, each as its own operating-system user ([Chapter 8](../../../install/08-serving-the-site.md#81-the-choice-in-one-table)). |
| Who runs updates and backups? | You, the customer, or both; this is what turns hosting into managed hosting. |

> **Note**
> The sources place the hosting models in tiers through the operating system and the panel, not through the model
> itself: cloud virtual machines "are ordinary Linux machines", tier 2 or 3 by their distribution; Kubernetes and
> platform-as-a-service offerings are tier 3, "a reasoned expectation, not a test result"
> ([The four tiers](../../os-support.md#the-four-tiers)).

## Where to read next

- [Hosting basics](../basics/README.md): requirements, PHP, the databases in order, permissions, sizing.
- [Control panels](../panels/README.md): Plesk, cPanel, DirectAdmin, ISPConfig, Webmin and the panel tasks.
- [Web servers and engines](../servers/README.md): Velocity, FrankenPHP, Apache, nginx and others.
- [Security](../security/README.md), especially [Multi-tenant isolation](../security/multi-tenant-isolation.md).

## Related pages

- [Hosting companies: start here](../hosting-companies-start-here.md)
- [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md): the same
  decision from the project manager's side.
- [Operating system support](../../os-support.md) and [Database support](../../database-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
