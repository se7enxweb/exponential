---
title: Privacy in the audit trail
audience: government, admin, project-manager
category: public-sector
order: 60
summary: For public bodies balancing accountability and privacy - what personal data Exponential's audit trail keeps by default, how it is minimized and pseudonymized, the lawful basis the audit guide suggests, and the strict-privacy configuration for sites that must keep less.
keywords: audit trail privacy, pseudonymization, AuditPrivacySettings, Field, truncate, hash, PseudonymiseAfterDays, BeforeAfter, strict privacy, lawful basis, legitimate interest, legal obligation, GDPR, employees, public sector
type: help
---
# Privacy in the audit trail

This page is for data protection officers and administrators. The audit trail records what staff and visitors do,
which is personal data about them, often employees. Exponential designs it for privacy by default and lets you make it
stricter ([4.12 Privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What is kept by default](#what-is-kept-by-default)
2. [Lawful basis](#lawful-basis)
3. [Pseudonymization](#pseudonymization)
4. [A stricter profile](#a-stricter-profile)
5. [Employees and works councils](#employees-and-works-councils)
6. [Related pages](#related-pages)

## What is kept by default

`settings/audit.ini [AuditPrivacySettings] Field[]` as shipped:

| Field | Default | Meaning |
|---|---|---|
| `actor.login` | `full` | the login of the person acting |
| `actor.ip` | `truncate` | the address cut to its /24 network (IPv6 /48) |
| `actor.ua` | `truncate` | user agent reduced to browser and OS family |
| `actor.session` | `hash` | the session id is never recorded |
| `request.url` | `truncate` | |
| `object.name` | `full` | the name of the object acted on |
| `email`, `attempted_login` | `hash` | addresses and logins of unknown users |

Never recorded: content values, passwords, password hashes, tokens, reset keys, secrets, POST bodies. Reads (the
`read` channel) are off.

## Lawful basis

The audit guide names, for your own record of processing: legitimate interest in the security and accountability of
the system (art. 6(1)(f)) and a legal obligation where a law requires access logging (art. 6(1)(c)); art. 32 names
logging as a security measure. Public bodies often rely on a public task or legal obligation instead of legitimate
interest; decide with your DPO and document it.

## Pseudonymization

- After `PseudonymiseAfterDays` (90 by default) the daily task replaces login, address, user agent and personal fields
  **in the index** (which the console searches) with their keyed hash; recorded as `system.audit.pseudonymise`.
- Preview: `./console exp:audit pseudonymise --dry-run --allow-root-user`.
- The files are not rewritten (that would break the hash chain): full values remain in the archives, readable with
  `audit/manage`. A site that must not keep them even there sets `hash` from the start.
- A hash is a keyed HMAC: the same person still groups together (alerts by address keep working) but cannot be read
  back without the key.

## A stricter profile

The guide's strict-privacy reference configuration ([6.4 Strict privacy](../../../bc/6.0/audit.md#64-strict-privacy)),
in `settings/override/audit.ini.append.php`:

```ini
[AuditPrivacySettings]
Field[actor.login]=hash
Field[actor.ip]=hash
Field[actor.ua]=off
Field[actor.cli.os_user]=hash
Field[request.url]=truncate
Field[request.host]=hash
Field[object.name]=truncate
PseudonymiseAfterDays=30

[AuditRecordSettings]
BeforeAfter=keys
```

with shorter retention per channel (`LiveDays=30`, `ArchiveDays=180`) and `[AuditIndexSettings] KeepDays=180`. With
`BeforeAfter=keys` the record says which fields changed, not their values. Weigh it against investigations: hashed
logins can still be matched when you know the login, but not read.

## Employees and works councils

Monitoring staff is regulated in many jurisdictions and may need consultation with staff representatives. Document
what the audit records, who may read it (`audit/read`, limited by channel), how long it is kept, and that it is not
used for performance monitoring unless agreed. Give `audit/read` to few people; every read of the audit is itself
recorded (`system.audit.read`).

## Related pages

- [Audit guide, 4.12](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)
- [Audit trail](../../../features/6.0/audit-trail.md)
- [Audit retention](audit-retention.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
