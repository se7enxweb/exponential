# Styling the administration: one component stylesheet, tokens, no inline CSS

Read this page before you write or review a template or stylesheet of an administration design (`admin`, `admin2`,
`admin3`, `admin4`, `admin4l`) or of an extension's admin design. It holds the rules, the shared component and token
API, how the stylesheets are loaded, and the checklist for code review. Since 2026-10-14.

## Why

The admin4 module pages had grown one `<style>` block each: 55 templates under `design/admin4/templates/` (7,831
lines), 58 more in `design/admin`, and `style=""` attributes in 53 files. Each block copied the same utilities
(screen-reader text in 27 files, a box-sizing reset in 29, the focus ring in 29, button rules in 41) under its own
scope class (`.exp-roles`, `.exp-rad`, `.nf-`, `.cb-`, `.shop-` ...) and its own custom properties (35 prefixes such
as `--rl-`, `--rad-`). The copies drifted: four accent colours (`#c2410c` 27 times, `#2d6cdf` 12, the brand
`#f26a21` 3, `#ff5500` 1) and two radii. 46 of the 55 blocks hard-coded white with no dark rules, so admin4's dark
mode showed white cards. Inline CSS is not packed or cached, and extensions had no shared classes to build on.

## The rules

1. **No `<style>` block and no `style=""` attribute in a template of an administration design.** All CSS lives in
   the design's `stylesheets/` directory and is loaded through `design.ini` `[StylesheetSettings]`, so the ezjscore
   packer bundles and caches it. The few documented exceptions are listed below.
   - A value that really is per element (a bar's height, a percentage) is a class (`.a4-pct-0` to `.a4-pct-100`) or
     a `data-*` attribute that a stylesheet or the page's script reads, never `style=""`.
2. **Use the shared component classes first** (`stylesheets/components.css`, prefix `.a4-`). Add a new shared
   component centrally only if nothing fits; never copy one into a module or an extension.
3. **Colours, radius, spacing and fonts come only from the `--a4-*` tokens.** No hard-coded values (`#fff`,
   `#c2410c`, `12px`, `.6em`) and no local aliases (`--rl-ink`, `--rad-card`). There is one accent and one
   component radius; do not add another.
4. **Every colour token has a value for `html[data-a4-theme="dark"]`**, added in the same commit as the token.
5. **Module CSS is the exception**: only what the components cannot do, in
   `stylesheets/modules/<group>.css`, as `.a4-<module>-*` classes, built from tokens. One prefix (`.a4-`) for the
   whole administration; no per-module scope classes.
6. **Check every new or changed page in light and dark mode**, at 1440 px (scale 1) and at 960 px with scale 2 (a
   browser at 200% zoom): no light card on the dark page, text contrast at least 4.5:1, no horizontal overflow, no
   console errors.
7. **CSS reaches the browser through the packer.** Deploy with `exp:velocity deploy --packer` (it clears the
   template-block cache that holds the packed link). Never add `?v=` or another version query to a link; a browser
   that still shows the old file needs a hard reload.

These rules apply to every administration design and to the admin designs of extensions (`extension/*/design/admin*`,
`exp_adminui` and the rest).

## How the stylesheets are loaded

`settings/design.ini`:

```ini
[StylesheetSettings]
AdminComponentCSSFileList[]
AdminComponentCSSFileList[]=components.css
AdminComponentCSSFileList[]=modules/setup.css
AdminComponentCSSFileList[]=modules/content.css
AdminComponentCSSFileList[]=modules/people.css
AdminComponentCSSFileList[]=modules/structure.css
AdminComponentCSSFileList[]=modules/workflow.css
```

`page_head_style.tpl` of `admin`, `admin3` and `admin4` loads the list after the design's own stylesheets (and, in
admin4, after `admin4.css`) and before `BackendCSSFileList`, so an extension still has the last word on its own
markup. Every file is named in the INI: every admin page gets the same bundle, packed and cached once, instead of a
bundle per module.

| File | Holds |
|---|---|
| `components.css` | the tokens (light and dark) and the `.a4-*` components; the copy in `design/admin` is identical (the test checks it) |
| `modules/setup.css` | setup/* |
| `modules/content.css` | content/* |
| `modules/people.css` | role, section, state, user, notification, collaboration |
| `modules/structure.css` | class, pdf, rss, package |
| `modules/workflow.css` | workflow, trigger, audit, shop, settings, infocollector, search, url, visual, contentfinder |

Each module file exists in `design/admin4/stylesheets/modules/` and `design/admin/stylesheets/modules/` (`admin2`
and `admin3` find the `design/admin` files through their fallback). `admin4.css` stays the frame of admin4 (header,
columns, dashboard, sign-in page).

**Extensions** keep their stylesheet in their own design folder and add it to `BackendCSSFileList[]`, not to
`AdminComponentCSSFileList`. In admin4 they can use the `.a4-*` classes and the `--a4-*` tokens; their own classes
take their own prefix and the same rules (tokens only, a dark value for every colour).

## Tokens

All `var(--a4-...)`, defined in `components.css`.

| Group | Tokens |
|---|---|
| Accent | `--a4-accent` (`#f26a21`), `--a4-accent-hover`, `--a4-accent-strong` (filled controls with white text, 5.2:1), `--a4-accent-press` (their hovered fill), `--a4-accent-text` (the accent as text or link), `--a4-accent-soft` (tint), `--a4-on-accent`, `--a4-on-accent-hover` (text on the `-press` fill), `--a4-ring` (focus ring, opaque: 3:1 on every surface) |
| Neutral | `--a4-ink`, `--a4-muted`, `--a4-line`, `--a4-line-strong`, `--a4-soft`, `--a4-card`, `--a4-surface`, `--a4-field`, `--a4-field-line`, `--a4-disabled`, `--a4-overlay`, `--a4-code-bg`, `--a4-code-ink` |
| States | `--a4-ok`, `--a4-ok-bg`, `--a4-ok-line`; `--a4-warn`, `--a4-warn-bg`, `--a4-warn-line`; `--a4-bad`, `--a4-bad-bg`, `--a4-bad-line`, `--a4-bad-hover`; `--a4-info`, `--a4-info-bg`, `--a4-info-line` |
| Charts | `--a4-chart-1` to `--a4-chart-8`, a categorical palette in this order |
| Depth | `--a4-shadow-s`, `--a4-shadow-m`, `--a4-shadow-up` |
| Radius | `--a4-radius-s` (9 px): the one component radius. `--a4-radius` (18 px) is the frame's three columns only |
| Spacing | `--a4-space-1` to `--a4-space-6` (4, 8, 12, 16, 24, 32 px) |
| Type | `--a4-font`, `--a4-mono`, `--a4-text-xs`, `-s`, `-m`, `-l`, `-xl` (12, 13, 14, 16, 22 px), `--a4-leading` (1.5) |

Every colour, chart and depth token has a dark value; the radius, spacing and type tokens are the same in both modes.
`--a4-orange`, `--a4-orange-dark` and `--a4-orange-soft` are the accent's former names, kept for the extensions that
use them; new code uses the `--a4-accent*` names.

**The accent** is the brand orange `#f26a21`, as on admin4's header (the selected menu pill) and its default button.
White text on it is only 3:1, so filled controls with white text use its darker shade `--a4-accent-strong`
(`#c2410c`, 5.2:1) and accent-coloured text uses `--a4-accent-text` (`#c2410c` light, `#ff9a5c` dark).

## Components

| Component | Classes |
|---|---|
| Page scope | `.a4-page` wraps a module page: the box-sizing reset, font, `[hidden]`, links, the focus ring on `:focus-visible`. `.a4-standalone`: a page drawn without the main card |
| Utilities | `.a4-sr-only`, `.a4-focus`, `.a4-muted`, `.a4-meta`, `.a4-help`, `.a4-intro`, `.a4-num`, `.a4-nowrap`, `.a4-wrap`, `.a4-plain`, `.a4-code`, `.a4-ok-text`, `.a4-warn-text`, `.a4-bad-text`, `.a4-middle`, `.a4-flag`, `.a4-end`, `.a4-inset`, `.a4-overflow-visible` |
| Headings | `.a4-h2`, `.a4-h3` |
| Layout | `.a4-section`, `.a4-section-head`, `.a4-title-row`, `.a4-row` (`.is-between`), `.a4-stack`, `.a4-grid` (`.is-narrow`, `.is-wide`) |
| Card | `.a4-card` (`.is-soft`, `.is-selected`, `.is-attention`, `.is-warn`, `.is-bad`, `.is-ok`, `.is-info`), `.a4-cards`, `.a4-card-head`, `.a4-card-title`, `.a4-card-body`, `.a4-pick` (the selection checkbox) |
| Figures, facts | `.a4-figures` > `.a4-figure` (`.is-attention`, `.is-bad`, `.is-ok`, `.is-warn`, `.is-info`, `.is-current`); `dl.a4-facts` (`.is-rows`) |
| Tag, badge, status | `.a4-tag`, `.a4-badge` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`, `.is-mono`), `.a4-badges`; `.a4-status` (`.is-ok`, `.is-running`, `.is-warn`, `.is-bad`) with `.a4-dot` |
| Buttons | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-outline-danger`, `.a4-btn-ghost`, `.a4-btn-small`, `.a4-btn-large`, `.a4-btn-icon`; `.a4-actions` (a row of them) |
| Toolbar | `.a4-toolbar` (an action bar), `.is-bottom` (under a list), `.is-sticky` (stays at the window's bottom) |
| Filter bar | `.a4-filterbar` (a grid of fields above a list), `.a4-filter-count` |
| Forms | `.a4-field`, `.a4-field-wide`, `.a4-label`, `.a4-input` (`.is-auto`), `.a4-field-error`, `.a4-form` (`.is-wide`), `.a4-formrow` (label beside control), `.a4-searchrow`, `.a4-check`, `.a4-radios`, `.a4-chips` > `.a4-chip` |
| Tables | `.a4-table-wrap` > `table.a4-table` (`.is-compact`), `tr.is-selected` |
| Under a list | `.a4-listfoot`, `.a4-sizes`, `.a4-pager`; `.a4-tabs` (links, buttons, `span.current`, `a.current`, `.is-current`), `.a4-count` |
| Empty state, notices | `.a4-empty`; `.a4-notice` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`) |
| Folding | `details.a4-fold` > `summary` + `.a4-fold-body` (`.is-bad`, `.is-warn`); `details.a4-confirm` (a confirmation that opens in place) |
| Steps | `ol.a4-steps` > `li.is-done` / `li.is-current` |
| Code | `.a4-pre` (`.is-tall`), `.a4-kbd` |
| Amounts | `.a4-meter` > `span`, `.a4-bars` > children, sized by `.a4-pct-0` ... `.a4-pct-100` (`--a4-pct`) |
| Icons | `.a4-icon` with one `.a4-bi-<name>` (a Bootstrap icon of `share/icons/bootstrap-icons/svg/`, in `currentColor`; `.is-small`, `.is-medium`, `.is-large`); `.a4-icon-btn` (a link or button that is only an icon) |
| Other | `.a4-grip` (drag handle), `.is-dragging` |

States are `.is-*` classes on the component. Each component is one class plus at most an element or a state, so a
module rule with one more class always wins.

Example, a list page:

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

## Dark mode

admin4's mode is `html[data-a4-theme="light"|"dark"]`, set before the page is drawn by the script at the top of
`page_head_style.tpl` from the browser's choice (local storage `exp-admin4-theme`); the header's toggle switches it.
Since 2026-10-14 the frame's columns are dark in dark mode too (`--a4-card-bg` follows `--a4-surface`), so anything
that paints its own background must take it from a token. A component or module rule never needs a
`html[data-a4-theme="dark"]` rule of its own: the tokens switch.

## Exceptions

Inline CSS that stays, each documented where it is:

| Where | Why |
|---|---|
| admin4 `page_head_style.tpl`: the inline script at the top | sets `data-a4-theme` before anything is drawn (a page drawn light and then switched would flash), and the left column's hand-dragged width from `data-a4-left-width` on `<html>`. A script, not CSS. |
| admin3 `pagelayout.tpl`: a two-line script | the hand-dragged left column width, as above (admin3 has no theme script) |
| `design/admin/templates/pagelayout.tpl`: one `<style>` block | the column widths of the editor's preferences, in em from `menu.ini [LeftMenuSettings] MenuWidth` or a dragged width, which a stylesheet cannot know, needed before the page is drawn |

The left column's preset sizes (small, medium, large) are `html[data-a4-left-size]` rules in `pagelayout.css`.

## Checked in CI

`tests/tests/design/AdminStylingRulesTest.php` (testsuite `design`, no database) fails when:

- a template of `admin4`, `admin4l`, `admin3`, `admin2` or `admin` has a `<style>` block or a `style=""` attribute
  (template comments are not read), outside the exceptions above and the `ALLOWLIST` of templates still being
  migrated. The allowlist only shrinks: a listed file without inline CSS fails until it is taken off. It is empty
  when the migration is complete;
- a rule in `stylesheets/modules/*.css` (admin4 and admin) has a colour literal, a radius that is not a token, `0` or
  `50%`, a spacing length that is not a token (`0`, `1px`, `2px`, `auto` and `calc()` of tokens are allowed), a font
  family or pixel font size of its own, a custom property of its own, or a selector without an `.a4-` class;
- a colour token in `components.css` has no dark value, or the admin and admin4 copies differ;
- `admin4.css` or one of admin4's page stylesheets (`ezinfo.css`, `packagecompare.css`, `setup-extensions.css`,
  `visual-templateview.css`) has a colour literal outside a token definition;
- a `:focus` or `:focus-visible` rule of an admin4 stylesheet sets `outline` to `none` or `0`. The global ring
  (`outline: 3px solid var(--a4-ring)`, offset 2px) is the focus indicator; a field that draws its ring as a
  `box-shadow` keeps `outline: 2px solid transparent`, which forced-colors mode paints where it drops the shadow;
- a file named in `AdminComponentCSSFileList` is missing.

`tests/tests/design/AdminTokenContrastTest.php` computes the WCAG contrast of the token pairs in both modes: text
tokens on card, surface and soft 4.5:1, each state's text on its own tint 4.5:1, the text of filled buttons (also
hovered) 4.5:1, the ring and the field edge 3:1 on every surface and the page, the chart colours 3:1 on a card.

Never weaken the test or add an allowlist entry to make a change pass; move the CSS.

```bash
php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
```

## After a stylesheet change

```bash
./console exp:velocity deploy --packer --allow-root-user
```

New templates also need the template-override cache cleared (the deploy does it). A browser that still shows the
old look needs a hard reload (Ctrl+Shift+R); the packed file's name carries the modification time.

## Code review checklist

For every template or stylesheet change in an administration design or an extension's admin design:

- [ ] No `<style>` block and no `style=""` attribute was added (also not in a JavaScript string that writes markup).
- [ ] The page uses the shared `.a4-*` components; nothing in it copies a component under another name.
- [ ] New CSS is in `stylesheets/modules/<group>.css` (core) or the extension's own stylesheet, with `.a4-<module>-*`
      (or the extension's) class names, and holds only what the components cannot do.
- [ ] Icons are `.a4-icon` with a `.a4-bi-*` class: no GIF or PNG icon through `ezimage`, no inline `<svg>`, no
      `<input type="image">` (`tests/tests/design/AdminIconsTest.php` checks admin4).
- [ ] Colours, radius, spacing and fonts are `var(--a4-*)` tokens: no `#hex`, `rgb()`, named colour, px radius or
      spacing literal, and no local custom properties.
- [ ] A new token is in `components.css` with a light and a dark value, and documented on this page.
- [ ] A new module stylesheet is listed in `AdminComponentCSSFileList` and exists in `design/admin4` and `design/admin`.
- [ ] The page was checked in light and dark mode at 1440 px and at 960 px with scale 2: no light card on the dark
      page, text contrast at least 4.5:1, no horizontal overflow, no console errors.
- [ ] `php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php` passes, and the allowlist only shrank.
- [ ] Deployed with `--packer`; no version query added to a link.

## Related pages

- [The admin4 design](../../features/6.0/admin4-design.md)
- [admin4l: the admin page built from layouts](admin4l.md)
- [Contributors guidelines](../../../CONTRIBUTING.md)
