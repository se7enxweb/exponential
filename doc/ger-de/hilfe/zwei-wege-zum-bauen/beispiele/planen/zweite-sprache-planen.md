---
title: "Eine zweite Sprache planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1009
summary: Jede Sprachversion ist ein Siteaccess mit eigenen Einstellungen (Menüs) und kann über eine Siteaccess-Bedingung eigene Layouts bekommen.
keywords: Sprache, Übersetzung, Siteaccess, bold_ger, Bedingung, mehrsprachig, Planung, language, translation, siteaccess, condition, multilingual, planning
type: help
---
# Eine zweite Sprache planen

Eine deutsche Version einer Site mit eigenem Menü und, wo nötig, eigenen Layouts.

*Lebenszyklus: Planen, Beispiel 10 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen und Templates](#mit-einstellungen-und-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Eine Zuordnung kann eine Bedingung **Siteaccess** haben (eine Liste: site, admin, classic, bold, bold_ger ...). Die Starter-Zuordnungen verwenden `siteaccess: starter, starter_ger`.
2. Planen Sie, welche Seitentypen ein Layout pro Sprache brauchen und welche sich eines teilen.

## Mit Einstellungen und Templates

1. Jeder Siteaccess hat seine eigene `menu.ini` (`exp:ini where menu.ini/SiteInfo/MainMenuID <siteaccess>`), das Kopfmenü kann sich also unterscheiden.
2. Übersetzbare Template-Texte verwenden `|i18n( 'context' )` und die Übersetzungsdateien Ihrer Erweiterung.

## Welchen Weg Sie wählen

Teilen Sie Layouts über die Sprachen hinweg, solange sich die Zusammenstellung nicht wirklich unterscheidet; die Inhalte werden ohnehin übersetzt.

## Prüfen

- Der Plan listet den Siteaccess jeder Sprache.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
