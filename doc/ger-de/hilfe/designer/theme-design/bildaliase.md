---
title: Bildaliase
audience: designer
category: theme-design
order: 270
summary: Wie Inhaltsbilder in benannte Größen (Aliase) skaliert werden, die in image.ini definiert sind, die mitgelieferten Aliase, wie ein Design eigene hinzufügt, wie Templates einen Alias anfordern und wie Sie responsive Bilder mit mehreren Aliasen ausliefern.
keywords: Bildalias, image.ini, AliasList, Filters, scaledownonly, Alias reference, Bildgrößen, responsive Bilder, srcset, image_class, imagealias-Cache, Vorschaubilder, image alias, image sizes, responsive images, imagealias cache, thumbnails
type: help
---
# Bildaliase

Diese Seite ist für Designer, die entscheiden, in welchen Größen Inhaltsbilder erscheinen. Ein **Alias** ist eine benannte Größe; der
Kernel erzeugt die skalierte Datei, wenn ein Template sie zum ersten Mal anfordert, und speichert sie neben dem Original.

## Inhalt

1. [Die mitgelieferten Aliase](#die-mitgelieferten-aliase)
2. [Einen Alias in einem Design hinzufügen](#einen-alias-in-einem-design-hinzufügen)
3. [Einen Alias in einem Template anfordern](#einen-alias-in-einem-template-anfordern)
4. [Responsive Bilder](#responsive-bilder)
5. [Nach der Änderung eines Alias](#nach-der-änderung-eines-alias)

## Die mitgelieferten Aliase

`settings/image.ini [AliasSettings] AliasList[]` listet `reference`, `small`, `tiny`, `medium`, `large`, `rss`. Jeder hat einen eigenen
Abschnitt:

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

`Reference=reference` bedeutet, dass der Alias aus dem Alias `reference` erzeugt wird, nicht aus dem ursprünglichen Upload, was
schneller ist. `scaledownonly` behält die Proportionen und vergrößert nie.

Theme-Erweiterungen fügen eigene hinzu: Die `settings/image.ini.append.php` des Themes media definiert die Größen, die seine Templates
anfordern, und das Theme starter definiert `cjw_i30` bis `cjw_i1920` sowie Zuschnitte im Format 16:9 (laut seinem README).

## Einen Alias in einem Design hinzufügen

In `settings/image.ini.append.php` Ihrer Design-Erweiterung:

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

Stellen Sie Aliasnamen den Namen Ihres Themes voran, damit sie nie mit den Aliasen einer anderen Erweiterung kollidieren. Sehen Sie in
`settings/image.ini` nach, welche Filter Ihr Bildkonverter unterstützt (`geometry/scale`, `geometry/crop`,
`geometry/scalewidthdownonly` ...).

## Einen Alias in einem Template anfordern

Über das Datentyp-Template, das ein vollständiges `<img>` ausgibt:

```tpl
{attribute_view_gui attribute=$node.data_map.image image_class='mytheme_card'}
```

Oder von Hand, mit URL, Breite und Höhe des Alias:

```tpl
{def $img = $node.data_map.image.content['mytheme_card']}
<img src={$img.url|ezroot} width="{$img.width}" height="{$img.height}" alt="{$img.alternative_text|wash}" loading="lazy">
{undef $img}
```

## Responsive Bilder

Bieten Sie mit `srcset` mehrere Aliase an, damit Telefone kleine Dateien laden:

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

Jeder Alias, den Sie nennen, wird bei der ersten Verwendung erzeugt; eine Seite, die fünf Aliase nennt, erzeugt also beim ersten Aufruf
fünf Dateien pro Bild.

## Nach der Änderung eines Alias

Vorhandene skalierte Dateien werden nicht automatisch neu erzeugt, wenn Sie die Filter eines Alias ändern. Leeren Sie den Cache der
Bildaliase, damit sie neu entstehen: `php bin/php/ezcache.php --clear-id=imagealias` (er entfernt jede skalierte Datei; die ersten
Aufrufe danach sind langsamer, während sie neu erzeugt werden). Leeren Sie für einen neuen Alias auch den INI-Cache (`--clear-tag=ini`).

## Verwandte Seiten

- [Bilder in einem Design](bilder-in-einem-design.md)
- [Bild-Performance](bildleistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
