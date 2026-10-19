---
title: "Case study: two-factor authentication without inline scripts"
audience: developer, designer
category: extension-development
order: 250
summary: How sevenx_authentication_2fa lost its inline script, style block and style attributes in six small releases, with a test at each step, and why its colors keep token fallbacks.
keywords: case study, sevenx_authentication_2fa, two-factor authentication, CSP, inline script, style block, style attribute, tokens, dark mode, release, upgrade
type: help
---
# Case study: two-factor authentication without inline scripts

Part 4, chapter 25. se7enxweb/sevenx_authentication_2fa adds a second step to signing in (an authenticator app,
among other methods). Its pages appear in the administration, on public sites and on sign-in pages that load almost
nothing. On 17 October 2026 it went through the whole browser part of an upgrade in six small releases, 1.0.7 to
1.0.12. This chapter follows those commits, because they show the CSP work in the right order and with a test at
each step.

## The releases of one day

| Commit | Change | Release |
|---|---|---|
| e603c1c | the 2FA field of the content edit form: five `style=""` attributes become classes in a new admin stylesheet, plus a test | 1.0.8 (8803d7c) |
| e4c0319 | the setup page's inline script becomes a file | 1.0.9 (085ce86) |
| 599e793 | the pages' `<style>` block becomes a stylesheet | |
| ea4947c | the template links that stylesheet where the block was | |
| 6a75f30 | status colors from the admin tokens, with fallbacks | |
| 73bfdd2 | the setup page's last `style=""` attributes become classes | 1.0.10 (9a5fd94) |
| 2031e45 | the account link and the public 2FA field | 1.0.11 (771183d) |
| 0bee499 | the media design's pages | 1.0.12 (882295d) |

Each release could be installed on its own and was better than the one before.

## Step 1: the inline script

The setup page showed the enrolment of an authenticator app only while that method was picked, with a script block
in the template. Commit e4c0319 moved it into `design/standard/javascript/sevenx_authentication_2fa_setup.js`,
printed with `ezscript` where the block stood, in both the standard and the media design. The before and after are
shown in full in [CSP: moving inline scripts into files](csp-moving-inline-scripts-out.md).

The same commit added `tests/sevenxauthentication2fainlinescripttest.php`, which checks three things: no inline
script in any template, no event handler attribute or `javascript:` link, and that both setup pages print the new
file. The third check is the interesting one: it makes sure the fix stays connected, not only that the old code is
gone.

## Step 2: the style block, and a sign-in page with no stylesheet list

Commit 599e793 moved the rules of the `<style>` block in `user2fa/exp_style.tpl` into
`stylesheets/sevenx_authentication_2fa.css`, loaded through `design.ini [StylesheetSettings]` for public and
administration designs.

That was not enough, and the next commit (ea4947c) explains why: "The Admin UI's sign-in page loads no stylesheet
list, so its second step at sign-in would have lost its look with only the packer's lists." So the template also
links the stylesheet at the place of the former block, "which also keeps the rules where they were in the cascade".

The lesson: before you rely on `FrontendCSSFileList[]` or `BackendCSSFileList[]`, find every page layout your
templates are rendered in. Sign-in pages, popups and print views often load a reduced set of styles.

## Step 3: colors that work in the dark

Moving the styles revealed a second problem: "the card stayed white under light text, 1.2:1" in a dark admin4
(599e793), and status words kept light-mode colors on the dark card ("Off" in dark amber, 2.4:1, 6a75f30). The fix
takes the colors from the admin tokens.

Here this extension made a different choice from the layouts template editor
([admin4 styling for extension pages](admin4-styling-for-extension-pages.md)), and both are right:

| | explayouts_ui template editor | sevenx_authentication_2fa |
|---|---|---|
| Where it runs | admin4 only | admin4, other admin designs, public sites, sign-in pages |
| Token fallbacks | removed: the tokens always exist there | kept: "the former values for the designs without them" |

A fallback such as `var(--a4-ok, #166534)` (from this extension's stylesheet) is a problem when the token always exists (it hides mistakes) and a
necessity when it may not (the public site's design does not define admin tokens). Decide by where your stylesheet
is loaded.

## Step 4: the last attributes

Commits 73bfdd2, 2031e45 and 0bee499 went page by page through what was left: the setup page's spacing, the account
link, the public 2FA field and the media design's copies of the pages. Each replaced `style=""` attributes with
classes of the stylesheet and extended the test to the page.

## Why so many releases?

Because each one was complete. A site that updated after 1.0.9 had no inline script; one that updated after 1.0.12
had no inline CSS either. If a release had broken something, the change to look at would have been one small commit.
Each release was its own commit that only raised the version in `ezinfo.php` (this extension has no `extension.xml`;
one that has must raise it there too, see [Bumping ezinfo.php and extension.xml](../part-7-releasing/bumping-ezinfo-and-extension-xml.md)).

## What to take from it

1. Script first, then the style block, then the attributes: in order of how much breaks under the policy.
2. After moving styles, look at the page in dark mode: moved styles reveal hard-coded colors.
3. Find every page layout your templates appear in, especially sign-in pages.
4. Use token fallbacks only where the tokens may be missing.
5. Extend the test with every page you fix.

## Related pages

- Previous: [Case study: the layouts editor on jQuery 4](case-study-the-layouts-editor-on-jquery-4.md)
- Next: [Case study: small PHP 8 fixes across many extensions](case-study-small-fixes-across-many-extensions.md)
- [Inline CSS to stylesheets](inline-css-to-stylesheets.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
