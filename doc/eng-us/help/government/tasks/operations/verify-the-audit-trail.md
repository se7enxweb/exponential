---
title: Verify the audit trail
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Step by step - check that the audit trail is complete and unchanged with exp:audit status and verify (live files and archives), or Verify now in the Audit tab, and what to do with the result.
keywords: audit verify, hash chain, integrity, exp:audit status, exp:audit verify, archives, Verify now, evidence, tamper-evident, broken chain
type: help
---
# Verify the audit trail

This task is for administrators and audit owners, weekly, after an incident and before handing records to anyone.
At the end you have proof that the record was not edited or cut. Background:
[Audit trail verification](../../operations/monitoring/audit-trail-verification.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Reading the result](#reading-the-result)
4. [Related pages](#related-pages)

## Before you start

- Shell access, or `audit/manage` for **Verify now** in the browser.

## Steps

```bash
./console exp:audit status --allow-root-user
./console exp:audit verify --allow-root-user
./console exp:audit verify --archives --allow-root-user     # also every archived day
```

`status` lists each channel (`content`, `access`, `system`, `commerce`, `read`) with files, bytes, records today and
the chain state. `verify` prints `INTACT` per channel; exit code 0 intact, 1 broken, 2 error. In the browser: **Audit**
tab, dashboard, **Verify now**.

For one day of one channel: `--channel=access --date=2026-10-01`.

## Reading the result

| Result | Do |
|---|---|
| Every channel `INTACT` | keep the output with your weekly record or the evidence package |
| `BROKEN` | do not edit or remove anything; note file, line and kind; copy the audit directory somewhere safe; compare with syslog or webhook copies; treat as a possible incident ([Suspected account compromise](../../operations/incidents/suspected-account-compromise.md)) |
| Error (exit 2) | the audit directory or keys cannot be read; check permissions and `settings/override/audit.ini.append.php` |

A crash or a full disk can damage the last line of a file; the audit guide explains each kind of break
([audit guide, section 4.4](../../../../bc/6.0/audit.md#44-verify-and-restore)).

## Related pages
- [Export audit records for an auditor](export-audit-records-for-an-auditor.md)
- [Check who gets audit alerts](check-who-gets-audit-alerts.md)
- [Audit trail archives and backups](../../operations/backups-and-recovery/audit-trail-archives-and-backups.md)
- [Audit trail (hosting)](../../../hosting/security/audit-trail.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
