---
title: "Eine Layouts-Site in Templates einfrieren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1514
summary: Wenn sich eine Site nicht mehr ändern soll (ein Archiv), können ihre Layouts bleiben; Einfrieren heißt weniger Redakteure, nicht neu schreiben.
keywords: Archiv, einfrieren, Layouts, Templates, Berechtigungen, Migration, archive, freeze, permissions, migration
type: help
---
# Eine Layouts-Site in Templates einfrieren

Eine alte Kampagnen-Site bleibt unverändert online.

*Lebenszyklus: Migrieren, Beispiel 15 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Exportieren Sie alle Layouts als Nachweis; nehmen Sie im Rolleneditor (`/role/list` in der Administration) die Richtlinie `explayouts_ui_api/edit` (Bearbeiten im Layout-Editor) aus den Rollen der Redakteure; mit `explayouts_ui_api/read` können sie weiterhin hineinsehen.

## Mit Templates

1. Layouts als Templates neu zu schreiben, lohnt sich selten; der Seiten-Cache liefert unveränderte Seiten schnell aus.

## Welchen Weg Sie wählen

Einfrieren über Richtlinien und Exporte.

## Prüfen

- Das Archiv wird wie vorher dargestellt.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../../../../eng-us/help/build-two-ways/export-and-import-layouts.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
