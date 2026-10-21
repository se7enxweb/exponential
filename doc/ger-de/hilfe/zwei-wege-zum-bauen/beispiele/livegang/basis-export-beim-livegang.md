---
title: "Beim Livegang einen Basis-Export machen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1212
summary: Am Tag des Livegangs jedes Layout, jedes gemeinsame Layout und jede Zuordnung exportieren, um später zu vergleichen und wiederherzustellen.
keywords: Ausgangsbasis, Export, Livegang, Sicherung, baseline, export, launch, backup, exp:layouts export --all
type: help
---
# Beim Livegang einen Basis-Export machen

Ein bekannt guter Stand aller Layouts in einer Datei.

*Lebenszyklus: Livegang, Beispiel 13 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Oder **Layouts > Exportieren** in der Administration.

## Auf der Kommandozeile

1. `./console exp:layouts export --all --output=layouts-launch.zip`.

## Welchen Weg Sie wählen

Die Konsole für die Automatisierung; die Administration für einen einmaligen Export.

## Prüfen

- Die Zahlen in der `manifest.json` der Datei stimmen mit `exp:layouts list` überein.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
