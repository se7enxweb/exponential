---
title: Why businesses rely on Exponential
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: The reasons, each with its source, why an organization that earns money online can rely on Exponential: a long-lived code base, published versions that never change, few moving parts, speed under load, evidence when something goes wrong, and no license fee. And how each of them protects revenue and keeps the business available to customers.
keywords: stability, reliability, why Exponential, long-lived, release policy, security fixes, PHP versions, SQLite, Velocity, cache, audit trail, no license fee, GPL, return on investment, availability, uptime, low maintenance
type: help
---
# Why businesses rely on Exponential

This page is for owners and managers who must decide whether a web platform can carry part of their revenue for
years. It lists the properties of Exponential that matter for that decision, each with the place where it is written
down, and explains what each one means in money and in availability to customers.

> **Note**
> This page states what the sources say the software is and does. It does not promise an uptime figure: availability
> depends on the hosting, the people who run the site and the way it is built as much as on the software. The
> [Reliability](reliability/README.md) section explains how to get there.

## Contents

1. [The question a business asks](#the-question-a-business-asks)
2. [A long-lived code base](#a-long-lived-code-base)
3. [Releases you can plan around](#releases-you-can-plan-around)
4. [Few moving parts](#few-moving-parts)
5. [Fast under load](#fast-under-load)
6. [Evidence when something goes wrong](#evidence-when-something-goes-wrong)
7. [No license fee and no lock-in](#no-license-fee-and-no-lock-in)
8. [How stability turns into return on investment](#how-stability-turns-into-return-on-investment)
9. [What stability does not do for you](#what-stability-does-not-do-for-you)

## The question a business asks

A website that sells, or that staff and partners depend on, earns its money only while it is available and while it
does what customers expect. Each failure costs twice: the lost sale or lost working hour, and the time of the people
who fix it. A platform is worth more to a business the less often it fails, the faster it recovers and the fewer
surprises its upgrades bring. The sections below take these one by one.

## A long-lived code base

| Fact | Source |
|---|---|
| The copyright notice reads "Copyright (C) 1998 - 2026 7x & Exponential Foundation" | `COPYRIGHT.md`, quoted in [Who is 7x?](../who-is-7x.md#in-short) |
| "Exponential has made it beyond it's end of life in 2021 and survived" | `README.md`, "Exponential Project Status" |
| 7x has published the 6.0.x releases since December 2023, from 6.0.0 on 1 January 2024 to 6.0.14 on 7 June 2026, with 6.0.15 in development | [Who is 7x?](../who-is-7x.md#in-short) |
| Current releases aim "at easing the requirements to support current versions of the PHP language like PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9" | `README.md` |
| Sites built in the years when the software was still called eZ Publish can be migrated | [14. Migrating from the 4.x line](../../install/14-migrating-from-4x.md), [15. Migrating from the 5.x legacy stack](../../install/15-migrating-from-5x-legacy.md) |

**What it means for a business:** content, templates and extensions written years ago keep running. A business does
not have to rebuild its site every few years because the platform under it was abandoned or rewritten. The content
model, the URLs and the editors' habits survive upgrades.

## Releases you can plan around

| Fact | Source |
|---|---|
| "Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch ... and published with the next 6.0.x release" | `SECURITY.md` |
| Private vulnerability reports go to security@se7enx.com, with "a first answer within one business day" | `SECURITY.md` |
| "A published tag is never changed": a fix reaches you as the next release, not as a corrected copy | `SECURITY.md` |
| The continuous integration runs the test suite on PHP 8.1 to 8.5 and checks that PHP 8.0 still boots, on every push and pull request | [The support commitment](../who-is-the-exponential-foundation.md#the-support-commitment) |
| Before an upgrade, the administration checks the database and the files against the release (**Setup > Upgrade check**) | [Upgrade check](../../guides/upgrade-check.md), [File consistency check](../../features/6.0/file-consistency-check.md) |

**What it means for a business:** you know which version you run, you know a version never changes under you, and
you can test the next one before it reaches customers. See [Upgrades without surprises](reliability/upgrades-without-surprises.md)
and [Release line and support policy](reliability/release-line-and-support-policy.md).

## Few moving parts

Every component a site needs is one more thing that can fail, be misconfigured or need its own updates.

| Fact | Source |
|---|---|
| SQLite 3 is the default database; "SQLite needs none" (no database server) | `README.md`, "Requirements"; [Database support](../database-support.md) |
| Supported databases, in order of preference: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | `README.md`, "Main Exponential features" |
| Runs on almost any operating system with PHP 8.0 or later | [Operating system support](../os-support.md) |
| Runs behind Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd and others, or its own server, Velocity | `README.md`, "How Exponential is served" |
| Shop, search engine, workflows, multisite and many languages are built in | `README.md`, "Main Exponential features" |

**What it means for a business:** a small organization can run a complete site, a store included, on one server with
PHP and no separate database server, and add components (PostgreSQL, Solr search, a cluster) only when it needs
them. Fewer components mean fewer updates, fewer failure points and lower hosting cost. See
[Database choice for reliability](reliability/database-choice-for-reliability.md).

## Fast under load

The figures documented for the stack are "0.36 ms per cached page, 8,941 requests per second sustained, and repeat
visits never touch the network", with their context: a median over hundreds of requests, measured on a 12-core
machine serving a real page with zero errors at every concurrency tried, up to 384 connections, reproducible with
scripts that ship with the project (`README.md`, "What that measures as").

The README also says where the speed comes from: "Most of the speed came from fixing Exponential and the cache, not
from the server process model", for example one request rebuilding an expired page while everyone else is served the
existing copy, and a returning visitor's reload being answered with 202 bytes instead of the whole page. "Exponential
6.0.15+ is fast under load on Apache or Nginx too" (`README.md`, "The part that matters even if you never run
Velocity").

**What it means for a business:** a campaign, a newsletter or a sale that brings many visitors at once is the moment a
slow site loses the most money. A cache that serves the existing copy while one request rebuilds it is what keeps
the site answering during that peak. See [Peak traffic planning](reliability/peak-traffic-planning.md) and
[Velocity for businesses](reliability/velocity-for-businesses.md). Your own figures depend on your hardware and pages;
measure them with the benchmark tools ([Benchmarking](../../guides/benchmarking.md)).

## Evidence when something goes wrong

| Tool | What it gives you | Source |
|---|---|---|
| Tamper-evident audit trail | Who changed what and when, with a console, alerts and exports | [The audit trail](../../features/6.0/audit-trail.md) |
| Log viewer | The logs in the administration, without a shell | [Log viewer](../../features/6.0/log-viewer.md) |
| Error references | A reference shown to the visitor that leads to the logged error | [Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md) |
| System information | Versions, PHP, database and settings at a glance | [System information](../../guides/system-information.md) |
| Maintenance mode | A clean page for visitors while work is done | [Maintenance mode](../../guides/maintenance-mode.md) |

**What it means for a business:** shorter incidents. The time between "a customer says checkout fails" and "we know
why" is where most downtime is spent. See [Incident response for small teams](reliability/incident-response-for-small-teams.md).

## No license fee and no lock-in

Exponential is free software under the GNU General Public License, version 2 or later, and its documentation is
under the GNU Free Documentation License ([Licensing for buyers](../business/licensing-for-buyers.md),
[Free documentation](../free-documentation.md)). There is no per-site, per-user or per-server license fee, and the
code, the content and the database are yours. You can change the hosting company or the partner without changing the
software. See [What you do not pay for](costs/what-you-do-not-pay-for.md).

## How stability turns into return on investment

| Property | Saves or earns | Read |
|---|---|---|
| Long-lived code base | No forced rebuild of the site every few years | [Total cost of ownership](costs/total-cost-of-ownership.md) |
| Releases that never change | Predictable upgrade work, testable in advance | [Budgeting for upgrades and maintenance](costs/budgeting-for-upgrades-and-maintenance.md) |
| Few moving parts | Lower hosting and administration cost | [Hosting cost drivers](costs/hosting-cost-drivers.md) |
| Fast under load | Sales not lost at peak times | [Peak traffic planning](reliability/peak-traffic-planning.md) |
| Evidence when something goes wrong | Shorter incidents | [Cost of downtime worksheet](costs/cost-of-downtime-worksheet.md) |
| No license fee | Budget goes to content and features | [ROI of stability](costs/roi-of-stability.md) |

The [ROI of stability](costs/roi-of-stability.md) page turns this into a calculation with example assumptions you
replace with your own.

## What stability does not do for you

Honest limits, so that nobody is surprised later:

- **The software does not back itself up.** Someone must make backups and test that they restore
  ([Backups that restore](reliability/backups-that-restore.md)).
- **Updates are not installed by themselves.** Someone must apply security releases
  ([Security updates and disclosure](security/security-updates-and-disclosure.md)).
- **Custom code is your responsibility.** Extensions and templates written for you must be maintained by you or your
  partner ([Choosing a partner](choosing/choosing-a-partner.md)).
- **Hosting matters.** A cheap host without monitoring cannot deliver what the software allows
  ([Choosing hosting](choosing/choosing-hosting.md)).

## Related pages

- [Businesses: start here](businesses-start-here.md)
- [Why stability matters](reliability/why-stability-matters.md)
- [Positioning and value proposition](../business/positioning-and-value-proposition.md)
- [Comparing Exponential factually](../business/comparing-exponential-factually.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
