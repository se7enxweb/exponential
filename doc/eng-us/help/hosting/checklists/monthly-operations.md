---
title: Monthly operations checklist
audience: hosting, admin
category: hosting
order: 40
summary: What a host checks once a month for every Exponential site it operates: updates, backups and a restore sample, disk and logs, security log, cron runs, caches, certificates, accounts and a short report to the customer.
keywords: monthly, operations, checklist, maintenance, updates, security fixes, backups, restore test, disk usage, logs, log rotation, security log, cron, certificates, accounts, report
type: help
---
# Monthly operations checklist

This checklist is for the system administrators and support staff who operate Exponential sites. Run it once a month
per site (or per server for the server items), record the result, and open a ticket for each item that fails.

```text
Site/server: <name>        Month: <yyyy-mm>        Checked by: <name>
```

## Updates

- [ ] New Exponential releases since last month are known; security fixes among them are applied or scheduled
      ([Working with the project and 7x](../business/working-with-the-project-and-7x.md#following-releases),
      [Upgrading customer sites](../operations/upgrading-customer-sites.md)).
- [ ] Extension updates checked where you manage them ([Automated updates with ezupdate](../operations/automated-updates-with-ezupdate.md)).
- [ ] Operating system and PHP security updates applied.
- [ ] PHP version still supported by its maintainers and by Exponential (8.0 to 8.5).

## Backups

- [ ] Every backup of the month completed; failures followed up.
- [ ] One site's backup, chosen in rotation, restored on another machine this month
      ([Restore and restore tests](../operations/restore-and-restore-tests.md)).
- [ ] Old backups removed according to the retention policy ([Backup schedules and retention](../operations/backup-schedules-and-retention.md)).

## Disk and logs

- [ ] Disk use of `var/storage`, `var/cache`, `var/log` and the database within the plan ([Storage layout](../operations/storage-layout.md)).
- [ ] Logs rotate; archives pruned (`./console exp:logs --archives`, `./console exp:logs --prune --dry-run`)
      ([Log viewer and rotation](../operations/log-viewer-and-rotation.md)).
- [ ] No growing error pattern in `error.log` (`./console exp:logs --search=... --file=var/log/error.log --rotations`).

## Security

- [ ] `security.log` reviewed: probes are normal on the internet; look for bursts and for attacks that got a 200
      ([Security log](../operations/security-log.md)).
- [ ] No unknown administrator accounts; leavers removed.
- [ ] The audit trail shows no unexpected changes to roles and policies ([Audit trail](../security/audit-trail.md)).
- [ ] Certificates valid for more than the renewal margin (with Velocity: `php bin/php/console exp:velocity ssl show --allow-root-user`)
      ([TLS and HTTPS](../security/tls-and-https.md)).

## Jobs and caches

- [ ] Cron jobs ran on schedule; none failing repeatedly ([Cronjob console](../operations/cronjob-console.md)).
- [ ] Mail queue and bounces normal ([Mail consent and bounces](../operations/mail-consent-and-bounces.md)).
- [ ] With Velocity: `php bin/php/console exp:velocity status --allow-root-user` reports it running; the cache
      statistics look normal (`exp:velocity cache stats`).

## Report to the customer

- [ ] Availability of the month from your external check.
- [ ] Incidents and what was done.
- [ ] Updates applied and planned.
- [ ] Restore test result, if this site was the sample.
- [ ] Disk and database growth.

## Related pages

- [Monitoring overview](../operations/monitoring-overview.md)
- [Security review checklist](security-review.md)
- [SLA options](../business/sla-options.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
