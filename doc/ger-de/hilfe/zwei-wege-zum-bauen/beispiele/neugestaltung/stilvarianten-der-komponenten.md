---
title: "Die Stilvarianten der Komponenten verwenden"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1428
summary: Die Blöcke exp_component_* bringen Stil-Ansichtstypen mit (hero_style_1 bis 3, features_style_1 bis 7, about_style_1 bis 5, logos_style_1 und 2, lead_style_1 und 2, quote_style_1).
keywords: Komponenten, Stilvarianten, exp_component, Ansichtstypen, Neugestaltung, components, style variants, view types, redesign
type: help
---
# Die Stilvarianten der Komponenten verwenden

Unter fertigen Varianten wählen, statt neue zu bauen.

*Lebenszyklus: Neugestaltung, Beispiel 29 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Die mit dem Theme importierten Komponentenblöcke (`exp_component_hero` ...) bieten ihre Stilvarianten als Ansichtstypen an; das Media-Design stellt sie mit `explayouts/block/exp_component_<name>.tpl` dar.

## Mit Templates

1. Überschreiben Sie `exp_component_<name>.tpl` in Ihrer Erweiterung für das Aussehen einer neuen Variante.

## Welchen Weg Sie wählen

Probieren Sie die Varianten aus, bevor Sie ein Template schreiben.

## Prüfen

- Der Wrapper zeigt `ngl-vt-<style>`.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../../../../eng-us/help/build-two-ways/block-to-template-map.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
