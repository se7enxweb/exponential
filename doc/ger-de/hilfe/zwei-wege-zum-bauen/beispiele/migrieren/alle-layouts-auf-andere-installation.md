---
title: "Alle Layouts auf eine andere Installation übertragen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1500
summary: exp:layouts export --all auf der Quelle, Import mit Probelauf auf dem Ziel; Inhalte werden über Remote-ID und Pfad zugeordnet.
keywords: Layouts migrieren, alles exportieren, Import, andere Installation, Remote-ID, migrate layouts, export all, import, another installation, remote ID
type: help
---
# Alle Layouts auf eine andere Installation übertragen

Die Layouts einer Installation auf einer zweiten mit denselben Inhalten.

*Lebenszyklus: Migrieren, Beispiel 1 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Dasselbe mit **Layouts > Exportieren** und **Layouts > Importieren** in der Administration.

## Auf der Kommandozeile

1. Auf der Quelle: `./console exp:layouts export --all --output=all-layouts.zip`.
2. `./console exp:layouts import all-layouts.zip --dry-run` und die Zustände lesen (`new`, `identical`, `changed`, `conflicting`).
3. Noch einmal ohne `--dry-run` ausführen, mit der gewählten `--strategy` und `--publish`, wenn das Ergebnis sofort live sein soll.
4. `./console exp:cache clear --tag=content` (ein Import auf der Kommandozeile leert den Seiten-Cache nicht).

## Welchen Weg Sie wählen

Die Konsole für wiederholbare Übertragungen; die Administration für einen Einzelfall.

## Prüfen

- `exp:layouts list` auf dem Ziel zeigt die Layouts, Zuordnungen und Verknüpfungen.

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
