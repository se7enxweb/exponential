---
title: "FAQ: releases and upgrades"
audience: government, project-manager, admin, hosting
category: public-sector
order: 150
summary: Answers to the release and upgrade questions public bodies ask about Exponential - versions, supported lines, how often releases come, whether versions change, how upgrades work, automatic updates and PHP versions.
keywords: releases FAQ, upgrades, versions, 6.0.x, supported versions, tags, changelog, upgrade check, Git Manager, automatic updates, PHP versions, public sector
type: help
---
# FAQ: releases and upgrades

This page answers the release and upgrade questions public bodies ask about Exponential. The answers come from
`SECURITY.md`, [Source control and releases](../../../standards/source-control-and-releases.md) and the upgrade guides;
the lifecycle part of the procurement section explains them for contracts
([The product lifecycle](../procurement/lifecycle/README.md)).

## Contents

1. [Which version is current?](#which-version-is-current)
2. [Which versions are supported?](#which-versions-are-supported)
3. [How often are there releases?](#how-often-are-there-releases)
4. [Can a published version change?](#can-a-published-version-change)
5. [How does an upgrade work?](#how-does-an-upgrade-work)
6. [Can a site update itself?](#can-a-site-update-itself)
7. [Which PHP versions are supported?](#which-php-versions-are-supported)

## Which version is current?

The 6.0 line. Published kernel tags run from `v6.0.0` to `v6.0.14`; 6.0.15 is in development on `main`
([Source control and releases](../../../standards/source-control-and-releases.md#the-kernels-versions)). **Setup > System
information** shows what your site runs.

## Which versions are supported?

6.0.x; fixes are released as the next 6.0.x. 5.x, 4.x, 3.x and older are not supported (`SECURITY.md`;
[Supported versions](../procurement/lifecycle/supported-versions.md)).

## How often are there releases?

There is no fixed schedule. Recent releases were tagged in December 2025, February, April and June 2026
([The roadmap](../../project/roadmap.md#what-has-already-been-delivered)). Plan for updates several times a year.

## Can a published version change?

No. A published tag is never changed; a fix arrives as the next version (`SECURITY.md`;
[Release policy and version numbers](../procurement/lifecycle/release-policy-and-version-numbers.md)).

## How does an upgrade work?

Seven steps: back up and start a maintenance window, see what is waiting, update the kernel (Git or the Git Manager),
update the extensions (Composer or **Setup > Updates and packages**), check and update the database, clear caches,
check the site ([Update with Git](../../update-with-git.md)). **Setup > Upgrade check** compares your files with the
release first ([The upgrade check](../../../guides/upgrade-check.md)).

## Can a site update itself?

The update guide shows a script for cron or a systemd timer that follows release tags; it never runs database updates
and stops when something needs a person. It advises testing on staging first and keeping the live site on releases
([Update with Git: Let it update itself](../../update-with-git.md#let-it-update-itself)).

## Which PHP versions are supported?

PHP 8.0 to 8.5, Velocity included (from its release 0.0.4.54); PHP 7.4 only up to 6.0.7 ([2. Requirements](../../../install/02-requirements.md#versions)).

## Related pages

- [Government FAQ](README.md)
- [Upgrade readiness](../checklists/upgrade-readiness.md)
- [Upgrading](../../../guides/upgrading.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
