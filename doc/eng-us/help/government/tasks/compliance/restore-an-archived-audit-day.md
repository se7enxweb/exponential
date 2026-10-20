---
title: Restore an archived audit day
audience: government, admin
category: public-sector
order: 190
summary: Step by step - read audit records older than the 90 live days by restoring the archived day with exp:audit restore, check it against its signed manifest, search it, and know what happens to the restored copy.
keywords: audit archives, restore, exp:audit restore, archived day, manifest, sha256, search --files, LiveDays, ArchiveDays, old records, investigation, public sector
type: help
---
# Restore an archived audit day

For administrators. Audit days older than `LiveDays` (90) are compressed into archives with a signed manifest; the
console's index keeps two years of rows. To read the records of an archived day in full, restore it
([Audit guide, 4.4 Verify and restore](../../../../bc/6.0/audit.md#44-verify-and-restore)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Shell access as the site's user (or root with `--allow-root-user`; files are then written for the site's user).
- The channel (`content`, `access`, `system`, `commerce`, `read`) and the date.
- The day is still within `ArchiveDays` (730 by default); after that it was purged.
- Restoring is a change and is recorded as an audit event; it is refused while the audit cannot write if
  `OnWriteFailure=refuse`.

## Steps

1. See what is archived: **Setup > Audit** > Archives (`/audit/archives`, policy `audit/manage`), or list the archive
   directory.
2. Restore:

   ```bash
   ./console exp:audit restore --channel=access --date=<YYYY-MM-DD> --allow-root-user
   ```

   The day is decompressed into `<LogDir>/restored/` and checked against the manifest's sha256. `--to=<dir>` writes it
   elsewhere.
3. Read it:

   ```bash
   ./console exp:audit search --files --channel=access --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --user=<id> --allow-root-user
   ```

## Check that it worked

- The restore command reports the file and the sha256 check.
- `./console exp:audit verify --archives --channel=access --date=<YYYY-MM-DD> --allow-root-user` reports the day intact.
- Remove a copy written with `--to` when the task is done; it contains personal data.

## Related pages

- [Audit retention](../../privacy/audit-retention.md)
- [Long-term audit retention](../../records-and-archiving/long-term-audit-retention.md)
- [Export audit records for an investigation](export-audit-records-for-an-investigation.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
