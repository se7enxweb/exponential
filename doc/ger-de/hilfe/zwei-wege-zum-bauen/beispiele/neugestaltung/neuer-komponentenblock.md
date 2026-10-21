---
title: "Ein neuer Komponentenblock für die Neugestaltung"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1406
summary: Ein neuer Blocktyp für eine Komponente, die das alte Design nicht hatte, in der INI mit einem vorhandenen Handler definiert.
keywords: neuer Blocktyp, Komponente, Handler, Neugestaltung, Palette, new block type, component, handler, redesign, palette
type: help
---
# Ein neuer Komponentenblock für die Neugestaltung

Eine Komponente „Preistabelle“, die Redakteure hineinziehen können.

*Lebenszyklus: Neugestaltung, Beispiel 7 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Die Palette listet ihn in der Gruppe seiner Category.

## Mit Templates

1. Definieren Sie `[BlockDefinition_pricing]` und `AvailableBlocks[]=pricing` mit einem Handler, der die benötigten Felder hat (`expLayoutsComponentBlockHandler` bietet Titel, Inhalt, Bild, Link, CSS-Klasse und ID), und `explayouts/block/pricing.tpl` ([Einen Blocktyp hinzufügen](../../blocktyp-hinzufuegen.md)).

## Welchen Weg Sie wählen

Verwenden Sie einen Handler wieder, wenn seine Felder passen; schreiben Sie nur für neue Felder einen.

## Prüfen

- Der Block erzeugt `ngl-pricing`.

## Verwandte Seiten

- [Einen Blocktyp hinzufügen](../../blocktyp-hinzufuegen.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
