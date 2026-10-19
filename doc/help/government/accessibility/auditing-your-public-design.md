---
title: Auditing your public design with WCAG-EM
audience: government, project-manager, designer, developer
category: public-sector
order: 120
summary: For public bodies that must evaluate their own site - how to run an evaluation of a site built with Exponential following the W3C WCAG-EM methodology, choosing a sample from Exponential's templates, classes and siteaccesses, and recording the result.
keywords: WCAG-EM, evaluation methodology, accessibility audit, sample, templates, content classes, siteaccess, public design, conformance target, findings, report, re-audit
type: help
---
# Auditing your public design with WCAG-EM

This page is for accessibility coordinators, designers and auditors who evaluate a public site built with
Exponential. The project has **not** audited the public designs; that evaluation is yours
([Accessibility standards: Known gaps](../../../standards/accessibility.md#known-gaps)). The method below follows the
W3C Website Accessibility Conformance Evaluation Methodology, WCAG-EM (<https://www.w3.org/TR/WCAG-EM/>), and maps its
steps onto the way Exponential builds pages.

## Contents

1. [Why the structure of Exponential helps](#why-the-structure-of-exponential-helps)
2. [Step 1: Define the scope](#step-1-define-the-scope)
3. [Step 2: Explore the site](#step-2-explore-the-site)
4. [Step 3: Select a sample](#step-3-select-a-sample)
5. [Step 4: Evaluate](#step-4-evaluate)
6. [Step 5: Report](#step-5-report)
7. [Related pages](#related-pages)

## Why the structure of Exponential helps

Every page of an Exponential site is drawn by a small number of templates: the pagelayout, one full view per content
class, line and embed views, and the templates of the datatypes and rich text tags
([Designs and the fallback chain](../../creators/website-creators/designs-and-the-fallback-chain.md),
[Template overrides](../../creators/website-creators/template-overrides.md)). A finding in a template usually affects
every page that uses it, and a fix there repairs them all. Sample by template, then add content that editors
produced.

## Step 1: Define the scope

- **Target**: WCAG 2.2 level AA (contains 2.1 AA, which EN 301 549 clause 9 incorporates).
- **Siteaccesses**: list every public siteaccess (each language or site is one); include right-to-left ones.
- **Included**: forms, search, sign-in and registration, shop, newsletter subscription, documents linked from pages.
- **Excluded, explicitly**: third-party content you do not control (for example an embedded map service), stated in
  the report.
- **Accessibility support baseline**: the browsers and assistive technologies you test with.

## Step 2: Explore the site

Collect the building blocks:

| Look at | Where |
|---|---|
| Content classes in use | Setup > Classes (`/class/grouplist`) |
| Templates and overrides | the design's `override.ini` and `templates/` folders; Setup > Extensions for activated designs |
| Layouts and blocks (if the layouts editor is used) | [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md) |
| Forms | objects of classes with information collector attributes, Setup > Collected information |
| Common functions | search, sign-in, error pages (not found, access denied) |

## Step 3: Select a sample

A practical sample for an Exponential site:

1. the front page and each landing page layout;
2. one full view of every content class visitors see;
3. a folder list with paging;
4. search results with and without hits;
5. every form type;
6. the error pages;
7. one page per language siteaccess;
8. a random selection of about ten content pages written by editors, for content quality.

This matches the list in [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md#choose-the-pages).

## Step 4: Evaluate

For each page of the sample:

- an automated check (an axe-based tool, for example), recorded with the tool version;
- a keyboard pass and a screen reader pass;
- zoom to 200% and a 320 px width;
- a manual check of every WCAG 2.2 AA criterion that tools cannot decide (meaningful sequence, link purpose,
  headings, error suggestions).

Record each finding with: page, criterion, what happens, severity, and **where it comes from**: a template (name it),
content (name the object), or an extension.

## Step 5: Report

| Part | Content |
|---|---|
| Scope and target | as in step 1 |
| Sample | the URLs, and why each was chosen |
| Results per criterion | passed, failed, not present |
| Findings | with source (template, content, extension) |
| Conclusion | in your own words; avoid "conforms" unless every sampled page passes every criterion |
| Date and evaluator | who evaluated, when, with which tools |

Feed the findings into the accessibility statement (known gaps and plan) and into your fix list. Re-audit after a
redesign, an upgrade that changes templates, or a new content class.

## Related pages

- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Accessibility standards](../../../standards/accessibility.md)
- W3C, [WCAG-EM](https://www.w3.org/TR/WCAG-EM/)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
