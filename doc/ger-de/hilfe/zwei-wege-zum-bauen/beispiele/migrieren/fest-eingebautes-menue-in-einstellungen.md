---
title: "Ein fest eingebautes Menü in Einstellungen überführen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1526
summary: Ein altes Template mit eingetippten Links wird zu einer Liste MainMenuID[], die main_menu.tpl des Themes liest.
keywords: fest eingebautes Menü, Einstellungen, MainMenuID, Migration, hard-coded menu, settings, migration
type: help
---
# Ein fest eingebautes Menü in Einstellungen überführen

Redakteure und Administratoren können ein Menü ändern, das bisher Code war.

*Lebenszyklus: Migrieren, Beispiel 27 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor.

## Mit Einstellungen

1. Sammeln Sie die Knoten-IDs der verlinkten Seiten; schreiben Sie sie mit `exp:ini add` in `MainMenuID[]`; entfernen Sie Ihren Override von `main_menu.tpl`, damit das Template des Themes die Liste liest.

## Welchen Weg Sie wählen

Einstellungen oder der Baum, nicht Code, für Menüeinträge.

## Prüfen

- Für Besucher ist das Menü unverändert.

## Verwandte Seiten

- [Das Kopfmenü über die Kommandozeile](../../../../../eng-us/help/build-two-ways/header-menu-by-cli.md)
- [Beispiele zur Migration, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
