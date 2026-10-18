---
title: "Checklist: code review for templates"
audience: designer, developer
category: theme-design
order: 920
summary: What a reviewer checks in a template or stylesheet change: escaping, translations, fetches and caching, overrides, inline code, tokens and dark values, logical properties, accessibility, and the light and dark screenshots that go with the change.
keywords: code review, template review, review checklist, pull request checklist, wash, i18n, fetch limit, cache keys, inline css, tokens, admin template review
type: help
---
# Checklist: code review for templates

This page is for reviewers of template and stylesheet changes, and for designers who want their change accepted at the
first review. It follows the project's review list for admin templates and adds what applies to every design.

## Contents

1. [Correctness and safety](#correctness-and-safety)
2. [Performance](#performance)
3. [Styling](#styling)
4. [Accessibility and languages](#accessibility-and-languages)
5. [Evidence](#evidence)

## Correctness and safety

- [ ] Every value from content, settings or the request is washed when printed ([Wash and escaping](../templates/wash-and-escaping.md)).
- [ ] URLs built with `ezurl`/`ezroot`, design files with `ezimage`/`ezdesign`; no hard-coded siteaccess paths.
- [ ] Optional fields tested with `has_content` before use.
- [ ] `{def}` variables `{undef}`ined in included templates; no clash with the includer's names.
- [ ] Override rules: unique prefixed names, a correct `Match`, `Priority` where it matters (Override ordering).
- [ ] The template parses: `./console exp:eztemplatecheck <file or folder>`.

## Performance

- [ ] Every list fetch has a limit; no fetch in a loop (Fetch performance).
- [ ] Cache-block keys cover everything that changes the output, and nothing more.
- [ ] Nothing user-specific in a view-cached template; no `csp_nonce()` in cached output.

## Styling

- [ ] No `<style>` and no `style=""`; no inline scripts or handlers ([CSP-safe design](csp-safe-design.md)).
- [ ] Shared classes reused (admin: `.a4-*` components) before new CSS.
- [ ] Only tokens for colors, radius, spacing, fonts; a dark value for every new color token.
- [ ] Module CSS in the right file (admin: `stylesheets/modules/<group>.css` or the extension's admin stylesheet).
- [ ] Logical properties instead of left and right.
- [ ] Stylesheet changes deployed with the packer step; no `?v=` queries.

## Accessibility and languages

- [ ] Every interface text through `i18n` with the right context, placeholders for variable parts.
- [ ] Headings in order; landmarks intact; buttons are buttons, links are links.
- [ ] Images have alt text; icon-only controls have names.
- [ ] Focus visible; keyboard works for anything interactive the change adds.

## Evidence

- [ ] Screenshots of the changed page in light **and** dark mode, at 1440 px and at 960 px with scale 2.
- [ ] For admin designs: `php vendor/bin/phpunit --testsuite design` passes ([The admin styling rules test](admin-styling-rules-test.md)).
- [ ] No console errors on the changed pages.

## Related pages

- [Checklist: theme launch](checklist-theme-launch.md)
- [Template style guide](../templates/template-style-guide.md)
- [Common template mistakes](../templates/common-template-mistakes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
