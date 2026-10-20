---
title: "Layouts aus zwei Installationen zusammenführen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1524
summary: Die Layouts der zweiten Site mit --strategy=copy importieren, wo Bezeichner kollidieren, dann umbenennen und neu zuordnen.
keywords: Sites zusammenführen, als Kopie importieren, Bezeichner, Kollision, Migration, merge sites, import copy, identifiers, clash, migration
type: help
---
# Layouts aus zwei Installationen zusammenführen

Zwei Sites auf einer Installation.

*Lebenszyklus: Migrieren, Beispiel 25 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Benennen Sie die Kopien um, verknüpfen Sie ihre Zonen mit den richtigen gemeinsamen Layouts und fügen Sie für die neue Site Zuordnungen mit Teilbaum-Zielen hinzu.

## Auf der Kommandozeile

1. Probelauf: Einträge mit `conflicting` oder `changed` teilen sich Bezeichner; importieren Sie diese mit `--item=<key>=copy`.

## Welchen Weg Sie wählen

Bei Kollisionen kopieren; niemals die Layouts einer anderen Site überschreiben.

## Prüfen

- Beide Sites stellen ihre eigenen Layouts dar.

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
