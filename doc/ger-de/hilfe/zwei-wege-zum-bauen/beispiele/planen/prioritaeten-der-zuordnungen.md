---
title: "Die Prioritäten der Zuordnungen planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1010
summary: Die erste aktivierte Zuordnung nach Priorität, deren Ziele und Bedingungen passen, gewinnt; planen Sie die Prioritäten so, dass spezifische Zuordnungen zuerst kommen.
keywords: Priorität der Zuordnung, Reihenfolge der Regeln, Layout-Zuordnungen, spezifisch vor allgemein, mapping priority, rules order, layout mappings, specific before general
type: help
---
# Die Prioritäten der Zuordnungen planen

Keine Seite bekommt das falsche Layout, weil eine allgemeine Zuordnung zuerst kommt.

*Lebenszyklus: Planen, Beispiel 11 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Ohne Zuordnungen](#ohne-zuordnungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Fit & Healthy: Startseite (Ort 89) 210, Rezepte 160, Artikel 130, Themen 120, Default Layout (Pfadpräfix `/`) 10.
2. Geben Sie einzelnen Seiten (Ort, Pfad) hohe Zahlen, Klassenregeln mittlere Zahlen und Auffang-Zuordnungen niedrige Zahlen.

## Ohne Zuordnungen

1. Ohne jede Zuordnung verwendet die Seite `[ResolverSettings] DefaultLayout` der `explayouts.ini` (auf dieser Site der Bezeichner von Default Layout) oder gar kein Layout.

## Welchen Weg Sie wählen

Lassen Sie Lücken zwischen den Zahlen (10, 20 ...), damit neue Zuordnungen hineinpassen.

## Prüfen

- Für drei Beispielseiten führt der Weg durch die Liste nach Priorität zum erwarteten Layout.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
