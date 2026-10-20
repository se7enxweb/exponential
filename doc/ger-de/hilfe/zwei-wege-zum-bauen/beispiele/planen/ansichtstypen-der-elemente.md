---
title: "Planen, wie Einträge in Listen aussehen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1007
summary: Ansichtstypen der Elemente (Standard, Overlay, Card, Line, Mini, List item, mit oder ohne Einleitung) bestimmen das Aussehen jedes Eintrags; planen Sie sie pro Liste.
keywords: Ansichtstyp der Elemente, Karte, Zeile, Overlay, Mini, Listeneintrag, Inhaltsansichten, item view type, card, line, overlay, mini, list item, content views
type: help
---
# Planen, wie Einträge in Listen aussehen

Derselbe Artikel sieht als Karte auf der Startseite und als Zeile in der Seitenleiste richtig aus.

*Lebenszyklus: Planen, Beispiel 8 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Im Reiter **Design** einer Liste bietet **Ansichtstyp der Elemente** Standard, Standard (with intro), Overlay, Card, Card (with intro), Line, Mini, List item und List item (with intro).

## Mit Templates

1. Jede Ansicht der Einträge ist ein Template unter `content/views/` des Media-Designs (`standard`, `overlay`, `card`, `line`, `mini`, `listitem` ...); ein neues Aussehen pro Klasse ist `content/views/<view>/<class>.tpl` in Ihrer Erweiterung.

## Welchen Weg Sie wählen

Wählen Sie zuerst aus den vorhandenen Ansichten; fügen Sie nur dann eine Ansicht über Templates hinzu, wenn keine passt.

## Prüfen

- Jede Liste im Plan hat ihren Ansichtstyp der Elemente.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../../../../eng-us/help/build-two-ways/block-to-template-map.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
