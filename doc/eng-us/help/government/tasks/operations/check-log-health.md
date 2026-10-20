---
title: Check log health
audience: government, admin, hosting
category: public-sector
order: 140
summary: Step by step - read Setup > Log files > Log health for disk use against the budget, fast-growing logs, logs over their limit, logs the web server cannot write, and the rotation settings in effect, and act on each.
keywords: log health, disk budget, log growth, writable logs, mixed owners, rotation settings, logshealth, DiskBudget, log size, permissions
type: help
---
# Check log health

This task is for administrators doing a weekly check. At the end you know the logs are within budget, rotating and
writable. Reference: [Log viewer, "Log health"](../../../../features/6.0/log-viewer.md#log-health).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Acting on findings](#acting-on-findings)
4. [Related pages](#related-pages)

## Before you start

- `setup/logs`; `setup/managelogs` to act from the page.

## Steps

1. **Setup > Log files**, then **Log health** (`/setup/logshealth`).
2. **Disk use**: all logs and archives against `logviewer.ini [HealthSettings] DiskBudget` (256 MB by default); amber
   from 80 %, red above.
3. **Fastest growing**: lines and bytes an hour.
4. **Over their size limit**: logs that should have rotated.
5. **Not writable by the web server**: with a warning when logs have mixed owners.
6. **Every log** and **Rotation settings in effect**: each log's maximum size, archives kept, compression and age,
   with the file and setting each comes from.

## Acting on findings

| Finding | Do |
|---|---|
| Over budget | prune archives ([Rotate and prune logs](rotate-and-prune-logs.md)); review retention |
| One log growing fast | find the cause in its newest lines; a debug setting left on is common |
| Not writable / mixed owners | make the logs owned by the web server's user; run commands as that user, not root ([Check file and directory permissions](check-file-and-directory-permissions.md)) |
| Rotation not as intended | set `logviewer.ini [RotationSettings]` per log, through your change process |

## Related pages
- [Read the error digest](read-the-error-digest.md)
- [Log retention and rotation](../../operations/monitoring/log-retention-and-rotation.md)
- [Disk full](../../operations/incidents/disk-full.md)
- [Log viewer and rotation (hosting)](../../../hosting/operations/log-viewer-and-rotation.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
