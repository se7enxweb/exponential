---
title: "Ein durch einen Import beschädigtes Layout retten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1525
summary: Den vorherigen Export mit overwrite und publish importieren; der Import leert den Seiten-Cache selbst.
keywords: retten, beschädigtes Layout, Import, Zurücksetzen, Migration, rescue, broken layout, import, rollback, migration
type: help
---
# Ein durch einen Import beschädigtes Layout retten

Einen misslungenen Import rückgängig machen.

*Lebenszyklus: Migrieren, Beispiel 26 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Wenn der Import nur Entwürfe erzeugt hat (kein `--publish`), **Verwerfen** Sie sie im Editor.

## Auf der Kommandozeile

1. `./console exp:layouts import before-import.zip --dry-run --strategy=overwrite --publish` und die Zustände lesen (`new`, `identical`, `changed`, `conflicting`).
2. Noch einmal ohne `--dry-run` ausführen, mit der gewählten `--strategy` und `--publish`, wenn das Ergebnis sofort live sein soll.
3. Von Hand ist nichts zu leeren: Seit explayouts_core 1.0.10 leert ein Import mit `--publish` den Seiten-Cache selbst.

## Welchen Weg Sie wählen

Exportieren Sie immer vor dem Importieren.

## Prüfen

- Das Layout ist wie vor dem Import.

## Verwandte Seiten

- [Ein Layout zurücksetzen](../../layout-zuruecksetzen.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
