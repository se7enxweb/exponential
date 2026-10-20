---
title: "Das Messen der Arbeit planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1029
summary: Die Schritte zählen und die Wartezeiten typischer Änderungen messen, wie auf diesen Seiten, um die beiden Wege auf Ihrer eigenen Site zu vergleichen.
keywords: messen, Schritte, Zeitmessung, Vergleich, Methode, measure, steps, timing, comparison, method
type: help
---
# Das Messen der Arbeit planen

Entscheidungen zwischen den beiden Wegen auf Grundlage Ihrer eigenen Zahlen.

*Lebenszyklus: Planen, Beispiel 30 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Zählen Sie die Aktionen in der Administration: Klicks, Auswahlen, Eingaben.

## Auf der Kommandozeile

1. Messen Sie Befehle mit der Uhr der Shell (`date +%s%N` davor und danach) und Seiten mit `curl -w '%{time_total}'`; verwenden Sie auf Live-Installationen `--dry-run`.

## Welchen Weg Sie wählen

Wiederholen Sie jede Messung dreimal, wie in [Gemessene Schritte und Zeiten](../../../../../eng-us/help/build-two-ways/measured-steps-and-times.md).

## Prüfen

- Für jede gemessene Aufgabe sind Schritte und Zeiten notiert.

## Verwandte Seiten

- [Gemessene Schritte und Zeiten](../../../../../eng-us/help/build-two-ways/measured-steps-and-times.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
