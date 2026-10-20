---
title: Evaluating a support partner for Exponential
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 120
summary: How a public body evaluates the company that will build, host or support its Exponential site - partner levels and certifications as published, the support the partner itself can count on, the questions about staff, security, data and continuity, and a scoring checklist.
keywords: support partner, implementation partner, hosting partner, partner level, certification, support contract, SLA, response time, supplier evaluation, vendor due diligence
type: help
---
# Evaluating a support partner for Exponential

This page is for the evaluator who judges a supplier rather than the software: the implementation partner, hosting
company or support provider that bids to look after an Exponential site. Because the software is free, the supplier
is where most of the risk and cost sits. The facts about the partner program come from the partner section of this
help, which quotes the program pages as published on 18 October 2026.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Three kinds of supplier](#three-kinds-of-supplier)
2. [What the partner program tells you](#what-the-partner-program-tells-you)
3. [What support the supplier can count on](#what-support-the-supplier-can-count-on)
4. [Questions about the supplier](#questions-about-the-supplier)
5. [A supplier checklist](#a-supplier-checklist)
6. [Pitfalls](#pitfalls)

## Three kinds of supplier

| Supplier | Delivers | Evidence to ask for |
|---|---|---|
| Implementation partner | content model, design, templates, migration | [Implementation partners](../../../partners/implementation-partners.md), [implementation playbook](../../../partners/implementation-project-playbook.md) |
| Hosting partner | servers, backups, updates, monitoring | [Hosting partners](../../../partners/hosting-partners.md), [hosting technical checklist](../../../partners/hosting-partner-technical-checklist.md) |
| Support provider | answers, fixes, upgrades over the years | its support offer; compare with [Support tiers](../../../hosting/business/support-tiers.md) |

One company may offer all three; evaluate each part separately.

## What the partner program tells you

- The program has published levels from Student to Platinum; Professional and above are described as "Qualified"
  and Gold as having "successfully delivered one or more enterprise-level projects"
  ([Partner levels](../../../partners/partner-levels.md)). The level pages disagree on some details; the help notes this.
- "For Silver tier and above, partners must hold valid certifications" (Developer, Design, Solution, Sales). The
  syllabus, price and validity of certifications are not yet published ([Certifications](../../../partners/certifications.md)).
- Partners are expected to follow security and data practices described in
  [Security responsibilities](../../../partners/security-responsibilities.md) and
  [Protecting customer data](../../../partners/protecting-customer-data.md).

A partner level is a signal, not proof. Check it where partners are listed ([Partner directories](../../../partners/partner-directories.md))
and ask for evidence of the work behind it.

## What support the supplier can count on

Your supplier's promise to you can only be as good as its own staff and its own escalation path:

| Source | Promise | Page |
|---|---|---|
| Community | none: free help depends on people's time | [Getting support](../../../project/getting-support.md) |
| Partner support | response times per partner level, as published | [Support levels and response times](../../../partners/support-levels-and-response-times.md) |
| Paid help from 7x | agreed with 7x | [Who is 7x?](../../../who-is-7x.md) |
| Security reports | first answer within one business day | `SECURITY.md` |

The partner page itself warns partners never to promise a customer a shorter response than they can deliver with
their own staff. Ask the bidder how its promise to you is staffed.

## Questions about the supplier

**Staff and skills**
- How many people will work on the site, with which roles? Who replaces them during leave?
- Which training or certification have they completed? The free course material is in the
  [Course catalogue](../../../trainers/course-catalogue.md).

**Security and data**
- How are credentials handed over and stored? (Never in tickets or e-mail, as the partner guidance says.)
- Will the supplier process personal data for you? Then a data processing agreement is needed under the
  [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) if it applies to you.
- How does the supplier test and apply security releases ([Testing releases](../../../partners/testing-releases.md))?

**Continuity**
- What happens if the supplier stops trading? Do you hold the code, backups and keys
  ([Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md))?
- Will the supplier contribute fixes upstream ([Contributing code back](../../../partners/contributing-code-back.md)),
  so you do not depend on a private fork?

## A supplier checklist

```text
[ ] Partner level claimed: <level>; verified in the published listing on <date>
[ ] Certifications claimed: <list>; proof received
[ ] Named staff and back-ups; CVs of key staff
[ ] Support offer with severities and response times staffed by the supplier's own people
[ ] Security release patching commitment: within <time> of release
[ ] Hardening plan per site (doc/install/13-security-hardening.md)
[ ] Data processing agreement (if personal data is processed)
[ ] Source code, documentation and credentials handed over at milestones
[ ] Exit assistance clause
[ ] References checked (see Reference checks)
```

## Pitfalls

- Treating a partner logo as a guarantee. The program's own pages note open questions on response-time definitions.
- Accepting response times without asking whether they mean first answer or resolution, business or clock hours.
- A supplier who keeps the only copy of your code or keys.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Partners: start here](../../../partners/partners-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
