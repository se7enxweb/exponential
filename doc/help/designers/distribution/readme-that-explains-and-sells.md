---
title: "A README that explains and sells"
audience: designer, sales-marketing
category: theme-distribution
order: 50
summary: Turn a theme's README into the page that convinces - the first screen, a screenshot, proof of quality (accessibility, CSP, dark mode), honest requirements, a two-minute install and clear support - without claims you cannot prove.
keywords: README, sell, explain, first screen, screenshot, features, requirements, install, support, badges, proof
type: help
---
# A README that explains and sells

This page is for designers who already have a README ([Writing the README of a theme](../extensions/readme-for-a-theme.md))
and want it to persuade as well as instruct. On GitHub and Packagist the README is the product page.

## The first screen

A visitor decides within the first screen. Put there:

1. The name and one sentence of what it is.
2. One screenshot of the front page (light mode), with a link to a dark-mode and a phone screenshot below.
3. Three to five facts a buyer checks, each true and checkable:
   - "Works with Exponential 6.0 (tested with 6.0.15) on PHP 8.1 to 8.5."
   - "Passes axe with no serious issues at 1440 px and 375 px." (only if you ran it)
   - "Runs under a Content Security Policy without 'unsafe-inline'." (only if it does)
   - "GPL-2.0-or-later. Free to use, change and share."

## Proof instead of adjectives

| Instead of | Write |
|---|---|
| "Blazing fast" | The measured numbers and how you measured them, or nothing |
| "Fully accessible" | "Meets WCAG 2.2 AA contrast; checked with axe on the pages listed in doc/a11y.md" |
| "Secure" | "Escapes all output; no inline scripts; report problems as described in SECURITY.md" |

The 7x themes write their release notes this way: `cjw_themes_jumper` 0.1.5 says exactly which checks it passes
(contrast, target size and link names, WCAG 2.2 AA).

## The rest, in order

Requirements, install, switch on, configure, what is inside, license and notices, support and security, changelog,
credits. Keep install to commands a reader can copy.

## Step by step

1. Rewrite the first screen with the list above.
2. Replace every adjective with a fact or remove it.
3. Ask someone who has never seen the theme to install it from the README alone; fix every place they stop.
4. Check the rendered README on GitHub and on Packagist (Packagist shows it on the package page).

## Related pages

- [Screenshots for a theme](screenshots-for-a-theme.md)
- [Writing a theme's product page](../sales/product-page.md)
- [Positioning and value proposition](../../business/positioning-and-value-proposition.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
