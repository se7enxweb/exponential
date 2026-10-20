---
title: "Die gemeinsamen Zonen zuerst planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1002
summary: Festlegen, welche Zonen alle Seiten teilen (Kopfzeile, Fußzeile) und welche einige teilen (Prefooter), bevor Seitenlayouts gebaut werden.
keywords: gemeinsames Layout, gemeinsame Zonen, Kopfzeile, Fußzeile, Prefooter, Zonenverknüpfung, Planung, shared layout, shared zones, header, footer, prefooter, zone linking, planning
type: help
---
# Die gemeinsamen Zonen zuerst planen

Ein einziger Ort, um zu ändern, was viele Seiten zeigen.

*Lebenszyklus: Planen, Beispiel 3 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Listen Sie auf, was sich wiederholt: auf Fit & Healthy Kopf- und Fußzeile auf jeder Seite (gemeinsames Layout **Header / Footer**), das Band „Most popular articles“ auf Artikeln und Rezepten (**Prefooter**).
2. Planen Sie für jeden wiederholten Bereich ein gemeinsames Layout und welche Zone welcher Seitenlayouts damit verknüpft wird.

## Mit Templates

1. Wiederholter Rahmen, der sich nie pro Seite ändert, gehört ins Template des Seitenlayouts (`pagelayout.tpl`, `pagelayout/header.tpl`); in diesem Theme wird er über Template-Blöcke in Header / Footer platziert.

## Welchen Weg Sie wählen

Gemeinsame Layouts für das, was Redakteure anordnen; Templates für das, was fest ist.

## Prüfen

- `exp:layouts list` zeigt, dass jedes Seitenlayout `header` und `footer` mit Header / Footer verknüpft.

## Verwandte Seiten

- [Gemeinsame Layouts und Zonenverknüpfung](../../../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
