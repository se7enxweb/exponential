---
title: "Gespeicherte Block-Links auf Remote-IDs umstellen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1511
summary: ext:explayouts:updatelinklocationreferences wandelt Block-Links von ibexa-location://<id> in exp-remote-location://<remote id> um.
keywords: Block-Links, Ortsverweise, Remote-ID, Upgrade, Migration, block links, location references, remote ID, upgrade, migration
type: help
---
# Gespeicherte Block-Links auf Remote-IDs umstellen

Links in Blöcken überstehen den Umzug auf eine andere Installation.

*Lebenszyklus: Migrieren, Beispiel 12 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Als Adressen eingetippte Links sind nicht betroffen.

## Auf der Kommandozeile

1. `./console ext:explayouts:updatelinklocationreferences --allow-root-user` (seine Hilfe: *Converts the block links stored as ibexa-location://<id> to exp-remote-location://<remote id>*).

## Welchen Weg Sie wählen

Vor dem Export von Layouts auf eine andere Installation ausführen.

## Prüfen

- Links in Blöcken öffnen weiterhin die richtigen Seiten.

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
