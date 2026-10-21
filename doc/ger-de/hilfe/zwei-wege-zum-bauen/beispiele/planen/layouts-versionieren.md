---
title: "Planen, wie Layouts aufbewahrt und versioniert werden"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1014
summary: Layouts liegen in der Datenbank; planen Sie regelmäßige Exporte (exp:layouts export), damit sie verglichen, wiederhergestellt und übertragen werden können.
keywords: Versionsverwaltung, Sicherung, Export, Layouts-Zip, Wiederherstellen, Planung, version control, backup, export, layouts zip, restore, planning
type: help
---
# Planen, wie Layouts aufbewahrt und versioniert werden

Ein Layout lässt sich immer zurücksetzen.

*Lebenszyklus: Planen, Beispiel 15 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Legen Sie fest, wann exportiert wird: vor jeder größeren Änderung und nach einem Zeitplan.

## Auf der Kommandozeile

1. `./console exp:layouts export --all --output=layouts-<date>.zip` schreibt jedes Layout, jedes gemeinsame Layout und jede Zuordnung; bewahren Sie die Dateien beim Projekt auf.

## Welchen Weg Sie wählen

Der Export ist der einzige Weg zurück zu einer veröffentlichten Version; siehe [Ein Layout zurücksetzen](../../layout-zuruecksetzen.md).

## Prüfen

- Es gibt einen Export von vor der aktuellen Version.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
