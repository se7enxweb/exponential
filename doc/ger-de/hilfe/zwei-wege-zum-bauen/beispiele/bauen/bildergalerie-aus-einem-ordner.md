---
title: "Eine Bildergalerie aus einem Ordner"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1123
summary: Der Gallery-Block (Parent node ID, Limit, Class filter, Image attribute identifier, Thumbnail size alias) oder ein Galerie-Inhaltsobjekt.
keywords: Galerie, Bilder, Gallery-Block, übergeordneter Knoten, Vorschaugröße, Ordner, gallery, images, Gallery block, parent node, thumbnail size, folder
type: help
---
# Eine Bildergalerie aus einem Ordner

Zwölf Fotos aus einem Ordner der Medien-Bibliothek.

*Lebenszyklus: Bauen, Beispiel 24 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Gallery** (Gruppe *Gallery*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Parent node ID**, **Limit** (Standard 12), **Class filter**, **Image attribute identifier** (Standard `image`) und **Thumbnail size alias** (Standard `small`) aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Media-Design verteilt Galerien auf `explayouts/block/gallery/` (`slider`, `grid_gallery`, `thumb_gallery`, `sushi_bar`).

## Mit Templates

1. Die Klasse *NG Gallery* der Site (zum Beispiel „10 Workout Terms You Need To Know“ unter Fitness) ist eine Galerie als Inhalt, gezeigt durch ihre Full View.

## Welchen Weg Sie wählen

Fotos für eine Seite: der Block. Eine Galerie, die eine eigene Geschichte ist: ein NG-Gallery-Objekt.

## Prüfen

- Ein neues Bild im Ordner erscheint nach dem Veröffentlichen.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
