---
title: "Barrierefreiheit in die Blöcke einplanen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1019
summary: Die Felder nutzen, die die Blöcke dafür haben (Image alt text, Heading level, Linktexte), und ein einziges h1 pro Seite behalten.
keywords: Barrierefreiheit, Alternativtext, Überschriftenebene, h1, Linktext, WCAG, Planung, accessibility, alt text, heading level, link text, planning
type: help
---
# Barrierefreiheit in die Blöcke einplanen

Im Editor gebaute Seiten sind so barrierefrei wie von Hand geschriebene Templates.

*Lebenszyklus: Planen, Beispiel 20 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Image und Hero haben **Alt text** / **Image alt text**; Title hat **Heading level (1-6)**; Button und Hero haben eigene Felder für den Linktext.
2. Das Layout-Template des Media-Themes schreibt bereits ein verstecktes `h1` mit dem Namen der Seite, Abschnittstitel sollten also Ebene 2 und darunter haben.

## Mit Templates

1. In Templates: `alt` aus dem Alternativtext des Bilds, Überschriften in der richtigen Reihenfolge, `|wash` auf jedem ausgegebenen Wert.

## Welchen Weg Sie wählen

Schreiben Sie die Regeln in den Leitfaden für Redakteure: Sie gelten für jeden Block.

## Prüfen

- Ein Prüfwerkzeug für Barrierefreiheit findet ein einziges `h1` und kein Bild ohne Alternativtext.

## Verwandte Seiten

- [Barrierefreiheit für Site-Entwickler](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
