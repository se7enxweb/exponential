---
title: "Neben den vorhandenen Layouts importieren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1504
summary: --strategy=copy importiert bereits vorhandene Einträge als neue Kopien und lässt die Originale stehen.
keywords: als Kopie importieren, Strategie copy, nebeneinander, Migration, import copy, strategy copy, side by side, migration
type: help
---
# Neben den vorhandenen Layouts importieren

Ein eingehendes Layout vor dem Umschalten mit dem aktuellen vergleichen.

*Lebenszyklus: Migrieren, Beispiel 5 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie die Kopie im Editor, vergleichen Sie und schalten Sie dann die Zuordnung mit **Anderes Layout verknüpfen** oder einer neuen Zuordnung auf sie um.

## Auf der Kommandozeile

1. `./console exp:layouts import incoming.zip --strategy=copy` (ohne `--publish` sind die Kopien Entwürfe).

## Welchen Weg Sie wählen

Kopieren, wenn Sie unsicher sind; überschreiben, wenn der Import die Wahrheit ist.

## Prüfen

- Beide Layouts sind vorhanden.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
