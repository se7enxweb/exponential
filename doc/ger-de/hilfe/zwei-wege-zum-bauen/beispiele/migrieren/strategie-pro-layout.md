---
title: "Die Strategie pro Layout wählen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1505
summary: --item=<key>=<strategy>,... setzt skip, overwrite oder copy pro Eintrag, mit den Schlüsseln, die der Probelauf ausgibt.
keywords: Strategie pro Eintrag, --item, pro Layout, Import, item strategy, per layout, import
type: help
---
# Die Strategie pro Layout wählen

Den Prefooter überschreiben, aber den lokalen Header / Footer behalten.

*Lebenszyklus: Migrieren, Beispiel 6 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Im Editor nicht verfügbar.

## Auf der Kommandozeile

1. Zuerst der Probelauf; er gibt Schlüssel wie `layout:d14c82715a0c5f1da1e6df4d0b77e638:published` aus.
2. `./console exp:layouts import release.zip --item=layout:d14c82715a0c5f1da1e6df4d0b77e638:published=overwrite --publish`.

## Welchen Weg Sie wählen

Strategien pro Eintrag machen gemischte Releases sicher.

## Prüfen

- Nur der gewählte Eintrag hat sich geändert (ein zweiter Probelauf zeigt ihn als `identical`).

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
