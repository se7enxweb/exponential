---
title: Data location and what is stored where
audience: government, hosting, admin, project-manager
category: public-sector
order: 100
summary: For public bodies that must know and document where the data of an Exponential site is kept. Every place the software stores content, files, accounts, logs and keys, which of them hold personal data, and what leaves the server only when you switch it on.
keywords: data location, data residency, where is data stored, personal data, records of processing, database, var/storage, logs, audit trail, settings/override, backups, third parties, sovereignty, inventory
type: help
---
# Data location and what is stored where

This page is for data protection officers, architects and IT teams in public bodies who must document where the data
of a site is kept, for a record of processing, a security assessment or a data residency rule. It lists every place
Exponential stores data, says which places usually hold personal data, and which features send data elsewhere only
when you switch them on. Where the server itself stands is your hosting decision; this page tells you what to put on
the inventory. The facts come from [Storage layout](../../../hosting/operations/storage-layout.md) and
[Privacy standards](../../../../standards/privacy.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Two places hold everything](#two-places-hold-everything)
2. [The inventory](#the-inventory)
3. [What can leave the server](#what-can-leave-the-server)
4. [Copies you make yourself](#copies-you-make-yourself)
5. [Several machines and providers](#several-machines-and-providers)
6. [Checklist](#checklist)

## Two places hold everything

Exponential keeps its state in the **database** (content, users, roles, versions, settings stored in tables) and in
**files** (uploaded images and documents, settings, logs, the audit trail)
([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md)). If both are on machines in a
location you approved, so is the site's data, provided that nothing else was switched on that sends data out (see
below) and that your backups follow the same rule.

## The inventory

| Place | Holds | Personal data, usually |
|---|---|---|
| The database (or `var/storage/sqlite3/` for SQLite) | content and its versions, user accounts (name, login, e-mail address, password hash), role assignments, collected form entries, search phrases, mail preferences and their consent log | yes |
| `var/storage/` and `var/<site>/storage/` | uploaded images and files, their variations | possibly (documents, photos) |
| `var/<site>/log/audit/` | the audit trail: who did what, with truncated addresses and hashed sessions | yes, pseudonymized in part |
| `var/log/`, `var/<site>/log/` | error, warning, 404, security and other logs; client addresses masked in the request logs | possibly |
| `var/vc/` (Velocity) | Velocity's access and error logs, which name visitors | yes |
| `var/log/mail/` | mails written by the file transport (test machines) | yes |
| `var/cache/` | compiled templates and rendered pages, regenerated | possibly, briefly |
| `settings/override/` | settings, database credentials, the audit keys, the mail preference secret | no, but secrets |
| The web server's own logs | outside Exponential | yes |

Sources: [Storage layout](../../../hosting/operations/storage-layout.md),
[Request logs](../../../../features/6.0/request-logs.md), [Audit trail](../../../../features/6.0/audit-trail.md),
[Privacy standards](../../../../standards/privacy.md). How long each record is kept by default is on
[Log and data retention](../../../hosting/security/log-and-data-retention.md).

Privacy features that reduce what is stored: the request logs mask client addresses (`[RequestLogSettings] MaskIP`
enabled) and replace query values with `***`; the audit trail truncates addresses, hashes sessions and never records
passwords or tokens; the log viewer masks e-mail and IP addresses when it shows lines.

## What can leave the server

The kernel and the administration send nothing to third parties by default. The administration's scripts are served
locally and admin4 loads no web fonts or scripts from other hosts. Data leaves the server only through what you
configure ([Privacy standards](../../../../standards/privacy.md)):

| Feature | Sends data to | Default |
|---|---|---|
| Outgoing mail | your mail server or relay | configured by you |
| `bcwebsitestatistics` | Google Analytics or Tag Manager | extension not active |
| `recaptcha`, `hcaptcha` | the captcha provider | not active |
| Social login of `sevenx_authentication_2fa` | the chosen identity provider | not active |
| Audit sink syslog | the machine's own syslog (`Transport=local`), or a remote syslog host you set | on for the `access` and `system` channels, local |
| Audit sinks webhook and mail | the URL or the receivers you set | webhook without URL; mail for critical events to the site's receivers |
| Solr search (ezfind) | your Solr server | not active |
| A CDN or external proxy in front of the site | that provider | not configured |

Check what is active on a site with **Setup > Extensions**, or:

```bash
grep -rn "ActiveExtensions" settings/override/site.ini.append.php
```

## Copies you make yourself

Data residency rules usually apply to copies as well:

- **Backups** hold the database, the storage, the audit trail and `settings/override`; keep them in an approved
  location ([Offsite and encrypted copies](../backups-and-recovery/offsite-and-encrypted-copies.md)).
- **Test copies** made from production carry real personal data
  ([Environments for development, test and production](environments-dev-test-prod.md)).
- **Exports**: audit exports, CSV exports of accounts (**Users > Check user accounts**), form data exports. Know who
  may make them; each is recorded in the audit trail.
- **Support reports**: the help's error report removes e-mail addresses, IP addresses and credentials before it is
  sent ([Privacy standards](../../../../standards/privacy.md)).

## Several machines and providers

With a database server, a shared file system for uploads, a central log collector or a CDN, each of those is a
location of its own. List them all in your inventory. A site served from one machine with SQLite keeps everything in
one directory tree, which is the simplest case to document.

## Checklist

- [ ] The inventory above is filled in with your machines, providers and locations.
- [ ] Active extensions are checked; any that contact third parties are recorded and justified.
- [ ] Backups, test copies and exports follow the same location rules.
- [ ] Retention periods of logs and the audit trail are known and agreed.
- [ ] The record of processing names the site and refers to this inventory.

## Related pages

- [Hosting](README.md) index of this folder
- [Government and sovereign cloud hosting](government-and-sovereign-cloud.md)
- [Compliance and GDPR for hosts](../../../hosting/security/compliance-and-gdpr.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
