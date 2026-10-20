---
title: Billing and metering options
audience: hosting
category: hosting
order: 50
summary: Billing models a host can choose for Exponential hosting (flat, tiered, usage based, per site, per service) and what the server and Exponential can actually measure for each: disk, database size, requests, workers, backups. No prices are given.
keywords: billing, metering, pricing model, flat rate, tiered, usage based, per site, storage, disk usage, database size, requests, access log, workers, bandwidth, invoices, plans, license fee
type: help
---
# Billing and metering options

This page is for product managers and finance staff at a hosting company who decide how Exponential hosting is
billed, and for system administrators who must deliver the numbers. It answers: which billing models fit, and what
can be measured reliably for each.

There are no prices on this page. Exponential has no license fee to pass on: "Using Exponential under the terms of
the GNU GPL is free of charge" (`README.md`). What you bill is your infrastructure and your work.

## Contents

1. [Billing models](#billing-models)
2. [What you can measure](#what-you-can-measure)
3. [Measuring disk use](#measuring-disk-use)
4. [Measuring requests](#measuring-requests)
5. [Measuring the database](#measuring-the-database)
6. [Billing for services](#billing-for-services)
7. [Questions to settle before launch](#questions-to-settle-before-launch)

## Billing models

| Model | How it works | Suits | Watch out for |
|---|---|---|---|
| Flat per plan | one fixed price per period and plan | shared and VPS plans | limits must be enforced, or heavy sites subsidize by light ones |
| Tiered | plans step up by storage, workers or environments | most offers | define what happens at the limit: upgrade, throttle or notify |
| Usage based | a base fee plus measured use (storage, requests, backups) | large or variable sites | customers need to see the meter; disputes need logs |
| Per site | a price per Exponential installation, regardless of size | agencies with many small sites | a multisite installation serves several sites from one installation; decide how you count it |
| Per service | hosting plus separately billed services (updates, migrations, support hours) | managed plans | write the scope of each service down |

> **Note**
> One Exponential installation can serve several sites through siteaccesses
> ([Multisite on one installation](../provisioning/multisite-on-one-installation.md)). If you bill "per site", say
> whether you mean per installation, per domain or per siteaccess.

## What you can measure

| Quantity | Source | Reliable for billing? |
|---|---|---|
| Disk use of uploads and image variations | the size of `var/storage` (or the cluster storage) | yes |
| Disk use of caches and logs | `var/cache`, `var/log` and the per-site directories under `var/` | yes, but caches are rebuilt and shrink; bill storage on `var/storage` |
| Database size | the database engine's own tools; for SQLite 3 the size of the database file | yes |
| Requests | the web server's or Velocity's access log | yes, if the log is complete and rotated without gaps |
| Engine workers | `Workers` in `settings/velocity.ini` (`[ServerSettings]`, shipped `Workers=4`), or `pm.max_children` of the PHP-FPM pool | yes, as a plan property |
| Backup volume | your backup system | yes |
| CPU time | operating system accounting per system user or container | depends on your platform |

## Measuring disk use

Measure the directories that hold customer data, at a fixed time each period:

```bash
du -s --block-size=1M /var/www/vhosts/<domain>/<installation>/var/storage
```

Where a site is clustered, the files are in the cluster storage instead; measure there
([Clustering and shared storage](../operations/clustering-and-shared-storage.md)).

## Measuring requests

- **Apache or nginx**: the access log of the virtual host.
- **Velocity**: its own access log, one line per request. `[LogSettings]` in `settings/velocity.ini` switches it on
  (`Enabled=enabled`, the default), keeps it under `Dir=var/vc/qbix/log`, names it after the default siteaccess
  (`site-access.log`) and writes the `combined` format by default, "what Apache and nginx write, for tools that read
  those". The same tools you use for Apache logs can count it.

> **Warning**
> An access log names the people who visited. Velocity writes it with `FileMode=0640` for that reason. Use the
> counts for billing; do not hand raw access logs to third parties. See [Compliance and GDPR](../security/compliance-and-gdpr.md).

Exponential's own request logs (`404.log`, `security.log`) are for diagnosis, not billing: they record only
not-found answers and suspicious requests ([Request logs](../operations/request-logs.md)).

## Measuring the database

| Database (official order) | How to read its size |
|---|---|
| 1. SQLite 3 | the size of the database file |
| 2. PostgreSQL | the server's database size functions |
| 3. MongoDB | the server's database statistics |
| 4. MariaDB | the server's table status / information schema |
| 5. Oracle | the server's segment sizes for the schema |
| 6. MySQL | the server's table status / information schema |

Where the database lives on a shared server, measure per database (or schema) and keep the measurement with the
invoice. More on each engine: [Database support](../../database-support.md).

## Billing for services

Services that have a natural unit:

| Service | Unit |
|---|---|
| Installation of a new site | per installation, see [One-click installers](../provisioning/one-click-installers.md) |
| Migration of a customer site | per project, after an assessment, see [Migrating customers](../migrations/migrating-customers-to-your-hosting.md) |
| Managed updates | per period, see [Automated updates with ezupdate](../operations/automated-updates-with-ezupdate.md) |
| Restore on request | per restore, or included up to a number per period |
| Support beyond the tier | per hour, see [Support tiers](support-tiers.md) |

## Questions to settle before launch

1. What happens when a site reaches a limit: notice, automatic upgrade, or throttling?
2. Which measurement is authoritative when the customer disagrees, and how long do you keep it?
3. Do backups count toward the storage quota?
4. Is a staging installation billed as a second site?
5. Does a multisite installation count as one site or several?
6. Are caches counted in storage? (Recommendation: no; they can be cleared and rebuilt.)

## Related pages

- [Designing hosting plans](designing-hosting-plans.md)
- [Storage layout](../operations/storage-layout.md)
- [Sizing memory, CPU and disk](../basics/sizing-memory-cpu-and-disk.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
