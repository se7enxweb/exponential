---
title: SVG and image formats
audience: website-creator, admin
category: website-creation
order: 360
summary: Which image formats editors may upload, which formats pages show, how SVG is sanitized and served safely, and when to use SVG, WebP, AVIF, PNG or JPEG on a site.
keywords: SVG, WebP, AVIF, PNG, JPEG, GIF, image formats, AllowedMIMETypes, SanitizeSVG, expSvgSanitizer, ConversionRules, AllowedOutputFormat, logo, icons, website creator
type: help
---
# SVG and image formats

This page is for website creators who choose image formats for a site. It explains what Exponential accepts, what it
converts, and how SVG images are made safe.

## Contents

1. [What editors may upload](#what-editors-may-upload)
2. [What pages show](#what-pages-show)
3. [SVG](#svg)
4. [Which format for what](#which-format-for-what)
5. [Changing the settings](#changing-the-settings)

## What editors may upload

`settings/image.ini [ImageUploadSettings]`:

| Setting | Shipped |
|---|---|
| `ValidateMIMEType` | `enabled`: the file must be one of the allowed types by content |
| `AllowedMIMETypes[]` | `image/jpeg`, `image/png`, `image/gif`, `image/webp`, `image/avif`, `image/tiff`, `image/bmp`, `image/svg+xml` |
| `SanitizeSVG` | `enabled` |

## What pages show

Variations (aliases) are converted according to `[MIMETypeSettings] ConversionRules[]`: WebP and GIF to PNG, everything
else to JPEG, and `[OutputSettings] AllowedOutputFormat[]` (JPEG, PNG, WebP, GIF) lists what pages may show. The
`original` alias is the uploaded file. Animated GIFs are left unscaled (`[AnimatedGIF]`). See
[Images and image variations](images-and-image-variations.md).

## SVG

Since 15 October 2026 the image datatype accepts SVG (`.svg`, and `.svgz`) on every site
([SVG images](../../../bc/6.0/svg-images.md)):

- An SVG can carry script, so it is **never stored as uploaded**: the kernel's sanitizer, `expSvgSanitizer`, keeps only
  elements and attributes known to be safe (no script, no event attributes, no `foreignObject`, no external
  references), and the stored file is its output.
- Every upload path sanitizes: the edit form, uploads, multi-upload, the rich text editor, WebDAV, package imports.
- Every alias of an SVG is the sanitized file itself; SVGs are never scaled.
- The file is served with headers that stop it from running anything when opened directly; serve it with the headers
  the SVG page lists when you use your own web server rules.

Use SVG for logos, icons and diagrams; not for photos.

## Which format for what

| Image | Format |
|---|---|
| Logo, icons, simple illustrations | SVG |
| Photos | JPEG, or WebP/AVIF uploads (variations become JPEG unless you change the rules) |
| Screenshots, images with text or transparency | PNG |
| Animations | short video rather than GIF where possible |

## Changing the settings

Refuse SVG altogether:

```ini
# settings/override/image.ini.append.php
[ImageUploadSettings]
SanitizeSVG=disabled
```

(`disabled` refuses SVG whatever `AllowedMIMETypes[]` says, so an unsanitized SVG is never stored.)

Keep WebP variations as WebP instead of PNG by changing `ConversionRules[]`; test the result in the browsers your
visitors use, then clear the generated variations (`php bin/php/cache.php imagealias clear --allow-root-user`).

## Related pages

- [Images and image variations](images-and-image-variations.md)
- [Redirect, query and image alias guards](../../../bc/6.0/redirect-query-and-image-alias-guards.md)
- [Security for applications](../app-creators/security-for-applications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
