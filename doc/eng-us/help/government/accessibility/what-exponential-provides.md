---
title: What Exponential provides for accessibility
audience: government, project-manager, admin, developer
category: public-sector
order: 20
summary: For public bodies that must answer "is Exponential accessible?" - the status of WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 and Section 508 as the project documents it, what is tested, and what stays your responsibility.
keywords: accessibility, a11y, WCAG 2.2, AA, EN 301 549, Section 508, WAI-ARIA, status, partly complies, unverified, admin4, public sector, government, conformance
type: reference
---
# What Exponential provides for accessibility

This page is for project managers, accessibility coordinators and administrators in a public body who must state
how far Exponential supports their accessibility obligations. It summarizes the project's own statement,
[Accessibility standards](../../../standards/accessibility.md), keeps its status words exactly as written there, and
says what that means for a site you build and run.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The status in one table](#the-status-in-one-table)
2. [What "Partly complies" and "Unverified" mean](#what-partly-complies-and-unverified-mean)
3. [What is tested, and how](#what-is-tested-and-how)
4. [Software versus site](#software-versus-site)
5. [What to tell a procurement panel or an auditor](#what-to-tell-a-procurement-panel-or-an-auditor)
6. [Related pages](#related-pages)

## The status in one table

The table repeats the summary of [Accessibility standards](../../../standards/accessibility.md#summary). Do not
upgrade these words in your own documents.

| Standard | Version | Status in the project's statement | Scope of the evidence |
|---|---|---|---|
| WCAG | 2.2, level AA | **Partly complies** | the admin4 and admin4l administration designs, built and regression-tested against AA criteria; classic admin and the standard edit form partly fixed |
| WAI-ARIA and the ARIA Authoring Practices | WAI-ARIA 1.2 | **Partly complies** | landmarks, tabs, tree, disclosure and menu patterns in admin4 |
| EN 301 549 | v3.2.1 (2021-03) | **Unverified** | no conformance report against the standard |
| Section 508 (Revised 508 Standards) | 2017 refresh | **Unverified** | no VPAT and no Accessibility Conformance Report |

## What "Partly complies" and "Unverified" mean

- **Partly complies** means: the project designed the administration against the WCAG 2.2 AA criteria and holds the
  results with automated tests, but there is no full audit of every page, no assistive technology test log, and
  several parts were not reviewed. The statement says plainly that this "is not the same as a conformance claim".
- **Unverified** means: nobody has evaluated Exponential against that standard. The WCAG work may cover much of it
  in substance, but there is no evidence you could hand to an auditor.
- There is **no VPAT, no Accessibility Conformance Report (ACR) and no WCAG-EM evaluation report** for Exponential.
  If a tender asks for one, say so; see "Preparing an Accessibility Conformance Report for your site" in this
  section for what you can produce yourself.

## What is tested, and how

The design tests in `tests/tests/design/` run without a database in the `design` test suite of `phpunit.xml`. They
read template and stylesheet sources; they do not render pages in a browser. Among them:

| Test | Holds |
|---|---|
| `AdminFormLabelsTest.php` | every form control in admin4 templates has an accessible name |
| `AdminLandmarksTest.php` | skip link, banner, navigation, main and footer landmarks, `scope="col"` |
| `AdminFormErrorsTest.php` | error summary that takes the focus, `aria-invalid`, `aria-describedby` |
| `AdminTokenContrastTest.php` | color tokens: 4.5:1 for text, 3:1 for focus ring and field edges, light and dark mode |
| `AdminKeyboardTest.php`, `AdminContextMenuTest.php` | tabs pattern, tree toggle, context menus from the keyboard |
| `AdminResponsiveTest.php` | admin4 at phone, tablet and desktop widths |
| `AdminRightToLeftTest.php` | `dir="rtl"` and isolated names in right-to-left interface languages |

Automated checks with the axe engine are run by hand during reviews, **not in CI**; their results are recorded in
the [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md). Developers can run the suite with:

```bash
php vendor/bin/phpunit --testsuite design
```

## Software versus site

The statement opens with the key point: whether a *site* built with Exponential is accessible also depends on its
own design, templates and content. In practice:

| Part | Who answers for it | Status |
|---|---|---|
| admin4 / admin4l administration (editors' tool) | the Exponential project | Partly complies (WCAG 2.2 AA) |
| Classic administration (`design/admin`), admin3 | the Exponential project | partly fixed, not covered by the contrast, landmark and form-error tests |
| Online editor (TinyMCE 8 and 3 in `extension/ezoe`) | TinyMCE project and Exponential | Unverified |
| Public designs (`design/standard`, packaged site designs) | **you**, the site builder | not audited |
| Content: alternative text, headings, links, documents | **your editors** | depends on them |
| Extensions shipped as separate packages | each extension | reviewed one by one, no overall statement |

## What to tell a procurement panel or an auditor

A fair, defensible answer, in your own words:

1. The editors' administration (admin4) is designed and regression-tested against WCAG 2.2 AA; the project rates it
   "Partly complies" and makes no conformance claim.
2. EN 301 549 and Section 508 conformance is "Unverified"; there is no VPAT or ACR.
3. The public site will be evaluated by us (or our supplier) with WCAG-EM, and we will publish an accessibility
   statement for it.
4. Gaps we know of are listed in the project's statement, and we track them in our own plan.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md), the source of every status on this page
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
