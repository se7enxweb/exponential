---
title: Keys and secrets in backups
audience: government, hosting, admin
category: public-sector
order: 60
summary: For security and operations teams in public bodies. Which small files of an Exponential site hold keys that cannot be recreated (audit signing keys, the mail preference secret, certificate keys), what breaks if they are lost, and how to back them up without spreading credentials.
keywords: keys, secrets, audit keys, signing key, pseudonym key, mail preference secret, TokenSecret, certificate keys, settings/override, secrets store, key rotation, GenerateKeys, backup encryption
type: help
---
# Keys and secrets in backups

This page is for security officers and operations teams in public bodies. It answers which files of an Exponential
site hold keys that the software generated and that cannot be recreated, what breaks if they are lost, and how to back
them up without spreading credentials further than necessary. The reference is
[Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md) and the audit guide's section on
[keys](../../../../bc/6.0/audit.md#46-keys).

## Contents

1. [The files](#the-files)
2. [What breaks when they are lost](#what-breaks-when-they-are-lost)
3. [Backing them up safely](#backing-them-up-safely)
4. [Keys from your secrets store](#keys-from-your-secrets-store)
5. [Rotation](#rotation)
6. [Checklist](#checklist)

## The files

| File | Holds |
|---|---|
| `settings/override/audit.ini.append.php` | the audit trail's installation id, signing keys and pseudonym key, generated on the first event |
| `settings/override/mailpreferences.ini.append.php` | `[SecretSettings] TokenSecret`, the site secret of the e-mail preferences |
| `settings/override/site.ini.append.php` | database credentials and other connection settings |
| Velocity's configuration tree (`/etc/vc`, or `var/vc/qbix/etc`) | site configuration and, in `ssl/`, private keys of certificates (mode `0600`) |
| `config.cluster.php`, cluster settings | the cluster database credentials |

The audit keys file has mode `0640` and is never committed; exp:ini, the debug bar and the settings view mask the keys
and show only ids and fingerprints ([audit guide](../../../../bc/6.0/audit.md#46-keys)).

## What breaks when they are lost

| Lost | Effect |
|---|---|
| Audit signing keys | archives and checkpoints signed with them can no longer be verified (`unknown_key`) |
| Audit pseudonym key | new pseudonyms no longer match old ones |
| Mail preference secret | every link in every mail already sent stops working, and suppression entries stop matching |
| Certificate keys | HTTPS on Velocity has to be set up again |

For a public body the first is the most serious: an audit trail that cannot be verified is weaker evidence.

## Backing them up safely

- **Include them in every backup.** They are small; leaving them out to "reduce risk" creates a worse risk.
- **Encrypt the backup**, or at least the part that holds `settings/override/`
  ([Offsite and encrypted copies](offsite-and-encrypted-copies.md)).
- **Limit who can read it.** A backup with `settings/override/` is "as sensitive as the server itself"; never hand it
  to a contractor or a test team without checking what it contains.
- **Keep a separate, protected copy of the keys** in your secrets store, so the audit trail stays verifiable even if
  the site backups are lost.
- **Test copies**: a test environment restored from production carries the same keys; decide whether that is
  acceptable, or give test environments their own keys.

## Keys from your secrets store

Instead of letting the audit generate its keys, you can provide them: set `[AuditKeySettings] GenerateKeys=disabled`
and provide `InstallationID`, `ActiveSigningKey`, `SigningKey[<id>]` (base64, 32 bytes) and `PseudonymKey` in
`settings/override` yourself ([audit guide](../../../../bc/6.0/audit.md#46-keys)). Several web servers must share one key
file.

## Rotation

Rotate the audit signing key once a year (the dashboard reminds you after 365 days), and at once if it may have been
read:

```bash
./console exp:audit key list --allow-root-user
./console exp:audit key rotate --apply --allow-root-user
```

The old keys stay for their archives; back up the file again after a rotation. Retention never removes a signing key
from the settings; remove one yourself only when no archive signed with it remains.

## Checklist

- [ ] The key files are in every backup, and the backup is encrypted.
- [ ] A separate copy of the audit keys is in the secrets store.
- [ ] Access to backups that contain `settings/override/` is restricted and logged.
- [ ] The audit signing key rotation date is in the operations calendar.
- [ ] Test environments' use of production keys is decided.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Audit trail archives and backups](audit-trail-archives-and-backups.md)
- [Configuration under version control](../hosting/configuration-under-version-control.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
