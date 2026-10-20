---
title: Offsite and encrypted copies
audience: government, hosting, admin
category: public-sector
order: 130
summary: For public-sector IT and security teams. Why backups of an Exponential site must leave the server and be encrypted, where copies may go under data location rules, how to encrypt them with the tools the sources name, and how to keep the keys and passphrases recoverable.
keywords: offsite backup, encrypted backup, second location, GnuPG, encryption at rest, data location, data residency, passphrase, key management, ransomware, immutable storage, backup access
type: help
---
# Offsite and encrypted copies

This page is for IT and security teams in public bodies who decide where backups of a site are kept and how they are
protected. It answers why copies must leave the server, where they may go under data location rules, how to encrypt
them, and how to make sure the keys survive the disaster the backup is for.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Why the copy must leave](#why-the-copy-must-leave)
2. [Where copies may go](#where-copies-may-go)
3. [Encrypting](#encrypting)
4. [Keeping the keys recoverable](#keeping-the-keys-recoverable)
5. [Protecting copies from attackers](#protecting-copies-from-attackers)
6. [Checklist](#checklist)

## Why the copy must leave

"Store backups away from the server, and keep several generations"
([10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore)). A copy on the same
disk, the same machine or the same room does not survive a disk failure, a compromise or a fire. Backups made inside
the site (for example with git_manager, which stores them in `var/site/backups/captions`) are on the same server until
someone copies them away ([Backups from the administration with git_manager](backups-from-the-admin-with-git-manager.md)).

## Where copies may go

A backup holds the same data as the site: content, accounts, form entries, the audit trail and credentials. Your data
location rules apply to it as to the live site ([Data location and what is stored where](../hosting/data-location-and-what-is-stored-where.md)).

| Location | Consider |
|---|---|
| A second building of your own | distance from the first; access control |
| Your organization's enterprise backup system | usually the simplest route; meets central policies |
| A government cloud or another approved provider | the provider's locations and access; encryption before upload |
| Removable media in a safe | handling procedures; testing that media can still be read |

## Encrypting

Encrypt copies before they leave your control, and in transit. Tools the sources name:

- **GnuPG**, used by git_manager with AES-256 for its encrypted backups. A file encrypted that way is decrypted with:

  ```bash
  gpg --batch --yes --passphrase "YOUR_PASSPHRASE" -d backup_file.tar.gz.gpg | tar -xzf -
  ```

  Prefer a passphrase file or an agent over a passphrase on the command line in real use
  ([git_manager](../../../../features/6.0/extensions/git_manager.md)).
- Your enterprise backup system's own encryption, when it has one approved by your security team.

Whatever the tool, the files with credentials and keys (`settings/override/`) must never be in an unencrypted copy that
leaves the server ([Keys and secrets in backups](keys-and-secrets-in-backups.md)).

## Keeping the keys recoverable

"There is no recovery for a lost passphrase." An encrypted backup whose key was only on the lost server is no backup.

- Keep encryption keys and passphrases in your secrets store, with at least two people able to reach them.
- Keep a copy of the audit trail's keys there as well.
- Exercise a restore from the off-site copy, including decryption, as part of [Restore testing](restore-testing.md).

## Protecting copies from attackers

An attacker who controls the server may try to delete or encrypt the backups too. Practices that help:

- the server can write new backups but not change or delete old ones (write-once or append-only storage, or a pull
  from the backup system rather than a push from the server);
- separate credentials for the backup storage, not stored on the web server;
- several generations, so a backup taken after the attack began is not the only one
  ([Compromised site](../../../hosting/runbooks/compromised-site.md)).

The audit trail's reference configuration for long retention uses the same idea: archives on a mount the web server
can add files to but not change ([Audit trail archives and backups](audit-trail-archives-and-backups.md)).

## Checklist

- [ ] Every backup is copied off the server, to a location your data rules allow.
- [ ] Copies are encrypted at rest and in transit.
- [ ] Keys and passphrases are in the secrets store, reachable by at least two people.
- [ ] The server cannot delete or change older copies.
- [ ] A restore from the off-site copy, with decryption, has been tested.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Backup schedules and retention](backup-schedules-and-retention.md)
- [Disaster recovery plan](disaster-recovery-plan.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
