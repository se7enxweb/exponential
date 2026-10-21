---
title: Images and image aliases
audience: cms-developer
category: cms-development
order: 16
summary: For CMS developers, how images are modelled, stored and scaled. It covers image attributes against image objects, the media library, image aliases in image.ini, the converters, alternative text, responsive output in templates, and clearing scaled files after a change.
keywords: images, ezimage, image class, media library, image alias, image.ini, AliasList, Reference, Filters, scaledownonly, ImageMagick, GD, alternative text, srcset, responsive images, imagealias cache
type: help
---
# Images and image aliases

This page is for developers who decide where images live in the content model and in which sizes the site shows
them. Exponential stores the original once and makes every size on demand from named **image aliases**, so a
redesign changes settings, not uploads.

## Contents

1. [Image attribute or image object](#image-attribute-or-image-object)
2. [The media library](#the-media-library)
3. [Image aliases](#image-aliases)
4. [Defining an alias for your design](#defining-an-alias-for-your-design)
5. [Images in templates](#images-in-templates)
6. [Alternative text](#alternative-text)
7. [Converters: GD and ImageMagick](#converters-gd-and-imagemagick)
8. [After a change](#after-a-change)

## Image attribute or image object

| Model | How | Use it for |
|---|---|---|
| An `ezimage` attribute on the item | the article class has an `image` attribute | an image that belongs to this one item: a portrait, a product photo |
| An image object, related | an `image` object in the media library, referenced by an `ezobjectrelationlist` attribute | an image used by several items, replaced once for all |
| An image object, embedded | the same object embedded in rich text | images placed in the flow of the text |

Image objects have their own name, caption and alternative text, appear in the media library and carry their own
permissions. Choosing between the two is one of the decisions in
[Choosing datatypes](choosing-datatypes.md#decisions-that-are-hard-to-undo).

## The media library

The media library is the subtree below node 43 (`settings/content.ini [NodeSettings] MediaRootNode`), in the section
Media. Images and files placed there are not part of the site's navigation. The Anonymous role of a new installation
reads only the section Standard ([Sections](../../guides/sections.md#2-the-sections-of-a-new-installation)), so check
your roles when visitors must open media objects directly. Organize the library in folders by topic or by site, not by
date. Uploading in bulk is covered by the upload page of `ezmultiupload` ([Media and uploads](../media-and-uploads.md)).

## Image aliases

An alias is a named size and treatment, defined in `image.ini`. The shipped list
(`[AliasSettings] AliasList[]`) is `reference`, `small`, `tiny`, `medium`, `large` and `rss`:

```ini
[reference]
Filters[]
Filters[]=geometry/scaledownonly=600;600

[medium]
Reference=reference
Filters[]=geometry/scaledownonly=200;200
```

`Reference` names the alias the scaling starts from (here the 600 pixel `reference`, rather than a camera original),
and `Filters[]` lists the operations in order. `scaledownonly` never enlarges a small image. Every alias is created
the first time a page asks for it and kept as a file in the storage directory.

## Defining an alias for your design

Put your aliases in your design extension, prefixed with its name so they never clash with shipped ones:

```ini
# extension/exp_mysite/settings/image.ini.append.php
<?php /* #?ini charset="utf-8"?

[AliasSettings]
AliasList[]=exp_mysite_card
AliasList[]=exp_mysite_hero

[exp_mysite_card]
Reference=reference
Filters[]=geometry/scaledownonly=480;320

[exp_mysite_hero]
Filters[]=geometry/scaledownonly=1600;900
*/ ?>
```

The hero alias has no `Reference`, so it scales from the original: a hero larger than 600 pixels must not start from
`reference`. Designers have the same recipe with responsive output:
[Image aliases](../designers/theme-design/image-aliases.md).

## Images in templates

The datatype template prints a complete `<img>` for an alias:

```smarty
{attribute_view_gui attribute=$node.data_map.image image_class='exp_mysite_card'}
```

For your own markup, read the alias from the attribute's content:

```smarty
{def $img = $node.data_map.image.content['exp_mysite_card']}
<img src={$img.url|ezroot} width="{$img.width}" height="{$img.height}"
     alt="{$img.alternative_text|wash}" loading="lazy">
{undef $img}
```

Always print `width` and `height`, so the page does not jump while images load
([Image performance](../designers/theme-design/image-performance.md)). Every alias a page names is created on first use,
so do not name aliases you do not show.

## Alternative text

An `ezimage` attribute stores an alternative text with the image. A class can make it required: tick **Alternative
text is required** on the image attribute in the class editor; editors then cannot store an image without one. Imports
and bulk uploads do not ask for it, so fill it afterwards
([Alternative text is required](../../guides/content-model-and-editing.md#alternative-text-is-required-image-fields)).
A decorative image gets an empty `alt`, deliberately, in the template.

## Converters: GD and ImageMagick

`image.ini [ImageConverterSettings] ImageConverters[]` lists the converters in order: `GD` (the PHP extension) and
`ImageMagick` (the `convert` program). The Composer metadata of Exponential says GD is required unless ImageMagick is
installed. On a host, check which is present before you rely on filters that only one of them supports
([Image processing and media](../hosting/basics/image-processing-and-media.md)).

## After a change

Changing the filters of an existing alias does not recreate the files already made. Clear them, and the INI cache for
a new or changed alias:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=imagealias --allow-root-user
```

The first visits afterwards are slower while the scaled files are made again; on a large site do it in a quiet hour.

## Related pages

- [Relations and embeds](relations-and-embeds.md)
- [Images in a design](../designers/theme-design/images-in-a-design.md)
- [Caching for CMS developers](caching-for-cms-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
