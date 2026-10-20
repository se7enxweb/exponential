---
title: Erweiterungspunkte in image.ini
audience: developer, admin
category: examples
summary: Einstellungen von image.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (15, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, image.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in image.ini

Einstellungen von `settings/image.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/image.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## reference Filters

Ausgeliefert: eine Liste mit 1: `geometry/scaledownonly=600;600`. [Die Einstellung](../ini-einstellungen/image/reference.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[reference]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## small Filters

Ausgeliefert: eine Liste mit 1: `geometry/scaledownonly=100;100`. [Die Einstellung](../ini-einstellungen/image/small.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[small]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## tiny Filters

Ausgeliefert: eine Liste mit 1: `geometry/scaledownonly=30;30`. [Die Einstellung](../ini-einstellungen/image/tiny.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[tiny]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## medium Filters

Ausgeliefert: eine Liste mit 1: `geometry/scaledownonly=200;200`. [Die Einstellung](../ini-einstellungen/image/medium.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[medium]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## large Filters

Ausgeliefert: eine Liste mit 1: `geometry/scaledownonly=300;300`. [Die Einstellung](../ini-einstellungen/image/large.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[large]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## rss Filters

Ausgeliefert: eine Liste mit 1: `geometry/scale=88;31`. [Die Einstellung](../ini-einstellungen/image/rss.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[rss]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AnimatedGIF DisallowedFilters

Ausgeliefert: eine Liste mit 1: `*`. [Die Einstellung](../ini-einstellungen/image/AnimatedGIF.md#disallowedfilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[AnimatedGIF]
DisallowedFilters[]=expExampleDisallowedFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## AnimatedGIF ExtraFilters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/image/AnimatedGIF.md#extrafilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[AnimatedGIF]
ExtraFilters[]=expExampleExtraFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## LayeredPhotoshop ExtraFilters

Ausgeliefert: eine Liste mit 2: `flatten`, `resize=200x`. [Die Einstellung](../ini-einstellungen/image/LayeredPhotoshop.md#extrafilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[LayeredPhotoshop]
ExtraFilters[]=expExampleExtraFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## ImageConverterSettings ImageConverters

Ausgeliefert: eine Liste mit 2: `GD`, `ImageMagick`. [Die Einstellung](../ini-einstellungen/image/ImageConverterSettings.md#imageconverters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[ImageConverterSettings]
ImageConverters[]=expExampleImageConverter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## GD Handler

Ausgeliefert: `eZImageGDFactory`. [Die Einstellung](../ini-einstellungen/image/GD.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[GD]
Handler=eZImageGDFactory    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ImageMagick Handler

Ausgeliefert: `eZImageShellFactory`. [Die Einstellung](../ini-einstellungen/image/ImageMagick.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[ImageMagick]
Handler=eZImageShellFactory    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## ImageMagick Filters

Ausgeliefert: eine Liste mit 19: `geometry/scale=-geometry %1x%2`, `geometry/scalewidth=-geometry %1`, `geometry/scaleheight=-geometry x%1`, `geometry/scaledownonly=-geometry %1x%2>`, `geometry/scalewidthdownonly=-geometry %1>`, `geometry/scaleheightdownonly=-geometry x%1>`, `geometry/scaleexact=-geometry %1x%2!`, `geometry/scalepercent=-geometry %1x%2%`, `geometry/crop=-crop %1x%2+%3+%4`, `filter/noise=-noise %1`, `filter/swirl=-swirl %1`, `colorspace/gray=-colorspace GRAY` .... [Die Einstellung](../ini-einstellungen/image/ImageMagick.md#filters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[ImageMagick]
Filters[]=expExampleFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## GIFAnalyzer Handler

Ausgeliefert: `ezgif`. [Die Einstellung](../ini-einstellungen/image/GIFAnalyzer.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[GIFAnalyzer]
Handler=ezgif    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## EXIFAnalyzer Handler

Ausgeliefert: `ezexif`. [Die Einstellung](../ini-einstellungen/image/EXIFAnalyzer.md#handler).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/image.ini.append.php
[EXIFAnalyzer]
Handler=ezexif    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../../../eng-us/examples/cookbook/extension-skeleton.md)
- [Die Blöcke von image.ini](../ini-einstellungen/image/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
