---
title: Upgrades and change
audience: government, admin, project-manager
category: public-sector
order: 10
summary: Index of the pages on upgrading and changing a public-sector Exponential site under change management - the upgrade path, immutable releases, change advisory boards, staging, configuration managed by deployment, release notes, extensions, PHP and database upgrades, rollback, upgrade checks, maintenance windows and security updates.
keywords: upgrades, change management, change advisory board, staging, release notes, rollback, maintenance window, security updates, ManagedByDeployment, upgrade check, extensions, PHP upgrade, index
type: help
---
# Upgrades and change

This folder is for service owners, change managers and administrators who keep a public website current without
surprises. It connects Exponential's upgrade procedure ([Upgrading](../../../../guides/upgrading.md),
[chapter 11](../../../../install/11-upgrading.md)) with the change management public bodies run: approvals, staging,
evidence, rollback and communication.

## Contents
1. [Pages](#pages)
2. [Reading order](#reading-order)
3. [Related pages](#related-pages)

## Pages

| Page | Answers |
|---|---|
| [Upgrade overview for the public sector](upgrade-overview-for-public-sector.md) | Which upgrade path applies and the steps from backup to verification. |
| [Immutable releases and versions](immutable-releases-and-versions.md) | Why a version never changes after release, and how to pin and verify what runs. |
| [Change management and advisory boards](change-management-and-advisory-boards.md) | Which changes go to the board, the evidence the platform gives, a record template. |
| [Staging and approval](staging-and-approval.md) | Development, test and production; EXP_ENV variants; what to test; sign-off. |
| [Configuration managed by deployment](managed-by-deployment.md) | Locking live configuration so changes come through version control. |
| [Reading release notes](reading-release-notes.md) | Where the notes are and how to turn them into a risk assessment. |
| [Extension updates](extension-updates.md) | Finding outdated extensions, updating with a dry run, third-party review. |
| [PHP and database upgrades](php-and-database-upgrades.md) | Supported versions, the order of upgrades, the deprecations log. |
| [Rollback planning](rollback-planning.md) | The way back for each kind of change; the decision point. |
| [Upgrade check before and after](upgrade-check-before-and-after.md) | File and database consistency as evidence. |
| [Maintenance windows](maintenance-windows.md) | Planning, announcing, running and closing downtime. |
| [Security updates and disclosure](security-updates-and-disclosure.md) | Supported versions, how fixes arrive, reporting a vulnerability. |

## Reading order

1. [Upgrade overview](upgrade-overview-for-public-sector.md) and [Immutable releases](immutable-releases-and-versions.md).
2. [Change management and advisory boards](change-management-and-advisory-boards.md), [Staging and approval](staging-and-approval.md)
   and [Configuration managed by deployment](managed-by-deployment.md): the process.
3. For each change: [Reading release notes](reading-release-notes.md), [Upgrade check before and after](upgrade-check-before-and-after.md),
   [Rollback planning](rollback-planning.md), [Maintenance windows](maintenance-windows.md).
4. Platform and add-ons: [Extension updates](extension-updates.md), [PHP and database upgrades](php-and-database-upgrades.md).
5. [Security updates and disclosure](security-updates-and-disclosure.md): keep it in your security plan.

Up: [Running Exponential in the public sector](../README.md). The section landing page is
[Governments: start here](../../government-start-here.md).

## Related pages
- [Operations tasks](../../tasks/operations/README.md)
- [Upgrading customer sites (hosting)](../../../hosting/operations/upgrading-customer-sites.md)
- [Deploying guide](../../../../guides/deploying.md)
- [Source control and releases standard](../../../../standards/source-control-and-releases.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
