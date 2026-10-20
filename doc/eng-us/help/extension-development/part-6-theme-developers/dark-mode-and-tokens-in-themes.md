---
title: Dark mode and design tokens in themes
audience: designer, developer
category: extension-development
order: 70
summary: Give a theme one set of design tokens with light and dark values, follow the visitor's system setting, add an optional toggle without inline scripts, and check contrast, as the admin4 design does.
keywords: dark mode, design tokens, CSS custom properties, prefers-color-scheme, color-scheme, toggle, data attribute, contrast, admin4, a4 tokens, theme
type: help
---
# Dark mode and design tokens in themes

Part 6, chapter 7. Many visitors use their device in dark mode, and a site that ignores it flashes white at them.
Dark mode is cheap if the theme is built on **design tokens** from the start: named values for colors (and spacing,
radius, type) used everywhere, with a second set of values for dark. This chapter shows the pattern, as the admin4
design uses it.

## Tokens: one name, two values

```css
:root {
    color-scheme: light dark;
    --mytheme-ink: #1f2328;
    --mytheme-muted: #59636e;
    --mytheme-surface: #ffffff;
    --mytheme-card: #f6f8fa;
    --mytheme-line: #d0d7de;
    --mytheme-accent: #0b6bcb;
}

@media (prefers-color-scheme: dark) {
    :root:not([data-mytheme-theme="light"]) {
        --mytheme-ink: #e6edf3;
        --mytheme-muted: #9198a1;
        --mytheme-surface: #0d1117;
        --mytheme-card: #161b22;
        --mytheme-line: #30363d;
        --mytheme-accent: #4493f8;
    }
}

:root[data-mytheme-theme="dark"] {
    --mytheme-ink: #e6edf3;
    --mytheme-muted: #9198a1;
    --mytheme-surface: #0d1117;
    --mytheme-card: #161b22;
    --mytheme-line: #30363d;
    --mytheme-accent: #4493f8;
}

body { color: var(--mytheme-ink); background: var(--mytheme-surface); }
.mytheme-card { background: var(--mytheme-card); border: 1px solid var(--mytheme-line); }
a { color: var(--mytheme-accent); }
```

How it works:

- The first block is the light scheme, always.
- The media query applies the dark values when the device is in dark mode, **unless** the visitor chose light on
  the site (`data-mytheme-theme="light"` on `<html>`).
- The last block applies the dark values when the visitor chose dark on the site, whatever the device says.
- Every rule below uses only tokens. No rule needs its own dark version.
- `color-scheme: light dark` tells the browser that form fields and scrollbars may follow too.

The admin4 design works the same way: its tokens are `--a4-*` in `components.css`, and its mode is the attribute
`html[data-a4-theme="light"|"dark"]`. Its rule is that "a component or module rule never needs a
`html[data-a4-theme="dark"]` rule of its own: the tokens switch" ([Styling the administration](../../../bc/6.0/admin4-styling.md)).

## Rules for tokens

1. **Every color in the theme is a token.** A `#fff` written into one rule is the white box on the dark page.
2. **Every color token gets a dark value in the same change.** admin4's CI test fails on a token without one; you can
   keep the same discipline by hand.
3. **Few tokens, named by role** (ink, muted, surface, card, line, accent, ok, warn, bad), not by color
   (`--blue`). A role keeps its meaning in both schemes.
4. **One accent.** Two accents look like a mistake in one of the schemes.
5. **Your own prefix** (`--mytheme-*`), so a theme on top of yours, or an extension's stylesheet, does not collide.

## Images and the dark scheme

- Logos with dark text need a light version: use `<picture>` with a `prefers-color-scheme` media source, or an SVG
  that uses `currentColor`.
- Photos need nothing.
- Shadows look different on dark: make them tokens too.

## An optional toggle, without inline script

Following the device is enough for most sites. If you offer a toggle, it must apply the choice **before** the page is
painted, or the page flashes in the wrong scheme. That needs a script in `<head>`. Under a Content Security Policy it
must be a file, not an inline script. A small file, loaded as a normal (blocking) script in `<head>`:

```javascript
// design/mytheme/javascript/mytheme_head.js: apply the saved scheme before the first paint
( function()
{
    var saved = null;
    try { saved = window.localStorage.getItem( 'mytheme-theme' ); } catch ( e ) {}
    if ( saved === 'light' || saved === 'dark' )
    {
        document.documentElement.setAttribute( 'data-mytheme-theme', saved );
    }
} )();
```

```text
<head>
...
{ezscript( array( 'mytheme_head.js' ) )}
{ezcss_load( ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) )}
</head>
```

and a button handled by your main script:

```text
<button type="button" class="mytheme-toggle" data-mytheme-toggle>{'Dark mode'|i18n( 'design/mytheme' )|wash}</button>
```

```javascript
document.addEventListener( 'click', function( e )
{
    if ( !e.target.closest || !e.target.closest( '[data-mytheme-toggle]' ) )
        return;
    var root = document.documentElement;
    var dark = root.getAttribute( 'data-mytheme-theme' ) === 'dark' ||
        ( !root.hasAttribute( 'data-mytheme-theme' ) && window.matchMedia( '(prefers-color-scheme: dark)' ).matches );
    var next = dark ? 'light' : 'dark';
    root.setAttribute( 'data-mytheme-theme', next );
    try { window.localStorage.setItem( 'mytheme-theme', next ); } catch ( e2 ) {}
} );
```

The `try` blocks matter: storage may be unavailable (private windows, blocked site data), and the page must work
without it.

## Check it

- Switch the device between light and dark: the site follows.
- Use the toggle, reload: the choice stays, without a flash.
- Contrast: normal text at least 4.5:1 against its background in both schemes, large text 3:1. Browsers' developer
  tools show the ratio when you inspect a text element.
- Look at every page type, not only the front page: forms, tables, error pages.

## Related pages

- Previous: [Theme assets, the packer and CSP](theme-assets-the-packer-and-csp.md)
- Next: [Distributing a theme](distributing-a-theme.md)
- [admin4 styling for extension pages](../part-4-upgrading/admin4-styling-for-extension-pages.md)
- [prefers-color-scheme](https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-color-scheme) on MDN
- [Contrast (minimum)](https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html) in WCAG 2.2
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
