---
title: "FAQ: hosting and data location"
audience: government, project-manager, hosting, admin
category: public-sector
order: 50
summary: Answers to the hosting questions public bodies ask about Exponential - can it run on our own servers or in our country, which hosting models, databases and web servers, what the server needs, outbound connections and who operates it.
keywords: hosting FAQ, data location, data residency, on-premise, cloud, sovereign, own data center, requirements, databases, web servers, Velocity, operating systems, public sector
type: help
---
# FAQ: hosting and data location

This page answers the hosting questions public bodies ask before choosing where an Exponential site runs. The answers
come from [2. Requirements](../../../install/02-requirements.md), [Database support](../../database-support.md) and the
[hosting section](../../hosting/hosting-companies-start-here.md) of this help.

> **Note** This page describes features and common practice; it is not legal advice. Data location rules depend on
> your jurisdiction; check them with your legal counsel.

## Contents

1. [Can we host it ourselves, in our own country?](#can-we-host-it-ourselves-in-our-own-country)
2. [Is there a cloud service from the project?](#is-there-a-cloud-service-from-the-project)
3. [What does a server need?](#what-does-a-server-need)
4. [Which databases are supported?](#which-databases-are-supported)
5. [Which web servers?](#which-web-servers)
6. [Which hosting models fit?](#which-hosting-models-fit)
7. [Does the software call home?](#does-the-software-call-home)

## Can we host it ourselves, in our own country?

Yes. Exponential is free software you install wherever you want: your own data center, a national cloud, a local host.
Data location is a property of where you install it and where backups go, not of the software
([Choosing a hosting model](../../hosting/models/choosing-a-hosting-model.md)). State the location in the hosting
contract, including backups.

## Is there a cloud service from the project?

The project itself does not run one. 7x, the company that maintains Exponential, offers hosting and services on its own
terms ([Who is 7x?](../../who-is-7x.md#services-and-hosting)); partners and hosting companies offer their own
([Hosting partners](../../partners/hosting-partners.md)).

## What does a server need?

PHP 8.0 or later (8.1 or later for Velocity; 8.5 is the newest tested), Composer 2, a supported database or SQLite,
and GD or ImageMagick; a web server is optional with Velocity ([2. Requirements](../../../install/02-requirements.md#21-the-checklist),
[Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)). Sizing is covered in
[Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md).

## Which databases are supported?

In the official order: SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL. Microsoft SQL Server is
possible with additional work ([Database support](../../database-support.md)).

## Which web servers?

Exponential Velocity (its own PHP application server), Apache or nginx with PHP-FPM, FrankenPHP, and others described
for hosts (Caddy, LiteSpeed, IIS) ([8. Serving the site](../../../install/08-serving-the-site.md),
[Choosing a web server](../../hosting/servers/choosing-a-web-server.md)).

## Which hosting models fit?

Shared hosting, a virtual server, dedicated servers, containers, Kubernetes, platform as a service and managed
hosting are all described ([Hosting models](../../hosting/models/README.md)). For several web nodes you need a
database server and shared storage ([Several web nodes](../../hosting/operations/several-web-nodes.md)).

## Does the software call home?

No document in the repository describes Exponential sending data to the project. The administration loads its scripts
locally ([Privacy standards](../../../standards/privacy.md#gdpr)). Some features connect out when you use them:
update checks to package servers (**Setup > Updates and packages**), the AI assistant to its provider, and third-party
extensions when activated. Ask your supplier to list every outbound connection of your configuration, and restrict
outbound traffic at the firewall to that list.

## Related pages

- [Government FAQ](README.md)
- [Hosting companies: start here](../../hosting/hosting-companies-start-here.md)
- [Operating system support](../../os-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
