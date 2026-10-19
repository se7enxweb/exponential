---
title: "Tender template: accessibility clause"
audience: government, project-manager, partner
category: public-sector
order: 30
summary: Clause text for the accessibility obligations of a CMS contract - WCAG 2.2 AA for the public site and the administration, EN 301 549 and Section 508 references, evaluation before acceptance, accessibility statement and regression testing - with blanks to fill.
keywords: accessibility clause, tender template, WCAG 2.2, AA, EN 301 549, Section 508, VPAT, ACR, WCAG-EM, accessibility statement, contract, procurement
type: help
---
# Tender template: accessibility clause

This page is for procurement officers and accessibility leads who put accessibility obligations into a CMS contract.
The clause separates the public site, the administration and authoring support, as explained in
[Accessibility requirements](../requirements/accessibility-requirements.md), and asks for evidence that can be
checked.

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [Choosing the standard references](#choosing-the-standard-references)
3. [What an Exponential-based bid can show](#what-an-exponential-based-bid-can-show)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
X. ACCESSIBILITY

X.1 Standard. In this contract "the Standard" means the Web Content Accessibility
    Guidelines (WCAG) 2.2, level AA, published by the W3C, [and EN 301 549 v3.2.1]
    [and the Revised Section 508 Standards], as applicable under <applicable rules>.

X.2 Public website. The Supplier shall deliver the public website so that the pages
    and templates listed in <annex> meet the Standard. Before acceptance the Supplier
    shall deliver an evaluation report following the W3C Website Accessibility
    Conformance Evaluation Methodology (WCAG-EM), listing every failure found and
    its correction.

X.3 Administration. The Supplier shall state, per success criterion of the Standard,
    the conformance status of the administration and editing interface used by the
    Customer's staff, with the evidence for each status. Where a criterion is not
    met, the Supplier shall state the workaround and the plan, if any.

X.4 Authoring support. The Supplier shall configure the content types so that
    images carry a required alternative text field where <Customer> decides, and
    shall train editors in accessible content as set out in <training annex>.

X.5 Conformance reports. The Supplier shall deliver any existing conformance report
    for the software (for example an Accessibility Conformance Report) or state in
    writing that none exists.

X.6 Accessibility statement. The Supplier shall deliver a draft accessibility
    statement for the public website in the form required by <applicable rules>,
    based on the evaluation in X.2.

X.7 Regression. Each release delivered under this contract shall not reduce
    conformance. The Supplier shall describe its regression tests and run them
    before every release to production.

X.8 Defects. A failure of the Standard on a page in scope is a defect under
    <defect clause> with severity <level>.
```

## Choosing the standard references

| Reference | When to include | Link |
|---|---|---|
| WCAG 2.2 AA | always; it is the current W3C Recommendation | [WCAG 2.2](https://www.w3.org/TR/WCAG22/) |
| EN 301 549 v3.2.1 | public bodies in the EU (Web Accessibility Directive) | [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf), [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) |
| Revised Section 508 Standards | US federal agencies | [Section 508](https://www.section508.gov/), [US Access Board](https://www.access-board.gov/ict/) |
| WCAG-EM | for the evaluation report in X.2 | [WCAG-EM](https://www.w3.org/TR/WCAG-EM/) |
| Accessibility statement guidance | for X.6 | [W3C WAI statements](https://www.w3.org/WAI/planning/statements/) |

## What an Exponential-based bid can show

From [Accessibility standards](../../../../standards/accessibility.md):

- **X.3**: the admin4 and admin4l administration is built and regression-tested against WCAG 2.2 AA criteria;
  status **Partly complies**. Classic admin, admin3 and the online editor are not fully covered.
- **X.2**: public designs are **not audited**; the evaluation is the bidder's work on the design it delivers
  ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)).
- **X.5**: there is **no VPAT or Accessibility Conformance Report**; EN 301 549 and Section 508 are **Unverified**.
- **X.6**: there is no accessibility statement template in the product; the bidder writes it.
- **X.7**: design tests in `tests/tests/design/` run in CI for admin4; axe runs are manual.

## Notes for the evaluator

- X.5 deliberately accepts "none exists"; scoring it pass/fail would exclude most open source bids.
- Score X.2 on the method and the team's experience, since the report only exists after the build.
- Ask for a keyboard-only and screen reader walkthrough of the editor in the demonstration.

## Related pages
- [Tender templates](README.md)
- [Accessibility requirements](../requirements/accessibility-requirements.md)
- [Acceptance testing clause](acceptance-testing-clause.md)
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
