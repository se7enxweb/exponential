---
title: "Social share and metadata images"
audience: theme-designer
category: theme-design
order: 28
summary: What theme designers need to know about the image and text a page shows when it is shared - Open Graph and Twitter tags in the head, how the xrowmetadata extension chooses and sizes the image, a default image per class, safe areas, and how to test a share preview.
keywords: theme designer, social sharing, open graph, og:image, twitter card, share image, metadata, xrowmetadata, default image, safe area, preview card
type: help
---
# Social share and metadata images

This page is for theme designers who want shared links to look as designed. When someone shares a page, the social
network or chat program reads tags in the page's head (Open Graph `og:*` and `twitter:*`) and builds a preview card
from a title, a description and an image. The theme prints those tags; the content supplies the values. How to print
them is covered for site builders in [Metadata and social sharing](../creators/website-creators/metadata-and-social-sharing.md);
this page is what a designer decides.

## Contents

1. [What the preview card is made of](#what-the-preview-card-is-made-of)
2. [Where the values come from](#where-the-values-come-from)
3. [The share image](#the-share-image)
4. [A default image](#a-default-image)
5. [Title and description lengths](#title-and-description-lengths)
6. [Testing a preview](#testing-a-preview)

## What the preview card is made of

| Tag | Content | Design concern |
|---|---|---|
| `og:title` | the page title | length; brand suffix or not |
| `og:description` | a short summary | length, tone |
| `og:image` | an absolute image address, with width, height and alternative text | the image itself |
| `og:site_name` | the site's name | consistent with the header |
| `twitter:*` | the same values for that network | |

The look of the card (its layout, fonts, colors) belongs to the network, not to you. You control the image and the
words.

## Where the values come from

The shipped themes differ:

- the **simple** theme prints `og:title`, `og:description` and `og:type` in its `page_head.tpl`, from the page's
  metadata when it exists and from the site title and description otherwise;
- the **media** theme includes the xrowmetadata extension's template `xrowmetadata/opengraph_image.tpl` from its
  head template, which prints `og:image` (with width, height, type and alternative text) and `twitter:image`.

With the **xrowmetadata** extension, editors can fill a metadata field on objects of the classes that have one: title,
description, keywords, and an Open Graph image chosen from the media library. Its settings
(`xrowmetadata.ini`, group `[OpenGraph]`) name the image classes editors may choose, the attributes searched for a
picture (by default `site_opengraph_image`, `image`, `site_logo`, `file`), and the image alias used for the tag, by
default `opengraph`. The extension defines that alias as "scale down to fit 1200 by 1200". When the object has no own
choice, a default for the class is used, and when neither exists, no image tag is printed.

Check which of these your installation uses before designing; a site without xrowmetadata needs its own fields and
templates ([Metadata and social sharing](../creators/website-creators/metadata-and-social-sharing.md#with-your-own-fields)).

## The share image

Because the shipped alias scales down without cropping, the share image keeps the proportions of the picture
editors chose. Networks then crop it into their own card shapes, which differ between networks and change over time.
Practical rules:

- design share images in a landscape ratio; a portrait photo will be cut hard;
- keep the important part (faces, the logo, any text) in a **safe area** in the middle, away from all edges;
- avoid small text in share images; it is unreadable in a chat preview and cannot be translated;
- make sure the image works on light and dark backgrounds, because cards appear in both.

If the client wants a fixed share format for every page, that is a separate image field and a separate alias with a
crop; agree it with the developer ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)).

## A default image

Many pages have no picture at all. Design a default share image per site (and per brand in a multisite installation):
the logo or a brand pattern on the brand color, inside the safe area. With xrowmetadata it can be set as the class
default; without it, the head template falls back to it. The image is a normal file of the design or an object in the
media library, decided with the developer.

## Title and description lengths

The xrowmetadata edit form limits what editors write: by default 65 characters for the title and 150 for the
description (`[EditorInputSettings] MaxTitle` and `MaxDescription`). Write your examples to those limits, and design
the visible page title separately: the share title may be shorter than the headline.

## Testing a preview

1. Open the page source and check that the tags are present, with an absolute `https://` image address.
2. Open the image address on its own: it must load without signing in.
3. Paste the page address into the preview or debugging tool of the networks the client cares about, and into a chat
   program. Networks cache previews; their debugging tools can refresh them.
4. Check a page with its own image, a page with only the class default, and a page with neither.

## Related pages

- [Metadata and social sharing](../creators/website-creators/metadata-and-social-sharing.md)
- [Favicons and touch icons](../designers/theme-design/favicons-and-touch-icons.md)
- [Designing for several brands in multisite](designing-for-several-brands-in-multisite.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
