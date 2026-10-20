---
title: Backup readiness
audience: government, admin, hosting
category: public-sector
order: 150
summary: A checklist that proves an Exponential site of a public body can be restored - database and files taken together, keys that cannot be recreated, storage and retention, restore tests, recovery objectives and who does what.
keywords: backup, restore, restore test, RPO, RTO, database dump, storage, keys, audit keys, retention, encryption, disaster recovery, public sector
type: help
---
# Backup readiness

This checklist is for the site administrator and host of a public body. A backup is only proven by a restore. The
facts come from [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md),
[Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md) and
[10. After installing](../../../install/10-after-installing.md#109-backups-and-restore).

## Contents

1. [What is backed up](#what-is-backed-up)
2. [How](#how)
3. [Where and how long](#where-and-how-long)
4. [Restore](#restore)
5. [Agreements](#agreements)

## What is backed up

- [ ] The database, with the tool of its engine ([Database backups per engine](../../hosting/operations/database-backups-per-engine.md)).
- [ ] `var/storage/` and any `var/<site>/storage/`: uploaded files.
- [ ] `settings/override/` and `settings/siteaccess/`: settings and credentials.
- [ ] Keys that cannot be recreated: `settings/override/audit.ini.append.php` (audit signing keys), `settings/override/mailpreferences.ini.append.php` (the e-mail preferences secret), Velocity's TLS keys if used ([Keys and secrets](../../hosting/operations/backups-what-to-back-up.md#keys-and-secrets-that-cannot-be-recreated)).
- [ ] The audit directory `var/<site>/log/audit/`.
- [ ] Your own extensions and designs, unless they are in a repository you control; `composer.json` and `composer.lock`.
- [ ] Caches, `var/autoload/` and `vendor/` left out: they are rebuilt.

## How

- [ ] Database and files taken at the same moment, with maintenance mode or with the accepted small gap ([Taking database and files together](../../hosting/operations/backups-what-to-back-up.md#taking-database-and-files-together)).
- [ ] A SQLite database copied with SQLite's online backup, never by copying a busy file.
- [ ] Automated, with a failure alert to a person.

## Where and how long

- [ ] Stored off the server, in a second location.
- [ ] Encrypted; readable only by named people (the backup holds database passwords).
- [ ] Retention matches your records and privacy rules ([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).
- [ ] Data location of backups stated in the hosting contract.

## Restore

- [ ] A restore test at least once a year on another machine ([A restore test on another machine](../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).
- [ ] After the restore: sign-in, a page with images, a form, the audit chain (`./console exp:audit verify`).
- [ ] The time it took recorded: that is your real recovery time.
- [ ] The runbook [Restore from backup](../../hosting/runbooks/restore-from-backup.md) followed and improved.

## Agreements

- [ ] RPO (how much data may be lost) and RTO (how long a restore may take) agreed with your own figures ([SLA options](../../hosting/business/sla-options.md#recovery-point-and-recovery-time)).
- [ ] Who starts a restore and who decides to.
- [ ] Backup copies returned or deleted at the end of the hosting contract ([Exit readiness](exit-readiness.md)).

## Related pages

- [Government checklists](README.md)
- [Backups and restores (for site builders)](../../creators/website-creators/backups-and-restores.md)
- [Incident readiness](incident-readiness.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
