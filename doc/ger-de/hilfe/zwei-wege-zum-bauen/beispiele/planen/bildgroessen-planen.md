---
title: "Bildgrößen planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1020
summary: Galerien nehmen einen Thumbnail size alias, Templates eine Bildklasse; planen Sie, welche Größen das Design braucht.
keywords: Bildgrößen, Bild-Alias, Vorschaugröße, image_class, i480, small, image sizes, image alias, thumbnail size
type: help
---
# Bildgrößen planen

Bilder werden in den Größen erzeugt, die das Design zeigt, nicht im Browser skaliert.

*Lebenszyklus: Planen, Beispiel 21 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Die Galerie-Blöcke (Gallery, Slider, Thumb gallery, Grid gallery, Sushi bar, Logos) haben **Thumbnail size alias** (Standard `small`).

## Mit Templates

1. Templates wählen den Alias mit `{attribute_view_gui attribute=$node.data_map.image image_class=i480}`; das Media-Theme verwendet Aliase wie `i480` (seine Bilddateien enden auf `_i480.jpg`).
2. Neue Aliase werden in der `image.ini` Ihrer Erweiterung definiert.

## Welchen Weg Sie wählen

Verwenden Sie die Aliase des Themes wieder; fügen Sie nur für eine neue Größe einen hinzu.

## Prüfen

- Die Bilddateien der Seite haben die geplante Größe.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
