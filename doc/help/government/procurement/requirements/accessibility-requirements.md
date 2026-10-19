---
title: Accessibility requirements
audience: government, project-manager, admin, partner
category: public-sector
order: 40
summary: How a public body writes accessibility requirements for a CMS tender - WCAG 2.2 AA, EN 301 549, Section 508, the public site versus the administration, authoring support and conformance evidence - and what Exponential's documented status is for each.
keywords: accessibility, requirements, WCAG 2.2, AA, EN 301 549, Section 508, VPAT, ACR, conformance report, accessibility statement, authoring tool, public sector, tender
type: help
---
# Accessibility requirements

This page is for procurement teams and accessibility leads who must write the accessibility part of a CMS tender.
It explains which parts of a CMS an accessibility requirement has to cover, offers requirement wording, and states
what Exponential documents for each, using only the status words of
[Accessibility standards](../../../../standards/accessibility.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The legal and standards background](#the-legal-and-standards-background)
2. [Three things an accessibility requirement must cover](#three-things-an-accessibility-requirement-must-cover)
3. [Requirement wording](#requirement-wording)
4. [Exponential's documented status](#exponentials-documented-status)
5. [Evidence to ask for](#evidence-to-ask-for)
6. [Pitfalls](#pitfalls)

## The legal and standards background

- **WCAG 2.2** ([W3C Recommendation](https://www.w3.org/TR/WCAG22/)) is the technical standard most laws and
  procurement rules point to; level AA is the usual target.
- **In the EU**, the Web Accessibility Directive
  ([Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)) requires public sector bodies to make
  their websites accessible and to publish an accessibility statement. Its harmonized standard is
  [EN 301 549 v3.2.1](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf),
  whose web clause incorporates WCAG 2.1 AA.
- **In the United States**, [Section 508](https://www.section508.gov/) requires federal agencies to procure accessible
  ICT; the [Revised 508 Standards](https://www.access-board.gov/ict/) incorporate WCAG 2.0 AA by reference.
- How a conformance claim is evaluated is described in [WCAG-EM](https://www.w3.org/TR/WCAG-EM/).

## Three things an accessibility requirement must cover

| Part | Why it matters | Who is responsible |
|---|---|---|
| **The public website** (what visitors see) | This is what the law measures. It depends on the design, the templates and the content. | The bidder who builds the design, and your editors |
| **The administration and editor** (what staff use) | Staff with disabilities must be able to publish. Many laws treat staff tools as ICT too. | The software and the bidder |
| **Authoring support** (does the tool help editors publish accessible content?) | EN 301 549 clause 11.8 covers authoring tools: prompts for alternative text, accessible output. | The software |

A requirement that only says "the CMS is WCAG compliant" mixes all three and cannot be scored.

## Requirement wording

```text
ACC-01 (Must)  The public website delivered under this contract meets WCAG 2.2 level AA on the
               pages listed in <annex>. The supplier provides an evaluation following WCAG-EM
               before acceptance.
ACC-02 (Must)  The administration and editing interface used by staff meets WCAG 2.2 level AA.
               State the conformance status per success criterion and the evidence.
ACC-03 (Should) The editing tool supports authors in producing accessible content
               (EN 301 549 clause 11.8): describe alternative text fields, heading structure,
               link text and table markup in the editor.
ACC-04 (Should) The supplier provides a conformance report (for example an Accessibility
               Conformance Report) or states that none exists and how conformance is shown instead.
ACC-05 (Must)  The supplier delivers a draft accessibility statement for the public website
               in the form required by <applicable rules>.
ACC-06 (Should) Accessibility is regression-tested; describe the tests and how often they run.
```

## Exponential's documented status

From [Accessibility standards](../../../../standards/accessibility.md):

| Standard | Status | What it means for your tender |
|---|---|---|
| WCAG 2.2 AA | **Partly complies** | The admin4 and admin4l administration designs are built and regression-tested against AA criteria (labels, landmarks, contrast in light and dark mode, keyboard, target size). Public site designs are **not audited**; classic admin, admin3 and the online editor are not fully covered. |
| WAI-ARIA 1.2 | **Partly complies** | Tabs, disclosure, menu patterns in admin4, checked on template sources. |
| EN 301 549 v3.2.1 | **Unverified** | No evaluation against the standard; clause 11.8 (authoring tools) not reviewed. |
| Section 508 | **Unverified** | No VPAT or Accessibility Conformance Report exists. |

Other documented gaps: no accessibility statement template for sites; no automated accessibility engine in CI (axe
runs by hand); alternative text depends on editors, and nothing forces them to fill it.

So for Exponential, ACC-02 can be answered with evidence for admin4 and admin4l, ACC-01 is the work of whoever builds
the public design ([Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md),
[Testing accessibility](../../../creators/website-creators/testing-accessibility.md)), and ACC-04 is answered with
"none exists".

## Evidence to ask for

- Test names and where they run (Exponential: `tests/tests/design/`, the `design` test suite).
- Results of manual checks with assistive technology, with dates and versions, if any.
- An evaluation report of the delivered public site, following WCAG-EM, before acceptance.
- The list of known gaps and a plan for each.

## Pitfalls

- Accepting "fully compliant" without a report. Score evidence, not adjectives.
- Requiring a VPAT as a pass/fail criterion when the market you want to reach rarely has one; make it scored, and ask
  for an equivalent evaluation instead.
- Forgetting content: no software makes editors write good alternative text. Plan training
  ([Editors 201, quality and accessibility](../../../trainers/editors-201/module-08-quality-and-accessibility/README.md)).
- Forgetting documents: PDFs produced by the software are not tagged (PDF/UA **Does not comply** in
  [Documentation and publishing standards](../../../../standards/documentation-and-publishing.md)).

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [What Exponential can and cannot evidence](what-exponential-can-and-cannot-evidence.md)
- [Tender template: accessibility clause](../tender-templates/accessibility-clause.md)
- [The admin4 design](../../../../features/6.0/admin4-design.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
