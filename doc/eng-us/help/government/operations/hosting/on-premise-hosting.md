---
title: On-premise hosting in your own data center
audience: government, hosting, admin
category: public-sector
order: 20
summary: For in-house IT teams that run Exponential on their own servers. What a site needs from your data center, which operating systems the project tests, how to split the work between teams, and where the detailed server pages are.
keywords: on-premise, on premises, own data center, in-house hosting, self-hosting, server room, public sector IT, Linux, AlmaLinux, RHEL, Debian, Ubuntu, operating system tiers, requirements
type: help
---
# On-premise hosting in your own data center

This page is for the in-house IT team of a public body that wants to run Exponential on its own hardware or in its
own virtualization cluster. It answers what the software needs from your data center, which platforms the project
itself runs and tests, how the work usually splits between infrastructure, application and web teams, and where the
step-by-step server pages are. The facts come from [Operating system support](../../../os-support.md),
[Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md) and the installation book.

The hosting section of this help was written for hosting companies. Most of it applies to an in-house team as well;
this page links to it instead of repeating it, and adds what differs when the "customer" is your own organization.

## Contents

1. [What a site needs from your data center](#what-a-site-needs-from-your-data-center)
2. [Choosing the operating system](#choosing-the-operating-system)
3. [Who does what](#who-does-what)
4. [Why on-premise suits many public bodies](#why-on-premise-suits-many-public-bodies)
5. [What on-premise does not give you](#what-on-premise-does-not-give-you)
6. [Checklist](#checklist)

## What a site needs from your data center

An Exponential site is a PHP application with a database and a directory of files it writes. The full list is on
[What hosting Exponential involves](../../../hosting/basics/what-hosting-exponential-involves.md); in short:

| Part | What it is | Where to read |
|---|---|---|
| PHP 8 for the web and for the command line | the same version for both; 8.0 or later, Velocity included | [PHP versions and extensions](../../../hosting/basics/php-versions-and-extensions.md) |
| A database | one of six, in the project's official order | [Database choice in the official order](database-choice-in-the-official-order.md) |
| A web server or the Velocity engine | serves pages and HTTPS | [Web server choices](web-server-choices.md) |
| Writable directories | `var/` and the settings overrides | [Storage layout](../../../hosting/operations/storage-layout.md) |
| Cron | runs the cronjob parts (publishing, workflows, the audit) | [Cron jobs](../../../hosting/operations/cron-jobs.md) |
| Outgoing mail | notifications, password mails, newsletters | [Mail delivery](../../../hosting/operations/mail-delivery.md) |
| Backups and monitoring | your own systems | [Backups and recovery](../backups-and-recovery/README.md), monitoring section of these operations pages |

Before you install anything, check a machine with the command the project provides; it lists the PHP extensions
the installation wants and prints the install command for your system:

```bash
php -v | head -1
./console exp:velocity ext check --allow-root-user
```

## Choosing the operating system

The project sorts operating systems into four tiers ([Operating system support](../../../os-support.md)):

| Tier | Meaning | Examples |
|---|---|---|
| 1. Tested here | run every day by the project or its CI | AlmaLinux 9 (the development server); Ubuntu in GitHub Actions |
| 2. Known to work | covered by documentation, packages or the Velocity CI | RHEL 9 and 10, Rocky Linux, Oracle Linux, CentOS Stream; Debian 12 and 13; Ubuntu 22.04 and 24.04; Alpine; FreeBSD 14; Windows x64; Docker and Podman |
| 3. Expected to work | nothing known stands in the way, nobody has watched it run | Fedora, SUSE and openSUSE, Amazon Linux 2023, Kubernetes |

For a public body this usually means: use the Linux distribution your organization already supports, as long as it is
tier 1 or 2. A tier 3 system is "a reasoned expectation, not a test result"
([Operating systems for hosts](../../../hosting/basics/operating-systems-for-hosts.md)). If your standard platform
is tier 3, record that as a risk in your hosting decision (see [Hosting decision record](hosting-decision-record.md)).

## Who does what

In a typical public-sector IT organization the work splits like this. Adapt the names to your own teams.

| Task | Usually done by | Exponential tools |
|---|---|---|
| Machines, operating system, patching | infrastructure team | none; your standard tooling |
| PHP, the database server, the web server or Velocity | infrastructure or platform team | `exp:velocity ext check`, `exp:velocity status` |
| Installing and upgrading Exponential | application team | `exp:install`, the upgrade guide, Setup > Upgrade check |
| Settings, siteaccesses, roles | application team, web team | Setup > Ini settings, Users > Roles and policies |
| Content | departments' editors | the administration |
| Backups, monitoring, incidents | operations | see the backups, monitoring and incidents pages of this section |

Write the split down once, with names of teams rather than people, and keep it next to your runbooks. The incident
pages of this section assume that the person on call knows who owns the database and who owns the application.

## Why on-premise suits many public bodies

- **Data stays where you put it.** All content, uploaded files, accounts and logs are on your own machines; see
  [Data location and what is stored where](data-location-and-what-is-stored-where.md).
- **No license fees per server.** Exponential is free software under the GNU GPL v2 or later
  ([license](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)), so adding a test or standby server is a hardware
  decision, not a procurement of further licenses.
- **Your own change process applies.** Code and settings can be deployed from version control, and the administration
  can be locked against settings changes ([Configuration under version control](configuration-under-version-control.md)).
- **One machine is enough for many sites.** The default database, SQLite 3, needs no database server and suits a site
  served from one machine ([Database support](../../../database-support.md)).

## What on-premise does not give you

- **Redundancy is your job.** A single server is a single point of failure. Read
  [High availability overview](../availability/high-availability-overview.md) before you promise more than one machine
  can deliver.
- **Off-site copies are your job.** A backup in the same room as the server does not survive a fire or flood; see
  [Offsite and encrypted copies](../backups-and-recovery/offsite-and-encrypted-copies.md).
- **There is no vendor support contract by default.** The project publishes no service level agreement; support
  options are described on [Who is 7x?](../../../who-is-7x.md).

## Checklist

- [ ] The operating system is tier 1 or tier 2, or the tier 3 risk is recorded.
- [ ] `exp:velocity ext check` reports nothing missing for the features you use.
- [ ] Database, web server or Velocity, cron and mail are chosen and documented.
- [ ] The split of tasks between teams is written down.
- [ ] Backups leave the building, and a restore has been tested.

## Related pages

- [Hosting](README.md) index of this folder
- [Choosing a hosting model](../../../hosting/models/choosing-a-hosting-model.md) and [Dedicated servers](../../../hosting/models/dedicated-servers.md)
- [New server checklist](../../../hosting/checklists/new-server.md)
- [Hardening the host](hardening-the-host.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
