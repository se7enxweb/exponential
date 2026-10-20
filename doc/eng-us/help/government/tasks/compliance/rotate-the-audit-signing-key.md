---
title: Rotate the audit signing key
audience: government, admin
category: public-sector
order: 230
summary: Step by step - replace the key that signs Exponential's audit checkpoints and archive manifests, yearly or at once after a suspected leak, while keeping old keys for old archives, and back up the result.
keywords: audit signing key, key rotation, exp:audit key rotate, SigningKey, HMAC, checkpoints, manifests, fingerprint, pseudonym key, settings override backup, public sector
type: help
---
# Rotate the audit signing key

For administrators. The audit signs its daily checkpoints and archive manifests with a key kept in
`settings/override/audit.ini.append.php`. Rotate it once a year (the dashboard reminds you after 365 days) and at once if
it may have been read ([Audit guide, 4.6 Keys](../../../../bc/6.0/audit.md#46-keys)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Shell access.
- A current backup of `settings/override/audit.ini.append.php`, stored as a secret.

## Steps

1. Note the current key: `./console exp:audit key list --allow-root-user` (ids and fingerprints only).
2. Rotate:

   ```bash
   ./console exp:audit key rotate --allow-root-user
   ```

   The new key becomes active; old keys stay for the archives they signed; `system.audit.key.rotate` is recorded. The
   command clears the INI cache.
3. Restart Velocity if it runs, so no worker keeps signing with the old key: `./console exp:velocity restart --allow-root-user`.
4. Back up `settings/override/audit.ini.append.php` again.
5. In a cluster, copy the key file identically to every server.

## Check that it worked

- `./console exp:audit status --allow-root-user` shows the new signing key id and fingerprint.
- After the next checkpoint (`./console exp:audit checkpoint --allow-root-user` to make one now),
  `./console exp:audit verify --allow-root-user` is intact.

Do not remove old keys while archives signed with them remain; `exp:audit purge` names the keys still needed. The
pseudonym key is a different key, rotated only on purpose (`key rotate --pseudonym`).

## Related pages

- [Audit integrity and verification](../../security/audit-integrity-and-verification.md)
- [Secrets in settings/override](../../security/secrets-in-settings-override.md)
- [Long-term audit retention](../../records-and-archiving/long-term-audit-retention.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
