---
title: Images and image variations
audience: website-creator, developer
category: website-creation
order: 330
summary: How Exponential stores images and makes variations (aliases) - the shipped aliases, adding your own in image.ini, using them in templates, responsive images, converters, quality and clearing generated variations.
keywords: images, image alias, variations, image.ini, AliasList, filters, scaledownonly, image_class, responsive images, srcset, GD, ImageMagick, WebP, JPEG quality, imagealias cache, website creator
type: help
---
# Images and image variations

This page is for website creators who decide how images appear on a site. An editor uploads one image; the site shows
it in several sizes, called **aliases** (variations), generated on first use from rules in `settings/image.ini`
([10.8 Images](../../../install/10-after-installing.md#108-search-indexing-and-image-handling)).

## Contents

1. [The shipped aliases](#the-shipped-aliases)
2. [Adding your own aliases](#adding-your-own-aliases)
3. [Using aliases in templates](#using-aliases-in-templates)
4. [Responsive images](#responsive-images)
5. [Converters, formats and quality](#converters-formats-and-quality)
6. [After changing an alias](#after-changing-an-alias)
7. [Checklist](#checklist)

## The shipped aliases

`[AliasSettings] AliasList[]` in `settings/image.ini`:

| Alias | Rule |
|---|---|
| `reference` | scaled down to fit 600 x 600; the base of the others |
| `small` | 100 x 100, from `reference` |
| `tiny` | 30 x 30 |
| `medium` | 200 x 200 |
| `large` | 300 x 300 |
| `rss` | 88 x 31, for RSS feeds |
| `original` | the uploaded file itself |

`scaledownonly` never enlarges a small image. Themes add their own aliases in their `image.ini.append.php`.

## Adding your own aliases

In your design extension:

```ini
# extension/mysite_design/settings/image.ini.append.php
[AliasSettings]
AliasList[]=hero
AliasList[]=card

[hero]
Reference=
Filters[]
Filters[]=geometry/scalewidthdownonly=1600

[card]
Reference=reference
Filters[]
Filters[]=geometry/scaledownonly=480;320
```

Name aliases after their purpose (`hero`, `card`), not their size: when the design changes, the size changes and the
name still fits. The filters available depend on the converter; `settings/image.ini` lists them in the `[GD]` and
`[ImageMagick]` blocks.

## Using aliases in templates

```text
{attribute_view_gui attribute=$node.data_map.image image_class='card'}
```

renders the image field with the `card` alias and its alternative text. For your own markup, read the alias:

```text
{def $img = $node.data_map.image.content['card']}
<img src={$img.url|ezroot} width="{$img.width}" height="{$img.height}" alt="{$img.alternative_text|wash}" loading="lazy">
{undef $img}
```

Always print `width` and `height` (the page does not jump while images load) and the alternative text. Image fields can
require an alternative text ([The content model and editing](../../../guides/content-model-and-editing.md)).

## Responsive images

Define two or three aliases of the same image and offer them with `srcset`:

```text
{def $c = $node.data_map.image.content}
<img src={$c['card'].url|ezroot}
     srcset="{$c['card'].url|ezroot(no)} {$c['card'].width}w, {$c['hero'].url|ezroot(no)} {$c['hero'].width}w"
     sizes="(min-width: 960px) 50vw, 100vw"
     width="{$c['card'].width}" height="{$c['card'].height}" alt="{$c.alternative_text|wash}">
{undef $c}
```

## Converters, formats and quality

| Setting | Shipped | Meaning |
|---|---|---|
| `[ImageConverterSettings] ImageConverters[]` | `GD`, `ImageMagick` | GD first (in PHP, no external program); ImageMagick for formats GD cannot read |
| `[MIMETypeSettings] Quality[]` | `image/jpeg;75` | JPEG quality |
| `[MIMETypeSettings] ConversionRules[]` | WebP and GIF to PNG, everything else to JPEG | the output format of variations |
| `[OutputSettings] AllowedOutputFormat[]` | JPEG, PNG, WebP, GIF | the formats pages may show |
| `[ImageUploadSettings] AllowedMIMETypes[]` | JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG | what editors may upload |

SVG is handled differently: it is sanitized and never scaled ([SVG and image formats](svg-and-image-formats.md)).

## After changing an alias

Generated variations stay until removed:

```bash
php bin/php/cache.php imagealias clear --allow-root-user
```

Then clear the content view cache so pages link the new files.

## Checklist

- [ ] One alias per image role of the design, named by purpose.
- [ ] Every `<img>` has `width`, `height` and alternative text.
- [ ] Large images use `loading="lazy"` below the fold.
- [ ] JPEG quality and output formats chosen consciously.
- [ ] Old variations cleared after an alias change.

## Related pages

- [Organizing the media library](organizing-the-media-library.md)
- [Media and uploads (for editors)](../../media-and-uploads.md)
- [Velocity: static files and images](../../../features/6.0/velocity-static-files-and-images.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
