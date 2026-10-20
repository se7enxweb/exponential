---
title: Audit integrity and verification
audience: government, admin, developer
category: public-sector
order: 90
summary: For public bodies that rely on the audit trail as evidence - how Exponential's hash chains, signed checkpoints and signed archive manifests make tampering visible, how to verify them, what each finding means, and what to do when a chain is broken.
keywords: audit integrity, hash chain, tamper evident, verification, exp:audit verify, checkpoints, HMAC, signing key, manifests, broken chain, evidence, chain of custody, forensic, public sector
type: help
---
# Audit integrity and verification

This page is for administrators and information security officers who may have to present audit records as
evidence, for example in a disciplinary case, an investigation or a dispute about who changed official information.
It explains how the integrity of Exponential's audit trail is protected and checked, from
[5.3 The hash chain and what verification finds](../../../bc/6.0/audit.md#53-the-hash-chain-and-what-verification-finds)
and [4.4 Verify and restore](../../../bc/6.0/audit.md#44-verify-and-restore).

## Contents

1. [How tampering shows](#how-tampering-shows)
2. [Verifying](#verifying)
3. [What a finding means](#what-a-finding-means)
4. [When a chain is broken](#when-a-chain-is-broken)
5. [Keys](#keys)
6. [Related pages](#related-pages)

## How tampering shows

- **Hash chain per channel**: every record carries the SHA-256 hash of the record before it; the first file starts
  from a value derived from the installation id and the channel, so a chain cannot be passed off as another's.
- **Signed checkpoints**: once a day, `system.audit.checkpoint` records every channel's current position and hash,
  signed with HMAC-SHA-256, and goes to the sinks. A chain recomputed afterwards no longer matches a checkpoint signed
  before.
- **Signed manifests** for each archived day, chained to each other, so removing a whole archived day shows too.

An attacker with full control of the server could still delete everything; that is why forwarding records and
checkpoints off the machine matters (see "Forwarding the audit trail to a SIEM").

## Verifying

```bash
./console exp:audit verify --allow-root-user                       # live files, every channel
./console exp:audit verify --archives --allow-root-user            # archives too
./console exp:audit verify --channel=access --date=<YYYY-MM-DD> --allow-root-user
./console exp:audit verify -q --allow-root-user || <your alert command>
```

Exit code 0 means intact, 1 broken, 2 error, so the command fits monitoring. The cronjob part verifies every channel
daily; **Verify now** on the Audit dashboard does it on demand (`audit/manage`; with `ReauthForManage=enabled` the
user's password is asked first). For long retention, the guide recommends running `verify --archives` monthly and
keeping its output.

## What a finding means

| Kind | Means |
|---|---|
| `altered` | a record was changed |
| `link`, `gap`, `reordered` | lines removed, inserted or swapped |
| `missing_file`, `truncated` | a file is gone, or lines were cut off |
| `rewritten` | a signed checkpoint recorded another hash: the chain was recomputed |
| `checkpoint_invalid`, `unknown_key` | a checkpoint does not verify, or its key is not in the settings |
| `archive_sha256`, `hmac_invalid`, `previous_manifest` | an archive changed, a manifest was edited, an archived day removed |
| `repaired` (not a break) | a torn last line followed by a recorded repair |

## When a chain is broken

The audit guide's instruction is: **do not edit or remove anything**.

1. Note the file, line and kind of change from the verify output.
2. Copy the audit directory to safe, read-only storage.
3. Compare with what left the server: syslog or journal copies, the webhook receiver, the checkpoints sent by the
   sinks, the archives.
4. Treat it as a security incident (see "Incident response").
5. A broken file stays a record: the console still shows it and marks it.

## Keys

The signing and pseudonym keys are in `settings/override/audit.ini.append.php` (mode 0640, never committed),
generated on first use ([4.6 Keys](../../../bc/6.0/audit.md#46-keys)):

- back the file up as a secret; without the signing keys, old archives cannot be verified (`unknown_key`);
- rotate the signing key once a year, and at once if it may have been read: `./console exp:audit key rotate --allow-root-user`;
  old keys stay for their archives;
- keys can come from a secrets store with `[AuditKeySettings] GenerateKeys=disabled`.

## Related pages

- [Audit guide, 5.3](../../../bc/6.0/audit.md#53-the-hash-chain-and-what-verification-finds)
- [The audit trail for public bodies](audit-trail-for-public-bodies.md)
- [Audit trail](../../../features/6.0/audit-trail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
