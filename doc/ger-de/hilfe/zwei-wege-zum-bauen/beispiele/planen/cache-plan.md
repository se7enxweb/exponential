---
title: "Planen, welchen Cache jede Änderung braucht"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1013
summary: Pro Art von Änderung aufschreiben, welcher Cache zu leeren ist, damit Livegänge und Updates nicht mit „es erscheint nicht“ enden.
keywords: Caches, Cache-Plan, Template-Cache, Inhalts-Cache, Seiten-Cache, Velocity, caches, cache plan, template cache, content cache, page cache
type: help
---
# Planen, welchen Cache jede Änderung braucht

Keine Änderung wird für kaputt erklärt, wenn sie nur im Cache steckt.

*Lebenszyklus: Planen, Beispiel 14 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Welcher Cache](#welcher-cache)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Das Veröffentlichen im Layout-Editor und das Veröffentlichen von Inhalten haben die Seiten auf der Testkopie von selbst aktualisiert.

## Welcher Cache

1. In Templates gelesene Einstellungen (Menüs): **Template & Inhalt**. Neue Templates: **Template & Inhalt**. Stylesheets: **Template & Inhalt** (gepackte Dateien). Layout-Importe auf der Kommandozeile: keiner (ein Import mit `--publish` leert den Seiten-Cache selbst).

## Welchen Weg Sie wählen

Halten Sie [Caches nach einer Änderung](../../caches-nach-einer-aenderung.md) beim Plan bereit.

## Prüfen

- Das Team kennt den Kasten **Cache leeren** und `./console exp:cache clear --tag=template,content`.

## Verwandte Seiten

- [Caches nach einer Änderung](../../caches-nach-einer-aenderung.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
