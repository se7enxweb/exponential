---
title: "Abwechselnde Zeilen aus Bild und Text"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1120
summary: Der Block Zig-Zag (List) oder der List-Block in der Ansicht list_zigzag.
keywords: Zickzack, abwechselnd, Bild links rechts, list_zigzag, Zig-Zag (List), zigzag, alternating, image left right
type: help
---
# Abwechselnde Zeilen aus Bild und Text

Drei Geschichten mit dem Bild abwechselnd links und rechts.

*Lebenszyklus: Bauen, Beispiel 21 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Zig-Zag (List)** (Gruppe *Listing*) in die Zone `main`.
3. Füllen Sie seine Sammlung.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Media-Design stellt `list_zigzag` mit `explayouts/block/list/list_zigzag.tpl` dar; der Wrapper erhält die Klasse `list-zigzag`.

## Mit Templates

1. Überschreiben Sie `explayouts/block/list/list_zigzag.tpl` in Ihrer Design-Erweiterung, um die Zeilen jeder Zickzack-Liste zu ändern.

## Welchen Weg Sie wählen

Die Ansicht ist fertig vorhanden; überschreiben Sie nur, um ihr Markup zu ändern.

## Prüfen

- Die Seite des Bilds wechselt von Zeile zu Zeile.

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
