---
title: "Eine dynamische Liste auf einen anderen Bereich richten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1302
summary: Die Parent location einer dynamischen Sammlung ändern; die Liste füllt sich sofort aus dem neuen Bereich.
keywords: dynamische Sammlung, übergeordneter Ort, Quelle ändern, Bereich, Pflege, dynamic collection, parent location, change source, section, maintenance
type: help
---
# Eine dynamische Liste auf einen anderen Bereich richten

Die Liste „Latest“ soll Healthy eating statt Fitness zeigen.

*Lebenszyklus: Pflegen, Beispiel 3 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Wählen Sie die Liste aus; ändern Sie im Reiter **Inhalt** die **Parent location** mit ihrer Ordner-Schaltfläche (Content Browser).
2. Prüfen Sie die Vorschau in der Mitte und klicken Sie auf **Layout veröffentlichen**.

## Mit Templates

1. Ändern Sie die `parent_node_id` des `fetch( 'content', 'list', ... )` im Template und spielen Sie die Änderung ein.

## Welchen Weg Sie wählen

Mit dynamischen Sammlungen können Redakteure Listen ohne Entwickler auf ein neues Ziel richten.

## Prüfen

- Die Liste zeigt Artikel des neuen Bereichs.

## Verwandte Seiten

- [Manuelle und dynamische Sammlungen](../../manuelle-und-dynamische-sammlungen.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
