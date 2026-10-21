---
title: "Eine Liste der neuesten Artikel eines Bereichs"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1117
summary: Ein List-Block mit einer dynamischen Sammlung (übergeordneter Ort, Sortierung nach Veröffentlichung, Limit) oder fetch() in einem Template.
keywords: neueste Artikel, dynamische Sammlung, List-Block, übergeordneter Ort, Sortierung, latest articles, newest, dynamic collection, List block, parent location, sort, fetch content list
type: help
---
# Eine Liste der neuesten Artikel eines Bereichs

Die fünf neuesten Artikel von Fitness, immer aktuell.

*Lebenszyklus: Bauen, Beispiel 18 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **List** (Gruppe *Listing*) in die Zone `main`.
3. Stellen Sie im Reiter **Inhalt** mit **Ändern** den Sammlungstyp auf **Dynamische Sammlung**, Abfragetyp **Exponential**, dann **Anwenden**.
4. **Parent location**: Fitness; **Sortierart** Veröffentlicht, **Sortierrichtung** Absteigend; unter **Versatz & Anzahl der Elemente** die Zahl 5.
5. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Schritt für Schritt mit Bildschirmfotos: [Manuelle und dynamische Sammlungen](../../manuelle-und-dynamische-sammlungen.md).

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/latest_fitness.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `latest_fitness` ist ([Eigene Template-Blöcke](../../eigene-template-bloecke.md)). Das getestete Template steht auf jener Seite.

## Welchen Weg Sie wählen

Redakteure können die dynamische Liste selbst auf ein neues Ziel richten; das Template legt sie im Code fest.

## Prüfen

- Ein neu veröffentlichter Artikel unter Fitness steht an erster Stelle.

## Verwandte Seiten

- [Manuelle und dynamische Sammlungen](../../manuelle-und-dynamische-sammlungen.md)
- [Eigene Template-Blöcke](../../eigene-template-bloecke.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
