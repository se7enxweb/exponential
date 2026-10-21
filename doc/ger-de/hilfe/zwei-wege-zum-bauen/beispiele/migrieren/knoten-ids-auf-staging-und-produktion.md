---
title: "Unterschiedliche Knoten-IDs auf Staging und Produktion"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1521
summary: Knoten-IDs unterscheiden sich zwischen Installationen; Menüs (MainMenuID) brauchen die IDs des Ziels, Layouts werden über Remote-ID und Pfad zugeordnet.
keywords: Knoten-IDs, Staging, Produktion, Remote-ID, Menü-IDs, Migration, node IDs, production, remote ID, menu IDs, migration
type: help
---
# Unterschiedliche Knoten-IDs auf Staging und Produktion

Menüs und Layouts funktionieren nach dem Umzug in die Produktion.

*Lebenszyklus: Migrieren, Beispiel 22 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Layout-Importe lösen Inhalte über `references.json` auf (Remote-ID und Pfad).

## Mit Einstellungen

1. Schreiben Sie die Knoten-IDs der Produktion in die `menu.ini` der Produktion (keine Kopie der Liste von Staging).

## Welchen Weg Sie wählen

Kopieren Sie ID-Listen niemals blind zwischen Installationen.

## Prüfen

- Jeder Menüeintrag öffnet auf der Produktion die richtige Seite.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Das Kopfmenü über die Kommandozeile](../../kopfmenue-per-kommandozeile.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
