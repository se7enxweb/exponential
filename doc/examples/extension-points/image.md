---
title: Extension points in image.ini
audience: developer, admin
category: examples
summary: Settings of image.ini that list handlers, classes or directories an extension can add to (15, found by name).
keywords: image.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Extension points in image.ini

Settings of `settings/image.ini` whose name says they hold handlers, classes, filters, providers or directories that an extension can add to or replace. **Found by the setting's name: a heuristic.** Read the setting's comment (linked) before relying on it.

An extension adds to a list in its own `settings/image.ini.append.php` (active extensions' settings are read after the kernel's and before `settings/override`):

## reference Filters

Shipped: a list of 1: `geometry/scaledownonly=600;600`. [The setting](../ini-settings/image/reference.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[reference]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## small Filters

Shipped: a list of 1: `geometry/scaledownonly=100;100`. [The setting](../ini-settings/image/small.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[small]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## tiny Filters

Shipped: a list of 1: `geometry/scaledownonly=30;30`. [The setting](../ini-settings/image/tiny.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[tiny]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## medium Filters

Shipped: a list of 1: `geometry/scaledownonly=200;200`. [The setting](../ini-settings/image/medium.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[medium]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## large Filters

Shipped: a list of 1: `geometry/scaledownonly=300;300`. [The setting](../ini-settings/image/large.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[large]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## rss Filters

Shipped: a list of 1: `geometry/scale=88;31`. [The setting](../ini-settings/image/rss.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[rss]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## AnimatedGIF DisallowedFilters

Shipped: a list of 1: `*`. [The setting](../ini-settings/image/AnimatedGIF.md#disallowedfilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[AnimatedGIF]
DisallowedFilters[]=expExampleDisallowedFilter    # your class or value, added to the list
*/ ?>
```

## AnimatedGIF ExtraFilters

Shipped: an empty list. [The setting](../ini-settings/image/AnimatedGIF.md#extrafilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[AnimatedGIF]
ExtraFilters[]=expExampleExtraFilter    # your class or value, added to the list
*/ ?>
```

## LayeredPhotoshop ExtraFilters

Shipped: a list of 2: `flatten`, `resize=200x`. [The setting](../ini-settings/image/LayeredPhotoshop.md#extrafilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[LayeredPhotoshop]
ExtraFilters[]=expExampleExtraFilter    # your class or value, added to the list
*/ ?>
```

## ImageConverterSettings ImageConverters

Shipped: a list of 2: `GD`, `ImageMagick`. [The setting](../ini-settings/image/ImageConverterSettings.md#imageconverters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[ImageConverterSettings]
ImageConverters[]=expExampleImageConverter    # your class or value, added to the list
*/ ?>
```

## GD Handler

Shipped: `eZImageGDFactory`. [The setting](../ini-settings/image/GD.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[GD]
Handler=eZImageGDFactory    # replace with your class or value
*/ ?>
```

## ImageMagick Handler

Shipped: `eZImageShellFactory`. [The setting](../ini-settings/image/ImageMagick.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[ImageMagick]
Handler=eZImageShellFactory    # replace with your class or value
*/ ?>
```

## ImageMagick Filters

Shipped: a list of 19: `geometry/scale=-geometry %1x%2`, `geometry/scalewidth=-geometry %1`, `geometry/scaleheight=-geometry x%1`, `geometry/scaledownonly=-geometry %1x%2>`, `geometry/scalewidthdownonly=-geometry %1>`, `geometry/scaleheightdownonly=-geometry x%1>`, `geometry/scaleexact=-geometry %1x%2!`, `geometry/scalepercent=-geometry %1x%2%`, `geometry/crop=-crop %1x%2+%3+%4`, `filter/noise=-noise %1`, `filter/swirl=-swirl %1`, `colorspace/gray=-colorspace GRAY` .... [The setting](../ini-settings/image/ImageMagick.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[ImageMagick]
Filters[]=expExampleFilter    # your class or value, added to the list
*/ ?>
```

## GIFAnalyzer Handler

Shipped: `ezgif`. [The setting](../ini-settings/image/GIFAnalyzer.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[GIFAnalyzer]
Handler=ezgif    # replace with your class or value
*/ ?>
```

## EXIFAnalyzer Handler

Shipped: `ezexif`. [The setting](../ini-settings/image/EXIFAnalyzer.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[EXIFAnalyzer]
Handler=ezexif    # replace with your class or value
*/ ?>
```

## Related

- [All extension points](README.md)
- [An extension skeleton](../cookbook/extension-skeleton.md)
- [The blocks of image.ini](../ini-settings/image/README.md)

---

Generated by `./console exp:help:examples` from `settings/image.ini`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
