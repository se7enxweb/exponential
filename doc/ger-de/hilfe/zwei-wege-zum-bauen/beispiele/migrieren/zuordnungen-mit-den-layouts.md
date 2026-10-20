---
title: "Die Zuordnungen mit den Layouts übertragen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1502
summary: --rules=none|selected|all entscheidet, welche Layout-Zuordnungen mitreisen; selected (der Standard) nimmt die der gewählten Layouts.
keywords: Zuordnungen, Regeln, Regeln exportieren, Migration, mappings, rules, export rules, migration
type: help
---
# Die Zuordnungen mit den Layouts übertragen

Die Kampagnenseite bekommt ihre Zuordnung auch auf dem Ziel.

*Lebenszyklus: Migrieren, Beispiel 3 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Prüfen Sie die Zuordnungen nach dem Import unter **Layouts > Layout-Zuordnungen**.

## Auf der Kommandozeile

1. `--rules=selected` (Standard) exportiert die Zuordnungen der gewählten Layouts; `--rules=all` jede Zuordnung; `--rules=none` keine.
2. Zuordnungen verweisen über den Knoten auf Inhalte (Ziele Ort, Teilbaum): Die `references.json` des Archivs ergänzt Remote-IDs und Pfade.

## Welchen Weg Sie wählen

Übertragen Sie Zuordnungen nur, wenn das Ziel dieselben Inhalte hat.

## Prüfen

- Die Layout-Zuordnungen des Ziels listen die importierte Zuordnung.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../../../../eng-us/help/build-two-ways/export-and-import-layouts.md)
- [Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
