---
title: Compliance evidence from the audit trail
audience: businesses, admin, project-manager
category: business-solutions
order: 90
summary: How a business uses Exponential's tamper-evident audit trail as evidence for auditors, customers and its own reviews - what it records, how its integrity is proven, how to export a signed bundle, which alerts to route, and which settings to agree. Not legal advice.
keywords: audit trail, compliance, evidence, auditor, tamper-evident, hash chain, verify, export, bundle, signed manifest, alerts, retention, SIEM, accountability, who did what
type: help
---
# Compliance evidence from the audit trail

This page is for owners, compliance staff and managers who must show an auditor, a customer or their own board that
the site is run under control: who changed what, when, and that the record itself was not altered. Exponential's
audit trail was built for this. The page explains what it gives you and how to hand evidence over.

![The audit dashboard: health of the chains, events and security](../../images/help/bu-audit.webp)

> **Warning**
> Basics, not legal advice. Whether audit records satisfy a particular law, standard or contract is for you and your
> adviser or auditor to decide. The audit guide itself says the same about its privacy tasks
> ([Audit: privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

## Contents

1. [What the audit trail is](#what-the-audit-trail-is)
2. [What it records](#what-it-records)
3. [Why the record can be trusted](#why-the-record-can-be-trusted)
4. [Handing evidence to an auditor](#handing-evidence-to-an-auditor)
5. [Alerts: evidence that someone watches](#alerts-evidence-that-someone-watches)
6. [Settings to agree](#settings-to-agree)
7. [Common evidence requests](#common-evidence-requests)

## What the audit trail is

Since 6.0.15 Exponential records security-relevant events in a tamper-evident log. "The log is on by default, needs
no setup, and can be read from the admin interface or the command line." It is read in the **Audit** tab
(`/audit/dashboard`) ([The audit trail](../../../features/6.0/audit-trail.md)).

## What it records

| Channel | Examples |
|---|---|
| `content` | publish, move, copy, remove, hide, sections, states, trash, versions, URL aliases, classes, content jobs |
| `access` | sign-in, failed sign-in, refused view, account lock, role assignment, password reset |
| `system` | settings writes, cache clears, cronjob runs, commands, package installs, maintenance, deploys |
| `commerce` | orders, payments (never card data), VAT, currencies, discounts, exports and imports |
| `read` | sampled views, searches and downloads, only when switched on |

Source: [The audit trail](../../../features/6.0/audit-trail.md#what-is-recorded). Passwords, tokens, repair keys and
secrets are never recorded; addresses are truncated and sessions hashed.

## Why the record can be trusted

- **Hash chain**: every record carries the hash of the record before it.
- **Signed manifests**: daily files are closed, archived and listed in manifests signed with an HMAC.
- **Verification**: `./console exp:audit verify` reports whether anything was edited or cut, and exits with code 1
  when a chain is broken.
- **Access to the log**: only roles with `audit/read` see it; only Administrator holds it on a new installation, and
  a refused attempt is itself recorded.
- **When the log cannot be written**: by default the site keeps running and the failure is logged and shown;
  `OnWriteFailure=refuse` refuses security-relevant actions instead, for organizations where an unrecorded change is
  worse than a refused one.

Source: [The audit trail](../../../features/6.0/audit-trail.md).

## Handing evidence to an auditor

1. Agree the question: which period, which events (for example "all role assignments in 2026").
2. Run `exp:audit verify` and keep its output as proof that the chain was intact.
3. Export the records. In the administration, **Audit > Export** gives CSV, JSON lines or JSON, streamed or in the
   background. On the command line, a **bundle** contains the records as written and a manifest with the filter, the
   count, a sha256 and an HMAC: "It is what you hand to an auditor"
   ([Audit guide, Exporting](../../../bc/6.0/audit.md#exporting)).
4. Review the export before handing it out: it can contain personal data of staff and users. Give only what the
   question needs.
5. Record what you handed over, to whom and when, and delete local copies when they are no longer needed.

```text
EVIDENCE HANDOVER RECORD
Request from: ______________   Question: ______________________________
Period: __________ to __________   Events / channels: ______________
Chain verified on: __________ by: __________ result: intact / broken
Export format: bundle / csv / jsonl   File sha256: ______________________
Handed to: ______________ on: __________   Copies deleted on: __________
```

## Alerts: evidence that someone watches

Alert rules watch for brute-force sign-ins, an administrator role being granted, settings written out of hours, mass
deletes, the audit being switched off and a broken chain ([The audit trail](../../../features/6.0/audit-trail.md#why-you-want-it)).
Alert mail goes to the site's `AdminEmail` unless you name other recipients: users, groups, roles or addresses
([Audit guide, Alerts](../../../bc/6.0/audit.md#45-alerts-and-their-recipients)).

- [ ] Alerts reach a named person or group who reads them.
- [ ] Each alert is answered, and the answer noted (for example in your ticket system).
- [ ] `./console exp:audit alerts recipients` checked after staff changes.

## Settings to agree

| Topic | Default | Options | Source |
|---|---|---|---|
| Retention | 90 days live, 730 days archived | longer for compliance (the reference configuration keeps ten years), shorter for strict privacy | [Log and data retention](../../hosting/security/log-and-data-retention.md#the-audit-trail) |
| Privacy | minimization by default, pseudonymization after 90 days | stricter per field | [Audit: strict privacy](../../../bc/6.0/audit.md#64-strict-privacy) |
| Long-term evidence | archives on the server | write-once storage, signed checkpoints sent off the server | [Audit: compliance](../../../bc/6.0/audit.md#66-compliance-long-retention) |
| Central monitoring | none | syslog, signed webhook, mail; SIEM forwarding | [Audit: SIEM forwarding](../../../bc/6.0/audit.md#65-siem-forwarding) |
| Keys | in `settings/override/audit.ini.append.php` | back them up; keep every signing key as long as the archives it signed | [The audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes) |

Retention and privacy pull in opposite directions; settle the balance with your adviser and write it into your
retention schedule ([Data retention and deletion](data-retention-and-deletion.md)).

## Common evidence requests

| Request | Where the answer is |
|---|---|
| Who has administrator access, and since when? | role assignments in the `access` channel, plus the role pages ([Access reviews](access-reviews.md)) |
| Who changed this page? | the `content` channel, filtered by object |
| Were updates applied? | deploys and package installs in the `system` channel |
| Was a customer's order changed? | the `commerce` channel |
| What data did we export? | export events such as `data.export.csv`, which record the row count, never the values ([Collected information](../../../guides/collected-information.md)) |
| Did anyone try to break in? | failed sign-ins and the brute-force alerts in the `access` channel |
| Did consent change? | the mail consent log and the audit event `access.user.consent.change` ([The law checklist](../../../specifications/6.0/mail-preferences-compliance.md)) |

## Related pages

- [Audit trail for operations](../reliability/audit-trail-for-operations.md)
- [Audit trail for hosts](../../hosting/security/audit-trail.md)
- [KPIs from logs and audit](../../leadership/kpis-from-logs-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
