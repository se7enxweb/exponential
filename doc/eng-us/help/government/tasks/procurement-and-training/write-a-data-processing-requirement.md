---
title: Write a data processing requirement
audience: government, project-manager, admin
category: public-sector
order: 40
summary: Step by step, write the data protection requirements for a tender for an Exponential site and its hosting - data location, processors, third parties, retention, individuals' rights and return of data - based on what the software provides and what stays with you.
keywords: data processing requirement, GDPR, processor, processing agreement, data location, third parties, retention, rights, erasure, tender, procurement task
type: help
---
# Write a data processing requirement

**Who:** the procurement officer with the data protection officer. **When:** while drafting the requirements. The facts
come from [Privacy standards](../../../../standards/privacy.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel. EU: [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Example](#example)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- List the personal data the site will hold: accounts, form submissions, newsletter subscribers, logs
  ([Privacy checklist](../../checklists/privacy-checklist.md)).
- Know what the software provides: consent log, opt-in mail categories, erasure on account removal, log masking and
  retention settings; and what it lacks: no "export all my data", no cookie banner
  ([Privacy standards](../../../../standards/privacy.md#known-gaps)).

## Steps

1. **Identify the processors**: host, support supplier, any third-party service. Each signs a processing agreement.
2. **State data location** for production, backups and support access.
3. **Forbid unannounced third parties**: no extension that sends visitor data to a third party (analytics, CAPTCHA,
   social login, AI provider) without your written approval ([Privacy standards](../../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
4. **Set retention**: forms, logs (the kernel's general logs have no age limit by default), backups, audit records.
5. **Require support for individuals' rights**: the supplier documents how to find and export or erase a person's data
   across accounts, forms and newsletters, since there is no single function.
6. **Require no personal data in test, development or training copies.**
7. **Require return and deletion** at the end ([Personal data at exit](../../procurement/exit-plans/personal-data-at-exit.md)).

## Example

```text
DP-01  The Supplier acts as processor under a processing agreement in the form of Annex <x>.
DP-02  Production data, backups and support access remain in <location>.
DP-03  No component that transmits personal data to a third party is activated without <body>'s prior
       written approval; the Supplier lists every such component in the <period> report.
DP-04  Retention: form submissions <period>; logs <period>; backups <period>; audit records <period>.
DP-05  The Supplier documents and tests a procedure to export and erase one person's data within <time>.
DP-06  Test, development and training copies contain no personal data from production.
```

## Result

Requirements that make the data flows explicit and checkable, matched to what the software can do.

## Pitfalls

- Assuming the software is "GDPR compliant": the organization is the controller.
- Forgetting backups and logs in retention rules.
- Activating an analytics extension without consent and a notice.

## Related pages

- [Procurement and training tasks](README.md)
- [Privacy and data protection requirements](../../procurement/requirements/privacy-and-data-protection-requirements.md)
- [Privacy and data processing clause](../../procurement/tender-templates/privacy-and-data-processing-clause.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
