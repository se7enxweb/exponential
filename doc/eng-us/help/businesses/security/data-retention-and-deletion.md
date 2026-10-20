---
title: Data retention and deletion
audience: businesses, admin, project-manager
category: business-solutions
order: 80
summary: How long an Exponential site keeps logs, audit records, consent records, trash, sessions and form data by default, which settings change it, how to delete data when a person asks, and what stays with you and your host (backups, exports). Not legal advice.
keywords: retention, data retention, deletion, erasure, storage limitation, logs, audit trail, consent log, suppression list, trash, sessions, form submissions, orders, backups, exports, GDPR, right to erasure
type: help
---
# Data retention and deletion

This page is for owners, data protection officers and administrators who must decide how long the site keeps
personal data, and how to delete it when that time is up or a person asks. It summarizes the defaults the software
ships with and the commands that delete, and points to the full table for administrators.

> **Warning**
> Basics, not legal advice. Which periods are right is a legal and business decision, not a software one. Bookkeeping,
> tax and sector rules may require you to keep some records longer than privacy rules would suggest. Ask a lawyer,
> your accountant or your data protection officer.

## Contents

1. [The defaults at a glance](#the-defaults-at-a-glance)
2. [Deciding your periods](#deciding-your-periods)
3. [Deleting on request](#deleting-on-request)
4. [What does not delete itself](#what-does-not-delete-itself)
5. [Backups](#backups)
6. [A retention schedule to fill in](#a-retention-schedule-to-fill-in)

## The defaults at a glance

From [Log and data retention](../../hosting/security/log-and-data-retention.md#at-a-glance):

| Data | Default | Changed in |
|---|---|---|
| Kernel logs (`error.log` and others) | 3 archives, rotated at 2 MB, no age limit | `site.ini [FileSettings]` |
| `404.log` | pruned after 30 days | `logviewer.ini [RotationSettings]` |
| `security.log` | pruned after 90 days | same |
| `csp.log` | pruned after 30 days | same |
| Audit trail (content, access, system, commerce) | 90 days live, 730 days archived | `audit.ini [AuditChannel_<channel>]` |
| Audit index | 730 days; personal fields pseudonymized after 90 | `[AuditIndexSettings]`, `[AuditPrivacySettings]` |
| Mail consent log of people who are gone | 1095 days | `mailpreferences.ini [ConsentSettings] RetentionDays` |
| Pending mail confirmations | 7 days | `[ConsentSettings] PendingDays` |
| Suppression list | kept | `[SuppressionSettings] RetentionDays` |
| Trash | purged whole when the trash cronjob runs | `content.ini [TrashSettings] KeepItemsForDays` |
| Finished content jobs | 30 days | `content.ini [ContentJobSettings] KeepDays` |
| Sessions | 3 days | `site.ini [Session] SessionTimeout` |

Most of these are enforced by cronjobs. If the cronjobs do not run, nothing is removed: make sure your administrator
or host schedules them ([Cronjobs and background jobs](../reliability/cronjobs-and-background-jobs.md)).

## Deciding your periods

1. Start from your [data inventory](personal-data-in-exponential.md#building-your-own-data-inventory).
2. For each kind of data, ask your adviser how long you must keep it and how long you may.
3. Write the period into your privacy notice and your retention schedule.
4. Ask the administrator to set the matching settings in `settings/override`; the full list with examples is in
   [Log and data retention](../../hosting/security/log-and-data-retention.md).
5. Agree backup retention with your host so it fits the same periods.

## Deleting on request

| Data | How to delete | Source |
|---|---|---|
| Mail preferences and pending confirmations | `./console exp:mail:preferences erase --email=<address> --yes`; the consent log is anonymized and the withdrawal kept as proof | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#requests-from-data-subjects) |
| Stop all mail | `exp:mail:suppression add --email=<address> --reason=legal`, or the person's own unsubscribe link | same |
| A user account | removing the user object; it is final (no trash) and erases its mail preferences | [Check user accounts](../../../features/6.0/user-hygiene.md), same |
| Form submissions | **Setup > Collected information**: remove the collections of a form, or single collections | [Collected information](../../../guides/collected-information.md) |
| Orders | archive or remove in the store administration; check bookkeeping duties first | [Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md) |
| Newsletter subscriber records | in the newsletter administration (`cjw_newsletter` user list) | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Audit records | not deleted one by one; see below | [Audit: privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks) |

> **Note**
> The audit trail is a hash chain: removing one record would break the proof that nobody changed the log. The audit
> guide describes keeping records for their retention period, pseudonymizing personal fields in the index, and
> answering an erasure request with that period. Whether that is enough in your case is a question for your adviser.

Before deleting, consider whether to answer an access request first: the person may want a copy. Delete the export
files when the request is done; they hold personal data.

## What does not delete itself

- [ ] Exports you downloaded (CSV, JSON) on staff computers and in `var/tmp/`.
- [ ] Mail sent by forms to a receiver's inbox.
- [ ] Velocity's dated log files: the repository documents no age limit, so remove them with a scheduled job
      ([Log and data retention](../../hosting/security/log-and-data-retention.md#velocitys-logs)).
- [ ] Web server, mail server and panel logs outside Exponential.
- [ ] Content versions and drafts: their retention is an editorial decision.
- [ ] Records forwarded to other systems (a SIEM, a syslog server) under their own rules.

## Backups

Backups hold everything above for as long as they are kept. Decide, with your host and adviser:

- how long backups are kept, and where;
- whether deleted data must be removed from backups, or whether it expires with them;
- how a restore avoids bringing deleted data back into use.

[Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md) covers the technical side.

## A retention schedule to fill in

```text
RETENTION SCHEDULE - [Site]   Version [x], [date]   Owner: [name]
Data                        | Keep for | Reason / rule          | Setting or method          | Who checks
----------------------------|----------|------------------------|----------------------------|-----------
Request and security logs   |          |                        | logviewer.ini MaxAgeDays   |
Kernel logs                 |          |                        | LogFileMaxAge / MaxAgeDays |
Audit trail archives        |          |                        | audit.ini ArchiveDays      |
Mail consent log            |          |                        | RetentionDays              |
Form submissions            |          |                        | removed by hand, monthly   |
Orders                      |          |                        | archived / removed         |
Newsletter subscribers      |          |                        | newsletter admin           |
User accounts (inactive)    |          |                        | Check user accounts        |
Backups                     |          |                        | host contract              |
Exports                     |          |                        | deleted after the request  |
Approved by: __________   Next review: __________
```

## Related pages

- [Log and data retention for hosts](../../hosting/security/log-and-data-retention.md)
- [GDPR basics](gdpr-basics.md)
- [Customer offboarding checklist](../../hosting/checklists/customer-offboarding.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
