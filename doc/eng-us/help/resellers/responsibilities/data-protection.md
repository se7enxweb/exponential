---
title: Data protection for resellers
audience: reseller, partner, hosting
category: reselling
order: 430
summary: How a reseller protects the personal data of its customers and their visitors - your role as a processor, processing terms and subprocessors, what Exponential provides for privacy (consent records, masked logs, retention, erasure) and its known gaps, where data leaks in practice, and a checklist. Not legal advice.
keywords: data protection, privacy, GDPR, personal data, processor, controller, data processing agreement, subprocessors, consent, retention, erasure, logs, staging, leads, case studies
type: help
---
# Data protection for resellers

This page is for owners and technical leads at resellers. When you host, run or support a customer's site, you
usually process personal data on the customer's behalf: editors' accounts, members, orders, form entries, logs. This
page names the duties that typically follow and what Exponential provides. It is **not legal advice**; data
protection law differs by country, so ask a lawyer for your contracts.

## Contents

1. [Your role](#your-role)
2. [Contracts and subprocessors](#contracts-and-subprocessors)
3. [What Exponential provides](#what-exponential-provides)
4. [Known gaps to plan for](#known-gaps-to-plan-for)
5. [Where data leaks in practice](#where-data-leaks-in-practice)
6. [Checklist](#checklist)

## Your role

| Situation | Typical role under laws such as the GDPR |
|---|---|
| You host or run the customer's site | Processor for the customer, who is the controller |
| You buy in hosting or white-label 7x services | Your supplier is your subprocessor |
| Your own leads, your own customers' contact data | You are the controller |

Confirm the roles with your lawyer; they decide what your contract must say.

## Contracts and subprocessors

- [ ] A data processing agreement with each customer, or processing terms in the contract.
- [ ] A list of subprocessors (hosting supplier, backup storage, mail service, 7x for white-label services) and where
      the data is stored ([White-label reseller](../models/white-label-reseller.md#settle-these-in-writing)).
- [ ] How and when you notify the customer of a breach.
- [ ] What happens to data at the end of the contract ([Handing over](../customers/handing-over.md#after-the-handover)).

## What Exponential provides

From [Privacy standards](../../../standards/privacy.md):

| Topic | What ships |
|---|---|
| Consent | Optional e-mail categories off until the person turns them on; double opt-in for newsletters and marketing |
| Records of consent | Table `expmail_consent_log`, with the exact text shown |
| Access and portability | Mail preferences and consent history downloadable as JSON or CSV; audit records by or about a user exportable with `./console exp:audit export --subject-user=<id>` |
| Erasure | Removing a user account erases mail preferences and anonymizes the consent log; audit records are pseudonymized |
| Minimization in logs | Audit addresses truncated, session ids hashed; request logs with `MaskIP=enabled`; log viewer masks addresses and secrets |
| Storage limitation | Audit: 90 days live and 2 years archived; `settings/logviewer.ini [RotationSettings] MaxAgeDays[]` for some logs |
| No third-party calls from the administration | Admin scripts are served locally; admin4 loads no web fonts or scripts from other hosts |

Status in the standards page: **Partly complies**. The site operator still decides purposes and legal bases, keeps
the record of processing, writes the privacy notice and answers requests. Hosts have their own page:
[Compliance and GDPR for hosts](../../hosting/security/compliance-and-gdpr.md).

## Known gaps to plan for

From [Privacy standards](../../../standards/privacy.md#known-gaps):

- No single "export all my data" or "delete me" function; a person's data is reached through separate tools.
- User removal is manual and one account at a time.
- Kernel logs such as `error.log` and `debug.log` are rotated by size, without an age limit by default.
- Information collection forms store what visitors type; the site must add a consent text and a retention rule.
- Search phrases are stored and can contain personal data.

Offer to handle these as part of your service, and say which ones the customer must handle.

## Where data leaks in practice

| Place | Prevention |
|---|---|
| Support tickets and bug reports | Clean logs before pasting; placeholders such as `customer-site.example` |
| Screenshots | Test accounts; crop or blur |
| Database dumps for debugging | Share a reproduction, not a dump |
| Staging copies | Anonymize; protect staging like production |
| Demo sites | Never real data ([Demo sites for resellers](../offers/demo-sites.md)) |
| Leads passed on | Consent recorded first ([Lead handoffs for resellers](../sales/lead-handoffs.md)) |
| Case studies | Written approval ([Case studies for resellers](../sales/case-studies.md)) |

Source: [Protecting customer data](../../partners/protecting-customer-data.md).

## Checklist

- [ ] Roles confirmed; processing terms in every contract.
- [ ] Subprocessor list current and shared with customers.
- [ ] Staff trained on placeholders, screenshots and dumps.
- [ ] Staging anonymized; demos without real data.
- [ ] Log retention set where the customer needs it.
- [ ] Breach notification procedure written.
- [ ] Deletion routine for exports, dumps and departed customers.

## Related pages

- [Protecting customer data](../../partners/protecting-customer-data.md)
- [Log and data retention](../../hosting/security/log-and-data-retention.md)
- [GDPR requests (for site owners)](../../customers/commerce/gdpr-requests.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
