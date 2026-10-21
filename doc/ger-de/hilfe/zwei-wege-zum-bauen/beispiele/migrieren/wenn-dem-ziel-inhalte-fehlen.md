---
title: "Wenn dem Ziel Inhalte fehlen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1503
summary: --unmapped=drop|keep|fail entscheidet, was mit Verweisen auf Inhalte geschieht, die das Ziel nicht hat (Standard drop).
keywords: nicht zugeordnete Verweise, fehlende Inhalte, Import, drop, keep, fail, unmapped references, missing content, import
type: help
---
# Wenn dem Ziel Inhalte fehlen

Wissen, was mit einem handverlesenen Artikel geschieht, den es auf dem Ziel nicht gibt.

*Lebenszyklus: Migrieren, Beispiel 4 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Prüfen Sie nach einem Import mit `drop` handverlesene Listen auf Lücken.

## Auf der Kommandozeile

1. `--unmapped=fail` bricht den Import ab und zeigt, was fehlt; `drop` (Standard) lässt diese Einträge weg; `keep` behält den Verweis, wie er ist.
2. `./console exp:layouts import layouts.zip --dry-run --unmapped=fail` und die Zustände lesen (`new`, `identical`, `changed`, `conflicting`).
3. Noch einmal ohne `--dry-run` ausführen, mit der gewählten `--strategy` und `--publish`, wenn das Ergebnis sofort live sein soll.
4. Von Hand ist nichts zu leeren: Seit explayouts_core 1.0.10 leert ein Import mit `--publish` den Seiten-Cache selbst.

## Welchen Weg Sie wählen

Zuerst `fail`, um die Liste zu sehen; dann die Inhalte importieren oder `drop` wählen.

## Prüfen

- Manuelle Sammlungen haben die erwarteten Einträge.

## Verwandte Seiten

- [Layouts exportieren und importieren](../../layouts-exportieren-und-importieren.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
