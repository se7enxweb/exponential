---
title: Procuring Exponential in the public sector
audience: government, project-manager, sales-marketing, partner
category: public-sector
order: 10
summary: The procurement section for public bodies that buy a content management system, services around Exponential or both - requirements, tender templates, evaluation, total cost of ownership, exit plans, training and the product lifecycle, with what the repository can evidence and what it cannot.
keywords: procurement, tender, RFP, request for proposal, requirements, evaluation, scoring, total cost of ownership, TCO, exit plan, lock-in, training, lifecycle, public sector, government, buying a CMS
type: help
---
# Procuring Exponential in the public sector

This section is for procurement officers, project managers and the specialists who sit on an evaluation panel in a
public body: a ministry, an agency, a council, a school board. It answers the questions a public procurement of a web
content management system raises, from the first requirement to the end of the contract, and it says for each one
what Exponential's own documentation can prove. The facts come from the standards pages in `doc/standards/`, the
installation book in `doc/install/`, the partner and hosting sections of this help, and `SECURITY.md`.

> **Note** This section describes features and common practice; it is not legal advice. Public procurement is
> governed by the law and the rules of your organization. Check them with your procurement and legal counsel.

## Contents

1. [What you are buying](#what-you-are-buying)
2. [The parts of this section](#the-parts-of-this-section)
3. [Reading order](#reading-order)
4. [What the repository can and cannot evidence](#what-the-repository-can-and-cannot-evidence)
5. [Related pages](#related-pages)

## What you are buying

Exponential itself is free software under the GNU General Public License, version 2 or any later version
([Licensing questions](../../project/licensing-faq.md)). There is no license fee to compare. What a public body
procures is everything around it:

| You may procure | From whom | Where this help describes it |
|---|---|---|
| Implementation: content model, design, templates, migration | an implementation partner or agency | [Implementation partners](../../partners/implementation-partners.md) |
| Hosting and operations | a hosting company, or your own data center | [Hosting companies: start here](../../hosting/hosting-companies-start-here.md) |
| Support with agreed response times | 7x or a partner | [Getting support](../../project/getting-support.md), [Support levels and response times](../../partners/support-levels-and-response-times.md) |
| Training for editors and administrators | a training partner or your own trainers | [Course catalogue](../../trainers/course-catalogue.md) |
| Accessibility or security assessments | an independent assessor | [Accessibility standards](../../../standards/accessibility.md), [Security standards](../../../standards/security.md) |

Because the software is the same whoever supplies the services, a public body can change its supplier without
changing its system. That is the strongest argument against lock-in, and the exit-plans part of this section shows
how to keep it true in the contract.

## The parts of this section

| Part | It answers |
|---|---|
| [Requirements](requirements/README.md) | How to write verifiable requirements for a public CMS, and which evidence the repository provides for each |
| [Tender templates](tender-templates/README.md) | Clause text for a tender or request for proposal, with placeholders to adapt |
| [Evaluation](evaluation/README.md) | Checklists, scoring sheets, demo scripts, a proof-of-concept plan and reference checks |
| [Total cost of ownership](total-cost-of-ownership/README.md) | A cost model with categories and worked examples whose figures are all labeled example assumptions |
| [Exit plans](exit-plans/README.md) | How to plan the end of a contract: export formats, handover, transition and clauses against lock-in |
| [Training](training/README.md) | A training plan for a public body, built on the trainer courses of this help |
| [Lifecycle](lifecycle/README.md) | Release policy, supported versions, PHP support and how to plan upgrades in a budget |

## Reading order

1. Start with [Requirements](requirements/README.md): a tender is only as good as its requirements.
2. Turn them into clauses with the [Tender templates](tender-templates/README.md).
3. Prepare the panel with [Evaluation](evaluation/README.md) before the bids arrive.
4. Compare bids on [Total cost of ownership](total-cost-of-ownership/README.md), not on the first-year price.
5. Write the [Exit plan](exit-plans/README.md) before you sign, not when the contract ends.
6. Budget [Training](training/README.md) and the [Lifecycle](lifecycle/README.md) of the product for the whole term.

The checklists "Before a tender" and "Before signing" in the government checklists section sum up each stage on one
page.

## What the repository can and cannot evidence

Evaluation panels often ask for documents that a commercial vendor produces. The Exponential project publishes its
evidence in the repository instead, and says plainly where something is missing:

| Question | What exists | What does not exist |
|---|---|---|
| Accessibility | [Accessibility standards](../../../standards/accessibility.md): WCAG 2.2 AA **Partly complies** for the admin4 and admin4l administration; EN 301 549 and Section 508 **Unverified** | No VPAT or Accessibility Conformance Report; public site designs not audited; no accessibility statement template |
| Security | [Security standards](../../../standards/security.md): OWASP Top 10 mapping, three documented hardening passes, tamper-evident audit trail | No OWASP ASVS assessment; no external penetration test report in the repository; no `security.txt` shipped |
| Privacy | [Privacy standards](../../../standards/privacy.md): consent records, erasure on account removal, log masking and retention | No single "export all my data" function; no cookie consent banner |
| Vulnerability handling | [`SECURITY.md`](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md): private reporting, first answer within one business day | No published fix-time commitment; fixes arrive as the next release |
| Release policy | [Source control and releases](../../../standards/source-control-and-releases.md): permanent tags, changelogs | Commits and tags are not signed |
| Service levels | none from the project; partners and hosts publish their own | The project publishes no SLA ([SLA options for hosting](../../hosting/business/sla-options.md)) |

Ask bidders to supply the missing pieces as part of their offer (for example an accessibility audit of the site they
build) rather than expecting them from the open source project.

## Related pages

- Government: start here (the landing page of this help section)
- [Licensing questions: the GPL and Exponential](../../project/licensing-faq.md)
- [Who is 7x?](../../who-is-7x.md) and [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md)
- [Partners: start here](../../partners/partners-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
