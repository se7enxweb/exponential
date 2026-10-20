---
title: Favicons and touch icons
audience: designer
category: theme-design
order: 320
summary: Shipping a favicon, touch icons and a web app manifest with a design: where the files go, the link tags with ezimage, the /favicon.ico rewrite rule, and a minimal modern set.
keywords: favicon, favicon.ico, touch icon, apple-touch-icon, web app manifest, site icon, browser tab icon, icon sizes, svg favicon
type: help
---
# Favicons and touch icons

This page is for designers who give a site its tab icon and home screen icon. It answers: which files are needed, where
they go in a design, and how to link them.

## Contents

1. [A minimal modern set](#a-minimal-modern-set)
2. [Where the files go](#where-the-files-go)
3. [The link tags](#the-link-tags)
4. [The /favicon.ico request](#the-faviconico-request)
5. [Checks](#checks)

## A minimal modern set

| File | Purpose |
|---|---|
| `favicon.svg` | the tab icon in current browsers (scales, can adapt to dark mode with a media query inside the SVG) |
| `favicon.ico` (32 x 32) | older browsers and tools that ask for `/favicon.ico` |
| `apple-touch-icon.png` (180 x 180) | home screen icon on Apple devices |
| `icon-192.png`, `icon-512.png` | Android home screen and install, named in a web app manifest |

The starter design ships a larger set of PNG sizes (16 to 152 pixels) in `images/favicon/`; both approaches work.

## Where the files go

In your design's `images/favicon/`, so they are found with `ezimage` and served as static files:

```
extension/mytheme/design/mytheme/images/favicon/favicon.svg
extension/mytheme/design/mytheme/images/favicon/favicon.ico
extension/mytheme/design/mytheme/images/favicon/apple-touch-icon.png
```

## The link tags

In a head part of your pagelayout (for example `pagelayout/head/link.tpl`, as the starter design does):

```tpl
<link rel="icon" href={'favicon/favicon.svg'|ezimage} type="image/svg+xml">
<link rel="icon" href={'favicon/favicon.ico'|ezimage} sizes="32x32">
<link rel="apple-touch-icon" href={'favicon/apple-touch-icon.png'|ezimage}>
```

`ezimage` returns the URL in quotes; do not add your own.

## The /favicon.ico request

Browsers ask for `/favicon.ico` at the site root even when the page links another icon. The shipped `.htaccess_root`
serves `design/standard/images/favicon.ico` and has a commented rule to map `/favicon.ico` to it:

```apache
#RewriteRule ^favicon\.ico /design/standard/images/favicon.ico [L]
```

An administrator can point such a rule at your design's file instead. Without a rule, `/favicon.ico` goes to
`index.php` and costs a page request with a 404 answer.

## Checks

- [ ] Tab icon visible in light and dark browser themes.
- [ ] No 404 for `/favicon.ico` in the server log or the network panel.
- [ ] Home screen icon tested on a phone.

## Related pages

- [Images in a design](images-in-a-design.md)
- [SVG images](svg-images.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
