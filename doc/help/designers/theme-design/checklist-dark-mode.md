---
title: "Checklist: dark mode"
audience: designer
category: theme-design
order: 900
summary: A dark mode checklist: every color from tokens with a dark value, contrast measured in both themes, no white boxes, form controls dark, logos and images, no flash on load, choice remembered, print light.
keywords: dark mode checklist, dark theme checklist, tokens dark values, contrast dark mode, flash of light theme, color-scheme, dark mode review
type: help
---
# Checklist: dark mode

This page is for designers who check a dark mode. Background: [Dark mode overview](dark-mode-overview.md).

## Contents

1. [Tokens](#tokens)
2. [Looks](#looks)
3. [Behavior](#behavior)

## Tokens

- [ ] No color literal in rules; every color is a token ([Design tokens](design-tokens.md)).
- [ ] Every color token has a dark value, defined next to the light one.
- [ ] `color-scheme: dark` in the dark block (form controls and scrollbars follow).
- [ ] For admin designs: the admin styling rules test passes ([The admin styling rules test](admin-styling-rules-test.md)).

## Looks

- [ ] No white or light box on a dark page (cards, tables, code blocks, dialogs, third-party widgets).
- [ ] Text contrast at least 4.5:1 on every dark surface, including hover and selected states ([Color contrast](color-contrast.md)).
- [ ] Borders visible but not glaring; surfaces distinguishable (page, card, raised).
- [ ] Logo readable on the dark header ([SVG images](svg-images.md)).
- [ ] Icons follow the text color.
- [ ] Focus ring visible on dark surfaces.
- [ ] Checked at 1440 px and 960 px with scale 2.

## Behavior

- [ ] Follows the system setting when no choice is made, also when the system changes while the page is open.
- [ ] A switch (if any) offers light, dark and system, says its state, is keyboard accessible ([The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md)).
- [ ] No light flash when loading a page in dark mode.
- [ ] The choice survives reloads and navigation.
- [ ] Printing produces a light page.

## Related pages

- [Tutorial: add dark mode](tutorial-add-dark-mode.md)
- [Dark mode with prefers-color-scheme](dark-mode-with-prefers-color-scheme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
