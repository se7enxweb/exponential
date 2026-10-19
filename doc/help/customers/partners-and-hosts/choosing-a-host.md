---
title: Choosing a host
audience: customer, hosting, businesses
category: customer-success
order: 4
summary: How the owner of a running Exponential site chooses a new host - what the site needs, the questions that decide the hosting model, what to check beyond the technology, how to compare offers and how the move works.
keywords: choose a host, hosting, hosting model, VPS, shared hosting, managed hosting, Velocity, PHP, database, move, migration
type: help
---
# Choosing a host

This page is for an owner who already runs an Exponential site and wants to move it, or wants a host for a site a
partner is building. The full guide for businesses is [Choosing hosting](../../businesses/choosing/choosing-hosting.md),
and the hosting models are compared in [Hosting models compared](../../businesses/choosing/hosting-models-compared.md).
This page adds what matters when a site, its data and its history already exist.

## What the site needs

From [Choosing hosting](../../businesses/choosing/choosing-hosting.md):

| Need | Minimum |
|---|---|
| PHP | 8.0; the newest 8.x the system offers is recommended; Velocity needs 8.1 |
| Database | One of the supported engines; the project lists them in an official order |
| Web server | None if Velocity serves the site; or Apache, nginx and others |
| Composer | 2.x |
| Cron | A job every few minutes |
| Mail | Outgoing mail |
| HTTPS | For every public site |
| Backups | Database, uploaded files, settings, keys, audit trail |

Write down what your site uses today: **Setup > System information** shows the PHP version, the database, the
engine that answered and more ([System information](../../../guides/system-information.md)). A new host should offer
at least the same, or a planned upgrade path.

## The questions that decide the model

From [Choosing hosting](../../businesses/choosing/choosing-hosting.md):

| Question | If yes |
|---|---|
| Must more than one machine write the database? | A database server; not SQLite |
| Is the hosting without long-running processes (typical shared hosting)? | Apache or nginx with PHP-FPM; Velocity usually not |
| Is the machine's PHP older than 8.1? | A traditional web server; Velocity needs 8.1 |
| Is there no shell access? | The setup wizard in the browser, and fewer options for updates |

> **Tip**
> "The right question is not only 'which technology is best' but 'who can run it, back it up and restore it'"
> ([Choosing hosting](../../businesses/choosing/choosing-hosting.md)).

## Beyond the technology

Use the questions on [What to ask your host](../healthy/what-to-ask-your-host.md). The ones that decide most often:

- [ ] Who applies Exponential, PHP and system updates, and how quickly after a security release?
- [ ] Backups: what, how often, where, and when was the last restore test?
- [ ] Monitoring from outside, and who is alerted at night?
- [ ] Support hours and first response times, in writing.
- [ ] Where the servers and backups are located (for data protection).
- [ ] Has the host run Exponential sites before? Hosting partners are listed by the project
      ([Hosting partners](../../partners/hosting-partners.md)).
- [ ] How you leave again: export format, time, deletion ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)).

## Comparing offers fairly

| Compare | Not |
|---|---|
| What is included: updates, backups, restores, monitoring, support hours | Only the monthly price |
| Written response and restore times | "Fast support" |
| The exit terms | Only the start terms |
| Their answer to "who restores, and how long did it take last time?" | A list of features |

The cost side is in [Hosting cost drivers](../../businesses/costs/hosting-cost-drivers.md).

## The move

A move without a version change follows [Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md):
what to copy, what to fetch fresh, the settings that name the old server, and the checks afterwards. Your part:

- [ ] Agree a date outside busy periods, with a maintenance window
      ([Maintenance mode and planned work](../healthy/maintenance-mode-and-planned-work.md)).
- [ ] Ask the old host for a complete export (database, files, settings, versions) and keep a copy yourself.
- [ ] Have the database password changed at the new host; the settings that carry it travel with the export
      ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)).
- [ ] Run your test list after the move ([Upgrade day checklist](../healthy/upgrade-day-checklist.md)).
- [ ] Keep the old site until the new one is confirmed, then ask for deletion and confirmation.

## Related pages

- [Choosing a partner](choosing-a-partner.md)
- [Contracts and SLAs](contracts-and-slas.md)
- [Migrating customers to your hosting (for hosts)](../../hosting/migrations/migrating-customers-to-your-hosting.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
