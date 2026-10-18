---
title: Choosing hosting, database and operating system
audience: project-manager, admin
category: leadership
order: 40
summary: Decision tables for where an Exponential site runs: the six supported databases in order, operating system tiers, Velocity, FrankenPHP, Apache or nginx, install methods and a decision record.
keywords: hosting, database, operating system, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Velocity, FrankenPHP, Apache, nginx, PHP-FPM, Plesk, containers, Docker, Kubernetes, install method, requirements, decision
type: help
---
# Choosing hosting, database and operating system

This page is for project managers who must take or sign off the platform decision, and for administrators who
prepare it. It answers: which databases, operating systems and web servers Exponential supports and in which order
the project recommends them, which install method fits which situation, and which questions decide the choice for
your project. The facts come from [Database support](../database-support.md),
[Operating system support](../os-support.md) and chapters 2, 4 and 8 of the installation book; this page arranges
them as decisions.

## Contents

1. [The decision in five questions](#the-decision-in-five-questions)
2. [Database](#database)
3. [Operating system](#operating-system)
4. [Web server and engine](#web-server-and-engine)
5. [Hosting model](#hosting-model)
6. [Install method](#install-method)
7. [Sizing](#sizing)
8. [Backups and moving later](#backups-and-moving-later)
9. [A decision record to copy](#a-decision-record-to-copy)

## The decision in five questions

| Question | If the answer is ... | Then consider |
|---|---|---|
| Does more than one machine have to write the database? | yes | A database server, PostgreSQL first; not SQLite |
| Does the hosting allow long-running processes? | no (typical shared hosting) | Apache or nginx with PHP-FPM; Velocity usually not |
| Is the machine's PHP older than 8.1? | yes (for example the stock PHP 8.0 of RHEL 9) | A traditional web server; Velocity needs 8.1 |
| Is the organization standardized on one database? | yes | That database, if it is among the six; read its limits |
| Is there shell access on the server? | no | The setup wizard in the browser |

Each row is explained below with its source.

## Database

> **Supported databases (Exponential 6.0.x), in order of preference:** 1. SQLite 3 (the default) · 2. PostgreSQL ·
> 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server is known to be possible with additional work
> at this time. ([Database support](../database-support.md))

All six have a driver, an entry in the setup wizard and a path in the command-line installers
([Database support](../database-support.md#the-supported-databases-in-order)).

### Decision table

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| Needs a database server | no (one file) | yes | yes | yes | yes | yes |
| Several web servers on one database | no | yes | yes | yes | yes | yes |
| Tested in the project's CI | driver unit tests | yes (PostgreSQL 15) | adapter unit tests | no (covered by the MySQL job, same driver) | no | yes (MySQL 8.0) |
| Practical minimum version | 3.33 | 13 | 6.0 server, PHP 8.2 | a release your vendor supports | 19c | a release your vendor supports |
| Choose it when | one machine; simplest operation | you need a database server; recommended server | your organization runs MongoDB | you want a MySQL-compatible server | your organization runs Oracle | you already run MySQL |

Sources: "At a glance" and "Versions, checked against the code" on [Database support](../database-support.md).

### What the sources say about each choice

- **SQLite 3** is the default and "with the driver Exponential ships it is a full production database for a site
  served from one machine". Choose a server instead "when more than one machine must write the database", "when many
  editors publish large changes at the same moment all day, or when you need replication or database-level users and
  grants" ([Database support](../database-support.md)).
- **PostgreSQL** is "the recommended database server: strict SQL, row-level locking, replication and point-in-time
  recovery of its own, and it is tested in CI on every push".
- **MongoDB** has documented limits: hand-written SQL it cannot translate returns an empty result, some cronjobs are
  not ported, `eztags` is not usable, and the shop checkout and `ezflow` scheduling are untested end to end
  ([MongoDB limits](../database-support.md#mongodb-limits)). Check every extension you plan to use against them.
- **MariaDB** runs on the same driver as MySQL and is "the MySQL-compatible server the project prefers".
- **Oracle** (19c and later) is supported through the `ezoracle` extension, present in every installation.
- **MySQL** is last in the order "not because it works less well, but because the project recommends the others first
  for new sites".
- **Microsoft SQL Server** has no driver in 6.0.x; nothing should be read as a promise of one.

> **Tip**
> The project manager's question is not "which database is best" but "which database can our team run, back up and
> restore". Write the name of the person who restores it into the decision record.

## Operating system

Exponential runs on "almost any" system "that runs PHP 8.0 or later". [Operating system support](../os-support.md)
sorts systems into four tiers:

| Tier | Meaning | Examples from the sources |
|---|---|---|
| 1. Tested here | The project runs it every day, or CI runs on it every push | AlmaLinux 9 (the development server, with Plesk), Ubuntu on GitHub Actions |
| 2. Known to work | Covered by documentation, packages or Velocity's CI | Red Hat Enterprise Linux 9 and 10 and their rebuilds, Debian 12 and 13, Ubuntu 22.04 and 24.04, Alpine, Plesk, FreeBSD 14, macOS, Windows, Docker and Podman |
| 3. Expected to work | A maintained PHP 8 exists, nobody has watched Exponential run on it | Fedora, SUSE, Arch, cPanel and DirectAdmin hosts, Kubernetes and others |
| 4. Not possible today | No PHP 8 for the system | listed so the question gets a straight answer |

For a production project, prefer tier 1 or tier 2. A tier 3 system is "a reasoned expectation, not a test result"
([The four tiers](../os-support.md#the-four-tiers)); if you choose one, put it in the risk register and plan a test
installation early.

What every system has to provide is on [Operating system support](../os-support.md) and in
[chapter 2](../../install/02-requirements.md): PHP 8.0 or later (8.1 for Velocity), the required PHP extensions, a
database, Composer 2, writable directories, and 64-bit PHP recommended "for support of more than 30 languages".

## Web server and engine

[Chapter 8](../../install/08-serving-the-site.md#81-the-choice-in-one-table) compares the choices:

| | Exponential Velocity | FrankenPHP (through Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Role | **recommended** for every stage | production-ready alternative | traditional | traditional |
| PHP | 8.1 or later | the PHP built into the binary | 8.0 or later | 8.0 or later |
| Separate web server needed | no | no | yes | yes |
| Separate HTTPS or certificate tool needed | no: own certificates, self-signed fallback, ACME | no: your certificate or a self-signed one | yes: `mod_ssl` plus certbot or a hosting panel | yes |
| Application kept in memory | yes (persistent workers) | no | no | no |
| Suits shared hosting | usually not | usually not | yes | rarely |

When to use a traditional server, according to chapter 8: "when your hosting does not allow long-running processes,
when you already run a tuned Apache or nginx you want to keep, when the machine only has PHP 8.0 ... or when several
customers on one machine need operating-system isolation from each other". The choices are not exclusive: Velocity
can serve the site while Apache or nginx stays in place on the same files during a migration.

Other servers (LiteSpeed, Caddy with PHP-FPM, IIS, lighttpd) are tier 3; PHP's built-in server is for development
only ([Web servers](../os-support.md#web-servers)).

More on Velocity: [The persistent-worker server](../../features/6.0/velocity-persistent-worker-server.md),
[The Velocity web server](../../features/6.0/velocity-web-server.md),
[The response cache](../../features/6.0/velocity-response-cache.md),
[The control panel](../../features/6.0/velocity-control-panel.md),
[HTTPS certificates](../../features/6.0/velocity-https-certificates.md). On FrankenPHP:
[FrankenPHP](../../bc/6.0/frankenphp.md).

## Hosting model

| Model | What the sources say | Questions for your project |
|---|---|---|
| Own server or virtual machine | Cloud machines "are ordinary Linux machines: tier 2 or 3 by their distribution" | Who patches the operating system, PHP and the database? |
| Hosting panel | Plesk is tier 1; cPanel and DirectAdmin tier 3; "shared hosting may forbid long-running processes, so Velocity may not be allowed" | Does the panel allow long-running processes? Is there shell access? |
| Containers (Docker, Podman, Kubernetes) | Keep `var/` and `settings/override` on a persistent volume; with SQLite run one writing container; several replicas need a database server and shared storage for `var/storage` | How many replicas? Which database server? |
| Platform as a service | Expected to work with PHP 8 and a writable persistent disk; a read-only or ephemeral file system needs `var/` moved and a database server | Is the disk persistent? |
| Managed hosting by 7x | 7x offers "Exponential Website Hosting" and "Exponential Platform SSD Hosting"; prices and terms are on the 7x website | Ask 7x directly; this help does not state terms |

Sources: [Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels),
[Who is 7x?](../who-is-7x.md#services-and-hosting).

> **Note**
> Exponential is free software and installs on any system that meets the requirements, so a project is not tied to a
> host. Moving a site later means moving files and a database; see [Backups and moving later](#backups-and-moving-later).

## Install method

[Chapter 4](../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation) offers one installer
with three front ends:

| Situation | Recommended | Why |
|---|---|---|
| First look on a laptop | `exp:install` | one command, SQLite, nothing to answer |
| First production install, done by hand | the setup wizard, or `exp:install` with explicit options | the wizard explains each decision |
| Shared hosting without shell access | the setup wizard | it needs only a browser and FTP access |
| The same site built again and again (staging, test, training) | the kickstarter | the reviewed `kickstart.ini` is the specification of the install |
| CI pipeline, container image, automated test environment | `exp:install` | options or environment variables, exit status |
| No internet access on the server | the kickstarter or the wizard with local packages | packages are taken from the local repository |

For building websites the project recommends Git clones of the kernel and every extension with full history
([Develop with Git](../develop-with-git.md)). The site packages offered by the installer (for example
`sevenx_multisite`, with or without demo content) are listed in
[4.3 Site packages](../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed).

## Sizing

The installation book is honest that "the documentation gives few hard numbers, and this book does not invent
others". What it states ([2.10 Memory, CPU and disk](../../install/02-requirements.md#210-memory-cpu-and-disk)):

| Item | Value |
|---|---|
| PHP `memory_limit` | at least 64M |
| One Velocity worker | about 10 MB of private memory (measured) |
| Velocity pool | `Workers=4` shipped |
| A MongoDB installation | RAM 512 MB minimum, 2 GB recommended; disk 2 GB minimum, 10 GB recommended |

Disk use grows with uploads and image variations (`var/storage`), caches and logs. The advice is to "measure a running
site rather than guessing" with the [benchmark](../../features/6.0/benchmark.md) command and Velocity's
[control panel](../../features/6.0/velocity-control-panel.md). Plan a measurement on the test installation with real
content into the schedule, before you commit to a machine size.

Performance levers by database are listed in
[Performance and scaling](../database-support.md#performance-and-scaling).

## Backups and moving later

- **A complete backup is the database and the files**: `var/storage`, `settings/override` and
  `settings/siteaccess` ([Backup and restore](../database-support.md#backup-and-restore)).
- **Moving to another database** is possible between the SQL engines with the schema dump and insert scripts, and
  into Oracle and MongoDB with their own tools; plan a maintenance window, because nothing may be published between
  the dump and the switch ([Moving a site to another database](../database-support.md#moving-a-site-to-another-database)).
- **Restore must be tested**: the first-week checklist asks for a backup "taken **and restored** on another machine"
  ([10.15](../../install/10-after-installing.md#1015-a-checklist-for-the-first-week)).

## A decision record to copy

```text
Platform decision for <project name>          date: <date>        decided by: <names>

Product:            Exponential 6.0.x  /  <other product of the family>
Database:           <SQLite 3 | PostgreSQL | MongoDB | MariaDB | Oracle | MySQL>, version <x>
  Why:              <one or two sentences, with the row of the decision table>
  Who restores it:  <name>
Operating system:   <name and version>, tier <1-4> per the help's OS support page
PHP:                <version> for the web, <version> for the command line
Web server/engine:  <Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM | other>
Hosting model:      <own server | panel | containers | PaaS | managed>
Install method:     <setup wizard | kickstarter | exp:install | Git clones + ...>
Environments:       development <where>, test <where>, production <where>
Backups:            what <database + var/storage + settings>, where <off the machine>, restore tested on <date>
Risks accepted:     <for example a tier 3 system, MongoDB limits checked against extensions X, Y>
```

## Related pages

- [Database support](../database-support.md)
- [Operating system support](../os-support.md)
- [Chapter 2: Requirements](../../install/02-requirements.md)
- [Chapter 4: Choosing an install method](../../install/04-choosing-an-install-method.md)
- [Chapter 8: Serving the site](../../install/08-serving-the-site.md)
- [Risk management for Exponential projects](risk-management.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
