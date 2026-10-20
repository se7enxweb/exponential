---
title: "An accessibility audit for every release"
audience: designer
category: theme-lifecycle
order: 140
summary: A repeatable accessibility audit before each theme release - axe on the page list at three widths in light and dark, keyboard walk, zoom, contrast - recording results and fixing serious issues first, as the 7x themes did for WCAG 2.2 AA.
keywords: accessibility audit, a11y, axe, WCAG 2.2 AA, contrast, keyboard, zoom, release routine, serious issues
type: help
---
# An accessibility audit for every release

This page is for designers who maintain a theme. Accessibility regresses quietly: a new color, a new icon link without
a name. A short audit per release catches it. How to design accessibly is explained in
[Accessibility basics for themes](../theme-design/accessibility-basics-for-themes.md) and
[Checklist: accessibility](../theme-design/checklist-accessibility.md).

## The routine

1. **Automated check** with axe (browser extension or a headless run) on every page of your page list, at 1440 px,
   960 px at 200% zoom, and 375 px, in light and dark mode. Target: no serious or critical issues.
2. **Keyboard walk**: tab through the front page, an article and a form. Focus is always visible; nothing traps it.
3. **Zoom**: at 200% the content reflows without horizontal scrolling ([Testing at zoom levels](../theme-design/testing-at-zoom-levels.md)).
4. **Contrast** of new colors: text at least 4.5:1 ([Color contrast](../theme-design/color-contrast.md)).
5. **Names**: every link and button has an accessible name, every image a suitable alt text.

## Record and fix

| Record | Example |
|---|---|
| Date, theme version, tool and version | 2026-10-17, 0.1.5, axe |
| Pages and widths checked | 12 pages, 3 widths, light and dark |
| Findings by severity | 0 serious, 2 moderate |
| What was fixed | The footer links are 24 px high |

The `cjw_themes_jumper` 0.1.5 changelog is an example of reporting the result: it names the checks passed (contrast,
target size and link names, axe, WCAG 2.2 AA) and what was changed to pass them.

## Related pages

- [Visual regression checks](visual-regression-checks.md)
- [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
