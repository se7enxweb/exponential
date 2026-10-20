---
title: Customer offboarding checklist
audience: hosting
category: hosting
order: 60
summary: What a host does when a customer leaves or moves their Exponential site elsewhere: a complete handover export, credentials, DNS, the final backup, deletion of data according to contract and law, and closing the records.
keywords: offboarding, customer leaves, cancellation, handover, export, data return, backup, deletion, retention, DNS, credentials, GDPR, contract end
type: help
---
# Customer offboarding checklist

This checklist is for the support and system administration staff of a host when a customer ends their contract or
moves their site elsewhere. Exponential is free software, so a customer can take the site anywhere; a clean handover
is part of a good hosting service and of your reputation.

```text
Customer: <customer>       Site(s): <domains>       Contract end: <date>       Handled by: <name>
```

## Agree the plan

- [ ] Date of the final export and of the shutdown agreed in writing.
- [ ] What the customer receives, and how (secure transfer, not e-mail attachments).
- [ ] How long you keep backups after the end, according to the contract and the law that applies
      ([Log and data retention](../security/log-and-data-retention.md)).

## The handover export

A complete site is the database **and** the files ([What to back up](../operations/backups-what-to-back-up.md)):

- [ ] Database dump with the engine's tool ([Database backups per engine](../operations/database-backups-per-engine.md)).
- [ ] `var/storage` (and `var/<site>/storage` where separate).
- [ ] `settings/override` and `settings/siteaccess`; they contain the database password, so transfer them privately
      and tell the customer to change that password at the new host.
- [ ] Custom extensions and designs, or the addresses of their Git repositories.
- [ ] A note of the versions: Exponential, PHP, database engine and version, engine (Velocity, FrankenPHP, Apache or
      nginx), extensions.
- [ ] Take the export with maintenance mode on, so database and files agree:
      `php bin/php/maintenance.php on --message="..." --allow-root-user`.

The customer's new host can follow [Moving a site between hosts](../migrations/moving-a-site-between-hosts.md).

## Access and DNS

- [ ] DNS: if you manage it, transfer it or change the records on the agreed date.
- [ ] Certificates you issued are not transferred; the new host issues its own.
- [ ] Your staff accounts in the customer's administration are removed if the site keeps running elsewhere from your
      export; tell the customer which accounts were yours.
- [ ] The customer's access to your panel and SFTP is closed on the shutdown date.

## Shutdown

- [ ] Site in maintenance mode or switched off on the agreed date.
- [ ] Cron jobs of the site removed ([Cron jobs](../operations/cron-jobs.md)).
- [ ] Velocity instance or PHP-FPM pool of the site removed; its ports freed.
- [ ] Monitoring checks of the site removed.
- [ ] Mail sending for the site's domain stopped.

## Deletion

- [ ] Site files, database and database user deleted on the agreed date.
- [ ] Backups deleted at the end of the agreed retention, not earlier unless the customer asks and the law allows.
- [ ] Logs that hold personal data (access logs, request logs) handled according to your retention policy.
- [ ] Deletion confirmed to the customer in writing, with the date.

## Records

- [ ] The customer's record closed with the export date, the deletion date and who did it.
- [ ] Lessons learned noted, if the customer left because of a problem.

## Related pages

- [Moving a site between hosts](../migrations/moving-a-site-between-hosts.md)
- [Compliance and GDPR](../security/compliance-and-gdpr.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
