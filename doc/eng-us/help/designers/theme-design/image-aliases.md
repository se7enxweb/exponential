---
title: Image aliases
audience: designer
category: theme-design
order: 270
summary: How content images are scaled into named sizes (aliases) defined in image.ini, the shipped aliases, how a design adds its own, how templates ask for an alias, and how to serve responsive images with several aliases.
keywords: image alias, image.ini, AliasList, Filters, scaledownonly, reference alias, image sizes, responsive images, srcset, image_class, imagealias cache, thumbnails
type: help
---
# Image aliases

This page is for designers who decide in which sizes content images appear. An **alias** is a named size; the kernel
creates the scaled file the first time a template asks for it and stores it next to the original.

## Contents

1. [The shipped aliases](#the-shipped-aliases)
2. [Adding an alias in a design](#adding-an-alias-in-a-design)
3. [Asking for an alias in a template](#asking-for-an-alias-in-a-template)
4. [Responsive images](#responsive-images)
5. [After changing an alias](#after-changing-an-alias)

## The shipped aliases

`settings/image.ini [AliasSettings] AliasList[]` lists `reference`, `small`, `tiny`, `medium`, `large`, `rss`. Each has a
section of its own:

```ini
[reference]
Filters[]
Filters[]=geometry/scaledownonly=600;600

[small]
Reference=reference
Filters[]=geometry/scaledownonly=100;100

[medium]
Reference=reference
Filters[]=geometry/scaledownonly=200;200

[large]
Reference=reference
Filters[]=geometry/scaledownonly=300;300
```

`Reference=reference` means the alias is made from the `reference` alias, not from the original upload, which is
faster. `scaledownonly` keeps the proportions and never enlarges.

Theme extensions add their own: the media theme's `settings/image.ini.append.php` defines the sizes its templates ask
for, and the starter theme defines `cjw_i30` to `cjw_i1920` and 16:9 crops (by its README).

## Adding an alias in a design

In your design extension's `settings/image.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[AliasSettings]
AliasList[]=mytheme_card
AliasList[]=mytheme_hero

[mytheme_card]
Reference=reference
Filters[]=geometry/scaledownonly=480;480

[mytheme_hero]
Filters[]=geometry/scalewidthdownonly=1600
*/ ?>
```

Prefix alias names with your theme's name so they never collide with another extension's aliases. Look in
`settings/image.ini` for the filters your image converter supports (`geometry/scale`, `geometry/crop`,
`geometry/scalewidthdownonly` ...).

## Asking for an alias in a template

Through the datatype template, which prints a complete `<img>`:

```tpl
{attribute_view_gui attribute=$node.data_map.image image_class='mytheme_card'}
```

Or by hand, with the alias's URL, width and height:

```tpl
{def $img = $node.data_map.image.content['mytheme_card']}
<img src={$img.url|ezroot} width="{$img.width}" height="{$img.height}" alt="{$img.alternative_text|wash}" loading="lazy">
{undef $img}
```

## Responsive images

Offer several aliases with `srcset`, so phones download small files:

```tpl
{def $content = $node.data_map.image.content}
<img src={$content['mytheme_card'].url|ezroot}
     srcset="{$content['mytheme_card'].url|ezroot( 'no' )} {$content['mytheme_card'].width}w,
             {$content['mytheme_hero'].url|ezroot( 'no' )} {$content['mytheme_hero'].width}w"
     sizes="(min-width: 60em) 50vw, 100vw"
     width="{$content['mytheme_card'].width}" height="{$content['mytheme_card'].height}"
     alt="{$content.alternative_text|wash}">
{undef $content}
```

Every alias you name is created on first use, so a page that names five aliases creates five files per image the
first time it is seen.

## After changing an alias

Existing scaled files are not recreated automatically when you change an alias's filters. Clear the image alias
cache so they are made again: `php bin/php/ezcache.php --clear-id=imagealias` (it removes every scaled file; the first
visits afterwards are slower while they are recreated). Also clear the INI cache (`--clear-tag=ini`) for a new alias.

## Related pages

- [Images in a design](images-in-a-design.md)
- [Image performance](image-performance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
