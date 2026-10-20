---
title: "Ein neuer Bereich kommt ins Kopfmenü"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1325
summary: Die Bereichsseite veröffentlichen, ihre Knoten-ID finden, sie zu MainMenuID[] hinzufügen, Template & Inhalt leeren.
keywords: neuer Bereich, Kopfmenü, Knoten-ID, MainMenuID, Pflege, new section, header menu, node ID, maintenance
type: help
---
# Ein neuer Bereich kommt ins Kopfmenü

Der neue Bereich „Yoga“ erscheint im Menü.

*Lebenszyklus: Pflegen, Beispiel 26 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Inhalten und Einstellungen](#mit-inhalten-und-einstellungen)
3. [Mit einem Menü aus dem Baum](#mit-einem-menü-aus-dem-baum)
4. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
5. [Prüfen](#prüfen)

## Im Layout-Editor

Nicht im Layout-Editor; geben Sie dem Bereich bei Bedarf über eine Zuordnung sein Layout.

## Mit Inhalten und Einstellungen

1. Veröffentlichen Sie die Seite; notieren Sie `Node <id>`; `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] <id> siteaccess:site`; `./console exp:cache clear --tag=template,content`.

## Mit einem Menü aus dem Baum

1. Wenn die Kopfzeile den Override verwendet, der dem Baum folgt ([Ein Kopfmenü, das dem Baum folgt](../../../../../eng-us/help/build-two-ways/header-menu-from-the-tree.md)), genügt es, den Bereich zu veröffentlichen.

## Welchen Weg Sie wählen

Feste Liste: zwei Befehle. Menü aus dem Baum: nichts.

## Prüfen

- „Yoga“ steht im Menü.

## Verwandte Seiten

- [Einen Eintrag zum Kopfmenü hinzufügen](../../../../../eng-us/help/build-two-ways/add-a-header-menu-item.md)
- [Beispiele zur Pflege, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
