---
title: "Ein monatlicher Blick auf die Caches"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1331
summary: exp:cache list zeigt jeden Cache; achten Sie auf Wachstum und stellen Sie sicher, dass das Seiten-Caching noch funktioniert.
keywords: Cache, exp:cache list, Größen, Pflege, Seiten-Cache, cache, sizes, maintenance, page cache
type: help
---
# Ein monatlicher Blick auf die Caches

Die Caches arbeiten und füllen die Festplatte nicht.

*Lebenszyklus: Pflegen, Beispiel 32 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nichts im Layout-Editor.

## Auf der Kommandozeile

1. `./console exp:cache list --sizes`, dann zweimal `curl -s -D - -o /dev/null https://www.example.com/ | grep -i x-exp-cache`.

## Welchen Weg Sie wählen

Monatlich oder nach großen Änderungen.

## Prüfen

- Die zweite Anfrage meldet `HIT`.

## Verwandte Seiten

- [Caches nach einer Änderung](../../caches-nach-einer-aenderung.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
