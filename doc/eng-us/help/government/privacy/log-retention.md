---
title: Log retention
audience: government, admin, hosting
category: public-sector
order: 100
summary: For administrators of public sites - how Exponential rotates and prunes its logs, the age limits that ship for 404, security and CSP logs, why other kernel logs have none by default, and how to set MaxAgeDays per log, run pruning on a schedule and check it.
keywords: log retention, log rotation, pruning, MaxAgeDays, LogFileMaxAge, LogFileRotateCount, LogFileMaxSize, logviewer.ini RotationSettings, exp:logs, cronjob logs, logrotate, web server logs, storage limitation, public sector
type: help
---
# Log retention

This page is for administrators and hosting teams. Logs contain personal data (masked addresses, user agents, URLs).
Exponential rotates every log by size; only the request logs have age limits by default. The project lists "logs
without an age limit by default" as a known gap ([Privacy standards: Known gaps](../../../standards/privacy.md#known-gaps)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [How rotation works](#how-rotation-works)
2. [The defaults](#the-defaults)
3. [Setting age limits](#setting-age-limits)
4. [Pruning on a schedule](#pruning-on-a-schedule)
5. [Logs outside Exponential](#logs-outside-exponential)
6. [Related pages](#related-pages)

## How rotation works

When a log outgrows `site.ini [FileSettings] LogFileMaxSize` (2 MB), the next write rotates it: `.1` becomes `.2` and
so on, and archives beyond `LogFileRotateCount` (3) are removed. A **prune** removes archives beyond the count and
older than the age limit; a rotation never removes by age
([Log rotation](../../../features/6.0/log-rotation.md)).

## The defaults

| Log | Archives | Age limit |
|---|---|---|
| `404.log` | 5, compressed, 10 MB each | 30 days |
| `security.log` | 5, compressed, 10 MB each | 90 days |
| `csp.log` | 5, compressed, 10 MB each | 30 days |
| `error.log`, `warning.log`, `notice.log`, `debug.log`, `auth.log`, `storage.log`, cronjob and extension logs | 3, by size | none (`LogFileMaxAge=0`) |

## Setting age limits

Per log, by its key (the key of the log viewer's permalinks, such as `error`, `auth`, `site-storage`):

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
MaxAgeDays[error]=90
MaxAgeDays[auth]=90
MaxAgeDays[notice]=30
Compress[error]=enabled
```

or one limit for every log without its own entry, in `settings/override/site.ini.append.php`:

```ini
[FileSettings]
LogFileMaxAge=90
```

Choose the values with your DPO: long enough to investigate incidents, short enough for storage limitation.

## Pruning on a schedule

Age limits act when a prune runs:

```bash
./console exp:logs --archives --allow-root-user          # each log, its archives, sizes and limits
./console exp:logs --prune --dry-run --allow-root-user   # what would go
./console exp:logs --prune --allow-root-user             # remove it
```

Or the cronjob part `logs` (in no default group, so add it), as the site's user:

```
15 3 * * * cd /path/to/exponential && php runcronjobs.php -q logs
```

Rotations and prunes are recorded in the audit as `system.log.rotate` and `system.log.delete`. If the system's
`logrotate` manages `var/log` instead, set `LogFileRotateCount=0` so the kernel does not rotate as well.

## Logs outside Exponential

The web server's and PHP-FPM's logs, Velocity's logs, request-shield's log and the mail server's logs are rotated by
their own programs; set their retention there or ask your host.

## Related pages

- [Log rotation and pruning](../../../features/6.0/log-rotation.md)
- [Masking in logs and the log viewer](log-masking.md)
- [Log and data retention for hosts](../../hosting/security/log-and-data-retention.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
