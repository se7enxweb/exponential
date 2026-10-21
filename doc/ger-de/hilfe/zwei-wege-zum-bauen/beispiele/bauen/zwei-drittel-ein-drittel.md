---
title: "Eine Aufteilung in zwei Drittel und ein Drittel"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1129
summary: Der Two-columns-Container in der Ansicht two_columns_66_33 (oder 33_66) mit Blöcken in left und right.
keywords: zwei Spalten, 66/33, Seitenleiste, Two columns, Container, Platzhalter left right, two columns, sidebar, container, placeholders left right
type: help
---
# Eine Aufteilung in zwei Drittel und ein Drittel

Der Haupttext links, eine Liste von Links rechts.

*Lebenszyklus: Bauen, Beispiel 30 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Two columns** (Gruppe *Containers*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und setzen Sie **Ansichtstyp** auf `two_columns_66_33` (oder `two_columns_33_66`).
4. Ziehen Sie Blöcke in seine Platzhalter `left` und `right`.
5. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Für eine ganze Seite mit Seitenleiste ist der Layouttyp **Header / Left / Right / Footer** (`layout_4`, verwendet von Full / Recipe) der andere Weg.

## Mit Templates

1. Schreiben Sie die zwei Spalten im Template mit den Rasterklassen des Designs (das Media-Design verwendet Bootstrap-Zeilen: `row`, `col-lg-8`, `col-lg-4`, wie in `explayouts/layout.tpl` für `layout_4`).

## Welchen Weg Sie wählen

Innerhalb eines Seitenabschnitts: Two columns. Die ganze Seite: Layouttyp `layout_4`.

## Prüfen

- Die Spalten stehen auf einem Telefon untereinander.

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
