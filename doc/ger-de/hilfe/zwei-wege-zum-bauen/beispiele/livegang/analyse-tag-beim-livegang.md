---
title: "Beim Livegang ein Analyse-Tag hinzufügen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1227
summary: Das Theme hat ein Include für Google Tag Manager (parts/google_tag_manager_code_script.tpl und _noscript.tpl); Messcode gehört in Templates, nicht in HTML-Snippet-Blöcke.
keywords: Webanalyse, Tag Manager, Tracking, Template, Livegang, analytics, tag manager, tracking, template, launch
type: help
---
# Beim Livegang ein Analyse-Tag hinzufügen

Die Messung läuft vom Livegang an auf jeder Seite.

*Lebenszyklus: Livegang, Beispiel 28 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Ein HTML-Snippet-Block würde nur die Layouts abdecken, die ihn enthalten.

## Mit Templates

1. Das Seitenlayout des Media-Designs bindet `design:parts/google_tag_manager_code_script.tpl` und `parts/google_tag_manager_code_noscript.tpl` ein; überschreiben Sie sie in Ihrer Erweiterung, um den Code der Site einzufügen.

## Welchen Weg Sie wählen

Templates, damit jede Seite ihn genau einmal hat.

## Prüfen

- Das Tag erscheint einmal pro Seite.

## Verwandte Seiten

- [Ihre eigene Design-Erweiterung](../../eigene-design-erweiterung.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
