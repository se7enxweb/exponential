---
title: "Das Markup der Kopfzeile neu bauen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1402
summary: pagelayout/header.tpl (und seine Teile) in Ihrer Design-Erweiterung überschreiben; das gemeinsame Layout platziert es weiterhin.
keywords: Kopfzeile, pagelayout/header.tpl, Override, Neugestaltung, Logo, Suche, header, override, redesign, logo, search
type: help
---
# Das Markup der Kopfzeile neu bauen

Eine neue Kopfzeile mit zentriertem Logo und der Suche als Leiste über die ganze Breite.

*Lebenszyklus: Neugestaltung, Beispiel 3 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

Keine Änderung am Layout: Der Template-Block der Kopfzeile in Header / Footer bindet `pagelayout/header.tpl` ein ([Wie die Kopfzeile aufgebaut ist](../../../../../eng-us/help/build-two-ways/how-the-header-is-built.md)).

## Mit Templates

1. Kopieren Sie `extension/sevenx_themes_media/design/media/templates/pagelayout/header.tpl` nach `extension/mysite/design/media/templates/pagelayout/header.tpl`.
2. Ordnen Sie die Includes um (`content/parts/site_logo.tpl`, `pagelayout/header/main_menu.tpl`, `search_box.tpl`, `user_menu.tpl`, `language_selector.tpl`) oder schreiben Sie neues Markup; behalten Sie das Include des Menüs, solange sich nicht auch das Menü ändert.
3. Leeren Sie **Template & Inhalt** und prüfen Sie eine Seite, die es verwendet.

## Welchen Weg Sie wählen

Die Kopfzeile ist Design: Templates.

## Prüfen

- Jede Seite, mit und ohne Layout, zeigt die neue Kopfzeile.

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](../../../../../eng-us/help/build-two-ways/how-the-header-is-built.md)
- [Ihre eigene Design-Erweiterung](../../../../../eng-us/help/build-two-ways/your-own-design-extension.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
