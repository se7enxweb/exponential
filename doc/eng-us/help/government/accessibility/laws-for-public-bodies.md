---
title: What accessibility law asks of public bodies
audience: government, project-manager, admin
category: public-sector
order: 30
summary: An overview, for public bodies in the EU and the United States, of the accessibility rules that usually apply to a website and its editing tool, and how each one meets what Exponential documents.
keywords: accessibility law, Web Accessibility Directive, 2016/2102, European Accessibility Act, 2019/882, Section 508, Rehabilitation Act, EN 301 549, WCAG, public sector, obligations, accessibility statement, procurement
type: help
---
# What accessibility law asks of public bodies

This page is for project managers, legal and procurement staff and accessibility coordinators in a public body. It
gives a plain overview of the accessibility rules that most often apply to a public website, names the official
texts, and points to the pages of this section that deal with each obligation. Facts about Exponential come from
[Accessibility standards](../../../standards/accessibility.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Three families of rules](#three-families-of-rules)
2. [What the rules usually ask for](#what-the-rules-usually-ask-for)
3. [Where Exponential stands](#where-exponential-stands)
4. [Who in your organization does what](#who-in-your-organization-does-what)
5. [Related pages](#related-pages)

## Three families of rules

| Rule | Who it addresses | Technical reference | Official text |
|---|---|---|---|
| EU Web Accessibility Directive, Directive (EU) 2016/2102 | websites and mobile apps of public sector bodies in the EU (as transposed by each member state) | EN 301 549, whose web clause incorporates WCAG 2.1 AA | <https://eur-lex.europa.eu/eli/dir/2016/2102/oj> |
| European Accessibility Act, Directive (EU) 2019/882 | certain products and services placed on the EU market (as transposed by each member state) | EN 301 549 is the reference named in the project's statement | <https://eur-lex.europa.eu/eli/dir/2019/882/oj> |
| Section 508 of the US Rehabilitation Act, Revised 508 Standards | US federal agencies when they develop, procure, maintain or use ICT | WCAG 2.0 AA, incorporated by reference | <https://www.section508.gov/>, <https://www.access-board.gov/ict/> |

Many countries, states and provinces have rules of their own that point to WCAG as well. Read the text that binds
your organization; the summaries below are orientation only.

## What the rules usually ask for

Across these texts the same practical duties come back. Each has a page in this section:

| Duty | What it means for a website | Page in this section |
|---|---|---|
| Meet a technical standard | WCAG 2.1 or 2.2 level AA for web content, through EN 301 549 or directly | "Mapping WCAG 2.2 AA to Exponential", "Mapping EN 301 549" |
| Evaluate | an audit of a representative sample of pages, repeated after changes | "Auditing your public design with WCAG-EM" |
| State | a public accessibility statement with the conformance status, known gaps and a contact | "Writing an accessibility statement" |
| Offer feedback | a way for people to report barriers and ask for content in another form | "Monitoring and feedback" |
| Buy accessible ICT | accessibility requirements in tenders and contracts | "Procurement clauses for accessibility" |
| Keep it up | content published by editors stays accessible | "Accessible content for editors" |

## Where Exponential stands

- **The editing tool.** The admin4 and admin4l administration is designed and tested against WCAG 2.2 AA and the
  project rates it **Partly complies**. EN 301 549 and Section 508 are **Unverified**. There is no VPAT or ACR
  ([Accessibility standards](../../../standards/accessibility.md#summary)).
- **The public site.** The designs that render your public pages are not audited by the project
  ([Known gaps](../../../standards/accessibility.md#known-gaps)). Their accessibility is the site builder's
  responsibility, which in a public body means yours or your supplier's.
- **The statement.** Exponential ships no accessibility statement template; the project lists that as a gap against
  the Web Accessibility Directive ([EN 301 549](../../../standards/accessibility.md#en-301-549)).
- **Authoring support.** EN 301 549 clause 11.8 (the tool helps authors produce accessible content) has not been
  reviewed. Helpful features exist: an alternative text field on images, which a class can make required (the option
  "Alternative text is required" on image attributes, since 6.0.15), and headings, table captions and table headers
  in rich text.

## Who in your organization does what

| Role | Typical part |
|---|---|
| Project manager / product owner | sets the target (WCAG 2.2 AA is a common choice), plans audits, owns the statement |
| Procurement | puts the requirements and evidence into tenders and contracts |
| Designer and developer (or supplier) | builds accessible templates, color tokens, forms; fixes findings |
| Editors | write accessible content: alternative text, headings, link text, accessible documents |
| Administrator | configures image fields to require alternative text, keeps the admin on admin4 |
| Accessibility coordinator | runs or commissions audits, answers feedback, keeps the statement current |

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- W3C WAI, [WCAG 2.2](https://www.w3.org/TR/WCAG22/) and [accessibility statements](https://www.w3.org/WAI/planning/statements/)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
