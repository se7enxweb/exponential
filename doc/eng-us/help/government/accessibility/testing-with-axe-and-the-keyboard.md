---
title: Testing with axe and the keyboard
audience: government, developer, designer, project-manager
category: public-sector
order: 250
summary: For teams that keep a public site accessible release after release - a repeatable routine of automated checks with an axe-based tool and manual keyboard and screen reader passes, how the Exponential project itself tests, and how to record results.
keywords: axe, automated testing, accessibility testing, keyboard test, screen reader, regression, release routine, design tests, phpunit, CI, manual review, test log, WCAG
type: help
---
# Testing with axe and the keyboard

This page is for developers, designers and project managers who want accessibility testing to be a routine rather
than an event. Automated tools find a part of the problems; a keyboard and screen reader pass finds much of the rest.
Exponential ships no accessibility testing tool for public sites; the routine below uses common external tools and
the practice the project follows for its own administration.

## Contents

1. [How the project tests](#how-the-project-tests)
2. [Automated checks on your site](#automated-checks-on-your-site)
3. [The manual passes](#the-manual-passes)
4. [When to test](#when-to-test)
5. [Recording results](#recording-results)
6. [Related pages](#related-pages)

## How the project tests

- **Source tests** in `tests/tests/design/` (the `design` suite of `phpunit.xml`) check templates and stylesheets of
  the administration: labels, landmarks, keyboard hooks, contrast tokens, error summaries. They do not render pages.
- **axe runs by hand** during reviews, not in CI; findings and fixes are recorded in the
  [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md).
- Pages are checked in light and dark mode, at 1440 px and at 960 px with 200% zoom, and with the keyboard alone
  ([Accessibility standards: For template authors](../../../standards/accessibility.md#for-template-authors)).

If you extend the administration, run the project's suite after template changes:

```bash
php vendor/bin/phpunit --testsuite design
```

## Automated checks on your site

1. Keep a list of URLs: one per page template, form and language (see "Auditing your public design with WCAG-EM").
2. Run an axe-based checker on each URL: a browser extension for ad hoc checks, a command-line tool for the list.
   Record the tool and its version.
3. Run it at a phone width and in each color scheme.
4. Treat every "violation" as a finding; read every "needs review" item by hand.

Automated tools cannot judge whether alternative text is meaningful, whether the reading order makes sense or
whether a link text is clear.

## The manual passes

Follow [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md):

- **Keyboard**: skip link, order, visible focus, menus, no traps.
- **Screen reader**: page title, landmarks and headings, images, form labels and errors, language changes.
- **Zoom, color and motion**: 200% and 320 px, contrast, color not alone, reduced motion.

## When to test

| Event | Test |
|---|---|
| Before launch | full WCAG-EM evaluation |
| Every release that changes templates, stylesheets or scripts | automated list + keyboard pass on changed templates |
| Upgrade of Exponential or an extension that renders public pages | automated list + keyboard pass on the affected pages |
| Monthly | spot check of new content (see "Monitoring and feedback") |
| After a barrier report | the page reported and its template |

## Recording results

Use one table across all tests so trends show:

| Date | URL or task | Tool or pass | Criterion | Finding | Source (template / content / extension) | Fixed in |
|---|---|---|---|---|---|---|

Keep the table with the accessibility statement's evidence.

## Related pages

- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Accessibility standards](../../../standards/accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
