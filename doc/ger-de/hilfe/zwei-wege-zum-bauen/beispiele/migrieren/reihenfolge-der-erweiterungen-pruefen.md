---
title: "Die Reihenfolge der Erweiterungen nach dem Verschieben von Einstellungen prüfen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1520
summary: Nach dem Verschieben von Einstellungen oder dem Hinzufügen von Erweiterungen DesignExtensions[] und AvailableBlocks[] in ihrer Wirkung prüfen.
keywords: Reihenfolge der Erweiterungen, ActiveExtensions, DesignExtensions, AvailableBlocks, Migration, extension order, migration
type: help
---
# Die Reihenfolge der Erweiterungen nach dem Verschieben von Einstellungen prüfen

Ihr Design und Ihre Blocktypen haben weiterhin Vorrang.

*Lebenszyklus: Migrieren, Beispiel 21 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Die Palette listet weiterhin Ihre Blocktypen.

## Mit Templates

1. `./console exp:ini get design.ini/ExtensionSettings/DesignExtensions -s site` (Ihre nach dem Theme) und `exp:ini where explayouts.ini/BlockSettings/AvailableBlocks admin` (Ihre Blocktypen noch vorhanden).

## Welchen Weg Sie wählen

Prüfen Sie nach jeder Änderung von `ActiveExtensions[]`.

## Prüfen

- Beide Listen wie erwartet.

## Verwandte Seiten

- [Ihre eigene Design-Erweiterung](../../eigene-design-erweiterung.md)
- [Einen Blocktyp hinzufügen](../../blocktyp-hinzufuegen.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
