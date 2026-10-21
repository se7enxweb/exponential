---
title: "Neues Markup für jedes Raster"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1400
summary: Das Raster-Template für Listen des Media-Designs in Ihrer Design-Erweiterung überschreiben; jeder List-Block in der Rasteransicht ändert sich.
keywords: Override, Raster, list/grid.tpl, Block-Template, Neugestaltung, Design-Erweiterung, override, grid, block template, redesign, design extension
type: help
---
# Neues Markup für jedes Raster

Karten in Rastern bekommen eine neue Struktur, in jedem Layout auf einmal.

*Lebenszyklus: Neugestaltung, Beispiel 1 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

In den Layouts ändert sich nichts: Jeder List-Block mit der Ansicht `grid` verwendet Ihr Template.

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/explayouts/block/list/grid.tpl` nach `extension/mysite/design/media/templates/explayouts/block/list/grid.tpl`.
2. Ändern Sie das Markup des Rasters; die Spaltenvarianten stehen in `explayouts/block/list/grid/<n>_columns.tpl`.
3. Leeren Sie **Template & Inhalt** und prüfen Sie eine Seite, die es verwendet.

## Welchen Weg Sie wählen

Eine Neugestaltung des Aussehens eines Blocks ist eine Aufgabe für Templates; die Layouts bleiben, wie sie sind.

## Prüfen

- Ein Raster auf der Startseite und eines im Prefooter zeigen beide das neue Markup.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Ihre eigene Design-Erweiterung](../../eigene-design-erweiterung.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
