---
title: Section 508 and Exponential
audience: government, project-manager, admin
category: public-sector
order: 60
summary: For US federal agencies and their contractors - what Section 508 and the Revised 508 Standards ask of web content and authoring tools in general terms, the project's status (Unverified, no VPAT), and how to evaluate a site built with Exponential.
keywords: Section 508, Rehabilitation Act, Revised 508 Standards, US Access Board, VPAT, ACR, Accessibility Conformance Report, WCAG 2.0 AA, federal, United States, procurement
type: help
---
# Section 508 and Exponential

This page is for US federal agencies, the contractors who build and host their sites, and procurement staff who must
evaluate an open source CMS. It explains in general terms how Section 508 relates to Exponential and what the
project's accessibility statement says.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Section 508 in brief](#section-508-in-brief)
2. [The project's status](#the-projects-status)
3. [What to evaluate](#what-to-evaluate)
4. [When a VPAT is requested](#when-a-vpat-is-requested)
5. [Related pages](#related-pages)

## Section 508 in brief

Section 508 of the Rehabilitation Act requires federal agencies to make the information and communication technology
they develop, procure, maintain or use accessible. The Revised 508 Standards, issued by the US Access Board in 2017,
incorporate WCAG 2.0 level AA by reference for web content (<https://www.access-board.gov/ict/>). The government-wide
program site is <https://www.section508.gov/>. Vendors usually document conformance in a VPAT (Voluntary Product
Accessibility Template), which produces an Accessibility Conformance Report.

## The project's status

From [Accessibility standards: Section 508](../../../standards/accessibility.md#section-508):

- **Status: Unverified.** There is no VPAT or Accessibility Conformance Report for Exponential.
- **In substance**, the WCAG work covers it: WCAG 2.0 AA is a subset of what the admin4 administration is tested
  against (WCAG 2.2 AA, rated **Partly complies**).
- Not reviewed: the online editor (TinyMCE integrations, **Unverified**), public site designs (not audited),
  extensions shipped as separate packages (no overall statement).

## What to evaluate

A Section 508 evaluation of an Exponential project usually has two objects:

| Object | Users | What to check |
|---|---|---|
| The public site | the public, including people with disabilities | your design's templates, the content, documents, forms, media; WCAG 2.0 AA at least |
| The authoring environment | agency staff who edit | the admin4 administration, the online editor, the upload dialogs; keyboard use, screen reader use, contrast, error handling |

For the authoring side, start from what the project tests (labels, landmarks, keyboard patterns, contrast tokens,
error summaries; see "What Exponential's admin4 covers" in this section) and test the online editor and any
extensions you activate yourself, because the project has not.

## When a VPAT is requested

A tender may ask the bidder for a VPAT. With Exponential:

1. Do not present the project's statement as a VPAT. It is a status report with evidence, and it says that no VPAT
   exists.
2. If you (or the bidder) need one, produce an ACR **for the configured product you deliver**: your Exponential
   version, your administration design (admin4), your public design, your activated extensions. The ITI VPAT template
   is maintained outside this project; use the edition your agency asks for.
3. Base every row on a test you ran, and keep the test notes. "Supports", "Partially supports" and "Does not
   support" must be backed by evidence; where you did not test, say "Not evaluated".
4. See "Preparing an Accessibility Conformance Report for your site" in this section for a working method.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
