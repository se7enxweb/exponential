---
title: Monthly operations review
audience: government, admin, project-manager, hosting
category: public-sector
order: 360
summary: A one-hour monthly checklist for a public-sector Exponential site - health, logs, security, audit, cronjobs, integrity, accounts, backups and updates - with the command or page for each item and a record for management.
keywords: monthly review, operations checklist, health check, routine, security review, backups, updates, audit, accounts, record, service management
type: help
---
# Monthly operations review

This task is for the administrator who runs a public website, with the record going to the service owner. It
combines the weekly checklist of [Operating a site](../../../../guides/operating-a-site.md#10-a-weekly-checklist) with
the items public bodies are usually asked about. The hosting version is
[Monthly operations (hosting)](../../../hosting/checklists/monthly-operations.md).

## Contents
1. [Before you start](#before-you-start)
2. [The checklist](#the-checklist)
3. [The record](#the-record)
4. [Related pages](#related-pages)

## Before you start

- An administrator account and shell access; about an hour.

## The checklist

| Area | Check | How |
|---|---|---|
| Health | no red health checks | **Setup > System information**; `./console exp:system:info --allow-root-user` |
| Caches | layers as expected | `php bin/php/cache.php status --allow-root-user` ends in `PASS` |
| Errors | nothing new or growing | **Error digest**, window 720 hours ([Read the error digest](read-the-error-digest.md)) |
| Logs | within budget, writable | **Log health** ([Check log health](check-log-health.md)) |
| Security | the month's `security.log` | [Review the security log weekly](review-the-security-log-weekly.md) records |
| Audit | chains intact, alerts handled | `./console exp:audit verify --allow-root-user`; **Audit > Alerts** |
| Cronjobs | all parts run, none paused | [Check that cronjobs run](check-that-cronjobs-run.md) |
| Workflows | nothing waiting for days | **Setup > Workflow processes**, *Oldest waiting* |
| Integrity | no unexpected modified files | `php bin/php/checkmanifest.php --all --extensions` |
| Accounts | dormant, locked, shared | **Users > Check user accounts** ([Find accounts not used for months](../people-and-permissions/find-accounts-not-used-for-months.md)) |
| Admins | full access holders unchanged | [Audit who has admin rights](../people-and-permissions/audit-who-has-admin-rights.md) (quarterly) |
| Backups | a backup from the last days exists; restore test as planned | [Restore last night's backup to a test server](restore-last-nights-backup-to-a-test-server.md) |
| Certificates | more than a month left | `./console exp:velocity ssl show --allow-root-user` or `openssl` from outside |
| Updates | new releases, security fixes | **Setup > Updates and packages**, **Check for updates**; the changelog ([Security updates and disclosure](../../operations/upgrades-and-change/security-updates-and-disclosure.md)) |
| Headers | still present | [Check security headers](check-security-headers.md) |

## The record

Date, reviewer, each area with OK or a finding, actions with owners and dates. Three months of records show an
auditor a working practice; findings that repeat become changes for the change board.

## Related pages
- [Monitoring overview](../../operations/monitoring/monitoring-overview.md)
- [Admin access reviews](../../operations/people-and-permissions/admin-access-reviews.md)
- [Post-incident review](../../operations/incidents/post-incident-review.md)
- [Security review checklist (hosting)](../../../hosting/checklists/security-review.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
