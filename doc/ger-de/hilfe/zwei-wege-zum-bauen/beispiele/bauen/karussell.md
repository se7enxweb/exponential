---
title: "Ein Karussell von Inhaltsobjekten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1128
summary: Der Carousel-Block (Slides per view, Autoplay in ms, Image attribute identifier und die Listenoptionen) mit einer Sammlung.
keywords: Karussell, Carousel-Block, Folien pro Ansicht, automatisches Abspielen, Sammlung, carousel, Carousel block, slides per view, autoplay, collection
type: help
---
# Ein Karussell von Inhaltsobjekten

Artikel, die vorbeigleiten, drei auf einmal.

*Lebenszyklus: Bauen, Beispiel 29 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Carousel** (Gruppe *Gallery*) in die Zone `main`.
3. Füllen Sie seine Sammlung (manuell oder dynamisch).
4. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Slides per view** (Standard 1), **Autoplay (ms, 0 to disable)** und **Image attribute identifier** (Standard `image`) aus.
5. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Mit Templates

1. Kopieren Sie `extension/explayouts/design/standard/templates/explayouts/block/carousel.tpl` nach `extension/mysite/design/media/templates/explayouts/block/carousel.tpl` und ändern Sie Ihre Kopie; jeder Block dieses Typs ändert sich in jedem Layout ([Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)).

## Welchen Weg Sie wählen

Lassen Sie Autoplay auf 0, solange es keine Pausensteuerung gibt.

## Prüfen

- Mit Slides per view 3 sind drei Einträge sichtbar.

## Verwandte Seiten

- [Manuelle und dynamische Sammlungen](../../manuelle-und-dynamische-sammlungen.md)
- [Der Reiter Design](../../der-reiter-design.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
