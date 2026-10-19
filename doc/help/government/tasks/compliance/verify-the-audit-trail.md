---
title: Verify the audit trail
audience: government, admin, hosting
category: public-sector
order: 170
summary: Step by step - check that no audit record was changed, removed or inserted with exp:audit verify or Verify now, add the check to monitoring by its exit code, and know what to do when a channel is broken.
keywords: audit verify, hash chain, intact, broken, exp:audit verify, Verify now, monitoring, exit code, archives, tamper evident, incident, public sector
type: help
---
# Verify the audit trail

For administrators and hosting teams. The audit trail's hash chains make tampering visible, but only if someone checks.
The cronjob part verifies daily; this task covers checks on demand and in monitoring
([Audit integrity and verification](../../security/audit-integrity-and-verification.md)).

## Contents

1. [Steps](#steps)
2. [In monitoring](#in-monitoring)
3. [When a channel is broken](#when-a-channel-is-broken)
4. [Related pages](#related-pages)

## Steps

On the command line:

```bash
./console exp:audit verify --allow-root-user                  # live files
./console exp:audit verify --archives --allow-root-user       # archives as well
./console exp:audit verify --channel=access --allow-root-user
```

Each channel prints **INTACT** with the number of records and matched checkpoints, or **BROKEN** with file, line and kind.

In the browser (policy `audit/manage`): the Audit dashboard or console, **Verify now**. With
`[AuditConsoleSettings] ReauthForManage=enabled`, your password is asked first.

## In monitoring

Exit code 0 = intact, 1 = broken, 2 = error:

```bash
./console exp:audit verify -q --allow-root-user || <your alert command>
```

Run it daily from your monitoring system as the site's user; keep a monthly `--archives` run's output.

## When a channel is broken

1. Do not edit or remove anything.
2. Note the file, line and kind; copy the audit directory to read-only storage.
3. Compare with copies that left the server (SIEM, webhook receiver, checkpoints).
4. Treat it as a security incident ([Incident response](../../security/incident-response.md)).

A torn last line followed by a recorded repair shows as `repaired`, which is not a break.

## Related pages

- [Audit integrity and verification](../../security/audit-integrity-and-verification.md)
- [Audit guide, 4.4 Verify and restore](../../../../bc/6.0/audit.md#44-verify-and-restore)
- [Forwarding the audit trail to a SIEM](../../security/forwarding-audit-to-a-siem.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
