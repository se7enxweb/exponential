---
title: "Tender template: hosting and data location clause"
audience: government, project-manager, hosting, partner
category: public-sector
order: 90
summary: Clause text for where and how an Exponential site is hosted - data and backup locations, sub-processors, environments, backups and restore tests, access to the administration, customer access to its own data - with blanks and the technical facts a host's answer should match.
keywords: hosting clause, data location, data residency, sovereignty, backups, restore test, RPO, RTO, staging, environments, sub-processors, admin access restriction, tender template
type: help
---
# Tender template: hosting and data location clause

This page is for procurement officers and IT managers who contract hosting for an Exponential site, alone or with
build and support. Exponential runs on any host that meets its requirements, so the clause is about the host's
commitments. Background: [Hosting and data location requirements](../requirements/hosting-and-data-location-requirements.md).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [What a backup must contain](#what-a-backup-must-contain)
3. [Technical facts to check answers against](#technical-facts-to-check-answers-against)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
H. HOSTING AND DATA LOCATION

H.1 Locations. Production systems, backups, logs and disaster recovery copies shall
    be located only in <country / region>. The Supplier lists each location in
    <annex> and shall not change it without the Customer's prior written consent.

H.2 Access from elsewhere. Remote administration by Supplier staff from outside
    <country / region> is [not permitted / permitted only for <roles> under <terms>].

H.3 Environments. The Supplier provides a production environment and a staging
    environment with the same software versions; [a training environment].

H.4 Platform. The Supplier keeps the operating system, PHP, database and web server
    on versions supported by their maintainers and states the versions in <annex>.

H.5 Backups. The database and files are backed up together every <interval>,
    encrypted, kept for <period>, and stored in a location separate from
    production. Backups include the configuration and the keys listed in <annex>.

H.6 Recovery. Recovery point objective: <time>. Recovery time objective: <time>.
    The Supplier performs a restore test every <interval> and reports date,
    duration and result.

H.7 Administration access. Access to the administration is restricted to
    <networks / VPN / named addresses>; all access is over HTTPS.

H.8 Customer access to data. The Customer may obtain at any time, within <time>
    and without additional charge, a complete copy of the database, the files and
    the configuration.

H.9 Capacity. The Supplier monitors capacity (disk, memory, database) and warns the
    Customer before a limit is reached.
```

## What a backup must contain

From [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md), put this list in the H.5 annex:

| Item | Why |
|---|---|
| the database | content, users, versions, settings |
| `var/storage/` and `var/<site>/storage/` | uploaded files |
| `settings/override/` and `settings/siteaccess/` | configuration and credentials |
| `settings/override/audit.ini.append.php` | audit signing keys; without them existing records cannot be verified |
| `settings/override/mailpreferences.ini.append.php` | the e-mail preference secret; a new one invalidates every unsubscribe link already sent |
| `var/<site>/log/audit/` | the audit trail with signed manifests |
| `composer.json`, `composer.lock`, own extensions and designs | to rebuild the same versions |

Maintenance mode makes database and files agree during the backup
([10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore)).

## Technical facts to check answers against

| Topic | Source |
|---|---|
| Requirements (PHP 8.0+, databases, web servers) | [Requirements at a glance](../../../hosting/basics/requirements-at-a-glance.md) |
| Database backup commands per engine | [Database backups per engine](../../../hosting/operations/database-backups-per-engine.md) |
| Restore tests | [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md) |
| Restricting the administration | [Restricting the administration](../../../hosting/security/restricting-the-administration.md) |
| Several web nodes | [Several web nodes](../../../hosting/operations/several-web-nodes.md) |
| TLS | [TLS and HTTPS](../../../hosting/security/tls-and-https.md) |

## Notes for the evaluator

- An RTO without a dated restore test is a guess.
- H.8 also serves the exit clause; make the price zero here so it cannot be used as leverage later.

## Related pages
- [Tender templates](README.md)
- [Privacy and data processing clause](privacy-and-data-processing-clause.md)
- [Service levels clause](service-levels-clause.md)
- [Exit and data return clause](exit-and-data-return-clause.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
