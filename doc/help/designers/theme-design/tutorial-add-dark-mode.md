---
title: "Tutorial: add dark mode"
audience: designer
category: theme-design
order: 800
summary: Step by step, give an existing design a dark mode: move colors into tokens, add dark values that follow the system, add a light, dark and system switch without a flash and without inline script, fix images and logos, and test.
keywords: tutorial, dark mode, add dark mode, tokens, prefers-color-scheme, theme switch, data-theme, pre-paint guard, dark logo, contrast, dark theme design
type: help
---
# Tutorial: add dark mode

This tutorial is for designers who add a dark mode to a design that has only a light one. Part A follows the system
setting (no script); part B adds a switch. Allow two to four hours, most of it in step 1.

## Contents

1. [Step 1: colors into tokens](#step-1-colors-into-tokens)
2. [Step 2: dark values for the system setting](#step-2-dark-values-for-the-system-setting)
3. [Step 3: images and logos](#step-3-images-and-logos)
4. [Step 4: a switch](#step-4-a-switch)
5. [Step 5: test](#step-5-test)

## Step 1: colors into tokens

1. List every color in your stylesheets:

   ```bash
   grep -n -o -E "#[0-9a-fA-F]{3,8}\b|rgba?\([^)]*\)" extension/mytheme/design/mytheme/stylesheets/*.css | sort | uniq -c | sort -rn
   ```

2. Group them by role: text, muted text, page background, card background, border, accent, focus.
3. Define one token per role in `tokens.css` and replace every literal with `var(--site-...)`
   ([Design tokens](design-tokens.md)). Near-identical grays become one token.
4. Reload: the light page must look the same as before.

## Step 2: dark values for the system setting

Add to `tokens.css`:

```css
@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) {
    color-scheme: dark;
    --site-ink: #e8ebf0;
    --site-muted: #a3abb9;
    --site-surface: #171c25;
    --site-card: #1b212c;
    --site-line: #2e3644;
    --site-accent: #7fb3f0;
    --site-focus: #7fb3f0;
  }
}
```

Set your operating system to dark and reload. Measure the contrast of every text token on every surface token
([Color contrast](color-contrast.md)).

## Step 3: images and logos

- Logo: a version for dark backgrounds, or an SVG drawn in `currentColor` ([SVG images](svg-images.md)).
- Icons as CSS masks follow the text color.
- Shadows: darker and subtler in dark mode, or a border instead.

## Step 4: a switch

1. Copy the dark values into a second block for an explicit choice:

   ```css
   :root[data-theme="dark"] { color-scheme: dark; --site-ink: #e8ebf0; /* the same values as above */ }
   ```

2. Add `javascript/theme-guard.js` from [The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md)
   and load it in the head, **before** the stylesheets:

   ```tpl
   {ezscript( array( 'theme-guard.js' ) )}
   {ezcss_load( array( 'tokens.css', 'mytheme.css' ) )}
   ```

3. Add the button in the header and its click handler in your main script file (same page).
4. Translate the button's labels.

## Step 5: test

Use the [dark mode checklist](checklist-dark-mode.md). In short: every page type in light and dark, at 1440 px and
960 px with scale 2; no white boxes; contrast measured; reload keeps the choice; no flash on load; print is light.

## Related pages

- [Dark mode overview](dark-mode-overview.md)
- [Dark mode with prefers-color-scheme](dark-mode-with-prefers-color-scheme.md)
- [Checklist: dark mode](checklist-dark-mode.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
