---
title: Preparing an Accessibility Conformance Report for your site
audience: government, project-manager, developer
category: public-sector
order: 140
summary: For public bodies and suppliers asked for a VPAT or Accessibility Conformance Report - Exponential has none, so this page explains how to scope, test and write an honest ACR for the configuration you actually deliver.
keywords: ACR, Accessibility Conformance Report, VPAT, Voluntary Product Accessibility Template, conformance level, supports, partially supports, does not support, not evaluated, evidence, tender, procurement, scope
type: help
---
# Preparing an Accessibility Conformance Report for your site

This page is for project managers and suppliers who are asked in a tender or by a buyer for an Accessibility
Conformance Report (ACR), often in the form of a VPAT. There is **no VPAT and no ACR for Exponential**
([Accessibility standards: Section 508](../../../standards/accessibility.md#section-508)). What you can produce is a
report for **your configuration**: a given Exponential version, administration design, public design and set of
extensions. This page shows how.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Scope the product](#scope-the-product)
2. [Choose the standards](#choose-the-standards)
3. [Test](#test)
4. [Write each row](#write-each-row)
5. [What you may reuse from the project](#what-you-may-reuse-from-the-project)
6. [Related pages](#related-pages)

## Scope the product

Write the scope on the first page of the report:

| Item | Where to read it |
|---|---|
| Exponential version | Setup > System information, or `composer show se7enxweb/exponential` |
| Administration design | the admin siteaccess's `[DesignSettings] SiteDesign` (admin4 recommended) |
| Online editor engine | `ezoe.ini [EditorSettings] EditorEngine` (TinyMCE 3 by default, TinyMCE 8 opt-in) |
| Public design | your design and its extension |
| Activated extensions | Setup > Extensions |
| Browsers and assistive technologies tested | your test plan |

Anything outside the scope is "Not evaluated" in the report.

## Choose the standards

Ask the buyer which edition they expect: WCAG 2.x (A and AA), Revised Section 508, EN 301 549, or all. The VPAT
template editions are published by the ITI, outside this project; use the one the buyer names.

## Test

1. **Public site**: evaluate with WCAG-EM (see "Auditing your public design with WCAG-EM").
2. **Administration**: choose editor tasks (sign in, find content, create and publish an article with an image,
   upload a file, read versions, sign out) and test them with the keyboard, a screen reader, 200% zoom and forced
   colors.
3. **Online editor**: test separately (see "The online editor and accessibility").
4. Keep test notes per criterion: date, page or task, tool, result.

## Write each row

| Conformance level | Use it when |
|---|---|
| Supports | every tested page and task meets the criterion |
| Partially supports | some do, some do not; name which |
| Does not support | most do not |
| Not applicable | the criterion cannot occur (for example no audio on the site) |
| Not evaluated | you did not test it; never guess |

In the remarks, say where a limitation comes from (your template, a content item, an extension, the online editor)
and what the workaround or plan is.

## What you may reuse from the project

You may cite, with the link, the project's own evidence for the administration:

- the status words of [Accessibility standards](../../../standards/accessibility.md) (Partly complies, Unverified);
- the list of design tests and what they hold (form labels, landmarks, keyboard patterns, contrast tokens, error
  summaries);
- the manual axe results recorded in the [6.0.15 changelog](../../../../../changelogs/6.0/6.0.15.md).

Do not turn "Partly complies" into "Supports". The project's tests read template sources; they are evidence for your
remarks, not a substitute for your test.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [System information](../../../guides/system-information.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
