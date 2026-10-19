---
title: SVG images
audience: designer
category: theme-design
order: 300
summary: SVG in designs and in content: design SVGs you ship yourself, SVG uploads by editors (accepted and sanitized since October 2026, never scaled), what that means for templates, and how to keep SVGs accessible and theme-aware.
keywords: svg, svg images, vector images, logo svg, svg upload, SanitizeSVG, AllowedMIMETypes, expSvgSanitizer, svg in content, currentColor, svg accessibility, image aliases svg
type: help
---
# SVG images

This page is for designers who use SVG for logos, icons and illustrations. It answers: how design SVGs and uploaded
SVGs differ, what the kernel does to uploaded SVGs, and how templates must treat them.

## Contents

1. [Design SVGs](#design-svgs)
2. [SVGs uploaded by editors](#svgs-uploaded-by-editors)
3. [What this means for your templates](#what-this-means-for-your-templates)
4. [SVG and dark mode](#svg-and-dark-mode)
5. [Accessible SVG](#accessible-svg)

## Design SVGs

SVG files in your design's `images/` are your own files: you made them, you trust them. Link them with `ezimage` like
any image, or use them as CSS backgrounds and masks. Remove editor metadata and hidden layers before shipping (an SVG
optimizer does this), and give the file a `viewBox` so it scales.

## SVGs uploaded by editors

Since 15 October 2026 the image datatype accepts SVG (`.svg`, `.svgz`) uploads (`settings/image.ini [ImageUploadSettings]`):

```ini
AllowedMIMETypes[]=image/svg+xml
# enabled (default) or disabled
SanitizeSVG=enabled
```

An SVG can carry script, so the kernel never stores an upload as it came: `expSvgSanitizer` keeps only allow-listed
elements, attributes and CSS, and the stored file is its output. Uploaded SVGs are served with headers that stop them
from running anything when opened directly. `SanitizeSVG=disabled` refuses SVG uploads; there is no way to store one
unsanitized. Details: [SVG images (behavior change)](../../../bc/6.0/svg-images.md).

## What this means for your templates

- **An SVG is never scaled.** Every alias of an SVG image is the sanitized file itself (comment in
  `settings/image.ini`). Its displayed size comes from your CSS or the `width` and `height` attributes, not from the
  alias.
- Give SVG images in content a size in CSS, for example `max-width: 100%; height: auto;` and a width from the
  context, so a huge `viewBox` does not take the whole page.
- Show content SVGs with `<img>` (as the datatype template does), never by pasting their markup into the page: in an
  `<img>`, an SVG cannot run script or read the page.

## SVG and dark mode

- A design SVG drawn as a CSS mask, or written inline into the markup with `fill="currentColor"`, follows the text
  color and therefore the theme.
- An SVG in an `<img>` cannot see the page's colors. For logos, ship a light and a dark file and switch with
  `<picture>` and `prefers-color-scheme`, or choose colors that work on both backgrounds.

## Accessible SVG

- In `<img>`: an `alt` as for any image.
- Inline decorative SVG: `aria-hidden="true"` and `focusable="false"`.
- Inline meaningful SVG: `role="img"` and a `<title>` as its first child, or `aria-label`.

## Related pages

- [Images in a design](images-in-a-design.md)
- [Icons in a design](icons-in-a-design.md)
- [Dark mode overview](dark-mode-overview.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
