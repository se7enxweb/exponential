---
title: Disk full
audience: government, admin, hosting
category: public-sector
order: 130
summary: Runbook for a server whose disk filled up under a public website - find what grew, free space only through the safe commands (caches, log archives), never touch uploads, settings or the database by hand, and watch what the audit trail did meanwhile.
keywords: disk full, no space left, df, du, caches, log archives, exp:logs prune, ezcache, var/storage, SQLite WAL, audit OnWriteFailure, Log health, DiskBudget, incident
type: help
---
# Disk full

This runbook is for administrators when the disk under a public website is full. A full disk causes most "site down"
symptoms. The detailed procedure is [Disk full (hosting runbook)](../../../hosting/runbooks/disk-full.md); this page
adds the checks a public body needs afterwards.

## Contents
1. [Find what grew](#find-what-grew)
2. [Free space safely](#free-space-safely)
3. [What never to remove](#what-never-to-remove)
4. [The audit trail while the disk was full](#the-audit-trail-while-the-disk-was-full)
5. [Prevent it](#prevent-it)
6. [Related pages](#related-pages)

## Find what grew

```bash
df -h
du -sh var/* | sort -h | tail -15
./console exp:logs --list
```

Look outside the installation too: backups written to the same disk, the database's data directory, system logs.

## Free space safely

```bash
php bin/php/ezcache.php --clear-all --allow-root-user   # caches are regenerated
./console exp:logs --archives                           # see the log archives
./console exp:logs --prune --dry-run                    # what pruning would remove
./console exp:logs --prune
./console exp:logs --compress
```

A log growing by megabytes a minute is a symptom: find out why before emptying it with
`./console exp:logs --truncate=<key>` (the content stays in the `.1` archive). Warm the caches again when there is
space: `php bin/php/warm.php --allow-root-user`.

## What never to remove

| Path | Why |
|---|---|
| `var/storage`, `var/<site>/storage` | uploads and images: the organization's content |
| `settings/` | configuration |
| `var/storage/sqlite3/` | the SQLite database with its `-wal` and `-shm` files |
| `var/<site>/log/audit/` | the audit trail: evidence; archive it, do not delete it |

Do not run `rm -rf` under `var/` by hand during an incident; use the commands above.

## The audit trail while the disk was full

With `[AuditSettings] OnWriteFailure=continue` (the default) the site went on and unwritten records were noted in
`error.log` as `AUDIT-UNWRITTEN`; the audit dashboard warns. With `refuse`, security-relevant actions were refused
(`AUDIT-REFUSED`) ([Audit trail, "When the audit cannot write"](../../../../features/6.0/audit-trail.md#when-the-audit-cannot-write-onwritefailure)).
Afterwards:

```bash
./console exp:logs --search=AUDIT-UNWRITTEN --file=var/log/error.log
./console exp:audit verify --allow-root-user
```

Record the gap in the audit trail in your incident notes: an auditor will ask about it.

## Prevent it

- **Log health** warns from 80 % of `[HealthSettings] DiskBudget` for the logs; **Setup > System information** warns
  under 5 GB or 10 % free and fails under 1 GB or 5 % ([System information and health checks](../monitoring/system-information-and-health-checks.md)).
- Retention set per log ([Log retention and rotation](../monitoring/log-retention-and-rotation.md)).
- Backups written to another disk or system ([Offsite and encrypted copies](../backups-and-recovery/offsite-and-encrypted-copies.md)).
- Your monitoring system's disk alerts on every volume.

## Related pages
- [Site down](site-down.md)
- [Rotate and prune logs](../../tasks/operations/rotate-and-prune-logs.md)
- [Check log health](../../tasks/operations/check-log-health.md)
- [Storage layout (hosting)](../../../hosting/operations/storage-layout.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
