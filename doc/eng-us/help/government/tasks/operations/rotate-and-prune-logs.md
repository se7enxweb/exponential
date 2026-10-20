---
title: Rotate and prune logs
audience: government, admin, hosting
category: public-sector
order: 150
summary: Step by step - see each log's archives and limits, rotate logs now, prune archives beyond the limits, compress them, and schedule it with the logs cronjob part, always with a dry run first.
keywords: log rotation, prune logs, compress logs, exp:logs, --archives, --rotate-all, --prune, --dry-run, logs cronjob, logrotate, retention
type: help
---
# Rotate and prune logs

This task is for administrators managing disk use and retention. Every operation is planned first (`--dry-run`) and
then carried out step by step. Reference: [Log rotation and pruning](../../../../features/6.0/log-rotation.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Schedule it](#schedule-it)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- Shell access as the user that owns the logs (or root; new logs keep their owner).
- Know your retention rules ([Log retention and rotation](../../operations/monitoring/log-retention-and-rotation.md)).
  Audit records are not touched by this; the audit trail has its own retention.

## Steps

```bash
./console exp:logs --archives                    # each log, its archives, sizes and limits
./console exp:logs --rotate-all --dry-run        # what would be rotated
./console exp:logs --rotate-all                  # rotate the logs over their MaxSize
./console exp:logs --prune --dry-run             # archives beyond Keep and MaxAgeDays
./console exp:logs --prune                       # remove them
./console exp:logs --compress                    # compress plain archives from .2 on
```

Name logs by key (`error`, `debug`, `<site>-storage`) to act on some only: `--rotate=error,debug`,
`--prune=error`. Exit codes: 0 done, 1 nothing to do, 2 an unknown name or a failure. In the browser, **Rotate now**
on a log appears only when `logviewer.ini [ActionSettings] Rotate=enabled` and you hold `setup/managelogs`.

## Schedule it

The cronjob part `logs` is in no default part; run it nightly as the site's user:

```
15 3 * * * cd /path/to/installation && php runcronjobs.php -q logs
```

If the system's `logrotate` manages `var/log` instead, set `site.ini [FileSettings] LogFileRotateCount=0` so the two
do not both rotate.

## Check it worked

- `./console exp:logs --archives` shows the expected archives.
- **Log health** shows disk use within budget.
- The audit trail records `system.log.rotate` and `system.log.delete`.

## Related pages
- [Check log health](check-log-health.md)
- [Disk full](../../operations/incidents/disk-full.md)
- [Log and data retention (hosting)](../../../hosting/security/log-and-data-retention.md)
- [Log viewer and rotation (hosting)](../../../hosting/operations/log-viewer-and-rotation.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
