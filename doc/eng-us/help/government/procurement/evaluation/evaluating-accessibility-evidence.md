---
title: Evaluating accessibility evidence in a CMS bid
audience: government, project-manager, partner
category: public-sector
order: 90
summary: How an evaluator judges the accessibility evidence of a bid based on Exponential - what the product documentation states for WCAG 2.2, EN 301 549 and Section 508, what is not evidenced, what the bidder must add for the public site, and how to test it yourself.
keywords: accessibility evaluation, WCAG 2.2, EN 301 549, Section 508, VPAT, ACR, accessibility conformance report, WCAG-EM, accessibility statement, keyboard test, evidence
type: help
---
# Evaluating accessibility evidence in a CMS bid

This page is for the accessibility specialist on an evaluation panel. It separates the accessibility of the
**software** (the administration editors use) from the accessibility of the **site** (the public pages citizens use),
states what the Exponential documentation evidences for each, and lists the tests you can run yourself. The facts
come from [Accessibility standards](../../../../standards/accessibility.md); this page never upgrades its status words.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Two things to evaluate](#two-things-to-evaluate)
2. [What the product documentation evidences](#what-the-product-documentation-evidences)
3. [What is not evidenced](#what-is-not-evidenced)
4. [What to ask the bidder to supply](#what-to-ask-the-bidder-to-supply)
5. [Tests you can run yourself](#tests-you-can-run-yourself)
6. [Scoring guidance](#scoring-guidance)

## Two things to evaluate

| Part | Who uses it | Who makes it accessible |
|---|---|---|
| Administration (admin4, admin4l, classic admin) | your staff | the project, plus your configuration |
| Public site design and templates | citizens | the bidder who builds the design |
| Published content | citizens | your editors (alternative text, headings, link text) |

Laws that apply to public bodies usually concern the public website and documents; staff tools may fall under
workplace or procurement rules. In the EU, the [Web Accessibility Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
covers public sector websites and refers to [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf);
in the United States, [Section 508](https://www.section508.gov/) applies to federal agencies.

## What the product documentation evidences

| Standard | Status in the documentation | Evidence named |
|---|---|---|
| [WCAG 2.2](https://www.w3.org/TR/WCAG22/) level AA | **Partly complies** (admin4 and admin4l designed and regression-tested against AA criteria) | design tests in `tests/tests/design/` for labels, landmarks, errors, contrast in light and dark mode, keyboard, target size |
| [WAI-ARIA 1.2](https://www.w3.org/TR/wai-aria-1.2/) | **Partly complies** for admin4/admin4l | tabs, disclosure, menu patterns tested on template sources |
| EN 301 549 v3.2.1 | **Unverified** | no evaluation against the standard; clause 11.8 (authoring tools) not reviewed |
| Section 508 | **Unverified** | no VPAT or Accessibility Conformance Report |

Manual axe runs and their results are recorded in the [6.0.15 changelog](../../../../../changelogs/6.0/6.0.15.md); axe
does not run in CI.

## What is not evidenced

From the known gaps of [Accessibility standards](../../../../standards/accessibility.md):

- Public site designs (standard, base, plain, the site packages and themes) are **not audited**.
- The classic administration and admin3 are not covered by the contrast, landmark and form-error tests.
- The online editor (TinyMCE integrations) is **Unverified**.
- Extensions shipped separately are reviewed one by one; there is no overall statement.
- No WCAG-EM evaluation, VPAT or Accessibility Conformance Report exists.
- No accessibility statement template ships with the product.
- Nothing forces editors to fill in alternative text.

A bid that claims "Exponential is WCAG compliant" or "has a VPAT" contradicts the product's own documentation.
Record it as an unsupported claim.

## What to ask the bidder to supply

1. An accessibility plan for the public design: the WCAG 2.2 AA criteria it targets and how it tests them
   ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md),
   [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md)).
2. A conformance report for the delivered site, made with a method such as [WCAG-EM](https://www.w3.org/TR/WCAG-EM/),
   before acceptance.
3. A draft accessibility statement for the site, following the
   [W3C WAI guidance on accessibility statements](https://www.w3.org/WAI/planning/statements/).
4. Which administration design your editors will use (admin4 is the one tested), and any extension the bid adds,
   with its own accessibility evidence.
5. Editor training on accessible content ([Editors 201, quality and accessibility](../../../trainers/editors-201/README.md)).

## Tests you can run yourself

| Test | How | Expected for admin4 |
|---|---|---|
| Keyboard only | Tab through the dashboard and an edit form | skip link first, visible focus ring, all actions reachable |
| Zoom | 200% zoom at about 960 px wide | no horizontal scrolling of the page frame |
| Dark mode | switch the color mode | text remains readable |
| Form errors | save an edit form with a required field empty | an error summary that takes focus and links to the field |
| Screen reader | a short session with the reader your staff use | landmarks and labels announced; the documentation states no assistive technology test log exists |
| Public page | the bidder's design, same tests | depends on the bidder's work |

## Scoring guidance

- Score the administration on the documented status plus your own tests.
- Score the public site on the bidder's plan and evidence, not on the product documentation.
- A precise answer that names gaps and how they will be closed is worth more than a broad claim.

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Accessibility standards](../../../../standards/accessibility.md)
- [The admin4 design](../../../../features/6.0/admin4-design.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
