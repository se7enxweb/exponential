---
title: Release line and support policy
audience: businesses, project-manager, admin
category: business-solutions
order: 20
summary: Which Exponential versions are maintained, how releases and security fixes reach you, which PHP versions are supported, and what that means for a business planning years ahead.
keywords: release line, support policy, maintained versions, security fixes, PHP versions, 6.0.x, tags, Packagist, changelog, end of life, SECURITY.md
type: help
---
# Release line and support policy

This page is for owners, managers and project leads who need to know how long a site built on Exponential will be
supported, how fixes reach them, and which PHP versions their hosting must offer. It collects the commitments the
project has written down and says what each one means for a business. It does not add any promise of its own.

## Contents

1. [The maintained line](#the-maintained-line)
2. [The 6.0.x releases so far](#the-60x-releases-so-far)
3. [How security fixes reach you](#how-security-fixes-reach-you)
4. [PHP versions](#php-versions)
5. [Release rules you can rely on](#release-rules-you-can-rely-on)
6. [Where to read what changed](#where-to-read-what-changed)
7. [What this means for your plan](#what-this-means-for-your-plan)
8. [What the project does not publish](#what-the-project-does-not-publish)

## The maintained line

From SECURITY.md:

| Version | Supported |
|---|---|
| 6.0.x, current line (`main`, and the next tag cut from it) | yes |
| 6.0.x, released tags (`v6.0.0` to `v6.0.14`) | fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | no |

In plain words: **be on the newest 6.0.x release**. If you run an older 6.0.x, you are not cut off, but the way to
receive a fix is to upgrade within the line. If you run 5.x or older, plan an upgrade project
([Upgrading](../../../guides/upgrading.md)).

## The 6.0.x releases so far

Exponential 6.0.0 was released on 1 January 2024 and 6.0.14 on 7 June 2026; the 6.0.15 line is in development, and
its changelog says "Not released yet" ([Who is 7x?, the 6.0.x releases](../../who-is-7x.md#the-60x-releases),
[6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)).

Releases that matter for planning, from
[Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#the-versions-so-far):

| Release | Why it matters |
|---|---|
| 6.0.7 | The last release for PHP 7.4 |
| 6.0.8 | Needs PHP 8.1 |
| 6.0.10 | The first release named Exponential |
| 6.0.13 | Security fixes and sort column checks |
| 6.0.14 | MongoDB as a database, and the upgrade file check |
| 6.0.15 line | Velocity, the admin4 design, the audit trail, Exponential Layouts, Exponential Intelligence, the help system |

> **Note**
> The project publishes no fixed release calendar. The 6.0.x tags came at intervals of about one to five months
> ([Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#the-versions-so-far)). Plan on the
> tags that exist, not on a date.

## How security fixes reach you

SECURITY.md sets out the process:

1. A security problem is reported privately by e-mail to the address in SECURITY.md, not in the public issue
   tracker. "You can expect a first answer within one business day."
2. The reporter is told whether the report is accepted and is kept informed while a fix is prepared.
3. An accepted issue is fixed on `main` and released with the next 6.0.x version.
4. A published tag is never changed: "a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
   release, not as a corrected copy of the old one".

For a business this means that **staying current is your security plan**. Budget a routine upgrade within the
line as a recurring task. The security section has more on this:
Security updates and disclosure.

## PHP versions

From the README, "What version of PHP is required" and
[Who is the Exponential Foundation?, PHP versions](../../who-is-the-exponential-foundation.md#php-versions):

| PHP | Exponential 6 |
|---|---|
| 7.4 | Only up to 6.0.7, deprecated |
| 8.0 | The oldest supported version, so the stock PHP of Red Hat Enterprise Linux 9 runs it under Apache or PHP-FPM |
| 8.1 to 8.5 | Supported; Exponential Velocity needs 8.1 or later |
| 8.6 and later | Allowed by the `php` requirement of `composer.json`; the README names "PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9" as the aim |

The README's advice: "please use the latest version of PHP available on your OS". The continuous integration runs
the test suite on PHP 8.1 to 8.5 and checks that PHP 8.0 still boots
([PHP versions](../../who-is-the-exponential-foundation.md#php-versions)).

> **Tip**
> PHP and Exponential move together. Before you promise an upgrade date, ask your hosting provider which PHP
> versions they offer and when they retire one. **Setup > System information** warns when a PHP branch's security
> support has ended ([System information, health checks](../../../guides/system-information.md#3-health-checks)).

## Release rules you can rely on

From [Who is the Exponential Foundation?, releases and version numbers](../../who-is-the-exponential-foundation.md#releases-and-version-numbers):

- **Published versions never change.** What you tested is what you deploy.
- **The last position counts on.** After `0.1.9` comes `0.1.10`; versions are sorted by version, not by name.
- **Extensions carry their own version** in `ezinfo.php` and `extension.xml`, and **Setup > Extensions** shows it.
- **Every package is on GitHub and Packagist**, so the exact code of any version can always be fetched again.

## Where to read what changed

| Document | Use it for |
|---|---|
| [Changelogs per release](../../../changelogs/6.0/6.0.15.md) | What a version holds, with an "Upgrading" section |
| [Behavior change notes](../../../bc/6.0/behaviour-changes-2026-10.md) | What behaves differently and what to check, per period |
| [Extensions' release notes](../../../changelogs/extensions/README.md) | What an extension release changed |
| [The history](../../../history/README.md) | The story of every change since December 2023, by month |

Give one person on your team the job of reading these before each upgrade.

## What this means for your plan

- [ ] Your site runs a 6.0.x release, and you know which one.
- [ ] Upgrading to the newest 6.0.x is on the calendar at least as often as new tags appear.
- [ ] Your hosting offers a PHP version in the supported range, and you know when it will be retired.
- [ ] Someone reads the changelog and behavior change notes before each upgrade.
- [ ] Your partner or team knows how to report a security problem privately.

## What the project does not publish

The project does not publish a single support policy document, a release calendar, an end-of-life date for 6.0, or
a service level agreement ([Release and upgrade planning](../../leadership/release-and-upgrade-planning.md#what-the-project-commits-to),
[SLA options](../../hosting/business/sla-options.md)). Commercial support and hosting terms come from 7x and from
hosting partners ([Who is 7x?, services and hosting](../../who-is-7x.md#services-and-hosting)). If your business
needs a contractual commitment, get it from them in writing; see
Contracts and SLAs basics.

## Related pages

- [Upgrades without surprises](upgrades-without-surprises.md)
- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- Working with 7x and the project
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
