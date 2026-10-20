---
title: Self-hosting on premise
audience: government, admin, project-manager
category: public-sector
order: 30
summary: What a public body needs to run Exponential on its own servers: the requirements, the install paths, serving with Velocity or a traditional web server, the database, the regular operations, and the skills to plan for.
keywords: self-hosting, on premise, own data center, install, requirements, PHP, Velocity, Apache, database, operations, backups, sovereignty
type: help
---
# Self-hosting on premise

This page is for administrators and IT managers who plan to run Exponential in their own data center. Exponential is
a PHP application with a database and a writable file area; it needs no license server and no connection to a vendor
to run. The details are in the installation book and the hosting help, linked below.

## Contents

1. [What you need](#what-you-need)
2. [Getting the code](#getting-the-code)
3. [Serving the site](#serving-the-site)
4. [The database](#the-database)
5. [Operations you take on](#operations-you-take-on)
6. [Skills to plan for](#skills-to-plan-for)

## What you need

| Part | Where the requirements are |
|---|---|
| Operating system | [OS support](../../os-support.md) |
| PHP version and extensions | [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md), [2. Requirements](../../../install/02-requirements.md) |
| Database | [Database support](../../database-support.md) |
| Disk for content files and caches | [Sizing memory, CPU and disk](../../hosting/basics/sizing-memory-cpu-and-disk.md) |
| Writable directories | [Writable directories and permissions](../../hosting/basics/writable-directories-and-permissions.md) |

## Getting the code

The code is public; the install methods are described in [Choosing an install method](../../../install/04-choosing-an-install-method.md)
and [Getting the code](../../../install/03-getting-the-code.md). For a server without internet access, prepare the
installation with Composer on a machine that has it and copy the result; check the chosen method's chapter for what
it fetches.

## Serving the site

Two ways, both documented:

- **Velocity**, the PHP application server of the project (MIT licensed), which serves Exponential itself
  ([Velocity features](../../../features/6.0/velocity-packages-and-binaries.md)).
- **A traditional web server** with PHP-FPM, for example Apache with the shipped rewrite rules
  ([8. Serving the site](../../../install/08-serving-the-site.md)).

## The database

SQLite 3 is the default and needs no database server; PostgreSQL is the recommended database server. All six
supported databases can run on your own machines ([Database support](../../database-support.md)).

## Operations you take on

| Task | Documentation |
|---|---|
| Cronjobs (workflows, notifications, audit, search index) | [Cronjobs](../../../guides/cronjobs.md) |
| Backups and restore tests | [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md), [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md) |
| Updates and upgrades | [Upgrading](../../../guides/upgrading.md), [Upgrade check](../../../guides/upgrade-check.md) |
| Security hardening | [13. Security hardening](../../../install/13-security-hardening.md), [Security hardening](../../security-hardening.md) |
| Logs and monitoring | [Monitoring overview](../../hosting/operations/monitoring-overview.md) |
| Mail delivery | [Mail delivery](../../hosting/operations/mail-delivery.md) |

## Skills to plan for

At least two people who can: operate Linux servers and PHP, run and restore the database, apply updates with
Composer or the **Updates and packages** page, and read the logs. Training courses are listed in the
[course catalogue](../../trainers/course-catalogue.md). If you contract operations out, keep one person in-house who
can direct the provider (see "Keeping the skills in house").

## Related pages

- [Operating a site](../../../guides/operating-a-site.md)
- [Hosting companies start here](../../hosting/hosting-companies-start-here.md)
- [Choosing hosting, database and OS](../../leadership/choosing-hosting-database-and-os.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
