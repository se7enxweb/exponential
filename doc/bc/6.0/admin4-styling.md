# Styling the administration: one component stylesheet, tokens, no inline CSS

Read this page before you write or review a template or stylesheet of an administration design (`admin`, `admin3`,
`admin4`, `admin4l`) or of an extension's admin design (some extensions still carry an `admin2` folder). It holds the rules, the shared component and token
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

Each module file exists in `design/admin4/stylesheets/modules/` and `design/admin/stylesheets/modules/` (`admin3`
finds the `design/admin` files through its fallback). `admin4.css` stays the frame of admin4 (header,
columns, dashboard, sign-in page).

**Extensions** keep their stylesheet in their own design folder and add it to `BackendCSSFileList[]`, not to
`AdminComponentCSSFileList`. In admin4 they can use the `.a4-*` classes and the `--a4-*` tokens; their own classes
take their own prefix and the same rules (tokens only, a dark value for every colour).

## Tokens

All `var(--a4-...)`, defined in `components.css`.

| Group | Tokens |
|---|---|
| Accent | `--a4-accent` (`#f26a21`), `--a4-accent-hover`, `--a4-accent-strong` (filled controls with white text, 5.2:1), `--a4-accent-press` (their hovered fill), `--a4-accent-text` (the accent as text or link), `--a4-accent-soft` (tint), `--a4-on-accent`, `--a4-on-accent-hover` (text on the `-press` fill), `--a4-ring` (focus ring, opaque: 3:1 on every surface) |
| Neutral | `--a4-ink`, `--a4-muted`, `--a4-line`, `--a4-line-strong`, `--a4-soft`, `--a4-card`, `--a4-surface`, `--a4-zebra` (the even rows of data tables), `--a4-field`, `--a4-field-line`, `--a4-disabled`, `--a4-overlay`, `--a4-code-bg`, `--a4-code-ink` |
| States | `--a4-ok`, `--a4-ok-bg`, `--a4-ok-line`; `--a4-warn`, `--a4-warn-bg`, `--a4-warn-line`; `--a4-bad`, `--a4-bad-bg`, `--a4-bad-line`, `--a4-bad-hover`; `--a4-info`, `--a4-info-bg`, `--a4-info-line` |
| Charts | `--a4-chart-1` to `--a4-chart-8`, a categorical palette in this order |
| Depth | `--a4-shadow-s`, `--a4-shadow-m`, `--a4-shadow-up` |
| Radius | `--a4-radius-s` (9 px): the one component radius. `--a4-radius` (18 px) is the frame's three columns only |
| Spacing | `--a4-space-1` to `--a4-space-7` (4, 8, 12, 16, 24, 32, 48 px); a length between two steps takes the nearer one (3 and 5 → 4, 6 to 9 → 8, 10 to 14 → 12, 15 to 20 → 16, 22 to 28 → 24) |
| Controls | `--a4-control-h` (32 px): one height for the buttons, selects and pager buttons that stand in a row (the node's actions, the sub-items toolbar and pagers) |
| Density | `--a4-list-pad` (`.4em`, the cells of `table.list`), `--a4-cell-pad-y`/`-x` (8/12 px, `.a4-table`), `--a4-card-pad-y`/`-x` (12/16 px, `.a4-card`). `html[data-a4-density="compact"]` (the header's density switch) sets only these and `--a4-control-h` anew: `.25em`, 4/8 px, 8/12 px, 28 px. Compact therefore tightens `table.list` cells, `.a4-table`, `.a4-card` and the controls in a row; it does not touch `table.special`, the header row height of content.css or the content tree. A component that should follow the density reads these tokens; nothing else may change with it, and every target keeps 24 px (`AdminDensityTest`). The content tree keeps its row pitch. |
| Type | `--a4-font`, `--a4-mono`, `--a4-text-xs`, `-s`, `-m`, `-l`, `-xl`, `-2xl`, `-3xl` (12, 13, 14, 16, 22, 26, 32 px; nothing below 12), `--a4-leading` (1.5, every block that can wrap), `--a4-leading-tight` (1.25, headings only) |

Every colour, chart and depth token has a dark value; the radius, spacing, control and type tokens are the same in both
modes.

**Tables.** Every data table is striped the same way: `table.list`, the `.a4-table` component and the sub-items table
(`exp/datatable`, through `--exp-dt-row-alt`) paint every second data row with `--a4-zebra`, the hovered row with
`--a4-info-bg`, a selected row (`.a4-table tr.is-selected`) with `--a4-accent-soft`; a row header (`th scope="row"`)
takes its row's colour. Only rows with data cells count (`tr:where(:nth-child(even of :has(> td):not([hidden])))`): a header
row of `th` inside `tbody` or a row with the `hidden` attribute is skipped, so the first data row is always plain. The
`bglight`/`bgdark` classes the templates still write are ignored for the colour. A row hidden by a script
(`display: none`) still counts; that is a known limit of CSS striping. A table used for layout carries none of these
classes and stays plain; a form laid out as a `table.list` takes `a4-plain` as well (`shop/editvatrule.tpl`,
`shop/editcurrency.tpl`). Text on a zebra row and on a hovered row keeps 4.5:1 (`AdminTokenContrastTest`), and no
admin4 stylesheet paints rows in a colour of its own (`AdminStylingRulesTest::testDataTablesShareOneZebra`).
Cells of a data table sit in the middle of their row (`.a4-plain` form tables keep their labels at the top). A row
whose selection checkbox (a checkbox straight in one of its cells) is ticked takes `--a4-accent-soft` without a script
(`tr:has(> td > input[type="checkbox"]:checked)`), over stripe and hover. List headers are not sticky: almost every
table sits in a horizontally scrolling wrapper (`pagelayout.css`, `div:has(> table)`), and a sticky header binds to
that wrapper, not to the page. An empty list says so in `.a4-empty`, never in a plain paragraph
(`AdminStylingRulesTest::testEmptyListsUseTheEmptyState`).
`--a4-orange`, `--a4-orange-dark` and `--a4-orange-soft` are the accent's former names, kept for the extensions that
use them; new code uses the `--a4-accent*` names.

**The accent** is the brand orange `#f26a21`, as on admin4's header (the selected menu pill) and its default button.
White text on it is only 3:1, so filled controls with white text use its darker shade `--a4-accent-strong`
(`#c2410c`, 5.2:1) and accent-coloured text uses `--a4-accent-text` (`#bc3f0b` light, 4.7:1 also on a hovered table row; `#ff9a5c` dark).

### Stacking layers

Stacking order comes from one scale in `components.css`; a stylesheet writes `z-index: var(--a4-z-…)`, never a
number (0, 1 and -1 for order inside one component excepted). Theme-independent, no dark value.

| Token | Value | For |
|---|---|---|
| `--a4-z-raised` | 2 | an item over its neighbours (a hovered thumbnail, a drop marker) |
| `--a4-z-sticky` | 5 | sticky toolbars and notices in the content |
| `--a4-z-frame-grip` | 9 | the classic frame's width grip, under its show/hide links |
| `--a4-z-frame-control` | 11 | the classic frame's show/hide links and width handler |
| `--a4-z-hover-preview` | 50 | a thumbnail enlarged on hover, over sticky bars and grips |
| `--a4-z-popover` | 60 | a pane inside the header or the control bar (search scope) |
| `--a4-z-controlbar-top` | 60 | the sticky top control bar of the edit pages |
| `--a4-z-scroll-top` | 100 | the back-to-top button |
| `--a4-z-navbar` | 110 | the phone-width menu |
| `--a4-z-sidebar` | 120 | side columns open over the page |
| `--a4-z-sidebar-handle` | 140 | the left column's width grip |
| `--a4-z-controlbar` | 200 | the fixed control bar of edit pages |
| `--a4-z-drag-target` | 999 | the drop target when a table column is dragged |
| `--a4-z-header` | 1020 | the fixed header |
| `--a4-z-sidebar-toggle` | 1030 | the tabs on the window edges (inside the fixed header: ordered among its children) |
| `--a4-z-menu` / `--a4-z-menu-sub` | 1040 / 1050 | context menus and their submenus, above the header |
| `--a4-z-overlay` / `--a4-z-modal` | 1060 / 1070 | a dialog's mask and the dialog (also the base rule of `theme/modalwindow.css`) |
| `--a4-z-toast` | 1080 | short-lived notices |
| `--a4-z-drag` / `--a4-z-drag-mask` | 9000 / 9500 | column drag and resize proxies of the data tables |
| `--a4-z-skip-link` | 10000 | the skip link while it is focused, over everything |
| `--a4-z-card-veil` / `--a4-z-card-spinner` | 2 / 3 | the busy veil of a card and its spinner, inside the card |

Inside a parent with its own stacking context (the fixed header, a transformed card) a token only orders against that
parent's children. `AdminStylingRulesTest::testStackingComesFromTheLayerTokens` fails on any other z-index and on a
change of the order sticky < grip < frame controls < hover preview < popover < header < tabs < menus < mask < dialog <
toasts, and on a `var(--a4-z-…)` that `components.css` does not define (a typo would fall back to `auto`).

## Components

| Component | Classes |
|---|---|
| Page scope | `.a4-page` wraps a module page: the box-sizing reset, font, `[hidden]`, links, the focus ring on `:focus-visible`. `.a4-standalone`: a page drawn without the main card |
| Utilities | `.a4-sr-only`, `.a4-skip-link` (the frame's skip link, visible only while focused), `.a4-focus`, `.a4-muted`, `.a4-meta`, `.a4-help`, `.a4-intro`, `.a4-num`, `.a4-nowrap`, `.a4-wrap`, `.a4-plain`, `.a4-code`, `.a4-ok-text`, `.a4-warn-text`, `.a4-bad-text`, `.a4-middle`, `.a4-flag`, `.a4-end`, `.a4-inset`, `.a4-overflow-visible` |
| Headings | `.a4-h2`, `.a4-h3` |
| Layout | `.a4-section`, `.a4-section-head`, `.a4-title-row`, `.a4-row` (`.is-between`), `.a4-stack`, `.a4-grid` (`.is-narrow`, `.is-wide`) |
| Page header | `.a4-pagehead` > `.a4-pagehead-main` (`.a4-pagehead-pre`, `.a4-pagehead-title`, `.a4-pagehead-sub`) + `.a4-pagehead-actions` (right, under the title on narrow windows); the one title style of every page (`h1.context-title`, the node view and the dashboard take the same size) |
| Breadcrumb | `ol.a4-crumbs` (`role="list"`) > `li` (`.is-middle`, `.is-fold`: the middle steps fold into "..." up to 600px, visually only, a focused step shows again), separators from the stylesheet, the current step `aria-current="page"`, inside the `#path` navigation landmark |
| Card | `.a4-card` (`.is-soft`, `.is-selected`, `.is-attention`, `.is-warn`, `.is-bad`, `.is-ok`, `.is-info`, `.is-flush`, `.is-link`, `.is-busy`), `.a4-cards`, `.a4-card-head` (title, a `<p>` as subtitle, `.a4-card-actions` at its end), `.a4-card-title`, `.a4-card-body`, `.a4-card-foot` (the card's actions on a strip at its bottom edge), `.a4-pick` (the selection checkbox) |
| Figures, facts | `.a4-figures` > `.a4-figure` (`.is-attention`, `.is-bad`, `.is-ok`, `.is-warn`, `.is-info`, `.is-current`); `dl.a4-facts` (`.is-rows`) |
| Tag, badge, status | `.a4-tag`, `.a4-badge` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`, `.is-accent`, `.is-mono`), `.a4-badges`; `.a4-status` (`.is-ok`, `.is-running`, `.is-warn`, `.is-bad`) with `.a4-dot` |
| Buttons | `.a4-btn`, `.a4-btn-primary`, `.a4-btn-danger`, `.a4-btn-outline-danger`, `.a4-btn-ghost`, `.a4-btn-small`, `.a4-btn-large`, `.a4-btn-icon`; `.a4-actions` (a row of them) |
| Toolbar | `.a4-toolbar` (an action bar), `.is-bottom` (under a list), `.is-sticky` (stays at the window's bottom) |
| Filter bar | `.a4-filterbar` (a grid of fields above a list), `.a4-filter-count` |
| Forms | `.a4-field`, `.a4-field-wide`, `.a4-label`, `.a4-input` (`.is-auto`), `.a4-field-error`, `.a4-form` (`.is-wide`), `.a4-formrow` (label beside control), `.a4-searchrow`, `.a4-check`, `.a4-radios`, `.a4-chips` > `.a4-chip`, `.a4-inline-label` (a label around text that was plain before: an inline radio or checkbox with its text, or the words in front of a control in a table cell; drawn like the text around it) |
| Forms | `.a4-field`, `.a4-field-wide`, `.a4-label`, `.a4-input` (`.is-auto`), `.a4-field-error`, `.a4-required-mark` + `.a4-required-hint` (required fields), `.a4-error-summary` (the summary of a failed store), `.a4-form` (`.is-wide`), `.a4-formrow` (label beside control), `.a4-searchrow`, `.a4-check`, `.a4-radios`, `.a4-chips` > `.a4-chip` |
| Tables | `.a4-table-wrap` > `table.a4-table` (`.is-compact`), `tr.is-selected` |
| Under a list | `.a4-listfoot`, `.a4-sizes`, `.a4-pager`; `.a4-tabs` (links, buttons, `span.current`, `a.current`, `.is-current`; `.is-segmented`: joined into one compact switch, a pressed button is `aria-pressed="true"`; `.is-icons`: square icon buttons, each named by `.a4-sr-only` text and its `title`), `.a4-count` |
| Empty state, notices | `.a4-empty`; `.a4-notice` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`: each with its icon left of the text, `.no-icon` leaves it out; `.a4-notice-title` for a bold first line) |
| Toasts | `.a4-toasts` (the two live regions of the frame, `#a4-toasts` polite and `#a4-toasts-alert` assertive) and `.a4-toast` (`.is-ok`, `.is-warn`, `.is-bad`); added only by `window.a4Toast( message, kind )` from `admin4.js` |
| Folding | `details.a4-fold` > `summary` + `.a4-fold-body` (`.is-bad`, `.is-warn`); `details.a4-confirm` (a confirmation that opens in place) |
| Steps | `ol.a4-steps` > `li.is-done` / `li.is-current` |
| Timeline | `ol.a4-timeline` > `li` (`.is-ok`, `.is-warn`, `.is-bad`, `.is-info`) > `time.a4-timeline-time` (with `datetime`) + `.a4-timeline-body` (`.a4-timeline-meta`, a state badge); for short chronological lists without actions per row, newest first; the dot repeats the state a badge says in words. Audit events: `parts/audit_timeline.tpl` (`events`, `show_reason`) |
| Code | `.a4-pre` (`.is-tall`), `.a4-kbd` |
| Amounts | `.a4-meter` > `span`, `.a4-bars` > children, sized by `.a4-pct-0` ... `.a4-pct-100` (`--a4-pct`) |
| Language | `.a4-lang` (a content language's short code, from `design:parts/language_badge.tpl`; `.is-current` for the one shown), `.a4-langs` (several in a row) |
| Icons | `.a4-icon` with one `.a4-bi-<name>` (a Bootstrap icon of `share/icons/bootstrap-icons/svg/`, in `currentColor`; `.is-small`, `.is-medium`, `.is-large`); `.a4-icon-btn` (a link or button that is only an icon) |
| Other | `.a4-grip` (drag handle), `.is-dragging` |

A card opens with `.a4-card-head` (not `.a4-section-head`), puts the rest in `.a4-card-body` and its buttons in
`.a4-card-foot`. The foot and the flush head read the card's padding from `--a4-card-pad-x`/`--a4-card-pad-y` when a
density setting defines them, else from the spacing scale (`--a4-space-4`/`--a4-space-3`). `.is-flush` lets a table
run to the card's edges (head and foot keep their padding, the table's corner cells take the card's radius).
`.is-link` makes the whole card follow the link in its `.a4-card-title`: links, buttons and fields in the body, foot
and head actions stay clickable, but plain text under the link layer cannot be selected, so a link card holds no text
people copy; where the browser has `:has()` the focus ring goes around the card, elsewhere around the title link.
`.is-busy` works together with `aria-busy="true"` on the same element and veils the card with a spinner while its
content loads or is saved; the veil stops only the pointer, so the script that sets it also sets `inert` on the card
(or disables its controls) and removes both afterwards. `aria-busy` alone announces nothing: use
`window.a4CardBusy( card, on, message )` (admin4.js), which sets and removes `aria-busy` and `inert` together and
puts the message ("Saving...", then the result) in a polite live region outside the card. The title of a link card
holds exactly one link, whose text is the card's accessible name; never wrap a card in `<a>`.
`tests/tests/design/AdminCardsTest.php` checks the parts and
that no admin4 card opens with a section head.

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

## Legacy controls and the side columns

The kernel's module templates still write `input.button`, `input.defaultbutton`, `input.button-disabled`,
`button.button`, and selects and text fields without a class. Inside `#page`, `admin4.css` gives them the size of the
`.a4-btn` components and of the node's action row: `min-height: var(--a4-control-h)` (28px in compact density), the
type scale's small step and the space tokens; a select gets the field's edge and a chevron drawn in `--a4-muted`, and
forced colors get the native select back. The selectors keep the base rules' weight (element plus class, the scope
inside `:where()`), so a rule with one more class (the node's action row, the edit bars, `.a4-input`, an extension's
own control) always wins; a select or field with a class of its own is not touched. New markup still uses `.a4-btn`
and `.a4-field`.

Side columns: the rule between blocks is drawn only above a heading that follows a block, whatever the menu template
nests; block titles have one size (1.25em of the column's font, hyphenated); link lists have no bullets. Every left
menu of admin4 ends with `design:parts/left_width_control.tpl` (three icon buttons in a POST form, the current width
pressed) and a tree menu shows its trash link through `design:parts/left_trash.tpl`: include them, never copy their
markup (`AdminLegacyControlsTest`).

## Dark mode

admin4's mode is `html[data-a4-theme="light"|"dark"]`, set before the page is drawn by the script at the top of
`page_head_style.tpl` from the user's choice (`window.a4Pref`: the eZPreference `admin_color_mode`, the browser on an
anonymous page); the header's toggle switches it. Every remembered choice of the frame (mode, density, side
columns, menu groups) goes through `window.a4Pref`; never write `localStorage` from a feature script
(`AdminPreferencesTest`).
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

- a template of `admin4`, `admin4l`, `admin3` or `admin` (in `templates/` or `override/templates/`) has a `<style>`
  block or a `style=""` attribute (template comments are not read), outside the exceptions above and the `ALLOWLIST` of templates still being
  migrated. The allowlist only shrinks: a listed file without inline CSS fails until it is taken off. It is empty
  when the migration is complete. A design on the test's list that does not exist fails the test, rather than being
  skipped;
- a rule in `stylesheets/modules/*.css` (admin4 and admin) has a colour literal, a radius that is not a token, `0` or
  `50%`, a spacing length that is not a token (`0`, `1px`, `2px`, `auto` and `calc()` of tokens are allowed), a font
  family or pixel font size of its own, a custom property of its own, or a selector without an `.a4-` class;
- a colour token in `components.css` has no dark value, or the admin and admin4 copies differ;
- `admin4.css` or one of admin4's page stylesheets (`ezinfo.css`, `packagecompare.css`, `visual-templateview.css`)
  has a colour literal outside a token definition;
- one of admin4's base stylesheets (`core.css`, `content.css`, `pagelayout.css`, `theme/rounded.css`,
  `theme/admin_datatable.css`, `theme/modalwindow.css`) has a colour literal outside a token definition. A legacy
  literal kept on purpose would go into `BASE_COLOUR_ALLOWLIST`, which only shrinks; it is empty. A rule admin4.css
  overrides today still takes a token, so the next selector that slips past the override cannot show an old colour;
- a `:focus` or `:focus-visible` rule of an admin4 stylesheet removes the outline (`none`, `hidden` or a width of
  `0`, in any order of the shorthand; `:focus:not(:focus-visible)` and `:focus-within` aside). The global ring
  (`outline: 3px solid var(--a4-ring)`, offset 2px) is the focus indicator. A frame field that draws its ring as a
  `box-shadow` (outside `.a4-page`) sets `outline: 2px solid transparent` instead of `none`, which forced-colors mode
  paints where it drops the shadow; inside `.a4-page` the components' own ring applies anyway. The check reads only rules whose selector names the focus: a plain rule that outranks the global ring (an id such as `#logintext { outline: none }`) is not seen, so give such a rule a `:focus` rule that restores the outline;
- `components.css` loses the `color-scheme` of a theme, its forced-colors or touch block, `admin4.css` its
  `prefers-reduced-motion` rule, or an admin4 stylesheet transitions `all` (name the properties instead);
- a state of a module stylesheet (`.is-current`, `.is-selected`, `.is-moved`, `.is-dirty`, `[aria-pressed="true"]`,
  `:checked`) that differs only by border, background or box-shadow has no `outline: 2px solid Highlight` in the
  file's `@media (forced-colors: active)` block: forced colors drop all three
  (`AdminBaseStylesheetsTest::testModuleStatesKeepAnOutlineInForcedColors`);
- a base stylesheet has a rule for `html[data-a4-theme]` (the tokens switch; a focus ring is `var(--a4-ring)` in both
  modes) or a vendor-prefixed or IE-only leftover (`-moz-`/`-ms-`/`-o-` transitions, shadows and gradients,
  `filter: alpha()`, `zoom: 1`, `linear-gradient(top, …)`) (`AdminBaseStylesheetsTest`);
- `components.css` or `admin4.css` sets a pixel font size, or a spacing length that is not a `--a4-space-*` token
  (`1px` and `2px` aside; a layout length is a token of its own);
- a file named in `AdminComponentCSSFileList` is missing.

No text is smaller than 12px on an admin4 page. The base and page stylesheets (core.css, pagelayout.css, content.css,
the Info, package and template pages) size text in em of the body, which core.css sets to `var(--a4-text-s)` (13px);
an em size that would land below 12px is `var(--a4-text-xs)` instead
(`AdminStylingRulesTest::testBaseAndPageStylesheetsDrawNoTextBelowTwelvePixels`).

Targets are at least 24 x 24px (WCAG 2.5.8). Where the drawn control stays smaller, an invisible `::after` around
it takes the click (the search scope switch); where that would cover a neighbour (the content tree's open/close,
20 x 20px) or the control is a native checkbox (18px), the spacing exception of 2.5.8 applies: keep rows at least
24px apart. A pixel length that is a layout length rather than spacing (the password field's room for its toggle) is a token of
its own with a dark value (`--a4-pw-toggle-room`); an offset is a calc() of the space tokens. The test keeps no
exception list.

The node view's tabs switch (`.tab-block .show-hide-tabs`, the ids `#maincontent-show` / `#maincontent-hide`, or
`aria-pressed` on a button) is a 36 x 24px switch with the visible label `.a4-tabs-switch-label` ("Tabs"); both float
at the right end of the tab row. Style it by those hooks, not by the element, so link and button look the same.

Modes the browser asks for: each theme sets `color-scheme` (scrollbars, select popups and date pickers follow);
forced colors (Windows high contrast) get edges, the current and selected states and the focus in system colours,
and data drawn as fills (dots, meters, bars) keeps its colours (`forced-color-adjust: none`);
`prefers-reduced-motion: reduce` ends every transition and animation at once; on a touch screen (`pointer: coarse`)
fields use `--a4-text-l` (16px) so a phone does not zoom in on focus.

`tests/tests/design/AdminTokenContrastTest.php` computes the WCAG contrast of the token pairs in both modes: text
tokens on card, surface and soft 4.5:1, each state's text on its own tint 4.5:1, the text of filled buttons (also
hovered) 4.5:1, the ring and the field edge 3:1 on every surface and the page, the chart colours 3:1 on a card, a
meter's fill on its track 3:1 (`.a4-meter` fills with `--a4-accent-text`: `--a4-accent` was 2.9:1 on the light track,
`--a4-accent-strong` 2.8:1 on the dark one).

Printing: `admin4.css` has an `@media print` block (the content on the full width, no frame, no shadows), and the
head script switches `html[data-a4-theme]` to light before printing, so a print style needs no colours of its own.

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
- [ ] A popup or menu reuses the context menu (`ezpopupmenu.js`, `div.popupmenu`): it gets the menu roles and the
      keyboard handling there; nothing opens a menu that only a mouse reaches (`AdminContextMenuTest`).
- [ ] A control that has no visible text has a name (`aria-label`, or `.a4-sr-only` text inside it); a header cell
      has `scope`; a new frame column keeps its landmark role (`tests/tests/design/AdminLandmarksTest.php`).
- [ ] Every form field has a name: a `<label for>` on its id, a `<label>` around it, or `aria-labelledby` on text
      that is shown (a column header, the row's name, a card heading); `aria-label` only where no text is shown. A
      `title` or a `placeholder` is not a name. Around plain text use `<label class="a4-inline-label">`, which keeps
      the text's look. A field with a label that has text gets no `aria-label` beside it: it would replace the label's
      words (WCAG 2.5.3) (`tests/tests/design/AdminFormLabelsTest.php` checks admin4).
- [ ] Colours, radius, spacing and fonts are `var(--a4-*)` tokens: no `#hex`, `rgb()`, named colour, px radius or
      spacing literal, and no local custom properties.
- [ ] A new token is in `components.css` with a light and a dark value, and documented on this page.
- [ ] A z-index is a `--a4-z-*` token (or 0/1/-1 inside one component).
- [ ] A state shown only by border, background or shadow (current, selected, pressed) has an
      `outline: 2px solid Highlight` in the file's `@media (forced-colors: active)` block.
- [ ] A required field shows `.a4-required-mark` (`aria-hidden`) with the word in `.a4-sr-only`, and its control is
      `aria-required`; an invalid field shows its message (`.a4-field-error` with an id) and its control is
      `aria-invalid` with that id in `aria-describedby` (`tests/tests/design/AdminFormErrorsTest.php`).
- [ ] A new data table is a `table.list` or an `.a4-table` (zebra rows, hover); controls in one row share
      `--a4-control-h`.
- [ ] A notice that reports the result of an action has a role: `role="alert"` for an error (`.a4-notice.is-bad`,
      `div.message-error`), `role="status"` for the rest (a status region already in the page is usually not
      announced on load, but screen readers differ); a notice that explains a standing state has none. No
      hand-written glyph ("!", a tick) at the start of a notice: the state class draws the icon. Feedback of an action done in the page without reload (copied, stored by
      Ajax) is a toast (`window.a4Toast`), not a notice that appears; it never takes the focus (`AdminNoticesTest`).
- [ ] A new left-menu link (menu.ini `Links[]` of a section with `Groups[]`) is placed in a group
      (`GroupLinks_<group>[]`); otherwise it shows under "More" (`AdminLeftMenuGroupsTest` checks Leftmenu_setup).
- [ ] A new module stylesheet is listed in `AdminComponentCSSFileList` and exists in `design/admin4` and `design/admin`.
- [ ] The page was checked in light and dark mode at 1440 px and at 960 px with scale 2: no light card on the dark
      page, text contrast at least 4.5:1, no horizontal overflow, no console errors.
- [ ] `php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php` passes, and the allowlist only shrank.
- [ ] Deployed with `--packer`; no version query added to a link.

## Related pages

- [The admin4 design](../../features/6.0/admin4-design.md)
- [admin4l: the admin page built from layouts](admin4l.md)
- [Contributors guidelines](../../../CONTRIBUTING.md)
