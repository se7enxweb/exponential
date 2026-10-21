---
title: "Velocity nach einer Migration"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1530
summary: Auf einer Site, die Exponential Velocity ausliefert, brauchen Änderungen an Einstellungen exp:velocity restart, wie exp:ini sagt.
keywords: Velocity, Neustart, Migration, Einstellungen, restart, migration, settings
type: help
---
# Velocity nach einer Migration

Migrierte Einstellungen wirken auf Velocity.

*Lebenszyklus: Migrieren, Beispiel 31 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Layout-Importe brauchen weder einen Neustart noch ein Leeren des Cache: Ein Import mit `--publish` leert den Seiten-Cache selbst.

## Auf der Kommandozeile

1. Nach den Verschiebungen und Kopien mit `exp:ini`: `./console exp:velocity restart`; dann die Caches.

## Welchen Weg Sie wählen

Starten Sie am Ende einmal neu.

## Prüfen

- Die Velocity-Adresse zeigt die migrierten Menüs.

## Verwandte Seiten

- [Caches nach einer Änderung](../../caches-nach-einer-aenderung.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
