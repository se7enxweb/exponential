---
title: "Das Kopfmenü einer zweiten Sprache"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1215
summary: Ein deutscher Siteaccess (auf dieser Installation bold_ger, starter_ger) hat seine eigene menu.ini; schreiben Sie seine Liste in seinem eigenen Geltungsbereich.
keywords: Sprache, Siteaccess, menu.ini, bold_ger, Kopfmenü, Übersetzung, language, siteaccess, header menu, translation
type: help
---
# Das Kopfmenü einer zweiten Sprache

Das Menü der deutschen Site listet ihre eigenen Seiten.

*Lebenszyklus: Livegang, Beispiel 16 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Einstellungen](#mit-einstellungen)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor. Wählen Sie unter **Setup > INI-Einstellungen** `menu.ini` und den deutschen Siteaccess, dann `MainMenuID[]` **Bearbeiten**.

## Mit Einstellungen

1. `./console exp:ini where menu.ini/SiteInfo/MainMenuID bold_ger`, dann `exp:ini add ... <id> siteaccess:bold_ger`.

## Welchen Weg Sie wählen

Eine Liste pro Siteaccess hält die Sprachen unabhängig voneinander.

## Prüfen

- Die deutsche Site zeigt das deutsche Menü, die englische ist unverändert.

## Verwandte Seiten

- [Das Kopfmenü über die Kommandozeile](../../kopfmenue-per-kommandozeile.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
