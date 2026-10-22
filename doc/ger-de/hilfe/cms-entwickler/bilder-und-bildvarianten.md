---
title: Bilder und Bildvarianten
audience: cms-developer
category: cms-development
order: 16
summary: Für CMS-Entwickler, wie Bilder modelliert, gespeichert und skaliert werden. Die Seite behandelt Bildattribute gegenüber Bildobjekten, die Medienbibliothek, Bildvarianten (Image Aliases) in image.ini, die Konverter, Alternativtext, responsive Ausgabe in Templates und das Leeren skalierter Dateien nach einer Änderung.
keywords: Bilder, ezimage, Bildklasse, Medienbibliothek, Bildvariante, Image Alias, image.ini, AliasList, Reference, Filters, scaledownonly, ImageMagick, GD, Alternativtext, srcset, responsive Bilder, imagealias-Cache, images, media library, image alias, alternative text, responsive images
type: help
---
# Bilder und Bildvarianten

Diese Seite richtet sich an Entwickler, die entscheiden, wo Bilder im Inhaltsmodell liegen und in welchen Größen die
Website sie zeigt. Exponential speichert das Original einmal und erzeugt jede Größe bei Bedarf aus benannten
**Bildvarianten** (Image Aliases); ein Redesign ändert also Einstellungen, keine Uploads.

## Inhalt

1. [Bildattribut oder Bildobjekt](#bildattribut-oder-bildobjekt)
2. [Die Medienbibliothek](#die-medienbibliothek)
3. [Bildvarianten](#bildvarianten)
4. [Eine Variante für Ihr Design festlegen](#eine-variante-für-ihr-design-festlegen)
5. [Bilder in Templates](#bilder-in-templates)
6. [Alternativtext](#alternativtext)
7. [Konverter: GD und ImageMagick](#konverter-gd-und-imagemagick)
8. [Nach einer Änderung](#nach-einer-änderung)

## Bildattribut oder Bildobjekt

| Modell | Wie | Verwenden Sie es für |
|---|---|---|
| Ein `ezimage`-Attribut am Element | die Artikelklasse hat ein Attribut `image` | ein Bild, das zu genau diesem Element gehört: ein Porträt, ein Produktfoto |
| Ein Bildobjekt, verknüpft | ein `image`-Objekt in der Medienbibliothek, referenziert über ein `ezobjectrelationlist`-Attribut | ein Bild, das mehrere Elemente verwenden und das einmal für alle ersetzt wird |
| Ein Bildobjekt, eingebettet | dasselbe Objekt, in Rich Text eingebettet | Bilder im Fluss des Textes |

Bildobjekte haben einen eigenen Namen, eine Bildunterschrift und einen Alternativtext, erscheinen in der
Medienbibliothek und haben eigene Berechtigungen. Die Wahl zwischen beiden ist eine der Entscheidungen unter
[Datentypen wählen](datentypen-waehlen.md#entscheidungen-die-sich-schwer-rückgängig-machen-lassen).

## Die Medienbibliothek

Die Medienbibliothek ist der Teilbaum unter Knoten 43 (`settings/content.ini [NodeSettings] MediaRootNode`), in der
Sektion Media. Bilder und Dateien dort sind nicht Teil der Navigation der Website. Die Rolle Anonymous einer neuen
Installation liest nur die Sektion Standard
([Sektionen](../../anleitungen/sektionen.md#2-die-sektionen-einer-neuen-installation)); prüfen Sie also Ihre Rollen, wenn
Besucher Medienobjekte direkt öffnen sollen. Ordnen Sie die Bibliothek in Ordnern nach Thema oder Website, nicht nach
Datum. Das Hochladen vieler Dateien auf einmal übernimmt die Upload-Seite von `ezmultiupload`
([Medien und Uploads](../medien-und-uploads.md)).

## Bildvarianten

Eine Variante ist eine benannte Größe und Behandlung, festgelegt in `image.ini`. Die mitgelieferte Liste
(`[AliasSettings] AliasList[]`) ist `reference`, `small`, `tiny`, `medium`, `large` und `rss`:

```ini
[reference]
Filters[]
Filters[]=geometry/scaledownonly=600;600

[medium]
Reference=reference
Filters[]=geometry/scaledownonly=200;200
```

`Reference` nennt die Variante, von der die Skalierung ausgeht (hier die 600 Pixel große `reference` statt eines
Kameraoriginals), und `Filters[]` listet die Operationen der Reihe nach. `scaledownonly` vergrößert ein kleines Bild
nie. Jede Variante wird erzeugt, wenn eine Seite sie zum ersten Mal anfordert, und als Datei im Speicherverzeichnis
aufbewahrt.

## Eine Variante für Ihr Design festlegen

Legen Sie Ihre Varianten in Ihrer Design-Erweiterung ab, mit deren Namen als Präfix, damit sie nie mit mitgelieferten
kollidieren:

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

Die Hero-Variante hat kein `Reference`, sie skaliert also vom Original: Ein Hero-Bild, das größer als 600 Pixel ist,
darf nicht von `reference` ausgehen. Designer haben dasselbe Rezept mit responsiver Ausgabe:
[Bildvarianten](../designer/theme-design/bildaliase.md).

## Bilder in Templates

Das Template des Datentyps gibt ein vollständiges `<img>` für eine Variante aus:

```smarty
{attribute_view_gui attribute=$node.data_map.image image_class='exp_mysite_card'}
```

Für eigenes Markup lesen Sie die Variante aus dem Inhalt des Attributs:

```smarty
{def $img = $node.data_map.image.content['exp_mysite_card']}
<img src={$img.url|ezroot} width="{$img.width}" height="{$img.height}"
     alt="{$img.alternative_text|wash}" loading="lazy">
{undef $img}
```

Geben Sie immer `width` und `height` aus, damit die Seite beim Laden der Bilder nicht springt
([Bildleistung](../designer/theme-design/bildleistung.md)). Jede Variante, die eine Seite nennt, wird bei der
ersten Verwendung erzeugt; nennen Sie also keine Varianten, die Sie nicht zeigen.

## Alternativtext

Ein `ezimage`-Attribut speichert mit dem Bild einen Alternativtext. Eine Klasse kann ihn verpflichtend machen: Setzen
Sie im Klasseneditor beim Bildattribut das Häkchen bei **Alternativtext ist erforderlich**; Redakteure können dann
kein Bild ohne ihn speichern. Importe und Massen-Uploads fragen nicht danach; füllen Sie ihn also nachträglich
([Alternativtext ist erforderlich](../../anleitungen/inhaltsmodell-und-bearbeitung.md#alternativtext-ist-erforderlich-bildfelder)).
Ein dekoratives Bild erhält im Template absichtlich ein leeres `alt`.

## Konverter: GD und ImageMagick

`image.ini [ImageConverterSettings] ImageConverters[]` listet die Konverter der Reihe nach: `GD` (die PHP-Erweiterung)
und `ImageMagick` (das Programm `convert`). Die Composer-Metadaten von Exponential sagen, dass GD erforderlich ist,
sofern ImageMagick nicht installiert ist. Prüfen Sie auf einem Host, welcher vorhanden ist, bevor Sie sich auf Filter
verlassen, die nur einer von beiden unterstützt
([Bildverarbeitung und Medien](../hosting/grundlagen/bildverarbeitung-und-medien.md)).

## Nach einer Änderung

Eine Änderung der Filter einer vorhandenen Variante erzeugt die bereits angelegten Dateien nicht neu. Leeren Sie sie
und für eine neue oder geänderte Variante den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=imagealias --allow-root-user
```

Die ersten Aufrufe danach sind langsamer, während die skalierten Dateien neu entstehen; tun Sie es auf einer großen
Website in einer ruhigen Stunde.

## Verwandte Seiten

- [Relationen und Einbettungen](relationen-und-einbettungen.md)
- [Bilder in einem Design](../designer/theme-design/bilder-in-einem-design.md)
- [Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
