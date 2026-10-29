---
title: "Story: hosting freedom"
audience: press
category: press-media
order: 67
summary: A feature story about where Exponential can run - any web server that rewrites URLs or its own Velocity, six databases, almost any operating system with PHP 8.0, shared hosting with limits, and no tie to one provider - with printable facts, sources and the honest limits.
keywords: story, hosting, hosting freedom, no lock-in, web servers, databases, operating systems, shared hosting, containers, cloud, press
type: help
---
# Story: hosting freedom

This page is for journalists who write about vendor lock-in, digital sovereignty or the hosting market. Exponential
documents a wide choice of web servers, databases and operating systems, and also the limits of that choice. This
page gives both, with sources.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [The choices at a glance](#the-choices-at-a-glance)
3. [Facts you can print](#facts-you-can-print)
4. [The honest limits](#the-honest-limits)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

"Exponential 6.0.x runs on almost any operating system that has PHP 8.0 or later (PHP 7.4 usable without support) and a web server. It is plain PHP
files plus a database; it has no compiled code of its own" ([Operating system support](../os-support.md)). It runs
behind "any web server that can rewrite a URL", or under its own Velocity server
([README](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options)), on six databases from SQLite to Oracle
([Database support](../database-support.md#the-supported-databases-in-order)), and the code is GPL, so anyone may
host it. 7x sells managed hosting, but "Anyone" may host it; "you choose"
([Talking points: hosting](../advocates/talking-points-hosting-databases-and-freedom.md#questions-you-will-hear)).

## The choices at a glance

| Layer | Choices in the sources | Source |
|---|---|---|
| Web server | Apache, Nginx, LiteSpeed, OpenLiteSpeed, Caddy, IIS, lighttpd, H2O, Traefik in front, or Velocity; FrankenPHP as an alternative engine | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options) |
| Database | SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Database support](../database-support.md#the-supported-databases-in-order) |
| Operating system | Four tiers, from "tested here" to "not possible today" | [Operating system support](../os-support.md#the-four-tiers) |
| PHP | 8.0 to 8.5, Velocity included (from its release 0.0.4.54) | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) |
| Where | Containers, cloud machines and hosting panels are covered | [Operating system support](../os-support.md#containers-cloud-and-hosting-panels) |

## Facts you can print

| Fact | Source |
|---|---|
| Nothing in the requirements names an operating system. | [Operating system support](../os-support.md#what-the-operating-system-has-to-provide) |
| Tier 1 ("tested here") is AlmaLinux 9 on the project's development server and Ubuntu in the CI. | [Operating system support, Tier 1](../os-support.md#tier-1-tested-here) |
| Velocity ships `.deb` packages for Debian 12 and 13 and Ubuntu 22.04 and 24.04. | [Operating system support, Tier 2](../os-support.md#linux-distributions) |
| A site can move to another database with documented tools. | [Moving a site to another database](../database-support.md#moving-a-site-to-another-database) |
| Backups are standard dumps for each database. | [Backup and restore](../database-support.md#backup-and-restore) |
| "Using Exponential under the terms of the GNU GPL is free of charge." | [Who is 7x?, Open source licenses](../who-is-7x.md#open-source-licenses) |

## The honest limits

- **Shared hosting**: Apache or LiteSpeed with `.htaccess` can serve it, and the setup wizard installs it without a
  shell; Velocity usually cannot run there.
- **Tier 3** systems are "a reasoned expectation, not a test result" ([Operating system support](../os-support.md#the-four-tiers)).
- **MongoDB** has documented limits ([MongoDB limits](../database-support.md#mongodb-limits)).
- **Microsoft SQL Server** is "possible with additional work", not one of the six.

## What not to claim

- "Runs anywhere." Name the tiers and the limits.
- "Zero lock-in." Any site depends on its content model, templates and extensions; say instead that the code, the
  databases and the hosts can be changed with documented tools.
- "Certified on" a platform: the sources describe tiers, not certifications.

## Angles and headline ideas

```text
"Your server, your database, your country: hosting freedom in an open source CMS"
"Four tiers of honesty: how Exponential documents where it runs"
"From a laptop with SQLite to Oracle: one CMS, six databases"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Platform support, managed hosting | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| A system to add to a tier | the public tracker | <https://github.com/se7enxweb/exponential/issues> |

## Related pages

- [Talking points: hosting, databases and freedom](../advocates/talking-points-hosting-databases-and-freedom.md)
- [Story: six databases](story-databases.md)
- [Story: operating systems](story-operating-systems.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
