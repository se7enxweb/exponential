---
title: Upgrade check before and after
audience: government, admin, project-manager
category: public-sector
order: 110
summary: Using Setup > Upgrade check and its command-line counterpart as evidence around a change - the file consistency check against the release manifest, the database consistency check, the downloadable reports, and how to read each finding.
keywords: upgrade check, file consistency, database consistency, checkmanifest, filelist.md5, schema check, evidence, before and after, modified files, audit, change record
type: help
---
# Upgrade check before and after

This page is for administrators who need evidence that the code and database are what they should be, before and
after a change. **Setup > Upgrade check** (`/setup/systemupgrade`, page title "System upgrade check") has two checks,
described in [The upgrade check](../../../../guides/upgrade-check.md).

## Contents
1. [The two checks](#the-two-checks)
2. [Before a change](#before-a-change)
3. [After a change](#after-a-change)
4. [Reading the findings](#reading-the-findings)
5. [From the shell](#from-the-shell)
6. [Related pages](#related-pages)

## The two checks

| Check | Compares | Button | Download |
|---|---|---|---|
| File consistency | the files on disk with the release's manifest `share/filelist.md5` (and the manifests of active extensions that have one) | **Check file consistency** | **Download CSV**, **Download text** |
| Database consistency | the database schema with `share/db_schema.dba` and those of active extensions | **Check database consistency** | **Download SQL** |

Both only read; the page has no button that writes. Each run and download is recorded in the audit trail as
`system.upgrade.run`.

## Before a change

1. Run both checks; download the reports.
2. Files reported as modified that you changed on purpose: list them, because an upgrade replaces them.
3. Database differences: understand them now, not during the window.
4. Attach the reports to the change record.

## After a change

1. Run both checks again.
2. Expected: the files you listed are still the only modified ones (or none, if you moved your changes into an
   extension, which is the better place); the database check shows no missing table.
3. Attach the reports; compare with "before".

## Reading the findings

| Finding | What to do |
|---|---|
| Modified file you did not expect | find out who changed it and why; restore it from the release if unauthorized ([Defacement](../incidents/defacement.md)) |
| Missing file | restore from the release |
| Missing table | run its CREATE statement after a backup |
| Changed table | read each statement; those marked **Removes something** can remove data |
| Table not in the schema | usually an own extension's table; leave it |
| Table of an extension that is not active | no SQL is written for it; activate the extension or leave it |
| "File share/filelist.md5 does not exist." | copy the manifest of the release you run |

## From the shell

```bash
php bin/php/checkmanifest.php --all                 # exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also every extension with its own manifest
php bin/php/checkmanifest.php --all --extensions --csv > report.csv
```

The command needs no kernel, settings or database, so it also runs on a broken site. A non-zero exit code makes it
usable in monitoring ([Upgrade check as a monitor](../monitoring/upgrade-check-as-a-monitor.md)).

## Related pages
- [Check upgrade consistency](../../tasks/operations/check-upgrade-consistency.md)
- [Check the database schema](../../tasks/operations/check-the-database-schema.md)
- [File consistency check](../../../../features/6.0/file-consistency-check.md)
- [Upgrade overview for the public sector](upgrade-overview-for-public-sector.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
