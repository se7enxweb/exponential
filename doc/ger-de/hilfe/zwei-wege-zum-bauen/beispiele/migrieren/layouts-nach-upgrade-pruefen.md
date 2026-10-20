---
title: "Layouts nach einem Upgrade prüfen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1518
summary: Nach dem Upgrade von Exponential exportieren, auflisten, die wichtigsten Layouts im Editor öffnen und die Seiten vergleichen.
keywords: Upgrade, Prüfung, Layouts, Export, vergleichen, upgrade, check, layouts, export, compare
type: help
---
# Layouts nach einem Upgrade prüfen

Das Upgrade hat nicht verändert, wie Seiten dargestellt werden.

*Lebenszyklus: Migrieren, Beispiel 19 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie Header / Footer und das Layout der Startseite im Editor; prüfen Sie die Vorschau.

## Auf der Kommandozeile

1. Vorher: `exp:layouts export --all`; nachher: dasselbe; vergleichen Sie `layouts.json`.
2. Leeren Sie nach dem Upgrade **Template & Inhalt**.

## Welchen Weg Sie wählen

Vergleichen Sie Exporte; öffnen Sie den Editor für die Sichtprüfung.

## Prüfen

- Die Seitenmarkierungen (Zonen, Anzahl der Blöcke) sind wie vorher.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../../../../eng-us/help/build-two-ways/export-and-import-layouts.md)
- [Caches nach einer Änderung](../../../../../eng-us/help/build-two-ways/caches-after-a-change.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
