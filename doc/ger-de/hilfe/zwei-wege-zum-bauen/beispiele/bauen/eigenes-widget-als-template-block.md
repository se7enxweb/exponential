---
title: "Ein eigenes Widget, das nur ein Entwickler bauen kann"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1133
summary: Alles, was die Blocktypen nicht können (ein Rechner, ein Datenfeed), ist ein Template; mit dem Template-Block können Redakteure es platzieren.
keywords: eigenes Widget, Template-Block, Entwickler, Redakteur, Brücke, custom widget, template block, developer, editor, bridge, tpl_block
type: help
---
# Ein eigenes Widget, das nur ein Entwickler bauen kann

Eine Tabelle „Kalorien pro Training“, aus Inhalten berechnet und von Redakteuren dort platziert, wo sie sie haben wollen.

*Lebenszyklus: Bauen, Beispiel 34 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Mit Templates](#mit-templates)
2. [Dann im Layout-Editor](#dann-im-layout-editor)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Mit Templates

1. Schreiben Sie `extension/mysite/design/media/templates/explayouts/tpl_block/calories_table.tpl` in Ihrer Design-Erweiterung, leeren Sie den Template-Cache und platzieren Sie es mit einem **Template block**, dessen **Template name** `calories_table` ist ([Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)).
2. Holen Sie die Daten und rechnen Sie im Template, oder fügen Sie für echte Logik einen Template-Operator in Ihrer Erweiterung hinzu.

## Dann im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Template block** (Gruppe *Placeholders*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und tragen Sie als **Template name** `calories_table` ein.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Welchen Weg Sie wählen

Hier treffen sich die beiden Wege: der Code vom Entwickler, die Platzierung vom Redakteur.

## Prüfen

- Der Block lautet im Editor „Template block: **calories_table**“, und die Site zeigt die Tabelle.

## Verwandte Seiten

- [Eigene Template-Blöcke](../../../../../eng-us/help/build-two-ways/template-blocks.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
