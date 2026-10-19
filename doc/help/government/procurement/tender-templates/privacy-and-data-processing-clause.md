---
title: "Tender template: privacy and data processing clause"
audience: government, project-manager, partner, hosting
category: public-sector
order: 60
summary: Clause text for the privacy obligations of a CMS contract and the points a data processing agreement must cover - processing on instructions, sub-processors, locations, minimization settings, data subject requests, breach notice and deletion at the end.
keywords: privacy clause, data processing agreement, DPA, processor, sub-processor, GDPR article 28, data minimization, cookies, consent, breach notification, deletion, tender template
type: help
---
# Tender template: privacy and data processing clause

This page is for data protection officers and procurement officers. When a supplier builds, hosts or supports a
site, it processes personal data on your behalf. In the EU, the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj)
requires a contract with the processor (article 28). The clause below covers the CMS-specific points; your standard
data processing agreement covers the rest.

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [Personal data a CMS holds](#personal-data-a-cms-holds)
3. [What Exponential provides for the clause](#what-exponential-provides-for-the-clause)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
P. PRIVACY AND DATA PROCESSING

P.1 Roles. The Customer is the controller of personal data processed in the
    system. The Supplier processes it only on the Customer's documented
    instructions, under the data processing agreement in <annex>.

P.2 Inventory. Before go-live the Supplier shall deliver an inventory of the
    personal data the delivered system stores (tables, files, logs, backups),
    its purpose and its retention, and every cookie the public site sets.

P.3 Minimization by configuration. The Supplier shall configure log masking and
    retention as set out in <annex>, and shall not activate any extension or
    service that sends visitor data to a third party without the Customer's
    written approval and, where required, a consent mechanism.

P.4 Sub-processors and locations. The Supplier shall list every sub-processor and
    every location where personal data or backups are stored, and shall not
    change them without <period> prior notice and the Customer's right to object.

P.5 Requests. The Supplier shall assist the Customer in answering requests for
    access, correction, erasure and restriction within <time>, using the
    documented tools of the system, and shall document any data that cannot be
    reached by those tools.

P.6 Breaches. The Supplier shall notify the Customer of a personal data breach
    without undue delay and at the latest within <time> of becoming aware of it.

P.7 End of contract. At the end of the contract the Supplier shall return the data
    as set out in <exit clause> and then delete it, including from backups within
    <time>, and confirm the deletion in writing.
```

## Personal data a CMS holds

For P.2, ask the bidder to cover at least these, from [Privacy standards](../../../../standards/privacy.md):

| Where | What |
|---|---|
| User accounts | names, e-mail addresses, sign-in data of staff and registered visitors |
| Information collection | everything visitors type into forms ([Collected information](../../../../guides/collected-information.md)) |
| E-mail preferences and consent log | categories, consent history, the text shown ([E-mail preferences](../../../../features/6.0/mail-preferences.md)) |
| Newsletter data | subscribers; per-person statistics only with consent |
| Search phrases | stored, can contain personal data |
| Audit trail | actors, truncated addresses, pseudonymized after a set period ([Audit trail](../../../../features/6.0/audit-trail.md)) |
| Logs | request logs, error logs, web server logs (outside Exponential) |
| Backups | all of the above |

## What Exponential provides for the clause

| Clause | Feature |
|---|---|
| P.3 | Audit field settings `[AuditPrivacySettings] Field[]`; `MaskIP=enabled` for request logs; log viewer redaction; `MaxAgeDays` per log ([Log rotation](../../../../features/6.0/log-rotation.md)); third-party extensions not active by default |
| P.5 | Mail preferences export (JSON, CSV), `./console exp:audit export --subject-user=<id>`, erasure of mail data on account removal, "Deactivate and move" for restriction ([Check user accounts](../../../../features/6.0/user-hygiene.md)) |
| P.5 gap | No single "export all my data" or "delete me"; forms, orders and content are reached separately |
| P.2 gap | No cookie consent banner in the kernel or shipped designs |

## Notes for the evaluator

- A bidder who proposes analytics, CAPTCHA or social sign-in should name the third party and the consent approach
  (P.3).
- Ask how the bidder answered an access request on another Exponential site, step by step, without customer details.
- The hosting side of P.4 and P.7: [Compliance and GDPR for hosts](../../../hosting/security/compliance-and-gdpr.md).

## Related pages
- [Tender templates](README.md)
- [Privacy and data protection requirements](../requirements/privacy-and-data-protection-requirements.md)
- [Hosting and data location clause](hosting-and-data-location-clause.md)
- [Exit and data return clause](exit-and-data-return-clause.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
