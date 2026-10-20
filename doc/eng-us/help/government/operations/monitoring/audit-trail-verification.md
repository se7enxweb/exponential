---
title: Audit trail verification
audience: government, admin, project-manager
category: public-sector
order: 80
summary: How Exponential proves its audit trail was not edited or cut - the hash chain, signed manifests of archives, exp:audit verify and Verify now - how to put verification into monitoring, and what to do when a chain is broken.
keywords: audit verification, hash chain, tamper-evident, exp:audit verify, Verify now, integrity, evidence, manifests, archives, broken chain, chain_broken, forensic
type: help
---
# Audit trail verification

This page is for administrators, security officers and auditors who rely on the audit trail as evidence. The trail is
tamper-evident: every record carries the hash of the one before it, daily files are archived and listed in signed
manifests, and a verification tells you if anything was edited or cut
([Audit trail](../../../../features/6.0/audit-trail.md); [audit guide, section 4.4](../../../../bc/6.0/audit.md#44-verify-and-restore)).

## Contents
1. [What verification proves](#what-verification-proves)
2. [Running it](#running-it)
3. [In monitoring](#in-monitoring)
4. [When a chain is broken](#when-a-chain-is-broken)
5. [Limits](#limits)
6. [Related pages](#related-pages)

## What verification proves

- No record inside a file was changed (each record's hash covers the one before).
- No record was removed from the middle or the end of a live file without the chain showing it.
- Archives match the manifest written when they were archived (signed with an HMAC key from
  `settings/override/audit.ini.append.php`).

## Running it

```bash
./console exp:audit status --allow-root-user            # every channel and its chain state
./console exp:audit verify --allow-root-user            # live files; exit 0 intact, 1 broken, 2 error
./console exp:audit verify --archives --allow-root-user # everything, archives included
./console exp:audit verify --channel=access --date=2026-10-01 --allow-root-user
```

In the administration: **Audit** tab, **Verify now** on the dashboard or console (needs `audit/manage`); plain page
views show the stored state. The `audit` cronjob part verifies daily as part of rotation and archiving.

## In monitoring

```bash
./console exp:audit verify -q --allow-root-user || <your alert command>
```

The rule `chain_broken` also sends an alert (syslog and mail) when verification finds a break. Verify before handing
records to an auditor and after any incident.

## When a chain is broken

From the audit guide: do not edit or remove anything. Note the file, line and kind of break; copy the audit directory
somewhere safe; compare with what left the server: syslog or journal copies, the webhook receiver, checkpoints sent by
the sinks, archives. A broken file stays a record: the console still shows it and marks it. Treat it as a possible
incident ([Suspected account compromise](../incidents/suspected-account-compromise.md)), because someone with access
to the server's files may have been at work. Note also that a crash or a full disk can damage the last line of a file;
the guide's section 5.3 explains each kind.

## Limits

- Tamper-evident is not tamper-proof: someone with shell access can delete the files. An outside copy (syslog to a
  central log server, the webhook sink) is what makes deletion visible and recoverable.
- The signing keys are in `settings/override/audit.ini.append.php`; back them up separately and keep them from people
  who could alter the files ([Keys and secrets in backups](../backups-and-recovery/keys-and-secrets-in-backups.md)).
- `OnWriteFailure=continue` (default) lets the site go on when the audit cannot write; `refuse` refuses
  security-relevant actions while their channel cannot be written. Choose per your risk appetite.

## Related pages
- [Verify the audit trail](../../tasks/operations/verify-the-audit-trail.md)
- [Audit alerts](audit-alerts.md)
- [Audit trail archives and backups](../backups-and-recovery/audit-trail-archives-and-backups.md)
- [Role design: auditors](../people-and-permissions/role-design-auditors.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
