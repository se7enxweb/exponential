---
title: "Ein Raster mit einem hervorgehobenen Eintrag"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1122
summary: Der List-Block in der Ansicht grid_featured: der erste Eintrag groß, der Rest in einem Raster.
keywords: grid_featured, hervorgehobener Eintrag, Magazin-Raster, List-Block, featured item, magazine grid, List block
type: help
---
# Ein Raster mit einem hervorgehobenen Eintrag

Der Aufmacher groß, vier weitere klein.

*Lebenszyklus: Bauen, Beispiel 23 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **List** (Gruppe *Listing*) in die Zone `main`.
3. Füllen Sie die Sammlung; der erste Eintrag ist der hervorgehobene.
4. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und setzen Sie **Ansichtstyp** auf `grid_featured`.
5. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Media-Design hat `explayouts/block/list/grid_featured.tpl`.

## Mit Templates

1. Überschreiben Sie `explayouts/block/list/grid_featured.tpl`, um zu ändern, welcher Eintrag hervorgehoben wird oder wie.

## Welchen Weg Sie wählen

Ordnen Sie die Einträge, um den hervorgehobenen zu wählen; dafür ist kein Code nötig.

## Prüfen

- Der erste Eintrag der Sammlung ist der große.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../../../../eng-us/help/build-two-ways/block-to-template-map.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
