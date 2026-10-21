---
title: "Ein saisonales Layout nach Datum wechseln"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1313
summary: Eine Zeit-Bedingung (from, to) an der saisonalen Zuordnung schaltet sie zu Beginn und Ende um, ohne dass jemand klickt.
keywords: Zeitbedingung, saisonal, Zeitplan, Kampagnendaten, Zuordnung, time condition, seasonal, schedule, campaign dates, mapping
type: help
---
# Ein saisonales Layout nach Datum wechseln

Das Black-Friday-Layout erscheint von Freitag 00:00 bis Montag 23:59.

*Lebenszyklus: Pflegen, Beispiel 14 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Fügen Sie an der saisonalen Zuordnung mit **Bedingung hinzufügen** eine Bedingung vom Typ **Zeit** mit Beginn und Ende hinzu.
2. Lassen Sie ihre Priorität über der normalen Zuordnung.

Seit explayouts 1.4.42 merkt sich der Resolver diese Antwort nur bis zum nächsten Beginn oder Ende der Bedingung, und solange dieser Zeitpunkt innerhalb von `[HttpCacheSettings] MaxAge` des Seiten-Cache (3600 Sekunden) liegt, bleibt die Seite aus den Seiten-Caches (`Cache-Control: private, no-store`); das Layout wechselt also pünktlich.

## Mit Templates

1. Eine Template-Bedingung auf `currentdate()` plus ein Leeren des Cache an der Grenze.

## Welchen Weg Sie wählen

Die Zeit-Bedingung für geplante Kampagnen: Sie schaltet zu Beginn und Ende um, ohne Verzögerung.

## Prüfen

- Nach dem Enddatum ist das normale Layout zurück.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../layout-zuordnungen.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
