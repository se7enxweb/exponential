---
title: "Color palettes and ramps"
audience: theme-designer
category: theme-design
order: 15
summary: How theme designers build a color palette for an Exponential theme - brand colors, ramps of tints and shades, role tokens chosen from the ramps, dark values, status colors, and how to check contrast before anything is coded.
keywords: theme designer, color palette, color ramp, tints, shades, roles, semantic colors, brand colors, dark mode, status colors, contrast, tokens
type: help
---
# Color palettes and ramps

This page is for theme designers who build the colors of a theme. A good palette has two layers: **ramps** (a brand
color in ten or so lightness steps) that only designers see, and **role tokens** (text, surface, line, accent, focus)
that the stylesheets use. The role tokens and the rules for them are documented in
[Design tokens](../designers/theme-design/design-tokens.md); contrast targets in [Color contrast](../designers/theme-design/color-contrast.md);
dark mode in [Dark mode overview](../designers/theme-design/dark-mode-overview.md). This page connects them.

## Contents

1. [Two layers](#two-layers)
2. [Building a ramp](#building-a-ramp)
3. [Choosing roles from the ramps](#choosing-roles-from-the-ramps)
4. [Dark values](#dark-values)
5. [Status colors](#status-colors)
6. [One accent](#one-accent)
7. [Checking before coding](#checking-before-coding)
8. [A palette sheet](#a-palette-sheet)

## Two layers

| Layer | Example | Who uses it |
|---|---|---|
| Ramp | `blue-50` ... `blue-900`, `gray-50` ... `gray-900` | designers, to pick from |
| Role | `--site-ink`, `--site-surface`, `--site-accent` | stylesheets, through tokens |

Stylesheets never use ramp names. If the brand blue changes, you pick new ramp steps for the roles and the CSS does not
change. The ramp may live in your design tool only, or as a commented block at the top of the token file.

## Building a ramp

1. Start from each brand color and a neutral (gray, often slightly tinted toward the brand).
2. Make about ten steps from very light to very dark. Work in a perceptual color space (for example OKLCH in tools that
   offer it) so equal steps look equal; plain HSL steps look uneven.
3. Keep hue and chroma steady along the ramp, except where a step looks muddy; very light and very dark steps often
   need less chroma.
4. Write the contrast of each step against white and against your darkest surface next to it.

## Choosing roles from the ramps

| Role | Light theme picks | Requirement |
|---|---|---|
| Ink (body text) | a very dark neutral | 4.5:1 or more on surface and card |
| Muted text | a mid-dark neutral | 4.5:1 on surface and card |
| Surface, card | white and a very light neutral | card distinguishable from surface |
| Line | a light neutral | visible; 3:1 where the line is the only boundary of a control |
| Accent (links, buttons) | a mid-dark brand step | 4.5:1 on surface; text on accent 4.5:1 |
| Focus | accent or a distinct color | 3:1 against what is next to it |

The targets come from [Color contrast](../designers/theme-design/color-contrast.md#the-targets).

## Dark values

Every color role gets a dark value at the same time as its light value; it is a project rule for every token
([Design tokens](../designers/theme-design/design-tokens.md#rules-that-keep-tokens-useful)). In dark themes:

- surfaces come from the dark end of the neutral ramp, but not pure black;
- ink is a light neutral, not pure white, to reduce glare;
- the accent usually moves several steps lighter, because the mid step that worked on white fails on dark;
- cards are slightly lighter than the surface, so elevation reads without shadows.

Draw every key screen in both. A white card on a dark page is the most common dark-mode defect
([Checklist: dark mode](../designers/theme-design/checklist-dark-mode.md)).

## Status colors

Success, warning, error and information need their own roles (`--site-danger`, `--site-success` ...), each with a
background, a text and a border variant if alerts use tinted boxes, and each with a dark value. Never use color alone
to carry meaning: an error also has an icon or the word "Error" ([Designing forms](designing-forms.md)).

## One accent

The project's token rules allow one accent and one component radius. A second accent "just for this page" makes the
theme harder to brand and harder to keep accessible. For multi-brand sites, the accent changes per brand, not per page
([Designing for several brands in multisite](designing-for-several-brands-in-multisite.md)).

## Checking before coding

- Check every role pair (text on surface, text on card, text on accent, focus on surface) in both themes with a
  contrast checker, and write the ratio in the palette sheet.
- Look at the palette with a color-vision deficiency simulation, which most browsers' developer tools and design tools
  offer.
- Put the palette on the [style guide page](building-a-style-guide-page.md) once it is coded, so the real values are
  checked again.

## A palette sheet

| Token | Light | Ratio (light) | Dark | Ratio (dark) | Used for |
|---|---|---|---|---|---|
| `--site-ink` | gray-900 | on surface | gray-100 | on surface | body text |
| `--site-accent` | blue-700 | on surface | blue-300 | on surface | links, primary buttons |

Fill in the values and measured ratios for your palette. This sheet is what you hand over; the token file is written
from it ([From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md)).

## Related pages

- [Design tokens](../designers/theme-design/design-tokens.md)
- [Dark mode with prefers-color-scheme](../designers/theme-design/dark-mode-with-prefers-color-scheme.md)
- [Tutorial: add dark mode](../designers/theme-design/tutorial-add-dark-mode.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
