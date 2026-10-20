---
title: Image performance
audience: designer
category: theme-design
order: 660
summary: Fast images in a design: aliases at the displayed size, srcset and sizes, lazy loading below the first screen, eager loading for the main image, width and height against layout shift, modern formats, and SVG for graphics.
keywords: image performance, lazy loading, loading lazy, fetchpriority, srcset, sizes, image alias, webp, avif, responsive images, LCP image, decoding async
type: help
---
# Image performance

This page is for designers who decide how images are delivered. Images are usually the largest part of a page; a few
attributes and the right aliases make the biggest difference.

## Contents

1. [The right size](#the-right-size)
2. [Lazy and eager](#lazy-and-eager)
3. [Reserve the space](#reserve-the-space)
4. [Formats](#formats)
5. [A complete example](#a-complete-example)

## The right size

Never send a 1600 px image into a 300 px card. Define aliases for the sizes your design shows
([Image aliases](image-aliases.md)) and offer two or three of them with `srcset` and `sizes`, so the browser picks.

## Lazy and eager

- Images below the first screen: `loading="lazy"`. The browser fetches them when they come near.
- The main image of the first screen (a hero, the article's lead image): **not** lazy. It is usually the Largest
  Contentful Paint element; `fetchpriority="high"` tells the browser to fetch it first.
- `decoding="async"` lets the browser decode images off the main thread.

## Reserve the space

`width` and `height` attributes (the alias gives both), or a CSS `aspect-ratio`, let the browser reserve the box before
the image arrives, so text does not jump ([Measuring CLS and LCP](measuring-cls-and-lcp.md)).

## Formats

- Photos: JPEG, or WebP and AVIF where your image converter produces them; `settings/image.ini [ImageUploadSettings]
  AllowedMIMETypes[]` lists `image/webp` and `image/avif` as uploadable types.
- Logos, icons, diagrams: SVG ([SVG images](svg-images.md)).
- No images of text: use real text with a web font.

## A complete example

```tpl
{def $img = $node.data_map.image.content}
{if $node.data_map.image.has_content}
<img src={$img['mytheme_card'].url|ezroot}
     srcset="{$img['mytheme_card'].url|ezroot( 'no' )} {$img['mytheme_card'].width}w,
             {$img['mytheme_hero'].url|ezroot( 'no' )} {$img['mytheme_hero'].width}w"
     sizes="(min-width: 64em) 33vw, 100vw"
     width="{$img['mytheme_card'].width}" height="{$img['mytheme_card'].height}"
     alt="{$img.alternative_text|wash}" loading="lazy" decoding="async">
{/if}
{undef $img}
```

For the first image of a full view, drop `loading="lazy"` and add `fetchpriority="high"`.

## Related pages

- [Image aliases](image-aliases.md)
- [Images in a design](images-in-a-design.md)
- [Theme performance](theme-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
