---
title: "Ein Bild mit einem Link"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1110
summary: Der Image-Block (Image URL, Alt text, Link URL) oder ein Bildattribut, das ein Template ausgibt.
keywords: Bild, Foto, Image-Block, Alternativtext, Link, Bannerbild, image, picture, Image block, alt text, link, banner image
type: help
---
# Ein Bild mit einem Link

Ein Bild, das auf eine Seite verlinkt.

*Lebenszyklus: Bauen, Beispiel 11 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Image** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Image URL**, **Alt text** und **Link URL** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Image URL nimmt eine Adresse; ein Bild aus der Medien-Bibliothek wird über seine gespeicherte Datei angesprochen (siehe den Durchgang mit dem Hero, der einen Pfad `/var/<site>/storage/images/...` verwendete).

## Mit Templates

1. Geben Sie das Bildfeld eines Inhaltsobjekts mit einem Bild-Alias aus, damit die Größe für Sie erzeugt wird:

```smarty
{def $article = fetch( 'content', 'node', hash( 'node_id', 93 ) )}
<a href={$article.url_alias|ezurl}>{attribute_view_gui attribute=$article.data_map.image image_class=i480}</a>
{undef $article}
```

## Welchen Weg Sie wählen

Der Weg über Templates folgt dem Inhalt (ein neues Bild am Artikel erscheint überall); der Block hält eine feste Adresse.

## Prüfen

- Das Bild hat einen Alternativtext.

## Verwandte Seiten

- [Der Reiter Design](../../der-reiter-design.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
