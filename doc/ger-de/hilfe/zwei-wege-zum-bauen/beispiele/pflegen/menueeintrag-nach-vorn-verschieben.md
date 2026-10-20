---
title: "Einen Eintrag des Kopfmenüs nach vorn verschieben"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1308
summary: Die Zeilen von MainMenuID[] in der Administration umordnen oder exp:ini clear und add in der neuen Reihenfolge.
keywords: Menü umordnen, MainMenuID, exp:ini clear, Menüreihenfolge, Pflege, reorder menu, menu order, maintenance
type: help
---
# Einen Eintrag des Kopfmenüs nach vorn verschieben

Recipes als Erstes im Menü.

*Lebenszyklus: Pflegen, Beispiel 9 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Setup > INI-Einstellungen**, `MainMenuID[]` **Bearbeiten**, die Zeile `=144` nach oben verschieben (hinter die leere erste Zeile), **Speichern**, **Template & Inhalt**.

## Mit Einstellungen

1. `exp:ini clear`, dann `exp:ini add` für jede ID in der gewünschten Reihenfolge ([Einträge des Kopfmenüs umordnen oder entfernen](../../../../../eng-us/help/build-two-ways/reorder-or-remove-header-menu-items.md)).

## Welchen Weg Sie wählen

Die Administration für eine einzelne Verschiebung; die Konsole für Reihenfolgen per Skript.

## Prüfen

- Die Kopfzeile beginnt mit Recipes.

## Verwandte Seiten

- [Einträge des Kopfmenüs umordnen oder entfernen](../../../../../eng-us/help/build-two-ways/reorder-or-remove-header-menu-items.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
