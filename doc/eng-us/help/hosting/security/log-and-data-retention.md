---
title: Log and data retention
audience: hosting, admin
category: hosting
order: 140
summary: How long an Exponential site keeps each log and each kind of record by default, which setting changes it, and which cronjob or command enforces it: kernel logs, request and CSP logs, Velocity's logs, the audit trail and its index, the mail consent log and suppression list, trash, content jobs and sessions. With the parts a host owns: backups and server logs.
keywords: retention, log retention, data retention, rotation, prune, LogFileRotateCount, LogFileMaxAge, RotationSettings, MaxAgeDays, audit LiveDays, ArchiveDays, KeepDays, PseudonymiseAfterDays, RetentionDays, consent log, suppression list, trash KeepItemsForDays, content jobs, sessions, backups, GDPR, storage limitation
type: help
---
# Log and data retention

This page is for the system administrators and compliance staff of a host. It lists, for every log and record an
Exponential site keeps, the default retention, the setting that changes it, and what enforces it. Use it to write the
retention section of a plan description, or to answer a customer whose privacy notice states a period. Settings are
changed in overrides (`settings/override/<file>.ini.append.php`); clear the INI cache afterwards.

> **Note**
> Which periods are right is a legal and business decision of the host and the customer, not of the software. This
> page is not legal advice ([Compliance and GDPR](compliance-and-gdpr.md)).

## Contents

1. [At a glance](#at-a-glance)
2. [Kernel logs](#kernel-logs)
3. [Request, security and CSP logs](#request-security-and-csp-logs)
4. [Velocity's logs](#velocitys-logs)
5. [The audit trail](#the-audit-trail)
6. [Mail consent and suppression](#mail-consent-and-suppression)
7. [Content and sessions](#content-and-sessions)
8. [What the host owns](#what-the-host-owns)
9. [Checklist](#checklist)

## At a glance

| Data | Default retention | Setting | Enforced by |
|---|---|---|---|
| `error.log`, `warning.log`, `notice.log`, `debug.log`, `storage.log` ... | 3 archives, rotated at 2 MB; no age limit | `site.ini [FileSettings] LogFileRotateCount`, `LogFileMaxSize`, `LogFileMaxAge` (`0`) | the writer; `exp:logs --prune`; cronjob part `logs` |
| `404.log` | 5 archives, 10 MB, pruned after 30 days | `logviewer.ini [RotationSettings]` key `404` | cronjob part `logs`, `exp:logs` |
| `security.log` | 5 archives, 10 MB, pruned after 90 days | key `security` | the same |
| `csp.log` | 5 archives, 10 MB, pruned after 30 days | key `csp` | the same |
| Velocity access and error logs | rotated by date | `velocity.ini [LogSettings]` | the engine |
| Audit, channels `content`, `access`, `system`, `commerce` | 90 days live, 730 days archived | `audit.ini [AuditChannel_<channel>] LiveDays`, `ArchiveDays` | cronjob part `audit` (daily) |
| Audit, channel `read` (off by default) | 30 days live, 90 archived | `[AuditChannel_read]` | the same |
| Audit index | 730 days; pseudonymised after 90 | `[AuditIndexSettings] KeepDays`; `[AuditPrivacySettings] PseudonymiseAfterDays` | the same |
| Mail consent log of people who are gone | 1095 days | `mailpreferences.ini [ConsentSettings] RetentionDays` | cronjob part `mailpreferences` |
| Pending mail confirmations | 7 days | `[ConsentSettings] PendingDays` | the same |
| Suppression list | kept (`0`) | `[SuppressionSettings] RetentionDays` | the same, when above 0 |
| Trash | purged whole when the cronjob runs (empty value) | `content.ini [TrashSettings] KeepItemsForDays` | `cronjobs/trashpurge.php`, when scheduled |
| Finished content jobs | 30 days | `content.ini [ContentJobSettings] KeepDays` | cronjob part `contentjobs` |
| Sessions | `SessionTimeout` 259200 s (3 days) | `site.ini [Session]` | PHP's session GC; `bin/php/ezsessiongc.php` on demand |

## Kernel logs

When a log outgrows `LogFileMaxSize` (2 MB), the next write rotates it and the archives beyond `LogFileRotateCount` (3)
are removed. `LogFileMaxAge` (`0`, no limit) adds an age limit that a prune applies; `LogFileCompress=enabled`
compresses archives from `.2` on ([Log rotation](../../../features/6.0/log-rotation.md)). Per log, by its key:

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
Keep[error]=10
MaxAgeDays[error]=90
Compress[error]=enabled
```

On demand, or nightly as the site's user:

```bash
./console exp:logs --prune --dry-run
./console exp:logs --apply --prune
# crontab: 15 3 * * * cd /var/www/vhosts/<domain>/<installation> && php runcronjobs.php -q logs
```

The cronjob part `logs` is in no default group, so it runs only when scheduled. If the system's `logrotate` manages
`var/log` instead, set `LogFileRotateCount=0` so the kernel does not rotate as well. Each change made by the part is
recorded in the audit trail.

## Request, security and CSP logs

These ship with their own limits in `logviewer.ini [RotationSettings]` (keys `404`, `security`, `csp`: `MaxSize`
10485760, `Keep` 5, `Compress` enabled, `MaxAgeDays` 30, 90 and 30). To keep `security.log` longer
([Request logs](../../../features/6.0/request-logs.md#the-log-viewer-rotation-and-the-error-digest)):

```ini
# settings/override/logviewer.ini.append.php
[RotationSettings]
Keep[security]=10
MaxAgeDays[security]=365
```

## Velocity's logs

Velocity writes `<siteaccess>-access.log` and `<siteaccess>-error.log` below `var/vc/qbix/log/` (`[LogSettings] Dir`),
in Apache's combined format, with modes `0640` and `0750`. Rotation puts the date before the extension
(`site-access.2026-09-24.log`) ([8.3.12 Logs](../../../install/08-serving-the-site.md#8312-logs)). The repository
documents no age limit for these dated files; remove old ones with your own scheduled job, at the period your plan
states.

## The audit trail

Per channel in `[AuditChannel_<channel>]`: `LiveDays` (uncompressed) and `ArchiveDays` (kept as archives). The shipped
channel blocks set both values, so set them per channel, not in `[AuditRotationSettings]`
([4.3 Retention](../../../bc/6.0/audit.md#43-retention)):

```ini
# settings/override/audit.ini.append.php: one year of archives instead of two
[AuditChannel_content]
ArchiveDays=365

[AuditChannel_access]
ArchiveDays=365

[AuditChannel_system]
ArchiveDays=365

[AuditChannel_commerce]
ArchiveDays=365
```

- Removal is automatic, daily, and each removal is itself recorded.
- Retention never removes a signing key: `exp:audit purge` names the keys retained archives still need. Keep every
  `SigningKey[]` as long as the archives it signed.
- Long retention (for example ten years on write-once storage) is the
  [compliance configuration](../../../bc/6.0/audit.md#66-compliance-long-retention); minimal retention with hashed
  fields is the [strict-privacy configuration](../../../bc/6.0/audit.md#64-strict-privacy).
- Records forwarded by a sink are kept by the receiving system under its own rules.

## Mail consent and suppression

The cronjob part `mailpreferences` (group `infrequent`) removes the consent log rows of people who are gone once they
are older than `RetentionDays` (1095 days: the account's lifetime plus three years), removes confirmations that waited
longer than `PendingDays` (7), and, when `[SuppressionSettings] RetentionDays` is above 0, old suppression entries
([9. Retention and the cronjob](../../../guides/mail-preferences-administrator.md#9-retention-and-the-cronjob)).

```bash
./console exp:mail:consent cleanup --dry-run
php runcronjobs.php mailpreferences
```

Keep in mind that removing a suppression entry lets optional mail reach that address again if the person had switched
it on before.

## Content and sessions

- **Trash**: `[TrashSettings] KeepItemsForDays` empty or `0` means the `trashpurge` cronjob (`cronjobs/trashpurge.php`)
  purges the whole trash, as before; a number keeps items that many days (up to 36500). `bin/php/trashpurge.php` takes
  `--trashed-days=<days>`.
- **Content jobs**: finished jobs are removed after `[ContentJobSettings] KeepDays=30` (`0` keeps them).
- **Sessions**: `SessionTimeout` (259200 seconds) sets `session.gc_maxlifetime` with PHP's own session handler;
  `bin/php/ezsessiongc.php` removes expired sessions as defined by `SessionTimeout` on demand.
- **Versions and drafts** of content follow the content settings of the site; they are content, and their retention is
  the customer's editorial decision.

## What the host owns

- **Backups** hold everything above, including logs and personal data, for as long as the backup is kept. Decide
  backup retention together with these periods ([Backup schedules and retention](../operations/backup-schedules-and-retention.md)).
- **Web server, PHP-FPM, panel and mail server logs** are outside Exponential; their retention is the host's
  configuration.
- **Central log platforms and SIEMs** that receive audit sinks or log copies keep them under their own rules.
- **Exported files** (audit exports, consent CSVs, data-subject exports in `var/tmp/`) should be deleted when the
  request they answer is done.

## Checklist

- [ ] The retention of each row of the table above decided and written into the plan description.
- [ ] Cronjob groups `frequent` and `infrequent` running; the `logs` part scheduled if age limits are wanted.
- [ ] `LogFileMaxAge` or per-log `MaxAgeDays` set where the plan states a period for kernel logs.
- [ ] Velocity's dated log files removed by a scheduled job at the stated period.
- [ ] Audit `ArchiveDays` per channel agreed with the customer; signing keys kept as long as archives.
- [ ] Backup retention aligned with the log and data periods.
- [ ] Exported personal data deleted after use.

## Related pages

- [Security for hosts: overview](README.md)
- [Compliance and GDPR](compliance-and-gdpr.md)
- [Log viewer and rotation](../operations/log-viewer-and-rotation.md)
- [Audit trail](audit-trail.md)
- [Log rotation and pruning](../../../features/6.0/log-rotation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
