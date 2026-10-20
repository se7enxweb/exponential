---
title: "Gespeicherte Bezeichner von Komponentenblöcken aktualisieren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1510
summary: Layouts von vor der Umbenennung speichern Komponenten als ibexa_component_<type>; ext:explayouts:updatecomponentblockidentifiers benennt sie in exp_component_<type> um.
keywords: Komponentenblöcke, Bezeichner, umbenennen, Upgrade, exp_component, component blocks, identifiers, rename, upgrade
type: help
---
# Gespeicherte Bezeichner von Komponentenblöcken aktualisieren

Alte importierte Layouts werden mit den aktuellen Templates dargestellt.

*Lebenszyklus: Migrieren, Beispiel 11 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Danach listet der Editor sie unter ihren neuen Bezeichnern.

## Auf der Kommandozeile

1. `./console ext:explayouts:updatecomponentblockidentifiers --allow-root-user` (seine Hilfe: *Renames the stored component blocks from ibexa_component_<type> to exp_component_<type>*).
2. Das `block.tpl` des Media-Designs zeigt einen als `ibexa_component_<type>` gespeicherten Block bereits mit dem Template `exp_component_`.

## Welchen Weg Sie wählen

Einmal nach dem Import alter Layouts ausführen.

## Prüfen

- Ein neuer Export enthält kein `ibexa_component_`.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../../../../eng-us/help/build-two-ways/block-to-template-map.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
