---
title: Fonts in a design
audience: designer
category: theme-design
order: 280
summary: Serving web fonts from your own design instead of a third-party host: where font files go, @font-face rules, font-display, unicode ranges, system font stacks, licenses, and what the web server must allow.
keywords: fonts, web fonts, self-hosted fonts, font-face, woff2, font-display, Google Fonts, unicode-range, system font stack, font license, fonts folder, preload font
type: help
---
# Fonts in a design

This page is for designers who choose and ship the typefaces of a design. It answers: where font files go, how to
declare them, why self-hosting is the default here, and which licenses allow it.

## Contents

1. [Self-hosted by default](#self-hosted-by-default)
2. [Where the files go](#where-the-files-go)
3. [Declaring the font](#declaring-the-font)
4. [Loading the declaration](#loading-the-declaration)
5. [System fonts](#system-fonts)
6. [Licenses](#licenses)
7. [Checks](#checks)

## Self-hosted by default

The media design serves its Inter font from its own `fonts/` folder. The comment in its
`pagelayout/head/style.tpl` gives the reasons: a third-party font host costs a DNS lookup, a TCP connection and a TLS
handshake for its stylesheet, then the same again for the font files, before any text can be drawn; a self-hosted
declaration is packed with the rest of the CSS at no extra request. Self-hosting also means no visitor data goes to a
font host, and a Content Security Policy needs no extra `font-src` origin.

## Where the files go

```
design/mytheme/fonts/source-sans-3-400.woff2
design/mytheme/fonts/source-sans-3-700.woff2
design/mytheme/stylesheets/fonts.css
```

The shipped rewrite rules serve `fonts/` of a design, in the kernel and in extensions, as static files
(`.htaccess_root`; the Apache example `doc/eng-us/examples/ezpublish.conf` has the same list). WOFF2 is enough for every current
browser.

## Declaring the font

```css
/* design/mytheme/stylesheets/fonts.css */
@font-face {
  font-family: "Source Sans 3";
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url(../fonts/source-sans-3-400.woff2) format("woff2");
}
@font-face {
  font-family: "Source Sans 3";
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url(../fonts/source-sans-3-700.woff2) format("woff2");
}

:root { --site-font: "Source Sans 3", system-ui, -apple-system, "Segoe UI", sans-serif; }
body  { font-family: var(--site-font); }
```

- `font-display: swap` shows text in a fallback font at once and swaps when the font arrives.
- Use a relative `url(../fonts/...)`: the packer rewrites it when it moves the stylesheet.
- For large scripts (Cyrillic, Greek, Vietnamese) split the font by `unicode-range`, as the media design's `inter.css`
  does, so a page downloads only the ranges it uses.

## Loading the declaration

Put `fonts.css` **first** in your `ezcss_load` list, so the `@font-face` rules exist before any rule uses the family:

```tpl
{ezcss_load( array( 'fonts.css', 'mytheme.css' ) )}
```

## System fonts

The fastest font is one the visitor already has. A system stack needs no files:

```css
:root { --site-font: system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans", sans-serif; }
```

The admin4 design sets its fonts as tokens (`--a4-font`, `--a4-font-intl`, `--a4-font-cjk`, `--a4-mono` in
`components.css`), so languages with other scripts get a suitable stack.

## Licenses

Ship only fonts whose license allows web embedding and redistribution with your theme (for example fonts under the
SIL Open Font License). Put the license file next to the fonts, and name the font and its license in your theme's
README. Fonts bought under a desktop license usually may not be shipped. The theme distribution pages cover licenses of
third-party assets ([Theme extensions: start here](../extensions/theme-extensions-start-here.md)).

## Checks

- [ ] No request to a font host in the browser's network panel.
- [ ] Text visible at once while fonts load (`font-display: swap`).
- [ ] The fallback stack looks close enough that the swap does not move the layout much.
- [ ] Right-to-left and other scripts of your site's languages are covered by the font or the fallback.
- [ ] License file shipped with the fonts.

## Related pages

- [Loading stylesheets](loading-stylesheets.md)
- [Theme performance](theme-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
