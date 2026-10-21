---
title: "Type scale"
audience: theme-designer
category: theme-design
order: 13
summary: How theme designers choose a type scale for an Exponential theme and turn it into tokens - a small number of steps, fluid sizes with clamp(), line height and measure, headings that editors create in rich text, other scripts, and zoom.
keywords: theme designer, type scale, typography, font size, modular scale, clamp, fluid type, line height, measure, headings, tokens, rem
type: help
---
# Type scale

This page is for theme designers who set the sizes of text in a theme. A type scale is a short list of font sizes,
each with a role, from which every text in the theme takes its size. In an Exponential theme the scale becomes a set of
CSS custom properties next to the color tokens ([Design tokens](../designers/theme-design/design-tokens.md)). Fonts
themselves, their files and licenses are covered in [Fonts in a design](../designers/theme-design/fonts-in-a-design.md).

## Contents

1. [How many steps](#how-many-steps)
2. [Choosing the sizes](#choosing-the-sizes)
3. [Fluid sizes with clamp](#fluid-sizes-with-clamp)
4. [Line height and measure](#line-height-and-measure)
5. [Headings editors create](#headings-editors-create)
6. [Other scripts and languages](#other-scripts-and-languages)
7. [The scale as tokens](#the-scale-as-tokens)
8. [Checks](#checks)

## How many steps

Five to seven steps cover a content site: small (metadata, captions), body, large body (lead), and three or four
heading sizes. Every extra step is a size someone will use once and nobody will remember. If two steps are hard to
tell apart on screen, merge them.

## Choosing the sizes

1. Fix the **body** size first: usually `1rem` (16 px in default browser settings), never smaller for long text.
2. Choose a **ratio** between steps, for example 1.2 for a calm scale or 1.25 for more contrast between levels, and
   multiply up from the body for headings, down once for small text.
3. Round to values that look right; a scale is a starting point, not a law.
4. Use `rem` for sizes, so they follow the visitor's browser font setting. Pixel sizes ignore it.

## Fluid sizes with clamp

Large headings that fit a desktop line are too big on a phone. `clamp( <min>, <preferred>, <max> )` lets a size grow
with the viewport between two limits. The project's own token example uses this for its largest step:

```css
--site-text-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);
```

Keep a `rem` part in the preferred value (`1.2rem + 1.5vw`, not `4vw` alone), otherwise text stops responding to the
visitor's zoom and font settings. Body text stays fixed.

## Line height and measure

- **Line height** without a unit (`1.5` for body, `1.2` to `1.3` for headings), so it scales with the size.
- **Measure** (line length) of 60 to 75 characters for body text: limit the text column with `max-inline-size: 70ch`,
  not a pixel width. In narrow layout zones the zone limits it anyway.
- Make line height and the spacing between paragraphs part of the scale, so rhythm follows from the tokens
  ([Spacing and grid](spacing-and-grid.md)).

## Headings editors create

Editors insert headings in rich text fields, and the heading levels they choose are about structure, not looks. Two
consequences:

- Style `h2` to `h6` inside the rich text area in a sensible descending order; editors will use them all.
- Do not give the same heading level different sizes in different places to make a mockup work. If a card title must
  look small but be an `h3`, use a class for the look and keep the level for structure
  ([Landmarks and headings](../designers/theme-design/landmarks-and-headings.md)).

## Other scripts and languages

- Scripts such as Arabic, Devanagari or Thai often need a larger size or a taller line height to read as well as
  Latin text at the same size. Plan a separate stack and, if needed, a size adjustment per language through a
  `:lang()` rule ([Designing for many languages](designing-for-many-languages.md)).
- German, Finnish and other languages have long words: a large heading on a phone must wrap or hyphenate
  (`hyphens: auto` works when the page's `lang` attribute is correct).

## The scale as tokens

```css
/* design/mytheme/stylesheets/tokens.css */
:root {
  --site-text-s:  0.875rem;
  --site-text-m:  1rem;
  --site-text-l:  1.25rem;
  --site-text-xl: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);
  --site-text-2xl: clamp(1.875rem, 1.4rem + 2.2vw, 3rem);
  --site-leading-body: 1.5;
  --site-leading-tight: 1.2;
}
```

Size and line-height tokens are the same in light and dark and are defined once. Name the steps by size (`s`, `m`,
`l`) or by role (`--site-text-caption`), but not both, and not by pixel value.

## Checks

- [ ] Body text at least `1rem`; small text used only for secondary information.
- [ ] Every size in the stylesheets comes from a token.
- [ ] At 200% browser zoom no text overlaps or is cut off ([Testing at zoom levels](../designers/theme-design/testing-at-zoom-levels.md)).
- [ ] Headings `h2` to `h6` styled in rich text.
- [ ] Long words wrap on a 375 px screen.

## Related pages

- [Spacing and grid](spacing-and-grid.md)
- [From design tool variables to CSS custom properties](from-design-tool-variables-to-css-custom-properties.md)
- [Fonts in a design](../designers/theme-design/fonts-in-a-design.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
