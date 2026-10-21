---
title: Embed-Ansicht
audience: designer, developer
category: templates
order: 530
summary: Wie in Rich Text eingebettete Objekte gezeichnet werden - das Tag-Template embed, content_view_gui mit der Ansicht embed, die Standards content/view/embed.tpl und node/view/embed.tpl, Ausrichtung und Klassen aus dem Editor und Embed-Templates pro Klasse (Bilder, Videos, Galerien).
keywords: embed, Embed-Ansicht, Einbettung in Rich Text, ezxmltags embed.tpl, content/view/embed.tpl, embed_node, object_parameters, align, classification, eingebettetes Bild, Embed-Override, embed view, rich text embed, embedded image, embed override
type: help
---
# Embed-Ansicht

Diese Seite ist für Designer, die Inhalte gestalten, die Redakteure in Rich Text einbetten: ein Bild in einem Artikel, einen
verwandten Artikel als Kasten, ein Video. Das Tag `embed` des Rich Text zeichnet das Objekt im Ansichtsmodus `embed`.

## Inhalt

1. [Die Kette](#die-kette)
2. [Was das Embed-Template erhält](#was-das-embed-template-erhält)
3. [Einbettungen pro Klasse](#einbettungen-pro-klasse)
4. [Ausrichtung und Klassen](#ausrichtung-und-klassen)

## Die Kette

1. Das Rich-Text-Tag-Template `content/datatype/view/ezxmltags/embed.tpl` umschließt das Objekt mit einem `<div>` mit der
   Ausrichtung und Klasse, die der Redakteur gewählt hat, und ruft
   `{content_view_gui view=$view content_object=$object object_parameters=$object_parameters ...}` auf.
   (`embed_node.tpl` tut dasselbe mit `node_view_gui`, wenn ein Knoten eingebettet ist.)
2. `content/view/embed.tpl` zeichnet das Objekt; der Standard des Kernels gibt seinen Namen aus, verlinkt auf seinen
   Hauptknoten.
3. Overrides auf `Source=content/view/embed.tpl` geben Klassen ihre eigene Einbettung.

## Was das Embed-Template erhält

`$object`, `$object_parameters` (vom Editor: `align`, `id` und eigene Attribute wie `size` für Bilder), `$link_parameters`,
`$classification` (die Klasse, die der Redakteur gewählt hat) und `$view`.

## Einbettungen pro Klasse

```ini
[mytheme_embed_image]
Source=content/view/embed.tpl
MatchFile=embed/image.tpl
Subdir=templates
Match[class_identifier]=image
```

```tpl
{* override/templates/embed/image.tpl *}
<figure class="embed-image">
    {attribute_view_gui attribute=$object.data_map.image image_class=first_set( $object_parameters.size, 'medium' )}
    {if $object.data_map.caption.has_content}
        <figcaption>{attribute_view_gui attribute=$object.data_map.caption}</figcaption>
    {/if}
</figure>
```

Das Design media hat seine Embed-Overrides in `override/templates/embed/`. `content.ini [embed] CustomAttributes[]` listet
zusätzliche Attribute, die Redakteure setzen dürfen (die mitgelieferten Einstellungen fügen `gallery_view_mode` für
eingebettete Galerien hinzu).

## Ausrichtung und Klassen

Die Klassen des Wrappers sind `object-left`, `object-right`, `object-center` aus `align`, dazu die Klasse des Redakteurs.
Gestalten Sie sie in Ihrem Stylesheet (mit logischen Eigenschaften für links und rechts auf Seiten von rechts nach links,
siehe [Logische CSS-Eigenschaften](../theme-design/logische-css-eigenschaften.md)).

## Verwandte Seiten

- [Embed-Inline-Ansicht](embed-inline-ansicht.md)
- [Rich-Text-Templates](rich-text-templates.md)
- [content_view_gui](content-view-gui.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
