---
title: What Exponential does not cover for accessibility
audience: government, project-manager, admin, developer
category: public-sector
order: 100
summary: The known accessibility gaps the project names itself - public designs not audited, classic administration and admin3 partly fixed, online editor unverified, extensions without an overall statement, no automated engine in CI, no conformance report, content left to editors - and how a public body plans around each.
keywords: accessibility gaps, known gaps, not audited, public designs, classic admin, admin3, online editor, TinyMCE, extensions, axe, CI, VPAT, ACR, conformance report, risk register, plan
type: help
---
# What Exponential does not cover for accessibility

This page is for project managers and accessibility coordinators who keep a risk register or an accessibility plan.
It lists the gaps that the project's own statement names ([Accessibility standards: Known gaps](../../../standards/accessibility.md#known-gaps))
and, for each, what a public body can do about it. Saying where the limits are is part of an honest accessibility
statement.

## Contents

1. [The gaps at a glance](#the-gaps-at-a-glance)
2. [Planning around each gap](#planning-around-each-gap)
3. [A risk register entry, as an example](#a-risk-register-entry-as-an-example)
4. [Related pages](#related-pages)

## The gaps at a glance

| Gap (from the project's statement) | Effect on a public body |
|---|---|
| Public site designs (`design/standard`, `design/base`, `design/plain`, `design/mysite`, the packaged site designs) are not audited | the pages the public sees have no project evidence; you evaluate them |
| Classic administration (`design/admin`) and admin3 are not covered by the contrast, landmark and form-error tests | staff who use them work in an environment with less evidence |
| The online editor (TinyMCE 8 and TinyMCE 3 in `extension/ezoe`) was not audited; status **Unverified** | the rich text editing experience and its output need your own check |
| Extensions shipped as separate packages (layouts editor, newsletter, shop) are reviewed one by one; no overall statement | each extension you activate needs a look |
| No automated accessibility engine in CI; the tests read sources and do not render pages; axe runs are manual | regressions between releases are caught only by the source tests and manual reviews |
| No published conformance report (WCAG-EM evaluation, VPAT or ACR) | you cannot cite one in a tender or a statement |
| Alternative text, heading order and link text depend on editors; nothing forces editors to fill the image alternative text field by default | content quality is an organizational task |
| No accessibility statement template for sites | you write your own |
| EN 301 549 clause 11.8 (authoring tool support) not assessed | you assess the authoring support yourself if you need it |

## Planning around each gap

**Public designs.** Budget an audit of your design before launch and after each redesign. Prefer building on a
design whose templates you control, and give your designer the checklist in
[Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md).

**Classic administration and admin3.** Set `SiteDesign=admin4` on the administration siteaccess and limit the design
switch to admin4 and admin4l where you can ([The administration design switch](../../../guides/ui-switch-design.md)).

**Online editor.** Include the editor in your evaluation: insert a heading, a list, a table with a header row and a
caption, a link and an image with alternative text, using the keyboard only; then inspect the published HTML.
TinyMCE 3 is the default engine; TinyMCE 8 is opt-in per user or per siteaccess
([The online editor on TinyMCE 8](../../../features/6.0/online-editor-tinymce8.md)). Evaluate the engine you deploy.

**Extensions.** Keep a list of activated extensions (Setup > Extensions) and test the screens each one adds. Look for
the extension's entries in the [6.0.15 changelog](../../../../../changelogs/6.0/6.0.15.md) before you rely on it.

**No CI engine.** Add an automated check of your own public pages to your release routine (an axe-based tool on a
fixed list of URLs) and a keyboard pass for every release; see "Testing with axe and the keyboard" in this section.

**No conformance report.** Commission or write your own, scoped to your configuration; see "Preparing an
Accessibility Conformance Report for your site" in this section.

**Content.** Make alternative text required on image attributes (class edit, "Alternative text is required"), train
editors, and spot-check content each month.

## A risk register entry, as an example

| Field | Example entry |
|---|---|
| Risk | Public pages built on our own design may not meet WCAG 2.2 AA |
| Source | Accessibility standards, Known gaps: public designs not audited |
| Likelihood / impact | to be rated by your organization |
| Measures | audit before launch (WCAG-EM sample), fixes in templates, re-audit after redesigns, editor training, monthly content spot checks |
| Owner | `<accessibility coordinator>` |
| Evidence kept | audit report, fix list with dates, statement history |

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
