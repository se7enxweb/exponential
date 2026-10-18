---
title: Testing your site's accessibility
audience: website-creator, developer
category: website-creation
order: 610
summary: A practical accessibility test routine for an Exponential site - which pages to test, automatic checks, keyboard and screen reader passes, zoom and color, writing up findings, and keeping it tested after launch.
keywords: accessibility testing, a11y test, axe, automatic checker, keyboard test, screen reader, zoom, contrast, WCAG, regression, website creator
type: help
---
# Testing your site's accessibility

This page is for website creators who check a site before launch and after changes. Automatic tools find part of the
problems; the rest needs a person with a keyboard and a screen reader. This routine takes a few hours for a typical
site and catches most issues.

## Contents

1. [Choose the pages](#choose-the-pages)
2. [Automatic checks](#automatic-checks)
3. [Keyboard pass](#keyboard-pass)
4. [Screen reader pass](#screen-reader-pass)
5. [Zoom, color and motion](#zoom-color-and-motion)
6. [Recording findings](#recording-findings)
7. [After launch](#after-launch)

## Choose the pages

Test one page of every template, not every page:

- the front page and each landing page layout;
- one full view of every class (article, event, product ...);
- a folder list with paging;
- search results, with and without hits;
- every form: contact, registration, login, newsletter, checkout;
- error pages (not found, access denied);
- each language siteaccess, including right-to-left ones.

## Automatic checks

Run an automatic accessibility checker (for example a browser extension or a command-line tool based on axe) on each
page. Fix every error it reports; read its warnings. Run it on your color schemes and at a phone width too. Automatic
tools find missing alternative texts, labels, contrast problems and wrong ARIA, not whether the order makes sense.

## Keyboard pass

Put the mouse away and use Tab, Shift+Tab, Enter, Space, Escape and the arrow keys:

- [ ] The skip link appears on the first Tab and works.
- [ ] Every link and control is reachable, in a sensible order.
- [ ] The focus is always visible.
- [ ] Menus open and close with the keyboard; Escape closes pop-ups and returns focus.
- [ ] Nothing traps the focus (embedded maps, video players, CAPTCHAs).

## Screen reader pass

With a screen reader your operating system offers:

- [ ] The page title says where you are.
- [ ] Landmarks and headings give a usable outline.
- [ ] Images are described or skipped as intended.
- [ ] Form fields announce their label, required state and errors.
- [ ] The language switches are announced in the right language.

## Zoom, color and motion

- [ ] 200% zoom and 320 CSS pixels width: no horizontal scrolling, nothing cut off.
- [ ] Contrast at least 4.5:1 for text, 3:1 for large text and controls.
- [ ] Information is not given by color alone.
- [ ] With reduced motion requested, sliders and animations stop.

## Recording findings

| Page | Problem | Success criterion | Severity | Fix | Owner |
|---|---|---|---|---|---|
| `<page>` | `<what happens>` | `<WCAG reference>` | `<blocker/major/minor>` | `<template or content>` | `<who>` |

Sort fixes into template work (once, for every page) and content work (editors, page by page).

## After launch

- Re-run the automatic checks after every design change, as part of your release checks
  (Testing applications).
- Teach editors the content rules ([Accessibility for site builders](accessibility-for-site-builders.md#what-editors-control)).
- Give visitors a way to report barriers (a contact form), and answer them.

## Related pages

- [Accessibility for site builders](accessibility-for-site-builders.md)
- [The launch checklist](launch-checklist.md)
- [Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
