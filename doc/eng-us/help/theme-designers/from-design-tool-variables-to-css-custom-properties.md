---
title: "From design tool variables to CSS custom properties"
audience: theme-designer
category: theme-design
order: 16
summary: How to carry the variables of a design tool (colors, sizes, spacing, modes) into the token stylesheet of an Exponential theme - one naming scheme, light and dark modes as two value sets, an export table, the tokens.css file, and keeping both sides in step.
keywords: theme designer, design tool, variables, design tokens, CSS custom properties, export, modes, light, dark, tokens.css, naming, handoff
type: help
---
# From design tool variables to CSS custom properties

This page is for theme designers who keep colors, sizes and spacing as variables in a design tool and want them to
arrive unchanged in the theme. Most current design tools support variables with modes (for example a light and a dark
mode). An Exponential theme expresses the same thing as CSS custom properties in a token stylesheet, with a light value
set and a dark value set. The rules for tokens are in [Design tokens](../designers/theme-design/design-tokens.md); this
page is the bridge.

## Contents

1. [The mapping](#the-mapping)
2. [One naming scheme on both sides](#one-naming-scheme-on-both-sides)
3. [Modes become selectors](#modes-become-selectors)
4. [The export table](#the-export-table)
5. [Writing tokens.css](#writing-tokenscss)
6. [Loading it](#loading-it)
7. [Keeping both sides in step](#keeping-both-sides-in-step)

## The mapping

| Design tool | Exponential theme |
|---|---|
| Collection of primitive colors (ramps) | not in CSS, or a commented reference block ([Color palettes and ramps](color-palettes-and-ramps.md)) |
| Semantic color variable (text, surface, accent) | `--site-*` color token |
| Light mode / dark mode | `:root` values / dark values ([Modes become selectors](#modes-become-selectors)) |
| Number variables for spacing | `--site-space-*` tokens |
| Number variables for font sizes | `--site-text-*` tokens |
| Corner radius variable | the one `--site-radius` token |
| Text styles | classes in the components stylesheet, built from tokens |
| Brand modes (several brands) | a brand stylesheet per brand design ([Designing for several brands in multisite](designing-for-several-brands-in-multisite.md)) |

## One naming scheme on both sides

Name the design tool variable exactly as the token, minus the prefix and dashes the tool does not like:

| Design tool | CSS |
|---|---|
| `color/ink` | `--site-ink` |
| `color/accent` | `--site-accent` |
| `space/3` | `--site-space-3` |
| `text/l` | `--site-text-l` |

Use your theme's own prefix instead of `site` if you prefer, but one prefix for the whole design (the admin uses
`--a4-`). Never name a semantic variable after its value (`blue`), because its dark value will not be blue.

## Modes become selectors

A design tool switches a frame between modes; CSS switches the values by selector. The pattern the designer help uses
for a public design follows the system setting, and also honors an explicit choice once a switch exists
([Dark mode with prefers-color-scheme](../designers/theme-design/dark-mode-with-prefers-color-scheme.md)):

```css
:root { /* light values */ }

@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) { color-scheme: dark; /* dark values */ }
}
:root[data-theme="dark"] { color-scheme: dark; /* dark values */ }
```

Only color tokens change between modes. Spacing, sizes, radius and fonts are defined once in `:root`.

## The export table

Most tools can export variables as JSON or list them. Whatever the export format, turn it into a table the developer
can check line by line, and keep it in the theme's repository:

| Token | Light | Dark | Notes |
|---|---|---|---|
| `--site-ink` | `#1f2430` | `#e8ebf0` | body text |
| `--site-surface` | `#ffffff` | `#171c25` | page background |
| `--site-accent` | `#0b5cad` | `#7fb3f0` | links, primary buttons |
| `--site-space-3` | `1rem` | | same in both |
| `--site-radius` | `0.5rem` | | same in both |

These example values are the starter set from [Design tokens](../designers/theme-design/design-tokens.md#a-starter-set);
use your own. Convert pixel numbers to `rem` (divide by 16) for spacing and type.

## Writing tokens.css

The token file is a plain stylesheet in the design: `design/mytheme/stylesheets/tokens.css`. Write it from the export
table, in the same order, with the light values in `:root` and the dark values in the two dark selectors. If your team
uses a build step to generate it from the export, commit the generated file, because the packer reads stylesheets
from the design folder at run time; it has no build step of its own.

Never put a token value in a template, a `<style>` block or a `style=""` attribute
([No inline CSS](../designers/theme-design/no-inline-css.md)).

## Loading it

The token stylesheet loads first, before components, through the stylesheet list of the design and the packer
([Loading stylesheets](../designers/theme-design/loading-stylesheets.md)). A change reaches browsers through the packed
file, whose name carries the modification time; never add a `?v=` query to the link
([No version queries on asset links](../designers/theme-design/no-version-queries-on-asset-links.md)).

## Keeping both sides in step

- The export table is the source of truth; the design tool and `tokens.css` both follow it.
- A new token gets its dark value in the same change.
- Review once per release: tokens in CSS that no stylesheet uses, and variables in the design tool that have no token,
  are both drift. The [style guide page](building-a-style-guide-page.md) shows the coded values.

## Related pages

- [Design tokens](../designers/theme-design/design-tokens.md)
- [Color palettes and ramps](color-palettes-and-ramps.md)
- [Dark mode and design tokens in themes](../extension-development/part-6-theme-developers/dark-mode-and-tokens-in-themes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
