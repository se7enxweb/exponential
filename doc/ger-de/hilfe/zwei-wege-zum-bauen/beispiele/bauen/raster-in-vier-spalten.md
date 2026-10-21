---
title: "Ein Raster von Artikeln in vier Spalten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1119
summary: Der List-Block in der Ansicht grid mit Number of columns 4 (oder der Grid-Block), Ansichtstyp der Elemente Card.
keywords: Raster, Spalten, Karten, Anzahl der Spalten, Ansichtstyp der Elemente, Grid-Block, grid, columns, cards, number of columns, item view type, Grid block
type: help
---
# Ein Raster von Artikeln in vier Spalten

Acht Artikel als Karten, vier pro Zeile.

*Lebenszyklus: Bauen, Beispiel 20 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **List** (Gruppe *Listing*) in die Zone `main` (oder **Grid**).
3. Füllen Sie die Sammlung (manuell oder dynamisch).
4. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und setzen Sie **Ansichtstyp** `grid`, **Number of columns** 4 und **Ansichtstyp der Elemente** Card.
5. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Media-Design zeichnet das Raster mit `explayouts/block/list/grid.tpl` und `list/grid/4_columns.tpl`.

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/explayouts/block/list/grid/4_columns.tpl` nach `extension/mysite/design/media/templates/explayouts/block/list/grid/4_columns.tpl` und ändern Sie Ihre Kopie; jedes vierspaltige Raster ändert sich ([Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)).

## Welchen Weg Sie wählen

Die Spalten sind eine Design-Option; ändern Sie das Markup aller vierspaltigen Raster mit dem Override.

## Prüfen

- Vier Karten pro Zeile auf einem breiten Bildschirm, weniger auf einem Telefon.

## Verwandte Seiten

- [Der Reiter Design](../../der-reiter-design.md)
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
