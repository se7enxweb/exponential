---
title: Dark mode with prefers-color-scheme
audience: designer
category: theme-design
order: 520
summary: The simplest dark mode for a public design: dark token values in a prefers-color-scheme media query, combined with an explicit light or dark choice when a switch is added, plus images, color-scheme and testing.
keywords: prefers-color-scheme, dark mode, media query, system theme, color-scheme, automatic dark mode, picture source media, dark images, emulate dark mode
type: help
---
# Dark mode with prefers-color-scheme

This page is for designers who want a dark mode that follows the visitor's system setting, with no script. It also
shows how to combine it with a switch later.

## Contents

1. [The system setting alone](#the-system-setting-alone)
2. [Adding an explicit choice later](#adding-an-explicit-choice-later)
3. [Images and logos](#images-and-logos)
4. [Testing](#testing)

## The system setting alone

With tokens on `:root` (see [Design tokens](design-tokens.md)), one block gives a complete dark mode:

```css
@media (prefers-color-scheme: dark) {
  :root {
    color-scheme: dark;
    --site-ink: #e8ebf0;
    --site-surface: #171c25;
    --site-card: #1b212c;
    --site-line: #2e3644;
    --site-accent: #7fb3f0;
  }
}
```

`color-scheme: dark` tells the browser to draw form controls, scrollbars and the default canvas dark as well.

## Adding an explicit choice later

When a switch sets `data-theme` on `<html>`, the system block must give way to an explicit choice. Guard it so it only
applies when the visitor chose nothing or chose "system", and add a block for an explicit dark choice:

```css
@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) { color-scheme: dark; --site-ink: #e8ebf0; --site-surface: #171c25; /* ... */ }
}
:root[data-theme="dark"]          { color-scheme: dark; --site-ink: #e8ebf0; --site-surface: #171c25; /* ... */ }
```

The dark values appear twice; keep them in one place by building the file with a preprocessor, or accept the
duplication and keep both blocks next to each other. admin4 takes the other route: its head script resolves "system"
itself and the stylesheets only read `html[data-a4-theme="dark"]`, so each value is written once.

## Images and logos

- A logo for each scheme:

  ```tpl
  <picture>
      <source srcset={'logo-dark.svg'|ezimage( 'no' )} media="(prefers-color-scheme: dark)">
      <img src={'logo.svg'|ezimage} alt="{$site.title|wash}" width="160" height="40">
  </picture>
  ```

  (`<picture>` follows the system only; with a switch, swap the logo with CSS on `[data-theme]` instead.)
- Photos stay as they are; a slight dimming (`filter: brightness(0.9)`) in dark mode is optional.
- Icons drawn with `currentColor` or CSS masks follow the text color automatically.

## Testing

- The browser's developer tools can emulate `prefers-color-scheme: dark` (rendering settings).
- Check every page type in both schemes, at 1440 px and at 960 px with scale 2.
- Look for white boxes on the dark page: a literal `#fff` somewhere instead of a token.

## Related pages

- [Dark mode overview](dark-mode-overview.md)
- [The theme toggle and the pre-paint guard](theme-toggle-pre-paint-guard.md)
- [Checklist: dark mode](checklist-dark-mode.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
