---
title: "Ein Inhaltsobjekt als Teaser"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1131
summary: Der Block Single content (Node ID, Show name, Show intro).
keywords: einzelner Inhalt, Teaser, Block Single content, Knoten-ID, Einleitung, single content, teaser, Single content block, node ID, intro
type: help
---
# Ein Inhaltsobjekt als Teaser

Die Datenschutzrichtlinie als kurzer Teaser auf der Kontaktseite.

*Lebenszyklus: Bauen, Beispiel 32 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Single content** (Gruppe *Placeholders*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Node ID** (90 für Privacy Policy), **Show name** und **Show intro** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Mit Templates

1. `{fetch( 'content', 'node', hash( 'node_id', 90 ) )}` und seinen Namen und seine Einleitung in einem Template ausgeben.

## Welchen Weg Sie wählen

Der Block für einen Teaser, den ein Redakteur auf ein anderes Ziel richten kann; er zeigt Name, Einleitung und Link des Knotens.

## Prüfen

- Der Teaser verlinkt das Objekt.

## Verwandte Seiten

- [Eine Knoten-ID finden](../../knoten-id-finden.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
