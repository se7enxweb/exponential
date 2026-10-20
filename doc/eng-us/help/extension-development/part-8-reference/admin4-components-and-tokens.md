---
title: admin4 components and tokens
audience: developer, designer
category: extension-development
order: 350
summary: A reference card of the administration's shared components (.a4-* classes) and design tokens (--a4-* custom properties) an extension's admin pages use, with the rules that keep them working in light and dark mode.
keywords: admin4, components, tokens, a4-card, a4-btn, a4-table, a4-notice, a4-badge, a4-toolbar, a4-field, --a4-ink, --a4-accent, --a4-space, dark mode, components.css
type: help
---
# admin4 components and tokens

Part 8, reference 31. The administration styles every page with one set of shared component classes (`.a4-*`) and one
set of design tokens (`--a4-*`), defined in `components.css` of the admin designs. An extension's admin pages use
them too. The complete reference, with every class, state and token, is
[Styling the administration](../../../bc/6.0/admin4-styling.md); how to convert an old extension's pages is
[admin4 styling for extension pages](../part-4-upgrading/admin4-styling-for-extension-pages.md).

## Components used most

| Need | Classes |
|---|---|
| Page wrapper | `.a4-page` |
| Page title with actions | `.a4-pagehead` > `.a4-pagehead-main` (`.a4-pagehead-title`, `.a4-pagehead-sub`) + `.a4-pagehead-actions` |
| Card | `.a4-card` > `.a4-card-head`, `.a4-card-body`, `.a4-card-foot`; states `.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-flush` |
| Buttons | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-ghost`, `.a4-btn-small`; `.a4-actions` for a row |
| Toolbar | `.a4-toolbar` (`.is-bottom`, `.is-sticky`) |
| Table | `.a4-table-wrap` > `table.a4-table` (`.is-compact`) |
| Form | `.a4-field` > `.a4-label` + `.a4-input`; `.a4-field-error`; `.a4-check`, `.a4-radios` |
| Messages | `.a4-notice` with `.is-ok`, `.is-warn`, `.is-bad`, `.is-info` |
| Status | `.a4-badge`, `.a4-tag` with the same states; `.a4-status` with `.a4-dot` |
| Empty list | `.a4-empty` |
| Utilities | `.a4-sr-only`, `.a4-muted`, `.a4-meta`, `.a4-help`, `.a4-nowrap`, `.a4-code` |
| Icons | `.a4-icon` with `.a4-bi-<name>` |

States are `.is-*` classes on the component.

## Tokens used most

| Group | Tokens |
|---|---|
| Text | `--a4-ink`, `--a4-muted` |
| Surfaces | `--a4-surface`, `--a4-card`, `--a4-soft` |
| Lines | `--a4-line`, `--a4-line-strong` |
| Accent | `--a4-accent`, `--a4-accent-strong` (fills with white text), `--a4-accent-text` (text), `--a4-accent-soft` |
| States | `--a4-ok`, `--a4-warn`, `--a4-bad`, `--a4-info`, each with `-bg` and `-line` |
| Spacing | `--a4-space-1` to `--a4-space-7` (4, 8, 12, 16, 24, 32, 48 px) |
| Radius | `--a4-radius-s` |
| Type | `--a4-font`, `--a4-mono`, `--a4-text-xs` to `--a4-text-3xl` |
| Controls | `--a4-control-h` |

Every color token has a dark value; the mode is `html[data-a4-theme="light"]` or `"dark"`, and a rule built from tokens
needs nothing for dark mode.

## Rules for extension pages

1. No `<style>` and no `style=""` in templates.
2. Components first; your own classes only for what they cannot do, with your extension's prefix.
3. Only tokens for colors, spacing, radius and type; no local aliases, no hard-coded values.
4. Your stylesheet goes into `design.ini [StylesheetSettings] BackendCSSFileList[]`.
5. Check light and dark mode, at normal width and at 200% zoom.

## Related pages

- Previous: [The expui JavaScript API](expui-javascript-api.md)
- Next: [The help system for extensions](help-system-for-extensions.md)
- [Admin pages in the admin4 style](../part-3-extension-developer/admin-pages-in-admin4-style.md)
- [admin4 tokens](../../designers/theme-design/admin4-tokens.md), [admin4 components](../../designers/theme-design/admin4-components.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
