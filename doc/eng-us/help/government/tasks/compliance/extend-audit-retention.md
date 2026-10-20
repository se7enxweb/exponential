---
title: Extend audit retention
audience: government, admin
category: public-sector
order: 240
summary: Step by step - keep Exponential's audit archives longer than the shipped 730 days when your records schedule requires it, per channel, with an archive directory on write-once storage, and check the result.
keywords: audit retention, ArchiveDays, LiveDays, ArchiveFormat, ArchiveDir, write-once, WORM, exp:ini set, records schedule, evidence, purge dry-run, public sector
type: help
---
# Extend audit retention

For administrators, on a decision of the records manager. The shipped retention is 90 days live and 730 days archived
per channel; the shipped channel blocks set both values, so change them per channel
([Audit retention](../../privacy/audit-retention.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The decided period per channel (for example `access` and `system` 3650 days).
- Disk space: check the archive sizes on **Setup > Audit** > Archives; `xz` compresses more than the default gzip.
- Optionally a write-once mount for the archives.

## Steps

1. Preview and set the values:

   ```bash
   ./console exp:ini set audit.ini/AuditChannel_access/ArchiveDays 3650 global --dry-run --allow-root-user
   ./console exp:ini set audit.ini/AuditChannel_access/ArchiveDays 3650 global --allow-root-user
   ./console exp:ini set audit.ini/AuditChannel_access/ArchiveFormat xz global --allow-root-user
   ```

   Repeat for the other channels concerned.
2. Optionally move archives to a dedicated mount, in `settings/override/audit.ini.append.php`:

   ```ini
   [AuditArchiveSettings]
   ArchiveDir=/srv/worm/exponential-audit
   ```

   (create it first, owned by the site's user, mode 0750).
3. Clear the INI cache; reload PHP-FPM; restart Velocity.

## Check that it worked

- `./console exp:audit purge --dry-run --allow-root-user` no longer lists archives younger than the new period.
- **Setup > Audit** (`/audit/settings`) shows the values and the file they come from.
- Keep the signing keys for the whole period (see "Rotate the audit signing key").

## Related pages

- [Audit retention](../../privacy/audit-retention.md)
- [Long-term audit retention](../../records-and-archiving/long-term-audit-retention.md)
- [Audit guide, 4.3 Retention](../../../../bc/6.0/audit.md#43-retention)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
