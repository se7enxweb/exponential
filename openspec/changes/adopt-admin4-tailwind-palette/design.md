## Context

admin4 draws every colour from `--a4-*` custom properties: the shared tokens (light on `:root`, dark on
`html[data-a4-theme="dark"]:not(:where([data-a4-modes="light"]))`) in `design/admin4/stylesheets/components.css`,
whose copy `design/admin/stylesheets/components.css` must stay identical (`AdminTokenContrastTest`), and the frame's
tokens (page, header, side columns, footer, brand, environment badge) in `design/admin4/stylesheets/admin4.css`.
No rule outside these token blocks writes a literal colour (the inventory found 195 literals, all in token
definitions; three hits in `base.css` were the selector `#feed-elements`), except the close icon of `base.css` and
`theme/modalwindow.css`, an inline SVG with `stroke='%238a8a8a'`, which becomes `slate-500` (4.8:1 on white, the old
grey was 3.5:1). The change is therefore a change of token
values; no selector, template or script is touched.

No extension carries the change: it is the kernel's own administration design. No `lib/` or `kernel/` file, no INI
setting, no policy and no database table is involved.

## Goals / Non-Goals

**Goals:** one palette (Tailwind CSS 3.4's default palette, `slate` for greys) plus the logo's orange; every contrast
pair of `AdminTokenContrastTest` still passing; a test that keeps it that way.

**Non-Goals:** a Tailwind build or Tailwind's utility classes (only its colour values are used; no new dependency);
new tokens or renamed tokens; admin3, the front-end design, or the admin4 stylesheets of extension packages.

## Decisions

### D1 Tailwind CSS 3.4's palette, as hex values

Tailwind 3.4's default palette (`lib/public/colors.js` of tailwindcss 3.4.17) is a fixed list of sRGB hex values in
22 families of 11 steps. Tailwind 4 defines its palette in `oklch()`; those are other values, and `oklch()` would
change how the tokens are written and computed by `AdminTokenContrastTest`. The 3.4 values are stored in
`tests/tests/design/fixtures/tailwind-palette-3.4.17.json` (with its source), so the test needs no network and no
Node package.

### D2 Greys: slate only

admin4's greys were bluish (`#1f2430`, `#545c6a`, `#c9cfd8`); `slate` is the bluish ramp, and each old grey has a slate
step within ΔE 9. One ramp, so `gray-800` (the explayouts navigation column) moves to `slate-800`.
`AdminTokenPaletteTest::testTheGreysAreSlate` rejects `gray`, `zinc`, `neutral` and `stone`.

### D3 The accent is the logo's orange; its shades are Tailwind's orange

`--a4-accent` was `#f26a21`, a shade off the logo artwork's `#f15a28` (`parts/a4_logo.tpl`). Both become `#f15a28`,
the only colour outside the palette. White on it is 3.4:1, as before (3.1:1), so filled controls with white text keep
using `--a4-accent-strong` (`orange-700`, 5.2:1). `--a4-accent-text` light moves from `#ad3809` to `orange-800`
(`#9a3412`): `orange-700` gave 4.2:1 on the table header (`slate-200`), below 4.5:1.

### D4 Nearest step, then the contrast decides

Each token takes the step of its family nearest in CIE Lab (ΔE) to its old value. Where a pair of
`AdminTokenContrastTest` failed, the next step of the same ramp is taken, never a value between steps:

- light `--a4-accent-text`: `orange-700` → `orange-800` (see D3);
- dark `--a4-card`: `slate-800` gave expui's accent outline (`--exp-accent` = `orange-700`) 2.8:1 on the card; with
  `slate-900` it is 3.4:1, and white on that fill keeps 5.2:1. The dark surfaces follow: page `slate-950`, columns
  and cards `slate-900`, table header `slate-800`, lines `slate-700`/`slate-600`;
- dark muted text on the table header was 4.0:1 on `slate-700`; with the header on `slate-800` it is 5.7:1.

### D5 Between two steps: a palette colour over the card

The dark mode had surfaces a few percent apart (`#1e242f` card, `#232a36` zebra, `#29313e` soft). Slate's dark steps are
further apart. `--a4-soft` (`slate-700` at 60 %) and `--a4-zebra` (`slate-800` at 60 %) are translucent over the card,
which keeps them between the steps; `AdminTokenContrastTest` already lays a translucent background over the card
before it measures. The dark side columns (`--a4-side-bg`) are `slate-900` at 60 % over the page in the same way.
Light mode needs no such tint.

### D6 States, charts, code and environment badge from their Tailwind families

States: `green`, `amber`, `red`, `blue` (light: `-800`/`-700` text, `-50` tint, `-200` edge; dark: `-300` text and
`-500` tint, as before). Charts: the `-600`/`-700` steps light and `-400` dark of blue, orange, emerald, amber, pink,
green, indigo/violet, red, in the old order. Code: the `-300` steps (`purple`, `green`, `orange`, `red`, `blue`, `teal`,
`amber`, `sky`, `lime`) on `slate-800` light and `slate-950` dark. Environment badge: development `emerald-700`, test
`amber-400` with `amber-950` text, staging `violet-700` (dark `violet-600`), production `red-700` (dark `red-600`);
every badge text keeps 4.5:1 on its fill (4.8:1 to 9.0:1).

### D7 A palette test next to the contrast test

`AdminTokenPaletteTest` scans the values (text after a colon, comments removed) of every stylesheet of
`design/admin4/stylesheets/` and of `design/admin/stylesheets/components.css`, normalises `#rgb`, `#rrggbbaa` and
`rgb()/rgba()` and the URL-encoded `%23rrggbb` of inline SVGs to `#rrggbb`, and requires each to be a palette colour, white, black or the logo's orange; greys
`slate` only; `--a4-accent` and `--a4-brand` equal the logo's fill. Against the old tokens it fails all four tests;
against the new ones it passes. Contrast stays `AdminTokenContrastTest`'s part, unchanged.

## Mapping

Old values from `upstream/main` before the change; Tailwind step and value after it.

**components.css**

| Token | Light: was → now | Dark: was → now |
|---|---|---|
| `--a4-accent` | `#f26a21` → logo `#f15a28` | `#f26a21` → logo `#f15a28` |
| `--a4-accent-hover` | `#d9561a` → orange-600 `#ea580c` | `#ff8a4c` → orange-400 `#fb923c` |
| `--a4-accent-strong` | `#c2410c` → orange-700 `#c2410c` | `#c2410c` → orange-700 `#c2410c` |
| `--a4-accent-press` | `#9a3412` → orange-800 `#9a3412` | `#ff8a4c` → orange-400 `#fb923c` |
| `--a4-accent-text` | `#ad3809` → orange-800 `#9a3412` | `#ff9a5c` → orange-400 `#fb923c` |
| `--a4-accent-soft` | `rgba(242, 106, 33, 0.12)` → logo at 12 % | `rgba(242, 106, 33, 0.18)` → logo at 18 % |
| `--a4-on-accent` | `#fff` → white `#fff` | `#fff` → white `#fff` |
| `--a4-on-accent-hover` | `#fff` → white `#fff` | `#0e1218` → slate-950 `#020617` |
| `--a4-ring` | `#c2410c` → orange-700 `#c2410c` | `#ff9a5c` → orange-400 `#fb923c` |
| `--a4-ink` | `#1f2430` → slate-800 `#1e293b` | `#e8ebf0` → slate-200 `#e2e8f0` |
| `--a4-muted` | `#545c6a` → slate-600 `#475569` | `#a3abb9` → slate-400 `#94a3b8` |
| `--a4-line` | `#c9cfd8` → slate-300 `#cbd5e1` | `#3a4456` → slate-700 `#334155` |
| `--a4-line-strong` | `#aab2bd` → slate-400 `#94a3b8` | `#55617a` → slate-600 `#475569` |
| `--a4-soft` | `#eaedf1` → slate-100 `#f1f5f9` | `#29313e` → slate-700 at 60 % |
| `--a4-card` | `#fff` → white `#fff` | `#1e242f` → slate-900 `#0f172a` |
| `--a4-surface` | `#fff` → white `#fff` | `#1a1f29` → slate-900 `#0f172a` |
| `--a4-zebra` | `#eceff3` → slate-50 `#f8fafc` | `#232a36` → slate-800 at 60 % |
| `--a4-head-bg` | `#dce1e8` → slate-200 `#e2e8f0` | `#323b4d` → slate-800 `#1e293b` |
| `--a4-field` | `#fff` → white `#fff` | `#121720` → slate-950 `#020617` |
| `--a4-field-line` | `#6e7588` → slate-500 `#64748b` | `#7a85a0` → slate-400 `#94a3b8` |
| `--a4-disabled` | `#9aa1ad` → slate-400 `#94a3b8` | `#6b7486` → slate-500 `#64748b` |
| `--a4-overlay` | `rgba(16, 24, 40, 0.45)` → slate-900 at 45 % | `rgba(0, 0, 0, 0.6)` → black at 60 % |
| `--a4-code-bg` | `#1f2430` → slate-800 `#1e293b` | `#0e1218` → slate-950 `#020617` |
| `--a4-code-ink` | `#e8ebf0` → slate-200 `#e2e8f0` | `#e8ebf0` → slate-200 `#e2e8f0` |
| `--a4-code-comment` | `#9aa5b8` → slate-400 `#94a3b8` | `#9aa5b8` → slate-400 `#94a3b8` |
| `--a4-code-keyword` | `#c792ea` → purple-300 `#d8b4fe` | `#c792ea` → purple-300 `#d8b4fe` |
| `--a4-code-string` | `#a5d6a7` → green-300 `#86efac` | `#a5d6a7` → green-300 `#86efac` |
| `--a4-code-number` | `#f9ae77` → orange-300 `#fdba74` | `#f9ae77` → orange-300 `#fdba74` |
| `--a4-code-variable` | `#f78c8c` → red-300 `#fca5a5` | `#f78c8c` → red-300 `#fca5a5` |
| `--a4-code-function` | `#82aaff` → blue-300 `#93c5fd` | `#82aaff` → blue-300 `#93c5fd` |
| `--a4-code-tag` | `#7fdbca` → teal-300 `#5eead4` | `#7fdbca` → teal-300 `#5eead4` |
| `--a4-code-attr` | `#ffcb6b` → amber-300 `#fcd34d` | `#ffcb6b` → amber-300 `#fcd34d` |
| `--a4-code-operator` | `#89ddff` → sky-300 `#7dd3fc` | `#89ddff` → sky-300 `#7dd3fc` |
| `--a4-code-constant` | `#f9ae77` → orange-300 `#fdba74` | `#f9ae77` → orange-300 `#fdba74` |
| `--a4-code-meta` | `#b4bcd0` → slate-300 `#cbd5e1` | `#b4bcd0` → slate-300 `#cbd5e1` |
| `--a4-code-section` | `#ffd580` → amber-200 `#fde68a` | `#ffd580` → amber-200 `#fde68a` |
| `--a4-code-inserted` | `#a5d6a7` → green-300 `#86efac` | `#a5d6a7` → green-300 `#86efac` |
| `--a4-code-deleted` | `#ff9e9e` → red-300 `#fca5a5` | `#ff9e9e` → red-300 `#fca5a5` |
| `--a4-code-property` | `#addb67` → lime-300 `#bef264` | `#addb67` → lime-300 `#bef264` |
| `--a4-code-type` | `#ffcb8b` → orange-200 `#fed7aa` | `#ffcb8b` → orange-200 `#fed7aa` |
| `--a4-ok` | `#166534` → green-800 `#166534` | `#86efac` → green-300 `#86efac` |
| `--a4-ok-bg` | `#e7f5ea` → green-50 `#f0fdf4` | `rgba(34, 197, 94, 0.14)` → green-500 at 14 % |
| `--a4-ok-line` | `#b7dfc1` → green-200 `#bbf7d0` | `rgba(134, 239, 172, 0.35)` → green-300 at 35 % |
| `--a4-warn` | `#8a4b00` → amber-800 `#92400e` | `#fcd34d` → amber-300 `#fcd34d` |
| `--a4-warn-bg` | `#fff3df` → amber-50 `#fffbeb` | `rgba(245, 158, 11, 0.14)` → amber-500 at 14 % |
| `--a4-warn-line` | `#f3d19c` → amber-200 `#fde68a` | `rgba(252, 211, 77, 0.35)` → amber-300 at 35 % |
| `--a4-bad` | `#b91c1c` → red-700 `#b91c1c` | `#fca5a5` → red-300 `#fca5a5` |
| `--a4-bad-bg` | `#fdecec` → red-50 `#fef2f2` | `rgba(239, 68, 68, 0.15)` → red-500 at 15 % |
| `--a4-bad-line` | `#f1b4b4` → red-200 `#fecaca` | `rgba(252, 165, 165, 0.38)` → red-300 at 38 % |
| `--a4-bad-hover` | `#7f1d1d` → red-900 `#7f1d1d` | `#fecaca` → red-200 `#fecaca` |
| `--a4-info` | `#1e4fa8` → blue-800 `#1e40af` | `#93b4f5` → blue-300 `#93c5fd` |
| `--a4-info-bg` | `#e8effd` → blue-50 `#eff6ff` | `rgba(59, 130, 246, 0.15)` → blue-500 at 15 % |
| `--a4-info-line` | `#bcd0f5` → blue-200 `#bfdbfe` | `rgba(147, 180, 245, 0.38)` → blue-300 at 38 % |
| `--a4-chart-1` | `#2a78d6` → blue-600 `#2563eb` | `#5b9cf0` → blue-400 `#60a5fa` |
| `--a4-chart-2` | `#eb6834` → orange-600 `#ea580c` | `#f5875a` → orange-400 `#fb923c` |
| `--a4-chart-3` | `#188a63` → emerald-600 `#059669` | `#34d399` → emerald-400 `#34d399` |
| `--a4-chart-4` | `#a66c00` → amber-600 `#d97706` | `#fbbf24` → amber-400 `#fbbf24` |
| `--a4-chart-5` | `#d1578a` → pink-600 `#db2777` | `#f19bbf` → pink-400 `#f472b6` |
| `--a4-chart-6` | `#008300` → green-700 `#15803d` | `#4ade80` → green-400 `#4ade80` |
| `--a4-chart-7` | `#4a3aa7` → indigo-700 `#4338ca` | `#8b7cf0` → violet-400 `#a78bfa` |
| `--a4-chart-8` | `#e34948` → red-600 `#dc2626` | `#f87171` → red-400 `#f87171` |
| `--a4-shadow-s` | shadows in slate-900 (light) / black (dark) | |
| `--a4-shadow-m` | shadows in slate-900 (light) / black (dark) | |
| `--a4-shadow-up` | shadows in slate-900 (light) / black (dark) | |

**admin4.css**

| Token | Light: was → now | Dark: was → now |
|---|---|---|
| `--a4-page-color` | `#d8dee6` → slate-200 `#e2e8f0` | `#0b0e14` → slate-950 `#020617` |
| `--a4-page-image` | logo orange, blue-500, slate-200 → slate-300 | logo orange, blue-500, slate-900 → slate-950 |
| `--a4-header-bg` | `rgba(255, 255, 255, 0.82)` → white at 82 % | `rgba(20, 25, 34, 0.86)` → slate-900 at 86 % |
| `--a4-header-line` | `rgba(16, 24, 40, 0.07)` → slate-900 at 7 % | `rgba(255, 255, 255, 0.06)` → white at 6 % |
| `--a4-header-shadow` | shadows in slate-900 (light) / black (dark) | |
| `--a4-header-ink` | `#3b4252` → slate-700 `#334155` | `#d7dbe2` → slate-300 `#cbd5e1` |
| `--a4-header-hover` | `rgba(16, 24, 40, 0.05)` → slate-900 at 5 % | `rgba(255, 255, 255, 0.08)` → white at 8 % |
| `--a4-header-field-line` | `rgba(16, 24, 40, 0.14)` → slate-900 at 14 % | `rgba(255, 255, 255, 0.18)` → white at 18 % |
| `--a4-side-bg` | `#f3f5f8` → slate-100 `#f1f5f9` | `#151a22` → slate-900 at 60 % |
| `--a4-card-edge` | `rgba(16, 24, 40, 0.16)` → slate-900 at 16 % | `rgba(255, 255, 255, 0.09)` → white at 9 % |
| `--a4-explayouts-nav` | `#1f2937` → slate-800 `#1e293b` | `#1f2937` → slate-800 `#1e293b` |
| `--a4-explayouts-nav-edge` | `rgba(255, 255, 255, 0.04)` → white at 4 % | `rgba(255, 255, 255, 0.04)` → white at 4 % |
| `--a4-card-shadow` | shadows in slate-900 (light) / black (dark) | |
| `--a4-footer-ink` | `#4f5765` → slate-600 `#475569` | `#8d95a3` → slate-400 `#94a3b8` |
| `--a4-footer-link` | `#434b59` → slate-700 `#334155` | `#c8cdd6` → slate-300 `#cbd5e1` |
| `--a4-footer-link-hover` | `#1f2430` → slate-800 `#1e293b` | `#fff` → white `#fff` |
| `--a4-brand` | `#f15a28` → logo `#f15a28` | `#f15a28` → logo `#f15a28` |
| `--a4-env-development` | `#067647` → emerald-700 `#047857` | `#047857` → emerald-700 `#047857` |
| `--a4-env-development-ink` | `#fff` → white `#fff` | `#fff` → white `#fff` |
| `--a4-env-development-line` | `#067647` → emerald-700 `#047857` | `#047857` → emerald-700 `#047857` |
| `--a4-env-test` | `#fdb022` → amber-400 `#fbbf24` | `#fdb022` → amber-400 `#fbbf24` |
| `--a4-env-test-ink` | `#1f1300` → amber-950 `#451a03` | `#1f1300` → amber-950 `#451a03` |
| `--a4-env-test-line` | `#b54708` → amber-700 `#b45309` | `#fdb022` → amber-400 `#fbbf24` |
| `--a4-env-staging` | `#6941c6` → violet-700 `#6d28d9` | `#7a5af8` → violet-600 `#7c3aed` |
| `--a4-env-staging-ink` | `#fff` → white `#fff` | `#fff` → white `#fff` |
| `--a4-env-staging-line` | `#6941c6` → violet-700 `#6d28d9` | `#7a5af8` → violet-600 `#7c3aed` |
| `--a4-env-production` | `#b42318` → red-700 `#b91c1c` | `#d92d20` → red-600 `#dc2626` |
| `--a4-env-production-ink` | `#fff` → white `#fff` | `#fff` → white `#fff` |
| `--a4-env-production-line` | `#b42318` → red-700 `#b91c1c` | `#d92d20` → red-600 `#dc2626` |

The classic admin's `design/admin/stylesheets/components.css` takes the same values (it is a copy).

## Risks / Trade-offs

- The look shifts slightly: greys a touch bluer and lighter in light mode (page `slate-200` instead of `#d8dee6`), the
  dark mode's surfaces a step darker. Contrast does not drop below any guaranteed ratio; most ratios rise.
- Extensions that hard-code the old accent (`#f26a21`) in their own admin4 stylesheets now sit a shade off; they read
  `--a4-accent` where they use the tokens. Listed in the proposal as out of scope.
- The screenshots of the designers' help show the old colours until retaken.

## Migration Plan

None: token values only. A site that overrides `--a4-*` tokens in its own stylesheet keeps its overrides.

## Open Questions

None.
