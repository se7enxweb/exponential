---
title: Design tokens
audience: designer
category: theme-design
order: 510
summary: Building a design on tokens (CSS custom properties) for color, space, size, radius and fonts: naming, a starter token set with light and dark values, using tokens in rules, and the admin4 token rules as a model.
keywords: design tokens, css custom properties, css variables, color tokens, spacing scale, type scale, radius, theme variables, token naming, dark values
type: help
---
# Design tokens

This page is for designers who want a design that is easy to rebrand, has a dark mode and stays consistent. A **token**
is a named value (a CSS custom property) that every rule uses instead of a literal value.

## Contents

1. [Why tokens](#why-tokens)
2. [Naming](#naming)
3. [A starter set](#a-starter-set)
4. [Using tokens](#using-tokens)
5. [Rules that keep tokens useful](#rules-that-keep-tokens-useful)

## Why tokens

- **One place to change**: a brand color is one line, not fifty.
- **Dark mode** is a second set of values for the same names ([Dark mode overview](dark-mode-overview.md)).
- **Brands of a multisite** are a dozen lines each ([Designs in a multisite installation](designs-in-multisite.md)).
- **Consistency**: four spacing steps used everywhere look better than thirty slightly different paddings.

## Naming

Name tokens by **role**, not by value: `--site-ink` (text), `--site-muted`, `--site-surface`, `--site-card`,
`--site-line`, `--site-accent`, `--site-focus`. A token called `--orange` stops making sense the day the brand turns
blue, or in dark mode. Use one prefix for your design (admin4 uses `--a4-`).

## A starter set

```css
/* design/mytheme/stylesheets/tokens.css */
:root {
  /* color: roles */
  --site-ink: #1f2430;
  --site-muted: #5d6573;          /* 5.9:1 on --site-surface */
  --site-surface: #ffffff;
  --site-card: #f6f7f9;
  --site-line: #d5dae1;
  --site-accent: #0b5cad;          /* 6.4:1 on white, for links and buttons */
  --site-on-accent: #ffffff;
  --site-focus: #0b5cad;

  /* space: one scale */
  --site-space-1: 0.25rem;
  --site-space-2: 0.5rem;
  --site-space-3: 1rem;
  --site-space-4: 1.5rem;
  --site-space-5: 2.5rem;

  /* type */
  --site-font: system-ui, -apple-system, "Segoe UI", sans-serif;
  --site-text-s: 0.875rem;
  --site-text-m: 1rem;
  --site-text-l: 1.25rem;
  --site-text-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);

  /* shape */
  --site-radius: 0.5rem;
}

:root[data-theme="dark"] {
  color-scheme: dark;
  --site-ink: #e8ebf0;
  --site-muted: #a3abb9;
  --site-surface: #171c25;
  --site-card: #1b212c;
  --site-line: #2e3644;
  --site-accent: #7fb3f0;
  --site-on-accent: #0e1218;
  --site-focus: #7fb3f0;
}
```

The contrast ratios in the comments are examples of what to write down; measure your own colors
([Color contrast](color-contrast.md)).

## Using tokens

```css
body   { color: var(--site-ink); background: var(--site-surface); font-family: var(--site-font); }
.card  { background: var(--site-card); border: 1px solid var(--site-line);
         border-radius: var(--site-radius); padding: var(--site-space-4); }
a      { color: var(--site-accent); }
```

Load `tokens.css` first in the `ezcss_load` list.

## Rules that keep tokens useful

These are the admin's rules (`doc/en/us/bc/6.0/admin4-styling.md`), and they work for any design:

- No literal colors, radii, spacing or font values in rules: only tokens.
- No local aliases (`--card-bg: var(--site-card)` in a component): use the token itself.
- One accent and one component radius; do not add a second "just for this page".
- Every color token has a dark value, added in the same change as the light one.
- Spacing, size and font tokens are the same in both themes and are defined once.

## Related pages

- [admin4 tokens](admin4-tokens.md)
- [Dark mode with prefers-color-scheme](dark-mode-with-prefers-color-scheme.md)
- [Tutorial: add dark mode](tutorial-add-dark-mode.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
