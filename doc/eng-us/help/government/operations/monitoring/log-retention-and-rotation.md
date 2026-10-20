---
title: Log retention and rotation
audience: government, admin, hosting
category: public-sector
order: 130
summary: How long Exponential keeps its logs and audit records by default, how rotation, compression and pruning work, how to set retention per log to match a public body's records and data protection rules, and how to keep the record when logs must go.
keywords: log retention, log rotation, prune, compress, LogFileMaxSize, LogFileRotateCount, LogFileMaxAge, audit retention, LiveDays, ArchiveDays, data protection, records retention, exp:logs
type: help
---
# Log retention and rotation

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

This page is for administrators, records officers and data protection staff who must keep logs long enough for
security and audit, and no longer than allowed. Sources: [Log rotation and pruning](../../../../features/6.0/log-rotation.md),
[Audit trail](../../../../features/6.0/audit-trail.md#what-is-recorded) and
[Log and data retention (hosting)](../../../hosting/security/log-and-data-retention.md).

## Contents
1. [The defaults](#the-defaults)
2. [Setting retention per log](#setting-retention-per-log)
3. [The audit trail's own retention](#the-audit-trails-own-retention)
4. [Rotation on demand and by cron](#rotation-on-demand-and-by-cron)
5. [Deciding your periods](#deciding-your-periods)
6. [Related pages](#related-pages)

## The defaults

| Log | Default | Source |
|---|---|---|
| `error.log`, `warning.log`, `debug.log`, `storage.log`, cronjob and extension logs | rotated at 2 MB, 3 archives, no age limit | `site.ini [FileSettings] LogFileMaxSize`, `LogFileRotateCount`, `LogFileMaxAge=0` |
| `404.log` | 10 MB, 5 archives, compressed, pruned after 30 days | `logviewer.ini [RotationSettings]` |
| `security.log` | 10 MB, 5 archives, compressed, pruned after 90 days | the same |
| `csp.log` | 10 MB, 5 archives, compressed, pruned after 30 days | the same |
| Audit channels `content`, `access`, `system`, `commerce` | 90 days live, 730 days archived | `settings/audit.ini` |
| Audit channel `read` (only when reads are on) | 30 days live, 90 archived | the same |

With the defaults a kernel log takes at most about 8 MB on disk. Note that size-based rotation alone keeps logs for
however long 3 archives last, which on a busy site can be days, on a quiet one months.

## Setting retention per log

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
MaxSize[error]=10485760
Keep[error]=10
Compress[error]=enabled
MaxAgeDays[error]=180
```

`Keep` sets the number of archives, `MaxAgeDays` an age limit applied when pruning. Logs without an entry use the
`site.ini [FileSettings]` values. **Log health** shows the settings in effect for each log and where each comes from.

## The audit trail's own retention

Per channel in `settings/override/audit.ini.append.php`: `LiveDays`, `ArchiveDays`, `MaxFileSize`, `ArchiveFormat`.
Archives are compressed, read back and compared before the live file goes, and listed in a signed manifest. Privacy
tools (pseudonymization, purge) are in [the audit guide, section 4.12](../../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks).

## Rotation on demand and by cron

```bash
./console exp:logs --archives                # each log, its archives, sizes and limits
./console exp:logs --rotate-all --dry-run    # what would be rotated
./console exp:logs --prune --dry-run         # archives beyond Keep and MaxAgeDays
./console exp:logs --prune                   # remove them
```

The cronjob part `logs` (in no default part) rotates, prunes and compresses on a schedule:
`php runcronjobs.php -q logs`. Rotations and prunes are recorded as `system.log.rotate` and `system.log.delete`. If the
system's `logrotate` manages `var/log` instead, set `LogFileRotateCount=0` so the two do not both rotate.

## Deciding your periods

| Consideration | Points toward |
|---|---|
| Investigating incidents found late | longer `security.log` and audit `access` retention |
| Data protection (addresses, user agents, even masked) | shorter request log retention |
| Records rules for decisions and publications | audit `content` channel and version history, not the technical logs |
| Disk budget | **Log health**, `DiskBudget` |

Write the periods into your records schedule and configure them in settings under change control.

## Related pages
- [Rotate and prune logs](../../tasks/operations/rotate-and-prune-logs.md)
- [Audit trail archives and backups](../backups-and-recovery/audit-trail-archives-and-backups.md)
- [Privacy standard](../../../../standards/privacy.md)
- [Error digest and log health](error-digest-and-log-health.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
