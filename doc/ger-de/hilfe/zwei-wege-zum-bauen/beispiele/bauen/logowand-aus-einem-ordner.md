---
title: "Eine Wand mit Partnerlogos aus einem Ordner"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1103
summary: Der Logos-Block liest Bilder aus einem übergeordneten Knoten (Parent node ID, Limit, Class filter, Image attribute identifier, Thumbnail size alias); oder ein Fetch in einem Template.
keywords: Logos, Partner, Kunden, Logos-Block, übergeordneter Knoten, Bildattribut, Vorschaugröße, logos, partners, clients, Logos block, parent node, image attribute, thumbnail size
type: help
---
# Eine Wand mit Partnerlogos aus einem Ordner

Die Logos von Partnern zeigen, die als Bilder in einem Ordner liegen.

*Lebenszyklus: Bauen, Beispiel 4 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Logos** (Gruppe *Components*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Parent node ID** (der Ordner mit den Logos), **Limit** (Standard 12), **Class filter** (zum Beispiel `image`), **Image attribute identifier** (Standard `image`) und **Thumbnail size alias** (Standard `small`) aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Die Medien-Bibliothek der Site hat für solche Bilder einen Ordner **Clients & Partners**.

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/partner_logos.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `partner_logos` ist ([Eigene Template-Blöcke](../../eigene-template-bloecke.md)).

```smarty
{def $logos = fetch( 'content', 'list', hash( 'parent_node_id', <folder node id>, 'class_filter_type', 'include', 'class_filter_array', array( 'image' ), 'limit', 12 ) )}
<ul class="container partner-logos">
{foreach $logos as $logo}
    <li>{attribute_view_gui attribute=$logo.data_map.image image_class=small}</li>
{/foreach}
</ul>
{undef $logos}
```

## Welchen Weg Sie wählen

Beide lesen den Ordner, Redakteure fügen also ein Logo hinzu, indem sie dort ein Bild hochladen. Der Block braucht keinen Code.

## Prüfen

- Ein neues Bild im Ordner erscheint, nachdem es veröffentlicht wurde.

## Verwandte Seiten

- [Der Reiter Design](../../der-reiter-design.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
