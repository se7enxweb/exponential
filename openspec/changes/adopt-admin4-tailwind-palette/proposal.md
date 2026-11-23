## Why

admin4's colour tokens were picked one by one: a grey ramp of its own, an accent (`#f26a21`) a shade off the logo's
orange (`#f15a28`), state colours partly from Tailwind CSS, the environment badges from another kit, the code colours
from an editor theme. 141 distinct values, 36 of them from a known palette. Nothing keeps a new value in a family,
so each change adds a near-duplicate. One documented palette makes the values predictable for extension authors and
checkable by a test.

Ticket: none yet; this change is proposed as an upstream pull request to se7enxweb/exponential (the request comes from
the fork's owner, 23.11.2026).

## What Changes

- Every colour token of admin4 (`design/admin4/stylesheets/components.css`, `admin4.css`) and of the classic admin's
  copy of the tokens (`design/admin/stylesheets/components.css`) takes a colour of Tailwind CSS 3.4's default
  palette, white or black; translucent values take the red, green and blue of one of them.
- The greys are one ramp: `slate`.
- The accent and the brand token are the logo's orange `#f15a28`, the one colour outside the palette; its darker
  shades for text and filled controls are Tailwind's `orange-600` to `orange-800`.
- Contrast stays where it was or improves: every pair of `AdminTokenContrastTest` passes unchanged in both modes.
- A new test, `AdminTokenPaletteTest`, fails when a colour outside the palette, a grey outside `slate`, or an accent
  other than the logo's fill enters these stylesheets.
- The styling guide and the designers' help (English and German) give the new values and the palette rule.

No template, no PHP code, no INI setting changes. Token names stay; extensions that read `--a4-*` tokens follow
without a change. The look changes slightly (greys a touch bluer, the dark mode's surfaces a step darker).

## Capabilities

### New Capabilities

- `admin-colour-palette`: the administration's colours come from one documented palette, keep their contrast in both
  modes, and the logo's orange is the accent.

### Modified Capabilities

None.

## Impact

- `design/admin4/stylesheets/components.css`, `design/admin4/stylesheets/admin4.css`,
  `design/admin/stylesheets/components.css` (token values and their comments only); the close icon's stroke in
  `design/admin4/stylesheets/base.css` and `theme/modalwindow.css`.
- New `tests/tests/design/AdminTokenPaletteTest.php` and `tests/tests/design/fixtures/tailwind-palette-3.4.17.json`;
  `tests/test-map.json` names the test for the three stylesheets and the logo template.
- Docs: `doc/eng-us/bc/6.0/admin4-styling.md`, `doc/eng-us/help/designers/theme-design/admin4-tokens.md`,
  `color-contrast.md` and their German counterparts.

Out of scope:

- admin3's two literal `#f26a21` in `design/admin3/stylesheets/pagelayout.css` (admin3 is left as it is; whether it stays is a question of its own).
- The admin4 stylesheets that ship with extension packages (cjw_newsletter, explayouts, explayouts_ui,
  expimageeditor, admin4_bs_icons): they read the tokens; literal colours of their own are their packages' change.
- The front-end design `design/standard` and the screenshots of the designers' help (`admin4-tokens-light.webp`,
  `admin4-tokens-dark.webp`), which show the old colours until they are taken again.
