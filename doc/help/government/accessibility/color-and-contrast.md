---
title: Color and contrast in your theme
audience: government, designer, developer
category: public-sector
order: 220
summary: For designers who build or brand a public design - the contrast ratios WCAG 2.2 AA asks for, how admin4 keeps them with checked color tokens in light and dark mode, and how to apply the same discipline to your own theme and brand colors.
keywords: color, contrast, contrast ratio, 4.5:1, 3:1, tokens, CSS custom properties, dark mode, forced colors, brand colors, theme, WCAG 1.4.1, 1.4.3, 1.4.11, admin4, designers
type: help
---
# Color and contrast in your theme

This page is for designers and front-end developers who build the public design of a site, and for communication
teams who hand them brand colors. Public bodies often have brand palettes chosen for print; on screen, several of
those colors fail the contrast WCAG 2.2 AA requires. Fix that in the theme once, and every page benefits.

## Contents

1. [What WCAG asks](#what-wcag-asks)
2. [How admin4 does it](#how-admin4-does-it)
3. [Doing the same in your theme](#doing-the-same-in-your-theme)
4. [Brand colors that fail](#brand-colors-that-fail)
5. [Related pages](#related-pages)

## What WCAG asks

| Criterion | Requirement |
|---|---|
| 1.4.3 Contrast (minimum) | text 4.5:1 against its background; large text 3:1 |
| 1.4.11 Non-text contrast | boundaries of controls, focus indicators and meaningful graphics 3:1 |
| 1.4.1 Use of color | color is never the only way to convey information |

Source: <https://www.w3.org/TR/WCAG22/>.

## How admin4 does it

The admin4 administration defines all its colors as tokens (`--a4-*` custom properties in
`design/admin4/stylesheets/components.css`) with a light and a dark value, and the test
`tests/tests/design/AdminTokenContrastTest.php` computes the WCAG 2 contrast of the pairs that are used together:
text 4.5:1 on every surface, focus ring and field edges 3:1, chart colors 3:1, in both modes
([Accessibility standards](../../../standards/accessibility.md#wcag-22)). The design rules forbid hard-coded colors
in templates and require a dark value for every new token ([admin4 styling](../../../bc/6.0/admin4-styling.md)). It
also honors `@media (forced-colors: active)` and `prefers-color-scheme`.

## Doing the same in your theme

1. **Define tokens.** Put every color of the design in one place (CSS custom properties) with names by role: text,
   muted text, link, surface, border, focus, error, success.
2. **Check every pair** that appears together with a contrast calculator using the WCAG 2 formula, in every color
   scheme your design offers (light, dark, high contrast).
3. **No inline colors.** Templates use classes; content does not set colors. Colors chosen in rich text by editors
   escape your checks.
4. **Focus**: a focus indicator of at least 3:1 against both the component and its background; never remove the
   outline without a replacement.
5. **Links** in running text: underline them, or make them differ by 3:1 from the surrounding text and show a
   non-color cue on hover and focus.
6. **Forced colors**: test with the operating system's high contrast mode; use `forced-colors` media queries where
   borders or icons disappear.
7. **States**: errors, warnings and success messages carry an icon or a word as well as a color (admin4 notices do,
   `AdminNoticesTest.php`).

## Brand colors that fail

- Keep the brand color for large elements (headers, large headings, backgrounds behind dark text) and derive a darker
  shade for small text and links.
- Write the derived shades into your brand guidelines so communication teams use the same values elsewhere.
- Record the decision in your design documentation; auditors ask about it.

## Related pages

- [Branding your site](../../creators/website-creators/branding-your-site.md)
- [Stylesheets and scripts](../../creators/website-creators/stylesheets-and-scripts.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
