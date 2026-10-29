---
title: Raise the version history limit for records
audience: government, admin
category: public-sector
order: 300
summary: Task for administrators: keep more versions of record-relevant classes (decisions, disclosure entries, meetings) than the default 45, with VersionHistoryClass in content.ini, and check the effect.
keywords: version limit, DefaultVersionHistoryLimit, VersionHistoryClass, content.ini, versions, records, cleanupversions.php
type: help
---
# Raise the version history limit for records

This task is for administrators. When an object reaches its version limit, the oldest archived version is removed to
make room. Background: [Keeping versions longer](../../transparency-and-foi/keeping-versions-longer.md).

## Before you start

- Agree the number with your records officer.
- Know the class identifiers (**Setup > Classes**).

## Steps

1. Create or edit `settings/override/content.ini.append.php`:

   ```ini
   <?php /*
   [VersionManagement]
   VersionHistoryClass[decision]=500
   VersionHistoryClass[disclosure_entry]=200
   VersionHistoryClass[meeting]=200
   */ ?>
   ```

   The shipped values are `DefaultVersionHistoryLimit=45` and `VersionHistoryClass[1]=75`.
2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`; with Velocity also `./console exp:velocity cache clear --apply`.

## Check

**Ini settings** (`/settings/view`) shows the value for `content.ini`; after more than 45 edits of a decision, its
**Versions** page still lists the early versions.

## Problems

| Problem | Note |
|---|---|
| Versions already removed | the new limit applies from now on; removed versions are gone |
| Lowering a limit later | does not remove versions until `php bin/php/cleanupversions.php --apply` runs; back up first |

## Related pages

- [Version history as accountability](../../transparency-and-foi/version-history-as-accountability.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
