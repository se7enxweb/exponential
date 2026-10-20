---
title: Operations tasks
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Index of step-by-step operations tasks for a public-sector Exponential site - maintenance windows, backups and restores, caches, cronjobs, logs, the audit trail, upgrade checks, deploys, certificates, databases, security checks and the monthly review.
keywords: tasks, how to, operations, maintenance, backup, restore, cache, cronjobs, logs, audit, upgrade check, deploy, certificate, read replica, failover, static cache, security headers, permissions, index
type: help
---
# Operations tasks

This folder holds short, numbered procedures for the administrators who run a public website. Each task names what
you need before you start, gives the exact commands and admin screens, says how to check the result, and notes what
to put in a change record. The explanations behind them are in
[Running Exponential in the public sector](../../operations/README.md); the hosting companies' versions are in
[Hosting operations](../../../hosting/operations/README.md).

## Contents
1. [Maintenance, backup and restore](#maintenance-backup-and-restore)
2. [Caches and deploys](#caches-and-deploys)
3. [Cronjobs](#cronjobs)
4. [Logs and the audit trail](#logs-and-the-audit-trail)
5. [Checks and evidence](#checks-and-evidence)
6. [Certificates, databases and configuration](#certificates-databases-and-configuration)
7. [Suggested order](#suggested-order)
8. [Related pages](#related-pages)

## Maintenance, backup and restore

| Task | Answer in one line |
|---|---|
| [Take the site into maintenance for an upgrade](take-the-site-into-maintenance-for-an-upgrade.md) | **Setup > Maintenance** or `maintenance.php on` with a message, a duration and allowed addresses. |
| [Bring the site back online](bring-the-site-back-online.md) | Switch off, clear the caches in front, check 200, resume cron. |
| [Take a backup before a change](take-a-backup-before-a-change.md) | Database dump and file archive inside the window. |
| [Back up a SQLite site safely](back-up-a-sqlite-site-safely.md) | `sqlite3 ... ".backup ..."`, never a file copy. |
| [Restore last night's backup to a test server](restore-last-nights-backup-to-a-test-server.md) | Restore with the file mail transport; record the time taken. |
| [Pause cronjobs during maintenance](pause-cronjobs-during-maintenance.md) | **Pause** on Setup > Cronjobs, with a note; resume after. |

## Caches and deploys

| Task | Answer in one line |
|---|---|
| [Clear caches after a settings change](clear-caches-after-a-settings-change.md) | `--clear-tag=ini`, reload or deploy, page caches last. |
| [Clear caches after a template change](clear-caches-after-a-template-change.md) | Template caches, override cache for new templates, then page caches. |
| [Clear the Velocity response cache](clear-the-velocity-response-cache.md) | `exp:velocity cache clear`, after the new code runs. |
| [Warm the caches after a deploy](warm-the-caches-after-a-deploy.md) | `preload.php`, `warm.php` or **Preload Sites**. |
| [Deploy a PHP change](deploy-a-php-change.md) | `exp:velocity deploy --dry-run`, then for real. |
| [Enable the static cache](enable-the-static-cache.md) | Generate, check, `StaticCache=enabled`. |

## Cronjobs

| Task | Answer in one line |
|---|---|
| [Check that cronjobs run](check-that-cronjobs-run.md) | `crontab:list`, **Setup > Cronjobs**, the workflow status bar. |
| [Run a cronjob part by hand](run-a-cronjob-part-by-hand.md) | **Run part**, or `runcronjobs.php --script=workflow.php`. |

## Logs and the audit trail

| Task | Answer in one line |
|---|---|
| [Read the error digest](read-the-error-digest.md) | The daily five minutes on **Error digest**. |
| [Check log health](check-log-health.md) | Disk budget, growth, permissions, rotation in effect. |
| [Rotate and prune logs](rotate-and-prune-logs.md) | `exp:logs --rotate-all`, `--prune`, always `--dry-run` first. |
| [Review the security log weekly](review-the-security-log-weekly.md) | Rules, high-confidence 200s, floods, a short record. |
| [Find an error by its reference](find-an-error-by-its-reference.md) | `exp:logs --reference=ERR-...` or the viewer's search. |
| [Verify the audit trail](verify-the-audit-trail.md) | `exp:audit verify`; exit 1 means broken. |
| [Check who gets audit alerts](check-who-gets-audit-alerts.md) | `exp:audit alerts recipients`; a team mailbox. |
| [Export audit records for an auditor](export-audit-records-for-an-auditor.md) | **Audit > Export** or `exp:audit export --format=bundle`. |

## Checks and evidence

| Task | Answer in one line |
|---|---|
| [Check upgrade consistency](check-upgrade-consistency.md) | **Check file consistency** or `checkmanifest.php --all`. |
| [Check the database schema](check-the-database-schema.md) | **Check database consistency**; apply SQL only after a backup. |
| [Read system information for a support request](read-system-information-for-a-support-request.md) | **Download report (.txt)**, secrets left out. |
| [Check security headers](check-security-headers.md) | Five headers, 404 for dot files, cookie flags. |
| [Check file and directory permissions](check-file-and-directory-permissions.md) | Only `var/` writable, one owner, hardened modes. |
| [Check PHP extensions before an upgrade](check-php-extensions-before-an-upgrade.md) | `exp:velocity ext check` and the deprecations log. |
| [Monthly operations review](monthly-operations-review.md) | One hour, fifteen items, one record. |

## Certificates, databases and configuration

| Task | Answer in one line |
|---|---|
| [Renew an HTTPS certificate in Velocity](renew-an-https-certificate-in-velocity.md) | `exp:velocity ssl show` and `ssl renew`; no restart. |
| [Switch Velocity to Let's Encrypt](switch-velocity-to-lets-encrypt.md) | `mode: letsencrypt`, staging directory first. |
| [Add a read replica](add-a-read-replica.md) | `UseSlaveServer` on MySQL or MariaDB. |
| [Fail over to a standby database](fail-over-to-a-standby-database.md) | No automatic failover: change `Server`, clear INI, deploy. |
| [Sign out all sessions of a user](sign-out-all-sessions-of-a-user.md) | Disable, change the password, or remove sessions. |
| [Set up environment-specific settings](set-up-environment-specific-settings.md) | `EXP_ENV` and variant files. |
| [Lock configuration with ManagedByDeployment](lock-configuration-with-managed-by-deployment.md) | One switch; changes only through deployment. |

## Suggested order

Before go-live: environment settings, permissions, security headers, audit alert recipients, cronjobs, a backup and a
restore test, ManagedByDeployment. Then the routine: the error digest daily, the security log weekly, the monthly
review. Keep the maintenance, deploy and restore tasks at hand for change windows.

Up: [Tasks](../README.md) and [Running Exponential in the public sector](../../operations/README.md). The
section landing page is [Governments: start here](../../government-start-here.md).

## Related pages
- [People and permissions tasks](../people-and-permissions/README.md)
- [Incidents](../../operations/incidents/README.md)
- [Operating a site](../../../../guides/operating-a-site.md)
- [Hosting checklists](../../../hosting/checklists/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
