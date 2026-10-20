---
title: Audit retention
audience: government, admin, project-manager
category: public-sector
order: 90
summary: For public bodies deciding how long to keep audit records - the shipped 90 days live and 730 days archived per channel, how to shorten or lengthen them in settings, what purge and pseudonymization do, keys needed for old archives, and records schedules that require longer.
keywords: audit retention, LiveDays, ArchiveDays, KeepDays, purge, archive, pseudonymization, signing keys, records schedule, evidence, exp:audit purge, exp:ini set, storage limitation, public sector
type: help
---
# Audit retention

This page is for data protection officers, records managers and administrators. Audit records serve accountability
and security, and they contain personal data; their retention is a balance your organization decides. Exponential
removes them automatically according to settings per channel
([4.3 Retention](../../../bc/6.0/audit.md#43-retention)).

![The audit settings: index, pseudonymised after and kept in the index, keys, sinks and archive formats](../../images/government/gov-audit-settings.webp)

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The defaults](#the-defaults)
2. [Changing them](#changing-them)
3. [What happens at the end](#what-happens-at-the-end)
4. [Longer retention](#longer-retention)
5. [Related pages](#related-pages)

## The defaults

| Channel | LiveDays (uncompressed) | ArchiveDays (compressed archives) |
|---|---|---|
| content, access, system, commerce | 90 | 730 |
| read (off by default) | 30 | 90 |

The index the console searches keeps rows for `[AuditIndexSettings] KeepDays` (730) and pseudonymizes personal fields
after `PseudonymiseAfterDays` (90). The shipped channel blocks set both values themselves, so set them per channel; a
default in `[AuditRotationSettings]` would not be read.

## Changing them

Preview first, then write to `settings/override/audit.ini.append.php`:

```bash
./console exp:ini set audit.ini/AuditChannel_access/ArchiveDays 365 global --dry-run --allow-root-user
./console exp:ini set audit.ini/AuditChannel_access/ArchiveDays 365 global --allow-root-user
```

or edit the override by hand:

```ini
[AuditChannel_access]
LiveDays=90
ArchiveDays=365
```

The change itself is recorded as `system.setting.write`. Check the result on **Setup > Audit** (`/audit/settings`,
read-only, shows each value and the file it comes from).

## What happens at the end

- The daily task of the cronjob part archives days older than `LiveDays` (verified, compressed, signed manifest) and
  purges archives older than `ArchiveDays`; each removal is recorded.
- Preview: `./console exp:audit purge --dry-run --allow-root-user`.
- Purging never removes a signing key from the settings; the command names the keys that remaining archives still
  need. Remove a key yourself only when no archive signed with it remains.
- Erasure requests do not delete audit records before the end of their retention: deleting one would break the chain;
  the guide refers to art. 17(3)(b) and (e) and to the pseudonymization already applied.

## Longer retention

If a records schedule or a law requires keeping audit records longer, the guide's reference configuration "Compliance:
long retention" keeps ten years compressed with `xz` on write-once storage
([6.6 Compliance: long retention](../../../bc/6.0/audit.md#66-compliance-long-retention)):

```ini
[AuditChannel_access]
ArchiveDays=3650
ArchiveFormat=xz

[AuditArchiveSettings]
ArchiveDir=/srv/worm/exponential-audit
```

Keep every signing key for as long as the archives it signed, store the settings backup with the same retention, and
run `./console exp:audit verify --archives --allow-root-user` monthly. The records section of this help covers audit
records as evidence.

## Related pages

- [Audit guide, 4.3 Retention](../../../bc/6.0/audit.md#43-retention)
- [Privacy in the audit trail](audit-trail-privacy.md)
- [Retention of personal data: an overview](retention-overview.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
