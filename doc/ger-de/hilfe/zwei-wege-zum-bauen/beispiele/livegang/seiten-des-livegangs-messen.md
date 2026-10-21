---
title: "Die Seiten des Livegangs messen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1228
summary: Messen Sie die Hauptseiten mit und ohne Cache, wie auf der Testkopie, und bewahren Sie die Zahlen auf.
keywords: Leistung, Livegang, Zeitmessung, curl, Seiten-Cache, performance, launch, timing, page cache
type: help
---
# Die Seiten des Livegangs messen

Eine Ausgangsbasis für den Vergleich nach späteren Änderungen.

*Lebenszyklus: Livegang, Beispiel 29 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Vergleichen Sie Layouts mit vielen Blöcken (die Startseite) und mit wenigen.

## Auf der Kommandozeile

1. Mehrmals `curl -s -o /dev/null -w '%{time_total}\n' https://www.example.com/`; die erste Anfrage nach dem Leeren des Cache kompiliert Templates.

## Welchen Weg Sie wählen

Tragen Sie die Zahlen in die Dokumentation der Site ein.

## Prüfen

- Seiten aus dem Cache antworten in Millisekunden.

## Verwandte Seiten

- [Gemessene Schritte und Zeiten](../../gemessene-schritte-und-zeiten.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
