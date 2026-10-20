---
title: "Prüfen, dass Seiten aus dem Seiten-Cache kommen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1214
summary: Der Antwort-Header X-Exp-Cache meldet HIT, MISS oder MISS (purged); prüfen Sie die Hauptseiten nach dem Livegang.
keywords: Seiten-Cache, HTTP-Cache, X-Exp-Cache, HIT, MISS, Livegang, page cache, HTTP cache, launch
type: help
---
# Prüfen, dass Seiten aus dem Seiten-Cache kommen

Besucher bekommen Seiten aus dem Cache.

*Lebenszyklus: Livegang, Beispiel 15 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Nach dem Veröffentlichen eines Layouts meldete die nächste Anfrage auf der Testkopie `MISS (purged)`.

## Auf der Kommandozeile

1. Zweimal `curl -s -D - -o /dev/null https://www.example.com/fitness | grep -i x-exp-cache`: Die erste Anfrage kann `MISS` melden, die zweite sollte `HIT` melden.

## Welchen Weg Sie wählen

Prüfen Sie nach dem Livegang und nach großen Änderungen.

## Prüfen

- `HIT` bei der zweiten Anfrage.

## Verwandte Seiten

- [Caches nach einer Änderung](../../../../../eng-us/help/build-two-ways/caches-after-a-change.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
