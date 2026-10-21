---
title: "Designing image crops and art direction"
audience: theme-designer
category: theme-design
order: 27
summary: How theme designers plan the image sizes and crops of an Exponential theme - an image slot inventory, image aliases and their filters, what the converters can and cannot crop, CSS object-fit, responsive sources, the image editor editors can use, and what to hand the developer.
keywords: theme designer, images, image aliases, crop, art direction, aspect ratio, object-fit, srcset, picture, image.ini, geometry filters, ezie, image editor, responsive images
type: help
---
# Designing image crops and art direction

This page is for theme designers who decide how images appear: which sizes, which shapes, and what happens to a
portrait photo in a landscape slot. In Exponential, editors upload one original, and the theme asks for **image
aliases**: named, generated variants made by filters such as "scale down to fit 480 by 480". The settings and
template code are in [Image aliases](../designers/theme-design/image-aliases.md); performance in
[Image performance](../designers/theme-design/image-performance.md). This page is the design side.

## Contents

1. [The slot inventory](#the-slot-inventory)
2. [What an alias can do](#what-an-alias-can-do)
3. [Fixed ratios: crop in the alias or in CSS](#fixed-ratios-crop-in-the-alias-or-in-css)
4. [Art direction](#art-direction)
5. [What editors can do](#what-editors-can-do)
6. [Sizes for sharp and light images](#sizes-for-sharp-and-light-images)
7. [The image sheet](#the-image-sheet)

## The slot inventory

List every place an image appears, with its displayed size at each width:

| Slot | Ratio | Largest display width | Smallest | Can be empty? |
|---|---|---|---|---|
| Article hero | 16:9 | 1200 px | 375 px | yes |
| Card | 3:2 | 400 px | 300 px | yes |
| Line item thumbnail | 1:1 | 96 px | 64 px | yes |
| Embed in rich text | as uploaded | text column | 375 px | no |
| Open Graph image | as uploaded | 1200 px | | yes ([Social share and metadata images](social-share-and-metadata-images.md)) |

Keep the number of ratios small: each one is an alias (or several) and a test case.

## What an alias can do

An alias is defined in `image.ini` (in your design extension's `settings/image.ini.append.php`) as a list of filters
applied to the original or to another alias. The kernel's own aliases (`reference`, `small`, `medium`, `large` and
others) use `geometry/scaledownonly`, which fits the image into a box without enlarging it and keeps its proportions.
The filters both image converters support are listed as comments in `settings/image.ini`; among them:

| Filter | Effect |
|---|---|
| `geometry/scaledownonly=<w>;<h>` | fit into a box, never enlarge, keep proportions |
| `geometry/scalewidthdownonly=<w>` | fit to a width, never enlarge |
| `geometry/scaleexact=<w>;<h>` | force exactly that size (distorts if the ratio differs) |
| `geometry/crop=...` | cut a rectangle at a fixed position |

So an alias can make an image smaller and can cut a fixed rectangle, but it does not know where the subject of a photo
is. There is no focal-point crop in the shipped filters. A fixed crop from the top-left corner of a landscape and a
portrait gives very different results; check it with real uploads.

## Fixed ratios: crop in the alias or in CSS

For slots with a fixed ratio there are two ways:

| Way | How | Trade-off |
|---|---|---|
| CSS | the alias scales down; the slot has `aspect-ratio: 3 / 2` and the image `object-fit: cover` | the visible crop is centered (or set with `object-position`); a little more is downloaded |
| Alias | scale, then crop to the ratio in the alias | smaller files; the crop position is fixed for every image |

For most content sites, CSS is the safer choice: the full image is kept, the crop adapts to the slot, and
`object-position` can move the visible part for a whole slot type (for example toward the top for portraits of
people). Never put a per-image `object-position` in a `style=""` attribute; use a class or accept the centered crop.

## Art direction

Art direction means showing a different crop at different widths: a wide panorama on desktops, a tighter crop on
phones. With one original and fixed-position filters, true per-image art direction is limited. Options, in order of
effort:

1. **CSS only:** the same image with a different `aspect-ratio` and `object-position` per breakpoint.
2. **Two aliases in a `picture` element:** for example a wide alias for large screens and a square one for small
   screens, chosen with `media` conditions ([Image aliases](../designers/theme-design/image-aliases.md#responsive-images)
   shows `srcset`; the same aliases work in `<source>` elements).
3. **Two image fields:** when the client needs control, the content class gets a second, optional image for small
   screens. That is a content model change ([Mapping a design to content classes](mapping-a-design-to-content-classes.md)).

## What editors can do

Where the ezie extension (the online image editor) is installed, editors can crop, rotate and flip an image while
editing its object. The change is made to the stored image, so every alias follows it. It is a good tool for "this
photo needs a tighter crop" and a reason to choose CSS crops that keep most of the picture.

## Sizes for sharp and light images

- The alias should be about the largest displayed width, and twice that for small slots that must be sharp on
  high-density screens, offered through `srcset`.
- Every alias is generated on first use and kept, so a template that names many aliases creates many files per image.
  Prefer few aliases, each used in several slots.
- Name aliases with your theme's prefix (`mytheme_card`), so they never collide with another extension's.
- Images in the first screen are not lazy-loaded; the rest are ([Measuring CLS and LCP](../designers/theme-design/measuring-cls-and-lcp.md)).

## The image sheet

Hand over one row per slot:

| Slot | Alias name | Filter | Ratio and crop | srcset partners | Empty design |
|---|---|---|---|---|---|
| Card | `mytheme_card` | scale down to 480 by 480 | 3:2, CSS cover, centered | `mytheme_card_2x` | text-only card |

## Related pages

- [Image aliases](../designers/theme-design/image-aliases.md)
- [Images and image aliases](../cms-developers/images-and-image-aliases.md)
- [SVG images](../designers/theme-design/svg-images.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
