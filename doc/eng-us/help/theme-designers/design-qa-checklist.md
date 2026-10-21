---
title: "Design QA checklist"
audience: theme-designer
category: theme-design
order: 38
summary: A checklist for theme designers to check a built Exponential theme against the design - on a development or preview siteaccess, page type by page type, in light and dark, at three widths, with real and stress content - and how to report what fails.
keywords: theme designer, design QA, quality assurance, checklist, visual QA, testing, light and dark, zoom, stress content, preview siteaccess, bug report
type: help
---
# Design QA checklist

This page is for theme designers who check the built theme against their design before review and before launch. It
is a checklist of the **design** quality of the pages. The project's technical checklists (accessibility,
performance, CSP, dark mode, right to left, theme launch) are separate and still apply; links are at the end of each
section. Work on a development or preview siteaccess, never on the live site.

## Contents

1. [Set up](#set-up)
2. [For every page type](#for-every-page-type)
3. [The frame](#the-frame)
4. [Content views](#content-views)
5. [Lists, search and blocks](#lists-search-and-blocks)
6. [Special pages](#special-pages)
7. [Foundations](#foundations)
8. [Reporting](#reporting)

## Set up

- [ ] A development or preview siteaccess with the theme ([Previewing a design on a siteaccess](../designers/theme-design/previewing-a-design-on-a-siteaccess.md)).
- [ ] Template path comments on, to name templates in reports ([Tools for theme designers](tools-for-theme-designers.md)).
- [ ] The stress set of test objects in place ([Designing with real content](designing-with-real-content.md#the-stress-set)).
- [ ] A list of addresses: one per class and view, plus search, sign-in, errors, an empty folder.
- [ ] A test editor account with an `example.invalid` address.

## For every page type

Check each address in this matrix:

| | Light | Dark |
|---|---|---|
| 1440 px | [ ] | [ ] |
| 960 px at 200% zoom | [ ] | [ ] |
| 375 px | [ ] | [ ] |

And on each:

- [ ] Matches the mockup in layout, type, spacing and color, or the difference is agreed.
- [ ] No horizontal scrolling of the page.
- [ ] Keyboard only: every interactive element reachable, focus always visible.
- [ ] No white card or white box on a dark background in dark mode.
- [ ] Reduced motion setting on: nothing moves that should not.

## The frame

- [ ] Header and footer as designed at each width; small-screen menu open and closed.
- [ ] Current section marked in the main menu; current page last in breadcrumbs.
- [ ] Signed in and signed out states.
- [ ] Language switcher (if any): each language named in itself, current marked.
- [ ] Skip link appears on first Tab.
- [ ] Logo correct in light and dark.

See [Designing headers and footers](designing-headers-and-footers.md).

## Content views

- [ ] Full view of each class with all fields filled, and with each optional field empty.
- [ ] Longest headline and longest word wrap cleanly.
- [ ] Rich text "kitchen sink": every heading level, lists, tables, quotes, embeds, custom tags.
- [ ] Images: right alias, right crop, alternative text present, no distortion; portrait and panorama uploads.
- [ ] Dates and numbers formatted for the siteaccess's locale.

See [Designing view modes](designing-view-modes.md) and [Designing for editors](designing-for-editors.md).

## Lists, search and blocks

- [ ] Folder lists with mixed classes read as one list.
- [ ] Cards with and without images; whole-card click with one link for screen readers.
- [ ] Pagination at first, middle and last page, and on a phone.
- [ ] Search with results, one result, many results and none.
- [ ] Each layout block view type at full width and in a narrow zone; one item; empty collection leaves no gap.

See [Designing content listings and cards](designing-content-listings-and-cards.md),
[Designing search results](designing-search-results.md), [Designing layout blocks](designing-layout-blocks.md).

## Special pages

- [ ] Not found, access denied (signed out, with the sign-in form, and signed in), generic error.
- [ ] Sign in, forgotten password, registration (if enabled), confirmation pages.
- [ ] Forms: errors summary and per-field messages; confirmation page.
- [ ] Empty folder and empty related list.

See [Designing empty and error states](designing-empty-and-error-states.md), [Designing forms](designing-forms.md),
[Designing login and user pages](designing-login-and-user-pages.md).

## Foundations

- [ ] Every color, size, space and radius in the stylesheets is a token; every color token has a dark value.
- [ ] Text contrast at least 4.5:1 in both themes; focus and control borders at least 3:1.
- [ ] Type scale and spacing scale used as specified; no stray values.
- [ ] Interactive states as in the state matrix ([Designing interactive states](designing-interactive-states.md)).
- [ ] Page budget met: requests, no third-party requests before consent, LCP and CLS
      ([Designing for performance budgets](designing-for-performance-budgets.md)).
- [ ] The [style guide page](building-a-style-guide-page.md) matches the [component inventory](component-inventory.md).

Then run the project checklists: [Accessibility](../designers/theme-design/checklist-accessibility.md),
[Dark mode](../designers/theme-design/checklist-dark-mode.md), [Performance](../designers/theme-design/checklist-performance.md),
[CSP](../designers/theme-design/checklist-csp.md), [Right to left](../designers/theme-design/checklist-right-to-left.md).

## Reporting

One issue per report, with:

1. the address, the width, light or dark, and the zoom level;
2. what you see and what the design expects (mockup link);
3. the template that drew it, from the template path comments;
4. a screenshot with any personal data removed;
5. priority: blocks launch, should fix, later.

## Related pages

- [Theme design review checklist](theme-design-review-checklist.md)
- [Handoff checklist](handoff-checklist.md)
- [Checklist: theme launch](../designers/theme-design/checklist-theme-launch.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
