---
title: "Eine veröffentlichte Layoutänderung rückgängig machen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1321
summary: Den vor der Änderung gemachten Export mit --strategy=overwrite --publish importieren; der Import leert den Seiten-Cache selbst.
keywords: rückgängig machen, Zurücksetzen, veröffentlichtes Layout, Import mit Überschreiben, Pflege, undo, rollback, published layout, import overwrite, maintenance
type: help
---
# Eine veröffentlichte Layoutänderung rückgängig machen

Der Prefooter der letzten Woche ist zurück.

*Lebenszyklus: Pflegen, Beispiel 22 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Oder **Layouts > Importieren** in der Administration mit derselben Datei.

## Auf der Kommandozeile

1. `./console exp:layouts import prefooter-before.zip --dry-run --strategy=overwrite --publish`, dann ohne `--dry-run`; der Import leert den Seiten-Cache selbst (seit explayouts_core 1.0.10).

## Welchen Weg Sie wählen

Nur mit einem Export von vorher möglich; machen Sie vor jeder größeren Änderung einen.

## Prüfen

- Das Layout hat seine früheren Blöcke; ID und Bezeichner sind unverändert.

## Verwandte Seiten

- [Ein Layout zurücksetzen](../../layout-zuruecksetzen.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
