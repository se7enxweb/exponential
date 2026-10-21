---
title: "Ein Bilder-Slider"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1124
summary: Der Slider-Block mit denselben Feldern für die Bildquelle wie die Gallery.
keywords: Slider, Diashow, Slider-Block, Bilder, slider, slideshow, Slider block, images
type: help
---
# Ein Bilder-Slider

Eine Diashow oben auf einer Seite.

*Lebenszyklus: Bauen, Beispiel 25 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Slider** (Gruppe *Gallery*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie die Quelle aus: **Parent node ID**, **Limit**, **Class filter**, **Image attribute identifier**, **Thumbnail size alias**.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Mit Templates

1. Überschreiben Sie `explayouts/block/gallery/slider.tpl` des Media-Designs in Ihrer Erweiterung, um das Markup der Folien zu ändern.

## Welchen Weg Sie wählen

Berücksichtigen Sie `prefers-reduced-motion` in den Styles, wenn sich der Slider von selbst bewegt.

## Prüfen

- Die Folien lassen sich mit der Tastatur bewegen.

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
