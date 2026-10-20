---
title: admin4 styling for extension pages
audience: developer, designer
category: extension-development
order: 190
summary: Make an old extension's admin pages look like the rest of admin4 - shared a4 components, tokens only, dark mode, no inline CSS - with the explayouts_ui template editor as a real before and after.
keywords: admin4, a4 components, design tokens, --a4-, dark mode, components.css, BackendCSSFileList, a4-card, a4-btn, a4-table, extension admin page, upgrade
type: help
---
# admin4 styling for extension pages

Part 4, chapter 19. An old extension's admin pages usually bring their own look: a table with borders from 2010,
grey buttons, colors written into the stylesheet, and nothing for dark mode. In the admin4 design they stand out, and
in dark mode they show a white box on a dark page. This chapter gives your pages the look of the administration
using what it already has. The full reference is
[Styling the administration](../../../bc/6.0/admin4-styling.md); Part 3 covers new pages in
[Admin pages in the admin4 style](../part-3-extension-developer/admin-pages-in-admin4-style.md).

## The rules, short

The administration's rules apply to the admin designs of extensions too:

1. No `<style>` block and no `style=""` attribute in templates ([Inline CSS to stylesheets](inline-css-to-stylesheets.md)).
2. Use the shared component classes first: they start with `.a4-` and live in `components.css`.
3. Colors, radius, spacing and fonts come only from the `--a4-*` tokens. No hard-coded values, no local aliases.
4. Every color you use must work in dark mode, which the tokens do for you.
5. Your own classes get your extension's prefix and live in your stylesheet, added to `BackendCSSFileList[]`.
6. Check the page in light and dark mode, at a normal width and at 200% zoom.

## Step 1: replace your markup's classes with components

Map the old page's parts to the components. The most common ones:

| Old markup | Component |
|---|---|
| A box with a heading (`div.box-header` + `div.box-content`, or your own `.myoldext-box`) | `.a4-card` with `.a4-card-head`, `.a4-card-body`, `.a4-card-foot` |
| A data table (`table.list` or your own) | `table.list` already works; for new markup `.a4-table-wrap` > `table.a4-table` |
| Buttons | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-small` |
| A row of buttons above or below a list | `.a4-toolbar` (`.is-bottom` under a list) |
| Form fields | `.a4-field` > `.a4-label` + `.a4-input` |
| "Nothing found" text | `.a4-empty` |
| Success and error messages | `.a4-notice` with `.is-ok`, `.is-warn`, `.is-bad`, `.is-info` |
| Status words ("active", "failed") | `.a4-badge` with the same state classes |
| A page title with actions | `.a4-pagehead` |

Wrap the page's content in `.a4-page`, which sets the box model, the font, links and the focus ring.

A list page, as the reference shows it:

```html
<div class="a4-page">
  <div class="a4-toolbar"><p class="a4-meta">12 roles</p>
    <div class="a4-actions"><a class="a4-btn a4-btn-primary" href="...">New role</a></div></div>
  <ul class="a4-cards">
    <li class="a4-card"><div class="a4-card-head"><div class="a4-card-title"><h3 class="a4-h3">Editor</h3>
      <span class="a4-badge is-ok">assigned</span></div></div></li>
  </ul>
  <p class="a4-empty">No roles match.</p>
</div>
```

> **Note**
> The `.a4-*` classes are defined for the admin, admin3 and admin4 designs (design/admin has an identical copy of
> `components.css`). An extension that must also look right in a much older administration keeps its old classes
> beside the new ones until it drops support for it.

## Step 2: what is left goes into your stylesheet, from tokens

Some things the components cannot do: a chart's legend, a special grid. Those rules go into your extension's
stylesheet, with your prefix, built from tokens only.

A real one: se7enxweb/explayouts_ui, commit 9babf78, "The admin4 template editor takes its colors, spacing and type
straight from admin4's tokens, without local aliases or fallback colors". Before, the stylesheet defined its own
variables with fallback colors:

```css
:root {
    --bg: var(--a4-surface, #f6f8fa);
    --panel: var(--a4-card, #ffffff);
    --panel-2: var(--a4-line, #d0d7de);
    --fg: var(--a4-ink, #1f2328);
    --accent: var(--a4-accent-strong, #c2410c);
}
html, body { background: var(--bg); color: var(--fg); font-size: 14px; }
```

After, the aliases are gone and every rule names the token itself:

```css
html, body { background: var(--a4-surface); color: var(--a4-ink); font-family: var(--a4-mono); font-size: var(--a4-text-m); }
```

Why remove the fallbacks? A fallback color is a hard-coded light color that shows whenever the token is missing, and
in dark mode that is a white panel. Local aliases (`--bg`, `--panel`) also hide which token a rule uses, so the next
person cannot check it against the reference.

The tokens you will need most:

| For | Token |
|---|---|
| Text, muted text | `--a4-ink`, `--a4-muted` |
| Backgrounds | `--a4-surface` (page), `--a4-card` (cards), `--a4-soft` (a light tint) |
| Lines | `--a4-line`, `--a4-line-strong` |
| The accent | `--a4-accent`, `--a4-accent-strong` (fills with white text), `--a4-accent-text` (text and links) |
| States | `--a4-ok`, `--a4-warn`, `--a4-bad`, `--a4-info`, each with `-bg` and `-line` |
| Spacing | `--a4-space-1` to `--a4-space-7` (4 to 48 px) |
| Radius | `--a4-radius-s`, the one component radius |
| Type | `--a4-font`, `--a4-mono`, `--a4-text-xs` to `--a4-text-3xl` |

Load the stylesheet through `BackendCSSFileList[]` in your extension's `design.ini.append.php`, never through
`AdminComponentCSSFileList[]`, which is the kernel's own list.

## Step 3: dark mode

admin4's mode is `html[data-a4-theme="light"]` or `"dark"`. When every color comes from a token, you need no dark
rules at all: the tokens switch. If you think you need a `html[data-a4-theme="dark"]` rule, look for the token you
missed. If you add a token of your own (only for something the shared tokens really cannot express), give it a dark
value in the same commit.

## Step 4: look at it

Open each page of the extension in admin4:

- in light mode and in dark mode (the toggle is in the header);
- at about 1440 pixels wide, and at 960 pixels with the browser at 200% zoom;
- with the console open.

Check: no white card on the dark page, text readable (contrast at least 4.5:1), no horizontal scrollbar, no console
errors. After a stylesheet change, clear the caches that hold the packed stylesheet link (template and template-block)
and do a hard reload; do not add `?v=` to the link.

## Step 5: a test

explayouts_ui added `tests/Admin4StylingTest.php` in the same commit: it keeps the admin4 templates free of inline
CSS and version queries, and the stylesheets free of local custom properties and hard-coded colors. A test like that
is short and needs no database; see [Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md).

## Related pages

- Previous: [From old jQuery to jQuery 4](old-jquery-to-jquery-4.md)
- Next: [Context menus and admin integration](context-menus-and-admin-integration.md)
- [Styling the administration](../../../bc/6.0/admin4-styling.md)
- [Admin pages in the admin4 style](../part-3-extension-developer/admin-pages-in-admin4-style.md)
- [Dark mode and design tokens in themes](../part-6-theme-developers/dark-mode-and-tokens-in-themes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
