---
title: Bilder und Bildvarianten
audience: website-creator, developer
category: website-creation
order: 330
summary: Wie Exponential Bilder speichert und Varianten (Aliasse) erzeugt - die mitgelieferten Aliasse, eigene in image.ini hinzufügen, sie in Templates verwenden, responsive Bilder, Konverter, Qualität und das Leeren erzeugter Varianten.
keywords: Bilder, Bildvariante, Bildalias, Varianten, Filter, responsive Bilder, JPEG-Qualität, Website-Ersteller, images, image alias, variations, image.ini, AliasList, filters, scaledownonly, image_class, responsive images, srcset, GD, ImageMagick, WebP, JPEG quality, imagealias cache, website creator
type: help
---
# Bilder und Bildvarianten

Diese Seite richtet sich an Website-Ersteller, die entscheiden, wie Bilder auf einer Website erscheinen. Ein Redakteur
lädt ein Bild hoch; die Website zeigt es in mehreren Größen, den sogenannten **Aliassen** (Bildvarianten), die beim
ersten Gebrauch nach Regeln in `settings/image.ini` erzeugt werden
([10.8 Bilder](../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)).

## Inhalt

1. [Die mitgelieferten Aliasse](#die-mitgelieferten-aliasse)
2. [Eigene Aliasse hinzufügen](#eigene-aliasse-hinzufügen)
3. [Aliasse in Templates verwenden](#aliasse-in-templates-verwenden)
4. [Responsive Bilder](#responsive-bilder)
5. [Konverter, Formate und Qualität](#konverter-formate-und-qualität)
6. [Nach der Änderung eines Alias](#nach-der-änderung-eines-alias)
7. [Checkliste](#checkliste)

## Die mitgelieferten Aliasse

`[AliasSettings] AliasList[]` in `settings/image.ini`:

| Alias | Regel |
|---|---|
| `reference` | verkleinert auf höchstens 600 x 600; die Grundlage der anderen |
| `small` | 100 x 100, aus `reference` |
| `tiny` | 30 x 30 |
| `medium` | 200 x 200 |
| `large` | 300 x 300 |
| `rss` | 88 x 31, für RSS-Feeds |
| `original` | die hochgeladene Datei selbst |

`scaledownonly` vergrößert ein kleines Bild nie. Themes fügen in ihrer `image.ini.append.php` eigene Aliasse hinzu.

## Eigene Aliasse hinzufügen

In Ihrer Design-Erweiterung:

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

Benennen Sie Aliasse nach ihrem Zweck (`hero`, `card`), nicht nach ihrer Größe: Wenn sich das Design ändert, ändert sich
die Größe, und der Name passt trotzdem. Welche Filter verfügbar sind, hängt vom Konverter ab; `settings/image.ini`
listet sie in den Blöcken `[GD]` und `[ImageMagick]` auf.

## Aliasse in Templates verwenden

```text
{attribute_view_gui attribute=$node.data_map.image image_class='card'}
```

rendert das Bildfeld mit dem Alias `card` und seinem Alternativtext. Für Ihr eigenes Markup lesen Sie den Alias:

```text
{def $img = $node.data_map.image.content['card']}
<img src={$img.url|ezroot} width="{$img.width}" height="{$img.height}" alt="{$img.alternative_text|wash}" loading="lazy">
{undef $img}
```

Geben Sie immer `width` und `height` aus (die Seite springt nicht, während Bilder laden) sowie den Alternativtext.
Bildfelder können einen Alternativtext verlangen
([Das Inhaltsmodell und die Bearbeitung](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)).

## Responsive Bilder

Definieren Sie zwei oder drei Aliasse desselben Bildes und bieten Sie sie mit `srcset` an:

```text
{def $c = $node.data_map.image.content}
<img src={$c['card'].url|ezroot}
     srcset="{$c['card'].url|ezroot(no)} {$c['card'].width}w, {$c['hero'].url|ezroot(no)} {$c['hero'].width}w"
     sizes="(min-width: 960px) 50vw, 100vw"
     width="{$c['card'].width}" height="{$c['card'].height}" alt="{$c.alternative_text|wash}">
{undef $c}
```

## Konverter, Formate und Qualität

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `[ImageConverterSettings] ImageConverters[]` | `GD`, `ImageMagick` | zuerst GD (in PHP, kein externes Programm); ImageMagick für Formate, die GD nicht lesen kann |
| `[MIMETypeSettings] Quality[]` | `image/jpeg;75` | JPEG-Qualität |
| `[MIMETypeSettings] ConversionRules[]` | WebP und GIF zu PNG, alles andere zu JPEG | das Ausgabeformat der Varianten |
| `[OutputSettings] AllowedOutputFormat[]` | JPEG, PNG, WebP, GIF | die Formate, die Seiten anzeigen dürfen |
| `[ImageUploadSettings] AllowedMIMETypes[]` | JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG | was die Redaktion hochladen darf |

SVG wird anders behandelt: Es wird bereinigt und nie skaliert ([SVG und Bildformate](svg-und-bildformate.md)).

## Nach der Änderung eines Alias

Erzeugte Varianten bleiben, bis sie entfernt werden:

```bash
php bin/php/cache.php imagealias clear --allow-root-user
```

Leeren Sie danach den View-Cache für Inhalte, damit Seiten auf die neuen Dateien verlinken.

## Checkliste

- [ ] Ein Alias pro Bildrolle des Designs, nach Zweck benannt.
- [ ] Jedes `<img>` hat `width`, `height` und einen Alternativtext.
- [ ] Große Bilder unterhalb des sichtbaren Bereichs verwenden `loading="lazy"`.
- [ ] JPEG-Qualität und Ausgabeformate bewusst gewählt.
- [ ] Alte Varianten nach einer Alias-Änderung geleert.

## Verwandte Seiten

- [Die Medienbibliothek organisieren](die-medienbibliothek-organisieren.md)
- [Medien und Uploads (für die Redaktion)](../../medien-und-uploads.md)
- [Velocity: statische Dateien und Bilder](../../../../eng-us/features/6.0/velocity-static-files-and-images.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
