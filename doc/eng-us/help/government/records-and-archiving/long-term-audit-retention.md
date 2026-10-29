---
title: Long-term audit retention
audience: government, admin, hosting
category: public-sector
order: 170
summary: For public bodies whose records rules require keeping audit records for years - the audit guide's long-retention configuration (archives per channel, compression, write-once storage, signed checkpoints off the server), key management over the years, and the monthly verification routine.
keywords: long-term retention, audit archives, ArchiveDays, ArchiveFormat, xz, WORM, write-once storage, ArchiveDir, signing keys, checkpoints, webhook, verify archives, restore, compliance, records schedule, public sector
type: help
---
# Long-term audit retention

This page is for administrators and hosting teams of public bodies whose records schedule requires audit records for
longer than the default two years. The audit guide's reference configuration "Compliance: long retention" keeps ten
years of evidence compressed on write-once storage, with daily signed checkpoints sent off the server
([6.6 Compliance: long retention](../../../bc/6.0/audit.md#66-compliance-long-retention)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The configuration](#the-configuration)
2. [Storage](#storage)
3. [Keys over the years](#keys-over-the-years)
4. [Routine](#routine)
5. [Related pages](#related-pages)

## The configuration

In `settings/override/audit.ini.append.php` (excerpt of the guide's reference configuration; adapt the period):

```ini
[AuditChannel_content]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_access]
ArchiveDays=3650
ArchiveFormat=xz

[AuditChannel_system]
ArchiveDays=3650
ArchiveFormat=xz
Sinks[]=webhook

[AuditArchiveSettings]
ArchiveDir=/srv/worm/exponential-audit

[AuditIndexSettings]
KeepDays=730
```

The guide's version also enables `content.object.create`, `content.object.publish` and some commerce and data events,
and a webhook sink that receives the checkpoints, purges and key rotations.

## Storage

- `ArchiveDir` on a mount the web server can add files to but not change (write-once storage, or a backup target).
- Archives are compressed per day with a signed manifest; manifests are chained, so a removed day shows.
- The index keeps two years searchable; earlier years are read after `exp:audit restore`.
- Archive formats: gzip (always available), bzip2, xz, zstd, zip, depending on PHP extensions and binaries; the
  manifest names the handler used.

## Keys over the years

- Every `SigningKey[]` must be kept for as long as the archives it signed; rotation adds keys and never removes them.
- Store the backup of `settings/override/audit.ini.append.php` with the same retention as the archives.
- Rotate the signing key yearly (`./console exp:audit key rotate --apply --allow-root-user`).

## Routine

| When | Do |
|---|---|
| daily (automatic) | cronjob part archives, purges, signs checkpoints; webhook sends them off the server |
| monthly | `./console exp:audit verify --archives --allow-root-user`, keep the output |
| yearly | rotate the signing key; test a restore of an old day; check free space on the archive mount |
| on migration | copy archives and keys together; verify on the new server |

## Related pages

- [Audit guide, 4.2 Rotation and archives](../../../bc/6.0/audit.md#42-rotation-and-archives)
- [Audit records as evidence](audit-records-as-evidence.md)
- [OAIS framing](oais-framing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
