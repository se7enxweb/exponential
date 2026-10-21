---
title: "Layouts im Projekt-Repository aufbewahren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1506
summary: Exporte neben der Design-Erweiterung committen; die layouts.json im Archiv lässt sich zwischen Versionen vergleichen.
keywords: git, Versionsverwaltung, layouts.json, Export, Vergleich, version control, export, diff
type: help
---
# Layouts im Projekt-Repository aufbewahren

Sehen, was sich in einem Layout zwischen zwei Releases geändert hat.

*Lebenszyklus: Migrieren, Beispiel 7 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Im Repository](#im-repository)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Redakteure arbeiten weiter im Editor; der Export hält ihre Arbeit fest.

## Im Repository

1. Exportieren Sie nach jedem Release; entpacken Sie `layouts.json` und committen Sie sie, oder committen Sie die `.zip`.
2. Vergleichen Sie zwei Versionen von `layouts.json` mit einem Diff-Werkzeug, das JSON versteht.

## Welchen Weg Sie wählen

Templates und Layouts beide versioniert; eine einzige Historie für die Site.

## Prüfen

- Das Repository hat einen Export pro Release.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
