---
title: "Ein eigener Blocktyp"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1134
summary: "Wenn Redakteure immer wieder eine neue Art von Block brauchen, definieren Sie sie einmal: Definition, Handler, Template."
keywords: neuer Blocktyp, Blockdefinition, Handler, Notice, explayouts.ini, new block type, block definition
type: help
---
# Ein eigener Blocktyp

Ein Block „Notice“ in zwei Ausführungen (info, warning), den Redakteure wie jeden anderen hineinziehen.

*Lebenszyklus: Bauen, Beispiel 35 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Mit Templates](#mit-templates)
2. [Dann im Layout-Editor](#dann-im-layout-editor)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Mit Templates

1. Definieren Sie `[BlockDefinition_notice]` und `AvailableBlocks[]=notice` in der `explayouts.ini.append.php` Ihrer Erweiterung (mit der Erweiterung an erster Stelle in `ActiveExtensions[]`).
2. Schreiben Sie `explayouts/block/notice.tpl`.
3. Leeren Sie die Caches Template & Inhalt und INI.

## Dann im Layout-Editor

1. Die Gruppe *Basic* der Palette listet **Notice**.
2. Ziehen Sie den Block hinein, wählen Sie den Ansichtstyp und tippen Sie den Text im Reiter **Design** ein.
3. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Welchen Weg Sie wählen

Lohnt sich, sobald dasselbe Markup sonst in viele HTML-Snippets kopiert würde.

## Prüfen

- Der Block erzeugt `ngl-notice ngl-vt-<view type>`.

## Verwandte Seiten

- [Einen Blocktyp hinzufügen](../../blocktyp-hinzufuegen.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
