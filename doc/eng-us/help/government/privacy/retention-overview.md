---
title: Retention of personal data: an overview
audience: government, admin, project-manager
category: public-sector
order: 80
summary: For public bodies setting storage limits - every retention mechanism Exponential has (audit, logs, consent log, pending confirmations, suppression list, sessions, versions, drafts) with its default, where nothing removes data automatically, and how to turn your retention schedule into settings.
keywords: retention, storage limitation, GDPR article 5(1)(e), retention schedule, audit retention, log retention, MaxAgeDays, consent log RetentionDays, PendingDays, suppression RetentionDays, SessionTimeout, versions, drafts, form submissions, public sector
type: reference
---
# Retention of personal data: an overview

This page is for data protection officers, records managers and administrators. Storage limitation means personal
data is kept no longer than needed. Exponential has retention mechanisms for its own records; several places have
none and need a manual routine. Use this table to turn your retention schedule into settings.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Automatic retention](#automatic-retention)
2. [No automatic retention](#no-automatic-retention)
3. [From schedule to settings](#from-schedule-to-settings)
4. [Related pages](#related-pages)

## Automatic retention

| Data | Mechanism | Default | Setting |
|---|---|---|---|
| Audit trail | daily cronjob part: archive, purge | 90 days live, 730 archived (read channel 30/90) | `[AuditChannel_<channel>] LiveDays`, `ArchiveDays` |
| Audit index | daily | rows kept 730 days, pseudonymized after 90 | `[AuditIndexSettings] KeepDays`, `[AuditPrivacySettings] PseudonymiseAfterDays` |
| `404.log`, `security.log`, `csp.log` | prune by age | 30, 90, 30 days | `logviewer.ini [RotationSettings] MaxAgeDays[<key>]` |
| Other kernel logs | rotation by size only | 2 MB, 3 archives; no age limit | `site.ini [FileSettings] LogFileMaxSize`, `LogFileRotateCount`, `LogFileMaxAge`; per log `MaxAgeDays[<key>]` |
| Consent log of people who are gone | cronjob part `mailpreferences` (group `infrequent`) | 1095 days | `mailpreferences.ini [ConsentSettings] RetentionDays` |
| Pending mail confirmations | same | 7 days | `[ConsentSettings] PendingDays` |
| Suppression list | same, when set | kept forever (`0`) | `[SuppressionSettings] RetentionDays` |
| Sessions | expiry | 3 days | `site.ini [Session] SessionTimeout` |
| Content versions | when a new version exceeds the limit, the oldest archived version is removed | 45 per object (75 for class 1) | `content.ini [VersionManagement]` |

Sources: [Audit guide 4.3](../../../bc/6.0/audit.md#43-retention), [Log rotation](../../../features/6.0/log-rotation.md),
[Mail preferences: retention](../../../guides/mail-preferences-administrator.md#9-retention-and-the-cronjob),
[Sessions](../../../guides/sessions.md), [Versions of an object](../../../guides/content-history.md).

## No automatic retention

| Data | What to do |
|---|---|
| Form submissions (information collections) | remove on a schedule in Setup > Collected information, after export if needed |
| User accounts that are no longer used | review with Check user accounts; deactivate or remove |
| Search statistics | reset in Setup > Search statistics |
| Trash | empty on a schedule (`content/trash`) |
| Exports (CSV, JSON) people downloaded | delete after the task |
| Web server, Velocity and request-shield logs | configure in those programs |
| Backups | your backup rotation |

## From schedule to settings

1. For each record type in your schedule, find its row above.
2. Where a setting exists, set it in `settings/override/<file>.ini.append.php` (never the shipped file) and record the
   change.
3. Make sure the cronjobs run: `frequent` (audit) and `infrequent` (mail preferences, link check); add the `logs`
   part if you rely on age limits (`php runcronjobs.php logs`).
4. For rows without a mechanism, write a routine with an owner and a frequency.
5. Check yearly that the data really goes (dry runs: `./console exp:logs --prune --dry-run`,
   `./console exp:mail:consent cleanup --dry-run`, `./console exp:audit purge --dry-run`).

## Related pages

- [Audit retention](audit-retention.md)
- [Log retention](log-retention.md)
- [Log and data retention for hosts](../../hosting/security/log-and-data-retention.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
