---
title: No inline CSS
audience: designer
category: theme-design
order: 610
summary: Why templates carry no style blocks and no style attributes, where the CSS goes instead, how to handle per-element values (percentages, colors from content, computed widths), and the one documented exception in the admin.
keywords: inline css, style attribute, style block, no inline styles, per element values, data attributes, custom properties, pct classes, csp style-src, stylesheet only
type: help
---
# No inline CSS

This page is for designers who write templates. The project rule is short: **no `<style>` block and no `style=""`
attribute in a template.** All CSS lives in the design's `stylesheets/` folder and is loaded through ezjscore. The page
explains why, and how to handle the cases that seem to need inline CSS.

## Contents

1. [Why](#why)
2. [Where the CSS goes](#where-the-css-goes)
3. [Per-element values](#per-element-values)
4. [Styles that come from content](#styles-that-come-from-content)
5. [Finding inline CSS](#finding-inline-css)

## Why

- A Content Security Policy with `style-src 'self'` blocks inline styles ([CSP-safe design](csp-safe-design.md)).
- Inline CSS is not packed or cached, and is sent again with every page.
- Copies drift: the admin once had 55 templates with their own `<style>` blocks, four different accent colors and
  dozens of white cards that ignored dark mode (the story is in [Admin styling](../../../bc/6.0/admin4-styling.md),
  "Why").
- Inline styles beat stylesheet rules, so a theme can no longer restyle the element.

## Where the CSS goes

| CSS for | Goes in |
|---|---|
| the whole design | your main stylesheet, loaded by `ezcss_load` in the pagelayout |
| one kind of page | its own file, asked for by the view template with `ezcss_require` |
| an admin module page | the shared components first, then `stylesheets/modules/<group>.css` or the extension's admin stylesheet |

## Per-element values

A value that really belongs to one element (a progress percentage, a bar height, a color an editor picked):

- **A class from a fixed set.** admin4 has `.a4-pct-0` to `.a4-pct-100` in steps of 4 for meters and bars:

  ```tpl
  <span class="a4-meter"><span class="a4-pct-{$percent|div( 4 )|round|mul( 4 )}"></span></span>
  ```

- **A `data-*` attribute** that a script turns into a CSS custom property:

  ```tpl
  <div class="chart-bar" data-value="{$value|wash}"></div>
  ```

  ```js
  document.querySelectorAll('.chart-bar').forEach(function (el) {
    el.style.setProperty('--value', el.dataset.value);   /* set from a script file: allowed by style-src 'self' */
  });
  ```

  ```css
  .chart-bar { inline-size: calc(var(--value) * 1%); }
  ```

  Setting a style property from a script file is not an inline style attribute in the markup and is allowed by a policy
  without `'unsafe-inline'`.

## Styles that come from content

Rich text can contain alignment or classes chosen by editors; the datatype templates turn them into classes, not
`style=""`. Keep it that way in overrides of `ezxmltags/*.tpl`. Note that the CSP page says sites with content-made
`style=""` (embedded players, legacy rich text) should start with `Mode=report-only`.

## Finding inline CSS

```bash
grep -rn -E "<style|style=\"" extension/mytheme/design --include=*.tpl
```

For admin designs, `tests/tests/design/AdminStylingRulesTest.php` fails on every occurrence; the only exception is
`design/admin/templates/pagelayout.tpl`, for the editor's column widths before the page is drawn, documented in the test
([The admin styling rules test](admin-styling-rules-test.md)).

## Related pages

- [No inline JavaScript](no-inline-javascript.md)
- [Loading stylesheets](loading-stylesheets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
