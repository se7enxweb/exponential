---
title: Mapping the GDPR to Exponential
audience: government, project-manager, admin, developer
category: public-sector
order: 20
summary: For EU public bodies and their data protection officers - GDPR topics (consent, records of consent, access, erasure, restriction, minimization, storage limitation, security, third parties) mapped to the features Exponential provides, with the project's status "Partly complies (features provided)" and the operator's remaining duties.
keywords: GDPR, Regulation 2016/679, data protection, consent, right of access, right to erasure, restriction, data minimization, storage limitation, security of processing, mapping, DPO, public sector, controller
type: reference
---
# Mapping the GDPR to Exponential

This page is for data protection officers, project managers and administrators of public bodies in the EU (and
elsewhere where the GDPR applies). It maps GDPR topics to what Exponential provides, from
[Privacy standards: GDPR](../../../standards/privacy.md#gdpr). The project's status is **Partly complies**, in the
sense that the software provides the features listed; the regulation's duties stay with the organization that runs the
site. Official text: Regulation (EU) 2016/679, <https://eur-lex.europa.eu/eli/reg/2016/679/oj>.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The map](#the-map)
2. [What stays with the organization](#what-stays-with-the-organization)
3. [Known gaps](#known-gaps)
4. [Related pages](#related-pages)

## The map

| GDPR topic | What Exponential provides | Page in this section |
|---|---|---|
| Consent (art. 4(11), 7) | mail categories off until the person turns them on; one switch per purpose; double opt-in for newsletters and marketing; `DefaultOn=true` refused unless explicitly allowed | Mail categories and opt-in |
| Records of consent (art. 7(1)) | table `expmail_consent_log` with time, category, old and new state, source, the exact text shown, acting user, siteaccess; audit event `access.user.consent.change` | Consent records for mail |
| Withdrawal (art. 7(3)) | the same switch, "Turn off all optional e-mail", one-click unsubscribe in every optional mail, no login needed | One-click unsubscribe |
| Access and portability (art. 15, 20) | mail preferences and consent history as JSON or CSV; audit records by or about a user | Right of access; What cannot be exported |
| Erasure (art. 17) | removing a user account erases its mail preferences and anonymizes its consent log; `exp:mail:preferences erase`; audit records kept for their retention and pseudonymized | Right to erasure |
| Restriction (art. 18) | Deactivate and move (opt-in); Check user accounts | Restriction, deactivate and move |
| Minimization (art. 5(1)(c), 25) | audit: addresses truncated, sessions hashed, no passwords or POST bodies, per-field settings; request logs: masked addresses, query values `***`; log viewer masking | Data minimization; Log masking; Audit trail privacy |
| Storage limitation (art. 5(1)(e)) | audit 90 days live and 2 years archived; 404, security and CSP logs pruned after 30, 90 and 30 days; consent log retention | Retention overview |
| Security (art. 32) | see the security section of this help | (security section) |
| No data to third parties by the administration | admin scripts served locally; admin4 loads no fonts or scripts from other hosts | Third-party extensions |
| Error reports without personal data | the help's error report scrubs addresses and credentials | Personal data inventory |

## What stays with the organization

From the project's statement: decide purposes and legal bases, keep the record of processing (art. 30), write the
privacy notice, sign processing agreements with any third party it adds, and answer requests. For a public body add:
the DPIA where required, the retention schedule, and staff instructions.

## Known gaps

- No single "export all my data" or "delete me" function.
- User removal is manual, one account at a time; a user object never goes to the trash.
- Kernel logs other than 404, security and CSP have no age limit by default.
- Information collection forms store what visitors type; consent text and retention are the site's job.
- Search phrases are stored where search statistics are switched on.

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Audit guide, 4.12 Privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)
- [Compliance and GDPR for hosts](../../hosting/security/compliance-and-gdpr.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
