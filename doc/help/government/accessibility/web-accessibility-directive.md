---
title: The EU Web Accessibility Directive and Exponential
audience: government, project-manager, admin
category: public-sector
order: 40
summary: For EU public sector bodies - what the Web Accessibility Directive (EU) 2016/2102 asks of a website in general terms, and which part of each duty Exponential covers, which part your site and your organization cover.
keywords: Web Accessibility Directive, WAD, 2016/2102, public sector bodies, EN 301 549, accessibility statement, feedback mechanism, monitoring, disproportionate burden, EU, member state
type: help
---
# The EU Web Accessibility Directive and Exponential

This page is for public sector bodies in the European Union that run a website with Exponential. It describes the
duties of the Web Accessibility Directive in general terms and, for each one, what the software provides according to
[Accessibility standards](../../../standards/accessibility.md). The binding rules are those of your member state's
transposition.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The directive in brief](#the-directive-in-brief)
2. [Duty by duty](#duty-by-duty)
3. [A working plan](#a-working-plan)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## The directive in brief

Directive (EU) 2016/2102 on the accessibility of the websites and mobile applications of public sector bodies
(official text: <https://eur-lex.europa.eu/eli/dir/2016/2102/oj>) requires member states to make sure that public
sector bodies make their websites and apps perceivable, operable, understandable and robust. The technical reference
is the harmonized standard EN 301 549 (v3.2.1, published by ETSI:
<https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf>), whose web clause
incorporates WCAG 2.1 level AA. The directive also asks for an accessibility statement, a feedback mechanism, and
monitoring and reporting by the member states.

## Duty by duty

| Duty (general terms) | What Exponential provides | What you do |
|---|---|---|
| Accessible website content (EN 301 549 / WCAG 2.1 AA) | the editing tool (admin4) is tested against WCAG 2.2 AA, a superset of 2.1 AA except 4.1.1 Parsing; status **Partly complies**; EN 301 549 **Unverified** | build or buy an accessible public design and audit it; WCAG 2.2 AA is a safe target because it contains 2.1 AA |
| Accessible content published by staff | an alternative text field on images that a class can make required; headings, table captions and header cells in rich text | train editors; require alternative text on image fields; check documents before upload |
| Accessibility statement | **no template ships** (named as a gap in the project's statement) | write it from the model your member state publishes; see "Writing an accessibility statement" in this section |
| Feedback mechanism | contact forms can be built with the information collector ([Collected information](../../../guides/collected-information.md)) | add a contact link to the statement; answer within the time your law sets |
| Disproportionate burden (where your law allows it) | nothing in the software | document the assessment yourself; it is an organizational decision |
| Monitoring by the member state | nothing in the software | keep audit reports and fix lists ready for the monitoring body |

## A working plan

1. **Set the target.** WCAG 2.2 AA for new work; it covers what EN 301 549 clause 9 asks.
2. **Choose the administration design.** Keep editors on admin4 (or admin4l), the design the project tests
   ([The admin4 design](../../../features/6.0/admin4-design.md)).
3. **Audit the public design** with a representative sample, as WCAG-EM describes
   (<https://www.w3.org/TR/WCAG-EM/>). Record findings with the success criterion and the template to fix.
4. **Fix template issues first**: one template fix repairs every page that uses it.
5. **Make alternative text required** on the image attributes of your content classes (class edit, option
   "Alternative text is required").
6. **Publish the statement** as a normal content page, linked from every page footer.
7. **Repeat** the audit after redesigns and at the interval your monitoring body expects.

## Pitfalls

- **Declaring the site compliant because the CMS is "accessible".** The project's own statement rates the
  administration "Partly complies" and the public designs "not audited". Your statement is about your site.
- **Forgetting documents.** PDF and office files linked from pages are content too; see "Documents and PDFs" in this
  section.
- **Leaving editors without guidance.** Most regressions after launch come from content, not templates.
- **Online editor output.** The TinyMCE integrations were not audited (status **Unverified**); check the HTML your
  editors' rich text produces in your audit sample.

## Related pages

- [Accessibility standards: EN 301 549](../../../standards/accessibility.md#en-301-549)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- W3C WAI, [Developing an accessibility statement](https://www.w3.org/WAI/planning/statements/)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
