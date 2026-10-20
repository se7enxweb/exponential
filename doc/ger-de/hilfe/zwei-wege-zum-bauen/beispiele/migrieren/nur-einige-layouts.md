---
title: "Nur einige Layouts übertragen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1501
summary: --only mit den Bezeichnern der Layouts; die gemeinsamen Layouts, mit denen sie verknüpft sind, kommen mit.
keywords: nur exportieren, Bezeichner, gemeinsame Layouts inklusive, teilweise Migration, export only, identifiers, shared layouts included, partial migration
type: help
---
# Nur einige Layouts übertragen

Die neuen Kampagnen-Layouts übertragen, sonst nichts.

*Lebenszyklus: Migrieren, Beispiel 2 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Oder haken Sie die Layouts unter **Layouts > Exportieren** an.

## Auf der Kommandozeile

1. `./console exp:layouts export --only=spring_campaign --output=campaign.zip` (die gemeinsamen Layouts, mit denen es verknüpft ist, sind immer dabei; weitere fügen Sie mit `--shared=` hinzu).
2. `./console exp:layouts import campaign.zip --dry-run` und die Zustände lesen (`new`, `identical`, `changed`, `conflicting`).
3. Noch einmal ohne `--dry-run` ausführen, mit der gewählten `--strategy` und `--publish`, wenn das Ergebnis sofort live sein soll.
4. `./console exp:cache clear --tag=content` (ein Import auf der Kommandozeile leert den Seiten-Cache nicht).

## Welchen Weg Sie wählen

Kleine Exporte lassen sich im Probelauf leichter prüfen.

## Prüfen

- Der Probelauf listet das Kampagnen-Layout und Header / Footer.

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
