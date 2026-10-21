---
title: "Das Benutzermenü der Kopfzeile ändern"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1423
summary: Das Benutzermenü (Anmelden, Registrieren, Warenkorb, Administration ...) ist pagelayout/header/user_menu.tpl und menu.ini [HeaderUserMenu].
keywords: Benutzermenü, HeaderUserMenu, Kopfzeile, Override, Neugestaltung, user menu, header, override, redesign
type: help
---
# Das Benutzermenü der Kopfzeile ändern

Kein Eintrag für den Warenkorb, stattdessen ein Eintrag „My plans“.

*Lebenszyklus: Neugestaltung, Beispiel 24 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Keine Änderung am Layout.

## Mit Templates

1. Zuerst die Einstellungen: `[HeaderUserMenu] ShowBasket` und die anderen Schalter der `menu.ini`.
2. Für neue Einträge überschreiben Sie `pagelayout/header/user_menu.tpl`.

## Welchen Weg Sie wählen

Einstellungen für Schalter, das Template für neue Einträge.

## Prüfen

- Das Menü zeigt die geplanten Einträge, angemeldet und abgemeldet.

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](../../wie-die-kopfzeile-aufgebaut-ist.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
