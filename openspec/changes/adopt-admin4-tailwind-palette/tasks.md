Abbreviations: **DESIGN** = `php vendor/bin/phpunit --no-coverage --testsuite design`;
**PAL** = `php vendor/bin/phpunit --no-coverage tests/tests/design/AdminTokenPaletteTest.php tests/tests/design/AdminTokenContrastTest.php`.

## 1. Palette test

- [x] 1.1 `tests/tests/design/fixtures/tailwind-palette-3.4.17.json`: Tailwind CSS 3.4.17's default palette (22 families × 11 steps) with its source. Check: the fixture test of `AdminTokenPaletteTest` (22 families, 242 values)
- [x] 1.2 `tests/tests/design/AdminTokenPaletteTest.php`: every colour of admin4's stylesheets and of `design/admin/stylesheets/components.css` is a palette colour, white, black or the logo's orange; greys slate only; `--a4-accent` and `--a4-brand` equal the logo's fill (`parts/a4_logo.tpl`). Check: PAL fails all four tests against the old tokens
- [x] 1.3 `tests/test-map.json`: the test for `design/admin*/stylesheets/*`, `design/admin4/stylesheets/admin4.css` and `design/admin4/templates/parts/a4_logo.tpl`. Check: `php -r 'json_decode(file_get_contents("tests/test-map.json"), false, 512, JSON_THROW_ON_ERROR);'`

## 2. Tokens

- [x] 2.1 `design/admin4/stylesheets/components.css` and its copy `design/admin/stylesheets/components.css`: the shared tokens, light and dark, to the mapping of design.md (D2–D6); the ratio comments to the new values. Check: PAL green; `cmp` of the two copies
- [x] 2.2 `design/admin4/stylesheets/admin4.css`: the frame's tokens (page and its gradient, header, side columns, footer, brand, environment badge) to the mapping. Check: PAL green; `AdminEnvironmentBadgeTest` green
- [x] 2.3 The close icon of `base.css` and `theme/modalwindow.css` (inline SVG, `%238a8a8a`) to `slate-500`; the test reads `%23rrggbb`. Check: PAL fails on the old grey, passes on the new
- [x] 2.4 The whole design suite. Check: DESIGN green (1097 tests, 20 skipped for packages not installed, as before the change)

## 3. Documentation

- [x] 3.1 `doc/eng-us/bc/6.0/admin4-styling.md` and `doc/ger-de/bc/6.0/admin4-gestaltung.md`: the accent, the palette rule, the value table. Check: no old value left (`grep -E '#(f26a21|ad3809|1f2430|545c6a)'` only in the history paragraph)
- [x] 3.2 `doc/eng-us/help/designers/theme-design/admin4-tokens.md`, `color-contrast.md` and the German `admin4-tokens.md`, `farbkontrast.md`: the accent and the example values. Check: as 3.1

## 4. Owner

- [ ] 4.1 Owner: retake the screenshots `doc/eng-us/help/images/designers/admin4-tokens-{light,dark}.webp` and `doc/ger-de/hilfe/bilder/designers/admin4-tokens-{light,dark}.webp` in the new colours
- [ ] 4.2 Owner: pull request to se7enxweb/exponential; Redmine ticket if wanted
