---
title: Color contrast
audience: designer
category: theme-design
order: 440
summary: Contrast targets for text, large text, icons and focus rings, how admin4's tokens document their ratios, checking contrast in light and dark mode, and common traps (placeholder text, text on images, hover states, zebra rows).
keywords: contrast, color contrast, 4.5:1, 3:1, WCAG contrast, accessible colors, dark mode contrast, focus ring contrast, placeholder contrast, text on image
type: help
---
# Color contrast

This page is for designers who pick colors. It answers: which ratios to meet, how to check them in both themes, and
where contrast usually fails.

## Contents

1. [The targets](#the-targets)
2. [How admin4 documents its ratios](#how-admin4-documents-its-ratios)
3. [Check every state and both themes](#check-every-state-and-both-themes)
4. [Common traps](#common-traps)

## The targets

| What | At least |
|---|---|
| Body text, small text, links in text | 4.5:1 against its background |
| Large text (24 px, or 19 px bold and up) | 3:1 |
| Icons that carry meaning, form field borders, focus indicators | 3:1 against what is next to them |
| Disabled controls, pure decoration | no requirement, but keep them recognizable |

These follow the Web Content Accessibility Guidelines; the project's admin rules require 4.5:1 for text.

## How admin4 documents its ratios

admin4 writes the ratio next to each color token in `design/admin4/stylesheets/components.css`, for example:

```css
--a4-accent-strong: #c2410c;  /* filled controls carrying white text (5.2:1) */
--a4-accent-text:   #ad3809;  /* 6.3:1 on white, 5.4:1 on the hovered row */
--a4-ring:          #c2410c;  /* opaque: a focus indicator needs 3:1 against the page */
```

and a test keeps text on zebra and hovered rows at 4.5:1 (`AdminTokenContrastTest`, named in the admin styling
guide). Do the same in your design: write the ratio in a comment when you choose a color, so the next person knows
what not to break.

## Check every state and both themes

For each text color, check it on every background it can appear on:

- normal, hover, active, selected, visited;
- light and dark theme;
- on cards, on the page, on zebra rows, on colored banners.

The browser's developer tools show the contrast ratio of the selected text in the color picker. Automated checks (axe)
find most failures in the state the page is in; hover and focus states need a manual look.

## Common traps

- **Placeholder text** in fields is often too light; do not use it as the only label.
- **Text on photos**: put a solid or semi-opaque layer behind the text, or a text shadow, and check the worst part of the
  image.
- **Brand colors**: an orange or yellow brand color rarely reaches 4.5:1 with white text. admin4 keeps its accent
  `#f26a21` for fills and uses darker shades (`--a4-accent-strong`, `--a4-accent-text`) for text and filled buttons.
- **Dark mode**: pure white on pure black is harsh, gray on dark gray often fails. Measure, do not guess.
- **Muted text** (dates, captions) still carries information: it needs 4.5:1 too.

## Related pages

- [Design tokens](design-tokens.md)
- [Dark mode overview](dark-mode-overview.md)
- [admin4 tokens](admin4-tokens.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
