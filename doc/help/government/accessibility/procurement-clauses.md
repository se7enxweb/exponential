---
title: Procurement clauses for accessibility
audience: government, project-manager, partner
category: public-sector
order: 280
summary: For procurement officers and project managers - sample accessibility requirements, evidence requests and contract clauses for buying design, implementation, hosting or support around Exponential, written so a bidder can answer them honestly from the project's documented status.
keywords: procurement, tender, requirements, contract clauses, accessibility, WCAG 2.2 AA, EN 301 549, Section 508, VPAT, ACR, evidence, acceptance testing, warranty, defects, supplier, public sector
type: help
---
# Procurement clauses for accessibility

This page is for procurement officers, project managers and evaluation panels. It gives sample wording for the
accessibility part of a tender or contract for work around Exponential (design, implementation, hosting, support,
training). Exponential itself is free software without a license fee; what you buy is the work, and accessibility must
be part of it. Adapt the wording with your procurement and legal counsel.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Principles](#principles)
2. [Sample requirements](#sample-requirements)
3. [Sample evidence requests](#sample-evidence-requests)
4. [Sample contract clauses](#sample-contract-clauses)
5. [Evaluating the answers](#evaluating-the-answers)
6. [Related pages](#related-pages)

## Principles

- Ask for outcomes you can test (WCAG 2.2 AA on the delivered site), not for adjectives ("accessible").
- Separate the **software** (the project's statement: admin4 "Partly complies", EN 301 549 and Section 508
  "Unverified", no VPAT; [Accessibility standards](../../../standards/accessibility.md)) from the **deliverable** (your
  design, your configuration, your content model).
- Make acceptance depend on a test, and defects on fixing.

## Sample requirements

| No. | Requirement |
|---|---|
| A1 | The delivered public site meets WCAG 2.2 level AA on all page templates, forms and functions in scope. |
| A2 | The administration is configured with the admin4 (or admin4l) design for all editors. |
| A3 | Image attributes of content classes that editors use for informative images have "Alternative text is required" switched on, unless the contracting authority decides otherwise. |
| A4 | Templates write `lang` (and `dir` where needed) for every language siteaccess. |
| A5 | No template, stylesheet or script is delivered that removes the visible focus indicator without a replacement. |
| A6 | Media templates support captions and transcripts in the way agreed in the design phase. |
| A7 | The supplier delivers editor guidelines covering alternative text, headings, links, tables and documents. |
| A8 | The supplier delivers a list of activated extensions and states for each whether its public screens were tested. |

## Sample evidence requests

- "Describe the accessibility status of the CMS you propose, using the vendor's or project's own words, with a link."
  A good answer about Exponential repeats "Partly complies" and "Unverified" and does not claim a VPAT.
- "Describe your test method (tools, assistive technologies, sample) and give an anonymized example of a test report
  from a previous project."
- "Name the people who will test, and their qualification."

## Sample contract clauses

- **Acceptance**: the contracting authority (or an evaluator it appoints) evaluates the site against WCAG 2.2 AA with
  WCAG-EM before acceptance; findings of severity blocker or major prevent acceptance.
- **Defects**: accessibility failures are defects under the warranty and the support agreement, with the same
  response times as functional defects.
- **Regression**: every release that changes templates includes an automated check and a keyboard pass on the changed
  templates, with the results delivered.
- **Evidence**: the supplier hands over test reports and keeps them current for the term of the contract.
- **Upgrades**: when the supplier upgrades Exponential or an extension, it re-tests the affected pages.

## Evaluating the answers

Score lower an answer that claims full conformance of the CMS or a VPAT for Exponential without evidence: the project
itself makes no such claim. Score higher an answer with a concrete test method, named tools and examples.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Implementation partners](../../partners/implementation-partners.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
