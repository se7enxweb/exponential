---
title: Mapping EN 301 549 to Exponential
audience: government, project-manager, developer
category: public-sector
order: 80
summary: For EU public bodies and suppliers - how the clauses of EN 301 549 v3.2.1 that matter for a web CMS (9 web, 11 software and authoring tools, 12 documentation and support) relate to what Exponential documents, with the status Unverified and the work left to you.
keywords: EN 301 549, v3.2.1, ETSI, CEN, CENELEC, clause 9, clause 11, clause 11.8, authoring tool, clause 12, documentation and support, harmonized standard, Web Accessibility Directive, mapping
type: reference
---
# Mapping EN 301 549 to Exponential

This page is for accessibility coordinators, procurement staff and suppliers who must relate a site built with
Exponential to EN 301 549 "Accessibility requirements for ICT products and services", v3.2.1 (2021-03), published by
ETSI, CEN and CENELEC (<https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf>).
The project's status for this standard is **Unverified**
([Accessibility standards: EN 301 549](../../../standards/accessibility.md#en-301-549)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Which clauses matter for a CMS](#which-clauses-matter-for-a-cms)
2. [Clause by clause](#clause-by-clause)
3. [Producing your own evidence](#producing-your-own-evidence)
4. [Related pages](#related-pages)

## Which clauses matter for a CMS

| Clause | Subject | Applies to |
|---|---|---|
| 9 | Web | the public site and the administration (both are web content) |
| 10 | Non-web documents | PDF and office files your site offers for download |
| 11 | Software, including 11.8 authoring tools | the administration and the online editor as an authoring tool |
| 12 | Documentation and support services | the help and documentation you give to staff, and your support channel |

The project's statement names clause 9 (which incorporates WCAG 2.1 AA), clause 11 and clause 12 as relevant to
authoring tools, and clause 11.8 as not reviewed.

## Clause by clause

| Clause | What Exponential documents | Status | What you do |
|---|---|---|---|
| 9 Web (WCAG 2.1 AA) | admin4 / admin4l tested against WCAG 2.2 AA, which contains 2.1 AA except 4.1.1 Parsing | Unverified against the standard (Partly complies against WCAG 2.2) | audit your public design and the administration pages your editors use |
| 10 Non-web documents | nothing specific; files are delivered through `content/download` | not assessed | check documents before publishing (see "Documents and PDFs") |
| 11 Software (the administration as software) | same evidence as clause 9 for admin4 | Unverified | include the editing tasks in your evaluation |
| 11.8 Authoring tools | image fields offer an alternative text field; a class can make it required ("Alternative text is required", since 6.0.15); rich text has headings, table captions and header cells; the optional Exponential Intelligence extension can suggest alternative texts for an editor to review | **not assessed** by the project | evaluate whether the tool prompts authors and keeps the accessibility information they enter |
| 12 Documentation and support | the help system follows the same rules (one `h1`, named landmarks, labeled search, visible focus, announced search results) | not assessed against clause 12 | make your own support channel reachable in accessible ways |

Sources: [Accessibility standards](../../../standards/accessibility.md),
[Help system: Languages and accessibility](../../../features/6.0/help-system.md#languages-and-accessibility),
[Exponential Intelligence](../../../features/6.0/extensions/expai.md).

### A note on clause 11.8 and rich text

Clause 11.8 asks, among other things, that accessibility information entered by an author is preserved in the output.
Facts you can check in the repository and in your audit:

- The rich text renderer of the standard design writes `<caption>`, `scope` and `abbr` on tables and header cells
  when the editor sets them (`design/standard/templates/content/datatype/view/ezxmltags/table.tpl`, `th.tpl`).
- The standard design's image template uses the alternative text the editor entered and, **when it is empty, the
  object's name** (`design/standard/templates/content/datatype/view/ezimage.tpl`). An intentionally empty alternative
  text for a decorative image is therefore not written as `alt=""` by that template; your design must override it if
  you need that behavior.

## Producing your own evidence

1. Define the scope: your version of Exponential, the administration design, the public design, the activated
   extensions.
2. Evaluate clause 9 with WCAG-EM (<https://www.w3.org/TR/WCAG-EM/>) on the public site and on a sample of
   administration pages (dashboard, content tree, edit form, upload, versions).
3. For clause 11.8, list the authoring functions your editors use and check for each whether it helps or hinders
   accessible output.
4. Record the result per clause as "Supports", "Partially supports", "Does not support" or "Not evaluated", with the
   test notes. This is your report, not the project's.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
