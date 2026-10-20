---
title: Audit trail archives and backups
audience: government, admin, hosting
category: public-sector
order: 120
summary: For security officers, records officers and operations teams in public bodies. How Exponential's audit trail keeps live files and signed archives, how long by default, how to keep evidence for years on storage the web server cannot change, how to back it up and verify it, and how to read an archived day.
keywords: audit trail, audit archives, signed manifests, hash chain, ArchiveDir, LiveDays, ArchiveDays, WORM, evidence, exp:audit verify, exp:audit restore, retention, records, checkpoints
type: help
---
# Audit trail archives and backups

This page is for security officers, records officers and operations teams in public bodies who must keep the audit
trail of a site as evidence. It answers how the audit trail stores its records, how long it keeps them by default, how
to keep them longer on storage the web server cannot change, how to back them up and prove they are intact, and how to
read an old day. The references are [The audit trail](../../../../features/6.0/audit-trail.md) and the
[audit guide](../../../../bc/6.0/audit.md).

## Contents

1. [How records are kept](#how-records-are-kept)
2. [Default retention](#default-retention)
3. [Keeping evidence longer](#keeping-evidence-longer)
4. [Backing it up](#backing-it-up)
5. [Verifying and reading old records](#verifying-and-reading-old-records)
6. [Checklist](#checklist)

## How records are kept

- Each channel (`content`, `access`, `system`, `commerce`, `read`) writes daily files under `var/<site>/log/audit/`.
- Every record carries the hash of the one before it, a hash chain per channel.
- The daily task of the `audit` cronjob part (in the `frequent` group) archives closed days older than `LiveDays`:
  verified, compressed, read back and compared, and given a signed manifest. Only then is the live file removed
  ([audit guide, rotation and archives](../../../../bc/6.0/audit.md#42-rotation-and-archives)).
- Archives go to `[AuditArchiveSettings] ArchiveDir`, by default `log/audit/archive` under the var directory, with
  file mode `0440` and directory mode `0750`.

## Default retention

| Channel | `LiveDays` | `ArchiveDays` |
|---|---|---|
| content, access, system, commerce | 90 | 730 |
| read (only when reads are switched on) | 30 | 90 |

The searchable index keeps rows for `[AuditIndexSettings] KeepDays` (730). Retention never removes a signing key.

## Keeping evidence longer

Public bodies may need audit records for longer than two years. The audit guide gives a reference configuration for
long retention ([Compliance: long retention](../../../../bc/6.0/audit.md#66-compliance-long-retention)): `ArchiveDays=3650`
per channel, archives on a mount the web server can add files to but not change (write-once storage or a backup
target), and the daily signed checkpoints sent off the server through the webhook sink. In
`settings/override/audit.ini.append.php`:

```ini
[AuditChannel_content]
ArchiveDays=3650

[AuditArchiveSettings]
ArchiveDir=/srv/worm/exponential-audit
```

The path is an example; agree the period with your records officer and legal counsel.

## Backing it up

- Include the live directory and the archive directory in the file backup ([File and settings backups](file-and-settings-backups.md)).
- Include `settings/override/audit.ini.append.php`: without the signing keys, archives cannot be verified
  ([Keys and secrets in backups](keys-and-secrets-in-backups.md)).
- Keep the records' own retention in mind: a backup kept longer than `ArchiveDays` keeps the records longer too.
- Several web servers must share one key file.

## Verifying and reading old records

```bash
./console exp:audit verify --allow-root-user                # live files; exit 1 when a chain is broken
./console exp:audit verify --archives --allow-root-user     # also every archive and its signed manifest
./console exp:audit restore --channel=content --date=2026-06-02 --allow-root-user
```

`restore` decompresses an archived day into `<LogDir>/restored/` for reading and checks it against the manifest; it
never puts it back into the live chain. Verify before handing records to anyone. When a check says BROKEN: do not edit
or remove anything, copy the directory somewhere safe, and compare with copies that left the server (syslog, webhook
receiver, archives) ([audit guide, verify and restore](../../../../bc/6.0/audit.md#44-verify-and-restore)).

For handing records to an auditor, see the audit's export, which writes a signed bundle.

## Checklist

- [ ] The `frequent` cronjob group runs, so archives are made and verified daily.
- [ ] `ArchiveDays` and `ArchiveDir` match the agreed retention and storage.
- [ ] Audit directories and the key file are in the backup.
- [ ] `exp:audit verify --archives` runs on a schedule and alerts on failure.
- [ ] The procedure for a BROKEN result is known.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Audit trail for hosts](../../../hosting/security/audit-trail.md)
- [Security and audit](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
