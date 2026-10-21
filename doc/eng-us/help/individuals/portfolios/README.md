---
title: Portfolios
audience: individual, website-creator
category: website-creation
order: 1610
summary: The section for photographers, illustrators, designers and other makers who show their work on Exponential - galleries, uploading many images at once, editing images in the browser with ezie, image variations, and the limits to know about.
keywords: portfolio, gallery, photography, illustration, artwork, images, image variations, ezie, image editor, ezmultiupload, multi-upload, watermark, rating, ezstarrating, tags, individual
type: help
---
# Portfolios

This section is for individuals who show their work: photographs, illustrations, paintings, design projects,
craft. A portfolio is mostly images, and Exponential treats each image as content with its own title, caption and
tags, not as a file in a folder.

## Contents

1. [What ships for a portfolio](#what-ships-for-a-portfolio)
2. [The pages of this section](#the-pages-of-this-section)
3. [Two ways to structure a portfolio](#two-ways-to-structure-a-portfolio)
4. [Limits to know about](#limits-to-know-about)

## What ships for a portfolio

| Need | What carries it | Source |
|---|---|---|
| A gallery page | The *Gallery* class (name, short description, description, image, tags, rating) | `sevenx_classes` package |
| One work | The *Image* class (name, caption, image, tags, rating) | same |
| Many uploads at once | The multi-upload page; folders and galleries offer it | `ezmultiupload` |
| Crop, rotate, watermark in the browser | The image editor in the edit form | `ezie` |
| Thumbnails and sizes | Image variations (aliases) in `image.ini` | kernel |
| Grouping by subject | The `tags` keyword field; `eztags` for a managed list | `sevenx_classes`; `eztags` |
| Visitor ratings | Star rating field | `ezstarrating` |

## The pages of this section

| Page | Read it when |
|---|---|
| [Building an image gallery](building-an-image-gallery.md) | You set up the first gallery |
| [Uploading many images](uploading-many-images.md) | You have dozens or hundreds of files |
| [Editing images with ezie](editing-images-with-ezie.md) | You want to crop, rotate or watermark without another program |
| [Image variations for portfolios](image-variations-for-portfolios.md) | You care how sharp and how fast your images are |

## Two ways to structure a portfolio

| Structure | Tree | Suits |
|---|---|---|
| **By series** | Portfolio (folder) > one *Gallery* per series > *Image* items | Photographers, illustrators with series or projects |
| **By project** | Portfolio (folder) > one *Article* per project, with a *Gallery* below it for the pictures | Designers who explain each project in text |

Tags add a second way to browse: by subject, technique or year, across series
([Tags and categories](../blogs/tags-and-categories.md)).

## Limits to know about

- **The original file is stored as you upload it** (the `original` alias). Remove location data and other
  metadata you do not want to share before uploading, and do not upload print-resolution files you do not want copied.
- **Watermarks** are applied per image with `ezie`, not automatically to every upload.
- **No print shop or licensing store** ships. The built-in shop can sell items, but selling licenses or prints
  needs your own setup ([Shop overview](../../creators/website-creators/shop-overview.md)).
- **No right-click protection.** Anything a browser can show can be saved; publish sizes you are willing to share.

## Related pages

- [Individuals: start here](../individuals-start-here.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Organizing the media library](../../creators/website-creators/organizing-the-media-library.md)
- [Media and uploads](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
