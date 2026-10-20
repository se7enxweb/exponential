---
title: Write an accessibility requirement for a tender
audience: government, project-manager, sales-marketing
category: public-sector
order: 20
summary: Step by step, write an accessibility requirement for a tender for an Exponential site that names the standard and level, separates the administration from the public site, asks for evidence that exists and puts testing and fixing on the supplier.
keywords: accessibility requirement, tender, WCAG 2.2 AA, EN 301 549, Section 508, evidence, acceptance, VPAT, public site, administration, procurement task
type: help
---
# Write an accessibility requirement for a tender

**Who:** the procurement officer or project manager, with the accessibility lead. **When:** while drafting the
requirements, before the tender is published. The facts come from [Accessibility standards](../../../../standards/accessibility.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the accessibility law that
> applies to your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Example](#example)
4. [Result](#result)
5. [Pitfalls](#pitfalls)

## Before you start

- Know which law applies to you: in the EU the [Web Accessibility Directive](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
  with [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf); in the
  US federal government [Section 508](https://www.section508.gov/).
- Read the summary table of [Accessibility standards](../../../../standards/accessibility.md#summary): admin4 **Partly
  complies** with WCAG 2.2 AA; EN 301 549 and Section 508 **Unverified**; no VPAT.

## Steps

1. **Name the standard and level**: WCAG 2.2 level AA ([WCAG 2.2](https://www.w3.org/TR/WCAG22/)), plus EN 301 549 or
   Section 508 where your law requires them.
2. **Split the scope** into two parts, because the evidence differs:
   - the **public site** the supplier builds (design, templates, forms);
   - the **administration** your staff use.
3. **For the public site, make the supplier responsible**: the project's public designs are not audited, so the
   delivered design must be tested and fixed by the supplier ([Accessibility standards](../../../../standards/accessibility.md#known-gaps)).
4. **For the administration, ask what exists**: which design they propose (admin4 is the tested one), and how they handle
   the known gaps such as the online editor ([The admin4 design](../../../../features/6.0/admin4-design.md)).
5. **Ask for evidence you can verify**: a test report by a named method (for example [WCAG-EM](https://www.w3.org/TR/WCAG-EM/)),
   not a statement of intent. Do not require a VPAT for the product unless you accept that none exists; ask the bidder to
   produce an evaluation of the delivered site instead.
6. **Put the statement on the list**: the supplier drafts your accessibility statement; Exponential ships no template.
7. **Tie it to acceptance**: the requirement is met when the agreed test passes ([Write the acceptance test plan](write-the-acceptance-test-plan.md)).

## Example

```text
ACC-01  The public website delivered under this contract shall meet WCAG 2.2 level AA
        [and EN 301 549 v3.2.1 clause 9]. The Supplier shall evaluate <list of pages and templates>
        with <method, e.g. WCAG-EM> before acceptance and deliver the report. Failures shall be fixed
        at the Supplier's cost before acceptance.
ACC-02  The Supplier shall state which administration design it proposes for staff, which known
        accessibility gaps of that design and of the online editor remain, and how it mitigates them.
ACC-03  The Supplier shall draft the accessibility statement of the website for <body> to approve.
```

The tender templates part of the procurement section has the full clause ([Accessibility clause](../../procurement/tender-templates/accessibility-clause.md)).

## Result

A requirement that a bidder can answer honestly and that you can test at acceptance.

## Pitfalls

- Asking the open source project for a conformance claim it does not make.
- One requirement for "the system" that mixes the administration and the public site.
- No method and no list of pages for testing: then nobody can say whether it is met.

## Related pages

- [Procurement and training tasks](README.md)
- [Accessibility requirements](../../procurement/requirements/accessibility-requirements.md)
- [Accessibility checklist](../../checklists/accessibility-checklist.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
